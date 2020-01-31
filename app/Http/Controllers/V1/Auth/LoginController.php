<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Repositories\Auth\AuthInterface;
use App\Services\ResponseService;



class LoginController extends Controller
{
    protected $auth, $responseService;

    public function __construct(AuthInterface $auth , ResponseService $responseService)
    {
        
        $this->auth = $auth;
        $this->responseService = $responseService;
        
    }

    public function index(LoginRequest $request){
        
        $params = $request->all();
        return $this->auth->login($params);
    }

    public function logout(){
       return $this->auth->logout();
    }

    public function unauthenticatedResponse(){

        return $this->responseService->getErrorResource([
            'message'=>'Unauthenticated',
            'status_code' => '401'
        ]);

    }
}
