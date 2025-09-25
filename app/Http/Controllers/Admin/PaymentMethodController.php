<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    use CommonTraits;
    public function fetch(Request $request)
    {
        $search = $request->search;
        $lists = PaymentMethod::query();
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['name', 'description'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function update(Request $request)
    {
        // if slug is offline then instruction is required, if slug is stripe then publishable_key and secret_key are required
        $rules = [
            'name' => 'required|string',
            'slug' => 'required|string',
        ];

        if ($request->slug === 'offline') {
            $rules['instructions'] = 'required|string';
        } elseif ($request->slug === 'stripe') {
            $rules['publishable_key'] = 'required|string';
            $rules['secret_key'] = 'required|string';
        }

        $validators = Validator($request->all(), $rules);

        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }

        if (!$request->id) {
            $paymentMethod = PaymentMethod::create(array_merge(
                $request->all(),
                ['status' => PaymentMethod::STATUS_ACTIVE]
            ));
            return $this->sendSuccess('Payment method added successfully!');
        } else {
            $paymentMethod = PaymentMethod::find($request->id);
            if ($paymentMethod) {
                $paymentMethod->update(array_merge(
                    $request->all(),
                    ['status' => PaymentMethod::STATUS_ACTIVE]
                ));
                return $this->sendSuccess('Payment method updated successfully!');
            } else {
                return $this->sendError('Payment method not found', 421);
            }
        }

    }
    public function disable(Request $request)
    {
        $paymentMethod = PaymentMethod::find($request->id);
        if ($paymentMethod) {
            $paymentMethod->update(['status' => PaymentMethod::STATUS_INACTIVE]);
            return $this->sendSuccess('Payment method disabled successfully!');
        } else {
            return $this->sendError('Payment method not found', 421);
        }
    }
}
