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
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Feed retrieved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "feeds",
                                    type: "object",
                                    properties: [
                                        new OA\Property(
                                            property: "post",
                                            type: "array",
                                            items: new OA\Items(
                                                type: "object",
                                                properties: [
                                                    new OA\Property(property: "id", type: "integer", example: 10),
                                                    new OA\Property(property: "type", type: "string", example: "post"),
                                                    new OA\Property(property: "title", type: "string", example: "Sample Post Title"),
                                                    new OA\Property(property: "image_url", type: "string", nullable: true, example: "http://localhost:8000/storage/feeds/sample.jpg"),
                                                    new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                                    new OA\Property(
                                                        property: "ukm",
                                                        type: "object",
                                                        properties: [
                                                            new OA\Property(property: "id", type: "integer", example: 2),
                                                            new OA\Property(property: "name", type: "string", example: "HMTE"),
                                                            new OA\Property(property: "logo_url", type: "string", nullable: true, example: "http://localhost:8000/storage/ukms/logo.jpg")
                                                        ]
                                                    )
                                                ]
                                            )
                                        ),
                                        new OA\Property(
                                            property: "event",
                                            type: "array",
                                            items: new OA\Items(
                                                type: "object",
                                                properties: [
                                                    new OA\Property(property: "id", type: "integer", example: 11),
                                                    new OA\Property(property: "type", type: "string", example: "event"),
                                                    new OA\Property(property: "title", type: "string", example: "Sample Event Title"),
                                                    new OA\Property(property: "image_url", type: "string", nullable: true, example: "http://localhost:8000/storage/feeds/sample.jpg"),
                                                    new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                                    new OA\Property(
                                                        property: "ukm",
                                                        type: "object",
                                                        properties: [
                                                            new OA\Property(property: "id", type: "integer", example: 2),
                                                            new OA\Property(property: "name", type: "string", example: "HMTE"),
                                                            new OA\Property(property: "logo_url", type: "string", nullable: true, example: "http://localhost:8000/storage/ukms/logo.jpg")
                                                        ]
                                                    )
                                                ]
                                            )
                                        )
                                    ]
                                ),
                                new OA\Property(
                                    property: "ukms",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 1),
                                            new OA\Property(property: "name", type: "string", example: "HMIF"),
                                            new OA\Property(property: "logo_url", type: "string", nullable: true, example: "http://localhost:8000/storage/ukms/logo.jpg")
                                        ]
                                    )
                                )
                            ]
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

        $feeds = Feed::with(['unitKegiatan', 'paymentConfiguration'])
            ->select('id', 'unit_kegiatan_id', 'payment_configuration_id', 'type', 'title', 'image', 'created_at', 'event_date', 'is_paid')
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->when($unitKegiatanId, function ($query, $ukmId) {
                return $query->where('unit_kegiatan_id', $ukmId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $feedsData = $feeds->map(function ($feed) {
            $feedData = [
                'id' => $feed->id,
                'type' => $feed->type,
                'title' => $feed->title,
                'image_url' => $feed->image ? asset('storage/' . $feed->image) : null,
                'created_at' => $feed->created_at,
                'ukm' => [
                    'id' => $feed->unitKegiatan->id,
                    'name' => $feed->unitKegiatan->name,
                    'logo_url' => $feed->unitKegiatan->logo ? asset('storage/' . $feed->unitKegiatan->logo) : null,
                ]
            ];

            // Add event-specific data for events
            if ($feed->type === 'event') {
                $feedData['event_date'] = $feed->event_date;
                $feedData['is_paid'] = $feed->is_paid;
                
                // Add payment configuration details for paid events
                if ($feed->is_paid && $feed->paymentConfiguration) {
                    $feedData['payment_configuration'] = [
                        'id' => $feed->paymentConfiguration->id,
                        'name' => $feed->paymentConfiguration->name,
                        'description' => $feed->paymentConfiguration->description,
                        'amount' => $feed->paymentConfiguration->amount,
                        'currency' => $feed->paymentConfiguration->currency,
                        'payment_methods' => $feed->paymentConfiguration->payment_methods,
                        'custom_fields' => $feed->paymentConfiguration->custom_fields,
                    ];
                }
            }

            return $feedData;
        });

        // Group feeds by type
        $groupedFeeds = [
            'post' => $feedsData->where('type', 'post')->values()->toArray(),
            'event' => $feedsData->where('type', 'event')->values()->toArray()
        ];


        // Get all UKM with id and alias
        $ukms = \App\Models\UnitKegiatan::select('id', 'alias')
            ->whereNotNull('alias')
            ->orderBy('alias')
            ->get()
            ->map(function ($ukm) {
                return [
                    'id' => $ukm->id,
                    'name' => $ukm->alias
                ];
            })
            ->toArray();

        $responseData = [
            'feeds' => $groupedFeeds,
            'ukms' => $ukms
        ];

        return ApiResponse::success($responseData, 'Feed retrieved successfully');
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
                        new OA\Property(property: "success", type: "boolean", example: true),
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
                'event_type', 'location', 'is_paid', 
                'unit_kegiatan_id', 'payment_configuration_id', 'created_at'
            )
            ->with(['unitKegiatan:id,name,logo,alias', 'paymentConfiguration'])
            ->findOrFail($id);

            $data = [
                'id' => $feed->id,
                'type' => $feed->type,
                'title' => $feed->title,
                'content' => $feed->content,
                'image_url' => $feed->image ? asset('storage/' . $feed->image) : null,
                'ukm' => [
                    'id' => $feed->unitKegiatan->id,
                    'name' => $feed->unitKegiatan->name,
                    'alias' => $feed->unitKegiatan->alias,
                    'logo_url' => $feed->unitKegiatan->logo ? asset('storage/' . $feed->unitKegiatan->logo) : null,
                ],
                'created_at' => $feed->created_at,
            ];

            // Add event-specific data for events
            if ($feed->type === 'event') {
                $data['event_date'] = $feed->event_date;
                $data['event_type'] = $feed->event_type;
                $data['location'] = $feed->location;
                $data['is_paid'] = $feed->is_paid;
                
                // Add payment configuration details for paid events
                if ($feed->is_paid && $feed->paymentConfiguration) {
                    $data['payment_configuration'] = [
                        'id' => $feed->paymentConfiguration->id,
                        'name' => $feed->paymentConfiguration->name,
                        'description' => $feed->paymentConfiguration->description,
                        'amount' => $feed->paymentConfiguration->amount,
                        'currency' => $feed->paymentConfiguration->currency,
                        'payment_methods' => $feed->paymentConfiguration->payment_methods,
                        'custom_fields' => $feed->paymentConfiguration->custom_fields,
                    ];
                }
            }

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
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Feed retrieved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "feeds",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 10),
                                            new OA\Property(property: "type", type: "string", example: "post"),
                                            new OA\Property(property: "title", type: "string", example: "Sample Post Title"),
                                            new OA\Property(property: "image_url", type: "string", nullable: true, example: "http://localhost:8000/storage/feeds/sample.jpg"),
                                            new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                            new OA\Property(
                                                property: "ukm",
                                                type: "object",
                                                properties: [
                                                    new OA\Property(property: "id", type: "integer", example: 2),
                                                    new OA\Property(property: "name", type: "string", example: "HMTE")
                                                ]
                                            )
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: "ukms",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 1),
                                            new OA\Property(property: "name", type: "string", example: "HMIF")
                                        ]
                                    )
                                )
                            ]
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
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Feed retrieved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "feeds",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 10),
                                            new OA\Property(property: "type", type: "string", example: "event"),
                                            new OA\Property(property: "title", type: "string", example: "Sample Event Title"),
                                            new OA\Property(property: "image_url", type: "string", nullable: true, example: "http://localhost:8000/storage/feeds/sample.jpg"),
                                            new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                            new OA\Property(
                                                property: "ukm",
                                                type: "object",
                                                properties: [
                                                    new OA\Property(property: "id", type: "integer", example: 2),
                                                    new OA\Property(property: "name", type: "string", example: "HMTE")
                                                ]
                                            )
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: "ukms",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "integer", example: 1),
                                            new OA\Property(property: "name", type: "string", example: "HMIF")
                                        ]
                                    )
                                )
                            ]
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
