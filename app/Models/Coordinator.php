<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinator extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function supervisors()
    {
        return $this->hasMany(Supervisor::class);
    }
}
