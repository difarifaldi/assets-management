<?php

namespace App\Models;

use App\Models\asset\Asset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryMaintence extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function maintenceAsset()
    {
        return $this->belongsTo(Asset::class, 'assets_id');
    }
}
