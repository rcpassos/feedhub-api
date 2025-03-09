<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserAuthResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): UserAuthResource
    {
        $user = User::create($request->validated());

        $token = $user->createToken($request->email);

        return new UserAuthResource($user, $token);
    }

    public function login(LoginRequest $request): UserAuthResource
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            abort(401, 'Invalid credentials');
        }

        $token = $user->createToken($request->email);

        return new UserAuthResource($user, $token);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function refresh(Request $request): UserAuthResource
    {
        $user = $request->user();

        $token = $user->createToken($user->email);

        return new UserAuthResource($user, $token);
    }

    public function forgotPassword(): void
    {
        // TODO: send an email to the user with a link to reset the password
    }

    public function resetPassword(): void
    {
        // TODO: reset the user's password
    }
}
