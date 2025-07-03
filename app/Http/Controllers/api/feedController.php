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
                                                    new OA\Property(property: "is_paid", type: "boolean", example: true, description: "Only present for paid events"),
                                                    new OA\Property(property: "max_participants", type: "integer", nullable: true, example: 100, description: "Maximum number of participants allowed. Null if unlimited. Only present for paid events."),
                                                    new OA\Property(property: "current_registrations", type: "integer", example: 75, description: "Current number of registered participants. Only present for paid events."),
                                                    new OA\Property(property: "available_slots", type: "integer", nullable: true, example: 25, description: "Number of available slots remaining. Null if unlimited. Only present for paid events."),
                                                    new OA\Property(property: "is_full", type: "boolean", example: false, description: "Whether the event has reached maximum capacity. Only present for paid events."),
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
            ->select('id', 'unit_kegiatan_id', 'payment_configuration_id', 'type', 'title', 'image', 'created_at', 'event_date', 'is_paid', 'max_participants')
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

            // Add is_paid field for events that are paid
            if ($feed->type === 'event' && $feed->is_paid) {
                $feedData['is_paid'] = $feed->is_paid;

                // Add participant information for paid events
                $maxParticipants = $feed->max_participants;
                $currentRegistrations = $feed->getTotalRegistrationsCount();

                $feedData['max_participants'] = $maxParticipants;
                $feedData['current_registrations'] = $currentRegistrations;

                if ($maxParticipants !== null) {
                    $feedData['available_slots'] = max(0, $maxParticipants - $currentRegistrations);
                    $feedData['is_full'] = $currentRegistrations >= $maxParticipants;
                } else {
                    $feedData['available_slots'] = null; // Unlimited
                    $feedData['is_full'] = false;
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
        $ukms = \App\Models\UnitKegiatan::select('id', 'alias', 'logo')
            ->whereNotNull('alias')
            ->orderBy('alias')
            ->get()
            ->map(function ($ukm) {
                return [
                    'id' => $ukm->id,
                    'name' => $ukm->alias,
                    'logo_url' => $ukm->logo ? asset('storage/' . $ukm->logo) : null,
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
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 11),
                                new OA\Property(property: "type", type: "string", example: "event"),
                                new OA\Property(property: "title", type: "string", example: "Workshop Laravel"),
                                new OA\Property(property: "content", type: "string", example: "Join us for an exciting Laravel workshop..."),
                                new OA\Property(property: "image_url", type: "string", nullable: true, example: "http://localhost:8000/storage/feeds/sample.jpg"),
                                new OA\Property(property: "event_date", type: "string", format: "date", nullable: true, example: "2024-07-15", description: "Only present for events"),
                                new OA\Property(property: "event_type", type: "string", nullable: true, example: "Workshop", description: "Only present for events"),
                                new OA\Property(property: "location", type: "string", nullable: true, example: "Room A101", description: "Only present for events"),
                                new OA\Property(property: "is_paid", type: "boolean", example: true, description: "Only present for events"),
                                new OA\Property(property: "amount", type: "number", format: "float", example: 50000, description: "Only present for paid events"),
                                new OA\Property(property: "link", type: "string", example: "https://payment.example.com/pay/11", description: "Only present for paid events"),
                                new OA\Property(property: "max_participants", type: "integer", nullable: true, example: 100, description: "Maximum number of participants allowed. Null if unlimited. Only present for paid events."),
                                new OA\Property(property: "current_registrations", type: "integer", example: 75, description: "Current number of registered participants. Only present for paid events."),
                                new OA\Property(property: "available_slots", type: "integer", nullable: true, example: 25, description: "Number of available slots remaining. Null if unlimited. Only present for paid events."),
                                new OA\Property(property: "is_full", type: "boolean", example: false, description: "Whether the event has reached maximum capacity. Only present for paid events."),
                                new OA\Property(
                                    property: "ukm",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "integer", example: 2),
                                        new OA\Property(property: "name", type: "string", example: "HMTE"),
                                        new OA\Property(property: "alias", type: "string", example: "hmte"),
                                        new OA\Property(property: "logo_url", type: "string", nullable: true, example: "http://localhost:8000/storage/ukms/logo.jpg")
                                    ]
                                ),
                                new OA\Property(property: "created_at", type: "string", format: "date-time")
                            ]
                        )
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
                'id',
                'type',
                'title',
                'content',
                'image',
                'event_date',
                'event_type',
                'location',
                'is_paid',
                'max_participants',
                'unit_kegiatan_id',
                'payment_configuration_id',
                'created_at'
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

                // Add amount and payment link if paid event has payment configuration
                if ($feed->is_paid && $feed->paymentConfiguration) {
                    $data['amount'] = $feed->paymentConfiguration->amount;
                    $data['link'] = $feed->getRegistrationUrl();
                }

                // Add max participants and available slots information for paid events
                if ($feed->is_paid) {
                    $maxParticipants = $feed->max_participants;
                    $currentRegistrations = $feed->getTotalRegistrationsCount();

                    $data['max_participants'] = $maxParticipants;
                    $data['current_registrations'] = $currentRegistrations;

                    if ($maxParticipants !== null) {
                        $data['available_slots'] = max(0, $maxParticipants - $currentRegistrations);
                        $data['is_full'] = $currentRegistrations >= $maxParticipants;
                    } else {
                        $data['available_slots'] = null; // Unlimited
                        $data['is_full'] = false;
                    }
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
                                            new OA\Property(property: "is_paid", type: "boolean", example: true, description: "Only present for paid events"),
                                            new OA\Property(property: "max_participants", type: "integer", nullable: true, example: 100, description: "Maximum number of participants allowed. Null if unlimited. Only present for paid events."),
                                            new OA\Property(property: "current_registrations", type: "integer", example: 75, description: "Current number of registered participants. Only present for paid events."),
                                            new OA\Property(property: "available_slots", type: "integer", nullable: true, example: 25, description: "Number of available slots remaining. Null if unlimited. Only present for paid events."),
                                            new OA\Property(property: "is_full", type: "boolean", example: false, description: "Whether the event has reached maximum capacity. Only present for paid events."),
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
