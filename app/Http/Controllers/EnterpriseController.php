<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnterpriseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $enterprises = Enterprise::all();
        return response()->json([
            'enterprises' => $enterprises,
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
        $field = $request->validate([
            "name" => "required",
            "email" => "required|email|unique:enterprises",
        ]);

        // check user_id is not enterprise
        $user = auth()->user();
        if (!$user | $user->role != 'enterprise') {
            return response()->json([
                'message' => 'user_id is not enterprise'
            ], Response::HTTP_BAD_REQUEST);
        }

        // add user_id to field
        $field['user_id'] = $user->id;

        $enterprise = Enterprise::create($field);

        return response()->json([
            'enterprise' => $enterprise,
            'message' => 'enterprise created'
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Enterprise  $enterprise
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $enterprise = Enterprise::find($id);
        if (!$enterprise) {
            return response()->json([
                'message' => 'enterprise not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'enterprise' => $enterprise,
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Enterprise  $enterprise
     * @return \Illuminate\Http\Response
     */
    public function edit(Enterprise $enterprise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Enterprise  $enterprise
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $enterprise = Enterprise::find($id);
        if (!$enterprise) {
            return response()->json([
                'message' => 'enterprise not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $field = $request->validate([
            "name" => "required",
            "email" => "required|email|unique:enterprises",
        ]);

        $enterprise->update($field);

        return response()->json([
            'enterprise' => $enterprise,
            'message' => 'enterprise updated'
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Enterprise  $enterprise
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $enterprise = Enterprise::find($id);

        if (!$enterprise) {
            return response()->json([
                'message' => 'enterprise not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $enterprise->delete();

        return response()->json([
            'message' => 'enterprise deleted'
        ], Response::HTTP_OK);
    }

    // get all jobs of enterprise
    public function getJobs($id)
    {
        //
        $enterprise = Enterprise::find($id);
        if (!$enterprise) {
            return response()->json([
                'message' => 'enterprise not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $jobs = $enterprise->jobs;

        return response()->json([
            'jobs' => $jobs,
        ], Response::HTTP_OK);
    }
}
