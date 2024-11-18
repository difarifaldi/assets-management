<?php

namespace App\Models\history;

use App\Models\asset\Asset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryMaintence extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'riwayat_perawatan';

    public function maintenceAsset()
    {
        return $this->belongsTo(Asset::class, 'id_aset');
    }
}
