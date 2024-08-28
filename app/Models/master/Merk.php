<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merk extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function asset()
    {
        return $this->hasMany(Merk::class, 'merk_id');
    }
}
