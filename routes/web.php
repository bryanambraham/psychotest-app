<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Landing Page
Route::get('/', function () {
    return view('welcome');
});

// Rute Autentikasi Bawaan Laravel UI (Login, Register, Forgot Password)
Auth::routes();

// Rute Dashboard Utama setelah Login
Route::get('/home', 'HomeController@index')->name('home');

// ==========================================
// RUTE UJIAN PSIKOTES & PROCTORING
// ==========================================
// Dibungkus middleware 'auth' agar wajib login
Route::middleware(['auth'])->group(function () {

    // Menampilkan halaman ujian (Timer & Soal)
    Route::get('/exam/{exam_id}', 'ExamController@show')->name('exam.show');

    // Endpoint AJAX untuk auto-save jawaban JSON
    Route::post('/exam/answer', 'ExamController@storeAnswer')->name('exam.answer');

    // Endpoint AJAX rahasia untuk menerima foto dari webcam
    Route::post('/proctoring/snap', 'ProctoringController@storeSnapshot')->name('proctoring.snap');

});
