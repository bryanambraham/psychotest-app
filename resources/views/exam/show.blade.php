@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9 col-xl-8">

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center flex-wrap bg-white rounded" style="gap: 0.5rem;">
                    <div>
                        <h4 class="mb-0 font-weight-bold exam-title">{{ $exam->name }}</h4>
                        <span class="badge badge-info">{{ strtoupper($exam->type) }}</span>
                    </div>
                    <div class="text-danger font-weight-bold timer-display">
                        <span id="timer-display">Memuat...</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">

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
                                    <h5 class="mb-0 font-weight-bold text-dark">Instruksi Pengerjaan DISC</h5>
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
                            .pembatas {
                                margin: 3rem 0;
                            }
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
                                    <tr class="bg-dark text-white">
                                        <td colspan="3" class="font-weight-bold small">Soal No. {{ $q->number }}</td>
                                    </tr>
                                    {{-- Looping options dari JSON database --}}
                                    @foreach($q->options as $key => $text)
                                        <tr class="question-block" data-qnum="{{ $q->number }}">
                                            <td>{{ strtoupper($key) }}. {{ $text }}</td>
                                            <td class="text-center align-middle">
                                                <input type="radio" name="most_{{ $q->number }}" value="{{ strtoupper($key) }}" class="disc-radio" data-type="most" style="transform: scale(1.5);">
                                            </td>
                                            <td class="text-center align-middle">
                                                <input type="radio" name="least_{{ $q->number }}" value="{{ strtoupper($key) }}" class="disc-radio" data-type="least" style="transform: scale(1.5);">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>

                    @elseif($exam->type == 'kasus_akuntansi')
                        {{-- ======================================================== --}}
                        {{-- UI PREMIUM STACKED & SMART PARSER FOR KASUS AKUNTANSI    --}}
                        {{-- ======================================================== --}}
                        <div class="p-4 bg-light text-dark">
                            <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px; border-top: 6px solid #1a73e8 !important;">
                                <div class="card-body p-4 p-md-5">

                                    @foreach($exam->questions as $q)
                                        @php
                                            $fullText = $q->question_text;

                                            // 1. Potong bagian Instruksi Kerja / Tugas (Paling Bawah)
                                            $instructionSplit = preg_split('/Tugas\s*[\/|:]\s*Instruksi Kerja:/i', $fullText);
                                            $mainBody = $instructionSplit[0];
                                            $instructionsText = $instructionSplit[1] ?? '';

                                            // 2. Potong bagian Transaksi
                                            $transactionSplit = preg_split('/Transaksi selama[^:]*:/i', $mainBody);
                                            $upperBody = $transactionSplit[0];
                                            $transactionsText = $transactionSplit[1] ?? '';

                                            // 3. Potong Judul + Intro dari Tabel Saldo
                                            $tableHeaderPattern = '/Nama Perkiraan\s*[\/|:]\s*Akun\s+Saldo Berjalan\s*\(Rp\)/i';
                                            $tableSplit = preg_split($tableHeaderPattern, $upperBody);
                                            $introText = $tableSplit[0] ?? '';
                                            $tableRowsText = $tableSplit[1] ?? '';

                                            // --- LOGIKA BARU: EKSTRAK JUDUL & PENGANTAR SECARA DINAMIS DARI PDF ---
                                            $introLines = array_values(array_filter(array_map('trim', explode("\n", $introText))));
                                            $dynamicTitle = $introLines[0] ?? 'SOAL KASUS AKUNTANSI';
                                            $dynamicParagraph = implode(" ", array_slice($introLines, 1));

                                            // --- PARSING TABEL SALDO ---
                                            $tableRows = [];
                                            foreach (explode("\n", $tableRowsText) as $line) {
                                                $line = trim($line);
                                                if (empty($line)) continue;
                                                if (preg_match('/^(.*?)\s+(\(?\d+(?:\.\d+)*\)?)$/', $line, $matches)) {
                                                    $tableRows[] = [
                                                        'account' => trim($matches[1]),
                                                        'balance' => trim($matches[2])
                                                    ];
                                                }
                                            }

                                            // --- PARSING DAFTAR TRANSAKSI ---
                                            $transactions = [];
                                            foreach (explode("\n", $transactionsText) as $line) {
                                                $line = trim($line);
                                                if (empty($line)) continue;
                                                if (preg_match('/^\d+[\.\)]\s+(.*)$/', $line, $matches)) {
                                                    $transactions[] = $matches[1];
                                                } else if (!empty($transactions)) {
                                                    $transactions[count($transactions) - 1] .= " " . $line;
                                                }
                                            }

                                            // --- PARSING DAFTAR INSTRUKSI ---
                                            $instructions = [];
                                            foreach (explode("\n", $instructionsText) as $line) {
                                                $line = trim($line);
                                                if (empty($line)) continue;
                                                if (preg_match('/^\d+[\.\)]\s+(.*)$/', $line, $matches)) {
                                                    $instructions[] = $matches[1];
                                                } else if (!empty($instructions)) {
                                                    $instructions[count($instructions) - 1] .= " " . $line;
                                                }
                                            }
                                        @endphp

                                        @if(!empty($tableRows) && !empty($transactions))

                                            <div class="border-bottom pb-2 mb-4">
                                                <h4 class="font-weight-bold text-dark mb-1" style="letter-spacing: 0.5px;">
                                                    {{ $dynamicTitle }}
                                                </h4>
                                                <p class="text-secondary mb-0">Mata Ujian: {{ $exam->name }}</p>
                                            </div>

                                            <p class="text-dark mb-4" style="font-size: 1.05rem; line-height: 1.6;">
                                                {{ $dynamicParagraph }}
                                            </p>

                                            <div class="table-responsive mb-4 shadow-sm rounded border">
                                                <table class="table table-bordered table-hover mb-0" style="font-size: 1rem;">
                                                    <thead class="bg-light text-dark font-weight-bold">
                                                        <tr>
                                                            <th style="width: 60%;" class="py-3 px-4">Nama Perkiraan / Akun</th>
                                                            <th style="width: 40%;" class="py-3 px-4 text-left">Saldo Berjalan (Rp)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($tableRows as $row)
                                                            <tr>
                                                                <td class="py-2.5 px-4 text-secondary font-weight-normal">{{ $row['account'] }}</td>
                                                                <td class="py-2.5 px-4 text-dark font-weight-bold text-left">{{ $row['balance'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <h6 class="font-weight-bold text-dark mb-3" style="font-size: 1.05rem;">Transaksi selama periode ini:</h6>
                                            <ol class="text-dark pl-4 mb-5" style="font-size: 1rem; line-height: 1.85;">
                                                @foreach($transactions as $tx)
                                                    <li class="mb-2 pl-2 text-secondary font-weight-normal">
                                                        <span class="text-dark">{{ $tx }}</span>
                                                    </li>
                                                @endforeach
                                            </ol>

                                            <div class="p-4 rounded border-success" style="background-color: #f4faf6; border: 1px solid #c3e6cb !important; border-left: 5px solid #28a745 !important;">
                                                <h6 class="font-weight-bold text-success mb-3" style="font-size: 1.05rem;">
                                                    <i class="fas fa-clipboard-list mr-2"></i>Tugas / Instruksi Kerja:
                                                </h6>
                                                <ol class="text-dark pl-4 mb-0" style="font-size: 0.95rem; line-height: 1.75;">
                                                    @foreach($instructions as $inst)
                                                        <li class="mb-2 text-success font-weight-bold">
                                                            <span class="text-dark font-weight-normal">{{ $inst }}</span>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </div>

                                        @else
                                            <div class="text-dark p-4 bg-light border rounded" style="font-size: 1.05rem; line-height: 1.8; white-space: pre-line;">
                                                {{ $fullText }}
                                            </div>
                                        @endif

                                    @endforeach
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="col-12 col-lg-7 text-center text-lg-left d-md-flex align-items-center mb-3 mb-lg-0">
                                            <div class="text-primary mr-3 mb-2 mb-md-0">
                                                <i class="fas fa-folder-open fa-3x"></i>
                                            </div>
                                            <div>
                                                <h5 class="font-weight-bold mb-1 text-dark">Lembar Kerja Jawaban Peserta</h5>
                                                <p class="small text-muted mb-0">Anda dapat mengunggah **lebih dari 1 file** (Excel, PDF, Word). File akan langsung tersimpan otomatis.</p>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-5">
                                            <div class="custom-file shadow-sm mb-2">
                                                <input type="file" class="custom-file-input" id="answer-file-input" accept=".xlsx,.xls,.pdf,.doc,.docx" multiple>
                                                <label class="custom-file-label text-left font-weight-normal" for="answer-file-input">Pilih satu atau beberapa file...</label>
                                            </div>
                                            <div id="upload-alert" class="alert small p-2 text-center mb-0" style="display: none; border-radius: 6px;"></div>
                                        </div>
                                    </div>

                                    <div id="uploaded-files-box" class="mt-3 p-3 bg-light rounded border" style="display: none;">
                                        <h6 class="small font-weight-bold text-secondary mb-2"><i class="fas fa-paperclip mr-1"></i> File Terunggah:</h6>
                                        <div id="uploaded-files-list" class="d-flex flex-wrap" style="gap: 10px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    {{-- ========================================== --}}
                    {{-- UI UNTUK SOAL TABEL ANGKA (PENJUMLAHAN) --}}
                    {{-- ========================================== --}}
                    @elseif($exam->type == 'angka_akuntansi')
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
                                    
                                    // Parse table data jika ada
                                    $tableData = json_decode($q->question_text, true);
                                    $isStructuredTable = is_array($tableData) && isset($tableData['table']);
                                @endphp
                                <div class="mb-5 pb-4 border-bottom question-block" data-qnum="{{ $q->number }}">
                                    <h5 class="font-weight-bold mb-3 text-dark">
                                        <span class="badge badge-success mr-2">Soal {{ $q->number }}</span>
                                    </h5>
                                    
                                    {{-- Tampilkan instruksi --}}
                                    @if($isStructuredTable && isset($tableData['question']))
                                        <div class="card border-0 bg-light mb-3 p-3" style="border-left: 3px solid #28a745;">
                                            <p class="mb-0 text-dark font-weight-bold">{{ $tableData['question'] }}</p>
                                        </div>
                                    @endif
                                    
                                    {{-- Render Tabel Structured --}}
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
                                                    {{-- Data Rows (Menampilkan angka & Input Mendatar di Kolom Terakhir) --}}
                                                    @foreach($tableData['table']['rows'] as $rowIdx => $row)
                                                        <tr style="background-color: {{ $rowIdx % 2 == 0 ? '#ffffff' : '#f8f9fa' }};">
                                                            @foreach($row as $colIdx => $cellValue)
                                                                <td class="py-2 px-1 align-middle" style="font-family: 'Courier New', monospace; font-weight: 500;">
                                                                    
                                                                    {{-- Cek apakah ini adalah kolom paling kanan (Kolom 9) --}}
                                                                    @if($colIdx == count($row) - 1)
                                                                        {{-- Render Kotak Input untuk Penjumlahan Mendatar --}}
                                                                        <input type="text" 
                                                                            class="form-control form-control-sm table-number-cell" 
                                                                            name="answer_{{ $q->number }}_row_{{ $rowIdx }}" 
                                                                            placeholder="..." 
                                                                            style="font-size: 0.85rem; text-align: center; border-radius: 4px; font-weight: bold; border: 1px solid #17a2b8;"
                                                                            data-question="{{ $q->number }}">
                                                                    @else
                                                                        {{-- Render Angka Biasa --}}
                                                                        {{ $cellValue }}
                                                                    @endif

                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                    
                                                    {{-- Empty Row untuk Input Jawaban Menurun (Di paling bawah) --}}
                                                    <tr style="background-color: #e3f2fd; border-top: 3px solid #28a745;">
                                                        @for($col = 0; $col < count($tableData['table']['headers']); $col++)
                                                            <td class="py-2 px-1">
                                                                <input type="text" 
                                                                    class="form-control form-control-sm table-number-cell" 
                                                                    name="answer_{{ $q->number }}_col_{{ $col }}" 
                                                                    placeholder="..." 
                                                                    style="font-size: 0.85rem; text-align: center; border-radius: 4px; font-weight: bold; border: 1px solid #28a745;"
                                                                    data-question="{{ $q->number }}">
                                                            </td>
                                                        @endfor
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        {{-- Fallback jika tidak ada structured table data --}}
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

                    {{-- ========================================== --}}
                    {{-- UI UNTUK SOAL BERNOMOR (ESSAY/URAIAN)  --}}
                    {{-- ========================================== --}}
                    @elseif($exam->type == 'uraian')
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
                                <div class="mb-5 pb-4 border-bottom question-block" data-qnum="{{ $q->number }}">
                                    <h5 class="font-weight-bold mb-3 text-dark">
                                        <span class="badge badge-info mr-2">No. {{ $q->number }}</span>
                                    </h5>
                                    <div class="card border-0 bg-light mb-3 p-3">
                                        <p class="mb-0 text-dark" style="line-height: 1.6; white-space: pre-line;">{{ $q->question_text }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold text-secondary mb-2">Jawaban Anda:</label>
                                        <textarea class="form-control uraian-textarea" name="answer_{{ $q->number }}" rows="4" placeholder="Ketik jawaban Anda di sini..." style="font-size: 0.95rem; border-radius: 6px;">{{ $answerText }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    {{-- ========================================== --}}
                    {{-- UI STANDAR UNTUK MBTI, VAK, EPPS, DLL (DARI DB) --}}
                    {{-- ========================================== --}}
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
                            <style>
                                .pembatas {
                                    margin: 2rem 0;
                                }
                            </style>
                            @foreach($exam->questions as $q)
                                <div class="mb-4 pb-3 border-bottom question-block" data-qnum="{{ $q->number }}">
                                    <h5 class="font-weight-bold mb-3">Soal No. {{ $q->number }}</h5>
                                    <p>{{ $q->question_text }}</p>

                                    @foreach($q->options as $key => $text)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input std-radio" type="radio" name="answer_{{ $q->number }}" value="{{ strtoupper($key) }}">
                                            <label class="form-check-label" style="cursor: pointer;">
                                                {{ strtoupper($key) }}. {{ $text }}
                                            </label>
                                        </div>
                                    @endforeach
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


        const isKasus = "{{ $exam->type }}" === 'kasus_akuntansi';
            let uploadedFilesArray = {!! $session->answer_file ? $session->answer_file : '[]' !!};

            if (isKasus) {
                const fileInput = document.getElementById('answer-file-input');
                const uploadAlert = document.getElementById('upload-alert');
                const filesBox = document.getElementById('uploaded-files-box');
                const filesList = document.getElementById('uploaded-files-list');
                const fileLabel = document.querySelector('.custom-file-label');

                // 1. FUNGSI UNTUK MERENDER BADGE FILE + TOMBOL CANCEL
                function renderFileList(files) {
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

        // ==========================================
        // 5. LOGIKA VALIDASI & SUBMIT
        // ==========================================
        const btnSubmit = document.getElementById('btn-submit-exam');

        // Fungsi untuk mengecek apakah semua soal sudah terisi
        function checkCompletion() {
            if (isKasus) {
                // Jika semua file dihapus (kosong), gembok submit akan otomatis aktif kembali
                if (!uploadedFilesArray || uploadedFilesArray.length === 0) {
                    return ['Anda belum mengunggah file lembar jawaban apa pun'];
                }
                return [];
            }

            let unanswered = [];
            let isDisc = "{{ $exam->type }}" === 'disc';
            let isuraian = "{{ $exam->type }}" === 'uraian';
            let isTableNumber = "{{ $exam->type }}" === 'angka_akuntansi';

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
                    // Untuk uraian: Cek apakah textarea sudah diisi
                    let textarea = document.querySelector(`textarea[name="answer_${qNum}"]`);
                    if (!textarea || !textarea.value.trim()) {
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

        btnSubmit.addEventListener('click', function() {
            // 1. Jalankan Validasi
            let missingAnswers = checkCompletion();

            if (missingAnswers.length > 0) {
                // Jika ada yang kosong, tampilkan peringatan dan batalkan submit
                Swal.fire({
                    title: 'Belum Lengkap!',
                    text: `Anda belum menjawab soal nomor: ${missingAnswers.join(', ')}. Silakan lengkapi semua jawaban sebelum mengumpulkan.`,
                    icon: 'error',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Oke, Saya Lengkapi'
                });
                return; // Berhenti di sini, jangan lanjut ke konfirmasi yakin/tidak
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

/* Camera status badge wrap */
#camera-status { word-break: break-word; max-width: 90vw; display: inline-block; }
</style>
@endsection