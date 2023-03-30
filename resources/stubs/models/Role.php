<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function permissions()
    {
        return $this->setConnection('sqlsrv')->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->setConnection('sqlsrv')->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
            ->withTimestamps();
    }
}
