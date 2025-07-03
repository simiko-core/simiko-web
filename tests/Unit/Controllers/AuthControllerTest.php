<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\api\authController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new authController();
        Storage::fake('public');
    }

    public function test_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'testing@example.com',
            'password' => Hash::make('password123')
        ]);

        $request = Request::create('/api/login', 'POST', [
            'email' => 'testing@example.com',
            'password' => 'password123'
        ]);

        $response = $this->controller->login($request);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($responseData['status']);
        $this->assertEquals('Login successful', $responseData['message']);
        $this->assertArrayHasKey('access_token', $responseData['data']);
        $this->assertArrayHasKey('user', $responseData['data']);
        $this->assertEquals('Bearer', $responseData['data']['token_type']);
    }

    public function test_login_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'invalid@example.com',
            'password' => Hash::make('password123')
        ]);

        $request = Request::create('/api/login', 'POST', [
            'email' => 'invalid@example.com',
            'password' => 'wrongpassword'
        ]);

        $response = $this->controller->login($request);
        $responseData = $response->getData(true);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Invalid credentials', $responseData['message']);
    }

    public function test_login_validation_errors()
    {
        $request = Request::create('/api/login', 'POST', [
            'email' => 'invalid-email',
            'password' => ''
        ]);

        $response = $this->controller->login($request);
        $responseData = $response->getData(true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Validation failed', $responseData['message']);
        $this->assertArrayHasKey('errors', $responseData);
    }

    public function test_register_with_valid_data()
    {
        $request = Request::create('/api/register', 'POST', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'phone' => '08123456789'
        ]);

        $response = $this->controller->register($request);
        $responseData = $response->getData(true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($responseData['status']);
        $this->assertEquals('Registration successful', $responseData['message']);
        $this->assertArrayHasKey('access_token', $responseData['data']);
        $this->assertArrayHasKey('user', $responseData['data']);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08123456789'
        ]);
    }

    public function test_register_with_photo()
    {
        $photo = UploadedFile::fake()->image('profile.jpg');

        $request = Request::create('/api/register', 'POST', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'phone' => '08123456789'
        ]);
        $request->files->set('img_photo', $photo);

        $response = $this->controller->register($request);
        $responseData = $response->getData(true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue($responseData['status']);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user->photo);
        $this->assertTrue(Storage::disk('public')->exists($user->photo));
    }

    public function test_register_validation_errors()
    {
        $request = Request::create('/api/register', 'POST', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123'
        ]);

        $response = $this->controller->register($request);
        $responseData = $response->getData(true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Validation failed', $responseData['message']);
        $this->assertArrayHasKey('errors', $responseData);
    }

    public function test_register_with_duplicate_email()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $request = Request::create('/api/register', 'POST', [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123'
        ]);

        $response = $this->controller->register($request);
        $responseData = $response->getData(true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertFalse($responseData['status']);
        $this->assertArrayHasKey('errors', $responseData);
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');

        $request = Request::create('/api/logout', 'POST');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->controller->logout($request);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($responseData['status']);
        $this->assertEquals('Successfully logged out', $responseData['message']);

        // Verify tokens are deleted
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_profile()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08123456789',
            'photo' => 'profile_photos/test.jpg'
        ]);

        $request = Request::create('/api/user/profile', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->controller->profile($request);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($responseData['status']);
        $this->assertEquals('Profile retrieved successfully', $responseData['message']);

        $this->assertEquals($user->id, $responseData['data']['id']);
        $this->assertEquals('John Doe', $responseData['data']['name']);
        $this->assertEquals('john@example.com', $responseData['data']['email']);
        $this->assertEquals('08123456789', $responseData['data']['phone']);
        $this->assertStringContainsString('profile_photos/test.jpg', $responseData['data']['photo_url']);
    }

    public function test_profile_without_photo()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'photo' => null
        ]);

        $request = Request::create('/api/user/profile', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->controller->profile($request);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNull($responseData['data']['photo_url']);
    }

    public function test_register_with_invalid_photo_size()
    {
        $photo = UploadedFile::fake()->image('large.jpg')->size(3000); // 3MB

        $request = Request::create('/api/register', 'POST', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ]);
        $request->files->set('img_photo', $photo);

        $response = $this->controller->register($request);
        $responseData = $response->getData(true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertFalse($responseData['status']);
        $this->assertArrayHasKey('errors', $responseData);
    }

    public function test_register_with_invalid_photo_type()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $request = Request::create('/api/register', 'POST', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123'
        ]);
        $request->files->set('img_photo', $file);

        $response = $this->controller->register($request);
        $responseData = $response->getData(true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertFalse($responseData['status']);
        $this->assertArrayHasKey('errors', $responseData);
    }
}
