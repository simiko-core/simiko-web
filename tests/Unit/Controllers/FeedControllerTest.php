<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\api\feedController;
use App\Models\Feed;
use App\Models\UnitKegiatan;
use App\Models\PaymentConfiguration;
use App\Models\PaymentTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class FeedControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new feedController();
    }

    public function test_index_returns_all_feeds()
    {
        $ukm = UnitKegiatan::factory()->create(['alias' => 'HMIF']);
        Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'type' => 'post',
            'title' => 'Test Post'
        ]);
        Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'type' => 'event',
            'title' => 'Test Event'
        ]);

        $request = Request::create('/api/feed', 'GET');
        $response = $this->controller->index($request);
        $responseData = $response->getData(true);

        // Debug the response structure
        dump('Response data:', $responseData);
        dump('Response keys:', array_keys($responseData));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($responseData['status']);
        $this->assertEquals('Feed retrieved successfully', $responseData['message']);
        $this->assertArrayHasKey('feeds', $responseData['data']);
        $this->assertArrayHasKey('ukms', $responseData['data']);
        $this->assertCount(1, $responseData['data']['feeds']['post']);
        $this->assertCount(1, $responseData['data']['feeds']['event']);
    }

    public function test_index_with_type_filter()
    {
        $ukm = UnitKegiatan::factory()->create();
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'post']);
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'event']);

        $request = Request::create('/api/feed?type=post', 'GET');
        $response = $this->controller->index($request);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $responseData['data']['feeds']['post']);
        $this->assertCount(0, $responseData['data']['feeds']['event']);
    }

    public function test_index_with_ukm_filter()
    {
        $ukm1 = UnitKegiatan::factory()->create();
        $ukm2 = UnitKegiatan::factory()->create();
        Feed::factory()->create(['unit_kegiatan_id' => $ukm1->id, 'type' => 'post']);
        Feed::factory()->create(['unit_kegiatan_id' => $ukm2->id, 'type' => 'post']);

        $request = Request::create("/api/feed?ukm_id={$ukm1->id}", 'GET');
        $response = $this->controller->index($request);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $responseData['data']['feeds']['post']);
        $this->assertEquals($ukm1->id, $responseData['data']['feeds']['post'][0]['ukm']['id']);
    }

    public function test_index_includes_paid_event_information()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'max_participants' => 10
        ]);

        // Create some transactions
        PaymentTransaction::factory()->count(3)->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'paid'
        ]);

        $request = Request::create('/api/feed', 'GET');
        $response = $this->controller->index($request);
        $responseData = $response->getData(true);

        $eventData = $responseData['data']['feeds']['event'][0];
        $this->assertTrue($eventData['is_paid']);
        $this->assertEquals(10, $eventData['max_participants']);
        $this->assertEquals(3, $eventData['current_registrations']);
        $this->assertEquals(7, $eventData['available_slots']);
        $this->assertFalse($eventData['is_full']);
    }

    public function test_show_returns_feed_details()
    {
        $ukm = UnitKegiatan::factory()->create(['alias' => 'HMIF']);
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'type' => 'post',
            'title' => 'Test Post',
            'content' => 'Test content'
        ]);

        $response = $this->controller->show($feed->id);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($responseData['status']);
        $this->assertEquals('Feed item retrieved successfully', $responseData['message']);
        $this->assertEquals($feed->id, $responseData['data']['id']);
        $this->assertEquals('Test Post', $responseData['data']['title']);
        $this->assertEquals('Test content', $responseData['data']['content']);
        $this->assertEquals('HMIF', $responseData['data']['ukm']['alias']);
    }

    public function test_show_with_paid_event_includes_payment_info()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'amount' => 50000
        ]);
        $feed = Feed::factory()->paidEvent()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'registration_token' => 'test-token',
            'max_participants' => 100
        ]);

        // Create some transactions
        PaymentTransaction::factory()->count(25)->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'paid'
        ]);

        $response = $this->controller->show($feed->id);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($responseData['data']['is_paid']);
        $this->assertEquals(50000, $responseData['data']['amount']);
        $this->assertEquals(100, $responseData['data']['max_participants']);
        $this->assertEquals(25, $responseData['data']['current_registrations']);
        $this->assertEquals(75, $responseData['data']['available_slots']);
        $this->assertFalse($responseData['data']['is_full']);
    }

    public function test_show_with_event_details()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'type' => 'event',
            'event_date' => '2024-12-25',
            'event_type' => 'online',
            'location' => 'Zoom Meeting'
        ]);

        $response = $this->controller->show($feed->id);
        $responseData = $response->getData(true);

        $this->assertEquals('2024-12-25T00:00:00.000000Z', $responseData['data']['event_date']);
        $this->assertEquals('online', $responseData['data']['event_type']);
        $this->assertEquals('Zoom Meeting', $responseData['data']['location']);
    }

    public function test_show_with_invalid_id_returns_404()
    {
        $response = $this->controller->show(999);
        $responseData = $response->getData(true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertFalse($responseData['status']);
        $this->assertEquals('Feed item not found', $responseData['message']);
    }

    public function test_posts_endpoint()
    {
        $ukm = UnitKegiatan::factory()->create();
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'post']);
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'event']);

        $request = Request::create('/api/posts', 'GET');
        $response = $this->controller->posts($request);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(1, $responseData['data']['feeds']['post']);
        $this->assertCount(0, $responseData['data']['feeds']['event']);
    }

    public function test_events_endpoint()
    {
        $ukm = UnitKegiatan::factory()->create();
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'post']);
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'event']);

        $request = Request::create('/api/events', 'GET');
        $response = $this->controller->events($request);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertCount(0, $responseData['data']['feeds']['post']);
        $this->assertCount(1, $responseData['data']['feeds']['event']);
    }

    public function test_index_limits_results()
    {
        $ukm = UnitKegiatan::factory()->create();
        Feed::factory()->count(60)->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'post']);

        $request = Request::create('/api/feed', 'GET');
        $response = $this->controller->index($request);
        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertLessThanOrEqual(50, count($responseData['data']['feeds']['post']));
    }

    public function test_feed_with_image_includes_url()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'image' => 'feeds/test-image.jpg'
        ]);

        $response = $this->controller->show($feed->id);
        $responseData = $response->getData(true);

        $this->assertStringContainsString('feeds/test-image.jpg', $responseData['data']['image_url']);
    }

    public function test_feed_without_image_returns_null()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'image' => null
        ]);

        $response = $this->controller->show($feed->id);
        $responseData = $response->getData(true);

        $this->assertNull($responseData['data']['image_url']);
    }

    public function test_event_at_capacity_shows_full_status()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'type' => 'event',
            'is_paid' => true,
            'max_participants' => 5
        ]);

        // Fill the event to capacity
        PaymentTransaction::factory()->count(5)->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'paid'
        ]);

        $response = $this->controller->show($feed->id);
        $responseData = $response->getData(true);

        $this->assertTrue($responseData['data']['is_full']);
        $this->assertEquals(0, $responseData['data']['available_slots']);
        $this->assertEquals(5, $responseData['data']['current_registrations']);
    }
}
