<?php

namespace App\Models\history;

use App\Models\master\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryAssign extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $primaryKey = null;
    public $incrementing = false;
    protected $table = 'riwayat_penugasan';

    public function assignTo()
    {
        return $this->belongsTo(User::class, 'ditugaskan_ke');
    }

    public function returnBy()
    {
        return $this->belongsTo(User::class, 'dikembalikan_oleh');
    }
}
