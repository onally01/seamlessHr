<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Requests\BasicInfoRegistrationStepOneRequest;
use App\Http\Requests\BasicInfoRegistrationStepTwoRequest;
use App\Http\Requests\BvnRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\BvnResource;
use App\Http\Resources\LoanResource;
use App\Http\Resources\UserResource;
use App\Repositories\ApplicantBioData\ApplicantBioDataInterface;
use App\Repositories\ApplicantNextOfKin\ApplicantNextOfKinInterface;
use App\Repositories\Auth\AuthInterface;
use App\Repositories\VerifyEmail\VerifyEmailInterface;
use App\Services\Paystack\PaystackService;
use App\Services\ResponseService;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class RegistrationController extends Controller
{
    protected $auth, $responseService;

    public function __construct(AuthInterface $auth, ResponseService $responseService)
    {
        $this->auth = $auth;
        $this->responseService = $responseService;
    }

    public function create(RegistrationRequest $request){

        try {

            $params = $request->all();

            $this->auth->create($params);

            return $this->responseService->getSuccessResource();

        }catch (\Exception $e){

            Log::error('Failed New User Registration '.$e); //log error e.g slack
            return $this->responseService->getErrorResource([
                'message' => 'OOPS!!! Something went wrong, please contact system admin'
            ]);
        }

    }

}
