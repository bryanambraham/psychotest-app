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
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'phone' => $request->phone,
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
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'phone' => $request->phone,
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
}
