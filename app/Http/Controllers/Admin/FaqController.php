<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    use CommonTraits;
    public function fetch(Request $request){
        $search = $request->search;
        $lists = Faq::query();
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ( $query) use ($search) {
                $query->whereAny(['question','answer'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function store(Request $request){
        $validators = Validator($request->all(), [
            'question' => 'required|string',
            'answer' => 'required|string', 
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        if(!$request->id){
            Faq::create($request->all());
            return  $this->sendSuccess('Faq added successfully!');
        }else{
            Faq::find($request->id)->update($request->all());
            return  $this->sendSuccess('Faq updated successfully!');
        }
    }
    public function delete(Request $request){
        $test=Faq::find($request->id);
        if($test){
            $test->delete();
        }
        return  $this->sendSuccess('Faq deleted successfully!');
    }
}
