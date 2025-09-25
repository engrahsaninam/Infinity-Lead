<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Infinity;
use App\Models\Team;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;

class TeamController extends Controller
{

    use CommonTraits;
    public function fetch(Request $request){
        $search = $request->search;
        $lists = Team::query();
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ( $query) use ($search) {
                $query->whereAny(['name','title'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function store(Request $request){
        $validators = Validator($request->all(), [
            'name' => 'required|string',
            'title' => 'required|string', 
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }

        if($request->file){
            $profile=Infinity::upload($request->file,Infinity::_PUBLIC_TEAM_PROFILE);
            $request->merge(['profile'=>$profile]);
        }
        if(!$request->id){
            Team::create($request->all());
            return  $this->sendSuccess('Team added successfully!');
        }else{
            Team::find($request->id)->update($request->all());
            return  $this->sendSuccess('Team updated successfully!');
        }
    }
    public function delete(Request $request){
        $test=Team::find($request->id);
        if($test){
            $test->delete();
        }
        return  $this->sendSuccess('Team deleted successfully!');
    }
    //
}
