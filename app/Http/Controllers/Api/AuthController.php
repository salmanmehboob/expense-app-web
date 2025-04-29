<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $messages = [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a valid string.',
            'name.max' => 'Name must not be longer than 191 characters.',

            'email.required' => 'Email address is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'This email is already taken.',

            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password must be at least 6 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',

            'role.required' => 'Role is required.',
            'role.exists' => 'The selected role is invalid.',
        ];

        $validator = $this->validateRequest($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,name',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign Role
        $user->assignRole($request->role); // e.g., "user" or "admin"

        $token = $user->createToken('API Token')->accessToken;

        return $this->sendResponse([
            'token' => $token,
            'data' => new UserResource($user),
        ], 'User Regsiter Successfully');
    }


    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password')))
            return response()->json(['error' => 'Unauthorized'], 401);

        $user = Auth::user();
        $token = $user->createToken('API Token')->accessToken;

        return $this->sendResponse([
            'token' => $token,
            'data' => new UserResource($user),
        ], 'User Login Successfully');
    }

    public function userInfo()
    {
        $user = Auth::user();
        return $this->sendResponse([
            'data' => new UserResource($user),
        ], 'User data retrieve Successfully ');
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
