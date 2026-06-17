<?php

use App\Http\Controllers\ActivityLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman Landing Page
Route::get('/', function () {
    return redirect('/login');
});

// Rute Autentikasi Bawaan Laravel UI (Login, Register, Forgot Password)
Auth::routes();

Route::get('/register', function () {
    return redirect('/login');
});


// Rute Dashboard Utama setelah Login
Route::get('/home', 'HomeController@index')->name('home');

// ==========================================
// RUTE UJIAN PSIKOTES & PROCTORING
// ==========================================
// Sisi Peserta (Pengerjaan Ujian) - publik tanpa login
Route::post('/exam/{exam}/participant', 'ExamController@storeParticipant')->name('exam.participant.store');
Route::get('/exam/{exam}/instructions', 'ExamController@instructions')->name('exam.instructions');
Route::post('/exam/{exam}/begin', 'ExamController@begin')->name('exam.begin');
Route::get('/exam/{exam}/take/{session_id}', 'ExamController@take')->name('exam.take');

Route::post('/exam/upload-file-answer', 'ExamController@uploadFileAnswer')->name('exam.upload-file');
Route::post('/exam/delete-file-answer', 'ExamController@deleteFileAnswer')->name('exam.delete-file');

// Auto-save jawaban via AJAX
Route::post('/exam/answer', 'ExamController@storeAnswer')->name('exam.answer');

// Simpan foto proctoring diam-diam
Route::post('/proctoring/snap', 'ProctoringController@storeSnapshot')->name('proctoring.snap');

// Rute untuk menyelesaikan ujian (mengubah status session menjadi completed)
Route::get('/exam/finish/{session_id}', 'ExamController@finish')->name('exam.finish');

// Menampilkan halaman awal pengisian data peserta
Route::get('/exam/{exam}', 'ExamController@show')->name('exam.show');

// Route::get('/exam/{exam}', function ($exam) {
//     return redirect()->route('exam.instructions', $exam);
// })->name('exam.show');

Route::get('/verify-data', 'VerifyUserController@create')->name('verify.create');
Route::post('/verify-data', 'VerifyUserController@store')->name('verify.store');

// Grup Rute yang memerlukan Login
Route::middleware(['auth'])->group(function () {
    Route::post('/admin/toggle-site-closed', 'ExamManagementController@toggleSiteClosed')->name('admin.toggle-site-closed');
    /* --------------------------------------------------------------------------
    | Sisi Admin (Manajemen Ujian & Peserta)
    |--------------------------------------------------------------------------
    */
    // Daftar semua materi ujian
    Route::get('/manage-exams', 'ExamManagementController@index')->name('manage-exams.index')->middleware('role:admin');

    Route::delete('/manage-exams/destroy/{id}', 'ExamManagementController@destroy')->name('manage-exams.destroy')->middleware('role:admin');

    // Form tambah materi baru
    Route::get('/manage-exams/create', 'ExamManagementController@create')->name('manage-exams.create')->middleware('role:admin');

    // Simpan materi baru (logic pengiriman ke Python bisa ditaruh di sini)
    Route::post('/manage-exams', 'ExamManagementController@store')->name('manage-exams.store')->middleware('role:admin');

    // Form edit ujian & Atur Penugasan Peserta (Assignment)
    Route::get('/manage-exams/{id}/peserta', 'ExamManagementController@edit')->name('manage-exams.edit')->middleware('role:admin');

    Route::get('/manage-exams/edit/{id}', 'ExamManagementController@edit')->name('manage-exams.edit')->middleware('role:admin');

    // Update data ujian & Sinkronisasi Peserta (Sync many-to-many)
    Route::put('/manage-exams/{id}', 'ExamManagementController@update')->name('manage-exams.update')->middleware('role:admin');

    // Resource route untuk User (index, create, store, edit, update, destroy)
    Route::resource('users', 'UserController')->middleware('role:admin');
    Route::post('/users/import', [App\Http\Controllers\UserController::class, 'import'])->name('users.import');

    Route::get('/exam-results', 'ExamManagementController@resultsIndex')->name('manage-exams.results')->middleware('role:admin');
    Route::delete('/exam-results/{id}', 'ExamManagementController@destroyResultsIndex')->name('manage-exams.results.destroy')->middleware('role:admin');
    Route::get('/exam-results/{session_id}', 'ExamManagementController@resultsShow')->name('manage-exams.results.show')->middleware('role:admin');

    // TAMBAHKAN ROUTE EXPORT EXCEL DI SINI
    Route::get('/exam-results/{session_id}/export', 'ExamManagementController@exportExcel')->name('manage-exams.results.export')->middleware('role:admin');
    
    // Download QR untuk setiap ujian (menghasilkan PNG yang bisa diunduh)
    Route::get('/manage-exams/{id}/qr', 'ExamManagementController@downloadQr')->name('manage-exams.qr')->middleware('role:admin');

    // Rute untuk mengelola kunci jawaban
    Route::get('/manage-exams/{id}/answer-keys', 'ExamManagementController@editAnswerKeys')->name('manage-exams.edit-answer-keys')->middleware('role:admin');
    Route::put('/manage-exams/{id}/answer-keys', 'ExamManagementController@updateAnswerKeys')->name('manage-exams.update-answer-keys')->middleware('role:admin');

    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

});