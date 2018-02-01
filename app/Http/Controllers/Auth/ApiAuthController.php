<?php
namespace App\Http\Controllers\Auth;

use App\Events\UserRegistered;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

class ApiAuthController extends LoginController
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var JWTAuth
     */
    private $jwtAuth;

    /**
     * ApiLoginController constructor.
     *
     * @param User $user
     * @param JWTAuth $jwtAuth
     */
    public function __construct(User $user, JWTAuth $jwtAuth)
    {
        parent::__construct();
        $this->user = $user;
        $this->jwtAuth = $jwtAuth;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        try {
            $token = $this->jwtAuth->attempt($credentials);

            if (!$token) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to create token'
            ], 401);
        }

        return response()->json([
            'token' => $token,
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->user->create($request->validatedOnly());
        event(new UserRegistered($user));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $token = $this->jwtAuth->parseToken()->refresh();
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('jwt-auth', $e->getMessage(), $e, $e->getCode());
        }

        return response()->json([
            'token' => $token,
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        return response()->json([
            'message' => 'Success',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUser(){
        $user = $this->jwtAuth->toUser();

        return response()->json(['result' => $user]);
    }
}