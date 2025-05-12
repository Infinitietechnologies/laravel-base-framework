<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Interfaces\AuthInterface;
use App\Traits\AuthTrait;
use Illuminate\Http\Request;

class MerchantAuthController extends Controller implements AuthInterface
{
    use AuthTrait;
}
