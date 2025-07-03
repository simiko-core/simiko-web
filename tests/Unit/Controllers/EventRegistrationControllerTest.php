<?php

namespace Tests\Unit\Controllers;

use App\Models\Feed;
use App\Models\UnitKegiatan;
use App\Models\PaymentConfiguration;
use App\Models\PaymentTransaction;
use App\Models\AnonymousEventRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventRegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_show_returns_event_registration_page()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123',
            'event_date' => now()->addDays(7),
            'max_participants' => 100
        ]);

        $response = $this->get(route('event.register', 'test-token-123'));

        $response->assertStatus(200);
        $response->assertViewIs('event-registration.show');
        $response->assertViewHas('event');
    }

    public function test_show_returns_closed_page_for_past_event()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123',
            'event_date' => now()->subDays(1)
        ]);

        $response = $this->get(route('event.register', 'test-token-123'));

        $response->assertStatus(200);
        $response->assertViewIs('event-registration.closed');
        $response->assertViewHas('event');
    }

    public function test_show_returns_closed_page_for_full_event()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123',
            'event_date' => now()->addDays(7),
            'max_participants' => 2
        ]);

        // Fill the event to capacity
        PaymentTransaction::factory()->count(2)->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $event->id,
            'status' => 'paid'
        ]);

        $response = $this->get(route('event.register', 'test-token-123'));

        $response->assertStatus(200);
        $response->assertViewIs('event-registration.closed');
        $response->assertViewHas('event');
    }

    public function test_register_creates_anonymous_registration_and_transaction()
    {
        $ukm = UnitKegiatan::factory()->create(['alias' => 'HMIF']);
        $paymentConfig = PaymentConfiguration::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'amount' => 50000
        ]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123',
            'event_date' => now()->addDays(7)
        ]);

        $requestData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08123456789',
            'custom_data' => [
                'student-id' => '123456789',
                'phone-number' => '08123456789'
            ]
        ];

        $response = $this->post(route('event.register.submit', 'test-token-123'), $requestData);

        $response->assertStatus(302);
        $response->assertRedirect();

        // Check anonymous registration was created
        $this->assertDatabaseHas('anonymous_event_registrations', [
            'feed_id' => $event->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08123456789'
        ]);

        // Check payment transaction was created
        $this->assertDatabaseHas('payment_transactions', [
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $event->id,
            'amount' => 50000,
            'status' => 'pending'
        ]);
    }

    public function test_register_with_validation_errors()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123'
        ]);

        $requestData = [
            'name' => '',
            'email' => 'invalid-email',
            'phone' => '08123456789'
        ];

        $response = $this->post(route('event.register.submit', 'test-token-123'), $requestData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'email']);
    }

    public function test_register_redirects_if_user_already_registered()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123'
        ]);

        $registration = AnonymousEventRegistration::factory()->create([
            'feed_id' => $event->id,
            'email' => 'john@example.com'
        ]);

        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $event->id,
            'anonymous_registration_id' => $registration->id,
            'status' => 'pending'
        ]);

        $requestData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08123456789',
            'custom_data' => [
                'student-id' => '123456789',
                'phone-number' => '08123456789'
            ]
        ];

        $response = $this->post(route('event.register.submit', 'test-token-123'), $requestData);

        $response->assertStatus(302);
        $response->assertRedirect(route('event.payment', [
            'token' => 'test-token-123',
            'transactionId' => $transaction->transaction_id
        ]));
    }

    public function test_register_fails_when_event_is_full()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123',
            'max_participants' => 1
        ]);

        // Fill the event to capacity
        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $event->id,
            'status' => 'paid'
        ]);

        $requestData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '08123456789',
            'custom_data' => [
                'student-id' => '123456789',
                'phone-number' => '08123456789'
            ]
        ];

        $response = $this->post(route('event.register.submit', 'test-token-123'), $requestData);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_upload_proof_updates_transaction()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123'
        ]);

        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $event->id,
            'transaction_id' => 'TXN-123',
            'status' => 'pending'
        ]);

        $proofFile = UploadedFile::fake()->image('proof.jpg');

        $response = $this->post(route('event.upload-proof', ['test-token-123', 'TXN-123']), [
            'proof_file' => $proofFile
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('event.status', ['test-token-123', 'TXN-123']));

        $transaction->refresh();
        $this->assertNotNull($transaction->proof_of_payment);
        $this->assertTrue(Storage::disk('public')->exists($transaction->proof_of_payment));
    }

    public function test_upload_proof_with_invalid_file()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123'
        ]);

        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $event->id,
            'transaction_id' => 'TXN-123',
            'status' => 'pending'
        ]);

        $invalidFile = UploadedFile::fake()->create('document.txt', 100);

        $response = $this->post(route('event.upload-proof', ['test-token-123', 'TXN-123']), [
            'proof_file' => $invalidFile
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['proof_file']);
    }

    public function test_upload_proof_fails_for_non_pending_transaction()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123'
        ]);

        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $event->id,
            'transaction_id' => 'TXN-123',
            'status' => 'paid'
        ]);

        $proofFile = UploadedFile::fake()->image('proof.jpg');

        $response = $this->post(route('event.upload-proof', ['test-token-123', 'TXN-123']), [
            'proof_file' => $proofFile
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_status_shows_transaction_status()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123'
        ]);

        $registration = AnonymousEventRegistration::factory()->create([
            'feed_id' => $event->id
        ]);

        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $event->id,
            'anonymous_registration_id' => $registration->id,
            'transaction_id' => 'TXN-123',
            'status' => 'pending'
        ]);

        $response = $this->get(route('event.status', ['test-token-123', 'TXN-123']));

        $response->assertStatus(200);
        $response->assertViewIs('event-registration.status');
        $response->assertViewHas(['event', 'transaction']);
    }

    public function test_download_receipt_for_paid_transaction()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $event = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id,
            'type' => 'event',
            'is_paid' => true,
            'registration_token' => 'test-token-123'
        ]);

        $registration = AnonymousEventRegistration::factory()->create([
            'feed_id' => $event->id
        ]);

        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $event->id,
            'anonymous_registration_id' => $registration->id,
            'transaction_id' => 'TXN-123',
            'status' => 'paid'
        ]);

        $response = $this->get(route('event.download-receipt', ['test-token-123', 'TXN-123']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
