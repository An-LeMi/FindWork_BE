<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // show all bank accounts of user
        $user = auth()->user();
        $bankAccounts = $user->bankAccounts;

        return Response([
            'bankAccounts' => $bankAccounts,
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
        // make card alias
        if ($request['card_number'] != NULL && $request['password'] != NULL) {
            $request['card_alias'] = '*********' . substr($request['card_number'], -4);
            $request['card_number'] = Hash::make($request['card_number']);
            $request['password'] = Hash::make($request['password']);
        }

        // validate request
        $request->validate([
            "card_number" => "required|string",
            "password" => "required|string",
            "card_holder_name" => "required|string",
        ]);
        $request['user_id'] = auth()->user()->id;

        $card_numbers = BankAccount::all()->pluck('card_number');

        foreach ($card_numbers as $card_number) {
            if (!Hash::check($request['card_number'], $card_number)) {
                return Response([
                    'message' => 'Card number already exists',
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        // create bank account
        $bankAccount = BankAccount::create($request->all());

        return Response([
            'bankAccount' => $bankAccount,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(BankAccount $bankAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // valid this user is owner of this bank account
        $bankAccount = BankAccount::find($id);
        if (!$bankAccount) {
            return response()->json([
                'message' => 'Bank account not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($bankAccount->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'You are not owner of this bank account'
            ], Response::HTTP_FORBIDDEN);
        }

        // delete bank account
        $bankAccount->delete();
    }
}
