@extends('layouts.app')

@section('main')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="container mx-auto p-4">
                        <h1 class="text-2xl font-bold mb-4">Stripe Balance Overview</h1>

                        <div class="bg-white shadow-md rounded-lg p-6">
                            <h2 class="text-lg font-semibold mb-2">Available Balance</h2>
                            <ul class="space-y-2">
                                @foreach($balance->available as $available)
                                    <li>
                                        <p>Amount: ${{ number_format($available->amount / 100, 2) }}</p>
                                        <p>Currency: {{ strtoupper($available->currency) }}</p>
                                        <p>Source Types:</p>
                                        <ul class="ml-4">
                                            <li>Card: ${{ number_format($available->source_types->card / 100, 2) }}</li>
                                            <li>Bank Account: ${{ number_format($available->source_types->bank_account / 100, 2) }}</li>
                                            <!-- Add more source types if available -->
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="bg-white shadow-md rounded-lg p-6 mt-4">
                            <h2 class="text-lg font-semibold mb-2">Pending Balance</h2>
                            <ul class="space-y-2">
                                @foreach($balance->pending as $pending)
                                    <li>
                                        <p>Amount: ${{ number_format($pending->amount / 100, 2) }}</p>
                                        <p>Currency: {{ strtoupper($pending->currency) }}</p>
                                        <p>Source Types:</p>
                                        <ul class="ml-4">
                                            <li>Card: ${{ number_format($pending->source_types->card / 100, 2) }}</li>
                                            <li>Bank Account: ${{ number_format($pending->source_types->bank_account / 100, 2) }}</li>
                                            <!-- Add more source types if available -->
                                        </ul>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="bg-white shadow-md rounded-lg p-6 mt-4">
                            <h2 class="text-lg font-semibold mb-2">Balance Breakdown</h2>
                            <ul>
                                <li>
                                    <p>Available Balance Live Mode: {{ $balance->livemode ? 'True' : 'False' }}</p>
                                </li>
                                <li>
                                    <p>Instant Payouts Enabled: {{ $balance->instant_available ? 'Yes' : 'No' }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
