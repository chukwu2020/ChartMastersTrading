@extends('layout.user')

@section('content')
<div class="max-w-xl mx-auto mt-10 p-6 rounded-2xl shadow-xl border">
    <h1 class="text-lg font-semibold text-[#0C3A30] mb-6">Profit Share Payment</h1>
    
    @if(session('success'))
        <div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">
            {!! session('error') !!}
        </div>
    @endif

    @php
        $pendingProfitShare = $profitShares->where('status', 'pending')->first();
    @endphp

    @if($pendingProfitShare)
        <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-200 mb-6">
            <p class="text-sm text-yellow-800">
                You need to pay <strong class="text-red-600">${{ number_format($pendingProfitShare->amount, 2) }}</strong> 
                ({{ $pendingProfitShare->percentage }}% of your total profits) 
                before you can make withdrawals.
            </p>
            <p class="text-xs text-yellow-600 mt-2">
                Due by: {{ $pendingProfitShare->due_date->format('M d, Y') }}
            </p>
        </div>

        <form action="{{ route('profit-share.pay') }}" method="POST">
            @csrf
            <input type="hidden" name="profit_share_id" value="{{ $pendingProfitShare->id }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Amount to Pay</label>
                <input type="text" value="${{ number_format($pendingProfitShare->amount, 2) }}" readonly
                    class="w-full px-4 py-3 rounded-lg border bg-gray-50">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                <select name="payment_method" class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-[#8AC304]">
                    <option value="cryptocurrency">Cryptocurrency</option>
                    <option value="digital_wallet">Bank Transfer</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Transaction ID (Optional)</label>
                <input type="text" name="transaction_id" 
                    class="w-full px-4 py-3 rounded-lg border focus:outline-none focus:ring-2 focus:ring-[#8AC304]"
                    placeholder="Enter your transaction ID if available">
            </div>

            <button type="submit" 
                class="w-full py-3 px-4 rounded-lg font-medium shadow hover:shadow-md transition"
                style="background-color: #8AC304; color:#0C3A30;">
                Submit Profit Share Payment
            </button>
        </form>
    @else
        <div class="text-center py-8">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center">
                <iconify-icon icon="ph:check-circle-fill" class="text-green-600 text-3xl"></iconify-icon>
            </div>
            <p class="text-xl font-bold text-gray-700 mb-2">No Pending Profit Share</p>
            <p class="text-sm text-gray-500">You have no pending profit share payments.</p>
            <a href="{{ route('user.withdraw.form') }}" class="inline-block mt-4 px-6 py-2 rounded-lg text-white" style="background:#8AC304;">
                Back to Page
            </a>
        </div>
    @endif
</div>
@endsection