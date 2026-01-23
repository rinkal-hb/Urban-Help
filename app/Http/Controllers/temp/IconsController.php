<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class IconsController extends Controller
{
    public function icons()
    {
        return view('pages.icons');
    }
}
