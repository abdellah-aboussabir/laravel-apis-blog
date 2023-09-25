<?php

namespace App\Http\Controllers\Post;


use App\Http\Controllers\Controller;
use App\Http\Controllers\User\UserController;
use App\Http\Requests\Common\Pagination;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Traits\GeneraleTrait;
use App\Models\Post;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostController extends Controller
{
    use GeneraleTrait;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }
    /**
     * @return JsonResponse
     * @param Pagination $request
     * Display a listing of the resource.
     */
    public function index(Pagination $request):JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $posts = Post::paginate($validatedData['itemPerPage']);
            if(!$posts){
                return $this->returnData("data",[],404, "No posts found");
            }
            return $this->returnData("data", $posts, 200, "Posts selected" );
        }catch (ClientException $ex){
            return self::returnError(500, "Error occurred", $ex);
    }
    }

    /**
     * @return JsonResponse
     * @param int $id
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        try {
             $postID = Post::find($id);
            if(!$postID){
                return $this->returnData("data",[],404, "No post with id : $id found");
            }
            return $this->returnData("data", $postID, 200, "Post with id : $id selected" );
        }catch (ClientException $ex){
            return self::returnError(500, "Error occurred", $ex);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param StorePostRequest $request
     * @return JsonResponse
     */
    public function store(StorePostRequest $request) :JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $isUserIdExist = UserController::show($validatedData['user_id']);

            if ($isUserIdExist->data){
                $newPost = new Post();
                $newPost->user_id = $validatedData['user_id'];
                $newPost->title = $validatedData['title'];
                $newPost->content = $validatedData['content'];

                if (!$newPost->save()){
                    return self::returnError(500, 'Error Occurred', "Not Saved");
                }

                return self::returnSuccessMessage(200, "Post created");
            }

            return self::returnError(500, "Error occurred", "user_id not found");

        }catch (ClientException $ex){
            return self::returnError(500, "Error occurred", $ex);

        }
    }

    /**
     * Update the specified resource in storage.
     * @param UpdatePostRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdatePostRequest $request, int $id) : JsonResponse
    {
        try {
            $postID = self::show($id);
             if (!$postID->original['data']){
                self::returnError(500,"Post with id :$id not found", null);
            }
            $validatedData = $request->validated();

            $postID->title = $validatedData['title'];
            $postID->content = $validatedData['content'];

            if (!$postID->update()){
                return self::returnError(500, 'Error Occurred', "Not Saved");
            }

            return self::returnSuccessMessage(200, "Post updated");

        }catch (ClientException $ex){
            return self::returnError(500, "Error occurred", $ex);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id) : JsonResponse
    {
        try {
            $postID = $this->show($id);
            if (!$postID->data){
                return self::returnError(404, "No post with id : $id to delete", null);
            }
            if (!$postID->data->delete()){
                return self::returnError(404, "Post not deleted", null);
            }

            return self::returnSuccessMessage(200, "Post deleted");

        }catch (ClientException $ex){
            return self::returnError(500, "Error occurred", $ex);

        }
    }
}
