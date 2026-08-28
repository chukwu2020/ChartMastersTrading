@extends('layout.user')

@section('content')

<style>
    :root {
        --primary-green: #9EDD05;
        --dark-green: #0C3A30;
        --accent-green: #8AC304;
    }

    .deposit-card {
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        background: white;
        position: relative;
        overflow: hidden;
        background-image: url('{{ asset('assets/images/hero/hero-image-1.svg') }}');
        background-size: cover;
        background-position: center;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .deposit-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-green), var(--accent-green));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .deposit-card:hover {
        transform: translateY(-4px);
        border-color: var(--primary-green);
        box-shadow: 0 12px 24px rgba(158, 221, 5, 0.15);
    }

    .deposit-card:hover::before {
        opacity: 1;
    }

    .badge-approved {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #34d399;
    }

    .badge-pending {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #fbbf24;
    }

    .badge-failed {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #f87171;
    }

    .badge-bank-pending {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #60a5fa;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        background: white;
        border-radius: 16px;
        border: 2px dashed #e5e7eb;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .filter-btn {
        padding: 0.5rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-weight: 600;
        color: #4b5563;
        background: white;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .filter-btn:hover {
        border-color: var(--primary-green);
        color: var(--dark-green);
    }

    .filter-btn.active {
        background: var(--primary-green);
        border-color: var(--primary-green);
        color: var(--dark-green);
    }

    .method-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .pill-crypto {
        background: #f0f7ed;
        color: #15803d;
        border: 1px solid #86efac;
    }

    .pill-giftcard {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fde047;
    }

    .pill-bank {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }

    .bank-detail-item {
        font-size: 0.75rem;
        padding: 0.25rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .bank-detail-item:last-child {
        border-bottom: none;
    }

    .bank-detail-label {
        color: #6b7280;
        font-weight: 500;
    }

    .bank-detail-value {
        font-weight: 600;
        color: #0C3A30;
        font-family: monospace;
        font-size: 0.8rem;
    }
</style>

<div class="dashboard-main-body">
    <!-- Header with Stats -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h5 class="text-2xl font-bold" style="color: #0C3A30;">Deposit History</h5>
            <p class="text-sm text-gray-500 mt-1">Track all your deposit transactions</p>
        </div>
        <a href="{{ route('user.deposit') }}" 
           class="px-6 py-3 bg-[#9EDD05] text-[#0C3A30] font-semibold rounded-lg hover:bg-[#8AC304] transition flex items-center gap-2">
            <iconify-icon icon="ph:plus-circle-fill"></iconify-icon>
            New Deposit
        </a>
    </div>

    <!-- Summary Cards -->
    @php
        $totalDeposits = $deposits->count();
        $approvedCount = $deposits->where('status', 1)->count();
        $pendingCount  = $deposits->where('status', 0)->count();
        $failedCount   = $deposits->where('status', 2)->count();
        $totalAmount   = $deposits->where('status', 1)->sum('amount_deposited');
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-[#9EDD05]">
            <p class="text-sm text-gray-500 mb-1">Total Deposits</p>
            <p class="text-2xl font-bold" style="color: #0C3A30;">{{ $totalDeposits }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
            <p class="text-sm text-gray-500 mb-1">Approved</p>
            <p class="text-2xl font-bold text-green-600">{{ $approvedCount }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500 mb-1">Pending</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-red-500">
            <p class="text-sm text-gray-500 mb-1">Failed</p>
            <p class="text-2xl font-bold text-red-600">{{ $failedCount }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <span class="text-sm font-medium text-gray-600">Filter:</span>
        <button class="filter-btn active" onclick="filterDeposits('all', event)">All</button>
        <button class="filter-btn" onclick="filterDeposits('approved', event)">Approved</button>
        <button class="filter-btn" onclick="filterDeposits('pending', event)">Pending</button>
        <button class="filter-btn" onclick="filterDeposits('failed', event)">Failed</button>
    </div>

    <!-- Main Section -->
    <div class="grid grid-cols-1 gap-6">
        <div class="w-full">
            <div class="rounded-2xl p-6" style="background-image: url('{{ asset('assets/images/hero/hero-image-1.svg') }}'); background-repeat: no-repeat; background-size: cover; background-position: center;">
                <div class="bg-white/90 backdrop-blur-sm rounded-xl p-6">
                    <h6 class="font-bold text-lg mb-6" style="color: #0C3A30;">Recent Deposits</h6>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @forelse ($deposits as $deposit)
                        @php
                            $status = match ((int) $deposit->status) {
                                1       => 'approved',
                                2       => 'failed',
                                default => 'pending',
                            };

                            $badgeClass = match ($status) {
                                'approved' => 'badge-approved',
                                'failed'   => 'badge-failed',
                                default    => 'badge-pending',
                            };

                            $statusIcon = match ($status) {
                                'approved' => 'ph:check-circle-fill',
                                'failed'   => 'ph:x-circle-fill',
                                default    => 'ph:clock-fill',
                            };

                            $method = $deposit->payment_method ?? 'crypto';

                            $isBankTransfer = $method === 'bank_transfer';
                            $bankDetails = $isBankTransfer ? $deposit->bank_details : null;

                            $walletIcons = [
                                'BTC'  => '₿', 'ETH'  => 'Ξ', 'USDT' => '₮', 'BNB'  => '🟡',
                                'SOL'  => '◎', 'XRP'  => '✕', 'ADA'  => '₳', 'DOGE' => 'Ð',
                                'LTC'  => 'Ł', 'TRX'  => '🔺', 'MATIC'=> '⬣', 'DOT'  => '⚫',
                                'AVAX' => '🔺', 'LINK' => '🔗', 'UNI'  => '🦄', 'ATOM' => '⚛️',
                                'XLM'  => '✨', 'DEFAULT' => '🔗',
                            ];

                            $gcBrandMap = [
                                'amazon'  => 'Amazon', 'itunes'  => 'iTunes',
                                'google'  => 'Google Play', 'steam'   => 'Steam',
                                'walmart' => 'Walmart', 'other'   => $deposit->other_card_name ?? $deposit->card_type_label ?? 'Gift Card',
                            ];

                            $gcBrand = $gcBrandMap[$deposit->card_type ?? ''] ?? ucfirst($deposit->card_type ?? 'Gift Card');

                            $pillClass = 'pill-crypto';
                            $pillIcon = 'ph:currency-btc-bold';
                            if ($method === 'giftcard') {
                                $pillClass = 'pill-giftcard';
                                $pillIcon = 'ph:gift-bold';
                            } elseif ($method === 'bank_transfer') {
                                $pillClass = 'pill-bank';
                                $pillIcon = 'ph:bank-bold';
                            }
                        @endphp

                        <div class="deposit-card" data-status="{{ $status }}">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                        <span class="qa-btn-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                <path d="M12 5v14M5 12l7-7 7 7" />
                                            </svg>
                                        </span>
                                    </div>
                                    <h6 class="font-semibold text-gray-800" style="font-size: 1.2rem;">
                                        Deposit #{{ $deposit->id }}
                                    </h6>
                                </div>
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1 rounded-full {{ $badgeClass }}">
                                    <iconify-icon icon="{{ $statusIcon }}" class="text-sm"></iconify-icon>
                                    {{ strtoupper($status) }}
                                </span>
                            </div>

                            <!-- Details -->
                            <div class="space-y-3 text-sm flex-1">

                                @if($deposit->plan)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500">Plan:</span>
                                    <span class="font-medium text-gray-800">{{ $deposit->plan->name }}</span>
                                </div>
                                @endif

                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500">Method:</span>
                                    <span class="method-pill {{ $pillClass }}">
                                        <iconify-icon icon="{{ $pillIcon }}" class="text-xs"></iconify-icon>
                                        @if($method === 'crypto')
                                            @php
                                                $cryptoName = optional($deposit->wallet)->crypto_name;
                                            @endphp
                                            {{ $cryptoName ?? 'Crypto' }}
                                        @elseif($method === 'giftcard')
                                            {{ $gcBrand }} Gift Card
                                        @elseif($method === 'bank_transfer')
                                            Bank Transfer
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>

                                @if($isBankTransfer && $bankDetails)
                                <div class="bg-blue-50 rounded-lg p-3 mt-2">
                                    <p class="text-xs font-semibold text-blue-700 mb-2">Bank Account Details</p>
                                    <div class="space-y-1">
                                        @if($bankDetails['bank_name'] ?? false)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">Bank:</span>
                                            <span class="text-xs font-medium text-gray-800">{{ $bankDetails['bank_name'] }}</span>
                                        </div>
                                        @endif
                                        @if($bankDetails['account_name'] ?? false)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">Account:</span>
                                            <span class="text-xs font-medium text-gray-800">{{ $bankDetails['account_name'] }}</span>
                                        </div>
                                        @endif
                                        @if($bankDetails['account_number'] ?? false)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">Account #:</span>
                                            <span class="text-xs font-mono font-medium text-gray-800">{{ $bankDetails['account_number'] }}</span>
                                        </div>
                                        @endif
                                        @if($bankDetails['reference_code'] ?? false)
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">Reference:</span>
                                            <span class="text-xs font-mono font-bold text-blue-700">{{ $bankDetails['reference_code'] }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if($method === 'giftcard' && $deposit->card_code)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500">Card Code:</span>
                                    <span class="font-mono text-xs text-gray-600" style="background:#f3f4f6;padding:2px 8px;border-radius:6px;">
                                        {{ $deposit->card_code }}
                                    </span>
                                </div>
                                @endif

                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500">Amount:</span>
                                    <span class="font-bold text-lg" style="color: #0C3A30;">
                                        ${{ number_format($deposit->amount_deposited, 2) }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500">Date:</span>
                                    <span class="text-gray-600">{{ $deposit->created_at->format('d M Y, H:i') }}</span>
                                </div>

                                @if($deposit->status == 1)
                                <div class="flex justify-between items-center pt-2">
                                    <span class="text-gray-500">Approved:</span>
                                    <span class="text-green-600">{{ $deposit->updated_at->format('d M Y, H:i') }}</span>
                                </div>
                                @endif
                            </div>

                            @if($deposit->tx_hash)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <p class="text-xs text-gray-400 mb-1">Transaction Hash</p>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono text-gray-600 truncate">{{ $deposit->tx_hash }}</span>
                                    <button onclick="copyToClipboard('{{ $deposit->tx_hash }}', this)" 
                                            class="text-[#9EDD05] hover:text-[#8AC304]">
                                        <iconify-icon icon="ph:copy-simple"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                            @endif

                            @if($isBankTransfer && $deposit->status == 0)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex items-center gap-2 text-xs text-blue-600">
                                    <iconify-icon icon="ph:clock-fill"></iconify-icon>
                                    <span>Awaiting admin approval after transfer</span>
                                </div>
                            </div>
                            @endif

                            @if($deposit->status == 2 && $deposit->rejection_note)
                            <div class="mt-4 bg-red-50 border border-red-200 rounded-xl p-3">
                                <div class="flex items-start gap-2">
                                    <iconify-icon icon="ph:warning-circle-fill" class="text-red-500 text-lg flex-shrink-0 mt-0.5"></iconify-icon>
                                    <div>
                                        <p class="text-xs font-semibold text-red-600 mb-1">Rejection Reason:</p>
                                        <p class="text-sm text-gray-700">{{ $deposit->rejection_note }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($deposit->proof)
                            <div class="mt-4 pt-3 border-t border-gray-100">
                                <button onclick="viewProof('{{ asset('storage/'.$deposit->proof) }}')" 
                                        class="text-xs text-[#9EDD05] hover:text-[#8AC304] font-semibold flex items-center gap-1">
                                    <iconify-icon icon="ph:eye"></iconify-icon>
                                    {{ $method === 'giftcard' ? 'View Gift Card' : ($method === 'bank_transfer' ? 'View Transfer Proof' : 'View Proof') }}
                                </button>
                            </div>
                            @endif
                        </div>
                        @empty
                        <div class="col-span-full">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <iconify-icon icon="ph:wallet-fill" class="text-4xl text-gray-400"></iconify-icon>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">No Deposits Yet</h3>
                                <p class="text-gray-500 mb-6">Start your trading journey by making your first deposit</p>
                                <a href="{{ route('user.deposit') }}" 
                                   class="inline-flex items-center gap-2 px-6 py-3 bg-[#9EDD05] text-[#0C3A30] font-semibold rounded-lg hover:bg-[#8AC304] transition">
                                    <iconify-icon icon="ph:plus-circle-fill"></iconify-icon>
                                    Make First Deposit
                                </a>
                            </div>
                        </div>
                        @endforelse
                    </div>

                    @if(method_exists($deposits, 'links'))
                    <div class="mt-8">
                        {{ $deposits->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Proof Modal -->
<div id="proofModal" class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold" style="color: #0C3A30;">Deposit Proof</h3>
            <button onclick="closeProofModal()" class="text-gray-500 hover:text-gray-700">
                <iconify-icon icon="ph:x-bold" class="text-2xl"></iconify-icon>
            </button>
        </div>
        <div class="flex items-center justify-center max-h-[70vh] overflow-auto">
            <img id="proofImage" src="" alt="Deposit Proof" class="max-w-full max-h-[60vh] object-contain rounded-lg">
        </div>
        <div class="text-right mt-4">
            <button onclick="closeProofModal()" 
                    class="px-4 py-2 bg-[#9EDD05] text-[#0C3A30] rounded-lg hover:bg-[#8AC304] transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Failed Note Modal -->
<div id="noteModal" class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                <iconify-icon icon="ph:warning-circle-fill" class="text-red-600 text-xl"></iconify-icon>
            </div>
            <h3 class="text-xl font-bold text-red-600">Deposit Failed</h3>
        </div>
        <p id="noteContent" class="text-gray-700 text-sm leading-relaxed whitespace-pre-line bg-gray-50 p-4 rounded-lg"></p>
        <div class="flex justify-end mt-6">
            <button onclick="closeNoteModal()"
                class="px-6 py-2 bg-[#9EDD05] text-[#0C3A30] rounded-lg hover:bg-[#8AC304] transition font-semibold">
                Close
            </button>
        </div>
    </div>
</div>

<script>
    function filterDeposits(status, event) {
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        if (event && event.target) event.target.classList.add('active');
        document.querySelectorAll('.deposit-card').forEach(card => {
            card.style.display = (status === 'all' || card.dataset.status === status) ? 'flex' : 'none';
        });
    }

    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<iconify-icon icon="ph:check-bold"></iconify-icon>';
            btn.classList.add('text-green-500');
            setTimeout(() => { btn.innerHTML = originalIcon; btn.classList.remove('text-green-500'); }, 2000);
        });
    }

    function viewProof(url) {
        document.getElementById('proofImage').src = url;
        const modal = document.getElementById('proofModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeProofModal() {
        const modal = document.getElementById('proofModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('proofImage').src = '';
    }

    function openNoteModal(button) {
        document.getElementById('noteContent').innerText = button.getAttribute('data-note');
        const modal = document.getElementById('noteModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeNoteModal() {
        const modal = document.getElementById('noteModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    window.addEventListener('click', function(event) {
        const proofModal = document.getElementById('proofModal');
        const noteModal  = document.getElementById('noteModal');
        if (event.target === proofModal) closeProofModal();
        if (event.target === noteModal)  closeNoteModal();
    });
</script>

@endsection