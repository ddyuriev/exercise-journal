<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserPhysicalExerciseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('main.index');
    Route::post('/update', [MainController::class, 'update'])->name('main.update');


    Route::get('/profile', function () {
        return Auth::user()->name;
    })->name('profile.index');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    Route::get('/settings/physical-exercises', [SettingsController::class, 'physicalExercisesIndex'])->name('settings.physical-exercises.index');
    Route::post('/settings/physical-exercises/toggle', [SettingsController::class, 'physicalExercisesToggle'])->name('settings.physical-exercises.toggle');


    Route::get('/day/{date}', [UserPhysicalExerciseController::class, 'view'])->name('user-physical-exercises.view');
    Route::post('/day/user-physical-exercises', [UserPhysicalExerciseController::class, 'create'])->name('user-physical-exercises.create');
    Route::put('/day/user-physical-exercises/{id}', [UserPhysicalExerciseController::class, 'update'])->name('user-physical-exercises.update');
    Route::delete('/day/user-physical-exercises/{id}', [UserPhysicalExerciseController::class, 'destroy'])->name('user-physical-exercises.destroy');
});
