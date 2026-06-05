<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\VerifyUser; // PASTIKAN INI ADA AGAR TIDAK ERROR SAAT CEK DATABASE

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // Default redirect
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // 1. FUNGSI USERNAME: Menentukan apakah inputan berupa email atau nama
    public function username()
    {
        $login = request()->input('login');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        
        // Gabungkan field yang terdeteksi ke dalam request
        request()->merge([$fieldType => $login]);
        
        return $fieldType;
    }

    // 2. FUNGSI CREDENTIALS: Menangkap password dan field (nama/email) yang benar
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    // 3. FUNGSI AUTHENTICATED: Logika Redirect SESUDAH Login Berhasil
    protected function authenticated(Request $request, $user)
    {
        // Jika rolenya BUKAN user (berarti admin), langsung lempar ke halaman home
        if ($user->role !== 'user') {
            return redirect()->route('home');
        }

        // PERBAIKAN: Cukup cek berdasarkan EMAIL saja. 
        // Mencocokkan nama sangat rawan gagal karena perbedaan spasi atau huruf besar/kecil.
        $isVerified = VerifyUser::where('email', $user->email)->orWhere('name', $user->name)->exists();

        // Jika belum ada datanya di tabel verify_user, paksa lempar ke form
        if (!$isVerified) {
            return redirect()->route('verify.create');
        }

        // Jika sudah terverifikasi (emailnya ketemu), persilakan masuk ke home
        return redirect()->route('home');
    }
}