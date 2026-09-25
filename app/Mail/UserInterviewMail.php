<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserInterviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $jenis_undangan;
    public $pengundang;
    public $posisi_pengundang;
    public $tanggal_diundang;
    public $waktu_diundang;
    public $lokasi_diundang;
    public $persiapan_diundang;

    public function __construct($user, $jenis_undangan, $pengundang, $posisi_pengundang, $tanggal_diundang, $waktu_diundang, $lokasi_diundang, $persiapan_diundang)
    {
        $this->user = $user;
        $this->jenis_undangan = $jenis_undangan;
        $this->pengundang = $pengundang;
        $this->posisi_pengundang = $posisi_pengundang;
        $this->tanggal_diundang = $tanggal_diundang;
        $this->waktu_diundang = $waktu_diundang;
        $this->lokasi_diundang = $lokasi_diundang;
        $this->persiapan_diundang = $persiapan_diundang;
    }

    public function build()
    {
        return $this->subject('Undangan Interview Kerja')
            ->markdown('emails.user-interview')
            ->with([
                'user' => $this->user,
                'jenis_undangan' => $this->jenis_undangan,
                'pengundang' => $this->pengundang,
                'posisi_pengundang' => $this->posisi_pengundang,
                'tanggal_diundang' => $this->tanggal_diundang,
                'waktu_diundang' => $this->waktu_diundang,
                'lokasi_diundang' => $this->lokasi_diundang,
                'persiapan_diundang' => $this->persiapan_diundang,
            ]);
    }
}
