<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class bannerController extends Controller
{
    #[OA\Get(
        path: "/banner",
        summary: "Get active banners",
        description: "Retrieve a list of all active banners with associated feed information",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Banners retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "string", example: "success"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Banner")
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["Banner"]
    )]
    public function index()
    {
        $banners = banner::with("feed")->where("active", true)->get();

        return response()->json([
            "status" => "success",
            
            "data" => $banners->map(function ($banner) {
                return [
                    "id" => $banner->id,
                    "feed_id" => $banner->feed_id,
                    "image_url" => asset('storage/' . $banner->feed->image),
                    "ukm" => $banner->feed->unitKegiatan->alias,
                ];
            }),
        ]);
    }
}
