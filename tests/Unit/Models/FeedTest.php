<?php

namespace Tests\Unit\Models;

use App\Models\Feed;
use App\Models\UnitKegiatan;
use App\Models\PaymentConfiguration;
use App\Models\PaymentTransaction;
use App\Models\AnonymousEventRegistration;
use App\Models\Banner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedTest extends TestCase
{
    use RefreshDatabase;

    public function test_feed_can_be_created()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'type' => 'post',
            'title' => 'Test Feed'
        ]);

        $this->assertInstanceOf(Feed::class, $feed);
        $this->assertEquals('post', $feed->type);
        $this->assertEquals('Test Feed', $feed->title);
    }

    public function test_feed_has_unit_kegiatan_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertInstanceOf(UnitKegiatan::class, $feed->unitKegiatan);
        $this->assertEquals($ukm->id, $feed->unitKegiatan->id);
    }

    public function test_feed_has_payment_configuration_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id
        ]);

        $this->assertInstanceOf(PaymentConfiguration::class, $feed->paymentConfiguration);
        $this->assertEquals($paymentConfig->id, $feed->paymentConfiguration->id);
    }

    // TODO: Fix Banner class autoloading issue
    // public function test_feed_has_banner_relationship()
    // {
    //     $feed = Feed::factory()->create();
    //     $banner = Banner::factory()->create(['feed_id' => $feed->id]);

    //     $this->assertInstanceOf(Banner::class, $feed->banner);
    //     $this->assertEquals($banner->id, $feed->banner->id);
    // }

    public function test_feed_has_transactions_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id
        ]);

        $this->assertCount(1, $feed->transactions);
        $this->assertEquals($transaction->id, $feed->transactions->first()->id);
    }

    public function test_feed_has_anonymous_registrations_relationship()
    {
        $feed = Feed::factory()->create();
        $registration = AnonymousEventRegistration::factory()->create(['feed_id' => $feed->id]);

        $this->assertCount(1, $feed->anonymousRegistrations);
        $this->assertEquals($registration->id, $feed->anonymousRegistrations->first()->id);
    }

    public function test_posts_scope()
    {
        $ukm = UnitKegiatan::factory()->create();
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'post']);
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'event']);

        $posts = Feed::posts()->get();

        $this->assertCount(1, $posts);
        $this->assertEquals('post', $posts->first()->type);
    }

    public function test_events_scope()
    {
        $ukm = UnitKegiatan::factory()->create();
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'post']);
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'event']);

        $events = Feed::events()->get();

        $this->assertCount(1, $events);
        $this->assertEquals('event', $events->first()->type);
    }

    public function test_paid_events_scope()
    {
        $ukm = UnitKegiatan::factory()->create();
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'event', 'is_paid' => false]);
        Feed::factory()->create(['unit_kegiatan_id' => $ukm->id, 'type' => 'event', 'is_paid' => true]);

        $paidEvents = Feed::paidEvents()->get();

        $this->assertCount(1, $paidEvents);
        $this->assertTrue($paidEvents->first()->is_paid);
    }

    public function test_is_event_helper_method()
    {
        $event = Feed::factory()->create(['type' => 'event']);
        $post = Feed::factory()->create(['type' => 'post']);

        $this->assertTrue($event->isEvent());
        $this->assertFalse($post->isEvent());
    }

    public function test_is_post_helper_method()
    {
        $event = Feed::factory()->create(['type' => 'event']);
        $post = Feed::factory()->create(['type' => 'post']);

        $this->assertFalse($event->isPost());
        $this->assertTrue($post->isPost());
    }

    public function test_is_paid_event_helper_method()
    {
        $paidEvent = Feed::factory()->create(['type' => 'event', 'is_paid' => true]);
        $freeEvent = Feed::factory()->create(['type' => 'event', 'is_paid' => false]);
        $post = Feed::factory()->create(['type' => 'post']);

        $this->assertTrue($paidEvent->isPaidEvent());
        $this->assertFalse($freeEvent->isPaidEvent());
        $this->assertFalse($post->isPaidEvent());
    }

    public function test_registration_token_is_generated_for_paid_events()
    {
        $feed = Feed::factory()->create([
            'type' => 'event',
            'is_paid' => true
        ]);

        $this->assertNotNull($feed->registration_token);
        $this->assertEquals(32, strlen($feed->registration_token));
    }

    public function test_registration_token_is_not_generated_for_free_events()
    {
        $feed = Feed::factory()->freeEvent()->create();

        $this->assertNull($feed->registration_token);
    }

    public function test_get_registration_url()
    {
        $paidEvent = Feed::factory()->create([
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123'
        ]);

        $freeEvent = Feed::factory()->create([
            'type' => 'event',
            'is_paid' => false
        ]);

        $this->assertStringContainsString('test-token-123', $paidEvent->getRegistrationUrl());
        $this->assertNull($freeEvent->getRegistrationUrl());
    }

    public function test_regenerate_registration_token()
    {
        $feed = Feed::factory()->create([
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'old-token'
        ]);

        $oldToken = $feed->registration_token;
        $newToken = $feed->regenerateRegistrationToken();

        $this->assertNotEquals($oldToken, $newToken);
        $this->assertEquals(32, strlen($newToken));
        $this->assertEquals($newToken, $feed->fresh()->registration_token);
    }

    public function test_get_paid_registrations_count()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'paid'
        ]);
        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'pending'
        ]);

        $this->assertEquals(1, $feed->getPaidRegistrationsCount());
    }

    public function test_get_pending_with_proof_count()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'pending',
            'proof_of_payment' => 'path/to/proof.jpg'
        ]);
        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'pending',
            'proof_of_payment' => null
        ]);

        $this->assertEquals(1, $feed->getPendingWithProofCount());
    }

    public function test_get_pending_without_proof_count()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'pending',
            'proof_of_payment' => 'path/to/proof.jpg'
        ]);
        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'pending',
            'proof_of_payment' => null
        ]);

        $this->assertEquals(1, $feed->getPendingWithoutProofCount());
    }

    public function test_event_date_is_cast_to_date()
    {
        $feed = Feed::factory()->create([
            'event_date' => '2024-12-25'
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $feed->event_date);
    }

    public function test_is_paid_is_cast_to_boolean()
    {
        $feed = Feed::factory()->create([
            'is_paid' => '1'
        ]);

        $this->assertIsBool($feed->is_paid);
        $this->assertTrue($feed->is_paid);
    }

    public function test_fillable_attributes()
    {
        $ukm = UnitKegiatan::factory()->create();
        $data = [
            'unit_kegiatan_id' => $ukm->id,
            'type' => 'event',
            'title' => 'Test Event',
            'content' => 'Event content',
            'image' => 'path/to/image.jpg',
            'event_date' => '2024-12-25',
            'event_type' => 'online',
            'location' => 'Zoom Meeting',
            'is_paid' => true,
            'max_participants' => 100
        ];

        $feed = Feed::create($data);

        $this->assertEquals('Test Event', $feed->title);
        $this->assertEquals('Event content', $feed->content);
        $this->assertEquals('online', $feed->event_type);
        $this->assertEquals('Zoom Meeting', $feed->location);
        $this->assertTrue($feed->is_paid);
        $this->assertEquals(100, $feed->max_participants);
    }
}
