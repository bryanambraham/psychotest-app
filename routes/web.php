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

    // Rute untuk menyelesaikan ujian (mengubah status session menjadi completed)
    Route::get('/exam/finish/{session_id}', 'ExamController@finish')->name('exam.finish');

    /* |--------------------------------------------------------------------------
    | Sisi Admin (Manajemen Ujian & Peserta)
    |--------------------------------------------------------------------------
    */
    // Daftar semua materi ujian
    Route::get('/manage-exams', 'ExamManagementController@index')->name('manage-exams.index')->middleware('role:admin');

    // Form tambah materi baru
    Route::get('/manage-exams/create', 'ExamManagementController@create')->name('manage-exams.create')->middleware('role:admin');

    // Simpan materi baru (logic pengiriman ke Python bisa ditaruh di sini)
    Route::post('/manage-exams', 'ExamManagementController@store')->name('manage-exams.store')->middleware('role:admin');

    // Form edit ujian & Atur Penugasan Peserta (Assignment)
    Route::get('/manage-exams/{id}/peserta', 'ExamManagementController@edit')->name('manage-exams.edit')->middleware('role:admin');

    // Update data ujian & Sinkronisasi Peserta (Sync many-to-many)
    Route::put('/manage-exams/{id}', 'ExamManagementController@update')->name('manage-exams.update')->middleware('role:admin');

    // Resource route untuk User (index, create, store, edit, update, destroy)
    Route::resource('users', 'UserController')->middleware('role:admin');

    Route::get('/exam-results', 'ExamManagementController@resultsIndex')->name('manage-exams.results')->middleware('role:admin');
    Route::get('/exam-results/{session_id}', 'ExamManagementController@resultsShow')->name('manage-exams.results.show')->middleware('role:admin');
});
