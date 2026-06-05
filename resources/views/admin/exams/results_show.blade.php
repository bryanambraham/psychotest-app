@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Detail Hasil: {{ $session->user->name }}</h4>
            <div class="text-muted small">{{ $session->user->email }} | {{ $session->user->phone ?? '-' }} | {{ $session->user->position ?? '-' }}</div>
        </div>
        <div class="d-flex" style="gap: 10px;">
            {{-- TOMBOL EXPORT BARU --}}
            <a href="{{ route('manage-exams.results.export', $session->id) }}" class="btn btn-success btn-sm shadow-sm font-weight-bold">
                📥 Export ke Excel
            </a>
            
            <a href="{{ route('manage-exams.results') }}" class="btn btn-secondary btn-sm shadow-sm">Kembali</a>
        </div>
    </div>

    <div class="row">
        <div class="flex flex-col w-full gap-4">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-primary text-white">Info Ujian</div>
                    <div class="card-body">
                        <p><strong>Ujian:</strong> {{ $session->exam->name }}</p>
                        <p><strong>Tipe:</strong> {{ strtoupper($session->exam->type) }}</p>
                        <p><strong>Status:</strong> {{ strtoupper($session->status) }}</p>
                        <p><strong>Waktu Mulai:</strong> {{ $session->created_at }}</p>
                        <p><strong>Waktu Selesai:</strong> {{ $session->end_time ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-primary text-white">Info Peserta <span class="font-weight-bold">(DIISI HRD)</span></div>
                    <div class="card-body">
                        <p><strong>Nama:</strong> {{ $session->user->name }}</p>
                        <p><strong>Email:</strong> {{ $session->user->email }}</p>
                        <p><strong>Telepon:</strong> {{ $session->user->phone ?? '-' }}</p>
                        <p><strong>Posisi:</strong> {{ $session->user->position ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-primary text-white">Info Verifikasi Peserta <span class="font-weight-bold">(DIISI USER)</span></div>
                    <div class="card-body">
                        <p><strong>Nama:</strong> {{ $verifyUser->name }}</p>
                        <p><strong>Email:</strong> {{ $verifyUser->email }}</p>
                        <p><strong>Telepon:</strong> {{ $verifyUser->phone ?? '-' }}</p>
                        <p><strong>Posisi:</strong> {{ $verifyUser->position ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between">
                    <span>📸 Bukti Kamera (Random Snapshots)</span>
                    <span class="badge badge-light">{{ count($session->proctoringLogs) }} Foto Terdeteksi</span>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        @forelse($session->proctoringLogs as $photo)
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm">
                                    {{-- Pastikan folder storage sudah di-link: php artisan storage:link --}}
                                    <img src="{{ asset('proctoring/' . $photo->image_path) }}" class="card-img-top rounded" alt="Evidence">
                                    <div class="card-footer p-1 text-center bg-white border-0">
                                        <small class="text-muted">{{ $photo->created_at->format('H:i:s') }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <p class="text-muted italic">Tidak ada foto bukti yang terekam.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white font-weight-bold">
                                    📝 Detail Jawaban Peserta
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-bordered mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 50px;">No</th>
                                                <th>Pertanyaan / Pernyataan</th>
                                                <th>Jawaban Peserta</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($session->exam->type == 'kasus_akuntansi')
                                                {{-- ======================================================== --}}
                                                {{-- TAMPILAN KHUSUS UNTUK DOWNLOAD MULTIPLE FILE JAWABAN     --}}
                                                {{-- ======================================================== --}}
                                                @php
                                                    $uploadedFiles = json_decode($session->answer_file, true) ?: [];
                                                @endphp
                                                <tr>
                                                    <td class="text-center align-middle">1</td>
                                                    <td class="align-middle">
                                                        <span class="font-weight-bold text-dark d-block">📄 Pertanyaan bisa diliat dari File Soal</span>
                                                        <small class="text-muted">Seluruh instruksi siklus akuntansi dikerjakan peserta melalui lembar kerja eksternal.</small>
                                                    </td>
                                                    <td class="align-middle">
                                                        @if(!empty($uploadedFiles))
                                                            <div class="font-weight-bold small text-secondary mb-2">📥 Klik untuk mengunduh jawaban peserta:</div>
                                                            <div class="d-flex flex-column" style="gap: 8px;">
                                                                @foreach($uploadedFiles as $file)
                                                                    <a href="{{ asset($file['path']) }}" target="_blank"
                                                                    class="btn btn-sm btn-white border text-left d-inline-flex align-items-center shadow-sm rounded p-2"
                                                                    style="gap: 10px; width: max-content; color: #4e73df; font-size: 0.9rem;">
                                                                        <span style="font-size: 1.1rem;">📊</span>
                                                                        <span class="font-weight-bold" style="text-decoration: underline;">{{ $file['name'] }}</span>
                                                                        <i class="fas fa-download text-muted ml-2"></i>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <span class="badge badge-danger p-2">❌ Kosong / Peserta tidak mengunggah file apa pun.</span>
                                                        @endif
                                                    </td>
                                                </tr>

                                            @elseif($session->exam->type == 'uraian')
                                                {{-- ======================================================== --}}
                                                {{-- TAMPILAN KHUSUS UNTUK SOAL URAIAN (ESSAY)                --}}
                                                {{-- ======================================================== --}}
                                                @foreach($session->exam->questions as $question)
                                                    @php
                                                        $userAnswer = $session->userAnswers->where('question_number', $question->number)->first();
                                                        $answerText = $userAnswer ? ($userAnswer->answers['answer_text'] ?? '') : '';
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center align-middle font-weight-bold">{{ $question->number }}</td>
                                                        <td class="align-middle">
                                                            <div class="text-dark" style="white-space: pre-wrap; line-height: 1.5;">{{ $question->question_text }}</div>
                                                        </td>
                                                        <td class="align-middle">
                                                            @if($answerText)
                                                                <div class="p-2 bg-light rounded" style="border-left: 3px solid #17a2b8; white-space: pre-wrap; line-height: 1.5; max-height: 150px; overflow-y: auto;">
                                                                    {{ $answerText }}
                                                                </div>
                                                            @else
                                                                <span class="badge badge-secondary">❌ Tidak ada jawaban</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            @elseif($session->exam->type == 'angka')
                                                    {{-- ======================================================== --}}
                                                    {{-- TAMPILAN KHUSUS UNTUK SOAL TABEL ANGKA                   --}}
                                                    {{-- ======================================================== --}}
                                                    @foreach($session->exam->questions as $question)
                                                        @php
                                                            $userAnswer = $session->userAnswers->where('question_number', $question->number)->first();
                                                            // Ambil data JSON jawaban peserta
                                                            $data = $userAnswer ? $userAnswer->answers : [];
                                                            $answerText = $data['answer_text'] ?? '';
                                                            $details = $data['details'] ?? [];
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center align-middle font-weight-bold">{{ $question->number }}</td>
                                                            <td class="align-middle">
                                                                <span class="font-weight-bold text-dark d-block">🔢 Soal Penjumlahan Tabel Angka</span>
                                                                <small class="text-muted">Peserta diminta menghitung tabel angka secara mendatar & menurun.</small>
                                                            </td>
                                                            <td class="align-middle">
                                                                {{-- Cek apakah ada data struktur array (Versi Baru) --}}
                                                                @if(!empty($details))
                                                                    <div class="row" style="margin: 0 -5px;">
                                                                        @foreach($details as $label => $val)
                                                                            @php
                                                                                // Bersihkan inputan dari spasi berlebih
                                                                                $cleanVal = trim($val);
                                                                                // Jika isinya murni angka, format menggunakan titik (Ide Sebelumnya)
                                                                                $displayVal = is_numeric($cleanVal) ? number_format($cleanVal, 0, ',', '.') : $val;
                                                                            @endphp
                                                                            
                                                                            {{-- PERBAIKAN 1: col-12 agar di HP menjadi 1 baris penuh, tidak memaksakan dibagi 2 --}}
                                                                            <div class="col-12 col-md-6 p-1">
                                                                                {{-- PERBAIKAN 2: flex-wrap agar kotak bisa turun ke bawah jika tidak muat --}}
                                                                                <div class="border rounded p-3 bg-white shadow-sm d-flex flex-wrap justify-content-between align-items-center" style="gap: 10px;">
                                                                                    <small class="text-secondary font-weight-bold">{{ $label }}</small>
                                                                                    
                                                                                    {{-- PERBAIKAN 3: white-space: normal & word-break agar angka super panjang melipat ke bawah --}}
                                                                                    <span class="badge badge-success text-right" style="font-size: 1.05rem; letter-spacing: 0.5px; padding: 0.5em 0.8em; white-space: normal; word-break: break-word; max-width: 100%;">
                                                                                        {{ $displayVal }}
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    
                                                                {{-- Fallback jika ada peserta lama yang menjawab pakai format lama (1, 2) --}}
                                                                @elseif($answerText)
                                                                    <div class="p-2 bg-light rounded font-weight-bold text-success" style="border-left: 3px solid #28a745; font-size: 1.1rem; letter-spacing: 1px; word-break: break-word;">
                                                                        {{ $answerText }}
                                                                    </div>
                                                                @else
                                                                    <span class="badge badge-secondary">❌ Tidak ada jawaban</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                            @else
                                                {{-- ======================================================== --}}
                                                {{-- TAMPILAN DEFAULT UNTUK UJIAN DISC, MBTI, VAK, DLL        --}}
                                                {{-- ======================================================== --}}
                                                @foreach($session->exam->questions as $question)
                                                    @php
                                                        $userAnswer = $session->userAnswers->where('question_number', $question->number)->first();
                                                        $data = $userAnswer ? $userAnswer->answers : null;
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center">{{ $question->number }}</td>
                                                        <td>
                                                            {{ $question->question_text }}
                                                            <div class="small text-muted mt-2">
                                                                @foreach($question->options as $key => $val)
                                                                    <div class="mb-1">
                                                                        <strong>{{ strtoupper($key) }}.</strong> {{ $val }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($data)
                                                                @if($session->exam->type == 'disc')
                                                                    <span class="badge badge-success">Most: {{ $data['most'] ?? '-' }}</span>
                                                                    <span class="badge badge-danger">Least: {{ $data['least'] ?? '-' }}</span>
                                                                @else
                                                                    <span class="badge badge-primary">Pilihan: {{ $data['selected'] ?? '-' }}</span>
                                                                @endif
                                                            @else
                                                                <span class="badge badge-secondary">Kosong atau Tidak terisi.</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
