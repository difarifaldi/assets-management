<?php

namespace App\Models\master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'divisi';

    public function user()
    {
        return $this->hasMany(User::class, 'id_divisi');
    }
}
