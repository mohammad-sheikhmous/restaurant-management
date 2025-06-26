<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\Resource\UserResource;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $limit = \request()->limit ?? '10';
        $searched_text = strip_tags(\request()->validate([
            'searched_text' => 'nullable|min:3'
        ])['searched_text']);

        $users = User::when($searched_text, function ($query) use ($searched_text) {
            $query->where('first_name', 'LIKE', '%' . $searched_text . '%')
                ->orwhere('last_name', 'LIKE', '%' . $searched_text . '%');
        })->withCount(['orders', 'reservations'])
            ->with('wallet')
            ->paginate($limit);

        return dataJson('users', (UserResource::collection($users))->response()->getData(true), 'all users');
    }

    public function show($id)
    {
        if (\request()->details == 'complete')
            $user = User::whereId($id)->with('wallet')
                ->withCurrentOrdersCount()
                ->withRejectedOrdersCount()
                ->withCompletedOrdersCount()
                ->withCurrentReservationsCount()
                ->withRejectedReservationsCount()
                ->withFinishedReservationsCount()
                ->first();
        else
            $user = User::find($id);

        if (!$user)
            return messageJson('user not found', false, 404);

        return dataJson('user', UserResource::make($user), 'user returned successfully');
    }

    public function store(UserRequest $request)
    {
        $data = $request->only('last_name', 'first_name', 'status', 'password', 'email', 'mobile');
        $data['email_verified_at'] = now();

        if ($request->hasFile('image'))
            $data['image'] = storeImage($request->last_name, $request->image, 'users');

        User::create($data);

        return messageJson('new user created', true, 201);
    }

    public function destroy($id)
    {
        $user = User::withCurrentOrdersCount()->withCurrentReservationsCount()->find($id);
        if (!$user)
            return messageJson('user not found', false, 404);

        if ($user->current_reservation_count > 0 || $user->current_orders_count > 0)
            return messageJson('this user cannot be deleted', false, 400);

        $user->delete();
        return messageJson('user deleted');
    }

    public function changeStatus($id)
    {
        $user = User::find($id);
        if (!$user)
            return messageJson('user not found', false, 404);

        $user->update(['status' => !$user->status]);

        return messageJson('user status changed');
    }
}
