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
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-left: 4px solid var(--primary-green);
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .table th {
        background: #f8fafc;
        color: var(--dark-green);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .table td {
        vertical-align: middle;
    }

    .badge-pending {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-bank-pending {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        color: #1e40af;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #93c5fd;
    }

    .badge-bank-waiting {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #fbbf24;
    }

    .action-btn {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .action-btn.approve {
        background: var(--primary-green)!important;
        color: var(--dark-green) !important;
    }

    .action-btn.approve:hover {
        background: var(--accent-green);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(158, 221, 5, 0.3);
    }

    .action-btn.reject {
        background: #ef4444;
        color: white;
    }

    .action-btn.reject:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
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
        font-size: 0.85rem;
    }

    .wallet-address {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-green), var(--accent-green));
        color: var(--dark-green);
        padding: 1rem 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
    }

    .gc-info {
        font-size: 0.82rem;
    }

    .gc-info .gc-brand {
        font-weight: 700;
        color: #92400e;
        display: flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 2px;
    }

    .gc-info .gc-code {
        font-family: monospace;
        font-size: 0.72rem;
        background: #fef9c3;
        border: 1px solid #fde047;
        border-radius: 4px;
        padding: 1px 6px;
        display: inline-block;
        color: #374151;
    }

    .method-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
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

    .bank-detail-preview {
        font-size: 0.75rem;
        color: #1e40af;
        background: #eff6ff;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        margin-top: 0.25rem;
    }
</style>

<div class="dashboard-main-body">
    {{-- HEADER --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <h5 class="text-2xl font-bold" style="color: #0C3A30;">Pending Deposits</h5>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]"
                    style="color: #0C3A30;">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="font-medium" style="color: #9EDD05;">Pending Deposits</li>
        </ul>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stats-card">
            <p class="text-sm text-gray-500 mb-1">Total Pending</p>
            <p class="text-2xl font-bold" style="color: #0C3A30;">{{ $deposits->count() }}</p>
        </div>
        <div class="stats-card" style="border-left-color: #10b981;">
            <p class="text-sm text-gray-500 mb-1">Total Amount</p>
            <p class="text-2xl font-bold text-green-600">
                ${{ number_format($deposits->sum('amount_deposited'), 2) }}
            </p>
        </div>
        <div class="stats-card" style="border-left-color: #f59e0b;">
            <p class="text-sm text-gray-500 mb-1">Avg. Deposit</p>
            <p class="text-2xl font-bold text-yellow-600">
                ${{ number_format($deposits->avg('amount_deposited') ?: 0, 2) }}
            </p>
        </div>
        <div class="stats-card" style="border-left-color: #8b5cf6;">
            <p class="text-sm text-gray-500 mb-1">Unique Users</p>
            <p class="text-2xl font-bold text-purple-600">
                {{ $deposits->pluck('user_id')->unique()->count() }}
            </p>
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="grid grid-cols-1 gap-6">
        <div class="col-span-12">
            <div class="card border-0 rounded-xl shadow-sm overflow-hidden">
                {{-- CARD HEADER --}}
                <div class="card-header bg-white p-6 border-b border-gray-200">
                    <div class="flex items-center flex-wrap gap-2 justify-between">
                        <h6 class="font-bold text-lg mb-0" style="color: #0C3A30;">Pending Deposits</h6>
                        <span class="badge-pending">
                            <iconify-icon icon="ph:clock-fill" class="mr-1"></iconify-icon>
                            {{ $deposits->count() }} Pending
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
                                    <th class="p-3 text-left">Method</th>
                                    <th class="p-3 text-left">Details</th>
                                    <th class="p-3 text-left">Proof</th>
                                    <th class="p-3 text-left">Country</th>
                                    <th class="p-3 text-left">Amount</th>
                                    <th class="p-3 text-left">Status</th>
                                    <th class="p-3 text-left">Date</th>
                                    <th class="p-3 text-left">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($deposits as $deposit)
                                @php
                                $isCrypto = ($deposit->payment_method ?? 'crypto') === 'crypto';
                                $isGiftCard = ($deposit->payment_method ?? '') === 'giftcard';
                                $isBankTransfer = ($deposit->payment_method ?? '') === 'bank_transfer';

                                $bankTransfer = $isBankTransfer ? $deposit->bankTransfer : null;
                                $bankStatus = $bankTransfer ? $bankTransfer->status : 'pending';
                                $bankDetails = $isBankTransfer ? $deposit->bank_details : null;
                                $hasBankDetails = $isBankTransfer && !empty($bankDetails);

                                $gcBrandMap = [
                                'amazon' => 'Amazon',
                                'itunes' => 'iTunes',
                                'google' => 'Google Play',
                                'steam' => 'Steam',
                                'walmart' => 'Walmart',
                                'other' => $deposit->other_card_name ?? $deposit->card_type_label ?? 'Other',
                                ];
                                $gcBrand = $isGiftCard ? ($gcBrandMap[$deposit->card_type ?? ''] ?? ucfirst($deposit->card_type ?? 'Gift Card')) : null;

                                if ($isBankTransfer && $bankStatus === 'pending') {
                                    $statusBadge = 'badge-bank-waiting';
                                    $statusLabel = 'Waiting for Admin';
                                } elseif ($isBankTransfer && $bankStatus === 'details_sent') {
                                    $statusBadge = 'badge-bank-pending';
                                    $statusLabel = 'Awaiting Proof';
                                } elseif ($isBankTransfer && $bankStatus === 'completed') {
                                    $statusBadge = 'badge-pending';
                                    $statusLabel = 'Proof Submitted';
                                } else {
                                    $statusBadge = 'badge-pending';
                                    $statusLabel = 'Pending';
                                }
                                @endphp
                                <tr class="border-t border-gray-100 hover:bg-gray-50 transition">
                                    <td class="p-3">{{ $loop->iteration }}</td>

                                    <td class="p-3 font-medium">{{ $deposit->user->name ?? 'N/A' }}</td>

                                    <td class="p-3">{{ $deposit->user->email ?? 'N/A' }}</td>

                                    {{-- Method pill --}}
                                    <td class="p-3">
                                        @if($isCrypto)
                                        <span class="method-pill pill-crypto">
                                            <iconify-icon icon="ph:currency-btc-bold"></iconify-icon> Crypto
                                        </span>
                                        @elseif($isGiftCard)
                                        <span class="method-pill pill-giftcard">
                                            <iconify-icon icon="ph:gift-bold"></iconify-icon> Gift Card
                                        </span>
                                        @elseif($isBankTransfer)
                                        <span class="method-pill pill-bank">
                                            <iconify-icon icon="ph:bank-bold"></iconify-icon> Bank Transfer
                                        </span>
                                        @endif
                                    </td>

                                    {{-- Payment details --}}
                                    <td class="p-3">
                                        @if($isCrypto)
                                        <div class="wallet-info">
                                            <strong>{{ $deposit->wallet->crypto_name ?? 'N/A' }}</strong>
                                            @if($deposit->wallet)
                                            <div class="wallet-address text-gray-500 text-xs flex items-center gap-2">
                                                <span title="{{ $deposit->wallet->wallet_address }}">
                                                    {{ substr($deposit->wallet->wallet_address, 0, 15) }}...
                                                </span>
                                                <button onclick="copyText('{{ $deposit->wallet->wallet_address }}')"
                                                    class="text-xs px-2 py-1 bg-gray-100 rounded hover:bg-gray-200">
                                                    Copy
                                                </button>
                                            </div>
                                            @endif
                                        </div>
                                        @elseif($isGiftCard)
                                        <div class="gc-info">
                                            <div class="gc-brand">
                                                <iconify-icon icon="ph:gift-bold" style="color:#f59e0b;"></iconify-icon>
                                                {{ $gcBrand }} Gift Card
                                            </div>
                                            @if($deposit->card_code)
                                            Code: <span class="gc-code">{{ $deposit->card_code }}</span>
                                            <button onclick="copyGiftCardCode('{{ $deposit->card_code }}')"
                                                class="ml-2 text-xs text-gray-500 hover:text-green-600" title="Copy code">
                                                <iconify-icon icon="ph:copy-bold"></iconify-icon>
                                            </button>
                                            @endif
                                        </div>
                                        @elseif($isBankTransfer)
                                      <div>
    <div class="text-xs text-gray-500">Request Code:</div>
    <div class="font-mono text-sm font-bold text-blue-600">
        {{ $deposit->bank_details['reference_code'] ?? 'N/A' }}
    </div>
    @if($hasBankDetails)
    <div class="bank-detail-preview">
        <iconify-icon icon="ph:check-circle-fill" class="text-green-500"></iconify-icon>
        Details sent
    </div>
    @else
    <div class="bank-detail-preview" style="background: #fef3c7; color: #92400e;">
        <iconify-icon icon="ph:clock-fill" class="mr-1"></iconify-icon>
        Awaiting admin response
    </div>
    @endif
</div>
                                        @endif
                                    </td>

                                    {{-- Proof image --}}
                                    <td class="p-3">
                                        @if($deposit->proof)
                                        <img src="{{ Storage::url($deposit->proof) }}"
                                            alt="{{ $isGiftCard ? 'Gift Card' : 'Proof' }}"
                                            class="proof-thumbnail"
                                            onclick="openModal('{{ Storage::url($deposit->proof) }}')"
                                            title="{{ $isGiftCard ? 'Click to view gift card image' : 'Click to view proof' }}">
                                        @else
                                        <span class="text-gray-400 text-sm">No image</span>
                                        @endif
                                    </td>

                                    <td class="p-3">{{ $deposit->user->country ?? 'N/A' }}</td>

                                    <td class="p-3">
                                        <strong class="text-green-600">
                                            ${{ number_format($deposit->amount_deposited, 2) }}
                                        </strong>
                                    </td>

                                    <td class="p-3">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold {{ $statusBadge }}">
                                            <iconify-icon icon="ph:clock-fill" class="text-xs"></iconify-icon>
                                            {{ $statusLabel }}
                                        </span>
                                    </td>

                                    <td class="p-3 text-sm text-gray-500">
                                        {{ $deposit->created_at->format('d M, Y') }}
                                    </td>

                                    <td class="p-3">
    <div class="flex flex-wrap gap-2">
        @if($isBankTransfer && $deposit->proof)
        <form method="POST" action="{{ route('admin.approve.deposit', $deposit->id) }}"
            onsubmit="return lockBtn(this)">
            @csrf
            <button class="action-btn approve" type="submit">
                <iconify-icon icon="ph:check-bold"></iconify-icon>
                Approve
            </button>
        </form>
        @elseif(!$isBankTransfer && $deposit->status == 0)
        <form method="POST" action="{{ route('admin.approve.deposit', $deposit->id) }}"
            onsubmit="return lockBtn(this)">
            @csrf
            <button class="action-btn approve" type="submit">
                <iconify-icon icon="ph:check-bold"></iconify-icon>
                Approve
            </button>
        </form>
        @endif

        <button onclick="openRejectModal('{{ route('admin.reject.deposit', $deposit->id) }}')"
            class="action-btn reject">
            <iconify-icon icon="ph:x-bold"></iconify-icon>
            Reject
        </button>
    </div>
</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <iconify-icon icon="ph:clock-fill" class="text-4xl text-gray-400"></iconify-icon>
                        </div>
                        <p class="text-gray-500 text-lg mb-2">No Pending Deposits</p>
                        <p class="text-gray-400 text-sm">All deposits have been processed</p>
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
            class="absolute -top-10 right-0 text-white hover:text-gray-300">
            <iconify-icon icon="ph:x-bold" class="text-2xl"></iconify-icon>
        </button>
        <img id="modalImage"
            class="w-full rounded-lg shadow-2xl object-contain max-h-[80vh]"
            onclick="event.stopPropagation();">
    </div>
</div>

{{-- REJECT MODAL --}}
<div id="rejectModal"
    class="fixed inset-0 bg-black bg-opacity-70 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl w-full max-w-md modal-content">
        <div class="modal-header">
            <h4 class="font-bold text-lg flex items-center gap-2">
                <iconify-icon icon="ph:warning-circle-fill"></iconify-icon>
                Reject Deposit
            </h4>
        </div>
        <form method="POST" id="rejectForm">
            @csrf
            @method('DELETE')
            <div class="p-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for Rejection
                </label>
                <textarea name="rejection_note"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:border-[#9EDD05] focus:outline-none"
                    rows="4"
                    required
                    placeholder="Please provide a reason for rejection..."></textarea>
            </div>
            <div class="modal-footer bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeRejectModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition flex items-center gap-2">
                    <iconify-icon icon="ph:x-bold"></iconify-icon>
                    Confirm Reject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(action) {
        document.getElementById('rejectForm').action = action;
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
    }

    function openModal(src) {
        document.getElementById('modalImage').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('imageModal').classList.remove('flex');
    }

    function lockBtn(form) {
        const btn = form.querySelector('button[type="submit"]');
        if (btn.dataset.locked === 'true') return false;
        btn.dataset.locked = 'true';
        btn.disabled = true;
        btn.style.opacity = '0.6';
        btn.innerHTML = '...';
        return true;
    }

    function generateMembershipCode(userId) {
        if (!confirm('Generate membership code for this user?')) return;
        fetch("{{ route('admin.generate.membership.code') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ user_id: userId })
        }).then(r => r.json()).then(data => {
            if (data.success) location.reload();
            else alert(data.message || 'Failed to generate code');
        }).catch(() => alert('Something went wrong'));
    }

    function copyGiftCardCode(code) {
        navigator.clipboard.writeText(code)
            .then(() => {
                showCopyToast('Gift card code copied!');
            })
            .catch(() => {
                alert('Copy failed');
            });
    }

    function showCopyToast(message) {
        let toast = document.createElement('div');
        toast.innerText = message;
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.background = '#0C3A30';
        toast.style.color = 'white';
        toast.style.padding = '10px 14px';
        toast.style.borderRadius = '8px';
        toast.style.fontSize = '13px';
        toast.style.zIndex = '9999';
        toast.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
        document.body.appendChild(toast);
        setTimeout(() => { toast.remove(); }, 2000);
    }

    function copyText(text) {
        navigator.clipboard.writeText(text).then(() => {
            showCopyToast('Copied!');
        });
    }
</script>
@endsection