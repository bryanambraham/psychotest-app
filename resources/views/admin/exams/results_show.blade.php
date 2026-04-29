@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Detail Hasil: {{ $session->user->name }}</h4>
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
                                            @foreach($session->exam->questions as $question)
                                                @php
                                                    // Cari jawaban user untuk soal nomor ini
                                                    $userAnswer = $session->userAnswers->where('question_number', $question->number)->first();
                                                    $data = $userAnswer ? $userAnswer->answers : null;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">{{ $question->number }}</td>
                                                    <td>
                                                        {{ $question->question_text }}
                                                        <div class="small text-muted mt-1">
                                                            <div class="small text-muted mt-2">
                                                                @foreach($question->options as $key => $val)
                                                                    <div class="mb-1">
                                                                        <strong>{{ strtoupper($key) }}.</strong> {{ $val }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if($data)
                                                            {{-- Tampilkan jawaban yang ada --}}
                                                            @if($session->exam->type == 'disc')
                                                                <span class="badge badge-success">Most: {{ $data['most'] ?? '-' }}</span>
                                                                <span class="badge badge-danger">Least: {{ $data['least'] ?? '-' }}</span>
                                                            @else
                                                                <span class="badge badge-primary">Pilihan: {{ $data['selected'] ?? '-' }}</span>
                                                            @endif
                                                        @else
                                                            {{-- Jika waktu habis dan tidak terisi, tampilkan label ini --}}
                                                            <span class="badge badge-secondary">Kosong atau Tidak terisi.</span>
                                                            <!-- <div class="small text-muted italic">Peserta kehabisan waktu pada soal ini.</div> -->
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
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