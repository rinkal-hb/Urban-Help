<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UtilitiesController extends Controller
{
    public function avatars()
    {
        return view('pages.avatars');
    }

    public function borders()
    {
        return view('pages.borders');
    }

    public function breakpoints()
    {
        return view('pages.breakpoints');
    }

    public function colors()
    {
        return view('pages.colors');
    }

    public function columns()
    {
        return view('pages.columns');
    }

    public function flex()
    {
        return view('pages.flex');
    }

    public function gutters()
    {
        return view('pages.gutters');
    }

    public function helpers()
    {
        return view('pages.helpers');
    }

    public function positions()
    {
        return view('pages.positions');
    }

    public function more()
    {
        return view('pages.more');
    }

}
