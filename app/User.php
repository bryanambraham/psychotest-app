<?php

namespace App;

use App\Mail\UserCreatedMail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            Mail::to($user->email)->send(new UserCreatedMail($user));
        });
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class, 'user_id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_user', 'user_id', 'exam_id')->withTimestamps();
    }

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Dapatkan model Role aktif user (melalui role_id atau fallback nama kolom role)
     */
    public function getActiveRole()
    {
        if ($this->relationLoaded('roleModel') && $this->roleModel) {
            return $this->roleModel;
        }

        if ($this->role_id) {
            return $this->roleModel;
        }

        if ($this->role) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('roles')) {
                    return Role::where('name', $this->role)->first();
                }
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Cek apakah user memiliki role tertentu (string atau array)
     */
    public function hasRole($roles)
    {
        $currentRoleName = $this->roleModel ? $this->roleModel->name : $this->role;

        if (is_array($roles)) {
            return in_array($currentRoleName, $roles);
        }

        return $currentRoleName === $roles;
    }

    /**
     * Cek apakah user memiliki permission tertentu
     */
    public function hasPermission($permissionName)
    {
        // Superadmin bypass: jika role adalah admin, selalu diizinkan
        if ($this->hasRole('admin')) {
            return true;
        }

        $role = $this->getActiveRole();
        if (!$role) {
            return false;
        }

        return $role->hasPermission($permissionName);
    }
}
