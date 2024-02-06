<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Blog::all();
        return response()->json([
            "data" => $blogs
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3',
            'description' => 'required|min:5',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            "category_id" => ['required', Rule::exists(Category::class, "id")],
        ]);


        if ($validator->fails()) {
            return response()->json([
                "message" => $validator->errors()
            ]);
        }


         $blog = Blog::create([
            'title' => $request->title,
            'description' => $request->description,
            "category_id" =>  Category::findOrFail($request->category_id)->id
        ]);


        if ($request->file("image")) {
            $file = $request->file('image');
            $path = $file->storeAs('public', $file->getClientOriginalName());
            $url = URL(Storage::url($path));
            // dd($file->getClientOriginalExtension());
            $image = $blog->image()->create([
                "image" => $url,
                "extension" => $file->getClientOriginalExtension(),
            ]);
            $image->save();
            $blog["image"] = $image->image;
            $blog["extension"] =  $file->getClientOriginalExtension();
        }




        return response()->json([
            "data" => $blog
        ]);
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
