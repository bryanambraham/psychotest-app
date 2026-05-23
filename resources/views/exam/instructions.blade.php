@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="mb-4">
                        <span class="badge badge-warning mb-2">INSTRUKSI</span>
                        <h3 class="font-weight-bold mb-2">Persiapan Pengerjaan</h3>
                        <p class="text-muted mb-0">Data peserta sudah tersimpan. Saat Anda menekan tombol mulai, timer akan langsung berjalan dan halaman soal akan dibuka.</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded bg-light h-100">
                                <div class="small text-muted">Nama</div>
                                <div class="font-weight-bold">{{ $participant['name'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 border rounded bg-light h-100">
                                <div class="small text-muted">Telepon</div>
                                <div class="font-weight-bold">{{ $participant['phone'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light h-100">
                                <div class="small text-muted">Email</div>
                                <div class="font-weight-bold">{{ $participant['email'] }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0">
                        <ul class="mb-0 pl-3">
                            <li>Pastikan koneksi internet stabil.</li>
                            <li>Siapkan perangkat yang akan digunakan sampai ujian selesai.</li>
                            <li>Timer dimulai tepat setelah halaman soal terbuka.</li>
                        </ul>
                    </div>

                    <form method="POST" action="{{ route('exam.begin', $exam) }}" class="d-flex justify-content-end mt-4">
                        @csrf
                        <button type="submit" class="btn btn-success px-5">Mulai Ujian</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
