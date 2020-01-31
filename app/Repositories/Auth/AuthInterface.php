<?php

namespace App\Repositories\Auth;

interface AuthInterface
{
    public function login(array $params);

    public function logout();

    public function create(array $params);

}