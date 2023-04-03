<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPhysicalExercise extends Model
{

    protected $fillable = [
        'user_id', 'physical_exercise_id', 'intraday_key', 'count', 'comment', 'created_at'
    ];

    public function physical_exercises()
    {
        return $this->belongsTo(PhysicalExercise::class, 'physical_exercise_id');
    }
}
