<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // Mencatat aktivitas login menggunakan Spatie
        activity('authentication') // Opsional: kamu bisa mengelompokkan log_name jadi 'authentication'
            ->causedBy($event->user) // Menyimpan siapa user yang melakukan aksi ini
            ->log('User berhasil login'); // Pesan log-nya
    }
}