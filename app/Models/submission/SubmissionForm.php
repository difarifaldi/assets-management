<?php

namespace App\Models\submission;

use App\Models\master\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionForm extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissionFormItemAsset()
    {
        return $this->hasMany(SubmissionFormItemAsset::class, 'submission_form_id');
    }
}
