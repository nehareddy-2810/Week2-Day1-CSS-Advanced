<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // GET /api/posts
    public function index()
    {
        return response()->json(Post::all(), 200);
    }

    // POST /api/posts
    public function store(Request $request)
    {
        $post = Post::create($request->all());

        return response()->json($post, 201);
    }

    // GET /api/posts/{id}
    public function show($id)
    {
        return response()->json(Post::findOrFail($id), 200);
    }

    // PUT /api/posts/{id}
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $post->update($request->all());

        return response()->json($post, 200);
    }

    // DELETE /api/posts/{id}
    public function destroy($id)
    {
        Post::destroy($id);

        return response()->json([
            'message' => 'Post deleted successfully'
        ], 200);
    }
}