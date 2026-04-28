@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header font-weight-bold">Tambah Materi Ujian Baru</div>
                <div class="card-body">
                    <form action="{{ route('manage-exams.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Nama Ujian</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Tes DISC Karyawan" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Tipe Psikotes</label>
                                <select name="type" class="form-control">
                                    <option value="disc">DISC</option>
                                    <option value="mbti">MBTI</option>
                                    <option value="epps">EPPS</option>
                                    <option value="big_five">Big Five</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Durasi (Menit)</label>
                                <input type="number" name="duration_minutes" class="form-control" value="30" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Upload PDF Soal (Opsional)</label>
                            <input type="file" name="question_file" class="form-control-file">
                            <small class="text-muted">File ini nantinya akan diproses otomatis menjadi soal.</small>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('manage-exams.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-success">Simpan & Proses Soal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
