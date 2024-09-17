<?php

namespace App\Models;

use App\Models\master\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryAssign extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = null;
    public $incrementing = false;

    public function assignTo()
    {
        return $this->belongsTo(User::class, 'assign_to');
    }

    public function returnBy()
    {
        return $this->belongsTo(User::class, 'return_by');
    }
}
