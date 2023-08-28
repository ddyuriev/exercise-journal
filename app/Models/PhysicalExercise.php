<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalExercise extends Model
{

    const STATUS_PRIVATE = 1;
    const STATUS_PUBLIC = 2;
    const STATUS_CONFIRMED = 3;

    protected $fillable = [
        'name', 'private_name', 'status', 'description', 'created_by', 'created_at', 'updated_at'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
