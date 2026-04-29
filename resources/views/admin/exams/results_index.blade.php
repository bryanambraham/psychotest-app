@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Laporan Hasil Ujian Peserta</h3>

    <div class="card shadow-sm border-0">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Nama Peserta</th>
                    <th>Ujian</th>
                    <th>Status</th>
                    <th>Waktu Mulai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr>
                    <td>{{ $session->user->name }}</td>
                    <td>{{ $session->exam->name }}</td>
                    <td>
                        <span class="badge {{ $session->status == 'completed' ? 'badge-success' : 'badge-warning' }}">
                            {{ strtoupper($session->status) }}
                        </span>
                    </td>
                    <td>{{ $session->created_at->format('d M Y, H:i') }}</td>
                    <td>
                        <a href="{{ route('manage-exams.results.show', $session->id) }}" class="btn btn-sm btn-info text-white">
                            Lihat Detail & Foto
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection