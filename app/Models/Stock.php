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
        // 'process_status', // Hapus ini jika Anda menghapusnya di migrasi
        'kansai_process_status', // Tambahkan ini
        'owell_process_status',  // Tambahkan ini
        'qa_aiia_process_status',// Tambahkan ini
        'is_critical',
        'estimated_depletion_date',
        'expired_date',
        'aiia_stock',
    ];

    protected $casts = [
        'estimated_depletion_date' => 'date',
        'expired_date' => 'date',
        'is_critical' => 'boolean',
    ];

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}