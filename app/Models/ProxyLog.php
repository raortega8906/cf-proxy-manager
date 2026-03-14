<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProxyLog extends Model
{
    protected $fillable = [
        'action',
        'reason',
        'status',
        'message',
        'site_id',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(ProxySite::class, 'site_id');
    }
}
