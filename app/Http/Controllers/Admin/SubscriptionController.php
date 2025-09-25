<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    use CommonTraits;
    public function fetch(Request $request)
    {
        $search = $request->search;
        $lists = Subscription::with(['user','plan.currency','paymentMethod'])->orderBy('created_at','desc');
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['price', 'code'], "like", "%$search%");
            });
        });
        $lists->when($request->auth, function ($query) use ($request) {
            $query->where('user_id', $request->auth);
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function action(Request $request)
    {
        $action = $request->action;
        $id = $request->id;
        $message = '';
        if ($action === 'approve') {
            $subscription=Subscription::find($id);
            $subscription->update(['status' => Subscription::STATUS_ACTIVE]);
            User::find($subscription->user_id)->update([
                'status' => User::STATUS_ACTIVE
            ]);
            $message = 'Subscription approved successfully.';
        } elseif ($action === 'reject') {
            Subscription::find($id)->update(['status' => Subscription::STATUS_REJECTED]);
            $message = 'Subscription rejected successfully.';
        }
        return $this->sendSuccess($message);
    }
    public function recurring(Request $request){
        $subscription=Subscription::find($request->id);
        $subscription->recurring === Subscription::IS_RECURRING 
        ? $subscription->update(['recurring'=>Subscription::NOT_RECURRING])
        : $subscription->update(['recurring'=>Subscription::IS_RECURRING]);

        $subscription->refresh();
        if($subscription->recurring === Subscription::IS_RECURRING){
            $message='Subscription marked as recurring';
        }
        if ($subscription->recurring === Subscription::NOT_RECURRING) {
            $message = 'Subscription marked as not recurring';
        }
        return $this->sendSuccess($message);

    }
    public function replenish(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required',
            'credits' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $subscription=Subscription::find($request->id);
        $subscription->update(['credits'=>$request->credits]);
        return $this->sendSuccess('Credits replenished successfully!');

    }
    public function terminate(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $subscription = Subscription::find($request->id);
        Subscription::cancel($subscription->user_id);
        return $this->sendSuccess('Subscription cancelled successfully!');

    }
}
