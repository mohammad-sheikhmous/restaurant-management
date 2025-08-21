<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Admin;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $order_by = \request()->order_by ?? 'desc';
        $sort_by = \request()->sort_by ?? 'id';
        $limit_by = \request()->limit_by ?? '10';

        $admins = Admin::when(\request()->searched_text, function ($query) {
            $query->where('name', 'LIKE', '%' . \request()->searched_text . '%')
                ->orwhere('email', 'LIKE', '%' . \request()->searched_text . '%');
        })->when(!is_null(\request()->status), function ($query) {
            $query->where('status', request()->status);
        })->orderBy($sort_by, $order_by)->paginate($limit_by);

        return dataJson('admins', $admins, 'all admins');
    }

    public function show($id)
    {
        $admin = Admin::find($id);
        if (!$admin)
            return messageJson('admin not found', false, 404);

        return dataJson('admin', $admin, 'admin returned successfully');
    }

    public function store(AdminRequest $request)
    {
        $data = $request->only('name', 'email', 'status', 'role_id', 'password');
        if ($request->hasFile('image'))
            $data['image'] = storeImage($request->name, $request->image, 'admins');

        Admin::create($data);

        return messageJson('new admin created', true, 201);
    }

    public function update(AdminRequest $request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin)
            return messageJson('admin not found');

        $data = $request->only('name', 'email', 'status', 'role_id', 'password');
        if ($request->hasFile('image'))
            $data['image'] = updateImage($request->name, $admin->name, $request->image, 'admins');

        if (!isset($data['password']))
            unset($data['password']);

        $admin->update($data);

        return messageJson('admin updated');
    }

    public function destroy($id)
    {
        $admin = Admin::find($id);
        if (!$admin)
            return messageJson('admin not found');

        $admin->delete();
        deleteImage($admin->image, 'admins');

        return messageJson('Admin deleted.');
    }

    public function changeStatus($id)
    {
        $admin = Admin::find($id);
        if (!$admin)
            return messageJson('admin not found');

        $admin->update(['status' => !$admin->status]);

        return messageJson('admin status changed');
    }
}
