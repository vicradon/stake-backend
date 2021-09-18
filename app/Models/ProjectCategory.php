<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function project()
    {
        return $this->hasMany(Project::class);
    }

    public function supervisors()
    {
        return $this->belongsToMany(Supervisor::class);
    }
}
