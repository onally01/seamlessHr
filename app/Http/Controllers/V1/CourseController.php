<?php

namespace App\Http\Controllers\V1;

use App\Exports\CoursesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRegistration;
use App\Http\Requests\CourseRegistrationRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\UserCourseResource;
use App\Jobs\CourseFactoryJob;
use App\Repositories\Course\CourseInterface;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class CourseController extends Controller
{
    protected $responseService, $course;

    public function __construct(CourseInterface $course, ResponseService $responseService)
    {
        $this->responseService = $responseService;
        $this->course = $course;
    }

    public function create()
    {
        $checkCourse = $this->course->all();

        if (count($checkCourse) >= 1){

            $courseResource = CourseResource::collection($checkCourse);
            return $this->responseService->getSuccessResource(['data' => $courseResource]);
        }

        //Queue course factory
        dispatch(new CourseFactoryJob());

        $course = $this->course->all();

        return $this->responseService->getSuccessResource(['data'=>CourseResource::collection($course)]);
    }

    public function register(CourseRegistrationRequest $request){
        $user = Auth::user();

        //Loop through selected course
        foreach ($request->courses as $course){

            $data = [
                'course_id' => $course
            ];

            //Disable foreign key check so as to update pivot table
            Schema::disableForeignKeyConstraints();

            $user->courses()->updateOrCreate($data);

            Schema::enableForeignKeyConstraints();
        }

        return $this->responseService->getSuccessResource();
    }

    public function all(){
        //All courses
        $course = $this->course->all();
        $courseResource = CourseResource::collection($course);

        //User registered course
        $registeredCourse = UserCourseResource::collection(Auth::user()->courses);

        return $this->responseService->getSuccessResource([
            'data'=> [
               'registered_courses' => $registeredCourse,
                'courses ' =>$courseResource
            ]
        ]);
    }

    public function export(){
       return Excel::download(new CoursesExport,'courses.xlsx');
    }


}
