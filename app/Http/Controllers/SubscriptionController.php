<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Cashier\Subscription;
use Stripe\Plan;
use App\Models\Plan as PlanModel;
use Stripe\Stripe;
use Stripe\Charge;

class SubscriptionController extends Controller
{
    public function showPlans()
    {
        $basic = PlanModel::where('name', 'basic')->first();
        $professional = PlanModel::where('name', 'professional')->first();
        $enterprise = PlanModel::where('name', 'enterprise')->first();

        return view('plans.index',[
            'basic' => $basic,
            'professional' => $professional,
            'enterprise' => $enterprise
        ]);
    }

    public function createPlans()
    {
        return view('plans.create');
    }

    public function storePlans(Request $request)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $amount = ($request->amount * 100);
        $currency = $request->currency;
        $name = $request->name;
        $interval_count = $request->interval_count;
        $billing_period = $request->billing_period;

        try {
           $plan = Plan::create([
                'amount' => $amount,
                'currency' => $currency,
                'interval' => $billing_period,
                'interval_count' => $interval_count,
                'product' => [
                    'name' => $name,
                ],
           ]);

//           dd($plan); // see all values from $plan

           PlanModel::create([
               'plan_id' => $plan->id,
               'name' => $name,
               'price' => $plan->amount,
               'billing_method' => $plan->interval,
               'currency' => $plan->currency,
               'interval_count' => $plan->interval_count,
           ]);
        } catch (\Exception $e){
           return $e->getMessage();
        }

        return 'success';
    }

    public function checkout($id)
    {
        $plan = PlanModel::where('plan_id', $id)->first();

        if (!$plan)
            return back()->with('error', 'Plan not found');

        return view('plans.checkout',[
            'plan' => $plan,
            'intent' => auth()->user()->createSetupIntent()
        ]);
    }

    public function process(Request $request)
    {
            $user = $request->user();
            $user->createOrGetStripeCustomer();
            $paymentMethod = null;
            $paymentMethod = $request->payment_method;
            $plan = $request->plan_id;

            if ($paymentMethod != null) {
                $paymentMethod = $user->addPaymentMethod($paymentMethod);
            }

            try {
                $user->newSubscription('default', $plan)
                    ->create($paymentMethod != null ? $paymentMethod->id : null);
            } catch (\Exception $e) {
                    return back()->with('error', $e->getMessage());
            }

            return back()->with('success', 'Subscription has been successfully processed');
    }

    public function showSubscriptions()
    {
        $subscriptions = Subscription::where('user_id',auth()->id())->get();

        return view('subscriptions.index',[
            'subscriptions' => $subscriptions
        ]);
    }

    public function cancelSubscriptions(Request $request)
    {
        $name = $request->name;
        $user = auth()->user();

        if ($name){
            $user->subscription($name)->cancel();
            return "Subscription has been cancelled";
        }
    }

    public function resumeSubscriptions(Request $request)
    {
        $name = $request->name;
        $user = auth()->user();

        if ($name){
            $user->subscription($name)->resume();
            return "Subscription has been resume";
        }
    }

    public function getTransactionHistory()
    {
        $user = auth()->user();

        // Fetch Invoices
        $invoices = $user->invoices()->map(function ($invoice) {
            return [
                'type' => 'invoice',
                'id' => $invoice->id,
                'amount' => $invoice->total(),
                'status' => $invoice->status,
                'created_at' => $invoice->date()->toDateTimeString(),
                'url' => $invoice->hosted_invoice_url,
            ];
        });

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Fetch Charges
        if ($user && $user->stripe_id) {
            $charges = Charge::all([
                'limit' => 10,
                'customer' => $user->stripe_id,
            ])->data;

            $formattedCharges = collect($charges)->map(function ($charge) {
                return [
                    'type' => 'charge',
                    'id' => $charge->id,
                    'amount' => $charge->amount,
                    'status' => $charge->status,
                    'created_at' => \Carbon\Carbon::createFromTimestamp($charge->created)->toDateTimeString(),
                    'receipt_url' => $charge->receipt_url,
                ];
            });
        } else {
            return "User does not have a Stripe customer ID.";
        }

        // Combine invoices and charges
        $transactions = $invoices->concat($formattedCharges);

        // Optionally sort by date (most recent first)
        $transactions = $transactions->sortByDesc('created_at')->values();

        return view('subscriptions.transactions-history',[
            'transactions' => $transactions,
        ]);
    }
}
