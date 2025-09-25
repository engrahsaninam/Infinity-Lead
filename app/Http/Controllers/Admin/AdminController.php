<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\CommonTraits;
use Illuminate\Http\Request;
use App\Models\Infinity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class AdminController extends Controller
{
    //
    use CommonTraits;
    public function fetch(Request $request)
    {
        $search = $request->search;
        $lists = User::where('role', User::ROLE_ADMIN);
        $lists->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereAny(['first_name', 'last_name', 'email'], "like", "%$search%");
            });
        });
        $lists = $lists->paginate($request->records);
        return response()->json($lists);
    }
    public function create(Request $request)
    {
        $validators = Validator($request->all(), [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|unique:users,email' . ($request->id ? ',' . $request->id : ''),
            'password' => [
                $request->id ? 'nullable' : 'required',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ]
        ]);

        if ($validators->fails()) {
            return $this->sendError($validators->messages(), 422);
        }

        $profilePath = null;
        if ($request->hasFile('profile')) {
            $profilePath = Infinity::upload($request->file('profile'), Infinity::_PUBLIC_PROFILE);
        }

        if ($request->id) {
            $user = User::find($request->id);
            $updateData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'role' => User::ROLE_ADMIN,
                'status' => User::STATUS_ACTIVE,

            ];
            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }
            if ($profilePath) {
                $updateData['profile'] = $profilePath;
            }
            $user->update($updateData);
            $message = 'Admin updated successfully!';
        } else {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => User::ROLE_ADMIN,
                'profile' => $profilePath,
                'status'=>User::STATUS_ACTIVE,

            ]);
            $message = 'Admin created successfully!';
        }
        return $this->sendSuccess($message);
    }
    public function edit(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required|exists:users,id',
        ]);
        if ($validators->fails()) {
            return response()->json(['errors' => $validators->messages()], 422);
        }
        $user = User::find($request->id);
        return $this->sendSuccess('Admin fetched successfully!', $user);
    }
}
