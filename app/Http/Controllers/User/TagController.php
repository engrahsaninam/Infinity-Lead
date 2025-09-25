<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Lists;
use App\Models\Mapping;
use App\Models\Tag;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{

    use CommonTraits;
    public function count()
    {
        $count = Tag::where('user_id',  Auth::id())->count();
        return $this->sendSuccess('Tag count!', $count);
    }
    public function store(Request $request)
    {
        $validators = Validator($request->all(), [
            'name' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $name = strtoupper(str_replace(' ', '_', $request->name));
        $request->merge(['name'=>$name]);
        if (!$request->id) {
            $request->merge(['user_id' => Auth::id()]);
            Tag::create($request->all());
            return $this->sendSuccess('Tag created successfully!');
        } else {
            $template = Tag::find($request->id);
            $template->update($request->all());
            return $this->sendSuccess('Tag updated successfully!');
        }
    }
    public function fetch(Request $request)
    {
        $lists = Tag::where('user_id', Auth::id())->get();
        return response()->json($lists);
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
    public function fetch_column(Request $request)
    {
        $list = Lists::find($request->list_id);
        if (!$list || !$list->csv) {
            return $this->sendError('List or CSV not found.');
        }
        $headers = array_values($list->csv->headers ?? []);
        $mappings = Mapping::where('list_id', $request->list_id)->pluck('mappings', 'tag_id');
        return $this->sendSuccess('Headers fetched successfully!', [
            'headers' => $headers,
            'mappings' => $mappings,
        ]);
    }
    public function mapping(Request $request){
        $validators = Validator($request->all(), [
            'list_id' => 'required',
        ],[
            'list_id.required'=>'List selection is required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        foreach($request->tags as $tag){
            if(array_key_exists('value', $tag) && $tag['value']){

                Mapping::updateOrCreate([
                    'list_id' => $request->list_id,
                    'tag_id' => $tag['id'],
                ], [
                    'mappings' => $tag['value']
                ]);
            }else{
                Mapping::where('list_id',$request->list_id)->where('tag_id',$tag['id'])->delete();
            }
        }
        return $this->sendSuccess('Mapping saved successfully!');
    }
}
