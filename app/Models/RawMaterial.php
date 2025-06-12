<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the stock associated with the raw material.
     */
    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    /**
     * Get the monthly requirements for the raw material.
     */
    public function monthlyRequirements(): HasMany
    {
        return $this->hasMany(MonthlyRequirement::class);
    }
}