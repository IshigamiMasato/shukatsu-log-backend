<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;
use Laravel\Lumen\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use ResponseTrait;
}
