@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white font-weight-bold d-flex justify-content-between align-items-center">
                    <span>Kelola Peserta Ujian: {{ $exam->name }}</span>
                    <a href="{{ route('manage-exams.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
                </div>

                <div class="card-body">
                    <!-- 1. FORM PENCARIAN & PAGINATION NUMBER -->
                    <form action="{{ route('manage-exams.edit', $exam->id) }}" method="GET" id="filter-form" class="mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama atau email...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">Cari</button>
                                        <a href="{{ route('manage-exams.edit', $exam->id) }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select name="number" class="form-control" onchange="document.getElementById('filter-form').submit()">
                                    <option value="20" {{ $number_paginate == 20 ? 'selected' : '' }}>20 baris</option>
                                    <option value="40" {{ $number_paginate == 40 ? 'selected' : '' }}>40 baris</option>
                                    <option value="60" {{ $number_paginate == 60 ? 'selected' : '' }}>60 baris</option>
                                    <option value="80" {{ $number_paginate == 80 ? 'selected' : '' }}>80 baris</option>
                                    <option value="100" {{ $number_paginate == 100 ? 'selected' : '' }}>100 baris</option>
                                </select>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <!-- 2. FORM UTAMA UNTUK SIMPAN DATA -->
                    <form action="{{ route('manage-exams.update', $exam->id) }}" method="POST" id="main-form">
                        @csrf
                        @method('PUT')
                        <div id="examHiddenFields">
                            <input type="hidden" name="name" value="{{ $exam->name }}">
                            <input type="hidden" name="type" value="{{ $exam->type }}">
                            <input type="hidden" name="duration_minutes" value="{{ $exam->duration_minutes }}">
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                            <div class="mb-3 mb-md-0">
                                <h5 class="mb-1">Kelola peserta & pengaturan ujian</h5>
                                <p class="mb-0 text-muted small">Centang peserta untuk menambahkan atau menghapus dari ujian. Gunakan tombol di samping untuk mengubah detail ujian.</p>
                            </div>
                            <button id="examFormButton" type="button" class="btn btn-outline-primary btn-sm" aria-expanded="false">
                                <i class="fas fa-edit mr-1"></i> Edit Ujian
                            </button>
                        </div>

                        <div id="examForm" class="card card-body mb-4 d-none border-primary">
                            <h5 class="card-title">Form Edit Ujian</h5>
                            <p class="text-muted small mb-3">Ubah nama, tipe, dan durasi ujian sesuai kebutuhan. Simpan perubahan setelah selesai.</p>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="name" class="form-label">Nama Ujian</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $exam->name) }}"
                                        class="form-control" placeholder="Contoh: Ujian Tengah Semester" disabled required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="type" class="form-label">Tipe Ujian</label>
                                    <input type="text" name="type" id="type" value="{{ old('type', $exam->type) }}"
                                        class="form-control" placeholder="Contoh: Pilihan Ganda" disabled required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="duration_minutes" class="form-label">Durasi (Menit)</label>
                                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}"
                                        class="form-control" placeholder="60" min="1" disabled required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" id="cancelButton" class="btn btn-secondary btn-sm">Batal</button>
                                <button type="submit" class="btn btn-success btn-sm ml-2">Simpan Perubahan Ujian</button>
                            </div>
                        </div>

                        <div class="table-responsive" style="max-height: 520px; overflow-y: auto;">
                            <table class="table table-hover table-sm align-middle border">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 50px;">
                                            <input type="checkbox" id="check-all">
                                        </th>
                                        <th>Nama User</th>
                                        <th>Email</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td class="text-center">
                                                <!-- Input tersembunyi untuk menandai user ini sedang ditampilkan di layar -->
                                                <input type="hidden" name="visible_user_ids[]" value="{{ $user->id }}">

                                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                                    class="user-checkbox"
                                                    {{ in_array($user->id, $assignedUserIds) ? 'checked' : '' }}>
                                            </td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td class="text-center">
                                                @if(in_array($user->id, $assignedUserIds))
                                                    <span class="badge badge-success">Terdaftar</span>
                                                @else
                                                    <span class="badge badge-light">Belum</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">User tidak ditemukan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Links -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links() }}
                        </div>

                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success px-5 font-weight-bold">Simpan Peserta Terpilih</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fitur Select All
    document.getElementById('check-all').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    const examFormButton = document.getElementById('examFormButton');
    const cancelButton = document.getElementById('cancelButton');
    const examForm = document.getElementById('examForm');
    const examHiddenFields = document.querySelectorAll('#examHiddenFields input');
    const examVisibleFields = document.querySelectorAll('#examForm input');

    function setExamFormState(isOpen) {
        examForm.classList.toggle('d-none', !isOpen);
        examFormButton.setAttribute('aria-expanded', String(isOpen));

        examHiddenFields.forEach(input => input.disabled = isOpen);
        examVisibleFields.forEach(input => input.disabled = !isOpen);
    }

    examFormButton.addEventListener('click', () => {
        const isOpen = examForm.classList.contains('d-none');
        setExamFormState(isOpen);
    });

    cancelButton.addEventListener('click', () => {
        setExamFormState(false);
    });
</script>
@endsection
