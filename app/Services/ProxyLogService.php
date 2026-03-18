<?php

namespace App\Services;

use App\Models\ProxyLog;
use App\Models\ProxySite;

class ProxyLogService {

    public function writeLogs(ProxySite $site, string $action, string $reason, string $status, string $message): void
    {
        ProxyLog::create([
            'action' => $action,
            'reason' => $reason,
            'status' => $status,
            'message' => $message, 
            'site_id' => $site->id
        ]);
    }

}