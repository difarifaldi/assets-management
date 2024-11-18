<?php

namespace App\Models\asset;

use App\Models\history\HistoryAssign;
use App\Models\history\HistoryCheckInOut;
use App\Models\history\HistoryMaintence;
use App\Models\master\CategoryAssets;
use App\Models\master\Brand;
use App\Models\master\Manufacture;
use App\Models\master\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'aset';

    public function kategori()
    {
        return $this->belongsTo(CategoryAssets::class, 'id_kategori_aset');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'id_brand');
    }

    public function manufaktur()
    {
        return $this->belongsTo(Manufacture::class, 'id_manufaktur');
    }

    public function assignTo()
    {
        return $this->belongsTo(User::class, 'ditugaskan_ke');
    }

    public function checkOut()
    {
        return $this->belongsTo(User::class, 'dipinjam_oleh');
    }

    public function historyAssign()
    {
        return $this->hasMany(HistoryAssign::class, 'id_aset')->orderBy('history_assigns.created_at', 'desc')->orderBy('history_assigns.latest', 'desc');
    }

    public function historyCheck()
    {
        return $this->hasMany(HistoryCheckInOut::class, 'id_aset')->orderBy('history_check_in_outs.created_at', 'desc')->orderBy('history_check_in_outs.latest', 'desc');
    }

    public function historyMaintence()
    {
        return $this->hasMany(HistoryMaintence::class, 'id_aset')->orderBy('history_maintences.created_at', 'desc')->orderBy('history_maintences.latest', 'desc');
    }

    public function getAttachmentArrayAttribute()
    {
        return json_decode($this->attributes['lampiran'], true);
    }
}
