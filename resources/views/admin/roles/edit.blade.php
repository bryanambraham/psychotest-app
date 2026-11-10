@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            {{-- ===== HEADER ===== --}}
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="font-weight-bold mb-1">Edit Hak Akses Role: {{ $role->display_name }}</h4>
                    <p class="text-muted small mb-0">Ubah hak izin aksi (CRUD tombol) untuk role ini.</p>
                </div>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary btn-sm font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>

            @if($role->name === 'admin')
                <div class="alert alert-info shadow-sm mb-4" role="alert">
                    <i class="fas fa-info-circle mr-2"></i> <strong>Informasi Administrator:</strong> Role <code>admin</code> secara default memiliki izin penuh (Superadmin) ke seluruh aksi dan tombol sistem.
                </div>
            @endif

            {{-- ===== ERROR VALIDATION ===== --}}
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('roles.update', $role->id) }}" method="POST" id="roleEditForm">
                @csrf
                @method('PUT')

                {{-- CARD INFORMASI ROLE --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white font-weight-bold py-3 border-bottom">
                        <i class="fas fa-id-badge text-primary mr-2"></i> Informasi Role
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Nama Tampilan Role <span class="text-danger">*</span></label>
                                <input type="text" name="display_name" class="form-control" value="{{ old('display_name', $role->display_name) }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Identifier / Slug Role</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $role->name) }}" {{ in_array($role->name, ['admin', 'user']) ? 'readonly' : '' }}>
                                @if(in_array($role->name, ['admin', 'user']))
                                    <small class="form-text text-muted">Slug role bawaan sistem dilindungi dan tidak dapat diubah.</small>
                                @endif
                            </div>
                            <div class="col-12 form-group mb-0">
                                <label class="font-weight-bold">Deskripsi Tugas</label>
                                <textarea name="description" class="form-control" rows="2">{{ old('description', $role->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD PERMISSION MATRIX --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white font-weight-bold py-3 d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-lock text-primary mr-2"></i> Pengaturan Hak Akses Tombol &amp; Fitur (Permissions)
                        </div>
                        @if($role->name !== 'admin')
                            <div class="mt-2 mt-sm-0">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnCheckAll">
                                    <i class="fas fa-check-double mr-1"></i> Centang Semua
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary ml-1" id="btnUncheckAll">
                                    <i class="fas fa-times mr-1"></i> Hapus Semua
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            @foreach($permissions as $groupName => $groupPermissions)
                                <div class="col-md-6 mb-4">
                                    <div class="border rounded p-3 h-100 bg-light shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                            <span class="font-weight-bold text-dark fs-6">
                                                <i class="fas fa-folder mr-1 text-primary"></i> {{ $groupName }}
                                            </span>
                                            @if($role->name !== 'admin')
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input check-group" id="group_{{ Str::slug($groupName) }}" data-target=".perm-group-{{ Str::slug($groupName) }}">
                                                    <label class="custom-control-label small text-muted font-weight-bold" for="group_{{ Str::slug($groupName) }}">Pilih Grup</label>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-2">
                                            @foreach($groupPermissions as $permission)
                                                @php
                                                    $isChecked = in_array($permission->id, old('permissions', $rolePermissions)) || ($role->name === 'admin');
                                                @endphp
                                                <div class="custom-control custom-checkbox mb-2">
                                                    <input type="checkbox"
                                                           name="permissions[]"
                                                           value="{{ $permission->id }}"
                                                           class="custom-control-input perm-checkbox perm-group-{{ Str::slug($groupName) }}"
                                                           id="perm_{{ $permission->id }}"
                                                           {{ $isChecked ? 'checked' : '' }}
                                                           {{ $role->name === 'admin' ? 'disabled' : '' }}>
                                                    <label class="custom-control-label" for="perm_{{ $permission->id }}">
                                                        <span class="font-weight-bold text-dark d-block">{{ $permission->display_name }}</span>
                                                        <small class="text-muted d-block">{{ $permission->description }}</small>
                                                        <code class="small text-secondary">{{ $permission->name }}</code>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- TOMBOL SUBMIT --}}
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary px-4 font-weight-bold">Batal</a>
                    <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var btnCheckAll = document.getElementById('btnCheckAll');
        var btnUncheckAll = document.getElementById('btnUncheckAll');

        if (btnCheckAll) {
            btnCheckAll.addEventListener('click', function () {
                document.querySelectorAll('.perm-checkbox:not(:disabled)').forEach(function(cb) { cb.checked = true; });
                document.querySelectorAll('.check-group').forEach(function(cb) { cb.checked = true; });
            });
        }

        if (btnUncheckAll) {
            btnUncheckAll.addEventListener('click', function () {
                document.querySelectorAll('.perm-checkbox:not(:disabled)').forEach(function(cb) { cb.checked = false; });
                document.querySelectorAll('.check-group').forEach(function(cb) { cb.checked = false; });
            });
        }

        // Checkbox Per-Grup
        document.querySelectorAll('.check-group').forEach(function (groupCheckbox) {
            var targetSelector = groupCheckbox.getAttribute('data-target');
            var checkboxesInGroup = document.querySelectorAll(targetSelector);
            
            // Periksa apakah semua di grup sudah tercentang awal
            var allChecked = Array.from(checkboxesInGroup).every(function(cb) { return cb.checked; });
            if (allChecked && checkboxesInGroup.length > 0) {
                groupCheckbox.checked = true;
            }

            groupCheckbox.addEventListener('change', function () {
                checkboxesInGroup.forEach(function (cb) {
                    if (!cb.disabled) {
                        cb.checked = groupCheckbox.checked;
                    }
                });
            });
        });
    });
</script>
@endsection
@endsection
