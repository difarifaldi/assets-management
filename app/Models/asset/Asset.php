<?php

namespace App\Models\asset;

use App\Models\master\CategoryAssets;
use App\Models\master\Brand;
use App\Models\master\User;
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

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class, 'assign_to');
    }

    public function getAttachmentArrayAttribute()
    {
        return json_decode($this->attributes['attachment'], true);
    }
}
