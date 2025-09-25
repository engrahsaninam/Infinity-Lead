<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Plan;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    //
    use CommonTraits;
    public function currencies(){
        return $this->sendSuccess('Currencies fetched successfully!', Currency::all()->map(function ($currency) {
            return [
                'key' => $currency->id,
                'value' => $currency->name
            ];
        }));
    }
    public function fetch(Request $request)
    {
        $search = $request->search;
        $lists = Plan::query()->with('currency');
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['name', 'description'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function store(Request $request)
    {
        $validators = Validator($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'currency_id' => 'required|integer',
            'credits' => 'required|integer',

            'options.email_max' => 'required',
            'options.list_max' => 'required',
            'options.subscriber_max' => 'required',
            'options.subscriber_per_list_max' => 'required',
            'options.campaign_max' => 'required',
            'options.automation_max' => 'required',
            'options.max_size_upload_total' => 'required',
            'options.max_file_size_upload' => 'required',

        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        } 
        if($request->price<0){
            return $this->sendError('Price must be greater than zero.',421);
        } 
        if (!$request->id) {
            Plan::create($request->all());
            return $this->sendSuccess('Plan added successfully!');
        } else {
            Plan::find($request->id)->update($request->all());
            return $this->sendSuccess('Plan updated successfully!');
        }
        
    }
    public function delete(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|integer|exists:plans,id',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }   
        $plan = Plan::find($request->id);
        if (!$plan) {
            return $this->sendError('Plan not found!', 404);
        }       
        $plan->delete();
        return $this->sendSuccess('Plan deleted successfully!');
    }
}
