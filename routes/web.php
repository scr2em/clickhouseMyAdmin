<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/database/{database}', [DatabaseController::class, 'show'])->name('database.show');
Route::get('/database/{database}/table/{table}/structure', [TableController::class, 'structure'])->name('table.structure');
Route::match(['get', 'post'], '/database/{database}/table/{table}/data', [TableController::class, 'data'])->name('table.data');
Route::post('/database/{database}/table/{table}/update-cell', [TableController::class, 'updateCell'])->name('table.updateCell');
Route::get('/query', [QueryController::class, 'index'])->name('query.index');
Route::post('/query', [QueryController::class, 'execute'])->name('query.execute');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::post('/users/{user}/grant', [UserController::class, 'grant'])->name('users.grant');
Route::post('/users/{user}/revoke', [UserController::class, 'revoke'])->name('users.revoke');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
