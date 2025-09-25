<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingInfo;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingInformationController extends Controller
{
    //
    use CommonTraits;
    public function update(Request $request)
    {

        $validators = Validator($request->all(), [
            'first_name' => 'required|string',  
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'country' => 'required|string',    
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $request->merge(['user_id' => auth()->id()]);
        BillingInfo::updateOrCreate(
            ['user_id' => Auth::id()],
            $request->all()
        );
        return $this->sendSuccess('Billing info updated successfully!');
    }
    public function fetch(){
        $billingInfo = BillingInfo::where('user_id', Auth::id())->first();
        if (!$billingInfo) {
            return $this->sendError('Billing information not found');
        }
        return $this->sendSuccess('Billing information fetched successfully!', $billingInfo);
    }
}
