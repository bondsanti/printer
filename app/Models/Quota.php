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
        'id', 'name', 'department', 'code', 'printername','total_color_24', 'total_bw_24','total_color_25', 'total_bw_25'

    ];

    public function user_ref()
    {
        return $this->belongsTo(User::class, 'code', 'code');
    }

    public function dep_ref()
    {
        return $this->belongsTo(Department::class, 'department_id','id');
    }
}
