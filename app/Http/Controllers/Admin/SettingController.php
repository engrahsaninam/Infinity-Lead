<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;

class SettingController extends Controller
{
     use CommonTraits;
    public function fetch(Request $request){
        $setting = Setting::where('key',$request->key)->first();
        return response()->json($setting);
    }
    public function store(Request $request){
        $validators = Validator($request->all(), [
            'key' => 'required|string',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $key=$request->key;
        Setting::updateOrCreate(['key' => $key], ['value' => $request->value]);
        return  $this->sendSuccess('Updated successfully!');
    }
    public function delete(Request $request){
        $test=Setting::find($request->id);
        if($test){
            $test->delete();
        }
        return  $this->sendSuccess('Setting deleted successfully!');
    }
}
