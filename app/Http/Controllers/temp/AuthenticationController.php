<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function comingsoon()
    {
        return view('pages.comingsoon');
    }

    public function createpassword_basic()
    {
        return view('pages.createpassword-basic');
    }

    public function createpassword_cover()
    {
        return view('pages.createpassword-cover');
    }

    public function lockscreen_basic()
    {
        return view('pages.lockscreen-basic');
    }

    public function lockscreen_cover()
    {
        return view('pages.lockscreen-cover');
    }

    public function resetpassword_basic()
    {
        return view('pages.resetpassword-basic');
    }

    public function resetpassword_cover()
    {
        return view('pages.resetpassword-cover');
    }

    public function signup_basic()
    {
        return view('pages.signup-basic');
    }

    public function signup_cover()
    {
        return view('pages.signup-cover');
    }

    public function signin_basic()
    {
        return view('pages.signin-basic');
    }

    public function signin_cover()
    {
        return view('pages.signin-cover');
    }

    public function twostep_verification_basic()
    {
        return view('pages.twostep-verification-basic');
    }

    public function twostep_verification_cover()
    {
        return view('pages.twostep-verification-cover');
    }

    public function under_maintenance()
    {
        return view('pages.under-maintenance');
    }

}
