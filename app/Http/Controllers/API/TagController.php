<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:tags,name',
        ]);

        $tag = Tag::create([
            'name' => $request->name,
        ]);

        return response()->json(['tag' => $tag, 'message' => 'Tag created successfully'], 201);
    }
}