<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaymentConfiguration;

class CleanupCustomFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:cleanup-custom-fields {--dry-run : Show what would be cleaned without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up corrupted custom fields in payment configurations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info('Starting custom fields cleanup...');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        $configurations = PaymentConfiguration::whereNotNull('custom_fields')->get();
        $totalProcessed = 0;
        $totalCleaned = 0;

        foreach ($configurations as $config) {
            $totalProcessed++;
            $originalFields = $config->custom_fields;
            $cleanedFields = $config->sanitizeCustomFields($originalFields);

            if (count($originalFields) !== count($cleanedFields)) {
                $totalCleaned++;
                $removedCount = count($originalFields) - count($cleanedFields);

                $this->line("Payment Configuration ID {$config->id} ('{$config->name}'):");
                $this->line("  - Original fields: " . count($originalFields));
                $this->line("  - Valid fields: " . count($cleanedFields));
                $this->line("  - Removed {$removedCount} corrupted field(s)");

                if (!$dryRun) {
                    $config->update(['custom_fields' => $cleanedFields]);
                    $this->info("  ✓ Cleaned up successfully");
                } else {
                    $this->warn("  → Would be cleaned (dry run)");
                }

                $this->line('');
            }
        }

        $this->line('===========================================');
        $this->info("Summary:");
        $this->info("- Total configurations processed: {$totalProcessed}");
        $this->info("- Configurations cleaned: {$totalCleaned}");
        $this->info("- Configurations already clean: " . ($totalProcessed - $totalCleaned));

        if ($totalCleaned > 0) {
            if ($dryRun) {
                $this->warn("\nTo apply these changes, run without --dry-run flag:");
                $this->warn("php artisan payment:cleanup-custom-fields");
            } else {
                $this->info("\n✓ All corrupted custom fields have been cleaned up!");
            }
        } else {
            $this->info("\n✓ All custom fields are already in good shape!");
        }

        return 0;
    }
}
