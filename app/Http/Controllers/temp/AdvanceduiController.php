<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AdvanceduiController extends Controller
{
    public function accordions_collapse()
    {
        return view('pages.accordions-collapse');
    }

    public function carousel()
    {
        return view('pages.carousel');
    }

    public function draggable_cards()
    {
        return view('pages.draggable-cards');
    }

    public function modals_closes()
    {
        return view('pages.modals-closes');
    }

    public function navbars()
    {
        return view('pages.navbars');
    }

    public function offcanvas()
    {
        return view('pages.offcanvas');
    }

    public function placeholders()
    {
        return view('pages.placeholders');
    }

    public function ratings()
    {
        return view('pages.ratings');
    }

    public function scrollspy()
    {
        return view('pages.scrollspy');
    }

    public function swiperjs()
    {
        return view('pages.swiperjs');
    }

}
