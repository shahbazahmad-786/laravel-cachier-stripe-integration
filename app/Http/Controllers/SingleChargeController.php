<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SingleChargeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('single-charge',[
            'intent' => $user->createSetupIntent(),
        ]);
    }

    public function singleCharge(Request $request)
    {
        $amount = $request->input('amount');
        $amount *= 100;
        $paymentMethod = $request->input('payment_method');

        $user = auth()->user();
        $user->createOrGetStripeCustomer();

        $paymentMethod = $user->addPaymentMethod($paymentMethod);

        $user->charge($amount, $paymentMethod->id, [
            'return_url' => route('single-charge') // Specify your return URL here
        ]);

        return to_route('single-charge')->with('success', 'Payment successful!');
    }
}
