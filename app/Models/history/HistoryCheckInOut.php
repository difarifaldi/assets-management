<?php

namespace App\Models\history;

use App\Models\master\User;
use App\Models\submission\SubmissionForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryCheckInOut extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = null;
    public $incrementing = false;

    public function checkOut()
    {
        return $this->belongsTo(User::class, 'check_out_by');
    }

    public function checkIn()
    {
        return $this->belongsTo(User::class, 'check_in_by');
    }

    public function submission()
    {
        return $this->belongsTo(SubmissionForm::class, 'submission_form_id');
    }
}
