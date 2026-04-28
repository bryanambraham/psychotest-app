@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card mb-4 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center bg-light">
                    <h4 class="mb-0">{{ $exam->name }}</h4>
                    <div class="text-danger font-weight-bold" style="font-size: 1.5rem;">
                        Sisa Waktu: <span id="timer-display">Memuat...</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    Soal No. 1
                </div>
                <div class="card-body">
                    <p>Saya lebih suka bekerja dalam lingkungan yang...</p>
                    <form id="exam-form">
                        <div class="form-check mb-2">
                            <input class="form-check-input answer-radio" type="radio" name="answer" value="A" data-question="1">
                            <label class="form-check-label">Terstruktur dan jelas</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input answer-radio" type="radio" name="answer" value="B" data-question="1">
                            <label class="form-check-label">Bebas dan dinamis</label>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mt-3 text-center text-muted small">
                <span id="camera-status">🔴 Meminta akses kamera...</span>
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

        // ==========================================
        // 1. LOGIKA TIMER MUNDUR (Dari Server)
        // ==========================================
        let remainingSeconds = {{ $remainingSeconds }};
        const timerDisplay = document.getElementById('timer-display');

        function updateTimer() {
            if (remainingSeconds <= 0) {
                timerDisplay.innerHTML = "WAKTU HABIS!";
                // Redirect atau auto-submit saat waktu habis
                window.location.href = "/home";
                return;
            }

            let minutes = Math.floor(remainingSeconds / 60);
            let seconds = remainingSeconds % 60;

            // Format 00:00
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            timerDisplay.innerHTML = minutes + ":" + seconds;
            remainingSeconds--;
        }

        // Jalankan timer setiap 1 detik
        setInterval(updateTimer, 1000);
        updateTimer(); // Panggil sekali di awal agar tidak delay 1 detik


        // ==========================================
        // 2. LOGIKA KAMERA PROCTORING RAHASIA
        // ==========================================
        const video = document.getElementById('webcam-video');
        const canvas = document.getElementById('snapshot-canvas');
        const cameraStatus = document.getElementById('camera-status');
        const examSessionId = {{ $session->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Minta Izin Kamera
        navigator.mediaDevices.getUserMedia({ video: true, audio: false })
            .then(function(stream) {
                video.srcObject = stream;
                cameraStatus.innerHTML = "🟢 Kamera aktif (Ujian diawasi)";

                // Mulai siklus jepretan acak setelah kamera menyala
                scheduleNextSnapshot();
            })
            .catch(function(err) {
                alert("Anda WAJIB mengizinkan akses kamera untuk mengerjakan ujian ini!");
                cameraStatus.innerHTML = "❌ Akses Kamera Ditolak!";
                // Opsional: Redirect user keluar jika menolak kamera
            });

        function takeSnapshotAndSend() {
            // Gambar frame video ke canvas (Resolusi kecil 640x480 untuk hemat storage)
            canvas.width = 640;
            canvas.height = 480;
            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Convert ke Base64 format JPG dengan kompresi 60%
            const base64Image = canvas.toDataURL('image/jpeg', 0.6);

            // Kirim ke Backend Laravel
            fetch('/proctoring/snap', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    exam_session_id: examSessionId,
                    image: base64Image
                })
            }).then(response => response.json())
              .then(data => console.log("Proctoring tersimpan diam-diam"))
              .catch(error => console.error("Proctoring gagal", error));
        }

        function scheduleNextSnapshot() {
            // Hitung waktu acak antara 2 menit (120000ms) sampai 5 menit (300000ms)
            const minTime = 120000;
            const maxTime = 300000;
            const randomTimeout = Math.floor(Math.random() * (maxTime - minTime + 1)) + minTime;

            setTimeout(function() {
                takeSnapshotAndSend();
                scheduleNextSnapshot(); // Panggil dirinya sendiri untuk jepretan berikutnya
            }, randomTimeout);
        }

        // ==========================================
        // 3. AUTO-SAVE JAWABAN (AJAX)
        // ==========================================
        // Jika user memilih radio button, langsung kirim ke server tanpa reload
        const radioButtons = document.querySelectorAll('.answer-radio');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                let questionNum = this.getAttribute('data-question');
                let answerValue = this.value;

                fetch('/exam/answer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        exam_session_id: examSessionId,
                        question_number: questionNum,
                        answers: { selected: answerValue } // Disimpan sebagai JSON di DB
                    })
                });
            });
        });

    });
</script>
@endsection
