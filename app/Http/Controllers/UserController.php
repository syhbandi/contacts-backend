<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): UserResource
    {
        $data = $request->validated();

        if (User::where('username', $data['username'])->count() == 1) {
            throw new HttpResponseException(response([
                'errors' => [
                    'username' => 'username already used'
                ]
            ], 401));
        }

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        return new UserResource($user);
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => [
                    'messages' => 'username or password wrong'
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    public function get(): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): UserResource
    {
        $data = $request->validated();
        $user = Auth::user();

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        /** @var \App\Models\User $user **/
        $user->save();
        return new UserResource($user);
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        /** @var \App\Models\User $user **/
        $user->save();

        return response()->json(['data' => true], 200);
    }
}
