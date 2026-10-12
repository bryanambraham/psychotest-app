<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * Sesi/token CSRF yang kadaluarsa (419) biasanya kejadian pas user ninggalin
     * tab lama kebuka (sesi abis idle) terus balik lagi nyoba submit form -- defaultnya
     * Laravel nampilin halaman error teknis yang bikin bingung. Di sini di-redirect
     * balik ke halaman sebelumnya (atau login kalau gak ada) dengan pesan yang jelas,
     * gak usah tampilin error mentah ke user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            // Log buat diagnosis kenapa 419 sering muncul di server:
            // - session_cookie_sent = false -> cookie sesi hilang/ketimpa (bentrok cookie, domain, dll)
            // - session_cookie_sent = true tapi tetap 419 -> token di halaman basi (tab lama / login ulang di tab lain)
            Log::warning('CSRF token mismatch (419)', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'referer' => $request->headers->get('referer'),
                'session_cookie' => config('session.cookie'),
                'session_cookie_sent' => $request->cookies->has(config('session.cookie')),
                'form_token_sent' => $request->has('_token'),
                'logged_in' => auth()->check(),
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Masih login tapi token beda (misal habis login ulang di tab lain):
            // balik ke form sebelumnya, isian tetap ada, tinggal klik simpan lagi.
            if (auth()->check()) {
                return redirect()->back()
                    ->withInput($request->except('_token', '_method'))
                    ->with('error', 'Halaman sudah kedaluwarsa, data belum tersimpan. Silakan klik simpan sekali lagi.');
            }

            return redirect()
                ->guest(route('login'))
                ->with('error', 'Sesi Anda sudah berakhir karena terlalu lama tidak aktif. Silakan login kembali.');
        }

        return parent::render($request, $e);
    }
}
