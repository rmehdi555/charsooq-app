<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDescription extends Model
{
    protected $fillable = [
        'invoice_id',
        'invoice_item_id',
        'logistics_id',
        'description',
        'level',
        'agent_id',
    ];
}
