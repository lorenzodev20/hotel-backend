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
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Header;
use Knuckles\Scribe\Attributes\Response as ScribeResponse;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group("Auth", "Autenticación de un usuario en el API")]
#[Header("Content-Type", "application/json")]
class AuthController extends Controller
{
    use LogErrors, ApiResponseTrait;

    /**
     * Inicio de sesión vía API
     *
     * Inicia la sesión de un usuario y devuelve un token de acceso.
     *
     * @param \App\Http\Requests\Api\V1\Auth\LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[BodyParam("email", type: "string", example: "john.doe@example.com", description: "El email del usuario.")]
    #[BodyParam("password", type: "string", example: "password", description: "La contraseña del usuario.")]
    #[ScribeResponse([
        "message" => "Success",
        "user" => ["id" => 1, "name" => "John Doe", "email" => "john.doe@example.com"],
        "token" => "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    ], Response::HTTP_OK, description: "Inicio de sesión exitoso.")]
    #[ScribeResponse([
        "message" => "The provided credentials are incorrect.",
        "errors" => ["email" => ["The provided credentials are incorrect."]]
    ], Response::HTTP_UNPROCESSABLE_ENTITY, description: "Credenciales inválidas.")]
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
                'message' => 'Success',
                'user' => new UserResource($user),
                'token' => $user->createToken($request->email)->plainTextToken,
            ], Response::HTTP_OK);
        } catch (\Throwable $th) {
            $this->printLog($th);
            return $this->responseErrorByException($th);
        }
    }
    /**
     * Registro de usuario
     *
     * Registra un nuevo usuario en el sistema.
     *
     * @param \App\Http\Requests\Api\V1\Auth\RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    #[BodyParam("name", type: "string", example: "Jane Doe", description: "El nombre del usuario.")]
    #[BodyParam("email", type: "string", example: "jane.doe@example.com", description: "El email único del usuario.")]
    #[BodyParam("password", type: "string", example: "newpassword", description: "La contraseña del usuario (mínimo 8 caracteres).")]
    #[BodyParam("password_confirmation", type: "string", example: "newpassword", description: "Confirmación de la contraseña.")]
    #[ScribeResponse([
        "message" => "Usuario creado",
        "user" => ["id" => 1, "name" => "Jane Doe", "email" => "jane.doe@example.com"]
    ], Response::HTTP_CREATED, description: "Usuario registrado exitosamente.")]
    #[ScribeResponse([
        "message" => "The email has already been taken.",
        "errors" => ["email" => ["The email has already been taken."]]
    ], Response::HTTP_UNPROCESSABLE_ENTITY, description: "Error de validación (ej. email ya registrado).")]
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

    /**
     * Obtener información del usuario autenticado
     *
     * Devuelve la información del usuario actualmente autenticado.
     *
     * @authenticated
     * @return \Illuminate\Http\JsonResponse
     */
    #[ResponseFromApiResource(UserResource::class, User::class, description: "Información del usuario autenticado.")]
    public function me(): JsonResponse
    {
        return response()->json(new UserResource(auth()->user()));
    }

    /**
     * Cerrar sesión (Logout)
     *
     * Cierra la sesión del usuario actual revocando el token de acceso.
     *
     * @authenticated
     * @return \Illuminate\Http\JsonResponse
     */
    #[ScribeResponse([
        "message" => "Successfully logged out"
    ], Response::HTTP_OK, description: "Cierre de sesión exitoso.")]
    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();

        return $this->responseWithoutData(['message' => 'Successfully logged out'], Response::HTTP_OK);
    }
}
