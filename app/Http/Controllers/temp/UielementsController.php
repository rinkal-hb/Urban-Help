<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class UielementsController extends Controller
{
    public function alerts()
    {
        return view('pages.alerts');
    }

    public function badges()
    {
        return view('pages.badges');
    }

    public function breadcrumbs()
    {
        return view('pages.breadcrumbs');
    }

    public function buttons()
    {
        return view('pages.buttons');
    }

    public function buttongroups()
    {
        return view('pages.buttongroups');
    }
    public function cards()
    {
        return view('pages.cards');
    }

    public function dropdowns()
    {
        return view('pages.dropdowns');
    }

    public function images_figures()
    {
        return view('pages.images-figures');
    }

    public function listgroups()
    {
        return view('pages.listgroups');
    }

    public function navs_tabs()
    {
        return view('pages.navs-tabs');
    }

    public function object_fit()
    {
        return view('pages.object-fit');
    }

    public function paginations()
    {
        return view('pages.paginations');
    }

    public function popovers()
    {
        return view('pages.popovers');
    }
    
    public function progress()
    {
        return view('pages.progress');
    }

    public function spinners()
    {
        return view('pages.spinners');
    }

    public function toasts()
    {
        return view('pages.toasts');
    }

    public function tooltips()
    {
        return view('pages.tooltips');
    }

    public function typography()
    {
        return view('pages.typography');
    }
    
}
