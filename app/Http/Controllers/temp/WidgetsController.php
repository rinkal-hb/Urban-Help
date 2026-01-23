<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class WidgetsController extends Controller
{
    public function widgets()
    {
        return view('pages.widgets');
    }
}
