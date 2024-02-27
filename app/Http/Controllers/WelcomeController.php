<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        //echo \Request::getClientIp(true);
        $clientIP = $_SERVER['REMOTE_ADDR'];
        echo $clientIP;
    }
}
