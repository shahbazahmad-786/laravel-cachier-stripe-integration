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
