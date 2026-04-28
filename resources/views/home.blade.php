@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white font-weight-bold">{{ __('Dashboard Peserta') }}</div>

                <div class="card-body text-center">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h5 class="mb-4">Selamat datang, {{ Auth::user()->name }}!</h5>
                    <p class="text-muted mb-4">Silakan klik tombol di bawah ini untuk memulai sesi ujian psikotes Anda. Pastikan kamera Anda siap.</p>

                    <a href="{{ route('exam.show', ['exam_id' => 1]) }}" class="btn btn-primary btn-lg px-5">
                        Mulai Ujian Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
