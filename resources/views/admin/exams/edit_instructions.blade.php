@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-white font-weight-bold">
            Kelola Instruksi Ujian: {{ $exam->name }}
        </div>
        <div class="card-body">
            <form action="{{ route('manage-exams.update-instructions', $exam->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="instruction-list">
                    @php $data = json_decode($exam->instructions, true) ?? []; @endphp
                    @foreach($data as $num => $item)
                        <div class="row mb-3 p-3 border rounded">
                            <div class="col-md-2">
                                <label>No. Soal</label>
                                <input type="number" name="instruction_items[{{$loop->index}}][number]" value="{{$num}}" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Tipe</label>
                                <select name="instruction_items[{{$loop->index}}][type]" class="form-control type-select">
                                    <option value="text" {{ $item['type'] == 'text' ? 'selected' : '' }}>Teks</option>
                                    <option value="image" {{ $item['type'] == 'image' ? 'selected' : '' }}>Gambar</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label>Konten</label>
                                <div class="text-input {{ $item['type'] == 'image' ? 'd-none' : '' }}">
                                    <textarea name="instruction_items[{{$loop->index}}][text]" class="form-control">{{ $item['type'] == 'text' ? $item['content'] : '' }}</textarea>
                                </div>
                                <div class="file-input {{ $item['type'] == 'text' ? 'd-none' : '' }}">
                                    <input type="file" name="instruction_items[{{$loop->index}}][file]" class="form-control">
                                    @if($item['type'] == 'image') <small>File saat ini: {{ $item['content'] }}</small> @endif
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-btn">X</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <button type="button" id="add-btn" class="btn btn-primary mt-2">+ Tambah Instruksi</button>
                <button type="submit" class="btn btn-success mt-2">Simpan Semua Instruksi</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-btn').addEventListener('click', function() {
        let index = document.querySelectorAll('#instruction-list .row').length;
        let html = `
        <div class="row mb-3 p-3 border rounded">
            <div class="col-md-2"><label>No. Soal</label><input type="number" name="instruction_items[${index}][number]" class="form-control" required></div>
            <div class="col-md-4"><label>Tipe</label><select name="instruction_items[${index}][type]" class="form-control type-select"><option value="text">Teks</option><option value="image">Gambar</option></select></div>
            <div class="col-md-5"><label>Konten</label>
                <div class="text-input"><textarea name="instruction_items[${index}][text]" class="form-control"></textarea></div>
                <div class="file-input d-none"><input type="file" name="instruction_items[${index}][file]" class="form-control"></div>
            </div>
            <div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-danger remove-btn">X</button></div>
        </div>`;
        document.getElementById('instruction-list').insertAdjacentHTML('beforeend', html);
    });

    document.addEventListener('change', function(e) {
        if(e.target.classList.contains('type-select')) {
            let parent = e.target.closest('.row');
            parent.querySelector('.text-input').classList.toggle('d-none', e.target.value === 'image');
            parent.querySelector('.file-input').classList.toggle('d-none', e.target.value === 'text');
        }
    });

    document.addEventListener('click', function(e) {
        if(e.target.classList.contains('remove-btn')) e.target.closest('.row').remove();
    });
</script>
@endsection