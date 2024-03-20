<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DocumentController;

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
})->name('home');

Route::controller(DocumentController::class)
->prefix('documents')
->group(function(){
    Route::get('/', 'index')->name('documents.index');
    Route::post('/store', 'store')->name('documents.store');
    Route::get('/download/{document}', 'download')->name('documents.download');
    Route::delete('/{document}', 'destroy')->name('documents.destroy');
    Route::get('/{document}', 'test')->name('documents.test');
    // Route::get('/{document}/edit', 'edit')->name('documents.edit');
    // Route::put('/{document}', 'update')->name('documents.update');
});

Route::controller(ChatController::class)
->prefix('chat')
->group(function(){
    Route::get('/', 'index')->name('chat.index');
    // Route::post('/store', 'store')->name('chat.store');
    // Route::delete('/{chat}', 'destroy')->name('chat.destroy');
    // Route::get('/{chat}', 'test')->name('chat.test');
    // Route::get('/{chat}/edit', 'edit')->name('chat.edit');
    // Route::put('/{chat}', 'update')->name('chat.update');
});

