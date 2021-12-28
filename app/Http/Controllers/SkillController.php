<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SkillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $skills = Skill::all();
        return response()->json([
            'skills' => $skills,
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
            "name" => "required|string",
            "category_id" => "required|integer",
        ]);

        $skill = Skill::create($field);

        return response()->json([
            'skill' => $skill,
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $skill = Skill::find($id);
        if (!$skill) {
            return response()->json([
                'message' => 'skill not found'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'skill' => $skill,
        ], Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function edit(Skill $skill)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $skill = Skill::find($id);
        if (!$skill) {
            return response()->json([
                'message' => 'skill not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $field = $request->validate([
            "name" => "required|string",
            "category_id" => "required|integer",
        ]);

        $skill->update($field);

        return response()->json([
            'skill' => $skill,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Skill  $skill
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $skill = Skill::find($id);
        if (!$skill) {
            return response()->json([
                'message' => 'skill not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $skill->delete();

        return response()->json([
            'message' => 'skill deleted',
        ], Response::HTTP_OK);
    }

    // search skill
    public function searchSkillName($name)
    {
        $skill = Skill::where('name', 'LIKE', '%' . $name . '%')->get();
        if (count($skill)) {
            return response()->json([
                'skill' => $skill,
                'message' => 'Successful search'
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'message' => 'Skill not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
