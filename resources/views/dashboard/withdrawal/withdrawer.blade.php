@extends('layout.user')

@section('content')
@php
$profile = auth()->user()->profile;

$plansOrdered = \App\Models\Plan::where('status', 'active')
    ->orderBy('minimum_amount')
    ->get();

$leastPlan = $plansOrdered->first();
$leastPlanName = $leastPlan->name ?? 'entry-level';

$bitcoin = $profile->bitcoin_address ?? null;
$etherium = $profile->etherium_address ?? null;
$usdt = $profile->usdt_address ?? null;

$recipientName = $profile->recipient_name ?? null;
$bankName = $profile->bank_name ?? null;
$accountNumber = $profile->account_number ?? null;
$iban = $profile->iban ?? null;
$swiftBic = $profile->swift_bic ?? null;
$bankAddress = $profile->bank_address ?? null;

$hasBankInfo = $recipientName && $bankName && ($accountNumber || $iban) && $swiftBic;
$hasCryptoWallet = $bitcoin || $etherium || $usdt;

// Calculate lock data - same pattern as copy-trading plan grid
$user = auth()->user();
$locked = $user->withdrawal_locked ?? false;

$lockData = [
    'locked' => $locked,
    'completed' => 0,
    'required' => 0,
    'plan_name' => null,
    'reason' => null,
];

if ($locked) {
    // Find active investment with incomplete sessions
    $incomplete = \App\Models\Investment::with('plan')
        ->where('user_id', $user->id)
        ->where('status', 'active')
        ->get()
        ->first(fn ($inv) => !$inv->hasCompletedRequiredSessions());
    
    if ($incomplete) {
        $lockData['completed'] = $incomplete->completed_sessions ?? 0;
        $lockData['required'] = $incomplete->plan->trading_sessions ?? 0;
        $lockData['plan_name'] = $incomplete->plan->name ?? 'Active Plan';
        $lockData['reason'] = 'sessions_incomplete';
    } else {
        $lockData['reason'] = 'admin_locked';
    }
}
@endphp

{{-- Alert: No payment method --}}
@if(!$hasCryptoWallet && !$hasBankInfo)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert('You must add a payment method before withdrawing.');
    });
</script>
@endif

{{-- Flash Messages --}}
@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200 flex justify-between items-center">
    <span>{{ session('success') }}</span>
    @if(session('success') === 'Withdrawal card generated!')
    <a href="{{ route('user.viewCard') }}"
        class="ml-4 px-3 py-1 text-sm rounded-full bg-[#8AC304] text-[#0C3A30] hover:bg-[#7bb502] transition">
        View Card
    </a>
    @endif
</div>
@endif

@if($errors->any())
<div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200 flex justify-between items-center">
    <span>{{ $errors->first() }}</span>
    @if($errors->first() === 'Please generate your withdrawal card before proceeding.')
    <form method="POST" action="{{ route('withdrawals.generateCard') }}">
        @csrf
        <button type="submit"
            class="ml-4 px-3 py-1 text-sm rounded-full bg-[#8AC304] text-[#0C3A30] hover:bg-[#7bb502] transition">
            Generate Card
        </button>
    </form>
    @endif
</div>
@endif

{{-- Locked Alert (Server-Rendered) - Compact warning style --}}
@if($lockData['locked'] && $lockData['reason'] === 'sessions_incomplete')
<div class="mb-4 rounded-lg border border-amber-200 p-3 flex items-start gap-3" style="background:#fef9e7;">
    <div class="flex-shrink-0 mt-0.5">
        <iconify-icon icon="ph:hourglass-medium-fill" style="font-size:18px; color:#dc2626;"></iconify-icon>
    </div>
    <div>
        <p class="text-sm font-semibold" style="color:#991b1b;">
            ⚠️ Withdrawals Unavailable: Complete Your Trading Session
        </p>
        <p class="text-xs text-gray-600 mt-0.5">
            Plan: <span class="font-semibold" style="color:#0C3A30;">{{ $lockData['plan_name'] }}</span>
        </p>
    </div>
</div>
@elseif($lockData['locked'])
<style>
    .withdrawal-warning {
        background: #FFFBEB !important;
        border: 1px solid #FCD34D !important;
        color: #92400E !important;
        border-radius: 10px !important;
        padding: 14px 16px !important;
        margin-bottom: 1rem !important;
        font-size: 14px !important;
        line-height: 1.5 !important;
    }

    .withdrawal-warning strong {
        color: #78350F !important;
        font-weight: 700 !important;
        margin-right: 6px !important;
    }

    .withdrawal-warning .warning-icon {
        color: #F59E0B !important;
        margin-right: 6px !important;
        font-size: 16px !important;
        vertical-align: middle !important;
    }
</style>

<div class="withdrawal-warning">
    <span class="warning-icon">⚠️</span>
    <strong>Withdrawal Unavailable:</strong>
    <span>Please complete your current trading session before requesting a withdrawal.</span>
</div>
@endif

{{-- Withdrawal Form --}}
<div class="max-w-xl mx-auto mt-6 p-6 rounded-2xl shadow-xl border">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-lg font-semibold text-[#0C3A30]">Withdraw Funds</h1>
        <a href="{{ route('user_dashboard') }}" class="text-sm text-gray-600 hover:text-lime-500">← Back to Dashboard</a>
    </div>

    <form action="{{ route('user.balance.withdraw') }}" method="POST" class="space-y-6" id="withdraw-form">
        @csrf

        {{-- Amount --}}
        <div>
            <label class="block text-sm font-medium text-gray-700">Withdrawal Amount</label>
            <div class="relative">
                <span class="absolute left-3 top-3 text-gray-500">$</span>
                <input type="number" name="amount" id="amount-input" min="1"
                    max="{{ auth()->user()->available_balance }}"
                    value="{{ old('amount') }}"
                    class="w-full pl-8 pr-4 py-3 rounded-lg border focus:outline-none"
                    style="border-color: #8AC304;" required>
            </div>
            <p class="mt-1 text-xs text-gray-500">
                Available balance: <strong>${{ number_format(auth()->user()->available_balance, 2) }}</strong>
            </p>
        </div>

        {{-- Fee Summary --}}
        <div id="fee-info" class="hidden p-4 rounded-xl border border-yellow-200 bg-yellow-50">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-yellow-600 text-base"></span>
                </div>
                <div class="flex-1 text-xs space-y-2">
                    <p class="text-sm font-semibold text-yellow-800">Fee Breakdown</p>

                    <div class="flex justify-between text-yellow-700">
                        <span>Gross Amount:</span>
                        <span id="gross-amount" class="font-mono font-semibold">$0.00</span>
                    </div>

                    <div id="row-management" class="flex justify-between text-red-600 hidden">
                        <span>Management Fee:</span>
                        <span id="management-fee" class="font-mono">-$0.00</span>
                    </div>

                    <div id="row-performance" class="flex justify-between text-red-600 hidden">
                        <span>Performance Fee:</span>
                        <span id="performance-fee" class="font-mono">-$0.00</span>
                    </div>

                    <div id="row-bank-fee" class="flex justify-between text-orange-600 hidden">
                        <span>Bank Transfer Fee (5%):</span>
                        <span id="bank-fee" class="font-mono">-$0.00</span>
                    </div>

                    <div class="flex justify-between font-bold text-yellow-900 border-t border-yellow-200 pt-2">
                        <span>Total Fees:</span>
                        <span id="total-fees" class="font-mono">-$0.00</span>
                    </div>

                    <div class="mt-2 p-2 rounded-lg bg-green-100 border border-green-300 flex justify-between items-center">
                        <span class="font-bold text-green-800 text-sm">You will receive:</span>
                        <span id="net-amount" class="font-bold text-green-700 text-base font-mono">$0.00</span>
                    </div>

                    <div id="fee-breakdown-list" class="hidden border-t border-yellow-200 pt-2 mt-1">
                        <p class="font-semibold text-yellow-800 mb-1 text-xs">By Plan:</p>
                        <div id="breakdown-items"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="no-fee-notice" class="hidden p-3 rounded-lg bg-green-50 border border-green-200 text-xs text-green-700">
            No fees apply to this withdrawal.
        </div>

        {{-- Transfer Method --}}
        <div class="relative">
            <label class="block mb-1 text-sm font-medium text-gray-700">Withdraw Method</label>
            <div id="custom-dropdown" tabindex="0"
                class="w-full px-4 py-3 rounded-lg border cursor-pointer flex justify-between items-center hover:border-[#0C3A30]"
                style="border-color: #8AC304;">
                <span id="selected-option-text">Select transfer method</span>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <input type="hidden" id="payment_method" name="payment_method" required>

            <div id="dropdown-options" class="absolute mt-1 w-full border rounded-lg shadow-lg hidden z-50"
                style="border-color: #8AC304; background-color: white;">
                @if($hasCryptoWallet)
                <div class="option-item px-4 py-3 cursor-pointer hover:bg-[#8AC304] hover:text-[#0C3A30]"
                    data-value="cryptocurrency">
                    Cryptocurrency Wallet
                </div>
                @endif
                @if($hasBankInfo)
                <div class="option-item px-4 py-3 cursor-pointer hover:bg-[#8AC304] hover:text-[#0C3A30]"
                    data-value="digital_wallet">
                    Bank Transfer
                </div>
                @endif
            </div>
        </div>

        <div id="wallet-info" class="hidden mt-4 relative"></div>
        <input type="hidden" name="wallet_choice" id="wallet_choice_input">

        {{-- PIN --}}
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Security PIN</label>
            <div class="grid grid-cols-4 gap-2">
                @for ($i = 1; $i <= 4; $i++)
                    <input type="password" name="digit{{ $i }}" maxlength="1" required inputmode="numeric"
                    class="pin-input h-12 text-center text-xl rounded-lg border" style="border-color: #8AC304;">
                @endfor
            </div>
        </div>

        {{-- Withdrawal Policy --}}
        <div class="flex items-center gap-2 pt-2">
            <button type="button" id="policy-trigger"
                class="flex items-center gap-1 text-xs font-semibold text-amber-700 hover:text-amber-900">
                <iconify-icon icon="ph:warning-circle-fill" style="color: #8AC304 !important;" class="text-base"></iconify-icon>
                <span class="underline">Withdrawal Policy — Must Read</span>
            </button>
        </div>

        {{-- Submit --}}
        <button type="submit" id="submitBtn"
            class="w-full flex items-center justify-center py-3 px-4 rounded-lg font-medium shadow hover:shadow-md transition"
            style="background-color: #8AC304; color:#0C3A30; margin-top:2rem;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            Initiate Withdrawal
        </button>
    </form>
</div>

<style>
    .warning-icon {
        font-size: 30px;
        color: #8AC304 !important;
        display: block;
    }
</style>

{{-- Policy Modal --}}
<div id="policyModal"
    style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(29, 28, 28, 0.6); align-items:center; justify-content:center; padding:1rem;">
    <div class="relative max-w-sm w-full bg-white rounded-2xl shadow-2xl p-8">
        <button type="button" id="policyClose" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition">
            <iconify-icon icon="ph:x-bold" class="text-xl"></iconify-icon>
        </button>
         <h3 class="flex items-center gap-3 border-b p-5  border-gray-200 pb-4 mb-5">
          
            <span class="text-xl font-bold text-[#0C3A30]">Withdrawal Policy</span>
        </h3>
       
              <ul class="space-y-4 text-sm p-5 text-gray-700 list-disc pl-5 leading-6">
            <li>Withdrawals are not available on the <strong>{{ $leastPlanName }}</strong> plan. Only accounts on higher plans are eligible to withdraw.</li>
            <li>A profit tax of <strong>10%–15%</strong> applies to all profits before a withdrawal can be processed.</li>
            <li>This profit tax is paid <strong>externally</strong> and is not deducted from your account balance. Contact support to obtain the task-paying wallet address, then send your tax payment. Once support confirms your payment, your withdrawal request token will be <strong>activated</strong>, and your withdrawal request will be approved.</li>
        </ul>
        <button type="button" id="policyAcknowledge" class="mt-6 w-full py-3 rounded-lg font-semibold transition hover:opacity-90" style="background:#8AC304;color:#0C3A30;">I Understand</button>
    </div>
</div>

{{-- Simplified Session Lock Modal (No Progress, No Plan Name) --}}
<div id="lockErrorModal"
    style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(12,58,48,0.75); align-items:center; justify-content:center; padding:1rem;">
    <div class="relative max-w-sm w-full bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-5" style="background:#0C3A30;">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-11 h-11 rounded-full bg-[#FEF2F2]">
                    <iconify-icon icon="ph:hourglass-medium-fill" style="font-size:22px; color:#dc2626;"></iconify-icon>
                </div>
                <h3 class="text-lg font-bold text-white" id="lockModalTitle">Trading Sessions Incomplete</h3>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 text-center mb-6" id="lockModalMessage">
                Complete your trading sessions before withdrawing.
            </p>
            <div class="space-y-3">
                <a href="{{ route('copy-trading.index') }}"
                    class="block w-full py-3 rounded-lg text-center font-semibold transition hover:opacity-90"
                    style="background:#8AC304; color:#0C3A30;">
                    <iconify-icon icon="ph:arrow-right" class="inline mr-2"></iconify-icon>
                    Go to Copy Trading
                </a>
                <button id="contactSupportBtn"
                    class="block w-full py-3 rounded-lg text-center font-semibold transition hover:opacity-90 border-2"
                    style="border-color:#8AC304; color:#0C3A30; background:transparent;">
                    <iconify-icon icon="ph:headset-fill" class="inline mr-2"></iconify-icon>
                    Contact Support
                </button>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            <button onclick="closeLockModal()"
                class="w-full py-2 text-sm font-semibold text-gray-500 hover:text-gray-700 transition" style="background:#8AC304; color:#0C3A30;">
                Close
            </button>
        </div>
    </div>
</div>

{{-- Support Modal --}}
<div id="supportModal"
    style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(12,58,48,0.75); align-items:center; justify-content:center; padding:1rem;">
    <div class="relative max-w-sm w-full bg-white rounded-2xl shadow-2xl p-6">
        <button type="button" id="supportModalClose" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition">
            <iconify-icon icon="ph:x-bold" class="text-xl"></iconify-icon>
        </button>
        <div class="text-center">
            <div class="flex items-center justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-[#F3F9E8] flex items-center justify-center">
                    <iconify-icon icon="ph:headset-fill" style="font-size:32px; color:#8AC304;"></iconify-icon>
                </div>
            </div>
            <h3 class="text-xl font-bold text-[#0C3A30] mb-2">Contact Support</h3>
            <p class="text-sm text-gray-600 mb-4">
                Our support team will help you complete your trading sessions.<br>
                <span class="text-xs text-gray-400">Response within 24 hours</span>
            </p>
            <div class="space-y-3">
                <a href="mailto:support@chartmasterscircle.com?subject=Help with Trading Sessions"
                    class="block w-full py-3 rounded-lg text-center font-semibold transition hover:opacity-90"
                    style="background:#8AC304; color:#0C3A30;">
                    <iconify-icon icon="ph:envelope-fill" class="inline mr-2"></iconify-icon>
                    Email Support
                </a>
                <button onclick="closeSupportModal()"
                    class="block w-full py-3 rounded-lg text-center font-semibold text-gray-500 hover:text-gray-700 transition border border-gray-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // ── Server-rendered data ───────────────────────────────────────────
    const hasBankInfo = @json($hasBankInfo);
    const hasCryptoWallet = @json($hasCryptoWallet);
    const profileUrl = "{{ route('profile.show') }}";
    const withdrawalLock = @json($lockData);

    const bankDetails = {
        recipientName: @json($recipientName),
        bankName: @json($bankName),
        accountNumber: @json($accountNumber),
        iban: @json($iban),
        swiftBic: @json($swiftBic),
        bankAddress: @json($bankAddress),
    };

    const cryptoWallets = {
        bitcoin: @json($bitcoin),
        etherium: @json($etherium),
        usdt: @json($usdt),
    };

    // ── DOM refs ────────────────────────────────────────────────────────
    const amountInput = document.getElementById('amount-input');
    const paymentMethodInput = document.getElementById('payment_method');
    const feeInfo = document.getElementById('fee-info');
    const noFeeNotice = document.getElementById('no-fee-notice');
    const grossAmountSpan = document.getElementById('gross-amount');
    const managementFeeSpan = document.getElementById('management-fee');
    const performanceFeeSpan = document.getElementById('performance-fee');
    const bankFeeSpan = document.getElementById('bank-fee');
    const totalFeesSpan = document.getElementById('total-fees');
    const netAmountSpan = document.getElementById('net-amount');
    const feeBreakdownList = document.getElementById('fee-breakdown-list');
    const breakdownItems = document.getElementById('breakdown-items');
    const rowManagement = document.getElementById('row-management');
    const rowPerformance = document.getElementById('row-performance');
    const rowBankFee = document.getElementById('row-bank-fee');
    const withdrawForm = document.getElementById('withdraw-form');
    const submitBtn = document.getElementById('submitBtn');

    // ── Utilities ──────────────────────────────────────────────────────
    function fmt(n) {
        return '$' + parseFloat(n).toFixed(2);
    }

    function debounce(fn, ms) {
        let t;
        return function(...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), ms);
        };
    }

    // ── Lock Modal ────────────────────────────────────────────────────
    function showLockModal(data) {
        const lockModal = document.getElementById('lockErrorModal');
        if (!lockModal) return;

        const title = document.getElementById('lockModalTitle');
        const message = document.getElementById('lockModalMessage');

        if (data.reason === 'admin_locked') {
            title.textContent = 'Withdrawals Locked';
            message.textContent = 'Please complete your current trading session before requesting a withdrawal.';
        } else if (data.reason === 'check_failed') {
            title.textContent = 'Unable to Verify';
            message.textContent = 'Could not verify your withdrawal status. Please try again or contact support.';
        } else {
            title.textContent = 'Trading Sessions Incomplete';
            message.textContent = 'Complete your trading sessions before withdrawing.';
        }

        lockModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    window.closeLockModal = function() {
        const lockModal = document.getElementById('lockErrorModal');
        if (lockModal) {
            lockModal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    // ── Support Modal ──────────────────────────────────────────────────
    function openSupportModal() {
        const supportModal = document.getElementById('supportModal');
        if (supportModal) {
            supportModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    window.closeSupportModal = function() {
        const supportModal = document.getElementById('supportModal');
        if (supportModal) {
            supportModal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    // ── Fee Recalculation ─────────────────────────────────────────────
    async function recalculateFees() {
        const amount = parseFloat(amountInput.value) || 0;
        const paymentMethod = paymentMethodInput.value || 'cryptocurrency';

        if (amount <= 0) {
            feeInfo.classList.add('hidden');
            noFeeNotice.classList.add('hidden');
            return;
        }

        try {
            const response = await fetch('{{ route("withdrawal.calculate-fees") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ amount, payment_method: paymentMethod }),
            });

            const data = await response.json();

            if (data.total_fees > 0) {
                feeInfo.classList.remove('hidden');
                noFeeNotice.classList.add('hidden');

                grossAmountSpan.textContent = fmt(amount);
                totalFeesSpan.textContent = '-' + fmt(data.total_fees);
                netAmountSpan.textContent = fmt(data.net_amount);

                rowManagement.classList.toggle('hidden', data.total_management_fee <= 0);
                rowPerformance.classList.toggle('hidden', data.total_performance_fee <= 0);
                rowBankFee.classList.toggle('hidden', data.bank_fee <= 0);

                if (data.total_management_fee > 0) {
                    managementFeeSpan.textContent = '-' + fmt(data.total_management_fee);
                }
                if (data.total_performance_fee > 0) {
                    performanceFeeSpan.textContent = '-' + fmt(data.total_performance_fee);
                }
                if (data.bank_fee > 0) {
                    bankFeeSpan.textContent = '-' + fmt(data.bank_fee);
                }

                if (data.fee_breakdown && data.fee_breakdown.length > 0) {
                    feeBreakdownList.classList.remove('hidden');
                    breakdownItems.innerHTML = data.fee_breakdown.map(fee => `
                        <div class="flex justify-between mb-1 text-xs">
                            <span class="text-yellow-700">${fee.plan_name}:</span>
                            <span class="text-red-600 font-mono">
                                ${fee.management_fee > 0 ? `Mgmt: ${fmt(fee.management_fee)}` : ''}
                                ${fee.management_fee > 0 && fee.performance_fee > 0 ? ' | ' : ''}
                                ${fee.performance_fee > 0 ? `Perf: ${fmt(fee.performance_fee)}` : ''}
                            </span>
                        </div>
                    `).join('');
                } else {
                    feeBreakdownList.classList.add('hidden');
                }

            } else {
                feeInfo.classList.add('hidden');
                noFeeNotice.classList.remove('hidden');
            }

        } catch (err) {
            console.error('Fee calculation error:', err);
        }
    }

    // ── DOMContentLoaded ──────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {

        amountInput?.addEventListener('input', debounce(recalculateFees, 450));

        // ── Policy Modal ──────────────────────────────────────────────
        const policyTrigger = document.getElementById('policy-trigger');
        const policyModal = document.getElementById('policyModal');
        const policyClose = document.getElementById('policyClose');
        const policyAcknowledge = document.getElementById('policyAcknowledge');

        if (policyModal && policyModal.parentElement !== document.body) {
            document.body.appendChild(policyModal);
        }

        function openPolicyModal() {
            policyModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closePolicyModal() {
            policyModal.style.display = 'none';
            document.body.style.overflow = '';
        }

        policyTrigger?.addEventListener('click', openPolicyModal);
        policyClose?.addEventListener('click', closePolicyModal);
        policyAcknowledge?.addEventListener('click', closePolicyModal);
        policyModal?.addEventListener('click', function(e) {
            if (e.target === policyModal) closePolicyModal();
        });

        // ── Support Modal ──────────────────────────────────────────────
        const supportModal = document.getElementById('supportModal');
        const supportModalClose = document.getElementById('supportModalClose');
        const contactSupportBtn = document.getElementById('contactSupportBtn');

        if (supportModal && supportModal.parentElement !== document.body) {
            document.body.appendChild(supportModal);
        }

        contactSupportBtn?.addEventListener('click', openSupportModal);
        supportModalClose?.addEventListener('click', closeSupportModal);
        supportModal?.addEventListener('click', function(e) {
            if (e.target === supportModal) closeSupportModal();
        });

        // ── Lock Modal ──────────────────────────────────────────────────
        const lockModal = document.getElementById('lockErrorModal');
        if (lockModal && lockModal.parentElement !== document.body) {
            document.body.appendChild(lockModal);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (policyModal.style.display === 'flex') closePolicyModal();
                if (lockModal.style.display === 'flex') closeLockModal();
                if (supportModal.style.display === 'flex') closeSupportModal();
            }
        });

        // ── Transfer Method Dropdown ──────────────────────────────────
        const dropdown = document.getElementById('custom-dropdown');
        const options = document.getElementById('dropdown-options');
        const selectedText = document.getElementById('selected-option-text');
        const walletInfo = document.getElementById('wallet-info');

        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!hasBankInfo && !hasCryptoWallet) {
                showUpdateProfileDropdown();
                return;
            }
            options.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !options.contains(e.target)) {
                options.classList.add('hidden');
            }
        });

        document.querySelectorAll('.option-item').forEach(function(option) {
            option.addEventListener('click', function() {
                selectedText.textContent = this.textContent.trim();
                paymentMethodInput.value = this.dataset.value;
                options.classList.add('hidden');

                if (this.dataset.value === 'cryptocurrency') {
                    showCryptoWalletSelection();
                } else if (this.dataset.value === 'digital_wallet') {
                    showBankSelection();
                } else {
                    walletInfo.classList.add('hidden');
                    walletInfo.innerHTML = '';
                }
                recalculateFees();
            });
        });

        // ── Wallet Selection ──────────────────────────────────────────
        function showCryptoWalletSelection() {
            walletInfo.classList.remove('hidden');
            walletInfo.innerHTML = `
                <label class="block mb-1 text-sm font-medium text-gray-700">Select Cryptocurrency Wallet</label>
                <div id="wallet-dropdown" tabindex="0"
                    class="w-full px-4 py-3 rounded-lg border cursor-pointer flex justify-between items-center"
                    style="border-color:#8AC304; background-color:white;">
                    <span id="wallet-text">Choose wallet address</span>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="wallet-options"
                    class="absolute z-20 mt-1 border rounded-lg shadow-lg hidden bg-white text-gray-800 w-full overflow-auto"
                    style="border-color:#8AC304;">
                    ${cryptoWallets.bitcoin ? `<div class="wallet-item px-4 py-3 cursor-pointer hover:bg-gray-100" data-wallet="bitcoin">🟢 BTC — ${cryptoWallets.bitcoin}</div>` : ''}
                    ${cryptoWallets.etherium ? `<div class="wallet-item px-4 py-3 cursor-pointer hover:bg-gray-100" data-wallet="etherium">🟢 ETH — ${cryptoWallets.etherium}</div>` : ''}
                    ${cryptoWallets.usdt ? `<div class="wallet-item px-4 py-3 cursor-pointer hover:bg-gray-100" data-wallet="usdt">🟢 USDT — ${cryptoWallets.usdt}</div>` : ''}
                    ${!hasCryptoWallet ? `<div class="p-4 text-sm text-yellow-800 bg-yellow-50">No wallets added. <a href="${profileUrl}" class="underline">Go to profile →</a></div>` : ''}
                </div>`;
            setupWalletDropdownEvents();
        }

        function showBankSelection() {
            walletInfo.classList.remove('hidden');
            walletInfo.innerHTML = `
                <div class="mb-4 p-3 rounded-xl border-2 shadow-md"
                    style="background:linear-gradient(135deg,#fef3c7,#fde68a,#fbbf24); border-color:#f59e0b;">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color:#f59e0b;">
                            <span class="text-xl">⚠️</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-amber-900">5% Bank Transfer Fee Applies</p>
                            <p class="text-xs text-amber-800">Crypto withdrawals are <strong>commission-free</strong>.</p>
                        </div>
                    </div>
                </div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Your Bank Account</label>
                <div id="bank-dropdown" tabindex="0"
                    class="w-full px-4 py-3 rounded-lg border cursor-pointer flex justify-between items-center"
                    style="border-color:#8AC304; background-color:white;">
                    <div class="flex items-center gap-2">
                        <span class="text-xs">🏦</span>
                        <span id="bank-text" class="text-gray-600">View bank details</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="bank-options"
                    class="absolute z-20 mt-1 border rounded-lg shadow-lg hidden bg-white w-full overflow-auto"
                    style="border-color:#8AC304; max-height:300px;">
                    ${hasBankInfo ? `
                    <div class="bank-item cursor-pointer p-4 hover:bg-gray-50" data-bank="primary">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-[#8AC304] flex items-center justify-center">
                                <span class="text-white">🏦</span>
                            </div>
                            <p class="font-semibold text-[#0C3A30] text-sm">
                                ${bankDetails.bankName || 'Bank'}
                                <span class="ml-1 px-2 py-0.5 rounded text-xs text-white" style="background:#8AC304;">Verified</span>
                            </p>
                        </div>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">Recipient:</span><span class="font-semibold text-[#0C3A30]">${bankDetails.recipientName || 'N/A'}</span></div>
                            ${bankDetails.accountNumber ? `<div class="flex justify-between"><span class="text-gray-500">Account:</span><span class="font-mono text-xs">${bankDetails.accountNumber}</span></div>` : ''}
                            ${bankDetails.iban ? `<div class="flex justify-between"><span class="text-gray-500">IBAN:</span><span class="font-mono text-xs">${bankDetails.iban}</span></div>` : ''}
                            ${bankDetails.swiftBic ? `<div class="flex justify-between"><span class="text-gray-500">SWIFT:</span><span class="font-mono text-xs">${bankDetails.swiftBic}</span></div>` : ''}
                        </div>
                        <p class="mt-3 text-xs text-center text-white rounded py-1" style="background:#8AC304;">✓ Click to select</p>
                    </div>` : `
                    <div class="p-4 text-center bg-yellow-50">
                        <p class="text-yellow-800 font-semibold text-sm mb-2">Bank Info Required</p>
                        <a href="${profileUrl}" class="px-4 py-2 rounded-lg text-white text-sm" style="background:#8AC304;">Add Bank Details</a>
                    </div>`}
                </div>`;
            setupBankDropdownEvents();
        }

        function setupWalletDropdownEvents() {
            const wd = document.getElementById('wallet-dropdown');
            const wo = document.getElementById('wallet-options');
            const wt = document.getElementById('wallet-text');
            const wc = document.getElementById('wallet_choice_input');
            if (!wd || !wo) return;

            wd.addEventListener('click', function(e) {
                wo.classList.toggle('hidden');
                e.stopPropagation();
            });

            document.addEventListener('click', function(e) {
                if (!wd.contains(e.target) && !wo.contains(e.target)) {
                    wo.classList.add('hidden');
                }
            });

            document.querySelectorAll('.wallet-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    wt.textContent = this.textContent.trim();
                    wc.value = this.dataset.wallet;
                    wo.classList.add('hidden');
                });
            });
        }

        function setupBankDropdownEvents() {
            const bd = document.getElementById('bank-dropdown');
            const bo = document.getElementById('bank-options');
            const bt = document.getElementById('bank-text');
            const wc = document.getElementById('wallet_choice_input');
            if (!bd || !bo) return;

            bd.addEventListener('click', function(e) {
                bo.classList.toggle('hidden');
                e.stopPropagation();
            });

            document.addEventListener('click', function(e) {
                if (!bd.contains(e.target) && !bo.contains(e.target)) {
                    bo.classList.add('hidden');
                }
            });

            document.querySelectorAll('.bank-item').forEach(function(item) {
                item.addEventListener('click', function() {
                    bt.textContent = `${bankDetails.bankName} — ${bankDetails.accountNumber || bankDetails.iban}`;
                    bt.classList.replace('text-gray-600', 'text-gray-800');
                    wc.value = this.dataset.bank;
                    bo.classList.add('hidden');
                });
            });
        }

        function showUpdateProfileDropdown() {
            if (document.getElementById('update-profile-dropdown')) return;

            const el = document.createElement('div');
            el.id = 'update-profile-dropdown';
            el.className = 'absolute mt-2 w-full rounded-xl border shadow-lg bg-white z-50 overflow-hidden opacity-0 translate-y-3 transition-all duration-500';
            el.style.borderColor = '#8AC304';
            el.innerHTML = `
                <div class="p-4 text-center bg-gradient-to-br from-yellow-50 to-amber-100 rounded-xl">
                    <p class="text-amber-900 font-semibold text-sm mb-1">No Payment Method Found</p>
                    <p class="text-xs text-amber-700 mb-3">Update your profile to add a withdrawal method.</p>
                    <a href="${profileUrl}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-white text-xs font-semibold"
                        style="background:linear-gradient(135deg,#9EDD05,#8AC304);">Go to Profile →</a>
                </div>`;
            dropdown.parentElement.appendChild(el);

            setTimeout(function() {
                el.classList.replace('opacity-0', 'opacity-100');
                el.classList.replace('translate-y-3', 'translate-y-0');
            }, 50);

            document.addEventListener('click', function h(e) {
                if (!dropdown.contains(e.target) && !el.contains(e.target)) {
                    el.classList.replace('opacity-100', 'opacity-0');
                    setTimeout(function() { el.remove(); }, 400);
                    document.removeEventListener('click', h);
                }
            });
        }

        // ── PIN Auto-Jump ─────────────────────────────────────────────
        const pinInputs = document.querySelectorAll('.pin-input');
        pinInputs.forEach(function(input, idx) {
            input.addEventListener('input', function() {
                if (input.value.length === 1 && idx < pinInputs.length - 1) {
                    pinInputs[idx + 1].focus();
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && input.value === '' && idx > 0) {
                    pinInputs[idx - 1].focus();
                }
            });
        });

        pinInputs[0]?.addEventListener('paste', function(e) {
            e.preventDefault();
            const digits = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/\D/g, '').split('').slice(0, 4);
            digits.forEach(function(d, i) {
                pinInputs[i].value = d;
            });
            pinInputs[Math.min(digits.length, 3)].focus();
        });

        // ── Form Submit ────────────────────────────────────────────────
        withdrawForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (withdrawalLock.locked) {
                showLockModal(withdrawalLock);
                return;
            }

            submitBtn.disabled = true;
            const originalHTML = submitBtn.innerHTML;
            submitBtn.innerHTML = 'Checking...';
            submitBtn.style.backgroundColor = '#B2B2B2';
            submitBtn.style.color = '#333';

            fetch('{{ route("check.withdrawal.lock") }}', {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
            })
            .then(function(response) {
                if (!response.ok) throw new Error('Server error');
                return response.json();
            })
            .then(function(data) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
                submitBtn.style.backgroundColor = '';
                submitBtn.style.color = '';

                if (data.locked) {
                    showLockModal(data);
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Processing...';
                withdrawForm.submit();
            })
            .catch(function(err) {
                console.error('Lock check error:', err);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
                submitBtn.style.backgroundColor = '';
                submitBtn.style.color = '';
                showLockModal({
                    locked: true,
                    reason: 'check_failed',
                    completed: 0,
                    required: 0,
                    plan_name: null,
                });
            });
        });

    }); // end DOMContentLoaded
</script>

<style>
    .option-item:hover,
    .wallet-item:hover {
        background-color: #8AC304 !important;
        color: #0C3A30 !important;
    }

    .bank-item:hover {
        background-color: #f0fde4 !important;
    }

    select:focus,
    input:focus,
    #custom-dropdown:focus,
    #wallet-dropdown:focus,
    #bank-dropdown:focus {
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(138, 195, 4, .3) !important;
        border-color: #8AC304 !important;
    }

    #bank-options:not(.hidden),
    #wallet-options:not(.hidden),
    #dropdown-options:not(.hidden) {
        animation: slideDown .2s ease-out;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #bank-options::-webkit-scrollbar {
        width: 4px;
    }

    #bank-options::-webkit-scrollbar-thumb {
        background: #8AC304;
        border-radius: 2px;
    }
</style>
@endsection