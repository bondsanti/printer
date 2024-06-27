<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quota extends Model
{
    use HasFactory;
    protected $connection = 'mysql_printer';
    protected $table = 'quota';

    protected $fillable = [
        'id', 'name', 'department', 'code_user', 'printername','total_color_24', 'total_bw_24','total_color_25', 'total_bw_25'

    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'code_user', 'code');
    }
}
