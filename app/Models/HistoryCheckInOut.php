<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryCheckInOut extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = null;
    public $incrementing = false;
}
