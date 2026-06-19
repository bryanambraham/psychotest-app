<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    {{-- Atribut border="1" akan otomatis menjadi garis (border) tabel di Excel --}}
    <table border="1" style="border-collapse: collapse; width: 100%; font-family: Arial, sans-serif;">
        <thead>
            <tr>
                {{-- Judul Besar --}}
                <th colspan="3" style="background-color: #4e73df; color: #ffffff; font-size: 16px; font-weight: bold; text-align: center; height: 40px; vertical-align: middle;">
                    LAPORAN HASIL UJIAN PSIKOTES
                </th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="3"></td></tr>

            {{-- ===================================== --}}
            {{-- DATA PESERTA --}}
            {{-- ===================================== --}}
            <tr>
                <td colspan="3" style="background-color: #f8f9fc; font-weight: bold; text-align: center;">INFO PESERTA</td>
            </tr>
            <tr>
                <td style="font-weight: bold; width: 150px;">Nama</td>
                <td colspan="2" style="width: 500px;">{{ $vName }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Email</td>
                <td colspan="2">{{ $vEmail }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Telepon</td>
                {{-- mso-number-format:'\@' mencegah Excel menghilangkan angka 0 di depan nomor telepon --}}
                <td colspan="2" style="mso-number-format:'\@';">{{ $vPhone }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Posisi Dilamar</td>
                <td colspan="2">{{ $vPos }}</td>
            </tr>

            <tr><td colspan="3"></td></tr>

            {{-- ===================================== --}}
            {{-- DATA UJIAN --}}
            {{-- ===================================== --}}
            <tr>
                <td colspan="3" style="background-color: #f8f9fc; font-weight: bold; text-align: center;">INFO UJIAN</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Nama Ujian</td>
                <td colspan="2">{{ $session->exam->name }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Tipe Ujian</td>
                <td colspan="2">{{ strtoupper($session->exam->type) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Status</td>
                <td colspan="2">{{ strtoupper($session->status) }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Waktu Selesai</td>
                <td colspan="2">{{ $session->end_time ?? '-' }}</td>
            </tr>

            <tr><td colspan="3"></td></tr>

            {{-- ===================================== --}}
            {{-- DETAIL JAWABAN --}}
            {{-- ===================================== --}}
            <tr>
                <th style="background-color: #1cc88a; color: white; font-weight: bold; text-align: center;">No</th>
                <th style="background-color: #1cc88a; color: white; font-weight: bold; text-align: center; width: 400px;">Pertanyaan / Pernyataan</th>
                <th style="background-color: #1cc88a; color: white; font-weight: bold; text-align: center; width: 400px;">Jawaban Peserta</th>
            </tr>

            @foreach($session->exam->questions as $question)
                @php
                    $userAnswer = $session->userAnswers->where('question_number', $question->number)->first();
                    $data = $userAnswer ? $userAnswer->answers : null;
                    $answerString = '❌ Kosong / Tidak Terisi';

                    if ($data) {
                        if ($session->exam->type == 'disc') {
                            $most = $data['most'] ?? '-';
                            $least = $data['least'] ?? '-';
                            // br khusus excel agar pindah baris tapi tetap di kotak yang sama
                            $answerString = "Most: $most <br style='mso-data-placement:same-cell;' /> Least: $least";
                        } elseif ($session->exam->type == 'angka' || $session->exam->type == 'uraian') {
                            if (isset($data['details']) && !empty($data['details'])) {
                                $ansArr = [];
                                foreach ($data['details'] as $lbl => $val) {
                                    $valFormat = is_numeric($val) ? number_format($val, 0, ',', '.') : $val;
                                    $ansArr[] = "$lbl : $valFormat";
                                }
                                $answerString = implode("<br style='mso-data-placement:same-cell;' />", $ansArr);
                            } else {
                                $answerString = $data['answer_text'] ?? '-';
                            }
                        } elseif ($session->exam->type == 'soal_kasus') {
                            $uploadedFiles = json_decode($session->answer_file, true) ?: [];

                            if (!empty($uploadedFiles)) {
                                $names = array_column($uploadedFiles, 'name');
                                $answerString = "File Diupload: " . implode(", ", $names);
                            } else {
                                $tableData = $data ?: [];
                                $filteredTableData = [];
                                if (is_array($tableData)) {
                                    foreach ($tableData as $row) {
                                        $isEmpty = true;
                                        if (is_array($row)) {
                                            foreach ($row as $cell) {
                                                if ($cell !== null && $cell !== '') {
                                                    $isEmpty = false;
                                                    break;
                                                }
                                            }
                                        }
                                        if (!$isEmpty) {
                                            $filteredTableData[] = $row;
                                        }
                                    }
                                }

                                if (!empty($filteredTableData)) {
                                    $rowsHtml = '';
                                    foreach ($filteredTableData as $row) {
                                        $rowsHtml .= '<tr>'
                                            . '<td style="vertical-align:top;">' . date('d-m-Y', strtotime($row[0]) ?? '') . '</td>'
                                            . '<td style="vertical-align:top;">' . ($row[1] ?? '') . '</td>'
                                            . '<td style="vertical-align:top;">' . ($row[2] ?? '') . '</td>'
                                            . '<td style="vertical-align:top; text-align:right;">' . ($row[3] ?? '') . '</td>'
                                            . '<td style="vertical-align:top; text-align:right;">' . ($row[4] ?? '') . '</td>'
                                            . '</tr>';
                                    }

                                    $answerString = '<table border="1" cellpadding="3" cellspacing="0" '
                                        . 'style="border-collapse:collapse; width:100%; vertical-align:top;">'
                                        . '<tr style="font-weight:bold;background-color:#f2f2f2;">'
                                        . '<td>Tanggal</td><td>Keterangan</td><td>Ref</td><td>Debit</td><td>Kredit</td>'
                                        . '</tr>'
                                        . $rowsHtml
                                        . '</table>';
                                } else {
                                    $answerString = '❌ Tidak mengunggah file & tabel jurnal kosong.';
                                }
                            }
                        } else {
                            $answerString = $data['selected'] ?? '-';
                        }
                    }

                    $qText = $session->exam->type == 'angka' ? 'Soal Penjumlahan Tabel Angka' : strip_tags($question->question_text);
                @endphp

                <tr>
                    <td style="text-align: center; vertical-align: top;">{{ $question->number }}</td>
                    <td style="vertical-align: top;">{{ $qText }}</td>
                    <td style="vertical-align: top;">{!! $answerString !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>