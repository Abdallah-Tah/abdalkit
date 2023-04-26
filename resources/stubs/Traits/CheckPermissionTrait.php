<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait CheckPermissionTrait
{
    protected function checkPermission($permissionName)
    {
        $userPermissions = Auth::user()->hasPermissionTo();

        if (in_array($permissionName, $userPermissions)) {
            return true;
        }
    }
}
