<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

<<<<<<< HEAD
    protected $table = 'usuario';
    protected $primaryKey = 'id_user';
    public $timestamps = false;

=======
>>>>>>> 7abfc1b306a29fb563573d62b2755743c6aaad8f
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
<<<<<<< HEAD
        'username',
        'password',
        'id_rol',
=======
        'name',
        'email',
        'password',
>>>>>>> 7abfc1b306a29fb563573d62b2755743c6aaad8f
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
<<<<<<< HEAD
=======
        'remember_token',
>>>>>>> 7abfc1b306a29fb563573d62b2755743c6aaad8f
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
<<<<<<< HEAD
=======
            'email_verified_at' => 'datetime',
>>>>>>> 7abfc1b306a29fb563573d62b2755743c6aaad8f
            'password' => 'hashed',
        ];
    }
}
