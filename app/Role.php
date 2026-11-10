<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role')->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Cek apakah role memiliki permission tertentu berdasarkan nama permission
     */
    public function hasPermission($permissionName)
    {
        // Role admin selalu memiliki akses ke semua permission
        if ($this->name === 'admin') {
            return true;
        }

        return $this->permissions->contains('name', $permissionName);
    }
}
