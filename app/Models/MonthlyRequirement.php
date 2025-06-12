<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonthlyRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_id',
        'year',
        'month',
        'total_monthly_usage',
        'weekly_usage_1',
        'weekly_usage_2',
        'weekly_usage_3',
        'weekly_usage_4',
    ];

    /**
     * Get the raw material that owns the monthly requirement.
     */
    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }
}