<?php

namespace App\Models;

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
}
