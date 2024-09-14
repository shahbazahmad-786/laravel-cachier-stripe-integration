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
