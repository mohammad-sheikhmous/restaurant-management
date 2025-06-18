<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;

class UserAddressController extends Controller
{
    public function index()
    {
        $addresses = auth('user')->user()->addresses()
            ->select('id', 'name', 'city', 'area', 'street')
            ->latest()
            ->get();

        if ($addresses->isEmpty())
            return messageJson('There is no addresses', false, 404);

        return dataJson('addresses', $addresses, 'All user addresses');
    }

    public function show($id)
    {
        $address = auth('user')->user()->addresses()->find($id)
            ->makeHidden(['user_id', 'created_at', 'updated_at']);
        if (!$address)
            return messageJson("Address not found", false, 404);

        return dataJson('address', $address, "address with id: $id returned");
    }

    public function store(UserAddressRequest $request)
    {
        auth('user')->user()->addresses()->create($request->all());

        return messageJson('New address created', true, 201);
    }

    public function destroy($id)
    {
        $address = auth('user')->user()->addresses()->with(['orders' => function ($query) {
            return $query->whereIn('status', ['pending', 'accepted', 'preparing', 'prepared',
                'delivering']);
        }])->find($id);
        if (!$address)
            return messageJson("Address not found", false, 404);

        if ($address->orders->isNotEmpty()) {
            $message = 'This address cannot be deleted because there is a order in processing associated with it.';
            return messageJson($message, false, 409);
        }
        $address->delete();

        return messageJson("Address deleted");
    }
}
