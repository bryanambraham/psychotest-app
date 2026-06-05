<?php

namespace App\Http\Controllers;

use App\VerifyUser;
use Illuminate\Http\Request;

class VerifyUserController extends Controller
{
    // Tampilkan daftar semua user
    public function index()
    {
        $users = VerifyUser::latest()->paginate(10);
        return view('user.verify_users.index', compact('users'));
    }

    // Form tambah user
    public function create()
    {   
        $users = VerifyUser::latest()->paginate(10);
        return view('user.verify_users.create', compact('users'));
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:verify_user',
            'position' => 'nullable|string|max:50',
            'phone'    => 'nullable|string|max:20',
        ]);

        VerifyUser::create([
            'name' => strtolower($request->name),
            'email' => strtolower($request->email),
            'position' => strtolower($request->position),
            'phone' => strtolower($request->phone),
        ]);

        return redirect()->route('home')->with('success', 'Verifikasi user berhasil dilakukan.');
    }

    // Form edit user
    public function edit($id)
    {
        $user = VerifyUser::findOrFail($id);
        return view('user.verify_users.edit', compact('user'));
    }

    // Update data user
    public function update(Request $request, $id)
    {
        $user = VerifyUser::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:verify_user,email,' . $user->id,
            'position' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
        ]);

        $data = [
            'name' => strtolower($request->name),
            'email' => strtolower($request->email),
            'position' => strtolower($request->position),
            'phone' => strtolower($request->phone),
        ];


        $user->update($data);

        return redirect()->route('verify_users.index')->with('success', 'User berhasil diperbarui.');
    }

    // Hapus user
    public function destroy($id)
    {
        $user = VerifyUser::findOrFail($id);

        // Mencegah admin menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->route('verify_users.index')->with('success', 'User berhasil dihapus.');
    }
}
