<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranAnggota;
use App\Models\UnitKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class ukmController extends Controller
{
    #[OA\Get(
        path: "/ukms",
        summary: "Get all UKMs",
        description: "Retrieve a list of all Unit Kegiatan Mahasiswa (UKMs) with their latest profiles",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "UKM data retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "UKM data retrieved successfully"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/UkmSummary")
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["UKM"]
    )]
    public function index()
    {
        $ukmData = UnitKegiatan::select("id", "name", "alias", "category", "logo")
            ->with([
                "unitKegiatanProfile" => function ($query) {
                    $query
                        ->select("id", "unit_kegiatan_id", "description", "background_photo")
                        ->latest()
                        ->take(1);
                },
            ])
            ->get()
            ->map(function ($ukm) {
                return [
                    'id' => $ukm->id,
                    'name' => $ukm->name,
                    'alias' => $ukm->alias,
                    'category' => $ukm->category,
                    'logo' => url(Storage::url($ukm->logo)),
                    'profile_image_url' => $ukm->logo ? url(Storage::url($ukm->logo)) : null,
                    'description' => $ukm->unitKegiatanProfile->first()?->description ?? null,
                    'background_photo_url' => $ukm->unitKegiatanProfile->first()?->background_photo ? url(Storage::url($ukm->unitKegiatanProfile->first()->background_photo)) : null,
                ];
            });

        // Return the data as a JSON response
        return response()->json(
            [
                "status" => true,
                "message" => "UKM data retrieved successfully",
                "data" => $ukmData,
            ],
            200
        );
    }

    #[OA\Get(
        path: "/ukm/{id}/profile",
        summary: "Get UKM profile",
        description: "Retrieve basic profile information for a specific UKM",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "UKM ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "UKM profile retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "UKM profile retrieved successfully"),
                        new OA\Property(property: "data", ref: "#/components/schemas/UkmProfile")
                    ]
                )
            ),
            new OA\Response(response: 404, description: "UKM not found"),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["UKM"]
    )]
    public function profile(string $id)
    {
        // Find the UKM by ID
        $ukm = UnitKegiatan::select("id", "name", "alias", "category", "logo")
            ->with([
                "unitKegiatanProfile" => function ($query) {
                    $query->select("id", "unit_kegiatan_id", "description", "background_photo");
                },
            ])
            ->findOrFail($id);

        // Format the response data
        $responseData = [
            'id' => $ukm->id,
            'name' => $ukm->name,
            'alias' => $ukm->alias,
            'category' => $ukm->category,
            'logo' => $ukm->logo,
            'profile_image_url' => $ukm->logo ? url(Storage::url($ukm->logo)) : null,
            'unit_kegiatan_profile' => $ukm->unitKegiatanProfile,
        ];

        // Return the UKM profile as a JSON response
        return response()->json(
            [
                "status" => true,
                "message" => "UKM profile retrieved successfully",
                "data" => $responseData,
            ],
            200
        );
    }

    #[OA\Post(
        path: "/ukm/{id}/register",
        summary: "Register for UKM membership",
        description: "Submit a membership registration request to a specific UKM",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "UKM ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: "Registration submitted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Registration submitted successfully"),
                        new OA\Property(property: "data", ref: "#/components/schemas/RegistrationData")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Already registered",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "You are already registered for this UKM")
                    ]
                )
            ),
            new OA\Response(response: 404, description: "UKM not found"),
            new OA\Response(response: 401, description: "Unauthorized"),
            new OA\Response(response: 500, description: "Registration failed")
        ],
        tags: ["UKM"]
    )]
    public function registerMember(Request $request, string $id)
    {
        // Get the authenticated user
        $user = $request->user();

        // Check if UKM exists
        $ukm = UnitKegiatan::findOrFail($id);

        // Check if the user is already registered for this UKM
        $existingRegistration = PendaftaranAnggota::where("user_id", $user->id)
            ->where("unit_kegiatan_id", $ukm->id)
            ->first();
        if ($existingRegistration) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "You are already registered for this UKM",
                ],
                400
            );
        }

        // try to create a new registration
        $registration = PendaftaranAnggota::create([
            "user_id" => $user->id,
            "unit_kegiatan_id" => $ukm->id,
        ]);

        // Check if the registration was successful
        if (!$registration) {
            return response()->json(
                [
                    "status" => false,
                    "message" => "Registration failed",
                ],
                500
            );
        }

        // Return a success response
        return response()->json(
            [
                "status" => true,
                "message" => "Registration submitted successfully",
                "data" => $registration,
            ],
            201
        );
    }

    #[OA\Get(
        path: "/ukms/search",
        summary: "Search UKMs",
        description: "Search for UKMs by name with optional query parameter",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "q",
                description: "Search query for UKM name",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "UKM search result",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "UKM search result"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/UkmSummary")
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["UKM"]
    )]
    public function search(Request $request)
    {
        $query = $request->input('q');
        $ukmData = UnitKegiatan::select('id', 'name', 'alias', 'category', 'logo')
            ->when($query, function ($q) use ($query) {
                $q->where(function ($subQuery) use ($query) {
                    $subQuery->where('name', 'like', "%$query%")
                        ->orWhere('alias', 'like', "%$query%")
                        ->orWhere('category', 'like', "%$query%");
                });
            })
            ->with([
                'unitKegiatanProfile' => function ($query) {
                    $query->select('id', 'unit_kegiatan_id', 'description', 'background_photo')->latest()->take(1);
                },
            ])
            ->get()
            ->map(function ($ukm) {
                return [
                    'id' => $ukm->id,
                    'name' => $ukm->name,
                    'alias' => $ukm->alias,
                    'category' => $ukm->category,
                    'logo' => $ukm->logo,
                    'profile_image_url' => $ukm->logo ? url(Storage::url($ukm->logo)) : null,
                    'description' => $ukm->unitKegiatanProfile->first()?->description ?? null,
                    'background_photo_url' => $ukm->unitKegiatanProfile->first()?->background_photo ? asset('storage/' . $ukm->unitKegiatanProfile->first()->background_photo) : null,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'UKM search result',
            'data' => $ukmData,
        ], 200);
    }

    #[OA\Get(
        path: "/ukm/{id}/profile-full",
        summary: "Get complete UKM profile",
        description: "Retrieve comprehensive profile information including achievements, recent posts, and activity gallery",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "UKM ID",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "UKM full profile retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "UKM full profile retrieved successfully"),
                        new OA\Property(property: "data", ref: "#/components/schemas/UkmFullProfile")
                    ]
                )
            ),
            new OA\Response(response: 404, description: "UKM not found"),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["UKM"]
    )]
    public function profileFull(string $id)
    {
        $ukm = \App\Models\UnitKegiatan::with([
            'unitKegiatanProfile' => function ($query) {
                $query->orderByDesc('period')->limit(1);
            },
            'achievements:id,unit_kegiatan_id,title,image,description',
            'feeds' => function ($query) {
                $query->orderByDesc('created_at')->limit(5)->select('id', 'unit_kegiatan_id', 'title', 'image', 'content', 'type', 'created_at');
            },
            'activityGalleries:id,unit_kegiatan_id,image'
        ])->findOrFail($id);

        $profile = $ukm->unitKegiatanProfile->first();

        return response()->json([
            'status' => true,
            'message' => 'UKM full profile retrieved successfully',
            'data' => [
                'id' => $ukm->id,
                'name' => $ukm->name,
                'alias' => $ukm->alias,
                'category' => $ukm->category,
                'profile_image_url' => $ukm->logo ? asset('storage/' . $ukm->logo) : null,
                'description' => $profile?->description,
                'vision_mission' => $profile?->vision_mission,
                'background_photo_url' => $profile?->background_photo ? asset('storage/' . $profile->background_photo) : null,
                'achievements' => $ukm->achievements->map(function ($a) {
                    return [
                        'title' => $a->title,
                        'description' => $a->description,
                        'image_url' => asset('storage/' . $a->image),
                    ];
                }),
                'recent_posts' => $ukm->feeds->map(function ($f) {
                    return [
                        'id' => $f->id,
                        'title' => $f->title,
                        'type' => $f->type,
                        'image_url' => asset('storage/' . $f->image),
                        'created_at' => $f->created_at,
                    ];
                }),
                'activity_gallery' => $ukm->activityGalleries->map(function ($g) {
                    return [
                        'image_url' => asset('storage/' . $g->image),
                    ];
                }),
            ]
        ], 200);
    }
}
