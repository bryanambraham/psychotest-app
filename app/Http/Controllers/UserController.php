<?php

namespace App\Http\Controllers;

use App\Mail\UserInterviewMail;
use App\Role;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.view,users.view_own')->only(['index']);
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.edit')->only(['edit', 'update']);
        $this->middleware('permission:users.delete')->only(['destroy']);
        $this->middleware('permission:users.import')->only(['import']);
        $this->middleware('permission:users.interview')->only(['filterInterview', 'undangInterview']);
    }

    private function sendMail($user){
        
        return view('emails.user_created', $user);
    }

    // Tampilkan daftar semua user
    public function index()
    {
        $query = User::latest()
            ->where('name', 'not like', strtolower(config('app.admin_name')));

        // Jika bukan admin dan tidak memiliki hak 'users.view' (semua), tapi memiliki 'users.view_own'
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasPermission('users.view') && auth()->user()->hasPermission('users.view_own')) {
            $query->where('created_by', auth()->id());
        }

        $users = $query->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Form tambah user
    public function create()
    {
        $roles = Schema::hasTable('roles') ? Role::all() : collect([
            (object)['name' => 'user', 'display_name' => 'Peserta (User)'],
            (object)['name' => 'admin', 'display_name' => 'Administrator'],
        ]);
        return view('admin.users.create', compact('roles'));
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $roleRules = Schema::hasTable('roles') ? 'required|string|exists:roles,name' : 'required|in:admin,user';

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'position' => 'nullable|string|max:50',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role'     => $roleRules,
            'interview' => 'nullable|date',
            'status_kandidat' => 'nullable|string',
        ]);

        $roleModel = Schema::hasTable('roles') ? Role::where('name', $request->role)->first() : null;

        $user = User::create([
            'name' => strtolower($request->name),
            'email' => strtolower($request->email),
            'position' => strtolower($request->position),
            'phone' => strtolower($request->phone),
            'password' => \Illuminate\Support\Facades\Crypt::encryptString($request->password),
            'role'     => $roleModel ? $roleModel->name : $request->role,
            'role_id'  => $roleModel ? $roleModel->id : null,
            'interview' => $request->interview,
            'status_kandidat' => $request->status_kandidat,
            'created_by' => auth()->id(),
        ]);

        ActivityLogger::logCreate($user, $user->id, $user, "User di POST: {$user->name}, email: {$user->email}, dan NoTelp: {$user->phone}.");

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    // Form edit user
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // Jika hanya memiliki izin view_own, pastikan hanya bisa mengedit user buatannya sendiri
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasPermission('users.view') && auth()->user()->hasPermission('users.view_own')) {
            if ($user->created_by !== auth()->id()) {
                abort(403, 'Anda hanya dapat mengedit user yang Anda buat sendiri.');
            }
        }

        $roles = Schema::hasTable('roles') ? Role::all() : collect([
            (object)['name' => 'user', 'display_name' => 'Peserta (User)'],
            (object)['name' => 'admin', 'display_name' => 'Administrator'],
        ]);
        return view('admin.users.edit', compact('user', 'roles'));
    }

    // Update data user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Jika hanya memiliki izin view_own, pastikan hanya bisa mengupdate user buatannya sendiri
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasPermission('users.view') && auth()->user()->hasPermission('users.view_own')) {
            if ($user->created_by !== auth()->id()) {
                abort(403, 'Anda hanya dapat mengupdate user yang Anda buat sendiri.');
            }
        }

        $roleRules = Schema::hasTable('roles') ? 'required|string|exists:roles,name' : 'required|in:admin,user';

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'position' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'role'  => $roleRules,
            'interview' => 'nullable|date',
            'status_kandidat' => 'nullable|string',
        ]);

        $roleModel = Schema::hasTable('roles') ? Role::where('name', $request->role)->first() : null;

        $data = [
            'name' => strtolower($request->name),
            'email' => strtolower($request->email),
            'position' => strtolower($request->position),
            'phone' => strtolower($request->phone),
            'role' => $roleModel ? $roleModel->name : $request->role,
            'role_id' => $roleModel ? $roleModel->id : null,
            'interview' => $request->interview,
            'status_kandidat' => $request->status_kandidat,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $data['password'] = \Illuminate\Support\Facades\Crypt::encryptString($request->password);
        }

        $user->update($data);

        ActivityLogger::logUpdate($user, $user->id, $user, "User di UPDATE: {$user->name}, email: {$user->email}, dan NoTelp: {$user->phone}.");


        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    // Hapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Mencegah admin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        // Jika hanya memiliki izin view_own, pastikan hanya bisa menghapus user buatannya sendiri
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasPermission('users.view') && auth()->user()->hasPermission('users.view_own')) {
            if ($user->created_by !== auth()->id()) {
                abort(403, 'Anda hanya dapat menghapus user yang Anda buat sendiri.');
            }
        }

        $user->delete();

        ActivityLogger::logDelete($user, $user->id, $user, "User dengan nama: {$user->name}, di DELETE.");

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    // Import user dari file CSV
    public function import(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getPathname(), "r");

        // Deteksi pemisah (delimiter) apakah koma (,) atau titik koma (;)
        $firstLine = fgets($handle);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        rewind($handle); // Kembalikan pointer ke baris awal file

        $header = true;

        // Gunakan delimiter yang sudah dideteksi
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if ($header) {
                $header = false; // Lewati baris pertama (Header CSV)
                continue;
            }

            // Pastikan array punya indeks ke-1 (Email) dan bersihkan spasi
            $email = isset($row[1]) ? strtolower(trim($row[1])) : '';

            // Jika email tidak kosong dan belum terdaftar di database
            if (!empty($email) && !User::where('email', $email)->exists()) {
                // Tampung dulu nilainya agar pengecekan lebih rapi
                $position = trim($row[2] ?? '');
                $phone    = trim($row[3] ?? '');
                $password = trim($row[4] ?? '');
                $role     = trim($row[5] ?? '');

                $user = User::create([
                    'name'     => isset($row[0]) ? strtolower(trim($row[0])) : '',
                    'email'    => $email,
                    // Cek jika nilainya bukan string kosong, jika kosong set jadi null
                    'position' => $position !== '' ? strtolower($position) : null,
                    'phone'    => $phone !== '' ? strtolower($phone) : null,
                    'password' => \Illuminate\Support\Facades\Crypt::encryptString($password !== '' ? $password : 'password123'),
                    'role'     => in_array(strtolower($role), ['admin', 'user']) ? strtolower($role) : 'user',
                    'created_by' => auth()->id(),
                ]);

                // Catat di Activity Logger
                ActivityLogger::logCreate($user, $user->id, $user, "User di IMPORT: {$user->name}, email: {$user->email}, dan NoTelp: {$user->phone}.");
            }
        }
        
        fclose($handle);

        return redirect()->route('users.index')->with('success', 'Data user berhasil di-import dari CSV.');
    }

    public function filterInterview(User $user)
    {
        return view('interview.create', compact('user'));
    }

    public function undangInterview(Request $request, User $user)
    {
        $request->validate([
            'jenis_undangan' => 'required|string',
            'pengundang' => 'required|string',
            'posisi_pengundang' => 'required|string',
            'tanggal_diundang' => 'required|date',
            'waktu_diundang' => 'required',
            'lokasi_diundang' => 'required|string',
            'persiapan_diundang' => 'required|string',
        ]);

        $jenis_undangan = $request->jenis_undangan;
        $pengundang = $request->pengundang;
        $posisi_pengundang = $request->posisi_pengundang;
        $tanggal_diundang = $request->tanggal_diundang;
        $waktu_diundang = $request->waktu_diundang;
        $lokasi_diundang = $request->lokasi_diundang;
        $persiapan_diundang = $request->persiapan_diundang;

        Mail::to($user->email)->send(
            new UserInterviewMail($user, $jenis_undangan, $pengundang, $posisi_pengundang, $tanggal_diundang, $waktu_diundang, $lokasi_diundang, $persiapan_diundang)
        );

        return redirect()->route('users.index')->with('success', 'Undangan interview berhasil dikirim ke ' . $user->email . '.');
    }
}
