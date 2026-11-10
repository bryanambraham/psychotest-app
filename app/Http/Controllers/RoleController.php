<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:roles.view')->only(['index']);
        $this->middleware('permission:roles.create')->only(['create', 'store']);
        $this->middleware('permission:roles.edit')->only(['edit', 'update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    /**
     * Tampilkan daftar role beserta jumlah user dan permission yang dimiliki.
     */
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Form tambah role baru.
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('group');
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Simpan role baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:50|alpha_dash|unique:roles,name',
            'display_name' => 'required|string|max:100',
            'description'  => 'nullable|string|max:255',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'exists:permissions,id',
        ], [
            'name.alpha_dash' => 'Identifier role hanya boleh berisi huruf, angka, tanda hubung (-) dan underscore (_).',
            'name.unique'     => 'Identifier role tersebut sudah digunakan.',
        ]);

        $role = Role::create([
            'name'         => Str::slug($request->name, '_'),
            'display_name' => $request->display_name,
            'description'  => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        ActivityLogger::logCreate($role, $role->id, $role->toArray(), "Menambahkan role baru: {$role->display_name}");

        return redirect()->route('roles.index')->with('success', "Role {$role->display_name} berhasil dibuat.");
    }

    /**
     * Form edit role dan permission-nya.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy('group');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update data role dan sinkronisasi permission.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'display_name' => 'required|string|max:100',
            'description'  => 'nullable|string|max:255',
            'permissions'  => 'nullable|array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        // Lindungi identifier 'admin' agar tidak diganti nama kuncinya
        $updateData = [
            'display_name' => $request->display_name,
            'description'  => $request->description,
        ];

        // Jika bukan default admin & user, boleh update slug jika diinputkan
        if (!in_array($role->name, ['admin', 'user']) && $request->filled('name')) {
            $request->validate([
                'name' => 'required|string|max:50|alpha_dash|unique:roles,name,' . $role->id,
            ]);
            $updateData['name'] = Str::slug($request->name, '_');
        }

        $role->update($updateData);

        // Jika role admin, selalu berikan semua permission
        if ($role->name === 'admin') {
            $allPermissions = Permission::pluck('id')->toArray();
            $role->permissions()->sync($allPermissions);
        } else {
            $role->permissions()->sync($request->permissions ?? []);
        }

        ActivityLogger::logUpdate($role, $role->id, $role->toArray(), "Mengubah role & permission: {$role->display_name}");

        return redirect()->route('roles.index')->with('success', "Role {$role->display_name} berhasil diperbarui.");
    }

    /**
     * Hapus role dari sistem.
     */
    public function destroy(Role $role)
    {
        // Cegah menghapus role bawaan sistem
        if (in_array($role->name, ['admin', 'user'])) {
            return redirect()->route('roles.index')->with('error', "Role bawaan ({$role->display_name}) tidak boleh dihapus.");
        }

        // Cegah menghapus jika masih ada user dengan role ini
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', "Tidak dapat menghapus role {$role->display_name} karena masih digunakan oleh {$role->users()->count()} user.");
        }

        $roleName = $role->display_name;
        $role->permissions()->detach();
        $role->delete();

        ActivityLogger::logDelete($role, $role->id, "Menghapus role: {$roleName}");

        return redirect()->route('roles.index')->with('success', "Role {$roleName} berhasil dihapus.");
    }
}
