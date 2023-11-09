<?php

namespace App\Http\Controllers\Api;

use AnisAronno\MediaHelper\Facades\Media;
use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarUpdateRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordRecoverRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
    * Create a new AuthController instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'passwordReset', 'passwordRecover']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     * @return JsonResponse|JsonResource
     */
    public function login(LoginRequest $request): JsonResource|JsonResponse
    {
        $credentials = $request->only(['password']);
        $loginType = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials[$loginType] = $request->input($loginType);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong Credential.'
            ], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @param RegistrationRequest $request
     * @return JsonResource|JsonResponse
     */
    public function register(RegistrationRequest $request): JsonResource | JsonResponse
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
    public function logout(): JsonResponse
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
     *  Get the authenticated User.
     *
     * @return JsonResource
     */
    public function profile(): JsonResource
    {
        return (new UserResource(auth()->user()->loadCount(['tweets','followers', 'following'])))->additional([
            'message'  => 'Profile retrieved successfully.',
            'success'  => true,
         ]);
    }
    /**
     * Get the authenticated User.
     *
     * @return JsonResource
     */
    public function userProfile(User $user): JsonResource
    {
        return (new UserResource($user->loadCount(['tweets','followers', 'following'])))->additional([
            'message'  => 'Profile retrieved successfully.',
            'success'  => true,
         ]);
    }

    /**
     *      * Profile Update
     *
     * @param ProfileUpdateRequest $request
     * @param User $user
     * @return JsonResource
     */
    public function profileUpdate(ProfileUpdateRequest $request, User $user): JsonResource
    {
        $response = $user->update($request->only('name', 'username', 'email'));
        if ($response) {
            return (new UserResource($user))->additional([
                'message' => 'Successfully Updated.',
                'success'  => true,
             ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Update failed.'
            ]);
        }
    }

    /**
     * Password Update function
     *
     * @param PasswordUpdateRequest $request
     * @param User $user
     * @return JsonResource|JsonResponse
     */
    public function passwordUpdate(PasswordUpdateRequest $request, User $user): JsonResource | JsonResponse
    {
        $response = $user->update($request->only('password'));

        if ($response) {
            return $this->refresh();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Update failed.'
            ]);
        }
    }

    /**
     *      * Update User Image.
     *
     * @param AvatarUpdateRequest $request
     * @return void
     */
    public function userAvatarUpdate(AvatarUpdateRequest $request): JsonResource| JsonResponse
    {
        $user = auth()->user();

        try {
            if ($request->hasFile('image')) {
                $oldImage = $user->image;
                $user->image = Media::upload($request, 'image', 'user');

                if($user->save()) {
                    Media::delete($oldImage);
                    return (new UserResource($user))->additional([
                        'message' => 'Successfully Updated.',
                        'success'  => true,
                     ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Update failed.'
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found!'
                ]);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ]);
        }
    }

    /**
     *      * Get the token array structure.
     *
     * @param [type] $token
     * @param integer $status
     * @return JsonResource
     */
    protected function createNewToken(string $token, $status = 200): JsonResource
    {
        return (new UserResource(new UserResource(auth()->user()->loadCount(['tweets','followers', 'following']))))->additional([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * (60 * 60 * 24),
            'success'  => true,
         ]);
    }

    /**
     * Password Recover
     *
     * @param PasswordRecoverRequest $request
     * @return void
     */
    public function passwordRecover(PasswordRecoverRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        $message = $status == 'passwords.throttled' ? 'Reset link allready send to your mail.' : $status;

        return $status === Password::RESET_LINK_SENT
        ? response()->json([
            'success' => true,
            'message' => 'Password recover token send to your email'
        ])
        : response()->json(['success' => false, 'message' => $message], 401);
    }

    /**
     *     * Password reset.
     *
     * @param PasswordResetRequest $request
     * @return mixed
     */
    public function passwordReset(PasswordResetRequest $request): mixed
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => $password,
                ]);

                $user->save();
            }
        );

        $message = $status == 'passwords.token' ? 'Invalied Token' : $status;

        $user = User::where('email', $request->email)->first();

        return $status === Password::PASSWORD_RESET
        ? $this->refresh()
        : response()->json(['success' => false, 'message' => $message], 401);
    }
}
