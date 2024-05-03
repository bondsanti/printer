<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'tb_position';

    public function  roleUsers()
    {
        return $this->hasMany(Role_user::class);
    }
}
