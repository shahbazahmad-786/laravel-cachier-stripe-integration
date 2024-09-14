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
