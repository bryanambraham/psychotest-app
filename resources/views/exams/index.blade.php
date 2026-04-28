@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Daftar Materi Psikotes</h3>
        <a href="{{ route('manage-exams.create') }}" class="btn btn-primary">+ Tambah Ujian</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Nama Ujian</th>
                    <th>Tipe</th>
                    <th>Durasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exams as $exam)
                <tr>
                    <td>{{ $exam->name }}</td>
                    <td><span class="badge badge-info">{{ strtoupper($exam->type) }}</span></td>
                    <td>{{ $exam->duration_minutes }} Menit</td>
                    <td>
                        <a href="{{ route('exam.show', $exam->id) }}" class="btn btn-sm btn-outline-success">Lihat Preview</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
