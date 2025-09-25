<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Traits\CommonTraits;
use Auth;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    //
    use CommonTraits;
    public function fetch(Request $request)
    {
        $subscriptions=Auth::user()->subscriptions()->with(['plan', 'paymentMethod'])->get();

        $active= Auth::user()->activeSubscription();
        $active=Subscription::with(['plan', 'paymentMethod'])->find($active->id);
        $data=[
            'all' => $subscriptions,
            'active'=>$active,
        ];
        return $this->sendSuccess('Plans fetched successfully', $data);
    }
    public function cancel(Request $request)
    {
        Subscription::cancel(Auth::id());
        return $this->sendSuccess('Subscription cancelled successfully',);
    }

    public function recurring(Request $request)
    {
        $subscription = Auth::user()->activeSubscription();
        $subscription = Subscription::find($subscription->id);
        $subscription->recurring === Subscription::IS_RECURRING
            ? $subscription->update(['recurring' => Subscription::NOT_RECURRING])
            : $subscription->update(['recurring' => Subscription::IS_RECURRING]);

        $subscription->refresh();
        if ($subscription->recurring === Subscription::IS_RECURRING) {
            $message = 'Subscription marked as recurring';
        }
        if ($subscription->recurring === Subscription::NOT_RECURRING) {
            $message = 'Subscription marked as not recurring';
        }
        return $this->sendSuccess($message);

    }
}
