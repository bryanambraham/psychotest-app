@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold">Kelola Ujian: {{ $exam->name }}</div>
                <div class="card-body">
                    <form action="{{ route('manage-exams.update', $exam->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Nama Ujian</label>
                            <input type="text" name="name" value="{{ $exam->name }}" class="form-control" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Durasi (Menit)</label>
                                <input type="number" name="duration_minutes" value="{{ $exam->duration_minutes }}" class="form-control" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Tipe</label>
                                <select name="type" class="form-control">
                                    <option value="disc" {{ $exam->type == 'disc' ? 'selected' : '' }}>DISC</option>
                                    <option value="mbti" {{ $exam->type == 'mbti' ? 'selected' : '' }}>MBTI</option>
                                    </select>
                            </div>
                        </div>

                        <hr>

                        <h5 class="font-weight-bold">Penugasan Peserta</h5>
                        <p class="text-muted small">Centang user yang diizinkan mengerjakan ujian ini:</p>
                        <div class="user-list mb-3" style="max-height: 250px; overflow-y: auto; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px;">
                            @foreach($users as $user)
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                           class="custom-control-input" id="user-{{ $user->id }}"
                                           {{ in_array($user->id, $assignedUserIds) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="user-{{ $user->id }}">
                                        {{ $user->name }} <span class="text-muted">({{ $user->email }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('manage-exams.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
