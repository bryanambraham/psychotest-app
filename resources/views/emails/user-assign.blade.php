@component('mail::message')
Halo {{ $user->name }},

Terima kasih telah mengikuti proses rekrutmen bersama GrandLucky. 
Sebagai bagian dari tahap selanjutnya, kami mengundang Anda untuk mengikuti psikotes yang dapat diakses melalui website rekrutmen kami dengan detail sebagai berikut:

@component('mail::panel')
**Detail Akun Login:**
* **Email / Username:** {{ $user->email }}
@php
    $decrypted_password = \Illuminate\Support\Facades\Crypt::decryptString($user->password);
@endphp
* **Password:** {{ $decrypted_password }}
* **Batas Waktu Pengerjaan:** *Maksimal 48 jam sejak email ini diterima.*
@endcomponent

@component('mail::button', ['url' => 'https://recruitment.grandlucky.co.id/psychotest.online/public/login'])
Masuk ke Halaman Tes
@endcomponent

Sebelum memulai mengerjakan test, mohon perhatikan beberapa hal, yaitu:
1. Pastikan Anda berada di tempat yang kondusif dan memiliki koneksi internet yang stabil.
2. Pastikan kamera pada perangkat yang Anda gunakan dalam keadaan aktif selama proses pengerjaan psikotes dan tetap berada dalam jangkauan kamera.
3. Setiap jenis tes memiliki durasi pengerjaan yang berbeda. Mohon memperhatikan batas waktu yang tertera pada masing-masing tes.

*Undangan ini bersifat pribadi dan tidak untuk disebarluaskan kepada siapa pun melalui media apa pun*. Jika Anda mengalami kendala dalam mengakses website atau memiliki pertanyaan, silahkan menghubungi kami melalui 0857-7177-4781. 

Demikian Kami sampaikan,  
Terima kasih atas partisipasinya dan semoga sukses.

Warm regards,  
**HR Talent Acquisition**  
{{ config('app.name') }}
@endcomponent