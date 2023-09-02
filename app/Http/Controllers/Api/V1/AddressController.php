<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Address\AddressStoreRequest;
use App\Http\Resources\AddressResource;
use App\Http\Resources\StateResource;
use App\Models\City;
use App\Models\State;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index(): JsonResponse
    {
        $state = State::all();
        $state = StateResource::collection($state);
        $address = UserAddress::where('user_id', Auth::id())->get();
        $address = AddressResource::collection($address);
        return $this->successResponse([
            'address' => $address,
            'state' => $state
        ], '');
    }

    public function store(AddressStoreRequest $request)
    {
        $address = UserAddress::create([
            'postalcode' => $request->postalcode,
            'content' => $request->address,
            'user_id' => Auth::id(),
            'state_id' => $request->state_id,
            'city_id' => $request->city_id
        ]);
        return $this->successResponse([
            'address' => $address,
        ], __('messages.create_success'));

    }

    public function update(AddressStoreRequest $request, $id)
    {
        $address = UserAddress::find($id);
        if (!filled($address))
            return $this->errorResponse(__('messages.item_not_found'), 404);
        $address->postalcode = $request->postalcode;
        $address->content = $request->address;
        $address->state_id = $request->state_id;
        $address->city_id = $request->city_id;
        $address->save();
        return $this->successResponse([
            'address' => $address,
        ], __('messages.success_update'));

    }
}
