<?php

namespace App\Repositories\Auth;

interface AuthInterface
{
    public function login(array $params);

    public function logout();

    public function findById(int $id);

    public function findByColumn(array $params);

    public function authUser();

    public function create(array $params);

}