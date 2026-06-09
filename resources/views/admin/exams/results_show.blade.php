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
                        @if($session->score !== null)
                            <p>
                                <strong>Nilai Akhir:</strong> 
                                <span class="badge badge-lg" style="font-size: 1.2em; padding: 0.5em 0.8em;
                                    @if($session->score >= 80)
                                        background-color: #28a745;
                                    @elseif($session->score >= 60)
                                        background-color: #ffc107;
                                        color: #000;
                                    @else
                                        background-color: #dc3545;
                                    @endif
                                ">
                                    {{ number_format($session->score, 2) }} / 100
                                </span>
                            </p>
                        @endif
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
                        <p><strong>Nama:</strong> {{ $verifyUser->name ?? $session->user->name }}</p>
                        <p><strong>Email:</strong> {{ $verifyUser->email ?? $session->user->email }}</p>
                        <p><strong>Telepon:</strong> {{ $verifyUser->phone ?? $session->user->phone }}</p>
                        <p><strong>Posisi:</strong> {{ $verifyUser->position ?? $session->user->position }}</p>
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
                                                <th>Kunci Jawaban</th>
                                                <th>Jawaban Peserta</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($session->exam->type == 'soal_kasus')
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

                                            @elseif($session->exam->type == 'uraian' || $session->exam->type == 'tes_kraeplin')
                                                {{-- ======================================================== --}}
                                                {{-- TAMPILAN KHUSUS UNTUK SOAL URAIAN (ESSAY)                --}}
                                                {{-- ======================================================== --}}
                                                @foreach($session->exam->questions as $question)
                                                    @php
                                                        $userAnswer = $session->userAnswers->where('question_number', $question->number)->first();
                                                        $answerText = $userAnswer ? ($userAnswer->answers['answer_text'] ?? '') : '';
                                                        $answerKeyValue = $question->answer_key ?? null;
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center align-middle font-weight-bold">{{ $question->number }}</td>
                                                        <td class="align-middle">
                                                            <div class="text-dark" style="white-space: pre-wrap; line-height: 1.5;">{{ $question->question_text }}</div>
                                                        </td>
                                                        <td class="align-middle">
                                                            @if($answerKeyValue)
                                                                <div style="font-size: 0.85rem; color: #666;">{{ $answerKeyValue }}</div>
                                                            @else
                                                                <span class="text-muted small">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle">
                                                            @if($answerText)
                                                                <div class="p-2 bg-light rounded" style="border-left: 3px solid #17a2b8; white-space: pre-wrap; line-height: 1.5; max-height: 150px; overflow-y: auto; font-size: 0.9rem;">
                                                                    {{ $answerText }}
                                                                </div>
                                                            @else
                                                                <span class="badge badge-secondary">❌ Tidak ada jawaban</span>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-muted small">Manual review</span>
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
                                                            $details = $data['details'] ?? [];
                                                            
                                                            // Decode answer key
                                                            $answerKeyValue = $question->answer_key ?? null;
                                                            $answerKeyData = [];
                                                            if ($answerKeyValue) {
                                                                $answerKeyData = @json_decode($answerKeyValue, true) ?: [];
                                                            }
                                                        @endphp
                                                        <tr>
                                                            <td class="text-center align-middle font-weight-bold">{{ $question->number }}</td>
                                                            <td class="align-middle">
                                                                <span class="font-weight-bold text-dark d-block">🔢 Soal Penjumlahan Tabel Angka</span>
                                                                <small class="text-muted">Peserta diminta menghitung tabel angka secara mendatar & menurun.</small>
                                                            </td>
                                                            <td class="align-middle">
                                                                {{-- Display kunci jawaban --}}
                                                                @if(!empty($answerKeyData))
                                                                    <div class="row" style="margin: 0 -5px; font-size: 0.85rem;">
                                                                        @foreach($answerKeyData as $label => $val)
                                                                            <div class="col-12 p-1">
                                                                                <small class="text-muted">{{ $label }}:</small>
                                                                                <div class="badge badge-warning">{{ $val }}</div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted small">Belum diatur</span>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle">
                                                                {{-- Display jawaban peserta --}}
                                                                @if(!empty($details))
                                                                    <div class="row" style="margin: 0 -5px; font-size: 0.85rem;">
                                                                        @foreach($details as $label => $val)
                                                                            <div class="col-12 p-1">
                                                                                <small class="text-muted">{{ $label }}:</small>
                                                                                <div class="badge badge-primary">{{ $val }}</div>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <span class="badge badge-secondary">❌ Kosong</span>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle text-center">
                                                                @if(!empty($answerKeyData) && !empty($details))
                                                                    @php
                                                                        $correctItemCount = 0;
                                                                        $totalItemCount = count($answerKeyData);

                                                                        foreach ($answerKeyData as $label => $expectedValue) {
                                                                            $actualValue = $details[$label] ?? null;
                                                                            if ($actualValue) {
                                                                                $normalizedActual = str_replace(['.', ','], '', strtolower(trim($actualValue)));
                                                                                $normalizedExpected = str_replace(['.', ','], '', strtolower(trim($expectedValue)));
                                                                                if ($normalizedActual === $normalizedExpected) {
                                                                                    $correctItemCount++;
                                                                                }
                                                                            }
                                                                        }
                                                                    @endphp
                                                                    
                                                                    {{-- Tampilkan detail Benar dan Salah --}}
                                                                    <div class="d-flex flex-column align-items-center" style="gap: 5px;">
                                                                        <span class="badge badge-success" style="font-size: 0.9rem;">✅ {{ $correctItemCount }} Benar</span>
                                                                        @if($totalItemCount - $correctItemCount > 0)
                                                                            <span class="badge badge-danger" style="font-size: 0.9rem;">❌ {{ $totalItemCount - $correctItemCount }} Salah</span>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted small">-</span>
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
                                                        $answerKeyValue = $question->answer_key ?? null;
                                                        
                                                        // Tentukan apakah jawaban benar (untuk soal yang punya answer key)
                                                        $isCorrect = false;
                                                        $userAnswerDisplay = '-';
                                                        
                                                        if ($data && $answerKeyValue) {
                                                            if ($session->exam->type == 'disc') {
                                                                $userAnswerDisplay = ($data['most'] ?? '-') . ' / ' . ($data['least'] ?? '-');
                                                            } else {
                                                                $userAnswerDisplay = $data['selected'] ?? '-';
                                                                // Cek kecocokan
                                                                if ($userAnswerDisplay && strtolower(trim($userAnswerDisplay)) === strtolower(trim($answerKeyValue))) {
                                                                    $isCorrect = true;
                                                                }
                                                            }
                                                        } elseif ($data) {
                                                            if ($session->exam->type == 'disc') {
                                                                $userAnswerDisplay = ($data['most'] ?? '-') . ' / ' . ($data['least'] ?? '-');
                                                            } else {
                                                                $userAnswerDisplay = $data['selected'] ?? '-';
                                                            }
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center">{{ $question->number }}</td>
                                                        <td>
                                                            {{ $question->question_text }}
                                                            <div class="small text-muted mt-2">
                                                                @foreach($question->options as $key => $val)
                                                                    <div class="mb-1">
                                                                    @php
                                                                        $optText = is_array($val) ? ($val['value'] ?? strtoupper($key)) : $val;
                                                                    @endphp
                                                                    <li>{{ strtoupper($key) }}. {{ $optText }}</li>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                        <td class="align-middle">
                                                            @if($answerKeyValue)
                                                                @if($session->exam->type == 'disc')
                                                                    <span class="text-muted" style="font-size: 0.9rem;">-</span>
                                                                @else
                                                                    <span class="badge badge-warning font-weight-bold" style="font-size: 1rem; padding: 0.5em 0.8em;">{{ $answerKeyValue }}</span>
                                                                @endif
                                                            @else
                                                                <span class="text-muted small">Belum diatur</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($data)
                                                                @if($session->exam->type == 'disc')
                                                                    <span class="badge badge-success">Most: {{ $data['most'] ?? '-' }}</span>
                                                                    <span class="badge badge-danger">Least: {{ $data['least'] ?? '-' }}</span>
                                                                @else
                                                                    <span class="badge badge-primary">{{ $data['selected'] ?? '-' }}</span>
                                                                @endif
                                                            @else
                                                                <span class="badge badge-secondary">❌ Kosong</span>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle">
                                                            @if($answerKeyValue && $session->exam->type != 'disc')
                                                                @if($isCorrect)
                                                                    <span class="badge badge-success" style="font-size: 1rem; padding: 0.5em 0.8em;">✅ Benar</span>
                                                                @else
                                                                    <span class="badge badge-danger" style="font-size: 1rem; padding: 0.5em 0.8em;">❌ Salah</span>
                                                                @endif
                                                            @else
                                                                <span class="text-muted small">-</span>
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
