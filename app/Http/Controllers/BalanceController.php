<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Balance;

class BalanceController extends Controller
{
    public function getBalance()
    {
        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Retrieve the balance from Stripe
        $balance = Balance::retrieve();

        // Pass the balance data to the view
        return view('balance.index', ['balance' => $balance]);
    }
}
