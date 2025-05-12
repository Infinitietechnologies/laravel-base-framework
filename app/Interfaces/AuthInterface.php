<?php

namespace App\Interfaces;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface AuthInterface
{
    public function logout(Request $request): JsonResponse;
    public function login(Request $request): JsonResponse;
}
