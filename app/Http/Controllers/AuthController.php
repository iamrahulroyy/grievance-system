<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new citizen.
     *
     * Creates the account and returns an API token the client should send as
     * `Authorization: Bearer <token>` on subsequent requests.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            ...$request->safe()->only(['name', 'email', 'password']),
            'role' => UserRole::Citizen,
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    /**
     * Log in with email + password and receive an API token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $deviceName = $request->string('device_name')->toString() ?: 'api';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'user'  => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Revoke the token that authenticated the current request.
     */
    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    /**
     * Return the currently authenticated user.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * List all admin users.
     *
     * Useful for assigning complaints to a specific admin.
     */
    public function admins(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $admins = User::where('role', \App\Enums\UserRole::Admin)->get();

        return UserResource::collection($admins);
    }
}
