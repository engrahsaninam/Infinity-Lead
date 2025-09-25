<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infinity;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\WelcomeEmail;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class CustomerController extends Controller
{
    use CommonTraits;
    public function fetch(Request $request)
    {
        $search = $request->search;
        $lists = User::where('role', User::ROLE_USER)->with(['active_subscription', 'assigned_subscription']);
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['first_name', 'last_name', 'email'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function create(Request $request){
        $validators = Validator($request->all(), [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|unique:users,email' . ($request->id ? ',' . $request->id : ''),
            'password' => [
                $request->id ? 'nullable' : 'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ]
        ]);

        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }

        $profilePath = null;
        if ($request->hasFile('profile')) {
            $profilePath = Infinity::upload($request->file('profile'), Infinity::_PUBLIC_PROFILE);
        }

        if($request->id){
            $user = User::find($request->id);
            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'role' => User::ROLE_USER,
            ];
            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }
            if ($profilePath) {
                $updateData['profile'] = $profilePath;
            }
            $user->update($updateData);
            $message='User updated successfully!';
        }else{
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_USER,
                'profile' => $profilePath,

            ]);
            $message = 'User created successfully!';
            $user->notify(new WelcomeEmail());

        }
        return $this->sendSuccess($message);
    }
    public function edit(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:users,id',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $user = User::find($request->id);
        return $this->sendSuccess('User fetched successfully!', $user);
    }
    public function impersonateAdmin(Request $request){
        $token=$request->token;
        try {
            $decryptedAdminId = Crypt::decryptString($token);
            $user = User::findOrFail($decryptedAdminId);
            Auth::login($user);
            $token = $user->createToken('API Token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Admin logged in successfully.',
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token.'], 421);
        }
    }
    public function login(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:users,id',
            'admin_id' => 'required|exists:users,id',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $adminId=$request->admin_id;
        $impersonationToken=Crypt::encryptString($adminId);

        $user = User::findOrFail($request->id);
        Auth::login($user);
        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'User logged in successfully without password.',
            'token' => $token,
            'user' => $user,
            'impersonation_token' => $impersonationToken,
        ]);
    }
    public function assign(Request $request){
        $validators = Validator($request->all(), [
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        if(Subscription::where('user_id',$request->user_id)->where('status',Subscription::STATUS_ASSIGNED)->count()>0){
            return $this->sendError('Already assigned',421);
        }
        $user=User::find($request->user_id);
        Subscription::cancel($user->id);
        $plan=Plan::find($request->plan_id);
        $subscription=Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
            'start' => now(),
            'end' => now()->addDays(30),
            'payment_method_id' => 0,
            'price' => -1,
            'code' => -1,
            'credits'=>$plan->credits,
            'status' => $plan->price >= 0 ? Subscription::STATUS_ASSIGNED : Subscription::STATUS_ACTIVE,
            'payment_response' => '',
        ]);
        
        if($subscription->status === Subscription::STATUS_ACTIVE){
            $user->status=User::STATUS_ACTIVE;
            $user->save();
        }
        return $this->sendSuccess('Plan assigned successfully!');
    }
    public function action(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required|exists:users,id',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $user = User::find($request->id);
        $user->status = $user->status === User::STATUS_ACTIVE
            ? User::STATUS_RESTRICTED
            : User::STATUS_ACTIVE;
        $user->save();
        $message = $user->status === User::STATUS_ACTIVE
            ? 'User activated successfully!'
            : 'User disabled successfully!';
        return $this->sendSuccess($message);
    }
    public function delete(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:users,id',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        User::find($request->id)->delete();
        return $this->sendSuccess('User deleted successfully!');
    }
}
