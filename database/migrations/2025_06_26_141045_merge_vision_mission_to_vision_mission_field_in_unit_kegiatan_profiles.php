<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the new vision_mission column
        Schema::table('unit_kegiatan_profiles', function (Blueprint $table) {
            $table->text('vision_mission')->nullable()->after('description');
        });

        // Merge existing vision and mission data
        DB::statement("
            UPDATE unit_kegiatan_profiles 
            SET vision_mission = CONCAT(
                'Vision: ', COALESCE(vision, ''), 
                '\n\nMission: ', COALESCE(mission, '')
            )
            WHERE vision IS NOT NULL OR mission IS NOT NULL
        ");

        // Remove the old vision and mission columns
        Schema::table('unit_kegiatan_profiles', function (Blueprint $table) {
            $table->dropColumn(['vision', 'mission']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the original columns
        Schema::table('unit_kegiatan_profiles', function (Blueprint $table) {
            $table->string('vision')->nullable()->after('description');
            $table->string('mission')->nullable()->after('vision');
        });

        // Extract vision and mission from vision_mission
        $profiles = DB::table('unit_kegiatan_profiles')->whereNotNull('vision_mission')->get();

        foreach ($profiles as $profile) {
            $content = $profile->vision_mission;

            // Extract vision
            $vision = '';
            if (preg_match('/Vision:\s*(.*?)(?=\n\nMission:|$)/s', $content, $matches)) {
                $vision = trim($matches[1]);
            }

            // Extract mission
            $mission = '';
            if (preg_match('/Mission:\s*(.*?)$/s', $content, $matches)) {
                $mission = trim($matches[1]);
            }

            DB::table('unit_kegiatan_profiles')
                ->where('id', $profile->id)
                ->update([
                    'vision' => $vision ?: null,
                    'mission' => $mission ?: null,
                ]);
        }

        // Drop the vision_mission column
        Schema::table('unit_kegiatan_profiles', function (Blueprint $table) {
            $table->dropColumn('vision_mission');
        });
    }
};
