<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\VerifyUser; 

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // Default redirect jika lolos semua pengecekan
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // 1. FIXED: Mengembalikan 'login' agar sesuai dengan nama input di Blade
    // Ini memastikan @error('login') dapat menangkap pesan error saat login gagal
    public function username()
    {
        return 'login';
    }

    // 2. FIXED: Memproses input 'login' untuk mendeteksi email atau nama di database
    protected function credentials(Request $request)
    {
        $loginInput = $request->input('login');

        // Cek apakah input berupa email atau nama biasa
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        return [
            $field => $loginInput,
            'password' => $request->input('password')
        ];
    }

    // 3. FUNGSI AUTHENTICATED: Logika Pengalihan Setelah Berhasil Login
    protected function authenticated(Request $request, $user)
    {
        // Jika rolenya BUKAN user (misal: admin), langsung arahkan ke Home
        if ($user->role !== 'user') {
            return redirect()->route('home');
        }

        // Cek apakah data user sudah ada di tabel verify_user
        $isVerified = VerifyUser::where('email', $user->email)->exists();

        // Jika belum diverifikasi, paksa masuk ke form verifikasi data
        if (!$isVerified) {
            return redirect()->route('verify.create');
        }

        // Jika sudah lolos verifikasi, arahkan ke Home
        return redirect()->route('home');
    }

    // TIMPA FUNGSI ATTEMPT LOGIN BAWAAN LARAVEL
    protected function attemptLogin(Request $request)
    {
        $loginInput = $request->input('login');
        
        // Deteksi apakah yang diketik user itu email atau nama
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // 1. Cari user di database menggunakan $field yang sudah dideteksi (email / name)
        $user = \App\User::where($field, $loginInput)->first();

        // 2. Jika usernya ketemu, kita cek passwordnya
        if ($user) {
            try {
                // Buka gembok Crypt dari database menggunakan decryptString
                $decryptedPassword = \Illuminate\Support\Facades\Crypt::decryptString($user->password);
                
                // Cocokkan teks asli password dengan yang diketik user di form
                if ($decryptedPassword === $request->input('password')) {
                    // Jika cocok persis, jalankan proses login!
                    $this->guard()->login($user, $request->filled('remember'));
                    return true;
                }
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Jika terjadi error saat proses dekripsi, gagalkan login
                return false;
            }
        }

        // Jika user tidak ditemukan atau password tidak cocok, gagalkan login
        return false;
    }
}