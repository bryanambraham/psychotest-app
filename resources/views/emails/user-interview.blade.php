@component('mail::message')
# Halo {{ $user->name }},

Terima kasih telah mengikuti proses rekrutmen bersama GrandLucky Group.

Sebagai bagian dari tahapan selanjutnya, kami mengundang Anda untuk mengikuti {{ $jenis_undangan }} bersama {{ $pengundang }} dengan detail sebagai berikut:

@component('mail::panel')
Posisi: {{ $posisi_pengundang }}
Hari/Tanggal: {{ \Carbon\Carbon::parse($tanggal_diundang)->translatedFormat('l, d F Y') }}
Waktu: {{ $waktu_diundang }}
Lokasi: {{ $lokasi_diundang }}                             
@endcomponent

* Mohon untuk hadir **10–15** menit sebelum jadwal interview dan {{ $persiapan_diundang }}. Kami juga mengimbau Anda untuk menggunakan pakaian yang rapi dan sopan serta menggunakan sepatu tertutup selama proses Interview. *

* Apabila terdapat kendala atau pertanyaan terkait jadwal maupun lokasi interview, silakan menghubungi kami melalui **0857-7177-4781.** *

Demikian kami sampaikan. Terima kasih atas perhatian dan partisipasi Anda dalam proses rekrutmen GrandLucky Group.

Kami menantikan kehadiran Anda.

Warm regards,
**HR Talent Acquisition**  
{{ config('app.name') }}
@endcomponent
