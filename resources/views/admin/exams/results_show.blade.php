@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Detail Hasil: {{ $session->user->name }}</h4>
            <div class="text-muted small">{{ $session->user->email }} | {{ $session->user->phone ?? '-' }}</div>
        </div>
        <a href="{{ route('manage-exams.results') }}" class="btn btn-secondary btn-sm">Kembali</a>
    </div>

    <div class="row">
        <div class="col-md-4">
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
                                            @if($session->exam->type == 'akuntansi_kasus')
                                                {{-- ======================================================== --}}
                                                {{-- TAMPILAN KHUSUS UNTUK DOWNLOAD MULTIPLE FILE JAWABAN     --}}
                                                {{-- ======================================================== --}}
                                                @php
                                                    // Decode data JSON array file dari tabel exam_sessions
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
