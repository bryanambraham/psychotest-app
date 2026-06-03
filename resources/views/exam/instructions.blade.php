@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-11 col-lg-8">

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-3 p-md-5">

                    {{-- Header --}}
                    <div class="mb-4">
                        <span class="badge badge-warning mb-2">INSTRUKSI</span>
                        <h3 class="font-weight-bold mb-2 fs-responsive">Persiapan Pengerjaan</h3>
                        <p class="text-muted mb-0 small">Data peserta sudah tersimpan. Saat Anda menekan tombol mulai, timer akan langsung berjalan dan halaman soal akan dibuka.</p>
                    </div>

                    {{-- Participant Info Cards --}}
                    <div class="row mb-4">
                        <div class="col-12 col-sm-4 mb-2 mb-sm-0">
                            <div class="p-3 border rounded bg-light h-100">
                                <div class="small text-muted">Nama</div>
                                <div class="font-weight-bold text-break">{{ $participant['name'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4 mb-2 mb-sm-0">
                            <div class="p-3 border rounded bg-light h-100">
                                <div class="small text-muted">Telepon</div>
                                <div class="font-weight-bold text-break">{{ $participant['phone'] }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-sm-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <div class="small text-muted">Email</div>
                                <div class="font-weight-bold text-break" style="font-size: 0.85rem;">{{ $participant['email'] }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Alert Tips --}}
                    <div class="alert alert-info border-0">
                        <ul class="mb-0 pl-3">
                            <li>Pastikan koneksi internet stabil.</li>
                            <li>Siapkan perangkat yang akan digunakan sampai ujian selesai.</li>
                            <li>Timer dimulai tepat setelah halaman soal terbuka.</li>
                        </ul>
                    </div>

                    {{-- Submit --}}
                    <form method="POST" action="{{ route('exam.begin', $exam) }}"
                          class="d-flex justify-content-end mt-4">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block-xs px-5">
                            Mulai Ujian
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fs-responsive { font-size: clamp(1.1rem, 4vw, 1.575rem); }

    @media (max-width: 575.98px) {
        .btn-block-xs { width: 100%; }
    }
</style>
@endsection