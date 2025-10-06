<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\LogbookItemController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;


Route::get('/', function () {
    return view('welcome');
});

// Route untuk authenticated users
Route::middleware('auth')->group(function () {
	// Routes for users profile
	Route::get('/profile/{user:name}', [ProfileController::class, 'show'])
     ->name('profile.show');

	Route::get('/profile/{user:name}/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
	Route::get('/qr-code/{user:name}', [ProfileController::class, 'generateQrCode'])->name('profile.qr');
	
	// Routes for account settings
	Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
		Route::get('/settings', [AccountController::class, 'settings'])->name('settings');
		Route::get('/security', [AccountController::class, 'security'])->name('security');
		Route::get('/notifications', [AccountController::class, 'notifications'])->name('notifications');

		Route::patch('/settings/details', [AccountController::class, 'updateDetails'])->name('update.details');
		Route::put('/security/password', [AccountController::class, 'updatePassword'])->name('update.password');
	});

	// Routes for QRCode
	Route::get('/qr-code/{user:name}', [ProfileController::class, 'generateQrCode'])
		 ->name('profile.qr');

    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

	 // Routes for Logbook
	Route::get('/logbook/{unit_id}/dashboard', [LogbookController::class, 'index'])->name('logbook.index');
    Route::get('/logbook/{unit_id}/dashboard/create', [LogbookController::class, 'create'])->name('logbook.create');
    Route::post('/logbook/{unit_id}/dashboard/store', [LogbookController::class, 'store'])->name('logbook.store');
    Route::get('/logbook/{unit_id}/dashboard/{logbook_id}/items', [LogbookController::class, 'items'])->name('logbook.items');
    Route::get('/logbook/{unit_id}/dashboard/edit/{logbook_id}', [LogbookController::class, 'edit'])->name('logbook.edit');
    Route::put('/logbook/{unit_id}/dashboard/update/{logbook_id}', [LogbookController::class, 'update'])->name('logbook.update');
    Route::delete('/logbook/{unit_id}/dashboard/delete/{logbook_id}', [LogbookController::class, 'destroy'])->name('logbook.destroy');
	Route::put('/logbook/{unit_id}/dashboard/approve/{logbook_id}', [LogbookController::class, 'approve'])->name('logbook.approve');
	Route::get('/logbook/{unit_id}/dashboard/{logbook_id}/view', [LogbookController::class, 'show'])->name('logbook.view');
	Route::get('/logbook/{unit_id}/dashboard/{logbook_id}/edit-content', [LogbookController::class, 'editContent'])->name('logbook.edit.content');
	
	
	// Routes for Tools
	Route::get('/manage/tools', [ToolController::class, 'index'])->name('tools.index');
	Route::post('/tools/update', [ToolController::class, 'update']);
	Route::post('/tools/delete', [ToolController::class, 'delete']);
	
	//Routes for positions
	Route::get('/manage/position', [PositionController::class, 'index'])->name('position.index');
	Route::post('/positions/update', [PositionController::class, 'update'])->name('positions.update');
	Route::post('/positions/delete', [PositionController::class, 'delete'])->name('positions.delete');
	
	//Routes for manage Users
	Route::get('/manage/users', [UserController::class, 'index'])->name('users.index');
	Route::get('/manage/users/create', [UserController::class, 'create'])->name('users.create');
	Route::post('/manage/users', [UserController::class, 'store'])->name('users.store');
	Route::get('/manage/users/{user}', [UserController::class, 'show'])->name('users.show');
	Route::get('/manage/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
	Route::put('/manage/users/{user}', [UserController::class, 'update'])->name('users.update');
	Route::delete('/manage/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Routes for Logbook Items
    Route::get('/logbook/{unit_id}/dashboard/{logbook_id}/item/create', [LogbookItemController::class, 'create'])->name('logbook.item.create');
    Route::post('/logbook/{unit_id}/dashboard/{logbook_id}/item/store', [LogbookItemController::class, 'store'])->name('logbook.item.store');
    Route::get('/logbook/{unit_id}/dashboard/{logbook_id}/item/{item_id}/edit', [LogbookItemController::class, 'edit'])->name('logbook.item.edit');
    Route::put('/logbook/{unit_id}/dashboard/{logbook_id}/item/{item_id}', [LogbookItemController::class, 'update'])->name('logbook.item.update');
    Route::delete('/logbook/{unit_id}/dashboard/{logbook_id}/item/{item_id}', [LogbookItemController::class, 'destroy'])->name('logbook.item.destroy');
});

require __DIR__.'/auth.php';
