<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth'],'prefix'=>'user','as'=>'user.'], function(){
    Route::post('task/status', [TaskController::class, 'status'])->name('task.status');
    Route::get('task/feature', [TaskController::class, 'feature_function'])->name('task.feature');
    Route::post('task/priority-change', [TaskController::class, 'priority_change'])->name('task.priority.change');
    Route::resource('task', TaskController::class);
});