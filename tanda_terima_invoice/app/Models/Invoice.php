<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'customer_name',
        'tax_invoice_number',
    ];

    protected $casts = [
        'invoice_date' => 'date',
    ];

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}