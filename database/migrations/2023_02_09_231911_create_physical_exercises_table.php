<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('physical_exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('private_name')->index();
            $table->text('description')->nullable()->index();
            $table->boolean('status');
            $table->unsignedInteger('created_by');
            $table->timestamps();
        });

        Schema::create('physical_exercise_user', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('physical_exercise_id');
            $table->primary(['user_id', 'physical_exercise_id']);
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('physical_exercises');
        Schema::dropIfExists('physical_exercise_user');
    }
};
