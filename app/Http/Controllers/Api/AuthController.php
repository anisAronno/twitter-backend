<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
    * Create a new AuthController instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return JsonResource
     */
    public function login(LoginRequest $request): JsonResource
    {
        if (!$token = auth()->attempt($request->validated())) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return JsonResource
     */
    public function register(RegistrationRequest $request): JsonResource
    {
        try {
            DB::beginTransaction();
            $user = User::create($request->only('name', 'username', 'email', 'password'));
            DB::commit();
            return $this->createNewToken(auth()->login($user), 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'success' => true,
            'message' => 'User successfully signed out'
        ]);
    }
    /**
     * Refresh a token.
     *
     * @return JsonResource
     */
    public function refresh(): JsonResource
    {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return JsonResource
     */
    public function userProfile(): JsonResource
    {
        return (new UserResource(auth()->user()->load(['tweets', 'followers', 'following'])))->additional([
            'message'  => 'Profile retrieved successfully.',
            'success'  => true,
         ]);
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResource
     */
    protected function createNewToken($token, $status = 200): JsonResource
    {
        return (new UserResource(auth()->user()))->additional([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'success'  => true,
         ]);
    }
}
