@extends('layout.admin')
@section('content')

<div class="dashboard-main-body">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <h6 class="font-semibold mb-0" style="color: #0C3A30;">Wallet List</h6>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color: #0C3A30;">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li style="color: #0C3A30;">-</li>
            <li class="font-medium" style="color: #9EDD05;">Wallet List</li>
        </ul>
    </div>

    <div class="grid grid-cols-12">
        <div class="col-span-12">
            <div class="card h-full p-0 rounded-xl border-0 overflow-hidden shadow-sm">
                <div class="card-header border-b py-4 px-6 flex items-center justify-between flex-wrap gap-3" style="background-color: white; border-color: #e5e7eb;">
                    <h6 class="text-base font-medium mb-0" style="color: #0C3A30;">All Wallets</h6>
                    <a href="{{ route('create_wallet') }}" class="text-sm px-4 py-2 rounded-lg flex items-center gap-2 transition" style="background-color: #9EDD05; color: #0C3A30;">
                        <iconify-icon icon="ic:baseline-plus" class="icon text-lg"></iconify-icon>
                        Add New Wallet
                    </a>
                </div>

                <div class="card-body p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[600px]">
                            <thead>
                                <tr style="background-color: #0C3A30;">
                                    <th class="px-4 py-3 text-left text-white font-semibold">#</th>
                                    <th class="px-4 py-3 text-left text-white font-semibold">Crypto Name</th>
                                    <th class="px-4 py-3 text-left text-white font-semibold">Wallet Address</th>
                                    <th class="px-4 py-3 text-center text-white font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($wallets as $index => $wallet)
                                <tr class="border-b hover:bg-gray-50" style="border-color: #e5e7eb;">
                                    <td class="px-4 py-3" style="color: #6b7280;">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <span class="font-medium" style="color: #0C3A30;">{{ ucfirst($wallet->crypto_name) }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm" style="color: #6b7280; word-break: break-all;">{{ $wallet->wallet_address }}</span>
                                            <button onclick="copyToClipboard('{{ $wallet->wallet_address }}', this)" 
                                                    class="text-xs p-1 rounded transition" 
                                                    style="color: #9EDD05;"
                                                    title="Copy address">
                                                <iconify-icon icon="ph:copy-simple" class="text-sm"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                  <td class="px-4 py-3 text-center">
    <div class="flex items-center gap-2 justify-center">
        <!-- View Button -->
        <button type="button" 
                onclick="viewWallet('{{ $wallet->crypto_name }}', '{{ $wallet->wallet_address }}')"
                class="w-8 h-8 flex justify-center items-center rounded-full transition" 
                style="background-color: #dbeafe; color: #2563eb;">
            <iconify-icon icon="majesticons:eye-line" class="text-base"></iconify-icon>
        </button>

        <!-- EDIT Button - NEW -->
        <a href="{{ route('admin.wallets.edit', $wallet->id) }}" 
           class="w-8 h-8 flex justify-center items-center rounded-full transition" 
           style="background-color: #fef3c7; color: #d97706;"
           title="Edit Wallet">
            <iconify-icon icon="ph:pencil-simple-bold" class="text-base"></iconify-icon>
        </a>

        <!-- Delete Button -->
        <form action="{{ route('wallet.delete', $wallet->id) }}" 
              method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this wallet?');">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="w-8 h-8 flex justify-center items-center rounded-full transition" 
                    style="background-color: #fee2e2; color: #dc2626;">
                <iconify-icon icon="fluent:delete-24-regular" class="text-base"></iconify-icon>
            </button>
        </form>
    </div>
</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <iconify-icon icon="ph:wallet-fill" class="text-5xl mb-3" style="color: #9EDD05;"></iconify-icon>
                                            <p class="text-gray-500">No wallets found</p>
                                            <a href="{{ route('create_wallet') }}" class="mt-2 px-4 py-2 rounded-lg text-sm" style="background-color: #9EDD05; color: #0C3A30;">
                                                Add Your First Wallet
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex items-center justify-between flex-wrap gap-2 mt-6">
                        {{ $wallets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const originalIcon = btn.innerHTML;
        btn.innerHTML = '<iconify-icon icon="ph:check-bold" class="text-sm"></iconify-icon>';
        setTimeout(() => {
            btn.innerHTML = originalIcon;
        }, 2000);
    });
}

function viewWallet(cryptoName, address) {
    alert(`${cryptoName} Wallet Address:\n${address}`);
}
</script>

@endsection