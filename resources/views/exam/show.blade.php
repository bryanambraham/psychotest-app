@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center bg-white rounded">
                    <div>
                        <h4 class="mb-0 font-weight-bold">{{ $exam->name }}</h4>
                        <span class="badge badge-info">{{ strtoupper($exam->type) }}</span>
                    </div>
                    <div class="text-danger font-weight-bold" style="font-size: 1.5rem;">
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
                        <div class="alert alert-warning m-3 rounded">
                            <strong>Instruksi DISC:</strong> Pilih satu pernyataan yang Paling Menggambarkan Diri Anda <b>(Most)</b> dan satu yang Paling Tidak Menggambarkan <b>(Least)</b>.
                        </div>

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

                    {{-- ========================================== --}}
                    {{-- UI STANDAR UNTUK MBTI, EPPS, DLL (DARI DB) --}}
                    {{-- ========================================== --}}
                    @else
                        <div class="p-4">
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
            fetch('/exam/answer', {
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

            fetch('/proctoring/snap', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ exam_session_id: examSessionId, image: base64Image })
            });
        }

        function scheduleNextSnapshot() {
            // INTERVAL DIPERPENDEK: Antara 1 menit (60rb) sampai 3 menit (180rb)
            // Agar ujian durasi pendek tetap ter-capture beberapa kali
            let timeout = Math.floor(Math.random() * (180000 - 60000 + 1)) + 60000;
            setTimeout(() => { 
                takeSnapshotAndSend(); 
                scheduleNextSnapshot(); 
            }, timeout);
        }

        // ==========================================
        // 5. LOGIKA SUBMIT (KONFIRMASI & JEPRETAN AKHIR)
        // ==========================================
        const btnSubmit = document.getElementById('btn-submit-exam');
        
        btnSubmit.addEventListener('click', function() {
            Swal.fire({
                title: 'Kumpulkan Ujian?',
                text: "Anda yakin telah menyelesaikan semua soal?",
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

        function finishExam() {
            // Ambil foto terakhir sebagai bukti pengerjaan selesai/waktu habis
            takeSnapshotAndSend();

            // Beri jeda sebentar (500ms) supaya proses upload foto selesai sebelum pindah halaman
            setTimeout(() => {
                window.location.href = "/exam/finish/" + examSessionId;
            }, 500);
        }

    });
</script>
@endsection
