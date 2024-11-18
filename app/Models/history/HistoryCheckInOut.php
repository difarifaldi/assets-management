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
    protected $table = 'riwayat_peminjaman';

    public function checkOut()
    {
        return $this->belongsTo(User::class, 'dipinjam_oleh');
    }

    public function checkIn()
    {
        return $this->belongsTo(User::class, 'pengembalian_oleh');
    }

    public function submission()
    {
        return $this->belongsTo(SubmissionForm::class, 'id_form_pengajuan');
    }
}
