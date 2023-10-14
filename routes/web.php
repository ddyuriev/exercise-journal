<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserPhysicalExerciseController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\PhysicalExerciseController;

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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [MainController::class, 'index'])->name('main.index');
    Route::post('/update', [MainController::class, 'update'])->name('main.update');


    Route::get('/profile', function () {
        return Auth::user()->name;
    })->name('profile.index');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    Route::resource('/settings/physical-exercises', PhysicalExerciseController::class, ['as' => 'settings']);
    Route::post('/settings/physical-exercises/toggle', [PhysicalExerciseController::class, 'toggle'])->name('settings.physical-exercises.toggle');
    Route::get('/settings/physical-exercises/search/{searchString}', [PhysicalExerciseController::class, 'search'])->name('settings.physical-exercises.search');

    Route::get('/day/{date}', [UserPhysicalExerciseController::class, 'view'])->name('user-physical-exercises.view');
    Route::post('/day/user-physical-exercises', [UserPhysicalExerciseController::class, 'store'])->name('user-physical-exercises.create');
    Route::put('/day/user-physical-exercises/{id}', [UserPhysicalExerciseController::class, 'update'])->name('user-physical-exercises.update');
    Route::delete('/day/user-physical-exercises/{id}', [UserPhysicalExerciseController::class, 'destroy'])->name('user-physical-exercises.destroy');

    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
});

Route::get('/register-retry', function () {
    Auth::logout();
    return redirect('/');
})->name('register.retry');
