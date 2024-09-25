<?php

namespace App\Models\submission;

use App\Models\HistoryAssign;
use App\Models\HistoryCheckInOut;
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function submissionFormItemAsset()
    {
        return $this->hasMany(SubmissionFormItemAsset::class, 'submission_form_id');
    }

    public function submissionFormsCheckoutDate()
    {
        return $this->hasOne(SubmissionFormsCheckoutDate::class, 'submission_form_id');
    }

    public function historyAssign()
    {
        return $this->hasMany(HistoryAssign::class, 'submission_form_id');
    }

    public function historyCheckOut()
    {
        return $this->hasMany(HistoryCheckInOut::class, 'submission_form_id');
    }
}
