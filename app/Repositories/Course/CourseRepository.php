<?php


namespace App\Repositories\Course;

use App\Course;
use App\Services\ResponseService;

class CourseRepository implements CourseInterface
{
    protected $course;

    public function __construct(Course $course, ResponseService $responseService)
    {
        $this->course = $course;
        $this->responseService = $responseService;
    }

    //Api authentication
    public function all(){
        return $this->course::get();
    }

}