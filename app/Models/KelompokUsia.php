<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelompokUsia extends Model
{
    protected $table = 'kelompok_usias';

    protected $fillable = [
        'name',
    ];

    public function dataset()
    {
        return $this->hasMany(Dataset::class, 'kelompok_usia_id');
    }
}
