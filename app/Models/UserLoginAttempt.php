<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLoginAttempt extends Model
{
    use HasFactory;

    protected $table = 'user_login_attempt';

    protected $fillable = ['user_id','ip_address'];
}
