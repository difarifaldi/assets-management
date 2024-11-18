<?php

namespace App\Models\master;

use App\Models\asset\Asset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'brand';
    public function aset()
    {
        return $this->hasMany(Asset::class, 'id_brand');
    }
}
