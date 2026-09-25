@component('mail::message')

# <span style="color: #000000;">Halo {{ $user->name }},</span>

<span style="color: #000000;">Terima kasih telah mengikuti proses rekrutmen bersama GrandLucky Group.</span>

<span style="color: #000000;">Sebagai bagian dari tahapan selanjutnya, kami mengundang Anda untuk mengikuti {{ $jenis_undangan }} bersama {{ $pengundang }} dengan detail sebagai berikut:</span>

@component('mail::panel')
<span style="color: #000000;">Posisi: {{ $posisi_pengundang }}</span>  
  
<span style="color: #000000;">Hari/Tanggal: {{ \Carbon\Carbon::parse($tanggal_diundang)->translatedFormat('l, d F Y') }}</span>  
  
<span style="color: #000000;">Waktu: {{ $waktu_diundang }} WIB</span>  
  
<span style="color: #000000;">Lokasi: {{ $lokasi_diundang }}</span>                             
@endcomponent

<span style="color: #000000;">Mohon untuk hadir 10–15 menit sebelum jadwal interview dan {{ $persiapan_diundang }}. Kami juga mengimbau Anda untuk menggunakan pakaian yang rapi dan sopan serta menggunakan sepatu tertutup selama proses Interview.</span>

<span style="color: #000000;">Apabila terdapat kendala atau pertanyaan terkait jadwal maupun lokasi interview, silakan menghubungi kami melalui 0857-7177-4781.</span> 

<span style="color: #000000;">Demikian kami sampaikan. Terima kasih atas perhatian dan partisipasi Anda dalam proses rekrutmen GrandLucky Group.</span>

<span style="color: #000000;">Kami menantikan kehadiran Anda.</span><br><br><span style="color: #000000;">Warm Regards,</span> 

<span style="color: #000000;">HR Talent Acquisition</span>  
<span style="color: #000000;">{{ config('app.name') }}</span>

@component('mail::footer')
<span style="color: #000000;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</span>
@endcomponent

@endcomponent
