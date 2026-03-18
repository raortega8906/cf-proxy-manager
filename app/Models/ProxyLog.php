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

    public function getReasonLabelAttribute(): string
    {
        return match($this->reason) {
            'laliga'      => '⚽ LaLiga',
            'ssl_renewal' => '🔒 SSL',
            default       => '✋ Manual',
        };
    }
}
