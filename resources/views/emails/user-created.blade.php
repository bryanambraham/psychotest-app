@component('mail::message')
# Halo {{ $user->name }},

Akun Anda telah berhasil dibuat di aplikasi kami.

@component('mail::panel')
Email: {{ $user->email }}
@endcomponent

Silakan login untuk mulai menggunakan aplikasi.

@component('mail::button', ['url' => url('/')])
Masuk ke Aplikasi
@endcomponent

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
