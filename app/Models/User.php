<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $connection = 'tollab';
    protected $table = 'members_fixfields';
    protected $primaryKey = 'pn_mid';

    public $timestamps = false;
    protected $guarded = [];

}
