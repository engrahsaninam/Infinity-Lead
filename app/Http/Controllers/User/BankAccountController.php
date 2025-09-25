<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe as StripeGateway;
use Stripe\Customer;
use Stripe\StripeClient;
use Stripe\Charge;
use App\Models\BankAccount;
use Stripe\Exception\ApiErrorException;

class BankAccountController extends Controller
{
    //
    use CommonTraits;
    public function banks(Request $request)
    {
        $user = auth()->user();
        $banks = $user->bankAccounts()->get();
        return $this->sendSuccess('Bank accounts fetched successfully', $banks);
    }
    public function plans(Request $request)
    {
        $plans = Plan::with('currency')->get();
        return $this->sendSuccess('Plans fetched successfully', $plans);
    }
    public function methods(Request $request)
    {
        $methods = PaymentMethod::where('status', PaymentMethod::STATUS_ACTIVE)->get();
        return $this->sendSuccess('Payment methods fetched successfully', $methods);
    }


    public function stripe_store(Request $request)
    {
        $method=PaymentMethod::where('slug','stripe')->first();
        if(!$method->publishable_key){
            return $this->sendError('Stripe publishable key is not set',421);
        }
        if (!$method->secret_key) {
            return $this->sendError('Stripe secret key is not set',421);
        }
        $secretKey=$method->secret_key;
        StripeGateway::setApiKey($secretKey);
        $user = auth()->user();
        if ($user->stripe_customer_id) {
            try {
                $customer = Customer::retrieve($user->stripe_customer_id);
                if ($customer && $customer->id === $user->stripe_customer_id) {
                }
            } catch (ApiErrorException $e) {
                logger()->error('Invalid Stripe customer ID: ' . $e->getMessage());
                $customer = Customer::create(['email' => $user->email]);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }
        } else {
            $customer = Customer::create(['email' => $user->email]);
            $user->stripe_customer_id = $customer->id;
            $user->save();
        }

        $token = $request->id;
        $stripe = new StripeClient($secretKey);
        $card = $stripe->customers->createSource(
            $user->stripe_customer_id,
            ['source' => $token]
        );

        $bank = new BankAccount();
        $bank->user_id = $user->id;
        $bank->stripe_card_id = $card['id'];
        $bank->card_number = $card['last4'];
        $bank->card_type = $card['brand'];
        $bank->expiry = $card['exp_month'] . '/' . $card['exp_year'];

        if (count(BankAccount::where('user_id', $user->id)->get()) == 0) {
            $bank->preferred = 1;
        }
        $bank->save();
        return $this->sendSuccess('Payment method has been added successfully');
    }
    public function processPayment(Request $request)
    {
        $plan = $request->plan;
        $method = $request->method;
        if (auth()->user()->activeSubscription()) {
            User::find(Auth::id())->update([
                'status' => User::STATUS_ACTIVE
            ]);
            return $this->sendError('You already have an active subscription. Go to settings',421);
        }
        $user = User::find(Auth::id());
        $assignedSubscription=Subscription::where('user_id',auth()->id())->where('status',Subscription::STATUS_ASSIGNED)->first();
        if ($method['slug'] === 'offline') {
            Subscription::create([
                'user_id' => auth()->id(),
                'plan_id' => $plan['id'],
                'start' => now(),
                'end' => now()->addDays(30),
                'payment_method_id' => $method['id'],
                'price' => $plan['price'],
                'code' => $plan['currency']['code'],
                'credits'=>$plan['credits'],
                'status' => ($plan['price'] == 0) ? Subscription::STATUS_ACTIVE : Subscription::STATUS_PENDING,
            ]);
            if ($plan['price'] === 0) {
                User::find(Auth::id())->update([
                    'status' => User::STATUS_ACTIVE
                ]);
            }
        }
        if($method['slug']==='stripe'){
            $user = auth()->user();
            if (!$user->stripe_customer_id) {
                return $this->sendError('Add/Select payment method',421);
            }
            $charge= null;
            if ($plan['price'] > 0) {
                $stripe = new StripeClient($method['secret_key']);
                $charge = $stripe->charges->create([
                    'amount' => $plan['price'] * 100,
                    'currency' => $plan['currency']['code'],
                    'customer' => $user->stripe_customer_id,
                    'description' => 'Payment for plan: ' . $plan['name'],
                    'shipping' => [
                        'name' => $user->billing_info->first_name . ' ' . $user->billing_info->last_name,
                        'address' => [
                            'line1' => $user->billing_info->address,
                            'city' => $user->billing_info->address,
                            'state' => $user->billing_info->address,
                            'country' => $user->billing_info->country,
                        ],
                    ],
                ]);
            }
            Subscription::create([
                'user_id' => auth()->id(),
                'plan_id' => $plan['id'],
                'start' => now(),
                'end' => now()->addDays(30),
                'payment_method_id' => $method['id'],
                'price' => $plan['price'],
                'code' => $plan['currency']['code'],
                'credits'=>$plan['credits'],
                'status' => Subscription::STATUS_ACTIVE,
                'payment_response' => json_encode($charge),
            ]);
            $user->update(['status'=>User::STATUS_ACTIVE]);
        }
        if($assignedSubscription){
            $assignedSubscription->delete();
        }
        $user = User::find(Auth::id());
        return $this->sendSuccess('Subscription created successfully', $user);
    }
    public function offlinePaymentPending(Request $request)
    {
        $user = auth()->user();
        $subscription = Subscription::with('plan.currency')->where('user_id', $user->id)
            ->where('status', Subscription::STATUS_PENDING)
            ->first();

        $data = [
            'status' => $subscription ? true : false,
            'plan' => $subscription?->plan,
        ];
        return $this->sendSuccess('Pending subscription found', $data);
    }
    public function refreshOfflineStatus(Request $request)
    {
        $user = auth()->user();
        $subscription = Subscription::with('plan.currency')->where('user_id', $user->id)
            ->where('status', Subscription::STATUS_PENDING)
            ->first();
            $message='';
        if($subscription->status === Subscription::STATUS_PENDING){
            $message='Payment is still pending';
        }
        if ($subscription->status === Subscription::STATUS_REJECTED) {
            $message = 'Payment is still rejected. Reason : '. $subscription->reject_reason;
        }
        if($subscription->status === Subscription::STATUS_ACTIVE){
            $message='Payment is successful. Your account is now active.';
        }
        return $this->sendSuccess($message);
    }
    public function assigned(){
        $subscription=Subscription::with('plan.currency')->where('user_id',Auth::id())->where('status', Subscription::STATUS_ASSIGNED)->first();
        if(!$subscription){
            return $this->sendError('Not assigned by admin',422);
        }
        return $this->sendSuccess('Assigned subscription', $subscription);
    }
    public  function publishKey(){
        $paymentMethod=PaymentMethod::where('slug','stripe')->first();
        $key='';
        if($paymentMethod and $paymentMethod->publishable_key){
            $key=$paymentMethod->publishable_key;
        }
        return $this->sendSuccess('Key', $key);

    }
                
}
