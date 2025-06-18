<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use App\Traits\LogErrors;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\Api\V1\Auth\UserResource;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;

class AuthController extends Controller
{
    use LogErrors, ApiResponseTrait;

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }


            return $this->responseWithoutData([
                'token' => $user->createToken($request->email)->plainTextToken,
                'message' => 'Success'
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                "name" => $request->input("name"),
                "email" => $request->input("email"),
                "password" => Hash::make($request->input("password"))
            ]);

            return $this->responseWithoutData([
                'message' => 'Usuario creado',
                'user' => new UserResource($user)
            ], Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }

    public function me(): JsonResponse
    {
        return response()->json(new UserResource(auth()->user()));
    }

    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->responseWithoutData(['message' => 'Successfully logged out'], Response::HTTP_OK);
    }
}
