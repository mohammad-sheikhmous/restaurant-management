<?php

namespace App\Http\Controllers\Dashboard;

use App\Casts\Translated;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return[
//            new Middleware('can:roles'),
        ];
    }

    public function index()
    {
        $roles = Role::all()->each(function ($role) {
            return $role->mergeCasts(['name' => Translated::class])->permissions = $role->translatedPermissions->values();
        });

        return dataJson('roles', $roles, 'All roles');
    }

    public function show($id)
    {
        $role = Role::find($id);
        if (!$role)
            return messageJson('Role not found', false, 404);

        $role->permissions = $role->translatedPermissions;

        return dataJson('role', $role, 'The role returned successfully');
    }

    public function getAllPermissions()
    {
        $permissions = config('roles.permissions.' . config('app.locale'));

        return dataJson('permissions', $permissions, 'All permissions in the system');
    }

    public function store(RoleRequest $request)
    {
        Role::create($request->only('name', 'permissions', 'status'));

        return messageJson('New role created', true, 201);
    }

    public function update(RoleRequest $request, $id)
    {
        $role = Role::find($id);
        if (!$role)
            return messageJson('Role not found', false, 404);

        $role->update($request->only('name', 'permissions', 'status'));

        return messageJson('The role updated');
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role)
            return messageJson('Role not found', false, 404);

        $role->delete();

        return messageJson('The role deleted');
    }

    public function changeStatus($id)
    {
        $role = Role::find($id);
        if (!$role)
            return messageJson('Role not found', false, 404);

        $role->update(['status' => !$role->status]);

        return messageJson('The role status changed');
    }
}
