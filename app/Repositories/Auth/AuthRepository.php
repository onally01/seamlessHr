<?php


namespace App\Repositories\Auth;

use App\Http\Resources\UserResource;
use App\Services\ResponseService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthInterface
{
    protected $user, $responseService;

    public function __construct(User $user, ResponseService $responseService)
    {
        $this->user = $user;
        $this->responseService = $responseService;
    }

    public function create(array $params){
        $model = $this->user;
        $this->setModelProperties($model, $params);
        $model->save();
        return $model;
    }

    //Api authentication
    public function login(array $params){

        $user = $this->user::where('email', $params['email'])->first();



        if (!$user) {
            return $this->responseService->getErrorResource([
                'message'=>'Wrong Email'
            ]);
        }


        // If a user with the email was found - check if the specified password
        // belongs to this user
        if (!Hash::check($params['password'], $user->password)) {
            return $this->responseService->getErrorResource([
                'message'=>'Wrong password'
            ]);
        }

        if (Auth::attempt(['email' => $params['email'], 'password' => $params['password']])){

            $user = $this->authUser();
            $user['token'] = $user->createToken('token')->accessToken;
            $resource = new UserResource($user);
            return $this->responseService->getSuccessResource(['data'=>$resource]);
        } else {
            // Auth::logout();
            return $this->responseService->getErrorResource([
                'message'=>'Contact system administrator'
            ]);
        }
    }

    public function logout()
    {

        $accessToken = auth()->user()->token();

        DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->delete();
        // $revoked = DB::table('oauth_access_tokens')->where('id', '=', $id)->update(['revoked' => 1]);
        // logout from all devices
        DB::table('oauth_access_tokens')
            ->where('user_id', Auth::user()->id)
            ->delete();

        $accessToken->revoke();
       // Auth::logout();

        return $this->responseService->getSuccessResource([
            'message'=>'Logout Successful'
        ]);

    }

    public function findById(int $id)
    {
        return $this->user::find($id);
    }


    public function findByColumn(array $params)
    {
        return $this->user::where($params);
    }

    public function authUser(){
        return Auth::user();
    }

    private function setModelProperties($model, $params){
        $model->name = $params['name'];
        $model->email = $params['email'];
        $model->password = bcrypt($params['password']);
    }
}