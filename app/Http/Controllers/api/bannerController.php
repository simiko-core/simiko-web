<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class bannerController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $banners = banner::with("post")->where("active", true)->get();

        return response()->json([
            "status" => "success",
            "data" => $banners->map(function ($banner) {
                return [
                    "id" => $banner->id,
                    "post_id" => $banner->post_id,
                    "image_url" => Storage::url($banner->post->image),
                ];
            }),
        ]);
    }
}
