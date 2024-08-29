<?php

namespace App\Models\master;

use App\Models\asset\Asset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function asset()
    {
        return $this->hasMany(Asset::class, 'brand_id');
    }
}
