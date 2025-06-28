<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Responses\ApiResponse;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Simiko API",
    version: "1.0.0",
    description: "API documentation for Simiko - Student Activity Management System"
)]
#[OA\Server(
    url: "http://localhost:8000/api",
    description: "Local development server"
)]
#[OA\Server(
    url: "https://simiko.software/api",
    description: "Production server"
)]
class authController extends Controller
{
    #[OA\Post(
        path: "/login",
        summary: "User login",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", example: "password123")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Login successful"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "access_token", type: "string"),
                                new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                                new OA\Property(property: "user", ref: "#/components/schemas/User")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Invalid credentials"),
            new OA\Response(response: 422, description: "Validation error")
        ],
        tags: ["Authentication"]
    )]
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|string|email",
            "password" => "required|string",
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        if (!Auth::attempt($request->only("email", "password"))) {
            return ApiResponse::unauthorized("Invalid credentials");
        }

        $user = User::where("email", $request->email)->firstOrFail();
        $token = $user->createToken("auth_token")->plainTextToken;

        return ApiResponse::success([
            "access_token" => $token,
            "token_type" => "Bearer",
            "user" => $user,
        ], "Login successful");
    }

    #[OA\Post(
        path: "/logout",
        summary: "User logout",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Successfully logged out")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["Authentication"]
    )]
    public function logout(Request $request)
    {
        // Revoke all tokens...
        $request->user()->tokens()->delete();

        return ApiResponse::success(null, "Successfully logged out");
    }

    #[OA\Post(
        path: "/register",
        summary: "User registration",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["name", "email", "password"],
                    properties: [
                        new OA\Property(property: "name", type: "string", example: "Test User"),
                        new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                        new OA\Property(property: "password", type: "string", example: "password123"),
                        new OA\Property(property: "phone", type: "string", example: "08123456789"),
                        new OA\Property(
                            property: "img_photo",
                            type: "string",
                            format: "binary",
                            description: "Profile photo (JPG, PNG, GIF, SVG max 2MB)"
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Registration successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Registration successful"),
                        new OA\Property(
                            property: "data",
                            properties: [
                                new OA\Property(property: "access_token", type: "string"),
                                new OA\Property(property: "token_type", type: "string", example: "Bearer"),
                                new OA\Property(property: "user", ref: "#/components/schemas/User")
                            ],
                            type: "object"
                        )
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error")
        ],
        tags: ["Authentication"]
    )]
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => "required|string|min:6",
            "phone" => "nullable|string|max:20",
            "img_photo" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "phone" => $request->phone,
            "photo" => $request->file("img_photo")
                ? $request->file("img_photo")->store("profile_photos", "public")
                : null,
        ]);

        $token = $user->createToken("auth_token")->plainTextToken;

        return ApiResponse::success([
            "access_token" => $token,
            "token_type" => "Bearer",
            "user" => $user,
        ], "Registration successful", 201);
    }

    #[OA\Get(
        path: "/user/profile",
        summary: "Get user profile",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Profile retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "status", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Profile retrieved successfully"),
                        new OA\Property(property: "data", ref: "#/components/schemas/UserProfile")
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthorized")
        ],
        tags: ["User"]
    )]
    public function profile(Request $request)
    {
        return ApiResponse::success([
            "id" => $request->user()->id,
            "name" => $request->user()->name,
            "email" => $request->user()->email,
            "phone" => $request->user()->phone,
            "photo_url" => $request->user()->photo ? asset('storage/' . $request->user()->photo) : null,
        ], "Profile retrieved successfully");
    }
}

// Swagger schemas and security definitions
class SwaggerSchemas {}

#[OA\Schema(
    schema: "User",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "John Doe"),
        new OA\Property(property: "email", type: "string", example: "john@example.com"),
        new OA\Property(property: "phone", type: "string", example: "08123456789"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
#[OA\Schema(
    schema: "UserProfile",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "John Doe"),
        new OA\Property(property: "email", type: "string", example: "john@example.com"),
        new OA\Property(property: "phone", type: "string", example: "08123456789"),
        new OA\Property(property: "photo_url", type: "string", example: "https://example.com/photo.jpg")
    ]
)]
#[OA\Schema(
    schema: "FeedSummary",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "type", type: "string", enum: ["post", "event"], example: "post"),
        new OA\Property(property: "title", type: "string", example: "Sample Post Title"),
        new OA\Property(property: "image_url", type: "string", example: "https://example.com/image.jpg"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "ukm_alias", type: "string", example: "HMIF")
    ]
)]
#[OA\Schema(
    schema: "FeedDetail",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 80),
        new OA\Property(property: "type", type: "string", enum: ["post", "event"], example: "event"),
        new OA\Property(property: "title", type: "string", example: "xxx"),
        new OA\Property(property: "content", type: "string", example: "xxx"),
        new OA\Property(property: "image_url", type: "string", example: "http://localhost:8000/storage/feeds/event-workshop-1.jpg"),
        new OA\Property(
            property: "ukm",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 7),
                new OA\Property(property: "name", type: "string", example: "Unit Kegiatan Mahasiswa Olahraga"),
                new OA\Property(property: "alias", type: "string", example: "UKM Sport"),
                new OA\Property(property: "logo_url", type: "string", example: "http://localhost:8000/storage/logo_unit_kegiatan/ukm-sport-logo.png")
            ]
        ),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2025-06-25T14:42:19.000000Z"),
        new OA\Property(property: "event_date", type: "string", format: "date-time", example: "2025-07-14T00:00:00.000000Z"),
        new OA\Property(property: "event_type", type: "string", enum: ["online", "offline"], example: "online"),
        new OA\Property(property: "location", type: "string", example: "Zoom Meeting"),
        new OA\Property(property: "is_paid", type: "boolean", example: true),
        new OA\Property(property: "amount", type: "string", example: "25000.00"),
        new OA\Property(property: "link", type: "string", example: "https://payment.example.com/pay/80", description: "Payment link for paid events")
    ]
)]
#[OA\Schema(
    schema: "UkmSummary",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Himpunan Mahasiswa Informatika"),
        new OA\Property(property: "alias", type: "string", example: "HMIF"),
        new OA\Property(property: "category", type: "string", example: "Himpunan"),
        new OA\Property(property: "logo", type: "string", example: "logo_unit_kegiatan/hmif-logo.png"),
        new OA\Property(property: "profile_image_url", type: "string", example: "https://example.com/logo.jpg"),
        new OA\Property(property: "description", type: "string", example: "Student organization for computer science students"),
        new OA\Property(property: "background_photo_url", type: "string", example: "https://example.com/background.jpg")
    ]
)]
#[OA\Schema(
    schema: "UkmProfile",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Himpunan Mahasiswa Informatika"),
        new OA\Property(property: "alias", type: "string", example: "HMIF"),
        new OA\Property(property: "category", type: "string", example: "Himpunan"),
        new OA\Property(property: "logo", type: "string", example: "logo_unit_kegiatan/hmif-logo.png"),
        new OA\Property(property: "profile_image_url", type: "string", example: "https://example.com/logo.jpg"),
        new OA\Property(
            property: "unit_kegiatan_profile",
            type: "array",
            items: new OA\Items(
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "unit_kegiatan_id", type: "integer", example: 1),
                    new OA\Property(property: "description", type: "string", example: "Detailed description of the UKM"),
                    new OA\Property(property: "background_photo", type: "string", example: "unit_kegiatan_profiles/backgrounds/background.jpg")
                ]
            )
        )
    ]
)]
#[OA\Schema(
    schema: "UkmFullProfile",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Himpunan Mahasiswa Informatika"),
        new OA\Property(property: "alias", type: "string", example: "HMIF"),
        new OA\Property(property: "category", type: "string", example: "Himpunan"),
        new OA\Property(property: "profile_image_url", type: "string", example: "https://example.com/logo.jpg"),
        new OA\Property(property: "description", type: "string", example: "Comprehensive description of the UKM"),
        new OA\Property(property: "vision_mission", type: "string", example: "Vision: To be the leading organization. Mission: To develop student potential."),
        new OA\Property(property: "background_photo_url", type: "string", example: "https://example.com/background.jpg"),
        new OA\Property(
            property: "achievements",
            type: "array",
            items: new OA\Items(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Programming Competition Winner"),
                    new OA\Property(property: "description", type: "string", example: "Won first place in national programming competition"),
                    new OA\Property(property: "image_url", type: "string", example: "https://example.com/achievement.jpg")
                ]
            )
        ),
        new OA\Property(
            property: "recent_posts",
            type: "array",
            items: new OA\Items(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Recent Activity Post"),
                    new OA\Property(property: "type", type: "string", enum: ["post", "event"], example: "post"),
                    new OA\Property(property: "image_url", type: "string", example: "https://example.com/post.jpg")
                ]
            )
        ),
        new OA\Property(
            property: "activity_gallery",
            type: "array",
            items: new OA\Items(
                properties: [
                    new OA\Property(property: "image_url", type: "string", example: "https://example.com/gallery.jpg")
                ]
            )
        )
    ]
)]
#[OA\Schema(
    schema: "RegistrationData",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "user_id", type: "integer", example: 1),
        new OA\Property(property: "unit_kegiatan_id", type: "integer", example: 1),
        new OA\Property(property: "status", type: "string", enum: ["pending", "accepted", "rejected"], example: "pending"),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time")
    ]
)]
#[OA\Schema(
    schema: "Banner",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "feed_id", type: "integer", example: 1),
        new OA\Property(property: "image_url", type: "string", example: "https://example.com/banner.jpg"),
        new OA\Property(property: "ukm", type: "string", example: "HMIF")
    ]
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class SwaggerDefinitions {}
