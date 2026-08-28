@extends('layout.admin')
@section('content')

<style>
    :root {
        --primary-green: #9EDD05;
        --dark-green: #0C3A30;
        --accent-green: #8AC304;
    }

    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-left: 4px solid var(--primary-green);
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .table th {
        background: #f8fafc;
        color: var(--dark-green);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
    }

    .badge-approved {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .proof-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 2px solid #e5e7eb;
    }

    .proof-thumbnail:hover {
        transform: scale(1.1);
        border-color: var(--primary-green);
        box-shadow: 0 4px 12px rgba(158, 221, 5, 0.3);
    }

    .wallet-info {
        max-width: 200px;
    }

    .wallet-address {
        font-size: 0.7rem;
        color: #6b7280;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .copy-btn {
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    tr:hover .copy-btn {
        opacity: 1;
    }

    .country-badge {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        color: #374151;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        background: #f9fafb;
        border-radius: 12px;
        border: 2px dashed #e5e7eb;
    }

    .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }

    .gc-info .gc-brand { font-weight: 700; color: #92400e; display: flex; align-items: center; gap: 4px; font-size: 0.82rem; margin-bottom: 2px; }
    .gc-info .gc-code  { font-family: monospace; font-size: 0.72rem; background: #fef9c3; border: 1px solid #fde047; border-radius: 4px; padding: 1px 6px; color: #374151; display: inline-block; }
    .method-pill { display: inline-flex; align-items: center; gap: 4px; padding: 2px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; }
    .pill-crypto   { background: #f0f7ed; color: #15803d; border: 1px solid #86efac; }
    .pill-giftcard { background: #fef9c3; color: #854d0e; border: 1px solid #fde047; }

    @media (max-width: 768px) {
        .stats-card { padding: 1rem; }
        .stats-card p:last-child { font-size: 1.25rem; }
        .table th, .table td { padding: 0.75rem; }
    }

    tbody tr:hover { background-color: #f9fafb; }

    .overflow-x-auto::-webkit-scrollbar { height: 6px; }
    .overflow-x-auto::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .overflow-x-auto::-webkit-scrollbar-thumb { background: var(--primary-green); border-radius: 10px; }
    .overflow-x-auto::-webkit-scrollbar-thumb:hover { background: var(--accent-green); }
</style>

<div class="dashboard-main-body">
    {{-- HEADER --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <h5 class="text-2xl font-bold" style="color: #0C3A30;">Approved Deposits</h5>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('admin_dashboard') }}" 
                   class="flex items-center gap-2 hover:text-[#9EDD05]"
                   style="color: #0C3A30;">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="font-medium" style="color: #9EDD05;">Approved Deposits</li>
        </ul>
    </div>

    {{-- STATS CARDS --}}
    @php
        $totalAmount = $deposits->sum('amount_deposited');
        $avgAmount   = $deposits->avg('amount_deposited') ?: 0;
        $uniqueUsers = $deposits->pluck('user_id')->unique()->count();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stats-card">
            <p class="text-sm text-gray-500 mb-1">Total Approved</p>
            <p class="text-2xl font-bold" style="color: #0C3A30;">{{ $deposits->count() }}</p>
        </div>
        <div class="stats-card" style="border-left-color: #10b981;">
            <p class="text-sm text-gray-500 mb-1">Total Amount</p>
            <p class="text-2xl font-bold text-green-600">${{ number_format($totalAmount, 2) }}</p>
        </div>
        <div class="stats-card" style="border-left-color: #f59e0b;">
            <p class="text-sm text-gray-500 mb-1">Average Amount</p>
            <p class="text-2xl font-bold text-yellow-600">${{ number_format($avgAmount, 2) }}</p>
        </div>
        <div class="stats-card" style="border-left-color: #8b5cf6;">
            <p class="text-sm text-gray-500 mb-1">Unique Users</p>
            <p class="text-2xl font-bold text-purple-600">{{ $uniqueUsers }}</p>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="col-span-12">
            <div class="card border-0 rounded-xl shadow-sm overflow-hidden">
                {{-- CARD HEADER --}}
                <div class="card-header bg-white p-6 border-b border-gray-200">
                    <div class="flex items-center flex-wrap gap-2 justify-between">
                        <h6 class="font-bold text-lg mb-0" style="color: #0C3A30;">Approved Deposits</h6>
                        <span class="badge-approved">
                            <iconify-icon icon="ph:check-circle-fill" class="mr-1"></iconify-icon>
                            {{ $deposits->count() }} Approved
                        </span>
                    </div>
                </div>

                {{-- CARD BODY --}}
                <div class="card-body p-6">
                    @if($deposits->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-[1400px] w-full table">
                                <thead>
                                    <tr>
                                        <th class="p-3 text-left">#</th>
                                        <th class="p-3 text-left">User</th>
                                        <th class="p-3 text-left">Email</th>
                                        <th class="p-3 text-left">Plan</th>
                                        <th class="p-3 text-left">Method</th>
                                        <th class="p-3 text-left">Payment Details</th>
                                        <th class="p-3 text-left">Proof / Card</th>
                                        <th class="p-3 text-left">Country</th>
                                        <th class="p-3 text-left">Amount</th>
                                        <th class="p-3 text-left">Date Approved</th>
                                        <th class="p-3 text-left">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deposits as $deposit)
                                    @php
                                        $isCrypto   = ($deposit->payment_method ?? 'crypto') === 'crypto';
                                        $isGiftCard = ($deposit->payment_method ?? '') === 'giftcard';

                                        $gcBrandMap = [
                                            'amazon'  => 'Amazon',
                                            'itunes'  => 'iTunes',
                                            'google'  => 'Google Play',
                                            'steam'   => 'Steam',
                                            'walmart' => 'Walmart',
                                            'other'   => $deposit->other_card_name
                                                          ?? $deposit->card_type_label
                                                          ?? 'Other',
                                        ];
                                        $gcBrand = $isGiftCard
                                            ? ($gcBrandMap[$deposit->card_type ?? ''] ?? ucfirst($deposit->card_type ?? 'Gift Card'))
                                            : null;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition">
                                        <td>
                                            <span class="font-medium text-gray-700">{{ $loop->iteration }}</span>
                                        </td>

                                        <td>
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-full bg-[#9EDD05] bg-opacity-20 flex items-center justify-center">
                                                    <span class="text-xs font-bold" style="color: #0C3A30;">
                                                        {{ substr($deposit->user->name ?? 'U', 0, 1) }}
                                                    </span>
                                                </div>
                                                <span class="font-medium text-gray-800">{{ $deposit->user->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="text-gray-600">{{ $deposit->user->email ?? 'N/A' }}</span>
                                        </td>

                                        <td>
                                            @if($deposit->plan)
                                                <span class="font-medium text-gray-800">{{ $deposit->plan->name }}</span>
                                            @else
                                                <span class="text-gray-400 text-sm">No plan selected</span>
                                            @endif
                                        </td>

                                        {{-- Method pill --}}
                                        <td>
                                            @if($isCrypto)
                                                <span class="method-pill pill-crypto">
                                                    <iconify-icon icon="ph:currency-btc-bold"></iconify-icon> Crypto
                                                </span>
                                            @else
                                                <span class="method-pill pill-giftcard">
                                                    <iconify-icon icon="ph:gift-bold"></iconify-icon> Gift Card
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Payment details --}}
                                        <td>
                                            @if($isCrypto)
                                                <div class="wallet-info">
                                                    <div class="flex items-center gap-1 mb-1">
                                                        @php
                                                            $walletIcons = [
                                                                'BTC'     => '₿',
                                                                'ETH'     => '♦️',
                                                                'USDT'    => '💲',
                                                                'BNB'     => '🔶',
                                                                'SOL'     => '◎',
                                                                'DEFAULT' => '🔗'
                                                            ];
                                                            $icon = $walletIcons[strtoupper($deposit->wallet->crypto_name ?? '')] ?? $walletIcons['DEFAULT'];
                                                        @endphp
                                                        <span>{{ $icon }}</span>
                                                        <strong>{{ $deposit->wallet->crypto_name ?? 'N/A' }}</strong>
                                                    </div>
                                                    @if($deposit->wallet)
                                                    <div class="flex items-center gap-1 group">
                                                        <span class="wallet-address" title="{{ $deposit->wallet->wallet_address }}">
                                                            {{ substr($deposit->wallet->wallet_address, 0, 10) }}...{{ substr($deposit->wallet->wallet_address, -6) }}
                                                        </span>
                                                        <button onclick="copyToClipboard('{{ $deposit->wallet->wallet_address }}', this)"
                                                                class="copy-btn text-xs text-[#9EDD05] hover:text-[#8AC304]">
                                                            <iconify-icon icon="ph:copy-simple"></iconify-icon>
                                                        </button>
                                                    </div>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="gc-info">
                                                    <div class="gc-brand">
                                                        <iconify-icon icon="ph:gift-bold" style="color:#f59e0b;"></iconify-icon>
                                                        {{ $gcBrand }} Gift Card
                                                    </div>
                                                    @if($deposit->card_code)
                                                        Code: <span class="gc-code">{{ $deposit->card_code }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Proof / card image --}}
                                        <td>
                                            @if($deposit->proof)
                                                @php
                                                    $proofUrl = file_exists(public_path('storage/' . $deposit->proof))
                                                        ? asset('storage/' . $deposit->proof)
                                                        : asset('uploads/' . $deposit->proof);
                                                @endphp
                                                <img src="{{ $proofUrl }}"
                                                     alt="{{ $isGiftCard ? 'Gift Card' : 'Proof' }}"
                                                     class="proof-thumbnail"
                                                     onclick="openModal('{{ $proofUrl }}')"
                                                     title="{{ $isGiftCard ? 'Gift card image' : 'Transaction proof' }}">
                                            @else
                                                <span class="text-gray-400 text-sm">No image</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="country-badge">
                                                <iconify-icon icon="ph:map-pin-fill" class="mr-1 text-xs"></iconify-icon>
                                                {{ $deposit->user->country ?? 'N/A' }}
                                            </span>
                                        </td>

                                        <td>
                                            <strong class="text-green-600 text-lg">
                                                ${{ number_format($deposit->amount_deposited, 2) }}
                                            </strong>
                                        </td>

                                        <td>
                                            <div class="flex items-center gap-2">
                                                <iconify-icon icon="ph:check-circle-fill" class="text-green-500"></iconify-icon>
                                                <span class="text-gray-600">{{ $deposit->updated_at->format('d M, Y') }}</span>
                                            </div>
                                        </td>

                                        <td>
                                            <form action="{{ route('admin.deposits.adminDelete', $deposit->id) }}" method="POST" onsubmit="return confirm('Remove this deposit from your view? The user will still see it as approved.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Delete from admin view">
                                                    <iconify-icon icon="ph:trash-fill" class="text-lg"></iconify-icon>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($deposits, 'links'))
                        <div class="mt-6">
                            {{ $deposits->links() }}
                        </div>
                        @endif

                    @else
                        <div class="empty-state">
                            <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <iconify-icon icon="ph:check-circle-fill" class="text-4xl text-gray-400"></iconify-icon>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">No Approved Deposits</h3>
                            <p class="text-gray-500">When you approve deposits, they will appear here</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- IMAGE MODAL --}}
<div id="imageModal"
     class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4"
     onclick="closeModal()">
    <div class="relative max-w-4xl w-full">
        <button onclick="closeModal()" 
                class="absolute -top-10 right-0 text-white hover:text-gray-300 transition">
            <iconify-icon icon="ph:x-bold" class="text-2xl"></iconify-icon>
        </button>
        <img id="modalImage"
             class="w-full rounded-lg shadow-2xl object-contain max-h-[80vh]"
             onclick="event.stopPropagation();">
    </div>
</div>

<script>
    function openModal(imageUrl) {
        const modal = document.getElementById('imageModal');
        document.getElementById('modalImage').src = imageUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<iconify-icon icon="ph:check-bold" class="text-green-500"></iconify-icon>';
            setTimeout(() => { btn.innerHTML = originalIcon; }, 2000);
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>

@endsection