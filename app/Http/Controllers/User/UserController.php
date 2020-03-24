<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\NewsAuthor;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;



class UserController extends ApiController {


    /**
     * @SWG\Get(
     *   path="/users/",
     *   tags={"users"},
     *   summary="Get users",
     *   description="Get list of users",
     *   operationId="user",
     *   produces={"application/json"},
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(
     *     response=404,
     *     description="User not found")
     * )
     */

    public function __construct(){ }

    public function index()
    {
        $users = User::all();
        return $this->printAll($users);
    }

    public function login(Request $request) {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

//        $credentials = request(['email', 'password']);
        $credentials = array( 'email' => $request->email, 'password'=> $request->password, 'active'=>true );

        if ( ! Auth::attempt($credentials)) {
            return $this->errorResponse( __('Login Failed'), 401);
        }

        $user        = $request->user();
        $restToken   = $user->createToken('personal access token');
        $token       = $restToken->token;
        $token->save();

        $result =  array(
            'access_token' => $restToken->accessToken,
            'userDetails' => $this->getUserDetails($user->id),
        );

        return $this->printData( $result, 200);

    }

    public function register(Request $request)
    {

        $this->validate($request, [
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'email'         => 'required|email|unique:users',
            'password'      => 'required|string',
            'c_password'    => 'required|same:password',
        ]);

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        return $this->printSingle($user, 201);
    }

    public function logout( Request $request) {

        return $this->printData($request->user()->token()->revoke());
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], 201);
    }

    private function getUserDetails($userId) {

        $user = User::find($userId);
        $authors = NewsAuthor::where('user_id', $user->assoc_id);
        $result = array(
            'user_id'=>$user->id,
            'first_name'=>$user->first_name,
            'last_name'=>$user->last_name,
            'email'=>$user->email,
            'authors' => $authors->first(),
        );

        return $result;
    }

    public function setUserStatus(Request $request) {

        $request->validate([
            'user_id' => 'required',
        ]);

        $result = array();

        try {
            $user = User::findOrFail($request->user_id);
            $user->active = !$user->active;
            $user->save();
            $result = array(
                'user_id' => $user->id,
                'active'  => $user->active,
            );

        } catch ( Exception $e) {
            $this->errorResponse( $e->getMessage());
        }

        return $this->printData( $result, 200);

    }
}
