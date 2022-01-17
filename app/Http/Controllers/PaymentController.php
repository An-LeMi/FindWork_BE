<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class PaymentController extends Controller
{
    //
    public function pay(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'password' => 'required|string',
            'month' => 'required|integer',
        ]);

        $user = auth()->user();
        $bankAccount = $user->bankAccounts->find($request['id']);

        if ($bankAccount == NULL) {
            return response()->json([
                'message' => 'Bank account not found',
            ], Response::HTTP_NOT_FOUND);
        }

        if (!Hash::check($request['password'], $bankAccount->password)) {
            return response()->json([
                'message' => 'Password is incorrect',
            ], Response::HTTP_NOT_FOUND);
        }

        return UserController::update_rank($request['month']);
    }
}
