@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="w-full overflow-x-auto">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap bg-white rounded" style="gap: 0.5rem;">
                    <div>
                        <h4 class="mb-0 font-weight-bold exam-title">{{ $exam->name }}</h4>
                    </div>
                    <div class="text-danger font-weight-bold timer-display">
                        <span id="timer-display">Memuat...</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">

                    {{-- ========================================== --}}
                    {{-- DECODE DATA INSTRUKSI (1 KALI UNTUK SEMUA) --}}
                    {{-- ========================================== --}}
                    @php
                        $instructionsData = json_decode($exam->instructions, true) ?? [];
                    @endphp

                    {{-- ========================================== --}}
                    {{-- UI KHUSUS UNTUK UJIAN DISC (DARI DB)       --}}
                    {{-- ========================================== --}}
                    @if($exam->type == 'disc')
                        <div class="card border-0 shadow-sm m-3 overflow-hidden" style="border-left: 5px solid #ffc107 !important;">
                            <div class="card-body bg-light">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                    <h5 class="mb-0 font-weight-bold text-dark">Instruksi Pengerjaan</h5>
                                </div>

                                <p class="text-secondary mb-3">
                                    Pada setiap nomor, Anda akan menemukan 4 pernyataan. Tugas Anda adalah memilih karakteristik yang <strong>Paling Mendekati</strong> dan <strong>Paling Tidak Mendekati</strong> diri Anda.
                                </p>

                                <div class="row">
                                    <div class="col-12 col-md-6 mb-2">
                                        <div class="p-3 rounded bg-white border border-success h-100">
                                            <h6 class="text-success font-weight-bold mb-2">
                                                <i class="fas fa-check-circle mr-1"></i> Kolom MOST (Mirip)
                                            </h6>
                                            <small class="text-muted">Pilih satu pernyataan yang <strong>Paling Menggambarkan</strong> diri Anda dalam lingkungan kerja/sosial.</small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <div class="p-3 rounded bg-white border border-danger h-100">
                                            <h6 class="text-danger font-weight-bold mb-2">
                                                <i class="fas fa-times-circle mr-1"></i> Kolom LEAST (Tidak Mirip)
                                            </h6>
                                            <small class="text-muted">Pilih satu pernyataan yang <strong>Paling Tidak Menggambarkan</strong> diri Anda saat ini.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pembatas"></div>
                        <style>
                            .pembatas { margin: 3rem 0; }
                        </style>

                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Pernyataan (Soal)</th>
                                    <th class="text-center" style="width: 80px;">Most</th>
                                    <th class="text-center" style="width: 80px;">Least</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exam->questions as $q)
                                    @php
                                        // Ambil jawaban peserta dari DB jika ada
                                        $userAnswer = $session->userAnswers->where('question_number', $q->number)->first();
                                        $mostAnswer = $userAnswer ? ($userAnswer->answers['most'] ?? '') : '';
                                        $leastAnswer = $userAnswer ? ($userAnswer->answers['least'] ?? '') : '';
                                    @endphp

        
                                    @if(isset($instructionsData[(string)$q->number]))
                                        @php $inst = $instructionsData[(string)$q->number]; @endphp
                                        <tr>
                                            <td colspan="3" class="p-3 border-0 bg-white">
                                                <div class="card border-0 shadow-sm" style="border-left: 5px solid #17a2b8;">
                                                    <div class="card-body bg-light">
                                                        <h5 class="font-weight-bold text-info"><i class="fas fa-info-circle mr-2"></i>Instruksi Tambahan</h5>
                                                        @if($inst['type'] == 'image')
                                                            <img src="{{ asset($inst['content']) }}" class="img-fluid rounded border shadow-sm my-2">
                                                        @else
                                                            <p class="mb-0 text-dark">{{ $inst['content'] }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                    <tr class="question-block bg-white" data-qnum="{{ $q->number }}">
                                        <td class="p-3">
                                            <h6 class="font-weight-bold text-primary mb-3">{{ $q->question_text }}</h6>
                                            @foreach($q->options as $key => $opt)
                                                @php
                                                    $letter = strtoupper($key);
                                                    $text   = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                                                @endphp
                                                <div class="mb-2 text-dark" style="font-size: 0.95rem; display: flex; align-items: center; height: 24px;">
                                                    <span class="font-weight-bold mr-2">{{ $letter }}.</span> {{ $text }}
                                                </div>
                                            @endforeach
                                        </td>
                                        
                                        <td class="text-center p-3 align-middle border-left">
                                            @foreach(['A','B','C','D'] as $letter)
                                                <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 24px;">
                                                    <input class="disc-radio" type="radio" 
                                                           name="most_{{ $q->number }}" 
                                                           value="{{ $letter }}" 
                                                           data-type="most"
                                                           style="transform: scale(1.5); cursor: pointer;"
                                                           {{ $mostAnswer === $letter ? 'checked' : '' }}>
                                                </div>
                                            @endforeach
                                        </td>

                                        <td class="text-center p-3 align-middle border-left">
                                            @foreach(['A','B','C','D'] as $letter)
                                                <div class="mb-2 d-flex justify-content-center align-items-center" style="height: 24px;">
                                                    <input class="disc-radio" type="radio" 
                                                           name="least_{{ $q->number }}" 
                                                           value="{{ $letter }}" 
                                                           data-type="least"
                                                           style="transform: scale(1.5); cursor: pointer;"
                                                           {{ $leastAnswer === $letter ? 'checked' : '' }}>
                                                </div>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    {{-- ========================================== --}}
                    {{-- UI UNTUK SOAL CFIT --}}
                    {{-- ========================================== --}}
                    @elseif($exam->type == 'cfit')
                        <div class="p-4">
                            @foreach($exam->questions as $q)
                                
                                {{-- INSTRUKSI UNIVERSAL CFIT --}}
                                @if(isset($instructionsData[(string)$q->number]))
                                    @php $inst = $instructionsData[(string)$q->number]; @endphp
                                    <div class="card mb-4 border-0 shadow-sm" style="border-left: 5px solid #17a2b8;">
                                        <div class="card-body bg-light">
                                            <h5 class="font-weight-bold text-info"><i class="fas fa-info-circle mr-2"></i>Instruksi / Contoh Pengerjaan</h5>
                                            @if($inst['type'] == 'image')
                                                <img src="{{ asset($inst['content']) }}" class="img-fluid rounded border shadow-sm my-2">
                                            @else
                                                <p class="mb-0 text-dark">{{ $inst['content'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-5 pb-4 border-bottom question-block" data-qnum="{{ $q->number }}">
                                    <h5 class="font-weight-bold mb-3">Soal No. {{ $q->number }}</h5>
                                    
                                    @if($q->question_image)
                                        <img src="{{ asset($q->question_image) }}" class="img-fluid mb-3 border rounded shadow-sm" alt="Soal CFIT">
                                    @endif

                                    <div class="row px-3">
                                        @foreach($q->options as $key => $val)
                                            <div class="col-4 col-md-2 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input std-radio" type="radio" name="answer_{{ $q->number }}" value="{{ strtoupper($key) }}" style="transform: scale(1.3);">
                                                    <label class="form-check-label ml-2 font-weight-bold" style="cursor: pointer; font-size: 1.1rem;">
                                                        Pilihan {{ strtoupper($key) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach  
                        </div>                  

                    {{-- ========================================== --}}
                    {{-- UI UNTUK SOAL TABEL ANGKA (PENJUMLAHAN) --}}
                    {{-- ========================================== --}}
                    @elseif($exam->type == 'angka')
                        <div class="p-4">
                            <div class="card border-0 shadow-sm m-3 overflow-hidden" style="border-left: 5px solid #28a745 !important;">
                                <div class="card-body bg-light">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-calculator"></i>
                                        </div>
                                        <h5 class="mb-0 font-weight-bold text-dark">Instruksi Pengerjaan</h5>
                                    </div>

                                    <p class="text-secondary mb-3">
                                        Perhatikan tabel angka di bawah dengan <strong>seksama</strong>. Hitung jumlah angka sesuai instruksi yang diberikan. Tulis jawaban numerik Anda pada kolom yang tersedia.
                                    </p>
                                </div>
                            </div>
                            <div class="pembatas border-bottom"></div>
                            
                            @foreach($exam->questions as $q)
                                @php
                                    $userAnswer = $session->userAnswers->where('question_number', $q->number)->first();
                                    $answerText = $userAnswer ? ($userAnswer->answers['answer_text'] ?? '') : '';
                                    
                                    $tableData = json_decode($q->question_text, true);
                                    $isStructuredTable = is_array($tableData) && isset($tableData['table']);
                                @endphp

                                {{-- INSTRUKSI UNIVERSAL ANGKA --}}
                                @if(isset($instructionsData[(string)$q->number]))
                                    @php $inst = $instructionsData[(string)$q->number]; @endphp
                                    <div class="card mb-4 border-0 shadow-sm" style="border-left: 5px solid #17a2b8;">
                                        <div class="card-body bg-light">
                                            <h5 class="font-weight-bold text-info"><i class="fas fa-info-circle mr-2"></i>Instruksi Tambahan</h5>
                                            @if($inst['type'] == 'image')
                                                <img src="{{ asset($inst['content']) }}" class="img-fluid rounded border shadow-sm my-2">
                                            @else
                                                <p class="mb-0 text-dark">{{ $inst['content'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-5 pb-4 border-bottom question-block" data-qnum="{{ $q->number }}">
                                    <h5 class="font-weight-bold mb-3 text-dark">
                                        <span class="badge badge-success mr-2">Soal {{ $q->number }}</span>
                                    </h5>
                                    
                                    @if($isStructuredTable && isset($tableData['question']))
                                        <div class="card border-0 bg-light mb-3 p-3" style="border-left: 3px solid #28a745;">
                                            <p class="mb-0 text-dark font-weight-bold">{{ $tableData['question'] }}</p>
                                        </div>
                                    @endif
                                    
                                    @if($isStructuredTable && isset($tableData['table']['headers']) && isset($tableData['table']['rows']))
                                        <div class="table-responsive mb-3">
                                            <table class="table table-bordered table-sm text-center mb-4" style="background-color: #f8f9fa; font-size: 0.85rem;">
                                                <thead class="bg-success text-white" style="position: sticky; top: 0;">
                                                    <tr>
                                                        @foreach($tableData['table']['headers'] as $header)
                                                            <th class="py-2">{{ $header }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($tableData['table']['rows'] as $rowIdx => $row)
                                                        <tr style="background-color: {{ $rowIdx % 2 == 0 ? '#ffffff' : '#f8f9fa' }};">
                                                            @foreach($row as $colIdx => $cellValue)
                                                                <td class="py-2 px-1 align-middle" style="font-family: 'Courier New', monospace; font-weight: 500;">
                                                                    @if($colIdx == count($row) - 1)
                                                                        <input type="text" 
                                                                            class="form-control form-control-sm table-number-cell" 
                                                                            name="answer_{{ $q->number }}_row_{{ $rowIdx }}" 
                                                                            placeholder="..." 
                                                                            style="font-size: 0.85rem; text-align: center; border-radius: 4px; font-weight: bold; border: 1px solid #17a2b8; min-width: 120px;"
                                                                            data-question="{{ $q->number }}">
                                                                    @else
                                                                        {{ $cellValue }}
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                    
                                                    <tr style="background-color: #e3f2fd; border-top: 3px solid #28a745;">
                                                        @for($col = 0; $col < count($tableData['table']['headers']); $col++)
                                                            <td class="py-2 px-1 text-center align-middle">
                                                                @if($col == count($tableData['table']['headers']) - 1)
                                                                    <span class="text-muted" style="font-weight: bold;">-</span>
                                                                @else
                                                                    <input type="text" 
                                                                        class="form-control form-control-sm table-number-cell" 
                                                                        name="answer_{{ $q->number }}_col_{{ $col }}" 
                                                                        placeholder="..." 
                                                                        style="font-size: 0.85rem; text-align: center; border-radius: 4px; font-weight: bold; border: 1px solid #28a745; min-width: 120px;"
                                                                        data-question="{{ $q->number }}">
                                                                @endif
                                                            </td>
                                                        @endfor
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="card border-0 bg-light mb-3 p-4" style="overflow-x: auto;">
                                            <pre class="mb-0 text-dark" style="font-family: 'Courier New', monospace; font-size: 0.9rem; line-height: 1.5; white-space: pre-wrap; word-wrap: break-word;">{{ $q->question_text }}</pre>
                                        </div>
                                        <div class="form-group">
                                            <label class="font-weight-bold text-secondary mb-2">Jawaban Anda (angka saja):</label>
                                            <input type="text" class="form-control table-number-input" name="answer_{{ $q->number }}" placeholder="Contoh: 1234567" style="font-size: 1rem; border-radius: 6px; font-weight: bold;" value="{{ $answerText }}">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                    {{-- =============================================== --}}
                    {{-- UI UNTUK SOAL BERNOMOR (ESSAY/URAIAN/KRAEPLIN)  --}}
                    {{-- =============================================== --}}
                    @elseif($exam->type == 'uraian' || $exam->type == 'tes_kraeplin')
                        <div class="p-4">
                            <div class="card border-0 shadow-sm m-3 overflow-hidden" style="border-left: 5px solid #17a2b8 !important;">
                                <div class="card-body bg-light">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <h5 class="mb-0 font-weight-bold text-dark">Instruksi Pengerjaan</h5>
                                    </div>
                                    <p class="text-secondary mb-3">
                                        Bacalah setiap pertanyaan dengan <strong>seksama</strong>. Ketik jawaban Anda pada kolom yang tersedia di bawah setiap soal. Pastikan jawaban Anda <strong>lengkap dan jelas</strong>.
                                    </p>
                                </div>
                            </div>
                            <div class="pembatas border-bottom"></div>
                            
                            @foreach($exam->questions as $q)
                                @php
                                    $userAnswer = $session->userAnswers->where('question_number', $q->number)->first();
                                    $answerText = $userAnswer ? ($userAnswer->answers['answer_text'] ?? '') : '';
                                @endphp

                                {{-- INSTRUKSI UNIVERSAL URAIAN --}}
                                @if(isset($instructionsData[(string)$q->number]))
                                    @php $inst = $instructionsData[(string)$q->number]; @endphp
                                    <div class="card mb-4 border-0 shadow-sm" style="border-left: 5px solid #17a2b8;">
                                        <div class="card-body bg-light">
                                            <h5 class="font-weight-bold text-info"><i class="fas fa-info-circle mr-2"></i>Instruksi Tambahan</h5>
                                            @if($inst['type'] == 'image')
                                                <img src="{{ asset($inst['content']) }}" class="img-fluid rounded border shadow-sm my-2">
                                            @else
                                                <p class="mb-0 text-dark">{{ $inst['content'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-5 pb-4 border-bottom question-block" data-qnum="{{ $q->number }}">
                                    <h5 class="font-weight-bold mb-3 text-dark">
                                        <span class="badge badge-info mr-2">No. {{ $q->number }}</span>
                                    </h5>
                                    <div class="card border-0 bg-light mb-3 p-3">
                                        @if($q->question_image)
                                            <img src="{{ $q->question_image }}" class="img-fluid d-block mb-3" style="max-width:100%; border:1px solid #dee2e6; border-radius:6px; background:#fff;">
                                        @endif
                                        <p class="mb-0 text-dark" style="line-height: 1.6; white-space: pre-line;">{{ $q->question_text }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold text-secondary mb-2">Jawaban Anda:</label>
                                        @if($exam->type == 'tes_kraeplin')
                                            <input type="text" class="form-control uraian-textarea" name="answer_{{ $q->number }}" placeholder="Ketik angka..." value="{{ $answerText }}" style="font-size: 1.1rem; border-radius: 6px; font-weight: bold; width: 100%; max-width: 300px;">
                                        @else
                                            <textarea class="form-control uraian-textarea" name="answer_{{ $q->number }}" rows="4" placeholder="Ketik jawaban Anda di sini..." style="font-size: 0.95rem; border-radius: 6px;">{{ $answerText }}</textarea>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    {{-- ========================================== --}}
                    {{-- UI STANDAR UNTUK MBTI, VAK, EPPS, DLL --}}
                    {{-- ========================================== --}}
                    @elseif($exam->type == 'soal_kasus')
                        {{-- KASUS LOGIC TETAP SAMA KARENA TIDAK ADA $q->number SPESIFIK YG PERLU INSTRUKSI IN-LINE --}}
                        {{-- Kamu bisa menyisipkan manual jika diperlukan di bagian atas logic kasus --}}
                        <div class="p-4 bg-light text-dark">
                           <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px; border-top: 6px solid #1a73e8 !important;">
                                </div>
                        </div>

                    @else
                        <div class="p-4">
                            <div class="card border-0 shadow-sm m-3 overflow-hidden" style="border-left: 5px solid #ffc107 !important;">
                                <div class="card-body bg-light">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <h5 class="mb-0 font-weight-bold text-dark">Instruksi Pengerjaan</h5>
                                    </div>
                                    <p class="text-secondary mb-3">
                                        Pada setiap nomor, Anda akan menemukan beberapa <strong>pilihan</strong>. Tugas Anda adalah memilih karakteristik yang <strong>Paling Mendekati</strong> diri Anda.
                                    </p>
                                </div>
                            </div>
                            <div class="pembatas border-bottom "></div>
                            
                            @foreach($exam->questions as $q)
                                @php
                                    $hasImageOpts  = $q->has_image_options ?? false;
                                    $questionImage = $q->question_image ?? null;
                                @endphp

                                {{-- INSTRUKSI UNIVERSAL PILIHAN GANDA --}}
                                @if(isset($instructionsData[(string)$q->number]))
                                    @php $inst = $instructionsData[(string)$q->number]; @endphp
                                    <div class="card mb-4 border-0 shadow-sm" style="border-left: 5px solid #17a2b8;">
                                        <div class="card-body bg-light">
                                            <h5 class="font-weight-bold text-info"><i class="fas fa-info-circle mr-2"></i>Instruksi Tambahan</h5>
                                            @if($inst['type'] == 'image')
                                                <img src="{{ asset($inst['content']) }}" class="img-fluid rounded border shadow-sm my-2">
                                            @else
                                                <p class="mb-0 text-dark">{{ $inst['content'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-4 pb-3 border-bottom question-block" data-qnum="{{ $q->number }}">
                                    <h5 class="font-weight-bold mb-3">Soal No. {{ $q->number }}</h5>
                                    @if($hasImageOpts && $questionImage)
                                        <img src="{{ $questionImage }}" alt="Soal {{ $q->number }}" class="img-fluid d-block mb-3" style="max-width:100%; border:1px solid #e9ecef; border-radius:6px; background:#fff;">
                                        <div class="mt-2">
                                            @foreach(['A','B','C','D','E'] as $letter)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input std-radio" type="radio" name="answer_{{ $q->number }}" value="{{ $letter }}">
                                                    <label class="form-check-label" style="cursor:pointer; font-weight:600;">{{ $letter }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p>{!! $q->question_text !!}</p>
                                        @foreach($q->options as $key => $opt)
                                            @php
                                                $letter = strtoupper($key);
                                                $text   = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                                            @endphp
                                            <div class="form-check mb-2">
                                                <input class="form-check-input std-radio" type="radio" name="answer_{{ $q->number }}" value="{{ $letter }}">
                                                <label class="form-check-label" style="cursor:pointer;">{{ $letter }}. {{ $text }}</label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="card-footer bg-white border-top pb-4 pt-4 text-center">
                        <button type="button" id="btn-submit-exam" class="btn btn-success btn-lg px-5 shadow-sm">
                            Selesaikan & Kumpulkan Ujian
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-center">
                <span id="camera-status" class="badge badge-pill badge-secondary px-3 py-2">🔴 Menyiapkan proctoring...</span>
            </div>

        </div>
    </div>
</div>

<video id="webcam-video" autoplay playsinline style="display: none;"></video>
<canvas id="snapshot-canvas" style="display: none;"></canvas>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const examSessionId = {{ $session->id }};

        // ==========================================
        // 1. FUNGSI AJAX REUSABLE (Simpan Jawaban)
        // ==========================================
        function saveAnswerAjax(qNum, jsonAnswer) {
            // PERBAIKAN: Gunakan helper url() agar path folder otomatis terdeteksi di server
            fetch("{{ url('/exam/answer') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    exam_session_id: examSessionId,
                    question_number: qNum,
                    answers: jsonAnswer
                })
            }).catch(err => console.error("Gagal menyimpan", err));
        }

        // ==========================================
        // 2. LOGIKA UI & VALIDASI: UJIAN DISC & STANDAR
        // ==========================================
        // (Logika DISC & Standar tetap sama seperti kode kamu sebelumnya)
        const discRadios = document.querySelectorAll('.disc-radio');
        discRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                let block = this.closest('.question-block');
                let qNum = block.dataset.qnum;
                let type = this.dataset.type;
                let val = this.value;
                let oppositeType = (type === 'most') ? 'least' : 'most';
                let oppositeRadios = document.querySelectorAll(`input[name="${oppositeType}_${qNum}"]`);
                oppositeRadios.forEach(el => {
                    el.disabled = false;
                    if (el.value === val) {
                        el.disabled = true;
                        if (el.checked) el.checked = false;
                    }
                });
                let mostVal = document.querySelector(`input[name="most_${qNum}"]:checked`)?.value || null;
                let leastVal = document.querySelector(`input[name="least_${qNum}"]:checked`)?.value || null;
                saveAnswerAjax(qNum, { most: mostVal, least: leastVal });
            });
        });

        const stdRadios = document.querySelectorAll('.std-radio');
        stdRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                let qNum = this.closest('.question-block').dataset.qnum;
                let val = this.value;
                saveAnswerAjax(qNum, { selected: val });
            });
        });

        // Handler untuk textarea (uraian questions)
        const uraianTextareas = document.querySelectorAll('.uraian-textarea');
        uraianTextareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                let qNum = this.closest('.question-block').dataset.qnum;
                let val = this.value;
                saveAnswerAjax(qNum, { answer_text: val });
            });
        });

        // Handler untuk input number (angka_akuntasi questions)
        const tableNumberInputs = document.querySelectorAll('.table-number-input');
        tableNumberInputs.forEach(input => {
            input.addEventListener('input', function() {
                let qNum = this.closest('.question-block').dataset.qnum;
                let val = this.value;
                saveAnswerAjax(qNum, { answer_text: val });
            });
        });

        // Handler untuk cell inputs di dalam tabel (angka_akuntasi questions)
        const tableNumberCells = document.querySelectorAll('.table-number-cell');
        tableNumberCells.forEach(cell => {
            cell.addEventListener('input', function() {

                // === FITUR BARU: Auto Format Ribuan (Titik) ===
                // 1. Hapus semua karakter selain angka (mencegah user mengetik huruf)
                let angkaSaja = this.value.replace(/\D/g, '');
                // 2. Tambahkan titik setiap kelipatan 3 digit
                let formatTitik = angkaSaja.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                // 3. Kembalikan nilai yang sudah diformat ke dalam input kotak
                this.value = formatTitik;
                // ==============================================

                let qNum = this.dataset.question;
                let block = document.querySelector(`.question-block[data-qnum="${qNum}"]`);
                
                let cells = block.querySelectorAll('.table-number-cell');
                let allValues = [];
                let detailsObj = {}; // Wadah baru untuk menyimpan posisi baris/kolom
                
                cells.forEach(c => {
                    let val = c.value.trim();
                    if(val !== '') {
                        allValues.push(val);
                        
                        // Ekstrak nama input (contoh: answer_1_row_0 atau answer_1_col_2)
                        let nameParts = c.name.split('_');
                        if(nameParts.length >= 4) {
                            let type = nameParts[2]; // 'row' atau 'col'
                            let index = parseInt(nameParts[3]) + 1; // +1 agar visualnya mulai dari 1, bukan 0
                            
                            let label = type === 'row' ? `Mendatar (Baris ${index})` : `Menurun (Kolom ${index})`;
                            detailsObj[label] = val;
                        }
                    }
                });
                
                let combinedValue = allValues.join(', ');
                
                // Update input final fallback (jika masih ada)
                let finalInput = block.querySelector('.table-number-input');
                if (finalInput) {
                    finalInput.value = combinedValue;
                }
                
                // SIMPAN KE DB DENGAN FORMAT STRUKTUR JSON YANG BARU
                saveAnswerAjax(qNum, { 
                    answer_text: combinedValue,
                    details: detailsObj // Kirim detail posisi ke server
                });
            });
        });

        // ==========================================
        // 3. LOGIKA TIMER MUNDUR
        // ==========================================
        let remainingSeconds = {{ $remainingSeconds }};
        const timerDisplay = document.getElementById('timer-display');

        // Simpan interval ke dalam variabel agar bisa dihentikan (clear)
        const timerInterval = setInterval(updateTimer, 1000);

        function updateTimer() {
            if (remainingSeconds <= 0) {
                // 1. Hentikan Timer
                clearInterval(timerInterval);
                timerDisplay.innerHTML = "WAKTU HABIS!";

                // 2. Notifikasi Ke Peserta (Pakai SweetAlert2)
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Sistem akan mengumpulkan jawaban Anda secara otomatis.',
                    icon: 'warning',
                    timer: 2500, // Tampil 2.5 detik
                    showConfirmButton: false,
                    willClose: () => {
                        // 3. Jalankan fungsi finish
                        finishExam();
                    }
                });
                return;
            }

            let m = Math.floor(remainingSeconds / 60).toString().padStart(2, '0');
            let s = (remainingSeconds % 60).toString().padStart(2, '0');
            timerDisplay.innerHTML = m + ":" + s;
            remainingSeconds--;
        }
        updateTimer();

        // ==========================================
        // 4. KAMERA PROCTORING
        // ==========================================
        const video = document.getElementById('webcam-video');
        const canvas = document.getElementById('snapshot-canvas');
        const cameraStatus = document.getElementById('camera-status');

        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(function(stream) {
                video.srcObject = stream;
                cameraStatus.innerHTML = "🟢 Kamera aktif (Ujian diawasi)";
                cameraStatus.classList.replace('badge-secondary', 'badge-success');

                // JEPRETAN PERTAMA: Langsung ambil foto saat masuk (Detik ke-0)
                // Memberi delay 1 detik agar kamera sudah benar-benar terbuka
                setTimeout(takeSnapshotAndSend, 1000);

                // Mulai siklus acak setelah jepretan pertama
                scheduleNextSnapshot();
            })
            .catch(function(err) {
                cameraStatus.innerHTML = "❌ Akses Kamera Ditolak!";
                cameraStatus.classList.replace('badge-secondary', 'badge-danger');
                Swal.fire('Kamera Wajib!', 'Ujian ini memerlukan kamera untuk pengawasan.', 'error');
            });

        function takeSnapshotAndSend() {
            canvas.width = 640; canvas.height = 480;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            let base64Image = canvas.toDataURL('image/jpeg', 0.6);

            fetch("{{ url('/proctoring/snap') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ exam_session_id: examSessionId, image: base64Image })
            });
        }

        function scheduleNextSnapshot() {
            // INTERVAL DIPERPENDEK: Antara 1 menit (60rb) sampai 5 menit (300rb)
            // Agar ujian durasi pendek tetap ter-capture beberapa kali
            let timeout = Math.floor(Math.random() * (300000 - 60000 + 1)) + 60000;
            setTimeout(() => {
                takeSnapshotAndSend();
                scheduleNextSnapshot();
            }, timeout);
        }


        const isKasus = "{{ $exam->type }}" === 'soal_kasus';
            let uploadedFilesArray = {!! $session->answer_file ? $session->answer_file : '[]' !!};

            if (isKasus) {
                const fileInput = document.getElementById('answer-file-input');
                const uploadAlert = document.getElementById('upload-alert');
                const filesBox = document.getElementById('uploaded-files-box');
                const filesList = document.getElementById('uploaded-files-list');
                const fileLabel = document.querySelector('.custom-file-label');

                // 1. FUNGSI UNTUK MERENDER BADGE FILE + TOMBOL CANCEL
                function renderFileList(files) {
                    // TAMBAHKAN BARIS INI: Cegah error jika elemen HTML sedang di-comment
                    if (!filesBox || !fileLabel || !filesList || !uploadAlert || !fileInput) return;

                    if (!files || files.length === 0) {
                        filesBox.style.display = 'none';
                        fileLabel.innerHTML = "Pilih satu atau beberapa file...";
                        return;
                    }
                    
                    filesBox.style.display = 'block';
                    filesList.innerHTML = '';

                    files.forEach(file => {
                        filesList.innerHTML += `
                            <div class="badge badge-white border text-dark p-2 shadow-sm d-flex align-items-center rounded" style="font-size: 0.85rem; gap: 10px;">
                                <span>📄</span>
                                <span class="font-weight-normal">${file.name}</span>

                                <button type="button" class="btn-delete-file" data-path="${file.path}"
                                        style="border: none; background: none; color: #dc3545; font-size: 1.2rem; line-height: 1; padding: 0 0 2px 0; margin-left: 5px; cursor: pointer; font-weight: bold;"
                                        title="Batalkan file ini">
                                    &times;
                                </button>
                            </div>
                        `;
                    });

                    // Daftarkan kembali event klik hapus untuk tombol baru
                    attachDeleteEvents();
                }

                // 2. FUNGSI AJAX UNTUK MENGHAPUS/CANCEL FILE
                function attachDeleteEvents() {
                    const deleteButtons = document.querySelectorAll('.btn-delete-file');
                    deleteButtons.forEach(btn => {
                        btn.addEventListener('click', function() {
                            const filePath = this.dataset.path;

                            Swal.fire({
                                title: 'Batalkan file ini?',
                                text: "File akan dihapus dari server lembar jawaban Anda.",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#6c757d',
                                confirmButtonText: 'Ya, Hapus!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    let formData = new FormData();
                                    formData.append('exam_session_id', examSessionId);
                                    formData.append('file_path', filePath);

                                    fetch("{{ route('exam.delete-file') }}", {
                                        method: 'POST',
                                        headers: { 'X-CSRF-TOKEN': csrfToken },
                                        body: formData
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            // Ganti array global dengan sisa file dari server
                                            uploadedFilesArray = data.files;
                                            renderFileList(uploadedFilesArray);

                                            if(uploadedFilesArray.length > 0) {
                                                fileLabel.innerHTML = `${uploadedFilesArray.length} file terpilih...`;
                                            }

                                            Swal.fire('Dibatalkan!', 'File berhasil dihapus.', 'success');
                                        } else {
                                            Swal.fire('Gagal!', data.message, 'error');
                                        }
                                    })
                                    .catch(err => console.error("Gagal menghapus berkas:", err));
                                }
                            });
                        });
                    });
                }

                // Render pertama kali saat halaman dibuka
                renderFileList(uploadedFilesArray);

                if(fileInput){
                    // 3. LOGIKA UPLOAD (Sudah Diperbaiki Sistem Error Handling & Header)
                    fileInput.addEventListener('change', function() {
                        if (this.files.length === 0) return;

                        let formData = new FormData();
                        formData.append('exam_session_id', examSessionId);
                        for (let i = 0; i < this.files.length; i++) {
                            formData.append('answer_files[]', this.files[i]);
                        }

                        uploadAlert.style.display = 'block';
                        uploadAlert.className = 'alert alert-info small p-2';
                        uploadAlert.innerHTML = '⏳ Sedang mengunggah berkas...';

                        fetch("{{ route('exam.upload-file') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json' // WAKIB: Biar Laravel return JSON kalau error, bukan redirect HTML
                            },
                            body: formData
                        })
                        .then(async res => {
                            // Jika server merespon dengan status error (422, 500, dll)
                            if (!res.ok) {
                                const errorData = await res.json();
                                throw new Error(errorData.message || 'Ukuran file terlalu besar atau format tidak didukung.');
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                uploadAlert.className = 'alert alert-success small p-2';
                                uploadAlert.innerHTML = '🟢 Berkas berhasil ditambahkan!';
                                uploadedFilesArray = data.files;
                                renderFileList(uploadedFilesArray);
                                fileLabel.innerHTML = `${data.files.length} file terpilih...`;
                            } else {
                                uploadAlert.className = 'alert alert-danger small p-2';
                                uploadAlert.innerHTML = '❌ Gagal: ' + data.message;
                            }
                        })
                        .catch(err => {
                            // Tangkap error di sini agar alert tidak stuck loading terus
                            uploadAlert.className = 'alert alert-danger small p-2';
                            uploadAlert.innerHTML = '❌ ' + err.message;
                            console.error(err);
                        });
                    });
                }
            }

        // ==========================================
        // 5. LOGIKA VALIDASI & SUBMIT
        // ==========================================
        const btnSubmit = document.getElementById('btn-submit-exam');

        // Fungsi untuk mengecek apakah semua soal sudah terisi
        function checkCompletion() {
            if (isKasus) {
                let hasFile = uploadedFilesArray && uploadedFilesArray.length > 0;
                let hasTableData = false;

                // Cek apakah tabel Jspreadsheet ada isinya
                if (typeof tableInstance !== 'undefined') {
                    let tableData = tableInstance.getData();
                    // Cek semua baris dan kolom, jika ada 1 saja yang tidak kosong, berarti sudah diisi
                    for (let i = 0; i < tableData.length; i++) {
                        for (let j = 0; j < tableData[i].length; j++) {
                            if (tableData[i][j] !== null && tableData[i][j] !== '') {
                                hasTableData = true;
                                break;
                            }
                        }
                        if (hasTableData) break;
                    }
                }

                // Jika tidak ada file DAN tabel Jurnal kosong, lempar error khusus
                if (!hasFile && !hasTableData) {
                    return ['kasus_kosong'];
                }
                return [];
            }

            let unanswered = [];
            let isDisc = "{{ $exam->type }}" === 'disc';
            let isuraian = "{{ $exam->type }}" === 'uraian' || "{{ $exam->type }}" === 'tes_kraeplin';
            let isTableNumber = "{{ $exam->type }}" === 'angka';

            // Ambil semua nomor soal unik yang ada di halaman
            let questionNumbers = [...new Set(Array.from(document.querySelectorAll('.question-block')).map(el => el.dataset.qnum))];

            questionNumbers.forEach(qNum => {
                let block = document.querySelector(`.question-block[data-qnum="${qNum}"]`);

                if (isDisc) {
                    // Untuk DISC: Cek apakah Most DAN Least sudah dipilih
                    let mostSelected = document.querySelector(`input[name="most_${qNum}"]:checked`);
                    let leastSelected = document.querySelector(`input[name="least_${qNum}"]:checked`);

                    if (!mostSelected || !leastSelected) {
                        unanswered.push(qNum);
                    }
                } 
                else if (isTableNumber) {
                    // LOGIKA BARU KHUSUS TABEL ANGKA
                    // Cek semua kotak kecil di dalam tabel
                    let cells = block.querySelectorAll('.table-number-cell');
                    // Cek apakah minimal ada 1 kotak yang sudah diisi angka oleh peserta
                    let hasAnswer = Array.from(cells).some(c => c.value.trim() !== '');
                    
                    if (!hasAnswer) {
                        unanswered.push(qNum);
                    }
                } 
                else if (isuraian) {
                    // Cek elemen berdasarkan attribut name, tidak peduli dia input atau textarea
                    let inputEl = document.querySelector(`[name="answer_${qNum}"]`);
                    if (!inputEl || !inputEl.value.trim()) {
                        unanswered.push(qNum);
                    }
                }
                else {
                    // Untuk Standar (MBTI/VAK): Cek apakah radio button sudah dipilih
                    let selected = document.querySelector(`input[name="answer_${qNum}"]:checked`);
                    if (!selected) {
                        unanswered.push(qNum);
                    }
                }
            });

            return unanswered;
        }

        // ==========================================
        // FUNGSI: Pilih opsi gambar (klik gambar)
        // ==========================================
        window.selectImageOption = function(imgEl, qNum, letter) {
            // Reset semua gambar di soal ini
            document.querySelectorAll(`[data-qnum="${qNum}"] .img-option`).forEach(img => {
                img.style.borderColor = '#dee2e6';
                img.style.boxShadow = 'none';
            });
            // Highlight gambar yang dipilih
            imgEl.style.borderColor = '#28a745';
            imgEl.style.boxShadow = '0 0 0 3px rgba(40,167,69,0.25)';
            // Set radio button tersembunyi & trigger save
            let radio = document.getElementById(`opt_${qNum}_${letter}`);
            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };

        btnSubmit.addEventListener('click', function() {
            // 1. Jalankan Validasi
            let missingAnswers = checkCompletion();

            if (missingAnswers.length > 0) {
                // Atur pesan teks default untuk soal biasa
                let warningText = `Anda belum menjawab soal nomor: ${missingAnswers.join(', ')}. Silakan lengkapi semua jawaban sebelum mengumpulkan.`;

                // Jika errornya berasal dari Soal Kasus (file kosong & tabel kosong)
                if (isKasus && missingAnswers[0] === 'kasus_kosong') {
                    warningText = "Anda belum mengisi Tabel Jurnal Umum atau mengunggah file lembar jawaban. Silakan lengkapi salah satunya sebelum mengumpulkan.";
                }

                // Tampilkan peringatan dan batalkan submit
                Swal.fire({
                    title: 'Belum Lengkap!',
                    text: warningText,
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Oke, Saya Lengkapi'
                });
                return; // Berhenti di sini
            }

            // 2. Jika sudah lengkap, tampilkan konfirmasi yakin/tidak
            Swal.fire({
                title: 'Kumpulkan Ujian?',
                text: "Anda telah menjawab semua soal. Yakin ingin mengumpulkan sekarang?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kumpulkan Sekarang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    finishExam();
                }
            });
        });

        // Fungsi ini dipanggil oleh Tombol (setelah validasi)
        // ATAU dipanggil langsung oleh Timer (tanpa validasi)
        function finishExam() {
            // Tampilkan loading agar user tidak klik berkali-kali
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Ambil foto terakhir
            takeSnapshotAndSend();

            setTimeout(() => {
                // Trik jitu: Render URL dasar dari PHP, lalu ganti teks 'PLACEHOLDER' dengan variable JavaScript
                let url = "{{ route('exam.finish', ['session_id' => 'PLACEHOLDER']) }}";
                window.location.href = url.replace('PLACEHOLDER', examSessionId);
            }, 500);
        }
        
        // ==========================================
        // 6. LOGIKA TABEL JURNAL (JSPREADSHEET)
        // ==========================================
        const jurnalContainer = document.getElementById('jurnal-table');
        
        if (jurnalContainer) {
            let qNum = jurnalContainer.dataset.qnum;

                var tableInstance = jspreadsheet(jurnalContainer, {
                data: [
                    ['', '', '', '', ''], 
                    ['', '', '', '', ''],
                ],
                columns: [
                    { type: 'calendar', title: 'Tanggal', width: 120, options: { format: 'DD/MM/YYYY' } },
                    { type: 'text', title: 'Keterangan', width: 250 },
                    { type: 'text', title: 'Ref', width: 80 },
                    { type: 'text', title: 'Debit (Rp)', width: 150, mask: '#.##0,00', align: 'right' },
                    { type: 'text', title: 'Kredit (Rp)', width: 150, mask: '#.##0,00', align: 'right' },
                ],
                minDimensions: [5, 5],
                allowInsertRow: true,
                allowManualInsertRow: true,
                allowDeleteRow: true,
                
                // UBAH BAGIAN INI MENJADI TRUE AGAR BISA DITAMBAH/DIHAPUS
                allowInsertColumn: true, 
                allowManualInsertColumn: true,
                allowDeleteColumn: true,
                
                wordWrap: true,
                
                // EVENT ONCHANGE: Terpanggil setiap sel diedit atau baris ditambah/dihapus
                onchange: function(instance, cell, x, y, value) {
                    // Ambil seluruh data tabel bentuk Array
                    let tableData = tableInstance.getData();
                    
                    // Simpan ke database secara otomatis lewat AJAX
                    saveAnswerAjax(qNum, tableData);
                },
                oninsertrow: function() {
                    let tableData = tableInstance.getData();
                    saveAnswerAjax(qNum, tableData);
                },
                ondeleterow: function() {
                    let tableData = tableInstance.getData();
                    saveAnswerAjax(qNum, tableData);
                }
            });
        }
    });
</script>

<style>
/* ===== RESPONSIVE SHOW.BLADE ===== */

/* Timer & judul di header */
.exam-title  { font-size: clamp(0.95rem, 3.5vw, 1.35rem); }
.timer-display { font-size: clamp(1.1rem, 4vw, 1.5rem); }

/* DISC table: scroll horizontal di HP */
#examUsersTable, .table-responsive { overflow-x: auto; }

/* Tombol submit full-width di HP */
@media (max-width: 575.98px) {
    #btn-submit-exam { width: 100%; font-size: 0.95rem; }

    /* Perkecil input di tabel angka */
    .table-number-cell { font-size: 0.75rem !important; padding: 2px 4px !important; }

    /* Instruksi DISC icon circle lebih kecil */
    .rounded-circle[style*="40px"] { width: 30px !important; height: 30px !important; font-size: 0.8rem; }

    /* Padding card lebih rapat */
    .card-body.p-4 { padding: 1rem !important; }

    /* Textarea uraian lebih pendek */
    .uraian-textarea { rows: 3; font-size: 0.88rem !important; }

    /* Tabel DISC: font lebih kecil */
    .table td, .table th { font-size: 0.78rem; padding: 0.35rem 0.4rem; }

    /* Radio DISC scale down */
    input[type=radio][style*="scale(1.5)"] { transform: scale(1.1) !important; }
}

@media (min-width: 576px) and (max-width: 991.98px) {
    .table td, .table th { font-size: 0.83rem; }
    #btn-submit-exam { width: 100%; }
}

/* Kasus akuntansi: tabel saldo scroll horizontal */
.table-responsive { -webkit-overflow-scrolling: touch; }

/* ── Opsi Gambar (soal IQ bergambar) ── */
.img-option {
    cursor: pointer;
    transition: border-color 0.15s, box-shadow 0.15s;
    border: 2px solid #dee2e6 !important;
    border-radius: 6px;
    background: #fff;
}
.img-option:hover {
    border-color: #80bdff !important;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.15);
}
@media (max-width: 575.98px) {
    .img-option { max-width: 80px !important; max-height: 70px !important; }
}

/* Camera status badge wrap */
#camera-status { word-break: break-word; max-width: 90vw; display: inline-block; }
</style>
@endsection