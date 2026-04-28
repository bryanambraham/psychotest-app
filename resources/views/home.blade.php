@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h3 class="mb-4">Daftar Ujian Anda</h3>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row">
                @forelse($assignedExams as $exam)
                    @php
                        $session = $exam->sessions->first();
                        $status = $session ? $session->status : 'belum_mulai';
                        $remainingText = null;

                        // Logika hitung sisa waktu jika sedang berlangsung
                        if ($status == 'in_progress' && $session) {
                            $endTime = \Carbon\Carbon::parse($session->start_time)->addMinutes($exam->duration_minutes);
                            $diffInSeconds = \Carbon\Carbon::now()->diffInSeconds($endTime, false);

                            if ($diffInSeconds > 0) {
                                $mins = floor($diffInSeconds / 60);
                                $secs = $diffInSeconds % 60;
                                $remainingText = "($mins Menit lagi)";
                            } else {
                                $status = 'timeout'; // Secara visual dianggap habis
                            }
                        }
                    @endphp

                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="font-weight-bold mb-1">{{ $exam->name }}</h5>
                                        <span class="badge badge-pill badge-info">{{ strtoupper($exam->type) }}</span>
                                    </div>
                                    <div class="text-muted small text-right">
                                        <i class="far fa-clock"></i> {{ $exam->duration_minutes }} Menit
                                    </div>
                                </div>

                                <p class="text-muted mt-3 small">
                                    {{ $exam->description ?? 'Tidak ada deskripsi untuk ujian ini.' }}
                                </p>

                                <hr>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if($status == 'completed')
                                            <span class="text-success small font-weight-bold">✅ Selesai</span>
                                        @elseif($status == 'in_progress')
                                            <div>
                                                <span class="text-warning small font-weight-bold">⏳ Sedang Berlangsung</span>
                                                <br>
                                                <small class="text-danger font-weight-bold">{{ $remainingText }}</small>
                                            </div>
                                        @elseif($status == 'timeout')
                                            <span class="text-danger small font-weight-bold">⏰ Waktu Habis</span>
                                        @else
                                            <span class="text-muted small">Belum dikerjakan</span>
                                        @endif
                                    </div>

                                    @if($status == 'completed' || $status == 'timeout')
                                        <button class="btn btn-secondary btn-sm" disabled>Sudah Selesai</button>
                                    @else
                                        <a href="{{ route('exam.show', $exam->id) }}" class="btn btn-primary btn-sm px-4 shadow-sm">
                                            {{ $status == 'in_progress' ? 'Lanjutkan' : 'Mulai Ujian' }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <div class="alert alert-light border shadow-sm">
                            Belum ada ujian yang ditugaskan untuk Anda saat ini.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
