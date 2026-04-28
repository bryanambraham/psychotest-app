<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exam;

class ExamManagementController extends Controller
{
    // Menampilkan daftar semua ujian
    public function index()
    {
        $exams = Exam::all();
        return view('exams.index', compact('exams'));
    }

    // Menampilkan form tambah ujian
    public function create()
    {
        return view('exams.create');
    }

    // Menyimpan data ujian baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:mbti,disc,epps,papi,big_five',
            'duration_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
            // File soal PDF (Kita siapkan fieldnya untuk nanti dikirim ke Python)
            'question_file' => 'nullable|mimes:pdf|max:2048'
        ]);

        $exam = Exam::create([
            'name' => $request->name,
            'type' => $request->type,
            'duration_minutes' => $request->duration_minutes,
            'description' => $request->description,
        ]);

        // Catatan: Logika pengiriman PDF ke Python akan diletakkan di sini nanti

        return redirect()->route('manage-exams.index')->with('success', 'Ujian berhasil ditambahkan.');
    }
}
