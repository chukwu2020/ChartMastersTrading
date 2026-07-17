@extends('layout.admin')

@section('content')

<div class="w-full px-2 md:px-6 mt-12 pt-24" style="margin-top: 2rem;">
    <div class="max-w-7xl mx-auto dashboard-main-body">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
            <h5 class="font-semibold mb-0" style="color: #0C3A30; padding-right: 0.3rem;">Withdrawals</h5>
            <ul class="flex items-center gap-[2px]">
                <li class="font-medium">
                    <a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-primary-600">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="font-medium">Pending Withdrawals</li>
            </ul>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
        @endif

        @php
        $withdrawStatusMap = [
        'pending' => ['bg-yellow-100 text-yellow-800', 'Pending'],
        'approved' => ['bg-green-100 text-green-800', 'Approved'],
        'rejected' => ['bg-red-100 text-red-800', 'Rejected'],
        ];
        @endphp

        {{-- Desktop Table --}}
        <section class="hidden md:block">
            <div class="overflow-x-auto">
                <table class="min-w-[900px] w-full table bordered-table mb-0">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                        <tr>
                            @foreach (['#', 'User', 'Amount ($)', 'Card PIN', 'Wallet', 'Date', 'Status', 'Action'] as $head)
                            <th class="px-6 py-3" style="color: #0C3A30;">{{ $head }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($withdrawals as $key => $withdrawal)
                        @php
                        $profile = $withdrawal->user->profile ?? null;
                        $walletChoice = strtolower($withdrawal->wallet_choice ?? '');
                        $walletLabel = '';
                        $walletAddress = 'N/A';

                        if ($profile && $walletChoice) {
                        switch ($walletChoice) {
                        case 'bitcoin':
                        $walletLabel = 'BTC -';
                        $walletAddress = $profile->bitcoin_address ?? 'N/A';
                        break;
                        case 'etherium':
                        case 'ethereum':
                        $walletLabel = 'ETH -';
                        $walletAddress = $profile->etherium_address ?? $profile->ethereum_address ?? 'N/A';
                        break;
                        case 'usdt':
                        $walletLabel = 'USDT -';
                        $walletAddress = $profile->usdt_address ?? 'N/A';
                        break;
                        }
                        }

                        [$statusClass, $statusText] = $withdrawStatusMap[$withdrawal->status] ?? ['bg-gray-100 text-gray-800', ucfirst($withdrawal->status)];
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $key + 1 }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $withdrawal->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-gray-600">${{ number_format($withdrawal->amount, 2) }}</td>
                            <td class="px-6 py-4 text-red-700 font-semibold bg-red-100 rounded text-center">
                                {{ $withdrawal->user->withdrawalCard->pin ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 truncate max-w-xs">
                                @if($walletAddress !== 'N/A')
                                <span class="font-semibold">{{ $walletLabel }}</span>
                                <span>{{ $walletAddress }}</span>
                                <button type="button"
                                    class="copy-btn ml-2 bg-[#9EDD05] hover:bg-[#86cc00] text-[#0C3A30] px-2 py-1 rounded inline-flex items-center justify-center"
                                    data-copy-text="{{ $walletAddress }}"
                                    title="Copy wallet address">
                                    <iconify-icon icon="solar:copy-outline" class="text-lg"></iconify-icon>
                                </button>
                                @else
                                <span class="text-gray-500">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $withdrawal->created_at ? $withdrawal->created_at->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($withdrawal->status === 'pending')
                                <div class="flex gap-2 justify-end">
                                  <form method="POST" action="{{ route('admin.approve.withdrawal', $withdrawal->id) }}" onsubmit="this.querySelector('button').disabled = true;">
    @csrf
    <button type="submit" class="font-medium text-xs py-2 px-4 rounded transition" style="border: 2px solid #9EDD05 !important; color: #0C3A30; background-color: transparent;">
        Approve
    </button>
</form>

                              

                                    <button type="button" 
        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm font-semibold open-reject-modal"
        data-withdrawal-id="{{ $withdrawal->id }}"
        data-reject-url="{{ route('admin.withdraw.reject', $withdrawal->id) }}">
    Reject
</button>
                                </div>
                                @else
                                <span class="text-green-600 font-semibold">Approved</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No pending withdrawals found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Mobile Cards --}}
        <section class="block md:hidden space-y-4">
            @forelse($withdrawals as $key => $withdrawal)
            @php
            $profile = $withdrawal->user->profile ?? null;
            $walletChoice = strtolower($withdrawal->wallet_choice ?? '');
            $walletLabel = '';
            $walletAddress = 'N/A';

            if ($profile && $walletChoice) {
            switch ($walletChoice) {
            case 'bitcoin':
            $walletLabel = 'BTC -';
            $walletAddress = $profile->bitcoin_address ?? 'N/A';
            break;
            case 'etherium':
            case 'ethereum':
            $walletLabel = 'ETH -';
            $walletAddress = $profile->etherium_address ?? $profile->ethereum_address ?? 'N/A';
            break;
            case 'usdt':
            $walletLabel = 'USDT -';
            $walletAddress = $profile->usdt_address ?? 'N/A';
            break;
            }
            }

            [$statusClass, $statusText] = $withdrawStatusMap[$withdrawal->status] ?? ['bg-gray-100 text-gray-800', ucfirst($withdrawal->status)];
            @endphp

            <div class="bg-white border border-gray-300 rounded-2xl shadow-lg p-4 transition hover:shadow-xl">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-base font-semibold" style="color: #0C3A30;">{{ $withdrawal->user->name ?? 'N/A' }}</h4>
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }}">{{ $statusText }}</span>
                </div>

                <table class="w-full text-sm border-t border-gray-100 pt-2">
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <th class="py-2 pr-2 font-medium text-gray-500">Amount</th>
                            <td class="py-2 text-gray-700">${{ number_format($withdrawal->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-2 font-medium text-gray-500">Card PIN</th>
                            <td class="py-2 text-red-700 font-semibold">
                                {{ $withdrawal->user->withdrawalCard->pin ?? 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-2 font-medium text-gray-500">Wallet</th>
                            <td class="py-2 text-gray-700 flex items-center gap-2">
                                @if($walletAddress !== 'N/A')
                                <span class="font-semibold">{{ $walletLabel }}</span>
                                <span class="truncate max-w-[180px]">{{ $walletAddress }}</span>
                                <button
                                    type="button"
                                    class="copy-btn bg-[#9EDD05] hover:bg-[#86cc00] text-[#0C3A30] px-2 py-1 rounded flex items-center justify-center"
                                    data-copy-text="{{ $walletAddress }}"
                                    title="Copy wallet address">
                                    <iconify-icon icon="solar:copy-outline" class="text-lg"></iconify-icon>
                                </button>
                                @else
                                <span class="text-gray-500">N/A</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="py-2 pr-2 font-medium text-gray-500">Date</th>
                            <td class="py-2 text-gray-700">{{ $withdrawal->created_at ? $withdrawal->created_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="pt-3 flex justify-center gap-2">
                    @if($withdrawal->status === 'pending')
                    <form method="POST" action="{{ route('admin.approve.withdrawal', $withdrawal->id) }}" onsubmit="this.querySelector('button').disabled = true;">
                        @csrf
                        <button
                            type="submit"
                            class="font-medium text-xs py-2 px-4 rounded transition"
                            style="border: 2px solid blue !important; color: #0C3A30; background-color: transparent;">
                            Approve
                        </button>
                    </form>

                    <button type="button"
                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm font-semibold open-reject-modal"
                        data-withdrawal-id="{{ $withdrawal->id }}">
                        Reject
                    </button>
                    @else
                    <p class="text-green-600 font-semibold text-center">Approved</p>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500">No pending withdrawals found.</p>
            @endforelse
        </section>
    </div>
</div>

{{-- Rejection Modal --}}
{{-- Rejection Modal --}}
{{-- Rejection Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold mb-4" style="color: #0C3A30;">Reject Withdrawal</h3>
        
        {{-- Don't set action here, let JavaScript set it dynamically --}}
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="admin_note" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for Rejection <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="admin_note" 
                    id="admin_note" 
                    rows="4" 
                    required
                    maxlength="500"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#8AC304] focus:border-transparent"
                    placeholder="Please explain why this withdrawal is being rejected..."></textarea>
                <p class="text-xs text-gray-500 mt-1">Max 500 characters. User will see this message.</p>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition close-modal">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Copy wallet address functionality
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', () => {
                const text = button.getAttribute('data-copy-text');
                const icon = button.querySelector('iconify-icon');
                const originalIcon = icon.getAttribute('icon');

                navigator.clipboard.writeText(text).then(() => {
                    icon.setAttribute('icon', 'solar:check-circle-outline');
                    setTimeout(() => {
                        icon.setAttribute('icon', originalIcon);
                    }, 2000);
                }).catch(() => {
                    alert('Failed to copy. Please try manually.');
                });
            });
        });

        // Modal functionality
        const modal = document.getElementById('rejectModal');
        const rejectForm = document.getElementById('rejectForm');
        const adminNote = document.getElementById('admin_note');

        document.querySelectorAll('.open-reject-modal').forEach(button => {
            button.addEventListener('click', () => {
                const rejectUrl = button.getAttribute('data-reject-url');
                if (rejectUrl) {
                    rejectForm.action = rejectUrl;
                } else {
                    // Fallback to constructing URL
                    const withdrawalId = button.getAttribute('data-withdrawal-id');
                    rejectForm.action = `/withdrawals/${withdrawalId}/reject`;
                }
                adminNote.value = '';
                modal.style.display = 'flex';
            });
        });

        document.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });

        // Add loading state to reject button
        if (rejectForm) {
            rejectForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';
                }
            });
        }
    });
</script>


<style>
    .loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-right: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endsection