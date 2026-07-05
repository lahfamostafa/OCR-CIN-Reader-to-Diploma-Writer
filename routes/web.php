<?php

use App\Http\Controllers\Admin\DiplomaTemplateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CinUploadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiplomaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // OCR CIN upload & validation workflow
    Route::get('/cin/upload', [CinUploadController::class, 'create'])->name('cin.create');
    Route::post('/cin/upload', [CinUploadController::class, 'store'])->name('cin.store');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    // Diploma generation
    Route::get('/diplomas', [DiplomaController::class, 'index'])->name('diplomas.index');
    Route::post('/diplomas', [DiplomaController::class, 'store'])->name('diplomas.store');
    Route::get('/diplomas/{diploma}', [DiplomaController::class, 'show'])->name('diplomas.show');
    Route::get('/diplomas/{diploma}/preview', [DiplomaController::class, 'preview'])->name('diplomas.preview');
    Route::get('/diplomas/{diploma}/download', [DiplomaController::class, 'download'])->name('diplomas.download');
    Route::delete('/diplomas/{diploma}', [DiplomaController::class, 'destroy'])->name('diplomas.destroy');

    // Admin-only: users & diploma templates
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::resource('templates', DiplomaTemplateController::class)->except('show')->parameters([
            'templates' => 'template',
        ]);
    });
});

require __DIR__.'/auth.php';
