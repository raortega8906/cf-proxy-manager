<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProxySite extends Model
{
    protected $fillable = [
        'name',
        'domain',
        'cloudflare_zone_id',
        'cloudflare_dns_record_id',
        'proxy_enabled',
        'ssl_auto_renewal',
        'ssl_next_renewal',
        'affected_by_laliga',
    ];

    protected $casts = [
        'proxy_enabled'            => 'boolean',
        'cloudflare_zone_id'        => 'encrypted',
        'cloudflare_dns_record_id'  => 'encrypted',
        'ssl_auto_renewal'         => 'boolean',
        'affected_by_laliga'       => 'boolean',
        'ssl_next_renewal'         => 'date',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(ProxyLog::class, 'site_id');
    }
}
