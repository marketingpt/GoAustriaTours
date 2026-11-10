<?php

namespace App\Console\Commands;

use App\Services\Ghl\GhlClient;
use App\Services\Ghl\GhlFields;
use Illuminate\Console\Command;

class GhlSetup extends Command
{
    protected $signature = 'ghl:setup {--dry-run : List what would be created without creating it}';

    protected $description = 'Create any missing GoHighLevel custom fields (safe to re-run)';

    public function handle(GhlClient $ghl): int
    {
        if (! $ghl->enabled()) {
            $this->error('GHL_API_TOKEN and GHL_LOCATION_ID must both be set in .env first.');

            return self::FAILURE;
        }

        try {
            $existing = collect($ghl->listFields())->pluck('id', 'name');
        } catch (\Throwable $e) {
            $this->error($ghl->scrubMessage($e));

            return self::FAILURE;
        }

        $created = $skipped = 0;
        foreach (GhlFields::all() as $name => $type) {
            if ($existing->has($name)) {
                $skipped++;
                $this->line("  exists   {$name}");

                continue;
            }
            if ($this->option('dry-run')) {
                $this->warn("  would create {$name} ({$type})");

                continue;
            }
            try {
                $ghl->createField($name, $type);
                $created++;
                $this->info("  created  {$name} ({$type})");
            } catch (\Throwable $e) {
                $this->error("  FAILED   {$name}: ".$ghl->scrubMessage($e));
            }
        }

        $ghl->fieldMap(fresh: true);
        $this->newLine();
        $this->info("Done. Created {$created}, already existed {$skipped}.");
        $this->line('Tip: in GHL you may move these fields into folders, but do NOT rename them.');

        return self::SUCCESS;
    }
}
