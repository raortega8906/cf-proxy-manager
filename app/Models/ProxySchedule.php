<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class ProxySchedule extends Model
{
    protected $fillable = [
        'type',
        'description',
        'disable_at',
        'enable_at',
        'status',
        'site_ids',
    ];

    protected $casts = [
        'disable_at' => 'datetime',
        'enable_at'  => 'datetime',
        'site_ids'   => 'array',
    ];

    /**
     * Devuelve los sitios asociados a este schedule.
     * (No es una relación Eloquent estándar porque site_ids es JSON)
     */
    public function sites(): Collection
    {
        return ProxySite::whereIn('id', $this->site_ids ?? [])->get();
    }
}
