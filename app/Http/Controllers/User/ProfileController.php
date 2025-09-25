<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Infinity;
use App\Models\User;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class ProfileController extends Controller
{
    //
    use CommonTraits;
    public function fetch(Request $request)
    {
        $user = User::find(Auth::id());
        return $this->sendSuccess('Profile fetched successfully!', $user);
    }
    public function update(Request $request)
    {
        $validators = Validator($request->all(), [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        User::find($request->id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);
        return $this->sendSuccess('Details updated successfully!');
    }
    public function help(Request $request)
    {
        $validators = Validator($request->all(), [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'phone' => 'required',
            'email' => 'required',
            'message' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }


        return $this->sendSuccess('Email sent successfully!');
    }

    public function password(Request $request)
    {
        $validators = Validator($request->all(), [
            'old_password' => 'required',
            'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()],
            'confirm_password' => 'required|same:password'
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $user = Auth::user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->update(['password' => Hash::make($request->password)]);
            return $this->sendSuccess('Password updated successfully!');
        } else {
            return $this->sendError('Old password does not match', 421);
        }
    }
    public function profile(Request $request)
    {
        $validators = Validator($request->all(), [
            'profile' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }
        $profile = $request->profile;
        User::find(Auth::id())->update([
            'profile' => Infinity::upload($profile, Infinity::_PUBLIC_PROFILE)
        ]);
        return $this->sendSuccess('Profile updated successfully!');
    }
}
