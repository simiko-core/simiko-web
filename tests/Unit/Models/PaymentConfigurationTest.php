<?php

namespace Tests\Unit\Models;

use App\Models\PaymentConfiguration;
use App\Models\UnitKegiatan;
use App\Models\Feed;
use App\Models\PaymentTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PaymentConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_configuration_can_be_created()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'name' => 'Test Payment Config',
            'amount' => 50000
        ]);

        $this->assertInstanceOf(PaymentConfiguration::class, $config);
        $this->assertEquals('Test Payment Config', $config->name);
        $this->assertEquals(50000, $config->amount);
    }

    public function test_payment_configuration_has_unit_kegiatan_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertInstanceOf(UnitKegiatan::class, $config->unitKegiatan);
        $this->assertEquals($ukm->id, $config->unitKegiatan->id);
    }

    public function test_payment_configuration_has_transactions_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $transaction = PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id
        ]);

        $this->assertCount(1, $config->transactions);
        $this->assertEquals($transaction->id, $config->transactions->first()->id);
    }

    public function test_payment_configuration_has_feeds_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id
        ]);

        $this->assertCount(1, $config->feeds);
        $this->assertEquals($feed->id, $config->feeds->first()->id);
    }

    public function test_is_active_attribute_with_no_feed()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertTrue($config->is_active);
    }

    public function test_is_active_attribute_with_unlimited_participants()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id,
            'max_participants' => null
        ]);

        $this->assertTrue($config->is_active);
    }

    public function test_is_active_attribute_with_available_slots()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id,
            'max_participants' => 10
        ]);

        // Create fewer transactions than max participants
        PaymentTransaction::factory()->count(5)->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'paid'
        ]);

        $this->assertTrue($config->is_active);
    }

    public function test_is_active_attribute_when_full()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);
        $feed = Feed::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id,
            'max_participants' => 5
        ]);

        // Create transactions equal to max participants
        PaymentTransaction::factory()->count(5)->create([
            'unit_kegiatan_id' => $ukm->id,
            'feed_id' => $feed->id,
            'status' => 'paid'
        ]);

        $this->assertFalse($config->is_active);
    }

    public function test_formatted_amount_attribute()
    {
        $config = PaymentConfiguration::factory()->create(['amount' => 50000.00]);

        $this->assertEquals('Rp 50.000', $config->formatted_amount);
    }

    public function test_total_transactions_attribute()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        PaymentTransaction::factory()->count(3)->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id
        ]);

        $this->assertEquals(3, $config->total_transactions);
    }

    public function test_total_revenue_attribute()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id,
            'amount' => 50000,
            'status' => 'paid'
        ]);
        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id,
            'amount' => 30000,
            'status' => 'paid'
        ]);
        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id,
            'amount' => 20000,
            'status' => 'pending'
        ]);

        $this->assertEquals(80000, $config->total_revenue);
    }

    public function test_pending_transactions_attribute()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        PaymentTransaction::factory()->count(2)->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id,
            'status' => 'pending'
        ]);
        PaymentTransaction::factory()->create([
            'unit_kegiatan_id' => $ukm->id,
            'payment_configuration_id' => $config->id,
            'status' => 'paid'
        ]);

        $this->assertEquals(2, $config->pending_transactions);
    }

    public function test_get_file_fields()
    {
        $config = PaymentConfiguration::factory()->create([
            'custom_fields' => [
                [
                    'name' => 'student_id',
                    'type' => 'text',
                    'label' => 'Student ID'
                ],
                [
                    'name' => 'photo',
                    'type' => 'file',
                    'label' => 'Photo'
                ],
                [
                    'name' => 'document',
                    'type' => 'file',
                    'label' => 'Document'
                ]
            ]
        ]);

        $fileFields = $config->getFileFields();

        $this->assertCount(2, $fileFields);
        $this->assertArrayHasKey('photo', $fileFields);
        $this->assertArrayHasKey('document', $fileFields);
        $this->assertEquals('Photo', $fileFields['photo']['label']);
    }

    public function test_validate_file_upload_with_invalid_field()
    {
        $config = PaymentConfiguration::factory()->create(['custom_fields' => []]);
        $file = UploadedFile::fake()->image('test.jpg');

        $errors = $config->validateFileUpload('invalid_field', $file);

        $this->assertEquals(['error' => 'Invalid file field'], $errors);
    }

    public function test_validate_file_upload_with_invalid_extension()
    {
        $config = PaymentConfiguration::factory()->create([
            'custom_fields' => [
                [
                    'name' => 'photo',
                    'type' => 'file',
                    'label' => 'Photo'
                ]
            ]
        ]);
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $errors = $config->validateFileUpload('photo', $file);

        $this->assertContains('File type not allowed. Only images (jpg, jpeg, png, gif) are accepted.', $errors);
    }

    public function test_validate_file_upload_with_valid_image()
    {
        $config = PaymentConfiguration::factory()->create([
            'custom_fields' => [
                [
                    'name' => 'photo',
                    'type' => 'file',
                    'label' => 'Photo'
                ]
            ]
        ]);
        $file = UploadedFile::fake()->image('test.jpg');

        $errors = $config->validateFileUpload('photo', $file);

        $this->assertEmpty($errors);
    }

    public function test_default_currency()
    {
        $ukm = UnitKegiatan::factory()->create();
        $config = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertEquals('IDR', $config->currency);
    }

    public function test_casts()
    {
        $config = PaymentConfiguration::factory()->create([
            'amount' => '50000.00',
            'payment_methods' => [['method' => 'Bank Transfer']],
            'custom_fields' => [['name' => 'test']],
            'settings' => ['key' => 'value']
        ]);

        $this->assertEquals(50000.00, $config->amount);
        $this->assertIsArray($config->payment_methods);
        $this->assertIsArray($config->custom_fields);
        $this->assertIsArray($config->settings);
    }

    public function test_fillable_attributes()
    {
        $ukm = UnitKegiatan::factory()->create();
        $data = [
            'unit_kegiatan_id' => $ukm->id,
            'name' => 'Event Registration',
            'description' => 'Payment for event registration',
            'amount' => 75000,
            'currency' => 'IDR',
            'payment_methods' => [['method' => 'Bank Transfer']],
            'custom_fields' => [['name' => 'student_id', 'type' => 'text']],
            'settings' => ['auto_confirm' => true]
        ];

        $config = PaymentConfiguration::create($data);

        $this->assertEquals('Event Registration', $config->name);
        $this->assertEquals('Payment for event registration', $config->description);
        $this->assertEquals(75000, $config->amount);
        $this->assertEquals('IDR', $config->currency);
        $this->assertEquals([['method' => 'Bank Transfer']], $config->payment_methods);
        $this->assertEquals([['name' => 'student_id', 'type' => 'text']], $config->custom_fields);
        $this->assertEquals(['auto_confirm' => true], $config->settings);
    }
}
