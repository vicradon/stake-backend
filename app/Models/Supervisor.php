<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function coordinator()
    {
        return $this->belongsTo(Coordinator::class);
    }

    public function student()
    {
        return $this->hasMany(Student::class, 'supervisor_id');
    }

    public function projectCategory()
    {
        return $this->belongsTo(ProjectCategory::class);
    }
}
