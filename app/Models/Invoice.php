<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_from', 'invoice_to', 'invoice_number', 'amount_invoice', 'amount_paid'
    ];
}
