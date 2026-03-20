<?php 

namespace App\Actions;

use App\Models\ProxySchedule;
use Carbon\Carbon;

class CreateProxySchedule {

    public function writeAutomaticSchedule(string $type, string $description, Carbon $disable_at, Carbon $enable_at, string $status, array $site_ids): void
    {
        ProxySchedule::create([
            'type' => $type,
            'description' => $description,
            'disable_at' => $disable_at,
            'enable_at' => $enable_at, 
            'status' => $status,
            'site_ids' => $site_ids
        ]);
    }
    
}