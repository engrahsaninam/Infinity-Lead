<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

trait CommonTraits {


    function sendSuccess($message, $data = '') {
        return response()->json(['message' => $message, 'data' => $data,],200);
    }
    function sendError($error_message, $code = 400, $data = NULL) {
        return response()->json(['errors' => $error_message, 'data' => $data],$code);
    }

    function c_authorize($permission)
    {
        abort_if(Gate::denies($permission), 401, 'Unauthorized');
    }
}
