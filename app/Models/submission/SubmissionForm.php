<?php

namespace App\Models\submission;

use App\Models\history\HistoryAssign;
use App\Models\history\HistoryCheckInOut;
use App\Models\master\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionForm extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'form_pengajuan';
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'ditolak_oleh');
    }

    public function submissionFormItemAsset()
    {
        return $this->hasMany(SubmissionFormItemAsset::class, 'id_form_pengajuan');
    }

    public function submissionFormsCheckoutDate()
    {
        return $this->hasOne(SubmissionFormsCheckoutDate::class, 'id_form_pengajuan');
    }

    public function historyAssign()
    {
        return $this->hasMany(HistoryAssign::class, 'id_form_pengajuan');
    }

    public function historyCheckOut()
    {
        return $this->hasMany(HistoryCheckInOut::class, 'id_form_pengajuan');
    }
}
