<?php

namespace Tests\Unit\Models;

use App\Models\PaymentTransaction;
use App\Models\UnitKegiatan;
use App\Models\PaymentConfiguration;
use App\Models\Feed;
use App\Models\AnonymousEventRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_transaction_can_be_created()
    {
        $ukm = UnitKegiatan::factory()->create();
        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'transaction_id' => 'TXN-TEST-123',
            'amount' => 50000
        ]);

        $this->assertInstanceOf(PaymentTransaction::class, $transaction);
        $this->assertEquals('TXN-TEST-123', $transaction->transaction_id);
        $this->assertEquals(50000, $transaction->amount);
    }

    public function test_payment_transaction_has_unit_kegiatan_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $transaction = PaymentTransaction::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertInstanceOf(UnitKegiatan::class, $transaction->unitKegiatan);
        $this->assertEquals($ukm->id, $transaction->unitKegiatan->id);
    }

    public function test_payment_transaction_has_anonymous_registration_relationship()
    {
        $registration = AnonymousEventRegistration::factory()->create();
        $transaction = PaymentTransaction::factory()->create([
            'anonymous_registration_id' => $registration->id
        ]);

        $this->assertInstanceOf(AnonymousEventRegistration::class, $transaction->anonymousRegistration);
        $this->assertEquals($registration->id, $transaction->anonymousRegistration->id);
    }

    public function test_payment_transaction_has_payment_configuration_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $paymentConfig->id
        ]);

        $this->assertInstanceOf(PaymentConfiguration::class, $transaction->paymentConfiguration);
        $this->assertEquals($paymentConfig->id, $transaction->paymentConfiguration->id);
    }

    public function test_payment_transaction_has_feed_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id
        ]);

        $this->assertInstanceOf(Feed::class, $transaction->feed);
        $this->assertEquals($feed->id, $transaction->feed->id);
    }

    public function test_pending_scope()
    {
        $ukm = UnitKegiatan::factory()->create();
        PaymentTransaction::factory()->create(['unit_kegiatan_id' => $ukm->id, 'status' => 'pending']);
        PaymentTransaction::factory()->create(['unit_kegiatan_id' => $ukm->id, 'status' => 'paid']);

        $pendingTransactions = PaymentTransaction::pending()->get();

        $this->assertCount(1, $pendingTransactions);
        $this->assertEquals('pending', $pendingTransactions->first()->status);
    }

    public function test_paid_scope()
    {
        $ukm = UnitKegiatan::factory()->create();
        PaymentTransaction::factory()->create(['unit_kegiatan_id' => $ukm->id, 'status' => 'pending']);
        PaymentTransaction::factory()->create(['unit_kegiatan_id' => $ukm->id, 'status' => 'paid']);

        $paidTransactions = PaymentTransaction::paid()->get();

        $this->assertCount(1, $paidTransactions);
        $this->assertEquals('paid', $paidTransactions->first()->status);
    }

    public function test_failed_scope()
    {
        $ukm = UnitKegiatan::factory()->create();
        PaymentTransaction::factory()->create(['unit_kegiatan_id' => $ukm->id, 'status' => 'failed']);
        PaymentTransaction::factory()->create(['unit_kegiatan_id' => $ukm->id, 'status' => 'paid']);

        $failedTransactions = PaymentTransaction::failed()->get();

        $this->assertCount(1, $failedTransactions);
        $this->assertEquals('failed', $failedTransactions->first()->status);
    }

    public function test_recent_scope()
    {
        $ukm = UnitKegiatan::factory()->create();
        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'created_at' => now()->subDays(5)
        ]);
        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'created_at' => now()->subDays(35)
        ]);

        $recentTransactions = PaymentTransaction::recent(30)->get();

        $this->assertCount(1, $recentTransactions);
    }

    public function test_formatted_amount_attribute()
    {
        $transaction = PaymentTransaction::factory()->create(['amount' => 50000.00]);

        $this->assertEquals('Rp 50.000', $transaction->formatted_amount);
    }

    public function test_status_color_attribute()
    {
        $pendingTransaction = PaymentTransaction::factory()->create(['status' => 'pending']);
        $paidTransaction = PaymentTransaction::factory()->create(['status' => 'paid']);
        $failedTransaction = PaymentTransaction::factory()->create(['status' => 'failed']);

        $this->assertEquals('warning', $pendingTransaction->status_color);
        $this->assertEquals('success', $paidTransaction->status_color);
        $this->assertEquals('danger', $failedTransaction->status_color);
    }

    public function test_status_label_attribute()
    {
        $pendingTransaction = PaymentTransaction::factory()->create(['status' => 'pending']);
        $paidTransaction = PaymentTransaction::factory()->create(['status' => 'paid']);
        $failedTransaction = PaymentTransaction::factory()->create(['status' => 'failed']);

        $this->assertEquals('Pending', $pendingTransaction->status_label);
        $this->assertEquals('Paid', $paidTransaction->status_label);
        $this->assertEquals('Failed', $failedTransaction->status_label);
    }

    public function test_is_expired()
    {
        $expiredTransaction = PaymentTransaction::factory()->create([
            'expires_at' => now()->subDay()
        ]);
        $validTransaction = PaymentTransaction::factory()->create([
            'expires_at' => now()->addDay()
        ]);

        $this->assertTrue($expiredTransaction->isExpired());
        $this->assertFalse($validTransaction->isExpired());
    }

    public function test_can_be_paid()
    {
        $pendingTransaction = PaymentTransaction::factory()->create([
            'status' => 'pending',
            'expires_at' => now()->addDay()
        ]);
        $paidTransaction = PaymentTransaction::factory()->create(['status' => 'paid']);
        $expiredTransaction = PaymentTransaction::factory()->create([
            'status' => 'pending',
            'expires_at' => now()->subDay()
        ]);

        $this->assertTrue($pendingTransaction->canBePaid());
        $this->assertFalse($paidTransaction->canBePaid());
        $this->assertFalse($expiredTransaction->canBePaid());
    }

    public function test_mark_as_paid()
    {
        $transaction = PaymentTransaction::factory()->create(['status' => 'pending']);

        $transaction->markAsPaid('Bank Transfer', ['account_number' => '123456']);

        $this->assertEquals('paid', $transaction->fresh()->status);
        $this->assertEquals('Bank Transfer', $transaction->fresh()->payment_method);
        $this->assertEquals(['account_number' => '123456'], $transaction->fresh()->payment_details);
        $this->assertNotNull($transaction->fresh()->paid_at);
    }

    public function test_mark_as_failed()
    {
        $transaction = PaymentTransaction::factory()->create(['status' => 'pending']);

        $transaction->markAsFailed('Payment method invalid');

        $this->assertEquals('failed', $transaction->fresh()->status);
        $this->assertEquals('Payment method invalid', $transaction->fresh()->notes);
    }

    public function test_mark_as_cancelled()
    {
        $transaction = PaymentTransaction::factory()->create(['status' => 'pending']);

        $transaction->markAsCancelled('User requested cancellation');

        $this->assertEquals('cancelled', $transaction->fresh()->status);
        $this->assertEquals('User requested cancellation', $transaction->fresh()->notes);
    }

    public function test_mark_as_expired()
    {
        $transaction = PaymentTransaction::factory()->create(['status' => 'pending']);

        $transaction->markAsExpired();

        $this->assertEquals('expired', $transaction->fresh()->status);
    }

    public function test_add_custom_file()
    {
        $transaction = PaymentTransaction::factory()->create();

        $transaction->addCustomFile('path/to/file.jpg', 'student_id');

        $customFiles = $transaction->fresh()->custom_files;
        $this->assertEquals('path/to/file.jpg', $customFiles['student_id']);
    }

    public function test_remove_custom_file()
    {
        $transaction = PaymentTransaction::factory()->create([
            'custom_files' => ['student_id' => 'path/to/file.jpg', 'other' => 'path/to/other.jpg']
        ]);

        $transaction->removeCustomFile('path/to/file.jpg');

        $customFiles = $transaction->fresh()->custom_files;
        $this->assertNotContains('path/to/file.jpg', $customFiles);
        $this->assertContains('path/to/other.jpg', $customFiles);
    }

    public function test_get_user_name_from_anonymous_registration()
    {
        $registration = AnonymousEventRegistration::factory()->create(['name' => 'John Doe']);
        $transaction = PaymentTransaction::factory()->create([
            'anonymous_registration_id' => $registration->id
        ]);

        $this->assertEquals('John Doe', $transaction->getUserName());
    }

    public function test_get_user_email_from_anonymous_registration()
    {
        $registration = AnonymousEventRegistration::factory()->create(['email' => 'john@example.com']);
        $transaction = PaymentTransaction::factory()->create([
            'anonymous_registration_id' => $registration->id
        ]);

        $this->assertEquals('john@example.com', $transaction->getUserEmail());
    }

    public function test_default_values()
    {
        $ukm = UnitKegiatan::factory()->create();
        $transaction = PaymentTransaction::factory()->pending()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertEquals('IDR', $transaction->currency);
        $this->assertEquals('pending', $transaction->status);
    }

    public function test_casts()
    {
        $transaction = PaymentTransaction::factory()->create([
            'amount' => '50000.00',
            'payment_details' => ['key' => 'value'],
            'custom_data' => ['field' => 'data'],
            'custom_files' => ['file1.jpg'],
            'paid_at' => '2024-01-01 12:00:00',
            'expires_at' => '2024-12-31 23:59:59'
        ]);

        $this->assertEquals(50000.00, $transaction->amount);
        $this->assertIsArray($transaction->payment_details);
        $this->assertIsArray($transaction->custom_data);
        $this->assertIsArray($transaction->custom_files);
        $this->assertInstanceOf(\Carbon\Carbon::class, $transaction->paid_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $transaction->expires_at);
    }
}
