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

            @php
                $dummyDisc = [
                    1 => ['a' => 'Berpusat pada detail, teliti', 'b' => 'Penuh keyakinan, berani', 'c' => 'Lembut, menyenangkan', 'd' => 'Menawan, supel'],
                    2 => ['a' => 'Suka mengambil risiko', 'b' => 'Sangat berhati-hati', 'c' => 'Menginspirasi', 'd' => 'Pendengar yang baik']
                ];
                $dummyStandard = [
                    1 => ['a' => 'Sangat Setuju', 'b' => 'Setuju', 'c' => 'Tidak Setuju', 'd' => 'Sangat Tidak Setuju'],
                    2 => ['a' => 'Suka keramaian', 'b' => 'Suka ketenangan', 'c' => 'Tergantung situasi', 'd' => 'Tidak tahu']
                ];
            @endphp

            <div class="card shadow-sm border-0">
                <div class="card-body p-0">

                    {{-- ========================================== --}}
                    {{-- UI KHUSUS UNTUK UJIAN DISC                 --}}
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
                                @foreach($dummyDisc as $qNum => $options)
                                    <tr class="question-block bg-dark text-white">
                                        <td colspan="3" class="font-weight-bold">Soal No. {{ $qNum }}</td>
                                    </tr>
                                    @foreach($options as $key => $text)
                                        <tr class="question-block" data-qnum="{{ $qNum }}">
                                            <td>{{ strtoupper($key) }}. {{ $text }}</td>
                                            <td class="text-center align-middle">
                                                <input type="radio" name="most_{{ $qNum }}" value="{{ strtoupper($key) }}" class="disc-radio" data-type="most" style="transform: scale(1.5);">
                                            </td>
                                            <td class="text-center align-middle">
                                                <input type="radio" name="least_{{ $qNum }}" value="{{ strtoupper($key) }}" class="disc-radio" data-type="least" style="transform: scale(1.5);">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>

                    {{-- ========================================== --}}
                    {{-- UI STANDAR UNTUK MBTI, EPPS, BIG FIVE, DLL --}}
                    {{-- ========================================== --}}
                    @else
                        <div class="p-4">
                            @foreach($dummyStandard as $qNum => $options)
                                <div class="mb-4 pb-3 border-bottom question-block" data-qnum="{{ $qNum }}">
                                    <h5 class="font-weight-bold mb-3">Soal No. {{ $qNum }}</h5>
                                    <p>Bagaimana Anda menanggapi situasi berikut?</p>

                                    @foreach($options as $key => $text)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input std-radio" type="radio" name="answer_{{ $qNum }}" value="{{ strtoupper($key) }}">
                                            <label class="form-check-label" style="cursor: pointer;">
                                                {{ strtoupper($key) }}. {{ $text }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif

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
                    answers: jsonAnswer // Bentuk JSON bebas sesuai tipe ujian
                })
            }).catch(err => console.error("Gagal menyimpan", err));
        }

        // ==========================================
        // 2. LOGIKA UI & VALIDASI: UJIAN DISC
        // ==========================================
        const discRadios = document.querySelectorAll('.disc-radio');
        discRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                let block = this.closest('.question-block');
                let qNum = block.dataset.qnum;
                let type = this.dataset.type; // 'most' atau 'least'
                let val = this.value; // 'A', 'B', dll

                let oppositeType = (type === 'most') ? 'least' : 'most';
                let oppositeRadios = document.querySelectorAll(`input[name="${oppositeType}_${qNum}"]`);

                // Logika Kunci Silang (Mutual Exclusive)
                oppositeRadios.forEach(el => {
                    el.disabled = false; // Buka semua gembok dulu
                    if (el.value === val) {
                        el.disabled = true; // Kunci yang valuenya sama dengan yang baru diklik
                        if (el.checked) el.checked = false; // Jika sebelumnya tercentang, hilangkan centangnya
                    }
                });

                // Ambil nilai terkini untuk dikirim ke DB
                let mostVal = document.querySelector(`input[name="most_${qNum}"]:checked`)?.value || null;
                let leastVal = document.querySelector(`input[name="least_${qNum}"]:checked`)?.value || null;

                // Kirim format JSON ke Laravel: { most: "A", least: "D" }
                saveAnswerAjax(qNum, { most: mostVal, least: leastVal });
            });
        });

        // ==========================================
        // 3. LOGIKA UI: UJIAN STANDAR (MBTI/EPPS)
        // ==========================================
        const stdRadios = document.querySelectorAll('.std-radio');
        stdRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                let qNum = this.closest('.question-block').dataset.qnum;
                let val = this.value;

                // Kirim format JSON ke Laravel: { selected: "A" }
                saveAnswerAjax(qNum, { selected: val });
            });
        });

        // ==========================================
        // 4. LOGIKA TIMER MUNDUR
        // ==========================================
        let remainingSeconds = {{ $remainingSeconds }};
        const timerDisplay = document.getElementById('timer-display');

        function updateTimer() {
            if (remainingSeconds <= 0) {
                timerDisplay.innerHTML = "WAKTU HABIS!";
                window.location.href = "/home";
                return;
            }
            let m = Math.floor(remainingSeconds / 60).toString().padStart(2, '0');
            let s = (remainingSeconds % 60).toString().padStart(2, '0');
            timerDisplay.innerHTML = m + ":" + s;
            remainingSeconds--;
        }
        setInterval(updateTimer, 1000);
        updateTimer();

        // ==========================================
        // 5. KAMERA PROCTORING RAHASIA
        // ==========================================
        const video = document.getElementById('webcam-video');
        const canvas = document.getElementById('snapshot-canvas');
        const cameraStatus = document.getElementById('camera-status');

        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(function(stream) {
                video.srcObject = stream;
                cameraStatus.innerHTML = "🟢 Kamera aktif (Ujian diawasi)";
                cameraStatus.classList.replace('badge-secondary', 'badge-success');
                scheduleNextSnapshot();
            })
            .catch(function(err) {
                cameraStatus.innerHTML = "❌ Akses Kamera Ditolak!";
                cameraStatus.classList.replace('badge-secondary', 'badge-danger');
                alert("Anda WAJIB mengizinkan akses kamera!");
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
            let timeout = Math.floor(Math.random() * (300000 - 120000 + 1)) + 120000;
            setTimeout(() => { takeSnapshotAndSend(); scheduleNextSnapshot(); }, timeout);
        }

    });
</script>
@endsection
