<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;
/**
 * @param string $website
 * @param string $source
 * @param string $asin
 * @param array $response
 * @param string $url
 * @param string $title
 * @param string $title_fa
 * @param string $price
 * @param string $retail_price
 * @param string $price_saving
 * @param string $ratings
 * @param array $images
 * @param string $description
 * @param string $description_fa
 * @param array $reviews
 * @param array $videos
 * @param array $details
 * @param array $features
 * @param string $exchange_type
 * @param string $exchange_name
 * @param string $region_type
 * @param string $region_name
 * @param string $weight
 * @param string $weight_unit
 * @param array $categories
 * @param array $variations
 * @param integer $view_count
 */
class CrawlerProduct extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'crawler_products';


    protected $guarded = [];

}
