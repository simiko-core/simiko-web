<?php

namespace Tests\Unit\Models;

use App\Models\UnitKegiatan;
use App\Models\Admin;
use App\Models\Feed;
use App\Models\PaymentConfiguration;
use App\Models\PaymentTransaction;
use App\Models\Achievement;
use App\Models\ActivityGallery;
use App\Models\UnitKegiatanProfile;
use App\Models\PendaftaranAnggota;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UnitKegiatanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles needed for admin observer
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin_ukm']);
    }

    public function test_unit_kegiatan_can_be_created()
    {
        $ukm = UnitKegiatan::factory()->create([
            'name' => 'Test UKM',
            'alias' => 'TEST',
            'category' => 'Himpunan'
        ]);

        $this->assertInstanceOf(UnitKegiatan::class, $ukm);
        $this->assertEquals('Test UKM', $ukm->name);
        $this->assertEquals('TEST', $ukm->alias);
        $this->assertEquals('Himpunan', $ukm->category);
    }

    public function test_unit_kegiatan_has_admin_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $admin = Admin::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertInstanceOf(Admin::class, $ukm->admins);
        $this->assertEquals($admin->id, $ukm->admins->id);
    }

    public function test_unit_kegiatan_has_pendaftaran_anggota_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $pendaftaran = PendaftaranAnggota::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertCount(1, $ukm->pendaftaranAnggota);
        $this->assertEquals($pendaftaran->id, $ukm->pendaftaranAnggota->first()->id);
    }

    public function test_unit_kegiatan_has_profile_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $profile = UnitKegiatanProfile::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertCount(1, $ukm->unitKegiatanProfile);
        $this->assertEquals($profile->id, $ukm->unitKegiatanProfile->first()->id);
    }

    public function test_unit_kegiatan_has_achievements_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $achievement = Achievement::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertCount(1, $ukm->achievements);
        $this->assertEquals($achievement->id, $ukm->achievements->first()->id);
    }

    public function test_unit_kegiatan_has_activity_galleries_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $gallery = ActivityGallery::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertCount(1, $ukm->activityGalleries);
        $this->assertEquals($gallery->id, $ukm->activityGalleries->first()->id);
    }

    public function test_unit_kegiatan_has_feeds_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $feed = Feed::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertCount(1, $ukm->feeds);
        $this->assertEquals($feed->id, $ukm->feeds->first()->id);
    }

    public function test_unit_kegiatan_has_payment_configurations_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $paymentConfig = PaymentConfiguration::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertCount(1, $ukm->paymentConfigurations);
        $this->assertEquals($paymentConfig->id, $ukm->paymentConfigurations->first()->id);
    }

    public function test_unit_kegiatan_has_payment_transactions_relationship()
    {
        $ukm = UnitKegiatan::factory()->create();
        $transaction = PaymentTransaction::factory()->create(['unit_kegiatan_id' => $ukm->id]);

        $this->assertCount(1, $ukm->paymentTransactions);
        $this->assertEquals($transaction->id, $ukm->paymentTransactions->first()->id);
    }

    public function test_unit_kegiatan_fillable_attributes()
    {
        $data = [
            'name' => 'Himpunan Mahasiswa Informatika',
            'alias' => 'HMIF',
            'category' => 'Himpunan',
            'logo' => ['path/to/logo.png'],
            'open_registration' => true
        ];

        $ukm = UnitKegiatan::create($data);

        $this->assertEquals('Himpunan Mahasiswa Informatika', $ukm->name);
        $this->assertEquals('HMIF', $ukm->alias);
        $this->assertEquals('Himpunan', $ukm->category);
        $this->assertEquals(['path/to/logo.png'], $ukm->logo);
        $this->assertTrue($ukm->open_registration);
    }

    public function test_open_registration_is_cast_to_boolean()
    {
        $ukm = UnitKegiatan::factory()->create([
            'open_registration' => '1'
        ]);

        $this->assertIsBool($ukm->open_registration);
        $this->assertTrue($ukm->open_registration);
    }

    public function test_logo_is_cast_to_array()
    {
        $ukm = UnitKegiatan::factory()->create([
            'logo' => ['path/to/logo.png']
        ]);

        $this->assertIsArray($ukm->logo);
        $this->assertEquals(['path/to/logo.png'], $ukm->logo);
    }

    public function test_created_at_and_updated_at_are_hidden()
    {
        $ukm = UnitKegiatan::factory()->create();
        $ukmArray = $ukm->toArray();

        $this->assertArrayNotHasKey('created_at', $ukmArray);
        $this->assertArrayNotHasKey('updated_at', $ukmArray);
    }
}
