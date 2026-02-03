<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function cattle()
    {
        return $this->belongsTo(Cattle::class);
    }

    // Query Scopes
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
