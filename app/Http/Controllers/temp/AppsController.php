<?php

namespace App\Http\Controllers\temp;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AppsController extends Controller
{
    public function full_calendar()
    {
        return view('pages.full-calendar');
    }

    public function gallery()
    {
        return view('pages.gallery');
    }

    public function sweet_alerts()
    {
        return view('pages.sweet-alerts');
    }

    public function projects_list()
    {
        return view('pages.projects-list');
    }

    public function projects_overview()
    {
        return view('pages.projects-overview');
    }

    public function projects_create()
    {
        return view('pages.projects-create');
    }

    public function job_details()
    {
        return view('pages.job-details');
    }

    public function job_company_search()
    {
        return view('pages.job-company-search');
    }

    public function job_search()
    {
        return view('pages.job-search');
    }
    public function job_post()
    {
        return view('pages.job-post');
    }

    public function job_list()
    {
        return view('pages.job-list');
    }

    public function job_candidate_search()
    {
        return view('pages.job-candidate-search');
    }

    public function job_candidate_details()
    {
        return view('pages.job-candidate-details');
    }

    public function nft_marketplace()
    {
        return view('pages.nft-marketplace');
    }

    public function nft_details()
    {
        return view('pages.nft-details');
    }

    public function nft_create()
    {
        return view('pages.nft-create');
    }

    public function nft_wallet_integration()
    {
        return view('pages.nft-wallet-integration');
    }

    public function nft_live_auction()
    {
        return view('pages.nft-live-auction');
    }

    public function crm_contacts()
    {
        return view('pages.crm-contacts');
    }

    public function crm_companies()
    {
        return view('pages.crm-companies');
    }

    public function crm_deals()
    {
        return view('pages.crm-deals');
    }

    public function crm_leads()
    {
        return view('pages.crm-leads');
    }

    public function crypto_transactions()
    {
        return view('pages.crypto-transactions');
    }

    public function crypto_currency_exchange()
    {
        return view('pages.crypto-currency-exchange');
    }

    public function crypto_buy_sell()
    {
        return view('pages.crypto-buy-sell');
    }

    public function crypto_marketcap()
    {
        return view('pages.crypto-marketcap');
    }

    public function crypto_wallet()
    {
        return view('pages.crypto-wallet');
    }

}
