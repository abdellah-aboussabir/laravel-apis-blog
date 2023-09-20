<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
     * listing users
     *  @return Illuminate\Http\JsonResponse
     *
     */
    function index() {
        return response()->json([User::all()]);
    }
    /**
     * listing user id
     *  @return Illuminate\Http\JsonResponse
     * @var int $id
     */
    function show($id) {
        $userId = User::find($id);
        if(!$userId){
            return response()->json([
                "status" =>404,
                "msg" => "No user with $id"
            ]);
        }
        return response()->json([
            "status" =>200,
            "msg" => "User found !",
            "data" => $userId
        ]);
    }
    /**
     * Create new user
     */
    function register(Request $request)  {
        // instance new user
        $user           = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = Hash::make($request->password);
        if (!$user->save()) {
            return response()->json([
                "status" =>500,
                "msg" => "Error"
            ]);
        }

        return response()->json([
            "status" =>200,
            "msg" => "User Created Successfully !"
        ]);

    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
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
