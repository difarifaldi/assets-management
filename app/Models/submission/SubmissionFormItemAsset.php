<?php

namespace App\Models\submission;

use App\Models\asset\Asset;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionFormItemAsset extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'assets_id');
    }
}
