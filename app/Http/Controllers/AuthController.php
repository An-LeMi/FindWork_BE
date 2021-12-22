<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Enterprise;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $field = $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|confirmed',
            // role required, employee or enterprise
            'role' => 'required|in:employee,enterprise',
        ]);

        $user = User::create([
            'username' => $field['username'],
            'password' => Hash::make($field['password']),
            'role' => $field['role']
        ]);

        // if employee, create employee
        if ($field['role'] == 'employee') {
            $employee = Employee::create([
                'user_id' => $user->id,
            ]);
        }
        // if enterprise, create enterprise
        else if ($field['role'] == 'enterprise') {
            $enterprise = Enterprise::create([
                'user_id' => $user->id,
            ]);
        }

        // generate token
        $token = $user->createToken('Laravel Password Grant Client')->plainTextToken;

        return response([
            'user_id' => $user->id,
            'role' => $user->role,
            'token' => $token,
            'message' => 'User Created',
        ], Response::HTTP_CREATED);
    }

    // logout
    public function logout(Request $request)
    {
        Auth::user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response([
            'message' => 'Logged out successfully'
        ], Response::HTTP_OK);
    }

    // login
    public function login(Request $request)
    {
        $field = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $field['username'])->first();
        if (!$user || !Hash::check($field['password'], $user->password)) {
            return response()->json([
                'message' => 'Username or password is incorrect'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // generate token
        $token = $user->createToken('Laravel Password Grant Client')->plainTextToken;

        return response([
            'user_id' => $user->id,
            'role' => $user->role,
            'token' => $token,
            'message' => 'Successfully logged in'
        ], Response::HTTP_OK);
    }

    // get user
    public function getUser()
    {
        $user = Auth::user();

        return response([
            'user' => $user,
            'message' => 'Successfully logged in'
        ], Response::HTTP_OK);
    }
}
