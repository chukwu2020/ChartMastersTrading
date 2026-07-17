@extends('layout.admin')

@section('content')
<div class="min-h-screen bg-cover bg-center bg-no-repeat">
<div class="dashboard-main-body px-4 py-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center justify-between gap-2 mb-6">
        <h5 class="font-semibold text-lg md:text-xl" style="color: var(--dark-green) !important;">Withdrawals</h5>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('user_dashboard') }}" class="flex items-center gap-2"
                    style="color: var(--dark-green) !important;"
                    onmouseover="this.style.color='var(--primary-green)';"
                    onmouseout="this.style.color='var(--dark-green)';">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li class="text-gray-400">-</li>
            <li class="font-medium">Withdrawals</li>
        </ul>
    </div>

    {{-- USER PROFILE SUMMARY CARD --}}
    <div class="bg-white/90 backdrop-blur-sm rounded-2xl shadow-sm border border-white/60 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            {{-- Profile Info --}}
            <div class="flex items-center gap-3 p-3 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100">
                <div class="w-12 h-12 rounded-full bg-[#9EDD05]/20 flex items-center justify-center">
                    @php
                        $profilePic = $user->profile->profile_pic ?? null;
                        $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') ?: 'U';
                    @endphp
                    @if($profilePic)
                        <img src="{{ asset('storage/profile_pics/' . $profilePic) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm" style="background: #9EDD05; color: #0C3A30;">
                            {{ $initials }}
                        </div>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-gray-500">Welcome</p>
                    <p class="font-semibold text-sm" style="color: #0C3A30;">{{ $user->name }}</p>
                </div>
            </div>

            {{-- Balance --}}
            <div class="p-3 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100">
                <p class="text-xs text-gray-500">Available Balance</p>
                <p class="font-bold text-lg" style="color: #0C3A30;">${{ number_format($user->balance ?? 0, 2) }}</p>
            </div>

            {{-- Card Info --}}
            <div class="p-3 rounded-xl bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-100">
                <p class="text-xs text-gray-500">Card Number</p>
                <p class="font-bold text-sm" style="color: #0C3A30;">
                    @if($card)
                        {{ chunk_split($card->card_number, 4, ' ') }}
                    @else
                        No card available
                    @endif
                </p>
            </div>

            {{-- Bank Info --}}
            <div class="p-3 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100">
                <p class="text-xs text-gray-500">Bank Account</p>
                <p class="font-bold text-sm" style="color: #0C3A30;">
                    @if($user->profile && $user->profile->bank_name)
                        {{ $user->profile->bank_name }}
                    @else
                        Not set
                    @endif
                </p>
            </div>

            {{-- Wallet Address --}}
            <div class="p-3 rounded-xl bg-gradient-to-r from-teal-50 to-cyan-50 border border-teal-100">
                <p class="text-xs text-gray-500">Primary Wallet</p>
                <p class="font-bold text-sm truncate" style="color: #0C3A30;" title="{{ $user->profile->bitcoin_address ?? 'Not set' }}">
                    {{ $user->profile->bitcoin_address ? substr($user->profile->bitcoin_address, 0, 12) . '...' : 'Not set' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Page header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <h6 class="font-bold text-2xl mb-1" style="color:#0C3A30;">Withdrawal History</h6>
            <p class="text-sm text-gray-500">Track all your withdrawal requests and their current status</p>
        </div>
        <a href="{{ route('user.withdraw.form') }}"
            class="px-5 py-2.5 rounded-xl font-semibold text-[#0C3A30] text-sm shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2"
            style="background: linear-gradient(135deg, #8AC304, #9EDD05);">
            <iconify-icon icon="ph:plus-bold" class="text-base"></iconify-icon>
            New Withdrawal
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-white/80 backdrop-blur-sm text-green-800 border border-green-200 flex items-center gap-3 shadow-sm">
        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
            <iconify-icon icon="ph:check-bold" class="text-green-600 text-lg"></iconify-icon>
        </div>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-white/80 backdrop-blur-sm text-red-800 border border-red-200 flex items-center gap-3 shadow-sm">
        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
            <iconify-icon icon="ph:warning-fill" class="text-red-600 text-lg"></iconify-icon>
        </div>
        <span class="text-sm font-medium">{{ session('error') }}</span>
    </div>
    @endif

    {{-- Main card --}}
    <div class="bg-white/85 backdrop-blur-sm rounded-2xl shadow-sm border border-white/60 p-6">
        <div class="bg-white/90 backdrop-blur-sm rounded-xl p-5">

        @if($withdrawals->isEmpty())
        {{-- Empty state --}}
        <div class="text-center py-20">
            <div class="w-28 h-28 mx-auto mb-5 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, #f0fde4, #e8f5e0);">
                <iconify-icon icon="mdi:inbox-remove-outline" style="font-size:56px; color:#8AC304;"></iconify-icon>
            </div>
            <p class="text-xl font-bold text-gray-700 mb-2">No Withdrawals Yet</p>
            <p class="text-sm text-gray-400 mb-6 max-w-sm mx-auto">You haven't made any withdrawal requests. Start your first withdrawal today!</p>
            <a href="{{ route('user.withdraw.form') }}"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-[#0C3A30] shadow-lg hover:shadow-xl transition-all duration-200"
                style="background: linear-gradient(135deg, #8AC304, #9EDD05);">
                <iconify-icon icon="ph:plus-bold" class="text-lg"></iconify-icon>
                Make Your First Withdrawal
            </a>
        </div>
        @else

        {{-- Stats summary --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 pb-6 border-b border-gray-100">
            <div class="text-center p-4 rounded-xl" style="background: linear-gradient(135deg, #f0fde4, #e8f5e0); border:1px solid rgba(138,195,4,0.2);">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Withdrawn</p>
                <p class="text-xl font-bold" style="color:#0C3A30;">${{ number_format($withdrawals->where('status', 'approved')->sum('amount'), 2) }}</p>
            </div>
            <div class="text-center p-4 rounded-xl bg-green-50 border border-green-100">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Completed</p>
                <p class="text-xl font-bold text-green-600">{{ $withdrawals->where('status', 'approved')->count() }}</p>
            </div>
            <div class="text-center p-4 rounded-xl bg-yellow-50 border border-yellow-100">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Pending</p>
                <p class="text-xl font-bold text-yellow-600">{{ $withdrawals->where('status', 'pending')->count() }}</p>
            </div>
            <div class="text-center p-4 rounded-xl bg-red-50 border border-red-100">
                <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Failed/Rejected</p>
                <p class="text-xl font-bold text-red-600">{{ $withdrawals->whereIn('status', ['rejected', 'failed'])->count() }}</p>
            </div>
        </div>

        {{-- Cards grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">

            @foreach ($withdrawals as $withdrawal)
            @php
                $profile = $withdrawal->user->profile ?? null;
                $user = $withdrawal->user;

                $status        = strtolower($withdrawal->status);
                $isInternal    = in_array($withdrawal->type, ['investment_transfer', 'profit']);
                $isProfit      = $withdrawal->type === 'profit';
                $isInvestment  = $withdrawal->type === 'investment_transfer';
                $isBalance     = $withdrawal->type === 'balance';

                $displayStatus = match($status) {
                    'approved' => 'Approved',
                    'pending'  => 'Pending',
                    'rejected' => 'Rejected',
                    'failed'   => 'Failed',
                    default    => ucfirst($withdrawal->status),
                };

                $badgeClass = match($status) {
                    'approved' => 'badge-approved',
                    'pending'  => 'badge-pending',
                    'failed'   => 'badge-failed',
                    'rejected' => 'badge-rejected',
                    default    => 'badge-default',
                };

                $statusIcon = match($status) {
                    'approved'          => 'ph:check-circle-fill',
                    'pending'           => 'ph:clock-fill',
                    'rejected','failed' => 'ph:warning-fill',
                    default             => 'ph:circle-fill',
                };

                $typeIcon = match($withdrawal->type ?? '') {
                    'investment_transfer' => 'ph:chart-pie-bold',
                    'profit'              => 'ph:trend-up-fill',
                    'balance'             => 'ph:money-fill',
                    default               => 'ph:money-fill',
                };

                $typeLabel = match($withdrawal->type ?? '') {
                    'investment_transfer' => 'Investment Withdrawal',
                    'profit'              => 'Profit Take',
                    'balance'             => 'Balance Withdrawal',
                    default               => 'Withdrawal',
                };

                if ($isInternal) {
                    $payment     = 'Internal Transfer';
                    $destination = 'Credited to Balance';
                } else {
                    $payment = match($withdrawal->payment_method) {
                        'cryptocurrency' => 'Cryptocurrency',
                        'digital_wallet' => 'Bank Transfer',
                        default          => ucfirst(str_replace('_', ' ', $withdrawal->payment_method ?? '')),
                    };

                    $destination    = 'N/A';
                    $walletCoinId   = null;

                    if ($withdrawal->payment_method === 'cryptocurrency') {
                        $walletCoinId = match($withdrawal->wallet_choice) {
                            'bitcoin'  => 'BTC',
                            'etherium' => 'ETH',
                            'usdt'     => 'USDT',
                            default    => null,
                        };
                        $destination = match($withdrawal->wallet_choice) {
                            'bitcoin'  => 'Bitcoin (BTC)',
                            'etherium' => 'Ethereum (ETH)',
                            'usdt'     => 'Tether (USDT)',
                            default    => ucfirst($withdrawal->wallet_choice ?? 'Crypto'),
                        };
                    } elseif ($withdrawal->payment_method === 'digital_wallet') {
                        if ($profile?->account_number) {
                            $destination = '****' . substr($profile->account_number, -4);
                        } elseif ($profile?->iban) {
                            $destination = '****' . substr($profile->iban, -4);
                        }
                    }
                }

                $grossAmount = floatval($withdrawal->amount);
                $netAmount   = floatval($withdrawal->net_amount ?? $withdrawal->amount);
                if ($isInternal && !$withdrawal->net_amount) $netAmount = $grossAmount;

                $bankFee    = $isBalance ? floatval($withdrawal->bank_fee ?? 0) : 0;
                $hasBankFee = $bankFee > 0;
            @endphp

            {{-- Individual card --}}
            <div class="group relative flex flex-col rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300"
                 style="background-image: url(assets/images/hero/hero-image-1.svg); background-size: cover; background-position: center;">

                <div class="absolute inset-0 bg-white/88 backdrop-blur-[2px] rounded-2xl pointer-events-none"></div>
                <div class="absolute top-0 left-0 right-0 h-[3px] rounded-t-2xl bg-gradient-to-r from-[#8AC304] to-[#9EDD05] opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"></div>

                <div class="relative z-10 flex flex-col flex-1 p-5">

                    {{-- Card header --}}
                    <div class="flex items-start justify-between mb-4 pb-3 border-b border-gray-200/70">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm"
                                 style="background: linear-gradient(135deg, #f0fde4, #d9f5a0);">
                                <iconify-icon icon="{{ $typeIcon }}" style="font-size:18px; color:#5a8a00;"></iconify-icon>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-400 leading-tight">{{ $typeLabel }}</p>
                                <h4 class="text-sm font-bold text-gray-800 leading-tight">
                                    #{{ str_pad($withdrawal->id, 4, '0', STR_PAD_LEFT) }}
                                </h4>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $badgeClass }} flex items-center gap-1.5 shadow-sm shrink-0">
                            <iconify-icon icon="{{ $statusIcon }}" style="font-size:12px;"></iconify-icon>
                            {{ $displayStatus }}
                        </span>
                    </div>

                    {{-- USER INFO SECTION --}}
                    <div class="bg-gray-50/80 rounded-lg p-3 mb-3 border border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-[#9EDD05]/20 flex items-center justify-center">
                                <iconify-icon icon="ph:user-fill" style="font-size:12px; color:#0C3A30;"></iconify-icon>
                            </div>
                            <span class="text-xs font-medium" style="color:#0C3A30;">{{ $user->name }}</span>
                            <span class="text-xs text-gray-400">|</span>
                            <span class="text-xs text-gray-500">{{ $user->email }}</span>
                        </div>
                        @if($profile)
                            <div class="flex flex-wrap gap-2 mt-1.5 text-xs text-gray-500">
                                @if($profile->phone)
                                    <span class="flex items-center gap-1">
                                        <iconify-icon icon="ph:phone-fill" style="font-size:10px;"></iconify-icon>
                                        {{ $profile->phone }}
                                    </span>
                                @endif
                                @if($profile->country)
                                    <span class="flex items-center gap-1">
                                        <iconify-icon icon="ph:globe-fill" style="font-size:10px;"></iconify-icon>
                                        {{ $profile->country }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Amount section --}}
                    <div class="space-y-2 text-sm flex-1">

                        @if($isBalance && $hasBankFee)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-xs">Gross Amount</span>
                            <span class="font-mono text-gray-400 line-through text-sm">${{ number_format($grossAmount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 text-xs flex items-center gap-1">
                                <iconify-icon icon="ph:bank-fill" style="font-size:11px;"></iconify-icon>
                                Bank Fee (5%)
                            </span>
                            <span class="font-mono text-orange-500 text-xs">-${{ number_format($bankFee, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center rounded-xl px-3 py-2.5 mt-1 shadow-sm"
                             style="background: linear-gradient(135deg, #61ff17, #1a5c46);">
                            <span class="font-bold text-white text-xs flex items-center gap-1">
                                <iconify-icon icon="ph:check-circle-fill" style="font-size:14px;"></iconify-icon>
                                You receive
                            </span>
                            <span class="font-bold text-white text-base font-mono" style="color:#61ff17 !important;">${{ number_format($netAmount, 2) }}</span>
                        </div>

                        @else
                        <div class="flex justify-between items-center rounded-xl px-3 py-3 border"
                             style="background: linear-gradient(135deg, #f0fde4, #e4f7cc); border-color: rgba(138,195,4,0.35);">
                            <span class="font-bold text-xs flex items-center gap-1" style="color:#0C3A30;">
                                <iconify-icon icon="ph:chart-pie-bold" style="font-size:14px;"></iconify-icon>
                                {{ $isInternal ? 'Credited to balance' : 'Amount' }}
                            </span>
                            <span class="font-bold text-lg font-mono" style="color:#0C3A30;">${{ number_format($grossAmount, 2) }}</span>
                        </div>
                        @endif

                        {{-- Method & destination --}}
                        <div class="pt-2 space-y-2 border-t border-gray-200/60">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-xs">Method</span>
                                <span class="font-semibold text-gray-700 text-xs flex items-center gap-1">
                                    @if($withdrawal->payment_method === 'cryptocurrency')
                                        <iconify-icon icon="ph:currency-circle-dollar-fill" style="font-size:13px; color:#f59e0b;"></iconify-icon>
                                    @elseif($withdrawal->payment_method === 'digital_wallet')
                                        <iconify-icon icon="ph:bank-fill" style="font-size:13px; color:#3b82f6;"></iconify-icon>
                                    @else
                                        <iconify-icon icon="ph:arrows-left-right-bold" style="font-size:13px; color:#8AC304;"></iconify-icon>
                                    @endif
                                    {{ $payment }}
                                </span>
                            </div>

                            @if($destination !== 'N/A' && $destination !== 'Credited to Balance')
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400 text-xs">Destination</span>
                                <span class="text-gray-600 text-xs flex items-center gap-1.5 font-mono truncate max-w-[160px]" title="{{ $destination }}">
                                    @if(!empty($walletCoinId))
                                        <img src="https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/svg/color/{{ strtolower($walletCoinId) }}.svg"
                                             alt="{{ $walletCoinId }}" width="14" height="14" class="rounded-full shrink-0"
                                             onerror="this.style.display='none'">
                                    @endif
                                    {{ $destination }}
                                </span>
                            </div>
                            @endif
                        </div>

                        {{-- Investment plan --}}
                        @if($withdrawal->investment_id && $withdrawal->investment)
                        <div class="flex justify-between items-center text-xs border-t border-gray-200/60 pt-2">
                            <span class="text-gray-400">Investment Plan</span>
                            <span class="font-semibold" style="color:#0C3A30;">{{ $withdrawal->investment->plan->name ?? 'N/A' }}</span>
                        </div>
                        @endif

                        {{-- Bank details toggle --}}
                        @if(!$isInternal && $withdrawal->payment_method === 'digital_wallet')
                        <div class="pt-0.5">
                            <button class="bank-details-toggle w-full px-2.5 py-1.5 text-[11px] font-semibold rounded-lg border flex justify-between items-center transition-all hover:shadow-sm"
                                    style="border-color:#8AC304; color:#0C3A30; background: rgba(240,253,228,0.6);">
                                <span class="flex items-center gap-1">
                                    <iconify-icon icon="ph:bank-fill" style="font-size:11px;"></iconify-icon>
                                    View Bank Details
                                </span>
                                <svg class="arrow-icon w-3 h-3 transition-transform duration-200 shrink-0"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <div class="bank-details-content hidden mt-1.5 p-2 rounded-lg border space-y-1 text-[11px] shadow-sm"
                                 style="border-color:#8AC304; background: rgba(240,253,228,0.8);">
                                @if($profile)
                                    <div class="flex justify-between py-0.5 border-b border-[#8AC304]/20">
                                        <span class="text-gray-500">Bank</span>
                                        <span class="font-semibold" style="color:#0C3A30;">
                                            {{ $profile->bank_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between py-0.5 border-b border-[#8AC304]/20">
                                        <span class="text-gray-500">Recipient</span>
                                        <span class="font-semibold" style="color:#0C3A30;">
                                            {{ $profile->recipient_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    @if($profile->account_number)
                                    <div class="flex justify-between py-0.5 border-b border-[#8AC304]/20">
                                        <span class="text-gray-500">Account</span>
                                        <span class="font-mono text-[10px]" style="color:#0C3A30;">
                                            {{ $profile->account_number }}
                                        </span>
                                    </div>
                                    @endif
                                    @if($profile->iban)
                                    <div class="flex justify-between py-0.5 border-b border-[#8AC304]/20">
                                        <span class="text-gray-500">IBAN</span>
                                        <span class="font-mono text-[10px]" style="color:#0C3A30;">
                                            {{ $profile->iban }}
                                        </span>
                                    </div>
                                    @endif
                                    @if($profile->swift_bic)
                                    <div class="flex justify-between py-0.5">
                                        <span class="text-gray-500">SWIFT</span>
                                        <span class="font-mono text-[10px]" style="color:#0C3A30;">
                                            {{ $profile->swift_bic }}
                                        </span>
                                    </div>
                                    @endif
                                @else
                                    <p class="text-center text-red-500 py-1 text-[11px]">
                                        No bank details on record.
                                    </p>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- Date --}}
                        <div class="flex justify-between items-center pt-2 border-t border-gray-200/60">
                            <span class="text-gray-400 text-xs flex items-center gap-1">
                                <iconify-icon icon="ph:calendar-fill" style="font-size:11px;"></iconify-icon>
                                Date
                            </span>
                            <span class="text-gray-500 text-xs">{{ $withdrawal->created_at->format('d M Y, h:i A') }}</span>
                        </div>

                        {{-- Admin note --}}
                        @if(in_array($status, ['rejected','failed']) && $withdrawal->admin_note)
                        <div class="mt-2 p-3 rounded-xl border-l-4 border-red-400 bg-red-50/80">
                            <p class="text-xs font-bold text-red-700 mb-1 flex items-center gap-1">
                                <iconify-icon icon="ph:warning-fill" style="font-size:13px;"></iconify-icon>
                                Admin Note
                            </p>
                            <p class="text-xs text-red-600">{{ $withdrawal->admin_note }}</p>
                        </div>
                        @endif

                        {{-- Status footer --}}
                        @if($status === 'pending')
                        <div class="mt-2 p-2 rounded-lg border text-center"
                             style="background: rgba(158,221,5,0.08); border-color:#9EDD05;">
                            <p class="text-xs flex items-center justify-center gap-1" style="color:#0C3A30;">
                                <iconify-icon icon="ph:clock-fill" style="font-size:13px; color:#9EDD05 !important;"></iconify-icon>
                                Processing • allow 1–3 minutes
                            </p>
                        </div>
                        @elseif($status === 'approved')
                        <div class="mt-2 p-2 rounded-lg border text-center"
                             style="background: rgba(138,195,4,0.12); border-color:#8AC304;">
                            <p class="text-xs flex items-center justify-center gap-1" style="color:#0C3A30;">
                                <iconify-icon icon="ph:check-circle-fill" style="font-size:13px; color:#8AC304 !important;"></iconify-icon>
                                Completed successfully
                            </p>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
            @endforeach

        </div>
        @endif
        </div>
    </div>

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.bank-details-toggle').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const content = this.nextElementSibling;
            const arrow   = this.querySelector('.arrow-icon');
            const open    = content.classList.toggle('hidden');
            arrow.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
        });
    });
});
</script>

<style>
    .badge-approved { background: linear-gradient(135deg, #dcfce7, #bbf7d0) !important; color: #166534 !important; }
    .badge-pending  { background: linear-gradient(135deg, #fef9c3, #fef08a) !important; color: #854d0e !important; }
    .badge-rejected { background: linear-gradient(135deg, #fee2e2, #fecaca) !important; color: #991b1b !important; }
    .badge-failed   { background: linear-gradient(135deg, #fee2e2, #fecaca) !important; color: #991b1b !important; }
    .badge-default  { background: #f3f4f6 !important; color: #374151 !important; }
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
    ::-webkit-scrollbar-thumb { background: #8AC304; border-radius: 3px; }
    ::-webkit-scrollbar-thumb:hover { background: #7bb502; }
</style>
@endsection