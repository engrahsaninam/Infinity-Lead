<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiKeyController extends Controller
{
    use CommonTraits;

    public function fetch_openAi(Request $request)
    {
        $key = ApiKey::where('user_id', Auth::id())->where('type', ApiKey::TYPE_AI)->first();
        return $this->sendSuccess('Open AI key fetched successfully!', $key);
    }
    public function delete_openAi(Request $request)
    {
        ApiKey::where('user_id', Auth::id())->where('type', ApiKey::TYPE_AI)->delete();
        return $this->sendSuccess('Open AI key deleted successfully!');
    }
    public function update_openAi(Request $request)
    {
        $validators = Validator($request->all(), [
            'key' => 'required',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $existingKey = ApiKey::where('user_id', Auth::id())->where('type', ApiKey::TYPE_AI)->first();
        if ($existingKey) {
            $existingKey->update([
                'key' => $request->key,
            ]);
            $message = 'Open Ai key updated successfully!';
        } else {
            ApiKey::create([
                'user_id' => Auth::id(),
                'type' => ApiKey::TYPE_AI,
                'key' => $request->key,
            ]);
            $message = 'Open Ai key created successfully!';
        }
        return $this->sendSuccess($message);

    }


    //

    public function fetch_verificationTool(Request $request)
    {
        $key = ApiKey::where('user_id', Auth::id())->where('type', ApiKey::TYPE_VT)->first();
        return $this->sendSuccess('Verification tool key fetched successfully!', $key);
    }
    public function delete_verificationTool(Request $request)
    {
        ApiKey::where('user_id', Auth::id())->where('type', ApiKey::TYPE_VT)->delete();
        return $this->sendSuccess('Verification tool key deleted successfully!');
    }
    public function update_verificationTool(Request $request)
    {
        $validators = Validator($request->all(), [
            'key' => 'required',
            'tool' => 'required',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $existingKey = ApiKey::where('user_id', Auth::id())->where('type', ApiKey::TYPE_VT)->first();
        if ($existingKey) {
            $existingKey->update([
                'key' => $request->key,
            ]);
            $message = 'Verification tool key updated successfully!';
        } else {
            ApiKey::create([
                'user_id' => Auth::id(),
                'type' => ApiKey::TYPE_VT,
                'key' => $request->key,
                'tool' => $request->tool,
            ]);
            $message = 'Verification tool key created successfully!';
        }
        return $this->sendSuccess($message);
    }
}
