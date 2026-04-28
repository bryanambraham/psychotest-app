<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // Mengambil daftar ujian yang ditugaskan ke user ini
        // Kita juga sertakan informasi session agar tahu mana yang sudah dikerjakan
        $assignedExams = $user->exams()->with(['sessions' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();

        return view('home', compact('assignedExams'));
    }
}
