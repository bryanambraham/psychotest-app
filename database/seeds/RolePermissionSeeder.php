<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Permission;
use App\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Materi Ujian
            [
                'name' => 'exams.view',
                'display_name' => 'Lihat Daftar Ujian & Preview',
                'group' => 'Materi Ujian',
                'description' => 'Melihat daftar materi ujian, preview dan copy link ujian',
            ],
            [
                'name' => 'exams.create',
                'display_name' => 'Tambah Materi Ujian',
                'group' => 'Materi Ujian',
                'description' => 'Membuat materi ujian baru',
            ],
            [
                'name' => 'exams.edit',
                'display_name' => 'Edit Ujian & Atur Peserta',
                'group' => 'Materi Ujian',
                'description' => 'Mengubah informasi ujian dan mengatur assignment peserta',
            ],
            [
                'name' => 'exams.delete',
                'display_name' => 'Hapus Materi Ujian',
                'group' => 'Materi Ujian',
                'description' => 'Menghapus materi ujian',
            ],
            [
                'name' => 'exams.answer_keys',
                'display_name' => 'Kelola Kunci Jawaban',
                'group' => 'Materi Ujian',
                'description' => 'Melihat dan mengupdate kunci jawaban soal ujian',
            ],
            [
                'name' => 'exams.instructions',
                'display_name' => 'Edit Instruksi Ujian',
                'group' => 'Materi Ujian',
                'description' => 'Mengubah instruksi pengerjaan ujian',
            ],
            [
                'name' => 'exams.qr',
                'display_name' => 'Download QR Code Ujian',
                'group' => 'Materi Ujian',
                'description' => 'Mengunduh kode QR ujian',
            ],

            // Hasil Ujian
            [
                'name' => 'exam_results.view',
                'display_name' => 'Lihat Laporan Hasil Ujian (Semua)',
                'group' => 'Hasil Ujian',
                'description' => 'Melihat daftar hasil ujian seluruh peserta di sistem',
            ],
            [
                'name' => 'exam_results.view_own',
                'display_name' => 'Lihat Hasil Ujian (User Buatan Sendiri Saja)',
                'group' => 'Hasil Ujian',
                'description' => 'Hanya dapat melihat daftar hasil ujian dari peserta yang dibuat oleh akun sendiri',
            ],
            [
                'name' => 'exam_results.detail',
                'display_name' => 'Lihat Detail & Foto Proctoring',
                'group' => 'Hasil Ujian',
                'description' => 'Melihat rincian jawaban peserta dan bukti snapshot kamera',
            ],
            [
                'name' => 'exam_results.delete',
                'display_name' => 'Hapus Hasil Ujian',
                'group' => 'Hasil Ujian',
                'description' => 'Menghapus data sesi hasil ujian peserta',
            ],
            [
                'name' => 'exam_results.export',
                'display_name' => 'Export Hasil Ujian ke Excel',
                'group' => 'Hasil Ujian',
                'description' => 'Mengunduh laporan hasil ujian dalam bentuk Excel (.xls)',
            ],

            // Manajemen User
            [
                'name' => 'users.view',
                'display_name' => 'Lihat Daftar User (Semua)',
                'group' => 'Manajemen User',
                'description' => 'Melihat daftar seluruh pengguna di sistem',
            ],
            [
                'name' => 'users.view_own',
                'display_name' => 'Lihat User (Buatan Sendiri Saja)',
                'group' => 'Manajemen User',
                'description' => 'Hanya dapat melihat daftar user yang dibuat oleh akun sendiri',
            ],
            [
                'name' => 'users.create',
                'display_name' => 'Tambah User Baru',
                'group' => 'Manajemen User',
                'description' => 'Menambahkan user baru secara manual',
            ],
            [
                'name' => 'users.edit',
                'display_name' => 'Edit Data User',
                'group' => 'Manajemen User',
                'description' => 'Mengubah data user, password, dan status',
            ],
            [
                'name' => 'users.delete',
                'display_name' => 'Hapus User',
                'group' => 'Manajemen User',
                'description' => 'Menghapus data user dari sistem',
            ],
            [
                'name' => 'users.import',
                'display_name' => 'Import User dari CSV',
                'group' => 'Manajemen User',
                'description' => 'Mengimpor data user secara massal dari file CSV',
            ],
            [
                'name' => 'users.interview',
                'display_name' => 'Kirim Undangan Interview',
                'group' => 'Manajemen User',
                'description' => 'Mengirim jadwal dan email undangan interview kandidat',
            ],

            // Verifikasi Peserta
            [
                'name' => 'verify_users.view',
                'display_name' => 'Lihat Data Verifikasi Peserta',
                'group' => 'Verifikasi Peserta',
                'description' => 'Melihat daftar identitas verifikasi peserta',
            ],
            [
                'name' => 'verify_users.create',
                'display_name' => 'Tambah Verifikasi Peserta',
                'group' => 'Verifikasi Peserta',
                'description' => 'Menambahkan data verifikasi peserta secara manual',
            ],
            [
                'name' => 'verify_users.delete',
                'display_name' => 'Hapus Data Verifikasi',
                'group' => 'Verifikasi Peserta',
                'description' => 'Menghapus data verifikasi peserta tertentu',
            ],
            [
                'name' => 'verify_users.delete_all',
                'display_name' => 'Hapus Semua Verifikasi Sekaligus',
                'group' => 'Verifikasi Peserta',
                'description' => 'Mengosongkan seluruh data verifikasi peserta',
            ],

            // Role & Permission
            [
                'name' => 'roles.view',
                'display_name' => 'Lihat Daftar Role',
                'group' => 'Role & Permission',
                'description' => 'Melihat daftar role dan hak akses',
            ],
            [
                'name' => 'roles.create',
                'display_name' => 'Tambah Role Baru',
                'group' => 'Role & Permission',
                'description' => 'Membuat role baru beserta hak aksesnya',
            ],
            [
                'name' => 'roles.edit',
                'display_name' => 'Edit Role & Permission',
                'group' => 'Role & Permission',
                'description' => 'Mengubah nama dan hak akses role',
            ],
            [
                'name' => 'roles.delete',
                'display_name' => 'Hapus Role',
                'group' => 'Role & Permission',
                'description' => 'Menghapus role yang sudah tidak digunakan',
            ],

            // Sistem & Aktivitas
            [
                'name' => 'activity_log.view',
                'display_name' => 'Lihat Activity Log',
                'group' => 'Sistem & Aktivitas',
                'description' => 'Melihat riwayat aktivitas pengguna di sistem',
            ],
            [
                'name' => 'activity_log.delete',
                'display_name' => 'Hapus Activity Log',
                'group' => 'Sistem & Aktivitas',
                'description' => 'Membersihkan log aktivitas',
            ],
            [
                'name' => 'system.toggle_closed',
                'display_name' => 'Kunci / Buka Website Publik',
                'group' => 'Sistem & Aktivitas',
                'description' => 'Mengubah status maintenance / tutup akses ujian publik',
            ],
        ];

        // 1. Simpan semua permission
        $permissionModelMap = [];
        foreach ($permissions as $p) {
            $permission = Permission::updateOrCreate(
                ['name' => $p['name']],
                [
                    'display_name' => $p['display_name'],
                    'group' => $p['group'],
                    'description' => $p['description'],
                ]
            );
            $permissionModelMap[$p['name']] = $permission->id;
        }

        // 2. Buat Default Roles
        $adminRole = Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Super Administrator dengan hak akses penuh ke seluruh sistem dan tombol',
            ]
        );
        // Admin dapat semua permission
        $adminRole->permissions()->sync(array_values($permissionModelMap));

        $hrRole = Role::updateOrCreate(
            ['name' => 'hr'],
            [
                'display_name' => 'HR Recruitment',
                'description' => 'HR Staff: Kelola Ujian, User, Hasil Ujian, dan Interview',
            ]
        );
        $hrPermissions = [
            'exams.view', 'exams.create', 'exams.edit', 'exams.qr', 'exams.answer_keys', 'exams.instructions',
            'exam_results.view', 'exam_results.detail', 'exam_results.export',
            'users.view', 'users.create', 'users.edit', 'users.import', 'users.interview',
            'verify_users.view',
        ];
        $hrRole->permissions()->sync(array_intersect_key($permissionModelMap, array_flip($hrPermissions)));

        $interviewerRole = Role::updateOrCreate(
            ['name' => 'interviewer'],
            [
                'display_name' => 'Pewawancara / User Dept',
                'description' => 'Reviewer: Melihat hasil ujian peserta dan mengundang interview',
            ]
        );
        $interviewerPermissions = [
            'exam_results.view', 'exam_results.detail', 'exam_results.export',
            'users.view', 'users.interview',
        ];
        $interviewerRole->permissions()->sync(array_intersect_key($permissionModelMap, array_flip($interviewerPermissions)));

        $userRole = Role::updateOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'Peserta Ujian',
                'description' => 'Kandidat peserta ujian psikotes',
            ]
        );
        // Peserta tidak memiliki hak akses manajemen admin
        $userRole->permissions()->sync([]);

        // 3. Sinkronisasi user yang sudah ada ke role_id
        User::where('role', 'admin')->update(['role_id' => $adminRole->id]);
        User::where('role', 'user')->orWhereNull('role')->update(['role_id' => $userRole->id]);
    }
}
