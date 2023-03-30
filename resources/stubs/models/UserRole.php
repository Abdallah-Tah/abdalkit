<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_roles';

    protected $connection = 'sqlsrv';

    protected $fillable = [
        'user_id',
        'role_id',
    ];

    public function user()
    {
        return $this->setConnection('sqlsrv')->belongsTo(User::class);
    }

    public function role()
    {
        return $this->setConnection('sqlsrv')->belongsTo(Role::class);
    }
}
