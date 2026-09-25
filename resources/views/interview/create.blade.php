@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h4 class="mb-0 font-weight-bold">Undangan Interview</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info small mb-4">
                        <strong>Calon kandidat:</strong> {{ $user->name }}<br>
                        <strong>Email:</strong> {{ $user->email }}
                    </div>

                    <form action="{{ route('users.interview.send', $user->id) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_undangan">Jenis Undangan</label>
                                    <input type="text" id="jenis_undangan" name="jenis_undangan" class="form-control" value="Interview Kerja" placeholder="Contoh: Interview Kerja / Test Psikotes" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pengundang">Nama Pengundang</label>
                                    <input type="text" id="pengundang" name="pengundang" class="form-control" placeholder="Contoh: HRD GrandLucky Group" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="posisi_pengundang">Posisi Pengundang</label>
                                    <input type="text" id="posisi_pengundang" name="posisi_pengundang" class="form-control" placeholder="Contoh: Talent Acquisition" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lokasi_diundang">Lokasi Interview</label>
                                    <input type="text" id="lokasi_diundang" name="lokasi_diundang" class="form-control" placeholder="Contoh: HO" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_diundang">Tanggal</label>
                                    <input type="date" id="tanggal_diundang" name="tanggal_diundang" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="waktu_diundang">Waktu</label>
                                    <input type="time" id="waktu_diundang" name="waktu_diundang" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="persiapan_diundang">Catatan Persiapan</label>
                            <textarea id="persiapan_diundang" name="persiapan_diundang" class="form-control" rows="3" placeholder="Contoh: membawa CV, KTP, dan alat tulis" required></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">Kirim Undangan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection