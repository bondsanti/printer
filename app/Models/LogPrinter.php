<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPrinter extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'log_printer_fuji';

    protected $fillable = [
        'id', 'printername', 'date', 'time', 'jobtype','pc_name', 'code_user','username', 'filename', 'jobstatus','jobnumber','total_color','total_bw',

    ];
}
