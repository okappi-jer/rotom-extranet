<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outstanding extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_to', 'amount_outstanding', 'amount_due', 'invoice_from'
    ];
}
