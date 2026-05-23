@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-5 text-center">
                    <div class="h2 mb-3 text-success font-weight-bold">Selesai</div>
                    <h3 class="font-weight-bold mb-3">Ujian telah dikumpulkan</h3>
                    <p class="text-muted mb-0">Terima kasih, {{ $session->user->name }}. Jawaban Anda untuk {{ $exam->name }} sudah tersimpan.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
