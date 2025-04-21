<?php

namespace App\Console\Commands;

use App\Services\Api\ApiService;
use Illuminate\Console\Command;

class SyncApiDatabaseCommand extends Command
{
    protected $signature = 'app:sync-api-database';

    protected $description = 'Sync api to database';

    public function __construct(protected ApiService $apiService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->syncAll();

        $this->info('Synchronization completed!');

        return 0;
    }

    protected function syncAll(): void
    {
        $this->info('Syncing all data...');
        $results = $this->apiService->syncAll();

        foreach ($results as $type => $result) {
            if (isset($result['error'])) {
                $this->error("Error syncing $type: ".$result['error']);
            } else {
                $this->info("$type: ".$result['message']);
            }
        }

        $this->call(command: 'app:generate-product-discount');
    }
}
