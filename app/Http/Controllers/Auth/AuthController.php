<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneraleTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use GeneraleTrait;
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
     * listing users
     *  @return Illuminate\Http\JsonResponse
     *
     */
    function index()
    {
        $users = User::all();
        return $this->returnData("data", $users, 200, "users selected !");
    }
    /**
     * listing user id
     *  @return Illuminate\Http\JsonResponse
     * @var int $id
     */
    function show($id)
    {
        $userId = User::find($id);
        if (!$userId) {
            return $this->returnError(404, "No users found !");
        }
        return
            $this->returnData("data", $userId, 200, "User with $id found !");
    }
    /**
     * Create new user
     */
    function register(Request $request)
    {
        // instance new user
        $user           = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);

        if (!$user->save()) {
            return $this->returnError(500, "Internal server error");
        }

        return $this->returnSuccessMessage(200, "User created !");
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return $this->returnError(401, "Unauthorized");
        }

        $tokenAuth = $this->respondWithToken($token);
        return $this->returnData("data", $tokenAuth->original, 200, "User logged !");
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $authUser = response()->json(auth()->user());
        return $this->returnData("data", $authUser, 200, "Auth user details !");
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        if (!auth()->logout()) {
            return $this->returnError(500, "Internal server error");
        }

        return $this->returnSuccessMessage(200, "Successfully logged out");
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
