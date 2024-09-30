<?php

namespace App\Models\asset;

use App\Models\history\HistoryAssign;
use App\Models\history\HistoryCheckInOut;
use App\Models\history\HistoryMaintence;
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

    public function checkOut()
    {
        return $this->belongsTo(User::class, 'check_out_by');
    }

    public function historyAssign()
    {
        return $this->hasMany(HistoryAssign::class, 'assets_id')->orderBy('history_assigns.created_at', 'desc')->orderBy('history_assigns.latest', 'desc');
    }

    public function historyCheck()
    {
        return $this->hasMany(HistoryCheckInOut::class, 'assets_id')->orderBy('history_check_in_outs.created_at', 'desc')->orderBy('history_check_in_outs.latest', 'desc');
    }

    public function historyMaintence()
    {
        return $this->hasMany(HistoryMaintence::class, 'assets_id')->orderBy('history_maintences.created_at', 'desc')->orderBy('history_maintences.latest', 'desc');
    }

    public function getAttachmentArrayAttribute()
    {
        return json_decode($this->attributes['attachment'], true);
    }
}
