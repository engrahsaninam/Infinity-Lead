<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemplateController extends Controller
{
    use CommonTraits;
    public function count()
    {
        $count = Template::where('user_id', Auth::id())->count();
        return $this->sendSuccess('Templates count!', $count);
    }
    public function store(Request $request)
    {
        $validators = Validator($request->all(), [
            'name' => 'required',
            'type' => 'required',
            'body' => 'required'
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        
        if (!$request->id) {
            $request->merge(['user_id' => Auth::id()]);
            Template::create($request->all());
            return $this->sendSuccess('Template created successfully!');
        } else {
            $template=Template::find($request->id);
            $template->update($request->all());
            if(!$request->signature){
                $template->update(['signature'=>null]);
            }
            return $this->sendSuccess('Template updated successfully!');
        }
    }
    public function fetch(Request $request)
    {
        $search = $request->search;
        $lists = Template::where('user_id', Auth::id());
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['body'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function edit(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $template = Template::find($request->id);
        return $this->sendSuccess('Template fetched successfully!', $template);

    }
    public function delete(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        Template::find($request->id)->delete();
        return $this->sendSuccess('Deleted successfully!');
    }
}
