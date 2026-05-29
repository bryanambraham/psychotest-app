<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // <-- PASTIKAN BARIS INI DITAMBAHKAN

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // Fungsi yang kemarin kita tambahkan
    public function username()
    {
        return 'login';
    }

    // TAMBAHKAN FUNGSI BARU INI DI SINI
    protected function credentials(Request $request)
    {
        $loginInput = $request->input('login');

        // Cek apakah yang diketik user memiliki format email (ada @ dan .com)
        // Jika iya, cari ke kolom 'email'. Jika tidak, cari ke kolom 'name'.
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        return [
            $field => $loginInput,
            'password' => $request->input('password')
        ];
    }
}