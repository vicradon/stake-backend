<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Student extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reg_number'
    ];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class);
    }
}
