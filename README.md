# (1) Laravel Stripe Integration

First, install the Cashier package for Stripe using the Composer package manager:

```bash
composer require laravel/cashier
```

After installing the package, publish Cashier's migrations using the vendor:publish Artisan command:

```bash
php artisan vendor:publish --tag="cashier-migrations"
```

Then, migrate your database:

```bash
php artisan migrate
```

Before using Cashier, add the Billable trait to your billable model definition. Typically, this will be the App\Models\User model. This trait provides various methods to allow you to perform common billing tasks, such as creating subscriptions, applying coupons, and updating payment method information:

```php
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
     use Billable;
}
```

You can install the bindings via Composer. Run the following command:

```bash
composer require stripe/stripe-php
```

Next, you should configure your Stripe API keys in your application's .env file. You can retrieve your Stripe API keys from the Stripe control panel:

```dotenv
STRIPE_KEY=your-stripe-key
STRIPE_SECRET=your-stripe-secret
```

The default Cashier currency is United States Dollars (USD). You can change the default currency by setting the CASHIER_CURRENCY environment variable within your application's .env file:

```dotenv
CASHIER_CURRENCY=gbp
```

You should install this package only when you are using Vite with Vue, React, or Angular:

```bash
npm install @stripe/stripe-js
```

Next,You should configure your Stripe API keys in your application's .env file. You can retrieve your Stripe API keys from the Stripe control panel only when you are using Vite with Vue, React, or Angular:

```dotenv
VITE_STRIPE_KEY=your-stripe-key
```

## Following this blog as a reference [Medium.com](https://medium.com/fabcoding/laravel-7-create-a-subscription-system-using-cashier-stripe-77cdf5c8ea5d)

Also in the services file,

### ***config/sevices.php***

Add the following array

```php
'stripe' => [
'model' => App\User::class,
'key' => env('STRIPE_KEY'),
'secret' => env('STRIPE_SECRET'),
],
```

<hr>

# (2) Create Single Charge 

### ***resources/views/single-charge.blade.php***

```bladehtml
@extends('layouts.app')

@section('style')
<style>
    .StripeElement {
        background-color: white;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid transparent;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
@endsection

@section('main')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
                @endif
                {{--        Stripe Form       --}}
                <form action="{{ route('single.charge') }}" method="POST" id="subscribe-form">
                    @csrf
                    <div>
                        <label for="amount">Amount</label> <br>
                        <input id="amount" name="amount" type="number" style="width: 100%">
                    </div>
                    <br>
                    <div>
                        <label for="card-holder-name">Card Holder Name</label> <br>
                        <input id="card-holder-name" type="text" style="width: 100%">
                    </div>
                    <br>

                    <div class="form-row">
                        <label for="card-element">Credit or debit card</label>
                        <div id="card-element" class="form-control">
                        </div>
                        <!-- Used to display form errors. -->
                        <div id="card-errors" role="alert"></div>
                    </div>
                    <div class="stripe-errors"></div>
                    @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                        @endforeach
                    </div>
                    @endif
                    <div class="form-group text-center mt-5">
                        <button  id="card-button" data-secret="{{ $intent->client_secret }}" class="text-white py-2 rounded hover:bg-green-400 px-4 bg-green-600">SUBMIT</button>
                    </div>
                </form>
                {{--        Stripe Form       --}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ env('STRIPE_KEY') }}');
    var elements = stripe.elements();
    var style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };
    var card = elements.create('card', {hidePostalCode: true,
        style: style});
    card.mount('#card-element');
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;
    cardButton.addEventListener('click', async (e) => {
        e.preventDefault();
        console.log("attempting");
        const { setupIntent, error } = await stripe.confirmCardSetup(
            clientSecret, {
                payment_method: {
                    card: card,
                    billing_details: { name: cardHolderName.value }
                }
            }
        );
        if (error) {
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
        } else {
            paymentMethodHandler(setupIntent.payment_method);
        }
    });
    function paymentMethodHandler(payment_method) {
        var form = document.getElementById('subscribe-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'payment_method');
        hiddenInput.setAttribute('value', payment_method);
        form.appendChild(hiddenInput);
        form.submit();
    }
</script>
@endsection
```

## Make SingleChargeController

```bash
php artisan make:controller SingleChargeController
```
### Paste that code in controller

```php
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
```

## Define Routes
### ***routes/web.php***

```php
Route::controller(SingleChargeController::class)->group(function () {
    Route::get('/single-charge', 'index')->name('single-charge');
    Route::post('/single-charge', 'singleCharge')->name('single.charge');
});
```

### Check output on (Stripe Developers Dashboard) for creating customer:

[Stripe.Test.Dashboard.Customer](https://dashboard.stripe.com/test/customers)

### Check output on (Stripe Developers Dashboard) for successful payment:

[Stripe.Test.Dashboard.Payments](https://dashboard.stripe.com/test/payments)

<hr>

# (3) Create Plans

## Create Model and Migration

```bash
php artisan make:model Plan -m
```

### Define these fields in migration

```php
Schema::create('plans', function (Blueprint $table) {
     $table->id();
     $table->string('plan_id');
     $table->string('name');
     $table->string('billing_method');
     $table->tinyInteger('interval_count')->default(1);
     $table->string('price');
     $table->string('currency');
     $table->timestamps();       
});
```

### Migrate this migration

```bash
php artisan migrate
```

### Define these fields in Model (App/Models/Plan.php)

```php
protected $fillable = [
    'plan_id',
    'name',
    'billing_method',
    'interval_count',
    'price',
    'currency',
];
```

## Make SubscriptionController

```bash
php artisan make:controller SubscriptionController
```
### Paste that code in controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Plan;
use App\Models\Plan as PlanModel;

class SubscriptionController extends Controller
{
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
}
```

## Define Routes
### ***routes/web.php***

```php
 Route::controller(SubscriptionController::class)->group(function () {
      Route::get('/plans/create', 'createPlans')->name('plans.create');
      Route::post('/plans/store', 'storePlans')->name('plans.store');
});
```

## Set Blade View

```bladehtml
@extends('layouts.app')

@section('main')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    {{--        Plan Form       --}}
                    <form action="{{ route('plans.store') }}" method="POST">
                        @csrf
                        <div>
                            <label for="name">Plan Name</label> <br>
                            <input id="name" name="name" type="text" style="width: 100%">
                        </div>
                        <br>
                        <div>
                            <label for="amount">Amount</label> <br>
                            <input id="amount" name="amount" type="number" style="width: 100%">
                        </div>
                        <br>
                        <div>
                            <label for="currency">Currency</label> <br>
                            <input id="currency" name="currency" type="text" style="width: 100%">
                        </div>
                        <br>
                        <div>
                            <label for="interval_count">Interval Count</label> <br>
                            <input id="interval_count" name="interval_count" type="number" style="width: 100%">
                        </div>
                        <br>
                        <div>
                            <label for="billing_period">Billing Period</label> <br>
                            <select name="billing_period" style="width: 100%">
                                <option disabled selected>Choose Billing Period</option>
                                <option value="week">Weekly</option>
                                <option value="month">Monthly</option>
                                <option value="year">Yearly</option>
                            </select>
                        </div>
                        <br>
                        <div class="form-group text-center mt-5">
                            <button class="text-white py-2 rounded hover:bg-green-400 px-4 bg-green-600">SUBMIT</button>
                        </div>
                    </form>
                    {{--        Plan Form       --}}
                </div>
            </div>
        </div>
    </div>
@endsection
```

## Check output on (Stripe Developers Dashboard) for creating plans:

[Stripe.Test.Dashboard.Products](https://dashboard.stripe.com/test/products?active=true)

<hr>

# (4) Show All Plans

## Paste that code into SubscriptionController

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Plan;
use App\Models\Plan as PlanModel;

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
}
```

## Define Routes (routes/web.php)

```php
Route::controller(SubscriptionController::class)->group(function () {
    Route::get('/plans', 'showPlans')->name('plans.index');
});
```

## Set Blade View

### ***resources/views/plans/index.blade.php***

```bladehtml
@extends('layouts.app')

@section('main')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="container mx-auto py-10">
                        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                            <!-- Box 1 -->
                            <div class="bg-white shadow-md rounded-lg p-6 w-full md:w-1/3">
                                <h2 class="text-3xl font-bold mb-2 capitalize">Plan: {{ $basic->name }}</h2>
                                <br><hr><br>
                                <p><strong>Billing Method:</strong> {{ $basic->interval_count }} {{ $basic->billing_method }}</p>
                                <p><strong>Price:</strong> £{{ $basic->price }}</p>
                                <p><strong>Currency:</strong> {{ $basic->currency }}</p>
                                <a href="{{ route('plans.checkout',$basic->plan_id) }}" class="inline-block mt-4 px-4 py-2 bg-green-600 text-white font-bold rounded hover:bg-green-400">
                                    Chose Plan
                                </a>
                            </div>

                            <!-- Box 2  -->
                            <div class="bg-white shadow-md rounded-lg p-6 w-full md:w-1/3">
                                <h2 class="text-3xl font-bold mb-2 capitalize">Plan: {{ $professional->name }}</h2>
                                <br><hr><br>
                                <p><strong>Billing Method:</strong> {{ $professional->interval_count }} {{ $professional->billing_method }}</p>
                                <p><strong>Price:</strong> £{{ $professional->price }}</p>
                                <p><strong>Currency:</strong> {{ $professional->currency }}</p>
                                <a href="{{ route('plans.checkout',$professional->plan_id) }}" class="inline-block mt-4 px-4 py-2 bg-green-600 text-white font-bold rounded hover:bg-green-400">
                                    Chose Plan
                                </a>
                            </div>

                            <!-- Box 3  -->
                            <div class="bg-white shadow-md rounded-lg p-6 w-full md:w-1/3">
                                <h2 class="text-3xl font-bold mb-2 capitalize">Plan: {{ $enterprise->name }}</h2>
                                <br><hr><br>
                                <p><strong>Billing Method:</strong> {{ $enterprise->interval_count }} {{ $enterprise->billing_method }}</p>
                                <p><strong>Price:</strong> £{{ $enterprise->price }}</p>
                                <p><strong>Currency:</strong> {{ $enterprise->currency }}</p>
                                <a href="{{ route('plans.checkout',$enterprise->plan_id) }}" class="inline-block mt-4 px-4 py-2 bg-green-600 text-white font-bold rounded hover:bg-green-400">
                                    Chose Plan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
```

<hr>

# (5) Choose or Checkout a Plan

## Paste that code into SubscriptionController

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Plan;
use App\Models\Plan as PlanModel;

class SubscriptionController extends Controller
{
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
}
```

## Define Routes (routes/web.php)

```php
Route::controller(SubscriptionController::class)->group(function () {
    Route::get('/plans/checkout/{id}', 'checkout')->name('plans.checkout');
});
```

## Set Blade View

### ***resources/views/plans/checkout.blade.php***

```bladehtml
@extends('layouts.app')

@section('style')
<style>
    .StripeElement {
        background-color: white;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid transparent;
        box-shadow: 0 1px 3px 0 #e6ebf1;
        -webkit-transition: box-shadow 150ms ease;
        transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
        box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
        border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
        background-color: #fefde5 !important;
    }
</style>
@endsection

@section('main')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center">
                    <h2 class="text-3xl">Your plan is <strong class="uppercase">{{ $plan->name }}</strong></h2>
                    <h2 class="text-3xl"><strong class="uppercase">£{{ $plan->price }}</strong></h2>
                </div>
                <br><hr><br>
                @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
                @endif

                @if (session('error'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('error') }}
                </div>
                @endif
                {{--        Stripe Form       --}}
                <form action="{{ route('plans.process') }}" method="POST" id="subscribe-form">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->plan_id }}">
                    <div>
                        <label for="card-holder-name">Card Holder Name</label> <br>
                        <input id="card-holder-name" type="text" style="width: 100%">
                    </div>
                    <br>

                    <div class="form-row">
                        <label for="card-element">Credit or debit card</label>
                        <div id="card-element" class="form-control">
                        </div>
                        <!-- Used to display form errors. -->
                        <div id="card-errors" role="alert"></div>
                    </div>
                    <div class="stripe-errors"></div>
                    @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                        @endforeach
                    </div>
                    @endif
                    <div class="form-group text-center mt-5">
                        <button  id="card-button" data-secret="{{ $intent->client_secret }}" class="text-white py-2 rounded hover:bg-green-400 px-4 bg-green-600">
                            Process Subscription
                        </button>
                    </div>
                </form>
                {{--        Stripe Form       --}}
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ env('STRIPE_KEY') }}');
    var elements = stripe.elements();
    var style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };
    var card = elements.create('card', {hidePostalCode: true,
        style: style});
    card.mount('#card-element');
    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });
    const cardHolderName = document.getElementById('card-holder-name');
    const cardButton = document.getElementById('card-button');
    const clientSecret = cardButton.dataset.secret;
    cardButton.addEventListener('click', async (e) => {
        e.preventDefault();
        console.log("attempting");
        const { setupIntent, error } = await stripe.confirmCardSetup(
            clientSecret, {
                payment_method: {
                    card: card,
                    billing_details: { name: cardHolderName.value }
                }
            }
        );
        if (error) {
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
        } else {
            paymentMethodHandler(setupIntent.payment_method);
        }
    });
    function paymentMethodHandler(payment_method) {
        var form = document.getElementById('subscribe-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'payment_method');
        hiddenInput.setAttribute('value', payment_method);
        form.appendChild(hiddenInput);
        form.submit();
    }
</script>
@endsection
```

<hr>

# (6) Process on Subscribe a Plan with Payment Method

## Paste that code into SubscriptionController

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Plan;
use App\Models\Plan as PlanModel;

class SubscriptionController extends Controller
{
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
}
```

## Define Routes (routes/web.php)

```php
Route::controller(SubscriptionController::class)->group(function () {
    Route::post('/plans/process', 'process')->name('plans.process');
});
```
## Check output on (Stripe Developers Dashboard) for Subscribe a Plan:

[Stripe.Test.Dashboard.Subscription](https://dashboard.stripe.com/test/subscriptions)

<hr>

# (7) Show User Subscriptions

## Paste that code into SubscriptionController

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Plan;
use App\Models\Plan as PlanModel;
use Laravel\Cashier\Subscription;

class SubscriptionController extends Controller
{
    public function showSubscriptions()
    {
        $subscriptions = Subscription::where('user_id',auth()->id())->get();

        return view('subscriptions.index',[
            'subscriptions' => $subscriptions
        ]);
    }
}
```

## Define Routes (routes/web.php)

```php
Route::controller(SubscriptionController::class)->group(function () {
    Route::get('/subscriptions', 'showSubscriptions')->name('subscriptions.index');
});
```

## Make Relationships

### Find this file and write down the relations

### ***vendor/laravel/cashier/src/Subscription.php***

```php
<?php
use App\Models\Plan;

class Subscription extends Model
{
        /**
     * @description 'plan' Add manually
     *
     * @author Shahbaz Ahmad @email shahbazahmad0987654321@gmail.com
     *
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['items','plan'];
    
        /**
     * @description Add manually
     *
     * @author Shahbaz Ahmad @email shahbazahmad0987654321@gmail.com
     *
     * Get the plan that related the subscription.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function plan()
    {
        return $this->hasOne(Plan::class,'plan_id','stripe_price');
    }
}
```

## Set Blade View

### ***resources/views/subscriptions/index.blade.php***

```bladehtml
@extends('layouts.app')

@section('main')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div id="success" style="display: none" class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert"></div>
                <div class="container mx-auto px-4 py-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300 shadow-md rounded-lg">
                            <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 border-b border-gray-300">Plan</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 border-b border-gray-300">Subscription</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 border-b border-gray-300">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 border-b border-gray-300">Price</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 border-b border-gray-300">Quantity</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 border-b border-gray-300">Trial Start</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 border-b border-gray-300">Trial End</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 border-b border-gray-300">Renew</th>
                                <th class="px-6 py-3 text-left text-sm font-medium text-gray-900 border-b border-gray-300">Items</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($subscriptions as $subscription)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 border-b border-gray-300 text-sm text-gray-700 capitalize">
                                    {{ $subscription->plan->name }}
                                </td>
                                <td class="px-6 py-4 border-b border-gray-300 text-sm text-gray-700 capitalize">
                                    {{ $subscription->type }}
                                </td>
                                <td class="px-6 py-4 border-b border-gray-300 text-sm text-gray-700">
                                                <span class="capitalize px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $subscription['stripe_status'] == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $subscription['stripe_status'] }}
                                                </span>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-300 text-sm text-gray-700">
                                    £{{ $subscription->plan->price }}
                                </td>
                                <td class="px-6 py-4 border-b border-gray-300 text-sm text-gray-700">
                                    {{ $subscription['quantity'] }}
                                </td>
                                <td class="px-6 py-4 border-b border-gray-300 text-sm text-gray-700">
                                    {{ \Carbon\Carbon::parse($subscription['created_at'])->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 border-b border-gray-300 text-sm text-gray-700">
                                    {{ \Carbon\Carbon::parse($subscription['ends_at'])->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 border-b border-gray-300 text-sm text-gray-700">
                                    <label for="toggle{{ $subscription->id }}" class="flex items-center cursor-pointer">
                                        <div class="relative">
                                            <input type="checkbox" id="toggle{{ $subscription->id }}" value="{{ $subscription->type }}" {{ $subscription->ends_at == null ? 'checked':''}} class="sr-only peer switch">
                                            <div class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-green-500 peer-focus:ring-4 peer-focus:ring-green-300"></div>
                                            <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full transition-transform peer-checked:translate-x-full"></div>
                                        </div>
                                        <span class="ml-3 text-sm font-medium">
                                                        {{ $subscription->ends_at != null ? 'Cancel' : 'Resume' }}
                                                    </span>
                                    </label>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-300 text-sm text-gray-700">
                                    <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                                        <h4 class="font-semibold text-gray-800 mb-2">Subscription Items</h4>
                                        <ul class="space-y-2 list-inside">
                                            @foreach($subscription['items'] as $item)
                                            <li class="flex items-center space-x-2">
                                                <!-- Icon -->
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9.293V7a1 1 0 012 0v1.707l.707-.707a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L9 8.707z" clip-rule="evenodd" />
                                                </svg>
                                                <!-- Item Info -->
                                                <div class="flex flex-col">
                                                    <span class="font-medium text-gray-900">Product: {{ $item['stripe_product'] }}</span>
                                                    <span class="text-gray-600 text-xs">Price: {{ $item['stripe_price'] }} • Quantity: {{ $item['quantity'] }}</span>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <h2>You are not subscribed any plan</h2>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

<hr>

# (8) Cancel and Resume Subscription 

## Paste that code into SubscriptionController

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Plan;
use App\Models\Plan as PlanModel;
use Laravel\Cashier\Subscription;

class SubscriptionController extends Controller
{
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
}
```

## Define Routes (routes/web.php)

```php
Route::controller(SubscriptionController::class)->group(function () {
   Route::get('/subscriptions/cancel', 'cancelSubscriptions')->name('subscriptions.cancel');
   Route::get('/subscriptions/resume', 'resumeSubscriptions')->name('subscriptions.resume');
});
```

## Add script in current blade file

### ***resources/views/subscriptions/index.blade.php***

```bladehtml
@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function (){
            $('.switch').click(function (){
                var name = $('.switch').val();
                if ($(this).is(':checked')){
                    $.ajax({
                        url:'{{ route("subscriptions.resume") }}',
                        data:{ name },
                        type:"GET",
                        success:function (response){
                            if (response){
                                $("#success").text(response).css("display", "block");
                                setTimeout(function() {
                                    $("#success").css("display", "none");
                                }, 4000);
                            } else {
                                $("#success").css("display", "none");
                            }
                        },
                        error:function (response){
                            console.log(response);
                        }
                    });
                } else {
                    $.ajax({
                        url:'{{ route("subscriptions.cancel") }}',
                        data:{ name },
                        type:"GET",
                        success:function (response){
                            if (response){
                                $("#success").text(response).css("display", "block");
                                setTimeout(function() {
                                    $("#success").css("display", "none");
                                }, 4000);
                            } else {
                                $("#success").css("display", "none");
                            }
                        },
                        error:function (response){
                            console.log(response);
                        }
                    });
                }
            });
        });
    </script>
@endsection
```

<hr>

# (9) Show Transactions History and Invoices 

## Paste that code into SubscriptionController

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class SubscriptionController extends Controller
{
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
```

## Define Routes (routes/web.php)

```php
Route::controller(SubscriptionController::class)->group(function () {
   Route::get('/transactions/history', 'getTransactionHistory')->name('transactions.history');
});
```

## Set Blade View

### ***resources/views/subscriptions/transactions-history.blade.php***

```bladehtml
@extends('layouts.app')

@section('main')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="container mx-auto p-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Transaction History</h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                            <thead>
                            <tr class="bg-gray-100 text-gray-700">
                                <th class="py-3 px-4 text-left font-medium">ID</th>
                                <th class="py-3 px-4 text-left font-medium">Type</th>
                                <th class="py-3 px-4 text-left font-medium">Amount</th>
                                <th class="py-3 px-4 text-left font-medium">Status</th>
                                <th class="py-3 px-4 text-left font-medium">Date</th>
                                <th class="py-3 px-4 text-left font-medium">Receipt/Invoice</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($transactions as $transaction)
                            <tr class="border-b">
                                <td class="py-3 px-4 text-gray-800">{{ $transaction['id'] }}</td>
                                <td class="py-3 px-4 text-gray-800 capitalize">{{ $transaction['type'] }}</td>
                                <td class="py-3 px-4 text-gray-800">
                                    £{{ number_format((float) $transaction['amount'] / 100, 2) }}
                                </td>
                                <td class="py-3 px-4">
                                            <span class="inline-block px-3 py-1 rounded-full text-sm
                                            {{ $transaction['status'] === 'succeeded' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                                {{ ucfirst($transaction['status']) }}
                                            </span>
                                </td>
                                <td class="py-3 px-4 text-gray-800">{{ $transaction['created_at'] }}</td>
                                <td class="py-3 px-4">
                                    @if($transaction['type'] === 'invoice')
                                    <a href="{{ $transaction['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800">View Invoice</a>
                                    @else
                                    <a href="{{ $transaction['receipt_url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800">View Receipt</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```





