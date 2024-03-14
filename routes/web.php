<?php

use Illuminate\Support\Facades\Route;
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
    // Route::get('/{document}', 'show')->name('documents.show');
    // Route::get('/{document}/edit', 'edit')->name('documents.edit');
    // Route::put('/{document}', 'update')->name('documents.update');
});

