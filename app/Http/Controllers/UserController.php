<?php

namespace App\Http\Controllers;

use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::all();
        return response()->json([
            'users' => $users,
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'user' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(user $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $field = $request->validate([
            "username" => "required|string",
            "password" => "required|string",
        ]);

        $user->update($field);

        return response()->json([
            'user' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\user  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], Response::HTTP_NOT_FOUND);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted',
        ], Response::HTTP_OK);
    }

    public function update_password(Request $request, $id)
    {
        $field = $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        $user = User::find($id);
        if ((Hash::check($field['old_password'], $user->password) && $user)) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
            return response()->json([
                'user' => $user,
                'message' => 'Update password success'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Password is incorrect'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    public static function update_rank($month)
    {
        $user = auth()->user();
        // cast $user as User
        $user = User::find($user->id);

        // if don't have rank
        // rank_expire_date = now + month
        // else rank_expire_date = rank_expire_date + month
        if ($user->rank == 0) {
            $user->update([
                'rank' => 1,
                'rank_expire_date' => now()->addMonth($month)
            ]);
        } else {
            $user->update([
                'rank_expire_date' => date('Y-m-d H:i:s', strtotime($user->rank_expire_date . ' + ' . $month . ' month'))
            ]);
        }

        return response()->json([
            'user' => $user,
            'message' => 'Update rank success'
        ], Response::HTTP_OK);
    }
}
