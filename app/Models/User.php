<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
        'must_change_password',   
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'must_change_password'   => 'boolean',  
    ];

    public function cadet()
    {
        return $this->hasOne(Cadet::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCadet(): bool
    {
        return $this->role === 'cadet';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
            public function isGMA(): bool
        {
            return $this->role === 'GMA';
        }

        public function isS1(): bool
        {
            return $this->role === 'S1';
        }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

       public function gradeRecords()
   {
       return $this->hasMany(GradeRecord::class, 'user_id');
   }
}