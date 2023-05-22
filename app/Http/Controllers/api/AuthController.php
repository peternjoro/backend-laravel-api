<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function sendResponse($data, $message, $status=200)
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($response, $status);
    }
    public function sendError($errorData, $message, $status=200)
    {
        $response = [];
        $response['status'] = false;
        $response['message'] = $message;
        if (!empty($errorData)) {
            $response['data'] = $errorData;
        }
        return response()->json($response, $status);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $input = $request->only('name', 'email', 'password', 'c_password');
        $validator = Validator::make($input, [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors(), 'Validation Error');
        }

        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));

        $success['user'] = $user;
        return $this->sendResponse($success, 'user registered successfully');
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $input = $request->only('email', 'password');
    	$validator = Validator::make($input, [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()){
            return $this->sendError($validator->errors(), 'Validation Error');
        }

        try
        {
            if (!$token = JWTAuth::attempt($input)) {
                return $this->sendError([], "invalid login credentials");
            }
        }
        catch (JWTException $e) {
            return $this->sendError([], $e->getMessage());
        }

        $user = auth()->user();
        $user['token'] = $token;
        // get user preferences
        $usr_id = $user->id;
        $sFields = ['user_settings.id','sources.source_id','sources.source_name'];
        $cFields = ['user_settings.id','categories.category_key','categories.category_name'];
        $aFields = ['user_settings.id','authors.author_name'];
        $my_sources = DB::table('user_settings')->join('sources','user_settings.source_id','=','sources.id')->whereIN('user_id',[$usr_id])->select($sFields)->get();
        $my_categories = DB::table('user_settings')->join('categories','user_settings.category_id','=','categories.id')->whereIN('user_id',[$usr_id])->select($cFields)->get();
        $my_authors = DB::table('user_settings')->join('authors','user_settings.author_id','=','authors.id')->whereIN('user_id',[$usr_id])->select($aFields)->get();
        $user['my_sources'] = $my_sources;
        $user['my_categories'] = $my_categories;
        $user['my_authors'] = $my_authors;

        $success = [
            $user,
            'token' => $token,
        ];

        return $this->sendResponse($user, 'successful login');
    }
    public function getUser()
    {
        $user = auth()->user();
        if(!$user){
            return $this->sendError([], "user not found");
        }
        return $this->sendResponse($user, "user found");
    }
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->invalidate(); // invalidate the active auth token
        auth()->logout();
        return response()->json(['status' => true,'message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $newToken = auth()->refresh();
        if($newToken){
            $success = [
                'token' => $newToken,
            ];
            return $this->sendResponse($success, 'token refreshed successfully');
        }
        return $this->sendError([], 'token could not be refreshed');
    }

}
