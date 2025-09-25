<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    //
    use CommonTraits;
    public function fetch(Request $request)
    {
        $search = $request->search;
        $lists = Currency::query();
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['name', 'code','symbol'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function store(Request $request)
    {
        $validators = Validator($request->all(), [
            'name' => 'required|string',
            'code' => 'required|string',
            'format' => [
                'required',
                'string',
                'regex:/\{PRICE\}/'
            ],
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        if (!$request->id) {
            Currency::create($request->all());
            return $this->sendSuccess('Currency added successfully!');
        } else {
            Currency::find($request->id)->update($request->all());
            return $this->sendSuccess('Currency updated successfully!');
        }
    }
    public function delete(Request $request)
    {
        $Currency = Currency::find($request->id);
        if ($Currency) {
            if($Currency->id ===1){
                return $this->sendError('Default currency cannot be deleted!',421);
            }else{
                $Currency->delete();
            }
        }
        return $this->sendSuccess('Currency deleted successfully!');
    }
}
