@extends('layouts.app')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="w-full overflow-x-auto">
            <div class="card mb-4 shadow-sm border-0">
                <div class="fixed-top card-body d-flex justify-content-between align-items-center flex-wrap bg-white rounded" style="gap: 0.5rem; z-index: 1030;">
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
                    <div id="fixed-content-spacer"></div>

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
                        <div class="card instruksi-fixed-box border-0 shadow-sm m-3 overflow-hidden" style="border-left: 5px solid #ffc107 !important;">
                            <div class="card-body bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <h5 class="mb-0 font-weight-bold text-dark">Instruksi Pengerjaan</h5>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-light border instruksi-toggle-btn" title="Sembunyikan/tampilkan instruksi">
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                </div>

                                <div class="instruksi-fixed-content">
                                <p class="text-secondary mb-3">
                                    {{ $exam->description }}
                                </p>
                                </div>

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
                                    @php
                                        // Ambil jawaban peserta dari DB jika ada (agar saat refresh halaman tidak hilang)
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
                                        
                                        {{-- Kolom Radio MOST --}}
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

                                        {{-- Kolom Radio LEAST --}}
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

                    @elseif($exam->type == 'soal_kasus')
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
                                    <!-- <div class="row align-items-center">
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
                                    </div> -->

                                    <div id="uploaded-files-box" class="mt-3 p-3 bg-light rounded border" style="display: none;">
                                        <h6 class="small font-weight-bold text-secondary mb-2"><i class="fas fa-paperclip mr-1"></i> File Terunggah:</h6>
                                        <div id="uploaded-files-list" class="d-flex flex-wrap" style="gap: 10px;"></div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm mb-4 align-items-center" style="border-radius: 12px;">
                                    <div class="card-body p-4">
                                        <h5 class="font-weight-bold mb-3 text-dark">Tabel Jurnal</h5>
                                        <p class="small text-muted mb-3">Isi tabel di bawah ini. Anda bisa klik kanan untuk menambah/menghapus baris.</p>
                                        
                                        <div id="jurnal-table" data-qnum="{{ $q->number }}"></div>

                                        <input type="hidden" name="answers" id="answers">
                                    </div>
                                </div>                                
                            </div>
                        </div>

                    {{-- ========================================== --}}
                    {{-- UI UNTUK SOAL CFIT) --}}
                    {{-- ========================================== --}}
                    @elseif($exam->type == 'cfit')
                        @foreach($exam->questions as $q)

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

                            <div class="mb-5 pb-4 border-bottom question-block" data-qnum="{{ $q->number }}">
                                <h5 class="font-weight-bold mb-3">Soal No. {{ $q->number }}</h5>
                                
                                {{-- Render Gambar CFIT yang sudah di-generate --}}
                                @if($q->question_image)
                                    <img src="{{ asset($q->question_image) }}" class="img-fluid mb-3 border rounded shadow-sm" alt="Soal CFIT">
                                @endif

                                {{-- Render Opsi Pilihan A, B, C, D... --}}
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

                    {{-- ========================================== --}}
                    {{-- UI UNTUK SOAL TABEL ANGKA (PENJUMLAHAN) --}}
                    {{-- ========================================== --}}
                    @elseif($exam->type == 'angka')
                        <div class="p-4">
                            <div class="card instruksi-fixed-box border-0 shadow-sm m-3 overflow-hidden" style="border-left: 5px solid #28a745 !important;">
                                <div class="card-body bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-calculator"></i>
                                            </div>
                                            <h5 class="mb-0 font-weight-bold text-dark">Instruksi Pengerjaan</h5>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light border instruksi-toggle-btn" title="Sembunyikan/tampilkan instruksi">
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                    </div>

                                    <div class="instruksi-fixed-content">
                                    <p class="text-secondary mb-3">
                                        {{ $exam->description }}
                                    </p>
                                    </div>
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
                                                                            style="font-size: 0.85rem; text-align: center; border-radius: 4px; font-weight: bold; border: 1px solid #17a2b8; min-width: 120px;"
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
                                                            <td class="py-2 px-1 text-center align-middle">
                                                                
                                                                {{-- PERBAIKAN: Jika ini adalah kolom paling kanan, jangan render input! --}}
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

                    {{-- =============================================== --}}
                    {{-- UI UNTUK SOAL BERNOMOR (ESSAY/URAIAN/KRAEPLIN)  --}}
                    {{-- =============================================== --}}
                    @elseif($exam->type == 'uraian' || $exam->type == 'tes_kraeplin')
                        <div class="p-4">
                            <div class="card instruksi-fixed-box border-0 shadow-sm m-3 overflow-hidden" style="border-left: 5px solid #17a2b8 !important;">
                                <div class="card-body bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <h5 class="mb-0 font-weight-bold text-dark">Instruksi Pengerjaan</h5>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light border instruksi-toggle-btn" title="Sembunyikan/tampilkan instruksi">
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                    </div>

                                    <div class="instruksi-fixed-content">
                                    <p class="text-secondary mb-3">
                                        <strong>{{ $exam->description }}</strong>
                                    </p>
                                    </div>
                                </div>
                            </div>
                            <div class="pembatas border-bottom"></div>
                            @foreach($exam->questions as $q)
                                @php
                                    $userAnswer = $session->userAnswers->where('question_number', $q->number)->first();
                                    $answerText = $userAnswer ? ($userAnswer->answers['answer_text'] ?? '') : '';
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

                                    {{-- LOGIKA BARU: CEK OTOMATIS DARI TEKS SOAL --}}
                                    @php
                                        $isDrawing = (stripos($q->question_text, 'gambarkan') !== false) || 
                                                     (stripos($q->question_text, 'gambar') !== false) || 
                                                     (stripos($q->question_text, 'skema') !== false);
                                    @endphp

                                    @if($isDrawing)
                                        <div class="form-group">
                                            <label class="font-weight-bold text-secondary mb-2">Buat Skema / Gambar Anda:</label>
                                            <div class="drawing-container border rounded bg-light mb-3 shadow-sm overflow-hidden">
                                                
                                                {{-- TOOLBAR ALA MS PAINT --}}
                                                <div class="paint-toolbar bg-white border-bottom p-2 d-flex flex-wrap align-items-center" style="gap: 8px;">
                                                    
                                                    {{-- Tools --}}
                                                    <button type="button" class="btn btn-sm btn-dark paint-tool-btn" data-tool="draw" onclick="setTool({{ $q->number }}, 'draw', this)">✏️ Pen</button>
                                                    <button type="button" class="btn btn-sm btn-outline-dark paint-tool-btn" data-tool="line" onclick="setTool({{ $q->number }}, 'line', this)">📏 Garis</button>
                                                    <button type="button" class="btn btn-sm btn-outline-dark paint-tool-btn" data-tool="rect" onclick="setTool({{ $q->number }}, 'rect', this)">⬜ Kotak</button>
                                                    <button type="button" class="btn btn-sm btn-outline-dark paint-tool-btn" data-tool="circle" onclick="setTool({{ $q->number }}, 'circle', this)">⭕ Lingkaran</button>

                                                    <button type="button" class="btn btn-sm btn-outline-dark paint-tool-btn" data-tool="text" onclick="setTool({{ $q->number }}, 'text', this)">🅰️ Teks</button>
                                                    <button type="button" class="btn btn-sm btn-outline-dark paint-tool-btn" data-tool="eraser" onclick="setTool({{ $q->number }}, 'eraser', this)">🧽 Hapus</button>

                                                    <div class="border-left mx-1" style="height: 24px;"></div>

                                                    {{-- Pengaturan Warna & Ketebalan --}}
                                                    <input type="color" id="color_{{ $q->number }}" value="#000000" onchange="updateBrush({{ $q->number }})" class="p-0 border-0" style="width: 30px; height: 30px; cursor: pointer;" title="Pilih Warna">
                                                    
                                                    <input type="range" id="size_{{ $q->number }}" min="1" max="15" value="3" onchange="updateBrush({{ $q->number }})" style="width: 80px; cursor: pointer;" title="Ketebalan Garis">

                                                    <div class="border-left mx-1" style="height: 24px;"></div>

                                                    {{-- Hapus Semua --}}
                                                    <button type="button" class="btn btn-sm btn-danger ml-auto" onclick="clearCanvas({{ $q->number }})">❌ Bersihkan</button>
                                                </div>

                                                {{-- Area Kanvas --}}
                                                <div style="background-color: #ffffff; cursor: crosshair;">
                                                    <canvas id="canvas_{{ $q->number }}" width="800" height="400" style="width:100%; touch-action: none;"></canvas>
                                                </div>
                                            </div>
                                            
                                            <input type="hidden" name="answer_{{ $q->number }}" id="drawing_input_{{ $q->number }}" value="{{ $answerText }}">
                                        </div>
                                    @else
                                        {{-- TAMPILKAN INPUT TEKS/KRAEPLIN SEPERTI BIASA --}}
                                        <div class="form-group">
                                            <label class="font-weight-bold text-secondary mb-2">Jawaban Anda:</label>
                                            @if($exam->type == 'tes_kraeplin')
                                                <input type="text" class="form-control uraian-textarea" name="answer_{{ $q->number }}" value="{{ $answerText }}" style="font-size: 1.1rem; border-radius: 6px; font-weight: bold; width: 100%; max-width: 300px;">
                                            @else
                                                <textarea class="form-control uraian-textarea" name="answer_{{ $q->number }}" rows="4">{{ $answerText }}</textarea>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                    {{-- ========================================== --}}
                    {{-- UI STANDAR UNTUK MBTI, VAK, EPPS, DLL (DARI DB) --}}
                    {{-- ========================================== --}}
                    @else
                        <div class="p-4">
                            <div class="card instruksi-fixed-box border-0 shadow-sm m-3 overflow-hidden" style="border-left: 5px solid #ffc107 !important;">
                                <div class="card-body bg-light">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                            <h5 class="mb-0 font-weight-bold text-dark">Instruksi Pengerjaan</h5>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-light border instruksi-toggle-btn" title="Sembunyikan/tampilkan instruksi">
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                    </div>

                                    <div class="instruksi-fixed-content">
                                    <p class="text-secondary mb-3">
                                        <strong>{{ $exam->description }}</strong>
                                    </p>
                                    </div>
                                </div>
                            </div>
                            <div class="pembatas border-bottom "></div>
                            <style>
                                .pembatas {
                                    margin: 2rem 0;
                                }
                            </style>
                            @foreach($exam->questions as $q)
                                @php
                                    $hasImageOpts  = $q->has_image_options ?? false;
                                    $questionImage = $q->question_image ?? null;
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

                                <div class="mb-4 pb-3 border-bottom question-block" data-qnum="{{ $q->number }}">
                                    <h5 class="font-weight-bold mb-3">Soal No. {{ $q->number }}</h5>

                                    @if($hasImageOpts && $questionImage)
                                        {{-- Soal Bergambar: 1 gambar utuh soal + pilihan A-E --}}
                                        <img src="{{ $questionImage }}"
                                            alt="Soal {{ $q->number }}"
                                            class="img-fluid d-block mb-3"
                                            style="max-width:100%; border:1px solid #e9ecef; border-radius:6px; background:#fff;">
                                        <div class="mt-2">
                                            @foreach(['A','B','C','D','E'] as $letter)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input std-radio" type="radio"
                                                        name="answer_{{ $q->number }}"
                                                        value="{{ $letter }}">
                                                    <label class="form-check-label" style="cursor:pointer; font-weight:600;">
                                                        {{ $letter }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                    @else
                                        {{-- Soal Teks biasa --}}
                                        <p>{!! $q->question_text !!}</p>
                                        @foreach($q->options as $key => $opt)
                                            @php
                                                $letter = strtoupper($key);
                                                $text   = is_array($opt) ? ($opt['value'] ?? '') : $opt;
                                            @endphp
                                            <div class="form-check mb-2">
                                                <input class="form-check-input std-radio" type="radio"
                                                    name="answer_{{ $q->number }}"
                                                    value="{{ $letter }}">
                                                <label class="form-check-label" style="cursor:pointer;">
                                                    {{ $letter }}. {{ $text }}
                                                </label>
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
    window.canvases = {};
    window.canvasStates = {};

    document.addEventListener("DOMContentLoaded", function() {

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const examSessionId = {{ $session->id }};
        const examType = "{{ $exam->type }}";

        // ==========================================
        // 1. FUNGSI AJAX REUSABLE (Simpan Jawaban)
        // ==========================================
        window.saveAnswerAjax = function(qNum, jsonAnswer) {
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
        };

        // ==========================================
        // 2. HANDLER INPUT (DISC, RADIO STANDAR, URAIAN, TABEL ANGKA)
        // ==========================================
        document.querySelectorAll('.disc-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                let block = this.closest('.question-block');
                let qNum = block.dataset.qnum;
                let type = this.dataset.type;
                let val = this.value;
                let oppositeType = (type === 'most') ? 'least' : 'most';
                document.querySelectorAll(`input[name="${oppositeType}_${qNum}"]`).forEach(el => {
                    el.disabled = false;
                    if (el.value === val) {
                        el.disabled = true;
                        if (el.checked) el.checked = false;
                    }
                });
                let mostVal = document.querySelector(`input[name="most_${qNum}"]:checked`)?.value || null;
                let leastVal = document.querySelector(`input[name="least_${qNum}"]:checked`)?.value || null;
                window.saveAnswerAjax(qNum, { most: mostVal, least: leastVal });
            });
        });

        document.querySelectorAll('.std-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                let qNum = this.closest('.question-block').dataset.qnum;
                window.saveAnswerAjax(qNum, { selected: this.value });
            });
        });

        document.querySelectorAll('.uraian-textarea, .table-number-input').forEach(input => {
            input.addEventListener('input', function() {
                let qNum = this.closest('.question-block').dataset.qnum;
                window.saveAnswerAjax(qNum, { answer_text: this.value });
            });
        });

        document.querySelectorAll('.table-number-cell').forEach(cell => {
            cell.addEventListener('input', function() {
                let angkaSaja = this.value.replace(/\D/g, '');
                let formatTitik = angkaSaja.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                this.value = formatTitik;

                let qNum = this.dataset.question;
                let block = document.querySelector(`.question-block[data-qnum="${qNum}"]`);
                let cells = block.querySelectorAll('.table-number-cell');
                let allValues = [];
                let detailsObj = {};

                cells.forEach(c => {
                    let val = c.value.trim();
                    if (val !== '') {
                        allValues.push(val);
                        let nameParts = c.name.split('_');
                        if (nameParts.length >= 4) {
                            let type = nameParts[2];
                            let index = parseInt(nameParts[3]) + 1;
                            let label = type === 'row' ? `Mendatar (Baris ${index})` : `Menurun (Kolom ${index})`;
                            detailsObj[label] = val;
                        }
                    }
                });

                let combinedValue = allValues.join(', ');
                let finalInput = block.querySelector('.table-number-input');
                if (finalInput) finalInput.value = combinedValue;

                window.saveAnswerAjax(qNum, { answer_text: combinedValue, details: detailsObj });
            });
        });

        // ==========================================
        // 3. TIMER MUNDUR
        // ==========================================
        let remainingSeconds = {{ $remainingSeconds }};
        const timerDisplay = document.getElementById('timer-display');
        const timerInterval = setInterval(updateTimer, 1000);

        function updateTimer() {
            if (remainingSeconds <= 0) {
                clearInterval(timerInterval);
                timerDisplay.innerHTML = "WAKTU HABIS!";
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Sistem akan mengumpulkan jawaban Anda secara otomatis.',
                    icon: 'warning',
                    timer: 2500,
                    showConfirmButton: false,
                    willClose: () => { finishExam(); }
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
        const canvasSnap = document.getElementById('snapshot-canvas');
        const cameraStatus = document.getElementById('camera-status');

        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(function(stream) {
                video.srcObject = stream;
                cameraStatus.innerHTML = "🟢 Kamera aktif (Ujian diawasi)";
                cameraStatus.classList.replace('badge-secondary', 'badge-success');
                setTimeout(takeSnapshotAndSend, 1000);
                scheduleNextSnapshot();
            })
            .catch(function() {
                cameraStatus.innerHTML = "❌ Akses Kamera Ditolak!";
                cameraStatus.classList.replace('badge-secondary', 'badge-danger');
                Swal.fire('Kamera Wajib!', 'Ujian ini memerlukan kamera untuk pengawasan.', 'error');
            });

        function takeSnapshotAndSend() {
            if (!video.srcObject) return;
            canvasSnap.width = 640; canvasSnap.height = 480;
            canvasSnap.getContext('2d').drawImage(video, 0, 0, canvasSnap.width, canvasSnap.height);
            let base64Image = canvasSnap.toDataURL('image/jpeg', 0.6);
            fetch("{{ url('/proctoring/snap') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ exam_session_id: examSessionId, image: base64Image })
            });
        }

        function scheduleNextSnapshot() {
            let timeout = Math.floor(Math.random() * (300000 - 60000 + 1)) + 60000;
            setTimeout(() => { takeSnapshotAndSend(); scheduleNextSnapshot(); }, timeout);
        }

        // ==========================================
        // 5. SOAL KASUS AKUNTANSI (UPLOAD FILE & TABEL JURNAL)
        // ==========================================
        const isKasus = examType === 'soal_kasus';
        let uploadedFilesArray = {!! $session->answer_file ? $session->answer_file : '[]' !!};
        var tableInstance; // dideklarasikan di scope luar agar bisa dipakai checkCompletion()

        if (isKasus) {
            const fileInput = document.getElementById('answer-file-input');
            const uploadAlert = document.getElementById('upload-alert');
            const filesBox = document.getElementById('uploaded-files-box');
            const filesList = document.getElementById('uploaded-files-list');
            const fileLabel = document.querySelector('.custom-file-label');

            function renderFileList(files) {
                if (!filesBox) return;
                if (!files || files.length === 0) {
                    filesBox.style.display = 'none';
                    if (fileLabel) fileLabel.innerHTML = "Pilih satu atau beberapa file...";
                    return;
                }
                filesBox.style.display = 'block';
                filesList.innerHTML = '';
                files.forEach(file => {
                    filesList.innerHTML += `
                        <div class="badge badge-white border text-dark p-2 shadow-sm d-flex align-items-center rounded" style="font-size: 0.85rem; gap: 10px;">
                            <span>📄</span>
                            <span class="font-weight-normal">${file.name}</span>
                            <button type="button" class="btn-delete-file" data-path="${file.path}" style="border:none;background:none;color:#dc3545;font-size:1.2rem;line-height:1;margin-left:5px;cursor:pointer;font-weight:bold;">&times;</button>
                        </div>`;
                });
                attachDeleteEvents();
            }

            function attachDeleteEvents() {
                document.querySelectorAll('.btn-delete-file').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const filePath = this.dataset.path;
                        Swal.fire({
                            title: 'Batalkan file ini?', text: "File akan dihapus dari server lembar jawaban Anda.", icon: 'warning',
                            showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let formData = new FormData();
                                formData.append('exam_session_id', examSessionId);
                                formData.append('file_path', filePath);
                                fetch("{{ route('exam.delete-file') }}", { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: formData })
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.status === 'success') {
                                            uploadedFilesArray = data.files;
                                            renderFileList(uploadedFilesArray);
                                            if (uploadedFilesArray.length > 0) fileLabel.innerHTML = `${uploadedFilesArray.length} file terpilih...`;
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

            renderFileList(uploadedFilesArray);

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files.length === 0) return;
                    let formData = new FormData();
                    formData.append('exam_session_id', examSessionId);
                    for (let i = 0; i < this.files.length; i++) formData.append('answer_files[]', this.files[i]);

                    uploadAlert.style.display = 'block';
                    uploadAlert.className = 'alert alert-info small p-2';
                    uploadAlert.innerHTML = '⏳ Sedang mengunggah berkas...';

                    fetch("{{ route('exam.upload-file') }}", {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    })
                        .then(async res => {
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
                            uploadAlert.className = 'alert alert-danger small p-2';
                            uploadAlert.innerHTML = '❌ ' + err.message;
                            console.error(err);
                        });
                });
            }

            const jurnalContainer = document.getElementById('jurnal-table');
            if (jurnalContainer) {
                let qNumJurnal = jurnalContainer.dataset.qnum;
                tableInstance = jspreadsheet(jurnalContainer, {
                    data: [['', '', '', '', ''], ['', '', '', '', '']],
                    columns: [
                        { type: 'calendar', title: 'Tanggal', width: 120, options: { format: 'DD/MM/YYYY' } },
                        { type: 'text', title: 'Keterangan', width: 250 },
                        { type: 'text', title: 'Ref', width: 80 },
                        { type: 'text', title: 'Debit (Rp)', width: 150, mask: '#.##0,00', align: 'right' },
                        { type: 'text', title: 'Kredit (Rp)', width: 150, mask: '#.##0,00', align: 'right' },
                    ],
                    minDimensions: [5, 5],
                    allowInsertRow: true, allowManualInsertRow: true, allowDeleteRow: true,
                    allowInsertColumn: true, allowManualInsertColumn: true, allowDeleteColumn: true, wordWrap: true,
                    onchange: function() { window.saveAnswerAjax(qNumJurnal, tableInstance.getData()); },
                    oninsertrow: function() { window.saveAnswerAjax(qNumJurnal, tableInstance.getData()); },
                    ondeleterow: function() { window.saveAnswerAjax(qNumJurnal, tableInstance.getData()); }
                });
            }
        }

        // ==========================================
        // 6. VALIDASI & SUBMIT (SATU-SATUNYA LISTENER)
        // ==========================================
        const btnSubmit = document.getElementById('btn-submit-exam');

        function checkCompletion() {
            if (isKasus) {
                let hasFile = uploadedFilesArray && uploadedFilesArray.length > 0;
                let hasTableData = false;
                if (tableInstance) {
                    let tableData = tableInstance.getData();
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
                if (!hasFile && !hasTableData) return ['kasus_kosong'];
                return [];
            }

            let unanswered = [];
            let isDisc = examType === 'disc';
            let isUraian = examType === 'uraian' || examType === 'tes_kraeplin';
            let isTableNumber = examType === 'angka';

            let questionNumbers = [...new Set(Array.from(document.querySelectorAll('.question-block')).map(el => el.dataset.qnum))];

            questionNumbers.forEach(qNum => {
                let block = document.querySelector(`.question-block[data-qnum="${qNum}"]`);

                if (isDisc) {
                    let mostSelected = document.querySelector(`input[name="most_${qNum}"]:checked`);
                    let leastSelected = document.querySelector(`input[name="least_${qNum}"]:checked`);
                    if (!mostSelected || !leastSelected) unanswered.push(qNum);
                } else if (isTableNumber) {
                    let cells = block.querySelectorAll('.table-number-cell');
                    let hasAnswer = Array.from(cells).some(c => c.value.trim() !== '');
                    if (!hasAnswer) unanswered.push(qNum);
                } else if (isUraian) {
                    let inputEl = document.querySelector(`[name="answer_${qNum}"]`);
                    let isDrawingCanvas = block.querySelector(`canvas[id="canvas_${qNum}"]`);
                    if (!isDrawingCanvas && (!inputEl || !inputEl.value.trim())) {
                        unanswered.push(qNum);
                    } else if (isDrawingCanvas && (!inputEl || inputEl.value === '')) {
                        unanswered.push(qNum);
                    }
                } else {
                    let selected = document.querySelector(`input[name="answer_${qNum}"]:checked`);
                    if (!selected) unanswered.push(qNum);
                }
            });

            return unanswered;
        }

        window.selectImageOption = function(imgEl, qNum, letter) {
            document.querySelectorAll(`[data-qnum="${qNum}"] .img-option`).forEach(img => {
                img.style.borderColor = '#dee2e6';
                img.style.boxShadow = 'none';
            });
            imgEl.style.borderColor = '#28a745';
            imgEl.style.boxShadow = '0 0 0 3px rgba(40,167,69,0.25)';
            let radio = document.getElementById(`opt_${qNum}_${letter}`);
            if (radio) {
                radio.checked = true;
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };

        btnSubmit.addEventListener('click', function() {
            let missingAnswers = checkCompletion();

            if (missingAnswers.length > 0) {
                let warningText = `Anda belum menjawab soal nomor: ${missingAnswers.join(', ')}. Silakan lengkapi semua jawaban sebelum mengumpulkan.`;
                if (isKasus && missingAnswers[0] === 'kasus_kosong') {
                    warningText = "Anda belum mengisi Tabel Jurnal Umum atau mengunggah file lembar jawaban. Silakan lengkapi salah satunya sebelum mengumpulkan.";
                }
                Swal.fire({
                    title: 'Belum Lengkap!', text: warningText, icon: 'error',
                    confirmButtonColor: '#d33', confirmButtonText: 'Oke, Saya Lengkapi'
                });
                return;
            }

            Swal.fire({
                title: 'Kumpulkan Ujian?',
                text: "Anda telah menjawab semua soal. Yakin ingin mengumpulkan sekarang?",
                icon: 'question', showCancelButton: true,
                confirmButtonColor: '#28a745', cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kumpulkan Sekarang!', cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) finishExam();
            });
        });

        function finishExam() {
            Swal.fire({
                title: 'Memproses...', text: 'Mohon tunggu sebentar', allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            takeSnapshotAndSend();
            setTimeout(() => {
                let url = "{{ route('exam.finish', ['session_id' => 'PLACEHOLDER']) }}";
                window.location.href = url.replace('PLACEHOLDER', examSessionId);
            }, 500);
        }

        // ==========================================
        // 7. CANVAS PAINT (DRAWING TOOL)
        // ==========================================
        document.querySelectorAll('canvas[id^="canvas_"]').forEach(canvasEl => {
            let qNum = canvasEl.id.split('_')[1];
            if (canvasEl.dataset.fabricInitialized === '1') return;
            canvasEl.dataset.fabricInitialized = '1';

            let canvas = new fabric.Canvas(canvasEl.id, {
                isDrawingMode: true, selection: false, renderOnAddRemove: true, enableRetinaScaling: false
            });
            canvas.freeDrawingBrush.width = 3;
            canvas.freeDrawingBrush.color = '#000000';

            window.canvases[qNum] = canvas;
            window.canvasStates[qNum] = { tool: 'draw', isDragging: false, shape: null, startX: 0, startY: 0 };

            canvas.on('mouse:down', function(o) {
                let state = window.canvasStates[qNum];
                if (state.tool === 'draw' || state.tool === 'eraser') return;

                let pointer = canvas.getPointer(o.e);
                state.startX = pointer.x;
                state.startY = pointer.y;

                let color = document.getElementById('color_' + qNum).value;
                let size = parseInt(document.getElementById('size_' + qNum).value);

                if (state.tool === 'text') {
                    Swal.fire({
                        title: 'Masukkan Teks', input: 'text', inputPlaceholder: 'Ketik tulisan di sini...',
                        showCancelButton: true, confirmButtonText: 'Tempelkan', cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed && result.value) {
                            let textObj = new fabric.Text(result.value, {
                                left: state.startX, top: state.startY, fill: color,
                                fontSize: 16 + (size * 2), selectable: false
                            });
                            canvas.add(textObj);
                            canvas.requestRenderAll();
                            window.updateDrawingInput(qNum);
                        }
                    });
                    return;
                }

                state.isDragging = true;

                if (state.tool === 'rect') {
                    state.shape = new fabric.Rect({ left: state.startX, top: state.startY, width: 0, height: 0, fill: 'transparent', stroke: color, strokeWidth: size, selectable: false });
                    canvas.add(state.shape);
                } else if (state.tool === 'circle') {
                    state.shape = new fabric.Circle({ left: state.startX, top: state.startY, radius: 0, fill: 'transparent', stroke: color, strokeWidth: size, selectable: false });
                    canvas.add(state.shape);
                } else if (state.tool === 'line') {
                    state.shape = new fabric.Line([state.startX, state.startY, state.startX, state.startY], { stroke: color, strokeWidth: size, selectable: false });
                    canvas.add(state.shape);
                }
                canvas.requestRenderAll();
            });

            canvas.on('mouse:move', function(o) {
                let state = window.canvasStates[qNum];
                if (!state.isDragging) return;
                let pointer = canvas.getPointer(o.e);

                if (state.tool === 'rect') {
                    state.shape.set({ width: Math.abs(pointer.x - state.startX), height: Math.abs(pointer.y - state.startY) });
                    state.shape.set({ left: Math.min(pointer.x, state.startX), top: Math.min(pointer.y, state.startY) });
                } else if (state.tool === 'circle') {
                    let radius = Math.max(Math.abs(pointer.x - state.startX), Math.abs(pointer.y - state.startY)) / 2;
                    state.shape.set({ radius: radius });
                    state.shape.set({ left: Math.min(pointer.x, state.startX), top: Math.min(pointer.y, state.startY) });
                } else if (state.tool === 'line') {
                    state.shape.set({ x2: pointer.x, y2: pointer.y });
                }
                canvas.renderAll();
            });

            canvas.on('mouse:up', function() {
                let state = window.canvasStates[qNum];
                if (state.isDragging) {
                    state.isDragging = false;
                    canvas.requestRenderAll();
                    window.updateDrawingInput(qNum);
                }
            });

            canvas.on('mouse:out', function() {
                let state = window.canvasStates[qNum];
                if (state.isDragging) {
                    state.isDragging = false;
                    canvas.requestRenderAll();
                    window.updateDrawingInput(qNum);
                }
            });

            canvas.on('path:created', function(e) {
                if (e && e.path && canvas.getObjects().indexOf(e.path) === -1) {
                    canvas.add(e.path);
                }
                canvas.requestRenderAll();
                window.updateDrawingInput(qNum);
            });

            window.addEventListener('resize', () => canvas.calcOffset());
            window.addEventListener('scroll', () => canvas.calcOffset(), { passive: true });
        });

        // ==========================================
        // 8. LAYOUT INSTRUKSI FIXED + TOMBOL TOGGLE
        // ==========================================
        function updateFixedLayout() {
            const timerBar = document.querySelector('.fixed-top');
            const instruksiBox = document.querySelector('.instruksi-fixed-box');
            const spacer = document.getElementById('fixed-content-spacer');

            const timerHeight = timerBar ? timerBar.offsetHeight : 0;
            let totalHeight = timerHeight;

            if (instruksiBox) {
                instruksiBox.style.top = timerHeight + 'px';
                totalHeight += instruksiBox.offsetHeight;
            }
            if (spacer) spacer.style.height = totalHeight + 'px';
        }

        window.addEventListener('load', updateFixedLayout);
        window.addEventListener('resize', updateFixedLayout);
        document.querySelectorAll('.instruksi-fixed-box img').forEach(img => {
            img.addEventListener('load', updateFixedLayout);
        });
        updateFixedLayout();

        document.querySelectorAll('.instruksi-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const box = this.closest('.instruksi-fixed-box');
                if (!box) return;
                box.classList.toggle('instruksi-collapsed');
                setTimeout(updateFixedLayout, 50);
            });
        });
    });

    // ==========================================
    // FUNGSI PENDUKUNG PAINT (GLOBAL, dipanggil dari onclick inline HTML)
    // ==========================================
    window.updateDrawingInput = function(qNum) {
        if (!window.canvases[qNum]) return;
        window.canvases[qNum].renderAll();
        let dataURL = window.canvases[qNum].toDataURL({ format: 'png', quality: 0.8 });
        let inputEl = document.getElementById('drawing_input_' + qNum);
        if (inputEl) inputEl.value = dataURL;
        if (typeof window.saveAnswerAjax === 'function') {
            window.saveAnswerAjax(qNum, { answer_text: dataURL });
        }
    };

    window.setTool = function(qNum, tool, btnEl) {
        qNum = String(qNum);
        if (!window.canvasStates[qNum] || !window.canvases[qNum]) return;
        window.canvasStates[qNum].tool = tool;
        let canvas = window.canvases[qNum];

        let container = btnEl.closest('.paint-toolbar');
        container.querySelectorAll('.paint-tool-btn').forEach(b => {
            b.classList.remove('btn-dark');
            b.classList.add('btn-outline-dark');
        });
        btnEl.classList.remove('btn-outline-dark');
        btnEl.classList.add('btn-dark');

        if (tool === 'draw' || tool === 'eraser') {
            canvas.isDrawingMode = true;
            window.updateBrush(qNum);
        } else {
            canvas.isDrawingMode = false;
        }
    };

    window.updateBrush = function(qNum) {
        qNum = String(qNum);
        if (!window.canvasStates[qNum] || !window.canvases[qNum]) return;
        let canvas = window.canvases[qNum];
        let color = document.getElementById('color_' + qNum).value;
        let size = parseInt(document.getElementById('size_' + qNum).value);
        let tool = window.canvasStates[qNum].tool;

        if (tool === 'eraser') {
            canvas.freeDrawingBrush.color = '#ffffff';
            canvas.freeDrawingBrush.width = size * 3;
        } else {
            canvas.freeDrawingBrush.color = color;
            canvas.freeDrawingBrush.width = size;
        }
    };

    window.clearCanvas = function(qNum) {
        qNum = String(qNum);
        if (!window.canvases[qNum]) return;
        window.canvases[qNum].clear();
        window.canvases[qNum].backgroundColor = "#ffffff";
        window.updateDrawingInput(qNum);
    };
</script>

<style>
/* ===== RESPONSIVE SHOW.BLADE ===== */

.exam-title  { font-size: clamp(0.95rem, 3.5vw, 1.35rem); }
.timer-display { font-size: clamp(1.1rem, 4vw, 1.5rem); }

#examUsersTable, .table-responsive { overflow-x: auto; }

@media (max-width: 575.98px) {
    #btn-submit-exam { width: 100%; font-size: 0.95rem; }
    .table-number-cell { font-size: 0.75rem !important; padding: 2px 4px !important; }
    .rounded-circle[style*="40px"] { width: 30px !important; height: 30px !important; font-size: 0.8rem; }
    .card-body.p-4 { padding: 1rem !important; }
    .uraian-textarea { rows: 3; font-size: 0.88rem !important; }
    .table td, .table th { font-size: 0.78rem; padding: 0.35rem 0.4rem; }
    input[type=radio][style*="scale(1.5)"] { transform: scale(1.1) !important; }
}

@media (min-width: 576px) and (max-width: 991.98px) {
    .table td, .table th { font-size: 0.83rem; }
    #btn-submit-exam { width: 100%; }
}

.table-responsive { -webkit-overflow-scrolling: touch; }

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

#camera-status { word-break: break-word; max-width: 90vw; display: inline-block; }

.row.justify-content-center > .w-full.overflow-x-auto {
    width: 100% !important;
    max-width: 100% !important;
    flex: 1 1 100% !important;
}

/* ===== INSTRUKSI PENGERJAAN: FIXED ===== */
.instruksi-fixed-box {
    position: fixed;
    left: 0;
    right: 0;
    top: 0; /* di-set ulang oleh JS via updateFixedLayout() */
    z-index: 1025;
    margin: 0 !important;
    border-radius: 0 !important;
    max-height: 40vh;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}
.instruksi-fixed-box .card-body { padding: 0.85rem 1rem; }
.instruksi-fixed-box.instruksi-collapsed .instruksi-fixed-content { display: none; }
.instruksi-fixed-box .instruksi-toggle-btn i { transition: transform 0.2s ease; }
.instruksi-fixed-box.instruksi-collapsed .instruksi-toggle-btn i { transform: rotate(180deg); }
@media (max-width: 575.98px) {
    .instruksi-fixed-box { max-height: 45vh; }
    .instruksi-fixed-box h5 { font-size: 0.95rem; }
    .instruksi-fixed-box small,
    .instruksi-fixed-box p { font-size: 0.85rem; }
}
</style>
@endsection