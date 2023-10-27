<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'unique_id',
        'supplier_code',
        'user_id',
        'user_name',
        'BTPLArticleCollie',
        'BTPLArticleWeight',
        'BTPLArtikelCode',
        'BTPLTekst',
        'BTPLVerpakkingsCode',
        'BTPLKaliber',
        'BTPLOrderDeliveryDate',
        'BTPLOrderDeliveryAt',
        'BTPLArticleRemark',
        'BTPLOrderReference',
        'BTPLLijnnr',
        'lotnumber'
    ];
}
