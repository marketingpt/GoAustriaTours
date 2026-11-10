<?php

namespace App\Console\Commands;

use App\Models\Enquiry;
use App\Services\Ghl\GhlClient;
use App\Services\Ghl\GhlFields;
use App\Services\Ghl\GhlPayloadBuilder;
use Illuminate\Console\Command;

class GhlTest extends Command
{
    protected $signature = 'ghl:test {--keep : Do not delete the test contact afterwards}';

    protected $description = 'Check the GHL token and location, and create (then remove) a clearly labelled test contact';

    public function handle(GhlClient $ghl, GhlPayloadBuilder $builder): int
    {
        if (! $ghl->enabled()) {
            $this->error('GHL_API_TOKEN and GHL_LOCATION_ID must both be set in .env first.');

            return self::FAILURE;
        }

        try {
            $fields = $ghl->fieldMap(fresh: true);
            $this->info('Token and location OK. Custom fields found: '.count($fields));

            $missing = array_diff(array_keys(GhlFields::all()), array_keys($fields));
            if ($missing) {
                $this->warn('Missing fields (run php artisan ghl:setup): '.implode(', ', $missing));
            }

            $test = new Enquiry([
                'form_name' => 'ghl:test', 'first_name' => 'TEST', 'last_name' => 'Safe to delete',
                'email' => 'ghl-test+'.time().'@example.com', 'phone_e164' => '+15555550100',
                'consent_given' => true, 'consent_at' => now(), 'terms_version' => 'test',
                'source' => 'ghl-test', 'tracking_first' => ['utm_source' => 'ghl-test'], 'tracking_last' => ['utm_source' => 'ghl-test'],
            ]);
            $result = $ghl->upsert($builder->build($test, $fields));
            $this->info("Test contact created: {$result['id']}");

            if ($this->option('keep')) {
                $this->warn('Left in place (--keep). Delete it in GHL when done.');
            } else {
                $ghl->deleteContact($result['id']);
                $this->info('Test contact deleted.');
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($ghl->scrubMessage($e));

            return self::FAILURE;
        }
    }
}
