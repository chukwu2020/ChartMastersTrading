@extends('layout.user')

@section('content')

@php
    /**
     * Get the correct crypto ticker/logo for any crypto name
     * Handles: "USDT ERC20", "USDT TRC20", "Bitcoin", "Ethereum", etc.
     */
    function getCryptoTicker($cryptoName) {
        $name = strtolower(trim($cryptoName));
        
        $exactMap = [
            'bitcoin' => 'btc',
            'ethereum' => 'eth',
            'etherium' => 'eth',
            'etherum' => 'eth',
            'tether' => 'usdt',
            'dogecoin' => 'doge',
            'dodge' => 'doge',
            'doge' => 'doge',
            'matic' => 'matic',
            'polygon' => 'matic',
            'solana' => 'sol',
            'ripple' => 'xrp',
            'cardano' => 'ada',
            'binance coin' => 'bnb',
            'binance' => 'bnb',
            'litecoin' => 'ltc',
            'tron' => 'trx',
            'avalanche' => 'avax',
            'chainlink' => 'link',
            'uniswap' => 'uni',
            'cosmos' => 'atom',
            'stellar' => 'xlm',
            'algorand' => 'algo',
            'vechain' => 'vet',
            'internet computer' => 'icp',
            'filecoin' => 'fil',
            'elrond' => 'egld',
            'theta' => 'theta',
            'tezos' => 'xtz',
            'eos' => 'eos',
            'pancakeswap' => 'cake',
            'aave' => 'aave',
            'the graph' => 'grt',
            'maker' => 'mkr',
            'compound' => 'comp',
            'synthetix' => 'snx',
            'curve' => 'crv',
            'yearn finance' => 'yfi',
            'basic attention token' => 'bat',
            'zcash' => 'zec',
            'dash' => 'dash',
            'monero' => 'xmr',
            'neo' => 'neo',
            'waves' => 'waves',
            'hedera' => 'hbar',
            'near' => 'near',
            'fantom' => 'ftm',
            'harmony' => 'one',
            'usd coin' => 'usdc',
            'binance usd' => 'busd',
            'shiba inu' => 'shib',
            'aptos' => 'apt',
            'arbitrum' => 'arb',
            'optimism' => 'op',
            'sui' => 'sui',
        ];
        
        if (isset($exactMap[$name])) {
            return $exactMap[$name];
        }
        
        // Handle USDT variants (ERC20, TRC20, BEP20, etc.)
        if (strpos($name, 'usdt') !== false) {
            return 'usdt';
        }
        
        // Handle USDC variants
        if (strpos($name, 'usdc') !== false) {
            return 'usdc';
        }
        
        // Handle BUSD variants
        if (strpos($name, 'busd') !== false) {
            return 'busd';
        }
        
        $firstWord = explode(' ', $name)[0];
        if (isset($exactMap[$firstWord])) {
            return $exactMap[$firstWord];
        }
        
        foreach ($exactMap as $key => $ticker) {
            if (strpos($name, $key) !== false) {
                return $ticker;
            }
        }
        
        return $name;
    }

    $supportedCoins = [
        'btc', 'eth', 'usdt', 'bnb', 'sol', 'xrp', 'ada', 'doge', 'ltc', 'trx',
        'matic', 'link', 'dot', 'avax', 'uni', 'atom', 'xlm', 'algo', 'vet', 'icp',
        'fil', 'egld', 'theta', 'xtz', 'eos', 'cake', 'aave', 'grt', 'mkr', 'comp',
        'snx', 'crv', 'yfi', 'bat', 'zec', 'dash', 'xmr', 'neo', 'waves', 'hbar',
        'near', 'ftm', 'one', 'usdc', 'busd', 'shib', 'apt', 'arb', 'op', 'sui'
    ];
    
    $cdnBase = 'https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/128/color/';
    
    function getCryptoLogo($cryptoName, $supportedCoins, $cdnBase) {
        $ticker = getCryptoTicker($cryptoName);
        if (in_array($ticker, $supportedCoins)) {
            return $cdnBase . $ticker . '.png';
        }
        return $cdnBase . 'generic.png';
    }
@endphp

<style>
    .crypto-icon { width: 24px; height: 24px; margin-right: 8px; }
    .wallet-card {
        border-left: 4px solid #9EDD05;
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    .time-badge {
        background-color: #f0f7ed;
        color: #0C3A30;
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 12px;
    }
    .partner-logo { width: 40px; height: 40px; object-fit: contain; margin-right: 12px; }
    .crypto-logo {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        flex-shrink: 0;
    }
    .crypto-logo img { width: 38px; height: 38px; object-fit: contain; }
    .partner-card {
        transition: all 0.3s;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        overflow: hidden;
    }
    .partner-card:hover {
        border-color: #9EDD05;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffecb5;
        color: #856404;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    .timer-display {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        color: #dc3545;
    }
    .deposit-summary {
        background: linear-gradient(135deg, #f8faf7 0%, #eef7ea 100%);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .currency-selector {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        color: #0C3A30;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .currency-selector:hover { border-color: #9EDD05; }
    .currency-display {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background: #f8faf7;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    .currency-symbol { font-weight: 700; color: #0C3A30; }
    .amount-in-currency { font-size: 1.1rem; font-weight: 600; color: #059669; }
    .wallet-address-container {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px;
        position: relative;
    }
    .back-btn {
        padding: 0.75rem 2rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.5rem;
        font-weight: 600;
        color: #4b5563;
        background: white;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .back-btn:hover {
        border-color: #9EDD05;
        background-color: #f9fafb;
        color: #0C3A30;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(158, 221, 5, 0.2);
    }
    .submit-btn {
        padding: 0.75rem 2rem;
        background: linear-gradient(135deg, #9EDD05, #8AC304);
        border: 2px solid #9EDD05;
        border-radius: 0.5rem;
        font-weight: 600;
        color: #0C3A30;
        transition: all 0.3s ease;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(158, 221, 5, 0.2);
    }
    .submit-btn:hover:not(:disabled) {
        background: linear-gradient(135deg, #8AC304, #7AB503);
        border-color: #8AC304;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(158, 221, 5, 0.3);
    }
    .submit-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #e5e7eb;
        border-color: #d1d5db;
        color: #9ca3af;
        box-shadow: none;
    }
    .submit-btn.submitting {
        background: #ffcc00;
        border-color: #ffcc00;
        color: #0C3A30;
        cursor: wait;
        position: relative;
        padding-left: 2.5rem;
    }
    .submit-btn.submitting::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        width: 1rem;
        height: 1rem;
        border: 2px solid #0C3A30;
        border-top-color: transparent;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }
    .nft-promo-card {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    .nft-promo-card__inner {
        position: relative;
        z-index: 2;
        background: white;
        border-radius: 12px;
        padding: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .upload-area {
        transition: all 0.3s ease;
    }
    .upload-area:hover {
        border-color: #9EDD05;
        background-color: #f8faf7;
    }
    .upload-info { padding: 12px; border-radius: 10px; text-align: center; }
    .upload-text { background: #9EDD05; font-size: 16px; color: #6b7280; }
    .upload-subtext { font-size: 12px; color: #9ca3af; margin-top: 4px; }

    @media (max-width: 640px) {
        .back-btn, .submit-btn { padding: 0.75rem 1.5rem; font-size: 0.875rem; }
        .flex.justify-center.gap-4 { flex-direction: column; gap: 0.75rem; }
        .back-btn, .submit-btn { width: 100%; text-align: center; }
    }
</style>

<div class="dashboard-main-body">

    <!-- Breadcrumb -->
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6 mt-3">
        <h5 class="font-semibold mb-0" style="color: #0C3A30;">Confirm Deposit</h5>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('user_dashboard') }}"
                    class="flex items-center gap-2 hover:text-primary-600"
                    onmouseover="this.style.color='#9EDD05';"
                    onmouseout="this.style.color='#0C3A30';">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="font-medium">Deposit</li>
        </ul>
    </div>

    <!-- Currency Selector -->
    <div class="flex justify-end mb-4">
        <select id="currencySelector" class="currency-selector">
            <option value="USD" selected>💵 USD</option>
            <option value="EUR">🇪🇺 EUR</option>
            <option value="GBP">🇬🇧 GBP</option>
            <option value="NGN">🇳🇬 NGN</option>
            <option value="AUD">🇦🇺 AUD</option>
            <option value="CAD">🇨🇦 CAD</option>
            <option value="JPY">🇯🇵 JPY</option>
            <option value="CNY">🇨🇳 CNY</option>
            <option value="INR">🇮🇳 INR</option>
            <option value="BRL">🇧🇷 BRL</option>
            <option value="ZAR">🇿🇦 ZAR</option>
        </select>
    </div>

    <!-- Status Messages -->
    <div id="statusMessages"></div>

    @if(!Session::has('deposit_details'))
        <div class="alert-danger">
            <iconify-icon icon="solar:danger-triangle-outline" class="mr-2"></iconify-icon>
            No deposit session found. Please start a new deposit.
        </div>
        <a href="{{ route('user.deposit') }}" class="btn btn-primary">
            <iconify-icon icon="solar:arrow-left-linear" class="mr-2"></iconify-icon>
            Start New Deposit
        </a>
    @else
        @php
            $depositDetails = Session::get('deposit_details');
            $wallet = \App\Models\Wallet::find($depositDetails['wallet_id'] ?? null);
            $amount = $depositDetails['amount_deposited'] ?? 0;

            if (!$wallet) {
                echo '<div class="alert-danger"><iconify-icon icon="solar:danger-triangle-outline" class="mr-2"></iconify-icon>Invalid deposit details. Please start again.</div>';
                echo '<a href="'.route('user.deposit').'" class="btn btn-primary"><iconify-icon icon="solar:arrow-left-linear" class="mr-2"></iconify-icon>Start New Deposit</a>';
                return;
            }

            // Get the crypto name and logo using our helper functions
            $cryptoName = $wallet->crypto_name;
            $ticker = getCryptoTicker($cryptoName);
            $logoUrl = in_array($ticker, $supportedCoins) 
                ? $cdnBase . $ticker . '.png' 
                : $cdnBase . 'generic.png';
        @endphp

        <!-- Session Expiry Warning -->
        <div class="alert-warning mb-4">
            <div class="flex items-center">
                <iconify-icon icon="solar:clock-circle-outline" class="mr-2"></iconify-icon>
                <span>Session expires in <span id="sessionTimer" class="timer-display">30:00</span></span>
            </div>
        </div>

        <!-- Deposit Summary -->
        <div class="deposit-summary">
            <h6 class="text-lg font-bold mb-3" style="color: #0C3A30;">Deposit Summary</h6>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Amount (USD)</p>
                    <p class="text-2xl font-bold" style="color: #0C3A30;">${{ number_format($amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Amount in Selected Currency</p>
                    <div class="currency-display">
                        <span id="currencySymbolDisplay" class="currency-symbol">$</span>
                        <span id="amountInCurrency" class="amount-in-currency">{{ number_format($amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="nft-promo-card">
            <div class="nft-promo-card__inner p-4">
                <div class="card rounded-xl p-6 shadow-sm wallet-card"
                    style="background-image: url({{ asset('assets/images/hero/hero-image-1.svg') }}); background-repeat: no-repeat; background-size: cover; background-position: center;">

                    <div class="flex justify-between items-center mb-4">
                        <h6 class="text-lg font-bold" style="color: #0C3A30;">
                            <iconify-icon icon="solar:wallet-outline" class="mr-2"></iconify-icon>
                            Payment Information
                        </h6>
                        <span class="time-badge flex items-center gap-1">
                            <iconify-icon icon="solar:clock-circle-outline"></iconify-icon>
                            <span id="paymentTimer" class="timer-display">30:00</span>
                        </span>
                    </div>

                    <div class="flex flex-col gap-6">
                        <div class="flex-1">

                            <!-- Crypto Identity Row with CORRECT LOGO -->
                            <div class="flex items-center gap-3 mb-4">
                                <div class="crypto-logo">
                                    <img
                                        src="{{ $logoUrl }}"
                                        alt="{{ $cryptoName }} logo"
                                        onerror="this.src='{{ $cdnBase }}generic.png'"
                                    >
                                </div>
                                <div>
                                    <h4 class="font-bold text-lg">{{ $cryptoName }}</h4>
                                    <p class="text-sm text-gray-500">Send only {{ $cryptoName }} to this address</p>
                                </div>
                            </div>

                            <!-- Wallet Address -->
                            <div class="wallet-address-container mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Wallet Address</label>
                                <div class="flex flex-wrap items-center gap-2 bg-gray-50 p-3 rounded-md border border-gray-200">
                                    <span class="font-mono text-sm text-gray-800 break-all flex-1">
                                        {{ $wallet->wallet_address }}
                                    </span>
                                    <button
                                        class="copy-btn flex items-center gap-1 text-sm text-gray-700 bg-white border border-gray-300 px-3 py-1.5 rounded-md hover:bg-gray-100 active:scale-95 transition"
                                        onclick="copyToClipboard('{{ $wallet->wallet_address }}', this)"
                                        type="button">
                                        <iconify-icon icon="solar:copy-linear" class="text-base"></iconify-icon>
                                        <span class="copy-text">Copy</span>
                                    </button>
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 mb-4">You can deposit crypto from our trusted partners:</p>

                            <!-- Partner Cards -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <a href="https://www.bybit.com/" target="_blank" rel="noopener noreferrer" class="partner-card p-3">
                                    <div class="flex items-center">
                                        <img id="bybitLogo" src="" alt="Bybit" class="partner-logo">
                                        <div>
                                            <span class="font-medium text-gray-800">Bybit</span>
                                            <p class="text-xs text-gray-500">Professional Trading</p>
                                        </div>
                                        <iconify-icon icon="solar:arrow-right-linear" class="text-gray-400 ml-auto"></iconify-icon>
                                    </div>
                                </a>
                                <a href="https://trustwallet.com/" target="_blank" rel="noopener noreferrer" class="partner-card p-3">
                                    <div class="flex items-center">
                                        <img id="trustWalletLogo" src="" alt="Trust Wallet" class="partner-logo">
                                        <div>
                                            <span class="font-medium text-gray-800">Trust Wallet</span>
                                            <p class="text-xs text-gray-500">Mobile Wallet</p>
                                        </div>
                                        <iconify-icon icon="solar:arrow-right-linear" class="text-gray-400 ml-auto"></iconify-icon>
                                    </div>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Confirmation -->
        <div class="mt-6">
            <div class="card rounded-xl p-6 shadow-sm wallet-card"
                style="background-image: url({{ asset('assets/images/hero/hero-image-1.svg') }}); background-repeat: no-repeat; background-size: cover; background-position: center;">

                <h6 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                    <iconify-icon icon="solar:upload-outline" class="mr-2 text-green-500"></iconify-icon>
                    Payment Confirmation
                </h6>

                <form id="depositForm" action="{{ route('deposit.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-5">
                        <label for="proof" class="inline-block font-semibold text-neutral-600 text-sm mb-2">
                            Upload Transaction Screenshot
                            <span class="text-danger-600">*</span>
                        </label>
                        <div class="upload-area p-6 text-center border-2 border-dashed border-gray-300 rounded-lg hover:border-[#9EDD05] transition cursor-pointer"
                            onclick="document.getElementById('proof').click()">
                            <iconify-icon icon="solar:gallery-upload-outline" class="text-3xl text-gray-400 mb-2"></iconify-icon>
                            <div class="upload-info">
                                <p class="upload-text">Click to upload or drag and drop</p>
                                <p class="upload-subtext">PNG, JPG, WEBP up to 10MB</p>
                            </div>
                        </div>
                        <input type="file" name="proof" class="hidden" id="proof"
                            accept="image/*" onchange="handleFileSelect(this)" />
                        <span class="text-danger">@error('proof'){{ $message }}@enderror</span>
                    </div>

                    <div class="flex justify-center gap-4">
                        <a href="{{ route('user.deposit') }}" class="back-btn">Back</a>
                        <button id="submitBtn" type="submit" class="submit-btn" disabled>
                            Submit Deposit
                        </button>
                    </div>

                </form>
            </div>
        </div>

    @endif
</div>

<script>
    const CONFIG = {
        amountUSD: {{ $amount ?? 0 }},
        sessionDuration: 30 * 60,
    };

    const fiatRates = {};
    let currentCurrency = 'USD';

    document.addEventListener('DOMContentLoaded', function () {
        initializeTimers();
        loadPartnerLogos();
        fetchFiatRates();
        setupFormSubmission();
    });

    // FIAT CONVERSION
    async function fetchFiatRates() {
        try {
            const response = await fetch('https://v6.exchangerate-api.com/v6/a8e67b756f551b68d4ada293/latest/USD');
            const data = await response.json();
            if (data.result === 'success') {
                Object.assign(fiatRates, data.conversion_rates);
                updateCurrencyDisplay();
            }
        } catch (error) {
            Object.assign(fiatRates, {
                USD: 1, EUR: 0.92, GBP: 0.79, NGN: 1500, AUD: 1.52,
                CAD: 1.36, JPY: 149, CNY: 7.24, INR: 83, BRL: 4.97, ZAR: 18.5
            });
            updateCurrencyDisplay();
        }
    }

    document.getElementById('currencySelector')?.addEventListener('change', function () {
        currentCurrency = this.value;
        updateCurrencyDisplay();
    });

    function getCurrencySymbol(currency) {
        return { USD: '$', EUR: '€', GBP: '£', NGN: '₦', AUD: 'A$', CAD: 'C$', JPY: '¥', CNY: '¥', INR: '₹', BRL: 'R$', ZAR: 'R' }[currency] || '$';
    }

    function updateCurrencyDisplay() {
        const rate = fiatRates[currentCurrency] || 1;
        const symbol = getCurrencySymbol(currentCurrency);
        document.getElementById('currencySymbolDisplay').textContent = symbol;
        document.getElementById('amountInCurrency').textContent = (CONFIG.amountUSD * rate).toFixed(2);
    }

    // FILE UPLOAD
    function handleFileSelect(input) {
        const file = input.files[0];
        const submitBtn = document.getElementById('submitBtn');

        if (file && validateFile(file)) {
            submitBtn.disabled = false;
            const uploadArea = document.querySelector('.upload-area');
            uploadArea.innerHTML = `
                <iconify-icon icon="solar:check-circle-bold" class="text-3xl text-green-500 mb-2"></iconify-icon>
                <p class="text-sm text-gray-700">${file.name}</p>
                <p class="text-xs text-gray-400 mt-1">Click to change</p>
            `;
        } else {
            submitBtn.disabled = true;
        }
    }

    function validateFile(file) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showMessage('Invalid file type. Please upload PNG, JPG, or WEBP files only.', 'error');
            return false;
        }
        if (file.size > 10 * 1024 * 1024) {
            showMessage('File too large. Maximum size is 10MB.', 'error');
            return false;
        }
        return true;
    }

    function showMessage(message, type = 'info') {
        const statusDiv = document.getElementById('statusMessages');
        if (!statusDiv) return;
        const cls = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'check-circle' : 'danger-triangle';
        statusDiv.innerHTML = `
            <div class="${cls} p-3 rounded-lg mb-4 flex items-center">
                <iconify-icon icon="solar:${icon}-outline" class="mr-2"></iconify-icon>
                ${message}
            </div>
        `;
        setTimeout(() => { statusDiv.innerHTML = ''; }, 5000);
    }

    // COPY ADDRESS
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const span = btn.querySelector('.copy-text');
            span.textContent = 'Copied!';
            btn.style.backgroundColor = '#28a745';
            btn.style.color = 'white';
            setTimeout(() => {
                span.textContent = 'Copy';
                btn.style.backgroundColor = '';
                btn.style.color = '';
            }, 2000);
        }).catch(() => showMessage('Failed to copy address', 'error'));
    }

    // TIMER
    function initializeTimers() {
        let timeLeft = CONFIG.sessionDuration;

        function tick() {
            const m = Math.floor(timeLeft / 60);
            const s = timeLeft % 60;
            const str = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            ['sessionTimer', 'paymentTimer'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.textContent = str;
            });
            if (timeLeft <= 0) {
                document.getElementById('submitBtn').disabled = true;
                document.querySelector('.upload-area').innerHTML = `
                    <iconify-icon icon="solar:clock-circle-outline" class="text-3xl text-red-500 mb-2"></iconify-icon>
                    <p class="text-sm text-gray-500">Session expired. Please start again.</p>
                `;
                return;
            }
            timeLeft--;
        }

        tick();
        setInterval(tick, 1000);
    }

    // PARTNER LOGOS
    function loadPartnerLogos() {
        const bybitLogo = "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iOCIgZmlsbD0iI0Y3OTMxRSIvPgo8dGV4dCB4PSIyMCIgeT0iMjYiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGZpbGw9IndoaXRlIiBmb250LXNpemU9IjEwIiBmb250LXdlaWdodD0iYm9sZCI+Qnl8Qml0PC90ZXh0Pgo8L3N2Zz4K";
        const trustWalletLogo = "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iMjAiIGZpbGw9IiMzMzc1QkIiLz4KPHR5cGUgdGV4dD0iVFciIHg9IjIwIiB5PSIyNiIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0id2hpdGUiIGZvbnQtc2l6ZT0iMTIiIGZvbnQtd2VpZ2h0PSJib2xkIi8+Cjwvc3ZnPgo=";
        const bybitEl = document.getElementById('bybitLogo');
        const trustEl = document.getElementById('trustWalletLogo');
        if (bybitEl) bybitEl.src = bybitLogo;
        if (trustEl) trustEl.src = trustWalletLogo;
    }

    // FORM SUBMISSION
    function setupFormSubmission() {
        const form = document.getElementById('depositForm');
        const submitBtn = document.getElementById('submitBtn');
        form.addEventListener('submit', function (e) {
            if (!document.getElementById('proof').files.length) {
                e.preventDefault();
                showMessage('Please upload payment proof screenshot', 'error');
                return false;
            }
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Processing...';
        });
    }
</script>

@endsection