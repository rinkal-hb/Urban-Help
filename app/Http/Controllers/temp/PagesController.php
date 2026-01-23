<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function aboutus()
    {
        return view('pages.aboutus');
    }

    public function blog()
    {
        return view('pages.blog');
    }

    public function blog_details()
    {
        return view('pages.blog-details');
    }

    public function blog_create()
    {
        return view('pages.blog-create');
    }

    public function chat()
    {
        return view('pages.chat');
    }

    public function contacts()
    {
        return view('pages.contacts');
    }

    public function contactus()
    {
        return view('pages.contactus');
    }

    public function add_products()
    {
        return view('pages.add-products');
    }

    public function cart()
    {
        return view('pages.cart');
    }

    public function checkout()
    {
        return view('pages.checkout');
    }

    public function edit_products()
    {
        return view('pages.edit-products');
    }

    public function order_details()
    {
        return view('pages.order-details');
    }

    public function orders()
    {
        return view('pages.orders');
    }

    public function products()
    {
        return view('pages.products');
    }

    public function products_details()
    {
        return view('pages.products-details');
    }

    public function products_list()
    {
        return view('pages.products-list');
    }

    public function wishlist()
    {
        return view('pages.wishlist');
    }

    public function mail()
    {
        return view('pages.mail');
    }

    public function mail_settings()
    {
        return view('pages.mail-settings');
    }

    public function empty_page()
    {
        return view('pages.empty-page');
    }

    public function faqs()
    {
        return view('pages.faqs');
    }

    public function filemanager()
    {
        return view('pages.filemanager');
    }

    public function invoice_create()
    {
        return view('pages.invoice-create');
    }

    public function invoice_details()
    {
        return view('pages.invoice-details');
    }

    public function invoice_list()
    {
        return view('pages.invoice-list');
    }

    public function landing()
    {
        return view('pages.landing');
    }

    public function landing_jobs()
    {
        return view('pages.landing-jobs');
    }

    public function notifications()
    {
        return view('pages.notifications');
    }

    public function pricing()
    {
        return view('pages.pricing');
    }

    public function profile()
    {
        return view('pages.profile');
    }

    public function reviews()
    {
        return view('pages.reviews');
    }

    public function teams()
    {
        return view('pages.teams');
    }

    public function terms_conditions()
    {
        return view('pages.terms-conditions');
    }

    public function timeline()
    {
        return view('pages.timeline');
    }

    public function todo_list()
    {
        return view('pages.todo-list');
    }

}
