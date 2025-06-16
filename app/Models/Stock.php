<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'ready_stock',
        'in_process_stock',
        'process_status',
        'is_critical',
        'estimated_depletion_date',
        'expired_date', // Tambahkan ini
    ];

    protected $casts = [
        'estimated_depletion_date' => 'date',
        'expired_date' => 'date', // Tambahkan ini
        'is_critical' => 'boolean',
    ];

    /**
     * Get the raw material that owns the stock.
     */
    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}