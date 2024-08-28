<?php

namespace App\Models\asset;

use App\Models\master\CategoryAssets;
use App\Models\master\Merk;
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

    public function merk()
    {
        return $this->belongsTo(Merk::class, 'merk_id');
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class, 'assign_to');
    }
}
