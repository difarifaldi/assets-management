<?php

namespace App\Models\asset;

use App\Models\master\CategoryAssets;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(CategoryAssets::class, 'category_asset_id');
    }
}
