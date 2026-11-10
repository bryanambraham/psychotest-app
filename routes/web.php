<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\UserController;
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
    // Verifikasi User Admin
    Route::get('/verify-user', 'VerifyUserController@index')
        ->name('verify_users.index')
        ->middleware('permission:verify_users.view');

    Route::delete('/verify-data/{id}', 'VerifyUserController@destroy')
        ->name('verify_user.destroy')
        ->middleware('permission:verify_users.delete');

    Route::delete('/verify-data/destroy/all', 'VerifyUserController@destroyAll')
        ->name('verify_user_all.destroy')
        ->middleware('permission:verify_users.delete_all');
    Route::post('/admin/toggle-site-closed', 'ExamManagementController@toggleSiteClosed')
        ->name('admin.toggle-site-closed')
        ->middleware('permission:system.toggle_closed');

    /* --------------------------------------------------------------------------
    | Sisi Admin & Manajemen (Dilindungi Permission)
    |--------------------------------------------------------------------------
    */
    // Manajemen Ujian
    Route::get('/manage-exams', 'ExamManagementController@index')
        ->name('manage-exams.index')
        ->middleware('permission:exams.view');

    Route::get('/manage-exams/create', 'ExamManagementController@create')
        ->name('manage-exams.create')
        ->middleware('permission:exams.create');

    Route::post('/manage-exams', 'ExamManagementController@store')
        ->name('manage-exams.store')
        ->middleware('permission:exams.create');

    Route::get('/manage-exams/{id}/peserta', 'ExamManagementController@edit')
        ->name('manage-exams.edit')
        ->middleware('permission:exams.edit');

    Route::get('/manage-exams/edit/{id}', 'ExamManagementController@edit')
        ->name('manage-exams.edit')
        ->middleware('permission:exams.edit');

    Route::put('/manage-exams/{id}', 'ExamManagementController@update')
        ->name('manage-exams.update')
        ->middleware('permission:exams.edit');

    Route::get('/manage-exams/{id}', function ($id) {
        return redirect()->route('manage-exams.edit', $id);
    })->where('id', '[0-9]+')->middleware('permission:exams.edit');

    Route::delete('/manage-exams/destroy/{id}', 'ExamManagementController@destroy')
        ->name('manage-exams.destroy')
        ->middleware('permission:exams.delete');

    Route::get('/manage-exams/{id}/qr', 'ExamManagementController@downloadQr')
        ->name('manage-exams.qr')
        ->middleware('permission:exams.qr');

    Route::get('/manage-exams/{id}/answer-keys', 'ExamManagementController@editAnswerKeys')
        ->name('manage-exams.edit-answer-keys')
        ->middleware('permission:exams.answer_keys');

    Route::put('/manage-exams/{id}/answer-keys', 'ExamManagementController@updateAnswerKeys')
        ->name('manage-exams.update-answer-keys')
        ->middleware('permission:exams.answer_keys');

    Route::get('/manage-exam/instruction/{exam}', 'ExamManagementController@editInstruction')
        ->name('manage-exams.edit-instructions')
        ->middleware('permission:exams.instructions');

    Route::post('/manage-exams/{id}/update-instructions', 'ExamManagementController@updateInstructions')
        ->name('manage-exams.update-instructions')
        ->middleware('permission:exams.instructions');

    // Hasil Ujian
    Route::get('/exam-results', 'ExamManagementController@resultsIndex')
        ->name('manage-exams.results')
        ->middleware('permission:exam_results.view');

    Route::get('/exam-results/{session_id}', 'ExamManagementController@resultsShow')
        ->name('manage-exams.results.show')
        ->middleware('permission:exam_results.detail');

    Route::delete('/exam-results/{id}', 'ExamManagementController@destroyResultsIndex')
        ->name('manage-exams.results.destroy')
        ->middleware('permission:exam_results.delete');

    Route::get('/exam-results/{session_id}/export', 'ExamManagementController@exportExcel')
        ->name('manage-exams.results.export')
        ->middleware('permission:exam_results.export');

    // Manajemen User
    Route::resource('users', 'UserController');
    Route::post('/users/import', [App\Http\Controllers\UserController::class, 'import'])
        ->name('users.import')
        ->middleware('permission:users.import');

    Route::get('/users/{user}/interview', [UserController::class, 'filterInterview'])
        ->name('users.interview')
        ->middleware('permission:users.interview');

    Route::post('/users/{user}/interview', [UserController::class, 'undangInterview'])
        ->name('users.interview.send')
        ->middleware('permission:users.interview');

    // Manajemen Role & Permission
    Route::resource('roles', 'RoleController');

    // Activity Log
    Route::get('/activity-log', [ActivityLogController::class, 'index'])
        ->name('activity-log.index')
        ->middleware('permission:activity_log.view');

    Route::delete('/activity-log/destroy', [ActivityLogController::class, 'destroy'])
        ->name('activity-log.destroy')
        ->middleware('permission:activity_log.delete');
});