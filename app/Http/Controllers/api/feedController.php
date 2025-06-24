<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Feed;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use OpenApi\Attributes as OA;

class feedController extends Controller
{
    #[OA\Get(
        path: "/feed",
        summary: "Get all feeds (posts and events)",
        description: "Retrieve a list of all feeds including posts and events with optional filtering",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "type",
                description: "Filter by feed type",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["post", "event"])
            ),
            new OA\Parameter(
                name: "ukm_id",
                description: "Filter by UKM ID",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Feed retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Feed retrieved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/FeedSummary")
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["Feeds"]
    )]
    public function index(Request $request)
    {
        $type = $request->get('type'); // Optional filter by type
        $unitKegiatanId = $request->get('ukm_id'); // Optional filter by UKM

        $feeds = Feed::select(
            'id', 'type', 'title', 'image', 'unit_kegiatan_id', 'created_at'
        )
        ->with('unitKegiatan:id,alias')
        ->when($type, function ($query, $type) {
            return $query->where('type', $type);
        })
        ->when($unitKegiatanId, function ($query, $unitKegiatanId) {
            return $query->where('unit_kegiatan_id', $unitKegiatanId);
        })
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();

        $data = $feeds->map(function ($feed) {
            return [
                'id' => $feed->id,
                'type' => $feed->type,
                'title' => $feed->title,
                'image_url' => $feed->image ? asset('storage/' . $feed->image) : null,
                'created_at' => $feed->created_at,
                'ukm_alias' => $feed->unitKegiatan->alias,
            ];
        });

        return ApiResponse::success($data, 'Feed retrieved successfully');
    }

    #[OA\Get(
        path: "/feed/{id}",
        summary: "Get specific feed item",
        description: "Retrieve detailed information about a specific feed item by ID",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Feed ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Feed item retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Feed item retrieved successfully"),
                        new OA\Property(property: "data", ref: "#/components/schemas/FeedDetail")
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Feed item not found"),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["Feeds"]
    )]
    public function show(string $id)
    {
        try {
            $feed = Feed::select(
                'id', 'type', 'title', 'content', 'image', 'event_date', 
                'event_type', 'location', 'is_paid', 'price', 'payment_methods', 
                'unit_kegiatan_id', 'created_at'
            )
            ->with('unitKegiatan:id,name,logo,alias')
            ->findOrFail($id);

            $data = [
                'id' => $feed->id,
                'type' => $feed->type,
                'title' => $feed->title,
                'content' => $feed->content,
                'image_url' => $feed->image ? asset('storage/' . $feed->image) : null,
                'event_date' => $feed->event_date,
                'event_type' => $feed->event_type,
                'location' => $feed->location,
                'is_paid' => $feed->is_paid,
                'price' => $feed->price,
                'payment_methods' => $feed->payment_methods,
                'ukm' => [
                    'id' => $feed->unitKegiatan->id,
                    'name' => $feed->unitKegiatan->name,
                    'alias' => $feed->unitKegiatan->alias,
                    'logo_url' => $feed->unitKegiatan->logo ? asset('storage/' . $feed->unitKegiatan->logo) : null,
                ],
                'created_at' => $feed->created_at,
            ];

            return ApiResponse::success($data, 'Feed item retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound('Feed item not found');
        }
    }

    #[OA\Get(
        path: "/posts",
        summary: "Get posts only",
        description: "Retrieve a list of posts only (excludes events)",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "ukm_id",
                description: "Filter by UKM ID",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Posts retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Feed retrieved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/FeedSummary")
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["Feeds"]
    )]
    public function posts(Request $request)
    {
        $request->merge(['type' => 'post']);
        return $this->index($request);
    }

    #[OA\Get(
        path: "/events",
        summary: "Get events only",
        description: "Retrieve a list of events only (excludes posts)",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "ukm_id",
                description: "Filter by UKM ID",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Events retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Feed retrieved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/FeedSummary")
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["Feeds"]
    )]
    public function events(Request $request)
    {
        $request->merge(['type' => 'event']);
        return $this->index($request);
    }
}
