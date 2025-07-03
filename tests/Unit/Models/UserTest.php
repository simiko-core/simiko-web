<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Admin;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin_ukm']);
    }

    public function test_user_can_be_created()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_user_has_admin_relationship()
    {
        $user = User::factory()->create();
        $admin = Admin::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Admin::class, $user->admin);
        $this->assertEquals($admin->id, $user->admin->id);
    }

    public function test_user_can_access_admin_panel_as_super_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $panel = \Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('admin');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_user_can_access_ukm_panel_as_admin_ukm()
    {
        $user = User::factory()->create();
        $user->assignRole('admin_ukm');

        $panel = \Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('ukmPanel');

        $this->assertTrue($user->canAccessPanel($panel));
    }

    public function test_user_cannot_access_admin_panel_without_super_admin_role()
    {
        $user = User::factory()->create();
        $user->assignRole('admin_ukm');

        $panel = \Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('admin');

        $this->assertFalse($user->canAccessPanel($panel));
    }

    public function test_user_cannot_access_ukm_panel_without_admin_ukm_role()
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $panel = \Mockery::mock(Panel::class);
        $panel->shouldReceive('getId')->andReturn('ukmPanel');

        $this->assertFalse($user->canAccessPanel($panel));
    }

    public function test_user_attributes_are_fillable()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'phone' => '08123456789',
            'photo' => 'path/to/photo.jpg'
        ];

        $user = User::create($userData);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('08123456789', $user->phone);
        $this->assertEquals('path/to/photo.jpg', $user->photo);
    }

    public function test_password_is_hidden_in_array()
    {
        $user = User::factory()->create();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    public function test_email_verified_at_is_cast_to_datetime()
    {
        $user = User::factory()->create([
            'email_verified_at' => '2024-01-01 12:00:00'
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $user->email_verified_at);
    }
}
