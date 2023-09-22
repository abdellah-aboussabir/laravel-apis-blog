<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\Pagination;
use App\Http\Traits\GeneraleTrait;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use GeneraleTrait;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     * @param Pagination $request
     */
    public function index(Pagination $request) :JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $users = User::paginate($validatedData['itemPerPage']);
            return $this->returnData('data', $users, 200, "Users selected");
        } catch (ClientException $ex) {
            return $this->returnError(500, "Error occured", null);
        }
    }


    /**
     * Display the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public static function show(int $id):JsonResponse
    {
        try {
            $userID = User::find($id);
            if (!$userID) {
                return self::returnData("data", [], 404, "User with id : $id not found");
            }
            return self::returnData('data', $userID, 200, "User with id : $id selected");
        } catch (ClientException $ex) {
            return self::returnError(500, "Error occured", null);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @return JsonResponse
     * @param int $id
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $userID = User::find($id);
            if (!$userID) {
                return $this->returnData("data", [], 404, "User with id : $id not found");
            }
            if (!$userID->delete()) {
                return $this->returnError(500, "Error occured", null);
            }
            return $this->returnSuccessMessage(200, "User with id : $id deleted");
        } catch (ClientException $ex) {
            return $this->returnError(500, "Error occured", null);
        }
    }
}
