<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use HasUuids;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::all();
        return  response()->json([
            "data" => $category
         ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(),[
            "title" => "required|min:3|unique:categories,title"
        ]);

        if($validator->fails()){
            return response()->json([
                "message" => $validator->errors()
            ],400);
        }

        $category = new Category();
        $category->title = $request->title;
        $category->save();

        return response()->json([
            "data" => $category
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
