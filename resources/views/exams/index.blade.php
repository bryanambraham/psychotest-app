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

    <div class="card shadow-sm border-0">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Nama Ujian</th>
                    <th>Tipe</th>
                    <th>Durasi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exams as $exam)
                <tr>
                    <td class="align-middle font-weight-bold">{{ $exam->name }}</td>
                    <td class="align-middle"><span class="badge badge-info">{{ strtoupper($exam->type) }}</span></td>
                    <td class="align-middle">{{ $exam->duration_minutes }} Menit</td>
                    <td class="text-center">
                        <a href="{{ route('exam.show', $exam->id) }}" class="btn btn-sm btn-outline-success">Preview</a>

                        <a href="{{ route('manage-exams.edit', $exam->id) }}" class="btn btn-sm btn-primary">Kelola</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
