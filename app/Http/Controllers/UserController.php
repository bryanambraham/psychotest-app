<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Tampilkan daftar semua user
    public function index()
    {
        $users = User::latest()
        ->where('name', 'not like', strtolower(config('app.admin_name')))
        ->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Form tambah user
    public function create()
    {
        return view('admin.users.create');
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'position' => 'nullable|string|max:50',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:admin,user',
        ]);

        $user = User::create([
            'name' => strtolower($request->name),
            'email' => strtolower($request->email),
            'position' => strtolower($request->position),
            'phone' => strtolower($request->phone),
            'password' => \Illuminate\Support\Facades\Crypt::encryptString($request->password),
            'role'     => $request->role,
        ]);

        ActivityLogger::logCreate($user, $user->id, $user, "User di POST: {$user->name}, email: {$user->email}, dan NoTelp: {$user->phone}.");

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    // Form edit user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // Update data user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'position' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'role'  => 'required|in:admin,user',
        ]);

        $data = [
            'name' => strtolower($request->name),
            'email' => strtolower($request->email),
            'position' => strtolower($request->position),
            'phone' => strtolower($request->phone),
            'role' => $request->role,
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
                ]);

                // Catat di Activity Logger
                ActivityLogger::logCreate($user, $user->id, $user, "User di IMPORT: {$user->name}, email: {$user->email}, dan NoTelp: {$user->phone}.");
            }
        }
        
        fclose($handle);

        return redirect()->route('users.index')->with('success', 'Data user berhasil di-import dari CSV.');
    }
}
