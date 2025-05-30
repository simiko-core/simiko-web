<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\UnitKegiatan;

class postController extends Controller
{
    public function index(){

    

    $posts = Post::select('id', 'title', 'content', 'image', 'unit_kegiatan_id', 'created_at')
        ->with('unitKegiatan:id,name,logo')
        ->orderBy('created_at', 'desc')
        ->get();

    // check if posts are empty
    if ($posts->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No posts found',
        ], 404);
    }
    
    return response()->json($posts);
}


    // GET /api/post/{id}
    public function show($id)
    {
    $post = Post::select('id', 'title', 'content', 'image', 'unit_kegiatan_id', 'created_at')
        ->with('unitKegiatan:id,name,logo')
        ->orderBy('created_at', 'desc')
        ->where('id', $id)
        ->get();

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post);
    }

    // PUT /api/post/{id}
    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'unit_kegiatan_id' => 'sometimes|exists:unit_kegiatans,id',
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('image')) {
            // delete old image
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $post->image = $request->file('image')->store('posts', 'public');
        }

        $post->update($request->only('unit_kegiatan_id', 'title', 'content'));

        return response()->json($post);
    }

    // DELETE /api/post/{id}
    public function destroy($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
