@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                Kelola Kunci Jawaban - {{ $exam->name }}
                <a href="{{ route('manage-exams.index') }}" class="btn btn-secondary btn-sm float-right">Kembali</a>
            </h2>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> Ada kesalahan saat menyimpan:
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Kunci Jawaban Soal</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('manage-exams.update-answer-keys', $exam->id) }}" method="POST" id="answerKeysForm">
                        @csrf
                        @method('PUT')

                        @foreach ($editableQuestions as $question)
                            @php
                                $tableData = json_decode($question->question_text, true);
                                $isStructuredTable = is_array($tableData) && isset($tableData['table']);
                            @endphp

                            <div class="card mb-3 border-left-primary" style="border-left: 4px solid #007bff;">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <span class="badge badge-primary">Soal {{ $question->number }}</span>
                                        @if($isStructuredTable)
                                            <span class="badge badge-success">Tabel Angka</span>
                                        @else
                                            <span class="badge badge-info">
                                                @if(is_array($question->options) && !empty($question->options))
                                                    Pilihan Ganda
                                                @else
                                                    Uraian
                                                @endif
                                            </span>
                                        @endif
                                    </h6>
                                </div>
                                <div class="card-body">
                                    {{-- Tampilkan pertanyaan --}}
                                    <div class="mb-3">
                                        <small class="text-muted"><strong>Pertanyaan:</strong></small>
                                        <p class="mb-0 text-dark" style="font-size: 0.95rem;">
                                            {{ $question->kalimat_soal ?? 'Tidak ada pertanyaan yang ditemukan.' }}
                                        </p>
                                    </div>

                                    {{-- Tampilkan opsi jawaban jika ada --}}
                                    @if(is_array($question->options) && !empty($question->options))
                                        <div class="mb-3">
                                            <small class="text-muted"><strong>Opsi Jawaban:</strong></small>
                                            <ul class="mb-0 pl-3" style="font-size: 0.9rem;">
                                                @foreach ($question->options as $key => $value)
                                                    @php
                                                        // Format baru: {"type":"text","value":"Beruang"}
                                                        // Format lama: "Beruang"
                                                        $optText = is_array($value) ? ($value['value'] ?? strtoupper($key)) : $value;
                                                    @endphp
                                                    <li>{{ strtoupper($key) }}. {{ $optText }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <hr class="my-3">

                                    {{-- INPUT KUNCI JAWABAN --}}
                                    @if($isStructuredTable)
                                        {{-- Untuk soal tabel angka: Multiple inputs (Mendatar + Menurun) --}}
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">Kunci Jawaban (Tabel Angka)</label>
                                            <small class="d-block text-muted mb-2">Masukkan jawaban untuk setiap baris dan kolom</small>

                                            <input type="hidden" name="answer_keys[{{ $loop->index }}][question_id]" value="{{ $question->id }}">
                                            <input type="hidden" name="answer_keys[{{ $loop->index }}][is_table]" value="1">

                                            @php
                                                $answerKeyData = [];
                                                if ($question->answer_key) {
                                                    $answerKeyData = json_decode($question->answer_key, true) ?: [];
                                                }

                                                // Tentukan jumlah baris dan kolom dari struktur table
                                                $rowCount = isset($tableData['table']['rows']) ? count($tableData['table']['rows']) : 0;
                                                $colCount = isset($tableData['table']['headers']) ? count($tableData['table']['headers']) : 0;
                                            @endphp

                                            <div class="row">
                                                {{-- Inputs untuk Mendatar (Baris) --}}
                                                @for($i = 1; $i <= $rowCount; $i++)
                                                    <div class="col-md-6 mb-2">
                                                        <input type="text"
                                                            class="form-control form-control-sm"
                                                            name="answer_keys[{{ $loop->index }}][details][Mendatar (Baris {{ $i }})]"
                                                            placeholder="Jawaban Mendatar Baris {{ $i }}"
                                                            value="{{ $answerKeyData['Mendatar (Baris ' . $i . ')'] ?? '' }}"
                                                            style="font-weight: 600;">
                                                    </div>
                                                @endfor

                                                {{-- Inputs untuk Menurun (Kolom) --}}
                                                {{-- Tambahkan - 1 di sini agar kolom ke-9 diabaikan --}}
                                                @for($i = 1; $i <= $colCount - 1; $i++)
                                                    <div class="col-md-6 mb-2">
                                                        <input type="text"
                                                            class="form-control form-control-sm"
                                                            name="answer_keys[{{ $loop->index }}][details][Menurun (Kolom {{ $i }})]"
                                                            placeholder="Jawaban Menurun Kolom {{ $i }}"
                                                            value="{{ $answerKeyData['Menurun (Kolom ' . $i . ')'] ?? '' }}"
                                                            style="font-weight: 600;">
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                    @else
                                        {{-- Untuk soal PG/Uraian: Single input --}}
                                        <div class="form-group">
                                            <label class="font-weight-bold text-primary">Kunci Jawaban</label>
                                            <input type="hidden" name="answer_keys[{{ $loop->index }}][question_id]" value="{{ $question->id }}">
                                            <input type="hidden" name="answer_keys[{{ $loop->index }}][is_table]" value="0">
                                            <input type="text"
                                                name="answer_keys[{{ $loop->index }}][key]"
                                                class="form-control form-control-sm"
                                                value="{{ $question->answer_key ?? '' }}"
                                                placeholder="Masukkan jawaban yang benar (misal: A, B, C, atau angka)"
                                                style="font-weight: 600;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Kunci Jawaban
                            </button>
                            <a href="{{ route('manage-exams.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Informasi</h5>
                </div>
                <div class="card-body">
                    <ul>
                        {{-- <li><strong>Tipe Ujian:</strong> {{ strtoupper($exam->type) }}</li> --}}
                        <li><strong>Jumlah Soal:</strong> {{ $editableQuestions->count() }}</li>
                        <li><strong>Durasi:</strong> {{ $exam->duration_minutes }} menit</li>
                        <li><strong>Catatan:</strong>
                            <ul>
                                <li>Untuk soal <strong>PG</strong>: Isikan jawaban benar (A, B, C, D, dll)</li>
                                <li>Untuk soal <strong>Angka</strong>: Isikan semua jawaban mendatar dan menurun dengan format seperti yang diterima peserta</li>
                                <li>Untuk soal <strong>Uraian/Kasus</strong>: Bisa dikosongkan atau diisi dengan kriteria jawaban (tidak berpengaruh pada scoring)</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
