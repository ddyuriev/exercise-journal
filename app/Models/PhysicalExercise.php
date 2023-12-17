<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PhysicalExercise extends Model
{

    const STATUS_PRIVATE = 1;
    const STATUS_IN_MODERATION = 2;
    const STATUS_APPROVED = 3;
    const STATUS_REJECTED = 4;

    protected $fillable = [
        'name', 'private_name', 'status', 'description', 'created_by', 'moderated_by', 'created_at', 'updated_at'
    ];

    public static function boot()
    {
        parent::boot();
        /**
         * moderated_by is defined and status = 2 cannot be present at the same time.
         * also, moderated_by cannot be empty and status 3
         */
        self::saving(function (PhysicalExercise $physicalExercise) {
            if (!empty($physicalExercise->moderated_by) && !empty($physicalExercise->status) && $physicalExercise->status == self::STATUS_IN_MODERATION) {
                Log::channel('db_integrity_violation')->error('PhysicalExercise STATUS_IN_MODERATION collision', ['user' => Auth::user(), 'model' => $physicalExercise]);
                throw new \Exception('internal error');
            }
            if (empty($physicalExercise->moderated_by) && !empty($physicalExercise->status) && $physicalExercise->status == self::STATUS_APPROVED) {
                Log::channel('db_integrity_violation')->error('PhysicalExercise STATUS_APPROVED collision', ['user' => Auth::user(), 'model' => $physicalExercise]);
                throw new \Exception('internal error');
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}
