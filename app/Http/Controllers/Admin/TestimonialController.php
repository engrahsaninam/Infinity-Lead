<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infinity;
use App\Models\Testimonial;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    use CommonTraits;
    public function fetch(Request $request){
        $search = $request->search;
        $lists = Testimonial::query();
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ( $query) use ($search) {
                $query->whereAny(['name','title','feedback'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function store(Request $request){
        $validators = Validator($request->all(), [
            'name' => 'required|string',
            'title' => 'required|string', 
            'feedback' => 'required|string', 
            'rating' => 'required', 
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }

        if($request->file){
            $profile=Infinity::upload($request->file,Infinity::_PUBLIC_TESTIMONIAL_PROFILE);
            $request->merge(['profile'=>$profile]);
        }
        if(!$request->id){
            Testimonial::create($request->all());
            return  $this->sendSuccess('Testimonial added successfully!');
        }else{
            Testimonial::find($request->id)->update($request->all());
            return  $this->sendSuccess('Testimonial updated successfully!');
        }
    }
    public function delete(Request $request){
        $test=Testimonial::find($request->id);
        if($test){
            $test->delete();
        }
        return  $this->sendSuccess('Testimonial deleted successfully!');
    }
}
