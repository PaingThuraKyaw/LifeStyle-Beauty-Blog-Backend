<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\BlogResource;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->query("search");
        $category = $request->query("category");
        $filter = Blog::query()->when($search, function ($query) use ($search) {
            $query->where('title', 'like', "%$search%");
        })->when($category, function ($query) use ($category) {
            $category !== "all" &&  $query->where("category_id", $category);
        })->paginate(5);


        $blogs = BlogResource::collection($filter);

        return response()->json([
            "body" => $blogs,
            "pagination" => [
                "total" => $filter->total(),
                "PAGE_SIZE" => $filter->perPage(),
                "current_page" => $filter->currentPage(),
                "total_page" => $filter->lastPage(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3|unique:blogs,title',
            'description' => 'required|min:5',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            "category_id" => ['required', Rule::exists(Category::class, "id")],
        ]);


        if ($validator->fails()) {
            return response()->json([
                "body" => $validator->errors(),
                "message" => "Something went wrong"
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
            "message" => "Blog created succesfully"
        ]);
    }

    /**
     * Display the specified resource.
     */

public function show(string $id)
{
    $blog = Blog::findOrFail($id);

    if(!request()->bearerToken()){
        return response()->json([
            "message" => "Unauthorized"
        ]);
    }

    try {
        return response()->json([
            "body" => new BlogResource($blog)
        ], 200);
    } catch (AuthorizationException $e) {
        return response()->json([
            "message" => "Unauthorized"
        ], 401);
    } catch (Exception $e) {
        return response()->json([
            "message" => "Blog Not Found"
        ], 404);
    }
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:3',
            'description' => 'required|min:5',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            "category_id" => ['required', Rule::exists(Category::class, "id")],
        ]);


        $blog = Blog::findOrFail($id);
        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->category_id = $request->category_id;
        $blog->save();

        if ($request->file("image")) {
        }


        if ($validator->fails()) {
            return response()->json([
                "body" => $validator->errors(),
                "message" => "Unable to create blog"
            ]);
        }

        return $blog;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
