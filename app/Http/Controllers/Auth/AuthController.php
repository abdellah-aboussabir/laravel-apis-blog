<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Traits\GeneraleTrait;
use App\Models\User;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
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
        $this->middleware('auth:api', ['except' => ['login', 'register', 'resetPassword']]);
    }

    /**
     * register new user
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * -----------------@Reponses-------------------
     * ----- 201 : User Created Successfully    ----
     * ----- 500 : Something went wrong         ----
     * ----- 422 : The given data was invalid   ----
     * ----- 409 : Email Already Used           ----
     * ---------------------------------------------
     */
    public function register(RegisterRequest $request)
    {
        try {

            // validation
            $validatedData = $request->validated();

            // new-user
            $newUser = new User();
            $newUser->name = $validatedData['name'];
            $newUser->email = $validatedData['email'];
            $newUser->password = Hash::make($validatedData['password']);

            if (!$newUser->save()) {
                return $this->returnError(500, "Something  Went Wrong");
            }

            return $this->returnSuccessMessage(201, "User Created Successfully");
        } catch (ClientException $e) {
            return $this->returnError(500, "Something  Went Wrong");
        }
    }

    /**
     * login
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * -----------------@Reponses-------------------
     * ----- 200 : Logged Successfully          ----
     * ----- 500 : Something Went Wrong         ----
     * ----- 422 : The given data was invalid   ----
     * ----- 401 : Unauthorized                 ----
     * ---------------------------------------------
     *
     */
    public function login(LoginRequest $request)
    {
        try {

            // validation
            $validatedData = $request->validated();
            $validatedData['email'];
            $validatedData['password'];

            // check-is-Authenticated
            $credentials =  request(['email', 'password']);

            if (!$token = auth()->attempt($credentials)) {
                return $this->returnError(401, "Unauthorized");
            }

            // return-token && user_type
            $details_response_token = $this->respondWithToken($token)->original;

            return $this->returnData("data", $details_response_token, 200, "Logged Successfully");
        } catch (\Exception $e) {
            return $this->returnError(500, "Something  Went Wrong");
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * -----------------@Reponses-------------------
     * ----- 200 : User Found Successfully      ----
     * ----- 403 : UnAuthenticated              ----
     * ---------------------------------------------
     *
     */
    public function me()
    {
        return $this->returnData("data", auth()->user(), 200, "You're Welcome !");
    }

    /**
     * logout
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * -----------------@Reponses-------------------
     * ----- 200 : Logged Out Successfully      ----
     * ----- 500 : Something  went wrong        ----
     * ----- 403 : UnAuthenticated              ----
     * ---------------------------------------------
     *
     */
    public function logout(Request $request)
    {
        $token =  $request->header('Authorization');
        $tokenFormatted = str_replace("Bearer ", "", $token);

        if ($tokenFormatted) {
            try {
                JWTAuth::setToken($tokenFormatted)->invalidate();
            } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
                return $this->returnError(500, "Something  Went Wrong");
            } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
                return $this->returnError(500, "Something  Went Wrong");
            }
            return $this->returnSuccessMessage(200, "Logged Out Successfully");
        } else {
            return $this->returnError(403, "UnAuthenticated");
        }
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * -----------------@Reponses-------------------
     * ----- 200 : Token Refreshed Successfully ----
     * ----- 500 : Something Went Wrong         ----
     * ----- 403 : UnAuthenticated              ----
     * ---------------------------------------------
     *
     */
    public function refresh()
    {
        try {
            if (auth()->user()) {
                $refreshToken = $this->respondWithToken(auth()->refresh());

                return $this->returnData("data", $refreshToken->original, 200, "Token Refreshed Successfully");
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->returnError(500, "Something  Went Wrong");
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->returnError(500, "Something  Went Wrong");
        }

        return $this->returnError(403, "UnAuthenticated");
    }

    /**
     * Reset Password.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * -----------------@Reponses--------------------
     * ----- 200 : Password Reseted Successfully ----
     * ----- 500 : Something Went Wrong          ----
     * ----- 404 : Email Not Found               ----
     * ----------------------------------------------
     *
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {

            // validation
            $validatedData = $request->validated();

            $user = User::where('email',  $validatedData['email'])->first();

            if (!$user) {
                return $this->returnError(404, "User Email Not Found !");
            }

            $user->password                     = Hash::make($validatedData['password']);
            $user->updated_at                   = $validatedData['updated_at'];
            $user->generate_reset_password      = null;
            $user->generate_reset_password_at   = null;
            $user->email_verified_at            = $validatedData['updated_at'];

            if ($user->save()) {
                return $this->returnSuccessMessage(200, "Password Reseted Successfully !");
            }
        } catch (Exception $e) {
            return $this->returnError(500, "Something  Went Wrong");
        }
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
