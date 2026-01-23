<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class TablesController extends Controller
{
    public function tables()
    {
        return view('pages.tables');
    }

    public function grid_tables()
    {
        return view('pages.grid-tables');
    }

    public function data_tables()
    {
        return view('pages.data-tables');
    }

}
