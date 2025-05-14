<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SetupController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

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

Route::get('/', function () {
    // Redirect to setup if not completed
    $setupStatePath = storage_path('app/setup_state.json');
    if (!file_exists($setupStatePath)) {
        return redirect('/setup');
    }
    
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Setup Routes
Route::prefix('setup')->middleware('setup')->group(function () {
    Route::get('/', [SetupController::class, 'index'])->name('setup.index');
    Route::post('/test-database', [SetupController::class, 'testDatabase'])->name('setup.test-database');
    Route::post('/save-database', [SetupController::class, 'saveDatabase'])->name('setup.save-database');
    Route::post('/run-migrations', [SetupController::class, 'runMigrations'])->name('setup.run-migrations');
    Route::post('/save-mail', [SetupController::class, 'saveMail'])->name('setup.save-mail');
    Route::post('/finalize', [SetupController::class, 'finalize'])->name('setup.finalize');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ticket Routes
    Route::resource('tickets', TicketController::class)->except(['edit', 'destroy']);
    Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])->name('tickets.comments.store');
});

require __DIR__.'/auth.php';
