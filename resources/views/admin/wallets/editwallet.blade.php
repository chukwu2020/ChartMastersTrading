@extends('layout.admin')
@section('content')

<div class="dashboard-main-body">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <h6 class="font-semibold mb-0" style="color: #0C3A30;">Edit Wallet</h6>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color: #0C3A30;">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li style="color: #0C3A30;">-</li>
            <li class="font-medium">
                <a href="{{ route('admin.wallets.index') }}" class="hover:text-[#9EDD05]" style="color: #0C3A30;">
                    Wallets
                </a>
            </li>
            <li style="color: #0C3A30;">-</li>
            <li class="font-medium" style="color: #9EDD05;">Edit Wallet</li>
        </ul>
    </div>

    <div class="card h-full rounded-xl overflow-hidden border-0 shadow-sm">
        <div class="card-body p-6 md:p-10">
            <form action="{{ route('admin.wallets.update', $wallet->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-5 gap-y-4">
                    <div class="mb-3">
                        <label class="text-sm font-semibold mb-2 block" style="color: #0C3A30;">Crypto Name</label>
                        <input type="text" 
                               class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9EDD05] @error('crypto_name') border-red-500 @enderror" 
                               style="border-color: #e5e7eb;" 
                               name="crypto_name" 
                               placeholder="e.g., Bitcoin, Ethereum, USDT" 
                               value="{{ old('crypto_name', $wallet->crypto_name) }}">
                        @error('crypto_name')
                        <span class="text-sm mt-1" style="color: #dc2626;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="text-sm font-semibold mb-2 block" style="color: #0C3A30;">Wallet Address</label>
                        <input type="text" 
                               class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9EDD05] @error('wallet_address') border-red-500 @enderror" 
                               style="border-color: #e5e7eb;" 
                               name="wallet_address" 
                               placeholder="Enter wallet address" 
                               value="{{ old('wallet_address', $wallet->wallet_address) }}">
                        @error('wallet_address')
                        <span class="text-sm mt-1" style="color: #dc2626;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-center gap-3 mt-6 col-span-2">
                        <a href="{{ route('admin.wallets.index') }}" 
                           class="px-10 py-3 rounded-lg transition border" 
                           style="border-color: #6b7280; color: #6b7280; background: transparent;">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 rounded-lg transition font-semibold" 
                                style="background-color: #9EDD05; color: #0C3A30;">
                            Update Wallet
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection