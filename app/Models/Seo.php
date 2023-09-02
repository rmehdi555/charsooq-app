<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    protected $table = 'seo';

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'title',
        'description',
        'keyword',
        'schema_markup',
        'header_script',
    ];
}
