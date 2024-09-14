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
