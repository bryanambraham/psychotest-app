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
// Grup Rute yang memerlukan Login
Route::middleware(['auth'])->group(function () {

    /* |--------------------------------------------------------------------------
    | Sisi Peserta (Pengerjaan Ujian)
    |--------------------------------------------------------------------------
    */
    // Menampilkan halaman ujian & timer
    Route::get('/exam/{exam_id}', 'ExamController@show')->name('exam.show');

    // Auto-save jawaban via AJAX
    Route::post('/exam/answer', 'ExamController@storeAnswer')->name('exam.answer');

    // Simpan foto proctoring diam-diam
    Route::post('/proctoring/snap', 'ProctoringController@storeSnapshot')->name('proctoring.snap');


    /* |--------------------------------------------------------------------------
    | Sisi Admin (Manajemen Ujian & Peserta)
    |--------------------------------------------------------------------------
    */
    // Daftar semua materi ujian
    Route::get('/manage-exams', 'ExamManagementController@index')->name('manage-exams.index');

    // Form tambah materi baru
    Route::get('/manage-exams/create', 'ExamManagementController@create')->name('manage-exams.create');

    // Simpan materi baru (logic pengiriman ke Python bisa ditaruh di sini)
    Route::post('/manage-exams', 'ExamManagementController@store')->name('manage-exams.store');

    // Form edit ujian & Atur Penugasan Peserta (Assignment)
    Route::get('/manage-exams/{id}/peserta', 'ExamManagementController@edit')->name('manage-exams.edit');

    // Update data ujian & Sinkronisasi Peserta (Sync many-to-many)
    Route::put('/manage-exams/{id}', 'ExamManagementController@update')->name('manage-exams.update');

});
