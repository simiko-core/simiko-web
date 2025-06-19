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
    public function index()
    {
        // Fetch posts with related unit kegiatan data
        $posts = Post::select(
            "id",
            "title",
            "content",
            "image",
            "unit_kegiatan_id",
            "created_at"
        )
            ->with("unitKegiatan:id,name,logo")
            ->orderBy("created_at", "desc")
            ->get();

        // Return the posts as a JSON response
        return response()->json(
            [
                "status" => true,
                "message" => "Posts retrieved successfully",
                "data" => $posts,
            ],
            200
        );
    }

    // GET /api/post/{id}
    public function show($id)
    {
        // Fetch a single post by ID with related unit kegiatan data
        $post = Post::select(
            "id",
            "title",
            "content",
            "image",
            "unit_kegiatan_id",
            "created_at"
        )
            ->with("unitKegiatan:id,name,logo")
            ->orderBy("created_at", "desc")
            ->where("id", $id)
            ->get();

        // Return the posts as a JSON response
        return response()->json(
            [
                "status" => true,
                "message" => "Posts retrieved successfully",
                "data" => $post,
            ],
            200
        );
    }
}
