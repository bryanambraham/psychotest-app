<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // Pastikan user-nya ada (untuk menghindari error jika session sudah habis/null)
        if ($event->user) {
            activity('authentication')
                ->causedBy($event->user)
                ->log('User berhasil logout');
        }
    }
}