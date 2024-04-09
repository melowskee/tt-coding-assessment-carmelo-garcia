<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;
use App\Models\Tag;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'tag_ids' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Auth::user();

        $post = $user->posts()->create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $post->tags()->attach($request->tag_ids);

        return response()->json(['post' => $post, 'message' => 'Post created successfully'], 201);
    }
}