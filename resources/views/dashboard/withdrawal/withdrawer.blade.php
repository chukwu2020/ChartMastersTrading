@extends('layout.user')

@section('content')
@php
$profile = auth()->user()->profile;

// Fetch active plans ordered by minimum amount so we can identify
// the entry-level (least) plan — this is the one exempt from withdrawals.
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
@endphp

@if(!$hasCryptoWallet && !$hasBankInfo)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        alert('You must add a payment method before withdrawing.');
    });
</script>
@endif

{{-- Flash messages --}}
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

<div class="max-w-xl mx-auto mt-10 p-6 rounded-2xl shadow-xl border">

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

        {{-- ── FEE SUMMARY BOX ── --}}
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

                    {{-- Management fee row — hidden if zero --}}
                    <div id="row-management" class="flex justify-between text-red-600 hidden">
                        <span>Management Fee:</span>
                        <span id="management-fee" class="font-mono">-$0.00</span>
                    </div>

                    {{-- Performance fee row — hidden if zero --}}
                    <div id="row-performance" class="flex justify-between text-red-600 hidden">
                        <span>Performance Fee:</span>
                        <span id="performance-fee" class="font-mono">-$0.00</span>
                    </div>

                    {{-- Bank fee row — hidden for crypto --}}
                    <div id="row-bank-fee" class="flex justify-between text-orange-600 hidden">
                        <span>Bank Transfer Fee (5%):</span>
                        <span id="bank-fee" class="font-mono">-$0.00</span>
                    </div>

                    <div class="flex justify-between font-bold text-yellow-900 border-t border-yellow-200 pt-2">
                        <span>Total Fees:</span>
                        <span id="total-fees" class="font-mono">-$0.00</span>
                    </div>

                    {{-- Net amount — most prominent --}}
                    <div class="mt-2 p-2 rounded-lg bg-green-100 border border-green-300 flex justify-between items-center">
                        <span class="font-bold text-green-800 text-sm"> You will receive:</span>
                        <span id="net-amount" class="font-bold text-green-700 text-base font-mono">$0.00</span>
                    </div>

                    {{-- Per-plan breakdown --}}
                    <div id="fee-breakdown-list" class="hidden border-t border-yellow-200 pt-2 mt-1">
                        <p class="font-semibold text-yellow-800 mb-1 text-xs">By Plan:</p>
                        <div id="breakdown-items"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- No-fee notice --}}
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

        {{-- Wallet / Bank selection --}}
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

        {{-- Withdrawal Policy Trigger --}}
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

<div id="policyModal"
    style="
        display:none;
        position:fixed;
        inset:0;
        z-index:99999;
        background:rgba(12,58,48,0.75);
        align-items:center;
        justify-content:center;
        padding:1rem;
    ">

    {{-- Modal shell is now a flex column capped at 85vh:
         header + footer stay fixed, only the middle content area scrolls. --}}
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col" style="max-height:85vh;">

        <!-- Header (fixed) -->
        <div class="flex items-center justify-between px-6 py-5 bg-[#0C3A30] flex-shrink-0">

            <div class="flex items-center gap-3">

                <div class="flex items-center justify-center w-11 h-11 rounded-full bg-[#F3F9E8]">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="w-7 h-7"
                        fill="#8AC304"
                        viewBox="0 0 24 24">

                        <path fill-rule="evenodd"
                            d="M2.25 12a9.75 9.75 0 1119.5 0 9.75 9.75 0 01-19.5 0zm9-4.5a.75.75 0 011.5 0v5.25a.75.75 0 01-1.5 0V7.5zm.75 9a1.125 1.125 0 100-2.25 1.125 1.125 0 000 2.25z"
                            clip-rule="evenodd" />

                    </svg>

                </div>

                <h3 class="text-xl font-bold text-white">
                    Withdrawal Policy
                </h3>

            </div>

            <button
                type="button"
                id="policyClose"
                class="flex items-center justify-center w-9 h-9 rounded-full text-white/70 hover:text-white hover:bg-white/10 transition">

                <iconify-icon
                    icon="ph:x-bold"
                    class="text-xl">
                </iconify-icon>

            </button>

        </div>


        <!-- Policy Content (scrollable) -->
        <div class="p-6 overflow-y-auto flex-1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Policy 1 -->
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">

                    <h6 class="font-semibold text-[#0C3A30] mb-2">
                        Plan Eligibility
                    </h6>

                    <p class="text-sm text-gray-700 leading-6">
                        Withdrawals are not available on the
                        <strong>{{ $leastPlanName }}</strong> plan.
                        Only accounts on higher plans are eligible to withdraw.
                    </p>

                </div>


                <!-- Policy 2 -->
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">

                    <h6 class="font-semibold text-[#0C3A30] mb-2">
                        Profit Tax
                    </h6>

                    <p class="text-sm text-gray-700 leading-6">
                        A profit tax of <strong>10%–15%</strong> applies to all
                        profits before a withdrawal can be processed.
                    </p>

                </div>


                <!-- Policy 3 -->
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">

                    <h6 class="font-semibold text-[#0C3A30] mb-2">
                        External Payment
                    </h6>

                    <p class="text-sm text-gray-700 leading-6">
                        The profit tax is paid <strong>externally</strong> and is
                        not deducted from your account balance. Contact support
                        to obtain the task-paying wallet address.
                    </p>

                </div>


                <!-- Policy 4 -->
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">

                    <h6 class="font-semibold text-[#0C3A30] mb-2">
                        Trading Sessions
                    </h6>

                    <p class="text-sm text-gray-700 leading-6">
                        Traders must complete
                        <strong>all trading sessions under their plan</strong>
                        before becoming eligible for a
                        <strong>standard withdrawal</strong>.
                    </p>

                </div>


                <!-- Policy 5 -->
                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 md:col-span-2">

                    <h6 class="font-semibold text-[#0C3A30] mb-2">
                        Early Withdrawal
                    </h6>

                    <p class="text-sm text-gray-700 leading-6">
                        Requesting a withdrawal before completing
                        <strong>all required trading sessions</strong> is
                        considered an <strong>early withdrawal request</strong>.
                        This is subject to an
                        <strong>early withdrawal requirement of 20% of the
                            available account balance</strong> at the time of
                        the request.
                    </p>

                </div>

            </div>

        </div>


        <!-- Footer (fixed) -->
        <div class="px-6 py-5 border-t border-gray-100 bg-white flex-shrink-0">

            <button
                type="button"
                id="policyAcknowledge"
                class="w-full py-3 rounded-lg font-semibold transition hover:opacity-90"
                style="background:#8AC304; color:#0C3A30;">

                I Understand

            </button>

        </div>

    </div>

</div>

<script>
    const hasBankInfo = @json($hasBankInfo);
    const hasCryptoWallet = @json($hasCryptoWallet);
    const profileUrl = "{{ route('profile.show') }}";

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

    // ── Fee recalculation ────────────────────────────────────────────────
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
                body: JSON.stringify({
                    amount,
                    payment_method: paymentMethod
                }),
            });

            const data = await response.json();

            if (data.total_fees > 0) {
                feeInfo.classList.remove('hidden');
                noFeeNotice.classList.add('hidden');

                grossAmountSpan.textContent = fmt(amount);
                totalFeesSpan.textContent = '-' + fmt(data.total_fees);
                netAmountSpan.textContent = fmt(data.net_amount);

                // Management fee row
                if (data.total_management_fee > 0) {
                    managementFeeSpan.textContent = '-' + fmt(data.total_management_fee);
                    rowManagement.classList.remove('hidden');
                } else {
                    rowManagement.classList.add('hidden');
                }

                // Performance fee row
                if (data.total_performance_fee > 0) {
                    performanceFeeSpan.textContent = '-' + fmt(data.total_performance_fee);
                    rowPerformance.classList.remove('hidden');
                } else {
                    rowPerformance.classList.add('hidden');
                }

                // Bank transfer fee row
                if (data.bank_fee > 0) {
                    bankFeeSpan.textContent = '-' + fmt(data.bank_fee);
                    rowBankFee.classList.remove('hidden');
                } else {
                    rowBankFee.classList.add('hidden');
                }

                // Per-plan breakdown
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

    // Recalculate whenever amount changes
    amountInput?.addEventListener('input', debounce(recalculateFees, 450));

    // ── DOMContentLoaded ─────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {

        // Disable submit on form send to prevent double-submit
        document.getElementById('withdraw-form').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = 'Processing...';
            btn.style.backgroundColor = '#B2B2B2';
            btn.style.color = '#333';
        });

        // ── Withdrawal policy modal ─────────────────────────────────────
        const policyTrigger = document.getElementById('policy-trigger');
        const policyModal = document.getElementById('policyModal');
        const policyClose = document.getElementById('policyClose');
        const policyAcknowledge = document.getElementById('policyAcknowledge');

        // Force it to the very end of <body> so no ancestor (transform,
        // overflow:hidden, or otherwise) can trap it or break its
        // fixed positioning. This is what fixes it rendering inline
        // instead of as a full-screen overlay.
        if (policyModal && policyModal.parentElement !== document.body) {
            document.body.appendChild(policyModal);
        }

        function openPolicyModal() {
            policyModal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // lock background scroll
        }

        function closePolicyModal() {
            policyModal.style.display = 'none';
            document.body.style.overflow = '';
        }

        policyTrigger?.addEventListener('click', openPolicyModal);
        policyClose?.addEventListener('click', closePolicyModal);
        policyAcknowledge?.addEventListener('click', closePolicyModal);
        policyModal?.addEventListener('click', (e) => {
            if (e.target === policyModal) closePolicyModal();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && policyModal.style.display === 'flex') closePolicyModal();
        });

        // ── Main dropdown ──────────────────────────────────────────────
        const dropdown = document.getElementById('custom-dropdown');
        const options = document.getElementById('dropdown-options');
        const selectedText = document.getElementById('selected-option-text');
        const walletInfo = document.getElementById('wallet-info');

        dropdown.addEventListener('click', e => {
            e.stopPropagation();
            if (!hasBankInfo && !hasCryptoWallet) {
                showUpdateProfileDropdown();
                return;
            }
            options.classList.toggle('hidden');
        });

        document.addEventListener('click', e => {
            if (!dropdown.contains(e.target) && !options.contains(e.target)) {
                options.classList.add('hidden');
            }
        });

        document.querySelectorAll('.option-item').forEach(option => {
            option.addEventListener('click', function() {
                selectedText.textContent = this.textContent.trim();
                paymentMethodInput.value = this.dataset.value;
                options.classList.add('hidden');

                if (this.dataset.value === 'cryptocurrency') showCryptoWalletSelection();
                else if (this.dataset.value === 'digital_wallet') showBankSelection();
                else {
                    walletInfo.classList.add('hidden');
                    walletInfo.innerHTML = '';
                }

                // Recalculate fees when method changes
                recalculateFees();
            });
        });

        // ── Crypto wallet selection ────────────────────────────────────
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
                    ${cryptoWallets.bitcoin  ? `<div class="wallet-item px-4 py-3 cursor-pointer hover:bg-gray-100" data-wallet="bitcoin">🟢 BTC — ${cryptoWallets.bitcoin}</div>`   : ''}
                    ${cryptoWallets.etherium ? `<div class="wallet-item px-4 py-3 cursor-pointer hover:bg-gray-100" data-wallet="etherium">🟢 ETH — ${cryptoWallets.etherium}</div>` : ''}
                    ${cryptoWallets.usdt     ? `<div class="wallet-item px-4 py-3 cursor-pointer hover:bg-gray-100" data-wallet="usdt">🟢 USDT — ${cryptoWallets.usdt}</div>`         : ''}
                    ${!hasCryptoWallet ? `<div class="p-4 text-sm text-yellow-800 bg-yellow-50">No wallets added. <a href="${profileUrl}" class="underline">Go to profile →</a></div>` : ''}
                </div>`;
            setupWalletDropdownEvents();
        }

        // ── Bank selection ─────────────────────────────────────────────
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
                            ${bankDetails.iban         ? `<div class="flex justify-between"><span class="text-gray-500">IBAN:</span><span class="font-mono text-xs">${bankDetails.iban}</span></div>`          : ''}
                            ${bankDetails.swiftBic     ? `<div class="flex justify-between"><span class="text-gray-500">SWIFT:</span><span class="font-mono text-xs">${bankDetails.swiftBic}</span></div>`     : ''}
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

        // ── Wallet dropdown events ─────────────────────────────────────
        function setupWalletDropdownEvents() {
            const wd = document.getElementById('wallet-dropdown');
            const wo = document.getElementById('wallet-options');
            const wt = document.getElementById('wallet-text');
            const wc = document.getElementById('wallet_choice_input');
            if (!wd || !wo) return;
            wd.addEventListener('click', e => {
                wo.classList.toggle('hidden');
                e.stopPropagation();
            });
            document.addEventListener('click', e => {
                if (!wd.contains(e.target) && !wo.contains(e.target)) wo.classList.add('hidden');
            });
            document.querySelectorAll('.wallet-item').forEach(item => {
                item.addEventListener('click', function() {
                    wt.textContent = this.textContent.trim();
                    wc.value = this.dataset.wallet;
                    wo.classList.add('hidden');
                });
            });
        }

        // ── Bank dropdown events ───────────────────────────────────────
        function setupBankDropdownEvents() {
            const bd = document.getElementById('bank-dropdown');
            const bo = document.getElementById('bank-options');
            const bt = document.getElementById('bank-text');
            const wc = document.getElementById('wallet_choice_input');
            if (!bd || !bo) return;
            bd.addEventListener('click', e => {
                bo.classList.toggle('hidden');
                e.stopPropagation();
            });
            document.addEventListener('click', e => {
                if (!bd.contains(e.target) && !bo.contains(e.target)) bo.classList.add('hidden');
            });
            document.querySelectorAll('.bank-item').forEach(item => {
                item.addEventListener('click', function() {
                    bt.textContent = `${bankDetails.bankName} — ${bankDetails.accountNumber || bankDetails.iban}`;
                    bt.classList.replace('text-gray-600', 'text-gray-800');
                    wc.value = this.dataset.bank;
                    bo.classList.add('hidden');
                });
            });
        }

        // ── No payment method nudge ────────────────────────────────────
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
            setTimeout(() => {
                el.classList.replace('opacity-0', 'opacity-100');
                el.classList.replace('translate-y-3', 'translate-y-0');
            }, 50);
            document.addEventListener('click', function h(e) {
                if (!dropdown.contains(e.target) && !el.contains(e.target)) {
                    el.classList.replace('opacity-100', 'opacity-0');
                    setTimeout(() => el.remove(), 400);
                    document.removeEventListener('click', h);
                }
            });
        }

        // ── PIN auto-jump ──────────────────────────────────────────────
        const pinInputs = document.querySelectorAll('.pin-input');
        pinInputs.forEach((input, idx) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && idx < pinInputs.length - 1)
                    pinInputs[idx + 1].focus();
            });
            input.addEventListener('keydown', e => {
                if (e.key === 'Backspace' && input.value === '' && idx > 0)
                    pinInputs[idx - 1].focus();
            });
        });
        pinInputs[0]?.addEventListener('paste', e => {
            e.preventDefault();
            const digits = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/\D/g, '').split('').slice(0, 4);
            digits.forEach((d, i) => {
                pinInputs[i].value = d;
            });
            pinInputs[Math.min(digits.length, 3)].focus();
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
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
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