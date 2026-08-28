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

<!-- Reinvestment Mode Alert -->
@if(session('reinvestment_mode') && session('reinvestment_expires') > now())
<div class="alert alert-warning mb-4 px-6" style="background-color:#9EDD05 !important;">
    <div class="flex items-center">
        <iconify-icon icon="solar:refresh-circle-outline" class="mr-2"></iconify-icon>
        <span>Reinvestment mode. Available balance:
            <strong><span id="availableBalanceDisplay">${{ number_format(auth()->user()->available_balance, 2) }}</span></strong>
        </span>
        <button onclick="location.href='{{ route('user_dashboard') }}'"
            class="ml-4 text-sm underline font-semibold hover:text-red-600"
            style="color:#000;">Cancel</button>
    </div>
</div>
@endif

<style>
    :root {
        --pg: #9EDD05;
        --dg: #0C3A30;
        --ag: #8AC304;
    }

    * { box-sizing: border-box; }
    .dashboard-main-body { max-width: 100%; overflow-x: hidden; }

    .method-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }
    @media(max-width:640px) { .method-grid { grid-template-columns: 1fr; } }

    .method-card {
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.5rem 1rem;
        cursor: pointer;
        transition: all .25s ease;
        background: white;
        text-align: center;
        position: relative;
        overflow: hidden;
        user-select: none;
    }
    .method-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--pg), var(--ag));
        transform: scaleX(0);
        transition: transform .25s ease;
        transform-origin: left;
    }
    .method-card:hover {
        border-color: var(--pg);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(158, 221, 5, .15);
    }
    .method-card:hover::after { transform: scaleX(1); }
    .method-card.selected {
        border-color: var(--pg);
        background: linear-gradient(135deg, #f9fffe, #f0f7ed);
        box-shadow: 0 6px 16px rgba(158, 221, 5, .2);
    }
    .method-card.selected::after { transform: scaleX(1); }

    .mc-icon {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        margin: 0 auto .75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.7rem;
        transition: transform .2s ease;
    }
    .method-card:hover .mc-icon { transform: scale(1.1); }
    .mc-name { font-weight: 700; font-size: .9rem; color: var(--dg); margin-bottom: .2rem; }
    .mc-desc { font-size: .72rem; color: #9ca3af; }
    .mc-badge {
        position: absolute;
        top: .5rem;
        right: .5rem;
        font-size: .58rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }
    .badge-live { background: #dcfce7; color: #15803d; }
    .mc-check {
        position: absolute;
        top: .5rem;
        left: .5rem;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--pg);
        color: var(--dg);
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: 700;
    }
    .method-card.selected .mc-check { display: flex; animation: cpop .3s ease; }
    @keyframes cpop {
        0% { transform: scale(0); }
        60% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }

    .pay-panel { display: none; }
    .pay-panel.active { display: block; animation: fadein .3s ease; }
    @keyframes fadein {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .panel-card {
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 2rem;
        background: white;
        background-image: url('{{ asset('assets/images/hero/hero-image-1.svg') }}');
        background-size: cover;
        background-position: center;
        margin-bottom: 1.5rem;
    }

    .gen-section {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .gen-btn {
        background: linear-gradient(135deg, var(--pg), var(--ag));
        color: var(--dg);
        font-weight: 700;
        padding: .9rem 2.5rem;
        border-radius: 50px;
        border: none;
        cursor: pointer;
        font-size: .95rem;
        box-shadow: 0 4px 15px rgba(158, 221, 5, .3);
        transition: all .25s ease;
    }
    .gen-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(158, 221, 5, .4); }
    .gen-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; }

    .gen-log {
        padding: 1rem 1.25rem;
        margin-top: 1.25rem;
        text-align: left;
        max-height: 190px;
        overflow-y: auto;
        background: #ffffff !important;
        border: 1px solid #e5e7eb !important;
    }
    .log-line {
        padding: .15rem 0;
        font-size: .78rem;
        color: #0C3A30 !important;
        font-family: monospace;
    }

    .wallet-opt {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        cursor: pointer;
        transition: all .2s ease;
        background: white;
        position: relative;
        display: block;
    }
    .wallet-opt:hover {
        border-color: var(--pg);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(158, 221, 5, .1);
    }
    .wallet-opt.selected {
        border-color: var(--pg);
        background: linear-gradient(135deg, #fff, #f0f7ed);
        box-shadow: 0 4px 12px rgba(158, 221, 5, .2);
    }
    .wallet-opt input[type="radio"] { display: none; }
    .wcheck {
        position: absolute;
        top: .5rem;
        right: .5rem;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: var(--pg);
        color: white;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }
    .wallet-opt.selected .wcheck { display: flex; animation: cpop .3s ease; }

    .clogo {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    .clogo img { width: 30px; height: 30px; object-fit: contain; }

    .gc-type {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: .85rem .5rem;
        cursor: pointer;
        transition: all .2s ease;
        background: white;
        text-align: center;
        position: relative;
    }
    .gc-type:hover { border-color: var(--pg); }
    .gc-type.selected { border-color: var(--pg); background: #f0f7ed; }
    .gc-type input[type="radio"] { display: none; }
    .gc-type .gc-check {
        position: absolute;
        top: .35rem;
        right: .35rem;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--pg);
        color: var(--dg);
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 700;
    }
    .gc-type.selected .gc-check { display: flex; animation: cpop .3s ease; }

    .upload-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: all .25s ease;
        cursor: pointer;
        background: #fafafa;
    }
    .upload-zone:hover {
        border-color: var(--pg);
        background: #f8faf7;
    }
    .upload-zone.has-file {
        border-color: var(--pg);
        background: #f0fdf4;
        border-style: solid;
    }

    .f-input {
        width: 100%;
        padding: .75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: .875rem;
        color: #111827;
        background: white;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .f-input:focus {
        border-color: var(--pg);
        box-shadow: 0 0 0 3px rgba(158, 221, 5, .15);
    }

    .f-label {
        display: block;
        font-size: .78rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: .35rem;
    }

    .preset-btn {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: .4rem .9rem;
        font-weight: 600;
        font-size: .82rem;
        color: var(--dg);
        cursor: pointer;
        transition: all .2s ease;
    }
    .preset-btn:hover {
        border-color: var(--pg);
        background: #f8faf7;
    }
    .preset-btn.active {
        background: var(--pg);
        border-color: var(--pg);
        color: var(--dg);
    }

    .btn-continue {
        background: var(--pg);
        color: var(--dg);
        font-weight: 700;
        padding: .85rem 2.5rem;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-size: .95rem;
        box-shadow: 0 4px 12px rgba(158, 221, 5, .3);
        transition: all .25s ease;
        position: relative;
    }
    .btn-continue:hover:not(:disabled) {
        background: var(--ag);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(158, 221, 5, .4);
    }
    .btn-continue:disabled {
        opacity: .45;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .btn-reset {
        padding: .85rem 2rem;
        border: 2px solid #fca5a5;
        color: #ef4444;
        border-radius: 10px;
        font-weight: 600;
        background: white;
        cursor: pointer;
        transition: all .2s ease;
    }
    .btn-reset:hover { background: #fef2f2; }

    .info-box {
        background: linear-gradient(135deg, #f8faf7, #eef7ea);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        border-left: 4px solid var(--pg);
    }

    .steps {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.75rem;
        flex-wrap: nowrap;
    }
    .step { display: flex; align-items: center; gap: 2.5rem; }
    .snum {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #e5e7eb;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .8rem;
        transition: all .25s;
    }
    .step.active .snum {
        background: var(--pg);
        color: var(--dg);
    }
    .sline {
        width: 40px;
        height: 2px;
        background: #e5e7eb;
    }

    .toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #0d1117;
        color: #ffffff !important;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: .875rem;
        z-index: 9999;
        max-width: 380px;
        line-height: 1.5;
        border-left: 4px solid var(--pg);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .35);
        animation: slideIn .3s ease-out;
    }
    .toast iconify-icon { color: #ffffff !important; }
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(80px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .gmodal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .8);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .gmodal.active { display: flex; }
    .gmodal-body {
        background: white;
        border-radius: 16px;
        max-width: 480px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        padding: 2rem;
    }
    .gstep {
        display: flex;
        gap: 1rem;
        margin-bottom: .65rem;
        padding: .85rem 1rem;
        background: #f9fafb;
        border-radius: 10px;
    }
    .gsnum {
        width: 28px;
        height: 28px;
        background: var(--pg);
        color: var(--dg);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: .8rem;
        flex-shrink: 0;
    }

    .selected-gc-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 20px;
        background: #f0f7ed;
        border: 1px solid var(--pg);
        font-size: .8rem;
        font-weight: 600;
        color: var(--dg);
        margin-bottom: 1rem;
    }

    .glow-button {
        background: linear-gradient(135deg, var(--pg), var(--ag));
        color: var(--dg);
        padding: 1rem 2.5rem;
        border-radius: 12px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(158, 221, 5, 0.3);
        position: relative;
        overflow: hidden;
        font-size: 1rem;
    }
    .glow-button::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 60%);
        animation: shimmer 3s infinite;
        transform: rotate(30deg);
    }
    @keyframes shimmer {
        0% { transform: rotate(30deg) translateX(-100%); }
        100% { transform: rotate(30deg) translateX(100%); }
    }
    .glow-button:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(158, 221, 5, 0.4);
    }
    .glow-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top-color: var(--dg);
        animation: spin 0.8s ease-in-out infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .country-select {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        width: 100%;
        font-size: 0.95rem;
        color: var(--dg);
        transition: all 0.2s ease;
    }
    .country-select:focus {
        border-color: var(--pg);
        box-shadow: 0 0 0 3px rgba(158, 221, 5, 0.15);
        outline: none;
    }

    .gradient-border {
        position: relative;
        border-radius: 16px;
        padding: 2px;
        background: linear-gradient(135deg, var(--pg), #8b5cf6, var(--pg));
        background-size: 300% 300%;
        animation: gradientMove 4s ease-in-out infinite;
    }
    @keyframes gradientMove {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .gradient-border-inner {
        background: white;
        border-radius: 14px;
        padding: 1.5rem;
    }

    .bank-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.75rem;
        margin: 1rem 0;
    }
    @media (max-width: 640px) { .bank-details-grid { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 400px) { .bank-details-grid { grid-template-columns: 1fr; } }

    .bank-detail-item {
        background: rgba(12, 58, 48, 0.05);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid rgba(12, 58, 48, 0.1);
        backdrop-filter: blur(10px);
    }
    .bank-detail-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.7;
        color: #0C3A30;
        margin-bottom: 0.25rem;
    }
    .bank-detail-value {
        font-size: 1.1rem;
        font-weight: 600;
        font-family: 'Courier New', monospace;
        color: #0C3A30;
    }
    .reference-code-highlight {
        background: rgba(158, 221, 5, 0.15);
        padding: 0.15rem 0.6rem;
        border-radius: 6px;
        border: 1px solid var(--pg);
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        font-weight: 600;
        color: #0C3A30;
        display: inline-block;
        letter-spacing: 1px;
        max-width: 100%;
        word-break: break-all;
        word-wrap: break-word;
        white-space: normal;
        line-height: 1.5;
    }

    .bank-detail-item .copy-btn-sm {
        font-size: 0.65rem;
        padding: 0.1rem 0.4rem;
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 4px;
        color: #0C3A30;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-left: 0.25rem;
    }
    .bank-detail-item .copy-btn-sm:hover { background: rgba(255, 255, 255, 0.3); }

    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] { -moz-appearance: textfield; }
</style>

<div class="dashboard-main-body">

    <!-- Breadcrumb -->
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <h5 class="font-semibold mb-0" style="color:#0C3A30;">Deposit Funds</h5>
        <ul class="flex items-center gap-[6px]">
            <li>
                <a href="{{ route('user_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color:#0C3A30;">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="text-lg"></iconify-icon> Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="font-medium" style="color:#9EDD05;">Deposit</li>
        </ul>
    </div>

    <!-- Step Indicator -->
    <div class="steps">
        <div class="step active" id="s1">
            <div class="snum">1</div><span class="text-sm font-medium" style="color:#0C3A30;">Choose Method</span>
        </div>
        <div class="sline"></div>
        <div class="step" id="s2">
            <div class="snum">2</div><span class="text-sm text-gray-400">Fill Details</span>
        </div>
        <div class="sline"></div>
        <div class="step" id="s3">
            <div class="snum">3</div><span class="text-sm text-gray-400">Confirm</span>
        </div>
    </div>

    <!-- Header row -->
    <div class="flex items-center justify-between gap-4 mb-5">
        <p class="text-sm text-gray-800">Select funding Method</p>
        <button type="button" onclick="openGuide()" class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-semibold">
            <iconify-icon icon="ph:question-fill" class="text-base"></iconify-icon> How it works
        </button>
    </div>

    <!-- ══ STEP 1 — CHOOSE METHOD ══ -->
    <div class="method-grid">
        <div class="method-card" id="mc-crypto" onclick="chooseMethod('crypto')">
            <div class="mc-check">✓</div>
            <span class="mc-badge badge-live">Live</span>
            <div class="mc-icon" style="background:linear-gradient(135deg,#f0f7ed,#dcfce7);">
                <iconify-icon icon="ph:stack-bold" style="color:#0C3A30;"></iconify-icon>
            </div>
            <div class="mc-name">Cryptocurrency</div>
            <div class="mc-desc">BTC · ETH · USDT & more</div>
        </div>

        <div class="method-card" id="mc-giftcard" onclick="chooseMethod('giftcard')">
            <div class="mc-check">✓</div>
            <span class="mc-badge badge-live">Live</span>
            <div class="mc-icon" style="background:linear-gradient(135deg,#fef9c3,#fde68a);">
                <iconify-icon icon="ph:gift-bold" style="color:#92400e;"></iconify-icon>
            </div>
            <div class="mc-name">Gift Card</div>
            <div class="mc-desc">Amazon · iTunes · Google Play</div>
        </div>

        <div class="method-card" id="mc-bank" onclick="chooseMethod('bank')">
            <div class="mc-check">✓</div>
            <span class="mc-badge badge-live">Live</span>
            <div class="mc-icon" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
                <iconify-icon icon="ph:bank-bold" style="color:#1e40af;"></iconify-icon>
            </div>
            <div class="mc-name">Bank Transfer</div>
            <div class="mc-desc">Wire · ACH · International</div>
        </div>
    </div>

    <!-- ══ PANEL — CRYPTO ══ -->
    <div class="pay-panel" id="panel-crypto">
        <div class="panel-card">
            <form action="{{ route('user.make-deposit') }}" method="POST" id="cryptoForm" onsubmit="return handleCryptoSubmit(this)">
                @csrf
                <input type="hidden" name="payment_method" value="crypto">

                <div id="genSection" class="gen-section">
                    <iconify-icon icon="ph:shield-checkered-fill" style="font-size:2.5rem;color:#0C3A30;display:block;margin-bottom:.5rem;"></iconify-icon>
                    <h3 class="text-xl font-bold mb-1" style="color:#0C3A30;">Generate Your Secure Wallet</h3>
                    <p class="text-sm text-gray-500 mb-5">Click below to generate your personal encrypted deposit wallets</p>
                    <button type="button" onclick="generateWallets()" class="gen-btn" id="genBtn">
                        <iconify-icon icon="ph:lock-key-fill" class="mr-1"></iconify-icon>
                        Generate Secure Wallet
                    </button>
                    <div id="genLog" style="display:none;" class="gen-log">
                        <div id="logLines"></div>
                    </div>
                </div>

                <div id="walletSection" style="display:none;" class="mb-6">
                    <h4 class="text-base font-bold mb-4" style="color:#0C3A30;">
                        <iconify-icon icon="ph:wallet-bold" class="mr-1" style="color:var(--pg);"></iconify-icon>
                        Select Cryptocurrency
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3" id="walletGrid"></div>
                </div>

                <div id="cryptoAmountSection" style="display:none;" class="mb-6">
                    <h4 class="text-base font-bold mb-4" style="color:#0C3A30;">
                        <iconify-icon icon="ph:currency-dollar-bold" class="mr-1" style="color:var(--pg);"></iconify-icon>
                        Enter Amount
                    </h4>
                    <div class="grid md:grid-cols-2 gap-5">
                        <div>
                            <label class="f-label">Deposit Amount (USD) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                <input type="number" name="amount" id="crypto_amt" step="0.01" min="0.01"
                                    class="f-input pl-8" placeholder="0.00" required oninput="syncCryptoAmt()">
                            </div>
                            <input type="hidden" name="amount_usd" id="crypto_amt_usd">
                            <p class="text-xs text-gray-400 mt-1">No minimum · No maximum</p>
                        </div>
                        <div>
                            <label class="f-label">Quick Select</label>
                            <div class="flex flex-wrap gap-2" id="cryptoPresets"></div>
                        </div>
                    </div>
                </div>

                <div id="cryptoActions" style="display:none;" class="flex justify-center gap-3 pt-5 border-t border-gray-200">
                    <button type="button" onclick="resetCrypto()" class="btn-reset">Reset</button>
                    <button type="submit" id="cryptoSubmit" class="btn-continue" disabled>
                        <span id="cryptoSubmitText">Continue to Payment →</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ══ PANEL — GIFT CARD ══ -->
    <div class="pay-panel" id="panel-giftcard">
        <div class="panel-card">
            <form action="{{ route('deposit.giftcard.submit') }}" method="POST" enctype="multipart/form-data" id="gcForm" onsubmit="return handleGcSubmit(this)">
                @csrf
                <input type="hidden" name="payment_method" value="giftcard">
                <input type="hidden" name="card_type_label" id="gc_type_label" value="Amazon">

                <div class="mb-5">
                    <h4 class="text-base font-bold mb-1" style="color:#0C3A30;">
                        <iconify-icon icon="ph:gift-bold" class="mr-1" style="color:var(--pg);"></iconify-icon>
                        Gift Card Deposit
                    </h4>
                    <p class="text-sm text-gray-500">Submit your gift card details. Our team reviews and credits your account within 1 minutes – 6 hours.</p>
                </div>

                <div class="mb-5">
                    <label class="f-label">Gift Card Type <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3" id="gcTypeGrid">
                        @foreach([
                            ['amazon', 'Amazon', 'ph:shopping-cart-bold', '#f97316', '#fff7ed'],
                            ['itunes', 'iTunes', 'ph:music-notes-bold', '#ec4899', '#fdf2f8'],
                            ['google', 'Google Play','ph:google-play-logo-bold','#16a34a', '#f0fdf4'],
                            ['steam', 'Steam', 'ph:game-controller-bold', '#2563eb', '#eff6ff'],
                            ['other', 'Other', 'ph:gift-bold', '#8b5cf6', '#f5f3ff'],
                        ] as [$val, $lbl, $ico, $col, $bg])
                        <label class="gc-type {{ $loop->first ? 'selected' : '' }}" id="gct-{{ $val }}" onclick="selectGcType('{{ $val }}', '{{ $lbl }}')">
                            <input type="radio" name="card_type" value="{{ $val }}" {{ $loop->first ? 'checked' : '' }}>
                            <div class="gc-check">✓</div>
                            <div style="width:40px;height:40px;border-radius:10px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;margin:0 auto .4rem;">
                                <iconify-icon icon="{{ $ico }}" style="font-size:1.3rem;color:{{ $col }};"></iconify-icon>
                            </div>
                            <div style="font-size:.72rem;font-weight:600;color:#374151;">{{ $lbl }}</div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div id="otherCardNameWrap" style="display:none;" class="mb-5">
                    <label class="f-label">Card Name <span class="text-red-500">*</span> <span class="text-gray-400 font-normal">(type the gift card brand)</span></label>
                    <input type="text" id="other_card_name" name="other_card_name"
                        class="f-input" placeholder="e.g. Visa Gift Card, Razer Gold, PSN Card..."
                        oninput="validateGc()">
                    <p class="text-xs text-gray-400 mt-1">Enter the exact name of the gift card you have</p>
                </div>

                <div id="gcSelectedSummary" class="selected-gc-badge" style="display:none;">
                    <iconify-icon id="gcSummaryIcon" icon="ph:gift-bold" style="font-size:1rem;"></iconify-icon>
                    <span id="gcSummaryName">Amazon Gift Card</span>
                    <span style="color:#9ca3af;">selected</span>
                </div>

                <div class="grid md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="f-label">Card Code / Redemption Number <span class="text-red-500">*</span></label>
                        <input type="text" name="card_code" id="gc_code" class="f-input"
                            placeholder="e.g. XXXX-XXXX-XXXX-XXXX" required oninput="validateGc()">
                        <p class="text-xs text-gray-400 mt-1">The code printed or scratched at the back of your card</p>
                    </div>
                    <div>
                        <label class="f-label">Card Value (USD) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                            <input type="number" name="amount_deposited" id="gc_amt" step="0.01" min="1"
                                class="f-input pl-8" placeholder="0.00" required oninput="validateGc()">
                        </div>
                        <div class="flex flex-wrap gap-2 mt-2" id="gcPresets"></div>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="f-label">Upload Gift Card Back Photo / Screenshot <span class="text-red-500">*</span></label>
                    <div class="upload-zone" id="gcZone" onclick="document.getElementById('gc_img').click()">
                        <iconify-icon icon="ph:gift" style="font-size:2.5rem;color:#d1d5db;display:block;margin:0 auto .5rem;"></iconify-icon>
                        <p class="text-sm font-medium text-gray-500">Click to upload your gift card image</p>
                        <p class="text-xs text-gray-400 mt-1">PNG · JPG · WEBP — max 10MB</p>
                    </div>
                    <input type="file" id="gc_img" name="card_image" class="hidden" accept="image/*" onchange="handleGcImg(this)">
                </div>

                <div class="mb-5">
                    <label class="f-label">Additional Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea name="notes" rows="2" class="f-input" placeholder="Any extra info about this card..."></textarea>
                </div>

                <div class="flex justify-center gap-3 pt-5 border-t border-gray-200">
                    <button type="button" onclick="resetGc()" class="btn-reset">Reset</button>
                    <button type="submit" id="gcSubmit" class="btn-continue" disabled>
                        <span id="gcSubmitText">Submit Gift Card →</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ══ PANEL — BANK TRANSFER ══ -->
    <div class="pay-panel" id="panel-bank">
        <div class="panel-card">

            <!-- STEP 1: Request Form -->
            <div id="bankStep1" class="bank-step">
                <div class="mb-5">
                    <h4 class="text-base font-bold mb-1" style="color:#0C3A30;">
                        <iconify-icon icon="ph:bank-bold" class="mr-1" style="color:var(--pg);"></iconify-icon>
                        Request Bank Transfer
                    </h4>
                    <p class="text-sm text-gray-500">Submit a request and you will receive bank details.</p>
                </div>

                <form id="bankRequestForm" onsubmit="return requestBankTransfer(event)">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-5 mb-5">
                        <div>
                            <label class="f-label">Your Country <span class="text-red-500">*</span></label>
                          <select name="country" id="bankCountry" class="country-select" required>
    <option value="">Select your country...</option>

    <option value="Afghanistan">🇦🇫 Afghanistan</option>
    <option value="Albania">🇦🇱 Albania</option>
    <option value="Algeria">🇩🇿 Algeria</option>
    <option value="Andorra">🇦🇩 Andorra</option>
    <option value="Angola">🇦🇴 Angola</option>
    <option value="Antigua and Barbuda">🇦🇬 Antigua and Barbuda</option>
    <option value="Argentina">🇦🇷 Argentina</option>
    <option value="Armenia">🇦🇲 Armenia</option>
    <option value="Australia">🇦🇺 Australia</option>
    <option value="Austria">🇦🇹 Austria</option>
    <option value="Azerbaijan">🇦🇿 Azerbaijan</option>

    <option value="Bahamas">🇧🇸 Bahamas</option>
    <option value="Bahrain">🇧🇭 Bahrain</option>
    <option value="Bangladesh">🇧🇩 Bangladesh</option>
    <option value="Barbados">🇧🇧 Barbados</option>
    <option value="Belarus">🇧🇾 Belarus</option>
    <option value="Belgium">🇧🇪 Belgium</option>
    <option value="Belize">🇧🇿 Belize</option>
    <option value="Benin">🇧🇯 Benin</option>
    <option value="Bhutan">🇧🇹 Bhutan</option>
    <option value="Bolivia">🇧🇴 Bolivia</option>
    <option value="Bosnia and Herzegovina">🇧🇦 Bosnia and Herzegovina</option>
    <option value="Botswana">🇧🇼 Botswana</option>
    <option value="Brazil">🇧🇷 Brazil</option>
    <option value="Brunei">🇧🇳 Brunei</option>
    <option value="Bulgaria">🇧🇬 Bulgaria</option>
    <option value="Burkina Faso">🇧🇫 Burkina Faso</option>
    <option value="Burundi">🇧🇮 Burundi</option>

    <option value="Cabo Verde">🇨🇻 Cabo Verde</option>
    <option value="Cambodia">🇰🇭 Cambodia</option>
    <option value="Cameroon">🇨🇲 Cameroon</option>
    <option value="Canada">🇨🇦 Canada</option>
    <option value="Central African Republic">🇨🇫 Central African Republic</option>
    <option value="Chad">🇹🇩 Chad</option>
    <option value="Chile">🇨🇱 Chile</option>
    <option value="China">🇨🇳 China</option>
    <option value="Colombia">🇨🇴 Colombia</option>
    <option value="Comoros">🇰🇲 Comoros</option>
    <option value="Congo">🇨🇬 Congo</option>
    <option value="Costa Rica">🇨🇷 Costa Rica</option>
    <option value="Croatia">🇭🇷 Croatia</option>
    <option value="Cuba">🇨🇺 Cuba</option>
    <option value="Cyprus">🇨🇾 Cyprus</option>
    <option value="Czechia">🇨🇿 Czechia</option>

    <option value="Democratic Republic of the Congo">🇨🇩 Democratic Republic of the Congo</option>
    <option value="Denmark">🇩🇰 Denmark</option>
    <option value="Djibouti">🇩🇯 Djibouti</option>
    <option value="Dominica">🇩🇲 Dominica</option>
    <option value="Dominican Republic">🇩🇴 Dominican Republic</option>

    <option value="Ecuador">🇪🇨 Ecuador</option>
    <option value="Egypt">🇪🇬 Egypt</option>
    <option value="El Salvador">🇸🇻 El Salvador</option>
    <option value="Equatorial Guinea">🇬🇶 Equatorial Guinea</option>
    <option value="Eritrea">🇪🇷 Eritrea</option>
    <option value="Estonia">🇪🇪 Estonia</option>
    <option value="Eswatini">🇸🇿 Eswatini</option>
    <option value="Ethiopia">🇪🇹 Ethiopia</option>

    <option value="Fiji">🇫🇯 Fiji</option>
    <option value="Finland">🇫🇮 Finland</option>
    <option value="France">🇫🇷 France</option>

    <option value="Gabon">🇬🇦 Gabon</option>
    <option value="Gambia">🇬🇲 Gambia</option>
    <option value="Georgia">🇬🇪 Georgia</option>
    <option value="Germany">🇩🇪 Germany</option>
    <option value="Ghana">🇬🇭 Ghana</option>
    <option value="Greece">🇬🇷 Greece</option>
    <option value="Grenada">🇬🇩 Grenada</option>
    <option value="Guatemala">🇬🇹 Guatemala</option>
    <option value="Guinea">🇬🇳 Guinea</option>
    <option value="Guinea-Bissau">🇬🇼 Guinea-Bissau</option>
    <option value="Guyana">🇬🇾 Guyana</option>

    <option value="Haiti">🇭🇹 Haiti</option>
    <option value="Honduras">🇭🇳 Honduras</option>
    <option value="Hungary">🇭🇺 Hungary</option>

    <option value="Iceland">🇮🇸 Iceland</option>
    <option value="India">🇮🇳 India</option>
    <option value="Indonesia">🇮🇩 Indonesia</option>
    <option value="Iran">🇮🇷 Iran</option>
    <option value="Iraq">🇮🇶 Iraq</option>
    <option value="Ireland">🇮🇪 Ireland</option>
    <option value="Israel">🇮🇱 Israel</option>
    <option value="Italy">🇮🇹 Italy</option>
    <option value="Ivory Coast">🇨🇮 Ivory Coast</option>

    <option value="Jamaica">🇯🇲 Jamaica</option>
    <option value="Japan">🇯🇵 Japan</option>
    <option value="Jordan">🇯🇴 Jordan</option>

    <option value="Kazakhstan">🇰🇿 Kazakhstan</option>
    <option value="Kenya">🇰🇪 Kenya</option>
    <option value="Kiribati">🇰🇮 Kiribati</option>
    <option value="Kuwait">🇰🇼 Kuwait</option>
    <option value="Kyrgyzstan">🇰🇬 Kyrgyzstan</option>

    <option value="Laos">🇱🇦 Laos</option>
    <option value="Latvia">🇱🇻 Latvia</option>
    <option value="Lebanon">🇱🇧 Lebanon</option>
    <option value="Lesotho">🇱🇸 Lesotho</option>
    <option value="Liberia">🇱🇷 Liberia</option>
    <option value="Libya">🇱🇾 Libya</option>
    <option value="Liechtenstein">🇱🇮 Liechtenstein</option>
    <option value="Lithuania">🇱🇹 Lithuania</option>
    <option value="Luxembourg">🇱🇺 Luxembourg</option>

    <option value="Madagascar">🇲🇬 Madagascar</option>
    <option value="Malawi">🇲🇼 Malawi</option>
    <option value="Malaysia">🇲🇾 Malaysia</option>
    <option value="Maldives">🇲🇻 Maldives</option>
    <option value="Mali">🇲🇱 Mali</option>
    <option value="Malta">🇲🇹 Malta</option>
    <option value="Marshall Islands">🇲🇭 Marshall Islands</option>
    <option value="Mauritania">🇲🇷 Mauritania</option>
    <option value="Mauritius">🇲🇺 Mauritius</option>
    <option value="Mexico">🇲🇽 Mexico</option>
    <option value="Micronesia">🇫🇲 Micronesia</option>
    <option value="Moldova">🇲🇩 Moldova</option>
    <option value="Monaco">🇲🇨 Monaco</option>
    <option value="Mongolia">🇲🇳 Mongolia</option>
    <option value="Montenegro">🇲🇪 Montenegro</option>
    <option value="Morocco">🇲🇦 Morocco</option>
    <option value="Mozambique">🇲🇿 Mozambique</option>
    <option value="Myanmar">🇲🇲 Myanmar</option>

    <option value="Namibia">🇳🇦 Namibia</option>
    <option value="Nauru">🇳🇷 Nauru</option>
    <option value="Nepal">🇳🇵 Nepal</option>
    <option value="Netherlands">🇳🇱 Netherlands</option>
    <option value="New Zealand">🇳🇿 New Zealand</option>
    <option value="Nicaragua">🇳🇮 Nicaragua</option>
    <option value="Niger">🇳🇪 Niger</option>
    <option value="Nigeria">🇳🇬 Nigeria</option>
    <option value="North Korea">🇰🇵 North Korea</option>
    <option value="North Macedonia">🇲🇰 North Macedonia</option>
    <option value="Norway">🇳🇴 Norway</option>

    <option value="Oman">🇴🇲 Oman</option>

    <option value="Pakistan">🇵🇰 Pakistan</option>
    <option value="Palau">🇵🇼 Palau</option>
    <option value="Palestine">🇵🇸 Palestine</option>
    <option value="Panama">🇵🇦 Panama</option>
    <option value="Papua New Guinea">🇵🇬 Papua New Guinea</option>
    <option value="Paraguay">🇵🇾 Paraguay</option>
    <option value="Peru">🇵🇪 Peru</option>
    <option value="Philippines">🇵🇭 Philippines</option>
    <option value="Poland">🇵🇱 Poland</option>
    <option value="Portugal">🇵🇹 Portugal</option>

    <option value="Qatar">🇶🇦 Qatar</option>

    <option value="Romania">🇷🇴 Romania</option>
    <option value="Russia">🇷🇺 Russia</option>
    <option value="Rwanda">🇷🇼 Rwanda</option>

    <option value="Saint Kitts and Nevis">🇰🇳 Saint Kitts and Nevis</option>
    <option value="Saint Lucia">🇱🇨 Saint Lucia</option>
    <option value="Saint Vincent and the Grenadines">🇻🇨 Saint Vincent and the Grenadines</option>
    <option value="Samoa">🇼🇸 Samoa</option>
    <option value="San Marino">🇸🇲 San Marino</option>
    <option value="Sao Tome and Principe">🇸🇹 Sao Tome and Principe</option>
    <option value="Saudi Arabia">🇸🇦 Saudi Arabia</option>
    <option value="Senegal">🇸🇳 Senegal</option>
    <option value="Serbia">🇷🇸 Serbia</option>
    <option value="Seychelles">🇸🇨 Seychelles</option>
    <option value="Sierra Leone">🇸🇱 Sierra Leone</option>
    <option value="Singapore">🇸🇬 Singapore</option>
    <option value="Slovakia">🇸🇰 Slovakia</option>
    <option value="Slovenia">🇸🇮 Slovenia</option>
    <option value="Solomon Islands">🇸🇧 Solomon Islands</option>
    <option value="Somalia">🇸🇴 Somalia</option>
    <option value="South Africa">🇿🇦 South Africa</option>
    <option value="South Korea">🇰🇷 South Korea</option>
    <option value="South Sudan">🇸🇸 South Sudan</option>
    <option value="Spain">🇪🇸 Spain</option>
    <option value="Sri Lanka">🇱🇰 Sri Lanka</option>
    <option value="Sudan">🇸🇩 Sudan</option>
    <option value="Suriname">🇸🇷 Suriname</option>
    <option value="Sweden">🇸🇪 Sweden</option>
    <option value="Switzerland">🇨🇭 Switzerland</option>
    <option value="Syria">🇸🇾 Syria</option>

    <option value="Taiwan">🇹🇼 Taiwan</option>
    <option value="Tajikistan">🇹🇯 Tajikistan</option>
    <option value="Tanzania">🇹🇿 Tanzania</option>
    <option value="Thailand">🇹🇭 Thailand</option>
    <option value="Timor-Leste">🇹🇱 Timor-Leste</option>
    <option value="Togo">🇹🇬 Togo</option>
    <option value="Tonga">🇹🇴 Tonga</option>
    <option value="Trinidad and Tobago">🇹🇹 Trinidad and Tobago</option>
    <option value="Tunisia">🇹🇳 Tunisia</option>
    <option value="Turkey">🇹🇷 Turkey</option>
    <option value="Turkmenistan">🇹🇲 Turkmenistan</option>
    <option value="Tuvalu">🇹🇻 Tuvalu</option>

    <option value="Uganda">🇺🇬 Uganda</option>
    <option value="Ukraine">🇺🇦 Ukraine</option>
    <option value="United Arab Emirates">🇦🇪 United Arab Emirates</option>
    <option value="United Kingdom">🇬🇧 United Kingdom</option>
    <option value="United States">🇺🇸 United States</option>
    <option value="Uruguay">🇺🇾 Uruguay</option>
    <option value="Uzbekistan">🇺🇿 Uzbekistan</option>

    <option value="Vanuatu">🇻🇺 Vanuatu</option>
    <option value="Vatican City">🇻🇦 Vatican City</option>
    <option value="Venezuela">🇻🇪 Venezuela</option>
    <option value="Vietnam">🇻🇳 Vietnam</option>

    <option value="Yemen">🇾🇪 Yemen</option>

    <option value="Zambia">🇿🇲 Zambia</option>
    <option value="Zimbabwe">🇿🇼 Zimbabwe</option>
</select>
                        </div>
                        <div>
                            <label class="f-label">Deposit Amount (USD) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">$</span>
                                <input type="number" name="amount" id="bankAmount" step="0.01" min="1"
                                    class="f-input pl-8" placeholder="0.00" required>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-2" id="bankPresets"></div>
                        </div>
                    </div>

                    <div class="flex justify-center pt-5 border-t border-gray-200">
                        <button type="submit" id="bankRequestBtn" class="glow-button">
                            <span id="bankRequestText">
                                <iconify-icon icon="ph:paper-plane-tilt-fill" class="mr-2"></iconify-icon>
                                Submit Request
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- STEP 2: Waiting for Admin / Details Display -->
            <div id="bankStep2" style="display:none;" class="bank-step">
                <div id="bankWaitingState">
                    <div class="text-center py-8">
                        <div class="w-20 h-20 mx-auto mb-4">
                            <iconify-icon icon="ph:clock-fill" style="font-size:4rem;color:var(--pg);"></iconify-icon>
                        </div>
                        <h4 class="text-xl font-bold mb-2" style="color:#0C3A30;">Waiting for Bank Details</h4>
                        <p class="text-gray-500 mb-4" id="bankWaitingMessage">Fetching your bank details. Please wait...</p>
                        <div class="bg-gray-100 rounded-lg p-4 inline-block">
                            <p class="text-sm text-gray-600">Request Code: <strong id="bankRequestCodeDisplay" style="color:var(--pg);">---</strong></p>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-3 justify-center">
                            <button onclick="checkBankStatus()" class="px-6 py-2 bg-[#9EDD05] text-[#0C3A30] rounded-lg font-semibold hover:bg-[#8AC304] transition">
                                <iconify-icon icon="ph:arrows-clockwise-fill" class="mr-2"></iconify-icon>
                                Check Status
                            </button>
                            <button onclick="resetBankTransfer()" class="px-6 py-2 border border-red-300 text-red-500 hover:bg-red-50 rounded-lg transition">
                                <iconify-icon icon="ph:x-circle-fill" class="mr-2"></iconify-icon>
                                Cancel
                            </button>
                            <button onclick="clearBankTransferState()"
                                class="px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-semibold">
                                <iconify-icon icon="ph:trash-fill" class="mr-2"></iconify-icon>
                                Reset
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-4">Auto-refreshes every 30 seconds</p>
                    </div>
                </div>

                <!-- Bank Details Display -->
                <div id="bankDetailsDisplay" style="display:none;">
                    <div class="gradient-border">
                        <div class="gradient-border-inner">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-bold" style="color:#0C3A30;">
                                    <iconify-icon icon="ph:check-circle-fill" style="color:var(--pg);" class="mr-2"></iconify-icon>
                                    Bank Details Received
                                </h4>
                                <span class="text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full">
                                    <iconify-icon icon="ph:check-circle-fill" class="mr-1"></iconify-icon>
                                    Ready
                                </span>
                            </div>

                            <div class="bank-details-grid" id="bankDetailsContent">
                                <!-- Populated by JavaScript -->
                            </div>

                            <div class="bg-yellow-50 rounded-lg p-4 mb-4">
                                <p class="text-sm text-yellow-800 flex items-start gap-2">
                                    <iconify-icon icon="ph:info-fill" class="text-yellow-600 mt-0.5"></iconify-icon>
                                    <span><strong>Important:</strong> Please transfer the exact amount and include your request code in the transfer description.</span>
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3 justify-center">
                                <button onclick="copyBankDetails()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition flex items-center gap-2">
                                    <iconify-icon icon="ph:copy-fill"></iconify-icon>
                                    Copy All Details
                                </button>
                                <button onclick="resetBankTransfer()" class="px-4 py-2 border border-red-300 text-red-500 hover:bg-red-50 rounded-lg transition flex items-center gap-2">
                                    <iconify-icon icon="ph:x-circle-fill"></iconify-icon>
                                    Cancel
                                </button>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h5 class="font-semibold mb-3" style="color:#0C3A30;">Submit Transfer Proof</h5>
                                <form id="bankProofForm" onsubmit="return submitBankProof(event)">
                                    @csrf
                                    <input type="hidden" name="deposit_id" id="bankDepositId">
                                    <input type="hidden" name="amount" id="bankProofAmount">

                                    <div class="grid md:grid-cols-2 gap-5">
                                        <div>
                                            <label class="f-label">Upload Transfer Screenshot <span class="text-red-500">*</span></label>
                                            <div class="upload-zone" id="bankProofZone" onclick="document.getElementById('bankProofInput').click()">
                                                <iconify-icon icon="ph:upload-bold" style="font-size:2.5rem;color:#d1d5db;display:block;margin:0 auto .5rem;"></iconify-icon>
                                                <p class="text-sm font-medium text-gray-500">Click to upload proof</p>
                                                <p class="text-xs text-gray-400 mt-1">PNG · JPG · WEBP — max 10MB</p>
                                            </div>
                                            <input type="file" id="bankProofInput" name="proof" class="hidden" accept="image/*" onchange="handleBankProof(this)">
                                        </div>
                                        <div>
                                            <label class="f-label">Amount Transferred (USD)</label>
                                            <input type="number" id="bankTransferAmount" class="f-input" placeholder="0.00" step="0.01" min="0.01" required>
                                            <p class="text-xs text-gray-400 mt-1">Enter the exact amount you transferred</p>
                                        </div>
                                    </div>

                                    <div class="flex justify-center mt-4">
                                        <button type="submit" id="bankProofSubmit" class="glow-button" disabled>
                                            <span id="bankProofText">
                                                <iconify-icon icon="ph:check-circle-fill" class="mr-2"></iconify-icon>
                                                Submit Proof
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Info box -->
    <div class="info-box">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background:rgba(158,221,5,.15);">
                <iconify-icon icon="ph:info-fill" style="color:var(--pg);font-size:1.25rem;"></iconify-icon>
            </div>
            <div>
                <h5 class="font-semibold mb-1" style="color:#0C3A30;">Deposit Information</h5>
                <p class="text-sm text-gray-600">
                    Crypto deposits are processed within <strong>1–10 minutes</strong> after blockchain confirmation.
                    Gift card deposits are manually reviewed within <strong>1–20 minutes</strong>.
                    Bank transfer details will be sent within <strong>3 minutes - 1 hour</strong>.
                    Funds are added to your available balance once approved.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Guide Modal -->
<div class="gmodal" id="gmodal" onclick="if(event.target===this)closeGuide()">
    <div class="gmodal-body">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-xl font-bold" style="color:#0C3A30;">Deposit Guide</h3>
            <button onclick="closeGuide()" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Crypto</p>
        @foreach(['Select your cryptocurrency wallet','Enter the amount you want to deposit','Copy the wallet address on the next page','Send crypto from your exchange or personal wallet','Upload a screenshot of the transaction as proof'] as $i => $s)
        <div class="gstep">
            <div class="gsnum">{{ $i+1 }}</div>
            <p class="text-sm text-gray-600 self-center">{{ $s }}</p>
        </div>
        @endforeach
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-5 mb-3">Gift Card</p>
        @foreach(['Choose your gift card brand (select Other if yours is not listed)','Enter the exact name if you chose Other','Enter the card serial / redemption code','Type the exact face value (USD) of the card','Upload a clear photo of the gift card and submit'] as $i => $s)
        <div class="gstep">
            <div class="gsnum">{{ $i+1 }}</div>
            <p class="text-sm text-gray-600 self-center">{{ $s }}</p>
        </div>
        @endforeach
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-5 mb-3">Bank Transfer</p>
        @foreach([
            'Select your country and enter the amount',
            'Submit your request',
            'System will generate bank details and send to your email',
            'Make the transfer to the provided account',
            'Upload proof of payment for approval'
        ] as $i => $s)
        <div class="gstep">
            <div class="gsnum">{{ $i+1 }}</div>
            <p class="text-sm text-gray-600 self-center">{{ $s }}</p>
        </div>
        @endforeach
        <div class="mt-5 p-3 bg-yellow-50 rounded-lg">
            <p class="text-xs text-yellow-800"><strong>⏱ Processing times:</strong> Crypto: 1–10 min · Gift Card: 30 min–12 hrs · Bank Transfer: 24-48 hrs</p>
        </div>
    </div>
</div>

<script>
// ── STATE ──
const rates = {};
let cryptoWallet = null;
let gcType = 'amazon';
let gcTypeLabel = 'Amazon';
const QUICK = [500, 1000, 2000, 5000];

const GC_META = {
    amazon: { icon: 'ph:shopping-cart-bold', color: '#f97316', label: 'Amazon Gift Card' },
    itunes: { icon: 'ph:music-notes-bold', color: '#ec4899', label: 'iTunes Gift Card' },
    google: { icon: 'ph:google-play-logo-bold', color: '#16a34a', label: 'Google Play Gift Card' },
    steam: { icon: 'ph:game-controller-bold', color: '#2563eb', label: 'Steam Gift Card' },
    other: { icon: 'ph:gift-bold', color: '#8b5cf6', label: 'Other Gift Card' },
};

const LOG_MSGS = [
    "Initializing secure wallet generator...",
    "Connecting to blockchain network...",
    "Establishing encrypted channel...",
    "Secure connection established ✓",
    "Generating Bitcoin wallet...",
    "Bitcoin wallet ready ✓",
    "Generating Ethereum wallet...",
    "Ethereum wallet ready ✓",
    "Generating USDT wallet...",
    "USDT wallet ready ✓",
    "Applying multi-layer encryption...",
    "All wallets generated successfully ✓"
];

// Extended coin list with all supported coins
const COINS = ['btc', 'eth', 'usdt', 'bnb', 'sol', 'xrp', 'ada', 'doge', 'ltc', 'trx', 
    'matic', 'link', 'dot', 'avax', 'uni', 'atom', 'xlm', 'algo', 'vet', 'icp', 
    'fil', 'egld', 'theta', 'xtz', 'eos', 'cake', 'aave', 'grt', 'mkr', 'comp', 
    'snx', 'crv', 'yfi', 'bat', 'zec', 'dash', 'xmr', 'neo', 'waves', 'hbar', 
    'near', 'ftm', 'one', 'usdc', 'busd', 'shib', 'apt', 'arb', 'op', 'sui'];

// ── CRYPTO LOGO HELPER (JavaScript version) ──
function getCryptoTickerJS(name) {
    const n = name.toLowerCase().trim();
    
    const exactMap = {
        'bitcoin': 'btc', 'ethereum': 'eth', 'etherium': 'eth', 'etherum': 'eth',
        'tether': 'usdt', 'dogecoin': 'doge', 'dodge': 'doge', 'doge': 'doge',
        'matic': 'matic', 'polygon': 'matic', 'solana': 'sol', 'ripple': 'xrp',
        'cardano': 'ada', 'binance coin': 'bnb', 'binance': 'bnb', 'litecoin': 'ltc',
        'tron': 'trx', 'avalanche': 'avax', 'chainlink': 'link', 'uniswap': 'uni',
        'cosmos': 'atom', 'stellar': 'xlm', 'algorand': 'algo', 'vechain': 'vet',
        'internet computer': 'icp', 'filecoin': 'fil', 'elrond': 'egld',
        'theta': 'theta', 'tezos': 'xtz', 'eos': 'eos', 'pancakeswap': 'cake',
        'aave': 'aave', 'the graph': 'grt', 'maker': 'mkr', 'compound': 'comp',
        'synthetix': 'snx', 'curve': 'crv', 'yearn finance': 'yfi',
        'basic attention token': 'bat', 'zcash': 'zec', 'dash': 'dash',
        'monero': 'xmr', 'neo': 'neo', 'waves': 'waves', 'hedera': 'hbar',
        'near': 'near', 'fantom': 'ftm', 'harmony': 'one', 'usd coin': 'usdc',
        'binance usd': 'busd', 'shiba inu': 'shib', 'aptos': 'apt',
        'arbitrum': 'arb', 'optimism': 'op', 'sui': 'sui'
    };
    
    if (exactMap[n]) return exactMap[n];
    
    // Handle USDT variants (ERC20, TRC20, BEP20, etc.)
    if (n.includes('usdt')) return 'usdt';
    if (n.includes('usdc')) return 'usdc';
    if (n.includes('busd')) return 'busd';
    
    const firstWord = n.split(' ')[0];
    if (exactMap[firstWord]) return exactMap[firstWord];
    
    for (const [key, ticker] of Object.entries(exactMap)) {
        if (n.includes(key)) return ticker;
    }
    
    return n;
}

function getCryptoLogoJS(name) {
    const ticker = getCryptoTickerJS(name);
    const base = 'https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/128/color/';
    return COINS.includes(ticker) ? base + ticker + '.png' : base + 'generic.png';
}

function getNetworkLabel(name) {
    const n = name.toLowerCase();
    if (n.includes('erc20')) return 'ERC20';
    if (n.includes('trc20')) return 'TRC20';
    if (n.includes('bep20')) return 'BEP20';
    if (n.includes('bsc')) return 'BSC';
    if (n.includes('polygon')) return 'Polygon';
    if (n.includes('solana')) return 'Solana';
    if (n.includes('avalanche')) return 'Avalanche';
    if (n.includes('arbitrum')) return 'Arbitrum';
    if (n.includes('optimism')) return 'Optimism';
    return 'Mainnet';
}

function logoUrl(cryptoName) {
    return getCryptoLogoJS(cryptoName);
}

function fmt(n) {
    return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// ── EXCHANGE RATES ──
fetch('https://v6.exchangerate-api.com/v6/a8e67b756f551b68d4ada293/latest/USD')
    .then(r => r.json()).then(d => {
        if (d.result === 'success') Object.assign(rates, d.conversion_rates);
    })
    .catch(() => { rates.USD = 1; });

// ── METHOD SELECTION ──
function chooseMethod(m) {
    document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('.pay-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('mc-' + m).classList.add('selected');
    document.getElementById('panel-' + m).classList.add('active');
    document.getElementById('s2').classList.add('active');
    if (m === 'crypto') buildPresets('cryptoPresets', 'crypto');
    if (m === 'giftcard') {
        buildPresets('gcPresets', 'gc');
        selectGcType('amazon', 'Amazon');
    }
    if (m === 'bank') {
        buildBankPresets();
    }
    document.getElementById('panel-' + m).scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// ── PRESETS ──
function buildPresets(containerId, type) {
    const c = document.getElementById(containerId);
    if (!c || c.children.length) return;
    QUICK.forEach(v => {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'preset-btn';
        b.textContent = '$' + fmt(v);
        b.onclick = () => {
            if (type === 'crypto') {
                document.getElementById('crypto_amt').value = v.toFixed(2);
                document.getElementById('crypto_amt_usd').value = v.toFixed(2);
                validateCrypto();
            } else {
                document.getElementById('gc_amt').value = v.toFixed(2);
                validateGc();
            }
            c.querySelectorAll('.preset-btn').forEach(x => x.classList.remove('active'));
            b.classList.add('active');
        };
        c.appendChild(b);
    });
}

// ── CRYPTO ──
async function generateWallets() {
    const btn = document.getElementById('genBtn'), logDiv = document.getElementById('genLog'), logBody = document.getElementById('logLines');
    btn.disabled = true;
    btn.textContent = 'Generating...';
    logBody.innerHTML = '';
    logDiv.style.display = 'block';
    for (const msg of LOG_MSGS) {
        await addLog(msg);
        await wait(450);
    }
    await fetchWallets();
    beep();
    setTimeout(() => {
        document.getElementById('genSection').style.display = 'none';
        document.getElementById('walletSection').style.display = 'block';
        document.getElementById('cryptoActions').style.display = 'flex';
        localStorage.setItem('wallets_generated', 'true');
    }, 400);
}

async function addLog(msg) {
    const body = document.getElementById('logLines');
    if (!body) return;
    const d = document.createElement('div');
    d.className = 'log-line';
    d.textContent = '> ' + msg;
    body.appendChild(d);
    d.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    await wait(60);
}

async function fetchWallets() {
    try {
        const r = await fetch("{{ route('user.wallets.generate') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            }
        });
        const data = await r.json();
        if (data.success && data.wallets) renderWallets(data.wallets);
    } catch (e) { console.error(e); }
}

function renderWallets(wallets) {
    const grid = document.getElementById('walletGrid');
    if (!grid) return;
    grid.innerHTML = '';
    if (!wallets?.length) {
        grid.innerHTML = '<div class="col-span-full text-center text-gray-400 text-sm py-6">No wallets available.</div>';
        return;
    }
    wallets.forEach(w => {
        const cryptoName = w.crypto_name || '';
        const logoUrl = getCryptoLogoJS(cryptoName);
        const network = getNetworkLabel(cryptoName);
        
        const lbl = document.createElement('label');
        lbl.className = 'wallet-opt';
        lbl.setAttribute('data-wid', w.id);
        lbl.onclick = () => pickWallet(w.id);
        lbl.innerHTML = `
            <input type="radio" name="wallet_id" value="${w.id}" required>
            <div class="wcheck">✓</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <div class="clogo">
                    <img src="${logoUrl}" alt="${cryptoName}"
                         onerror="this.src='https://cdn.jsdelivr.net/gh/spothq/cryptocurrency-icons@master/128/color/generic.png'">
                </div>
                <div>
                    <p style="font-weight:700;color:#0C3A30;margin:0;font-size:.875rem;">${cryptoName}</p>
                    <p style="font-size:.7rem;color:#9ca3af;margin:0;">${network}</p>
                </div>
            </div>
            <div style="margin-top:.6rem;font-size:.7rem;color:#9ca3af;display:flex;align-items:center;gap:4px;">
                <iconify-icon icon="ph:lightning-fill" style="color:#9EDD05;"></iconify-icon> Instant settlement
            </div>`;
        grid.appendChild(lbl);
    });
}

function pickWallet(id) {
    document.querySelectorAll('.wallet-opt').forEach(o => o.classList.remove('selected'));
    const w = document.querySelector(`.wallet-opt[data-wid="${id}"]`);
    if (w) {
        w.classList.add('selected');
        w.querySelector('input').checked = true;
    }
    cryptoWallet = id;
    document.getElementById('cryptoAmountSection').style.display = 'block';
    document.getElementById('s3').classList.add('active');
    buildPresets('cryptoPresets', 'crypto');
    validateCrypto();
}

function syncCryptoAmt() {
    document.getElementById('crypto_amt_usd').value = document.getElementById('crypto_amt').value;
    validateCrypto();
}

function validateCrypto() {
    const amt = parseFloat(document.getElementById('crypto_amt').value) || 0;
    document.getElementById('cryptoSubmit').disabled = !(cryptoWallet && amt > 0);
}

function resetCrypto() {
    document.getElementById('cryptoForm').reset();
    document.querySelectorAll('.wallet-opt').forEach(o => o.classList.remove('selected'));
    document.getElementById('cryptoAmountSection').style.display = 'none';
    document.getElementById('cryptoSubmit').disabled = true;
    document.querySelectorAll('#cryptoPresets .preset-btn').forEach(b => b.classList.remove('active'));
    cryptoWallet = null;
    const btn = document.getElementById('cryptoSubmit');
    btn.classList.remove('submitting');
    document.getElementById('cryptoSubmitText').textContent = 'Continue to Payment →';
}

function handleCryptoSubmit(form) {
    const btn = document.getElementById('cryptoSubmit');
    if (btn.classList.contains('submitting')) return false;
    btn.classList.add('submitting');
    btn.disabled = true;
    document.getElementById('cryptoSubmitText').innerHTML = '<iconify-icon icon="ph:spinner-gap-bold" style="animation:spin360 .8s linear infinite;display:inline-block;margin-right:4px;"></iconify-icon> Processing...';
    return true;
}

// ── GIFT CARD ──
function selectGcType(type, label) {
    gcType = type;
    gcTypeLabel = label || GC_META[type]?.label || type;

    document.querySelectorAll('.gc-type').forEach(el => el.classList.remove('selected'));
    const el = document.getElementById('gct-' + type);
    if (el) {
        el.classList.add('selected');
        el.querySelector('input').checked = true;
    }

    const otherWrap = document.getElementById('otherCardNameWrap');
    if (type === 'other') {
        otherWrap.style.display = 'block';
        document.getElementById('other_card_name').required = true;
    } else {
        otherWrap.style.display = 'none';
        document.getElementById('other_card_name').required = false;
        document.getElementById('other_card_name').value = '';
    }

    const meta = GC_META[type];
    const summary = document.getElementById('gcSelectedSummary');
    document.getElementById('gcSummaryIcon').setAttribute('icon', meta.icon);
    document.getElementById('gcSummaryIcon').style.color = meta.color;
    document.getElementById('gcSummaryName').textContent = (type === 'other' && document.getElementById('other_card_name').value) ?
        document.getElementById('other_card_name').value + ' Gift Card' :
        meta.label;
    summary.style.display = 'inline-flex';

    document.getElementById('gc_type_label').value = gcTypeLabel;
    validateGc();
}

document.getElementById('other_card_name')?.addEventListener('input', function() {
    const summaryName = document.getElementById('gcSummaryName');
    if (summaryName && gcType === 'other') {
        summaryName.textContent = this.value ? this.value + ' Gift Card' : 'Other Gift Card';
        document.getElementById('gc_type_label').value = this.value || 'Other';
    }
    validateGc();
});

function handleGcImg(input) {
    const file = input.files[0];
    const zone = document.getElementById('gcZone');
    if (!file) return;
    if (file.size > 10 * 1024 * 1024) {
        showToast('File too large. Max 10MB.');
        input.value = '';
        return;
    }
    if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
        showToast('Invalid type. Use PNG, JPG or WEBP.');
        input.value = '';
        return;
    }
    zone.classList.add('has-file');
    zone.innerHTML = `
        <iconify-icon icon="ph:check-circle-fill" style="font-size:2.5rem;color:#16a34a;display:block;margin:0 auto .5rem;"></iconify-icon>
        <p style="font-size:.875rem;font-weight:600;color:#111827;">${file.name}</p>
        <p style="font-size:.72rem;color:#9ca3af;margin-top:4px;">Click to change image</p>`;
    validateGc();
}

function validateGc() {
    const code = (document.getElementById('gc_code')?.value || '').trim();
    const amt = parseFloat(document.getElementById('gc_amt')?.value) || 0;
    const hasImg = (document.getElementById('gc_img')?.files?.length || 0) > 0;
    const otherOk = gcType !== 'other' || (document.getElementById('other_card_name')?.value || '').trim().length > 0;
    document.getElementById('gcSubmit').disabled = !(code && amt > 0 && hasImg && otherOk);
}

function resetGc() {
    document.getElementById('gcForm').reset();
    const zone = document.getElementById('gcZone');
    zone.classList.remove('has-file');
    zone.innerHTML = `
        <iconify-icon icon="ph:gift" style="font-size:2.5rem;color:#d1d5db;display:block;margin:0 auto .5rem;"></iconify-icon>
        <p style="font-size:.875rem;font-weight:500;color:#9ca3af;">Click to upload your gift card image</p>
        <p style="font-size:.72rem;color:#d1d5db;margin-top:4px;">PNG · JPG · WEBP — max 10MB</p>`;
    document.getElementById('gcSubmit').disabled = true;
    document.getElementById('gcSelectedSummary').style.display = 'none';
    document.getElementById('otherCardNameWrap').style.display = 'none';
    document.querySelectorAll('#gcPresets .preset-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('gcSubmit').classList.remove('submitting');
    document.getElementById('gcSubmitText').textContent = 'Submit Gift Card →';
    selectGcType('amazon', 'Amazon');
}

function handleGcSubmit(form) {
    const btn = document.getElementById('gcSubmit');
    if (btn.classList.contains('submitting')) return false;
    const code = (document.getElementById('gc_code')?.value || '').trim();
    const amt = parseFloat(document.getElementById('gc_amt')?.value) || 0;
    const hasImg = (document.getElementById('gc_img')?.files?.length || 0) > 0;
    const otherOk = gcType !== 'other' || (document.getElementById('other_card_name')?.value || '').trim().length > 0;
    if (!code || amt <= 0 || !hasImg || !otherOk) {
        showToast('Please fill all required fields.');
        return false;
    }
    btn.classList.add('submitting');
    btn.disabled = true;
    document.getElementById('gcSubmitText').innerHTML = '<iconify-icon icon="ph:spinner-gap-bold" style="animation:spin360 .8s linear infinite;display:inline-block;margin-right:4px;"></iconify-icon> Submitting...';
    return true;
}

// ── UTILS ──
function beep() {
    try {
        const ctx = new(window.AudioContext || window.webkitAudioContext)(),
            o = ctx.createOscillator(),
            g = ctx.createGain();
        o.connect(g);
        g.connect(ctx.destination);
        o.frequency.value = 523.25;
        g.gain.value = 0.25;
        o.start();
        g.gain.exponentialRampToValueAtTime(0.00001, ctx.currentTime + .5);
        o.stop(ctx.currentTime + .5);
    } catch (e) {}
}

function wait(ms) {
    return new Promise(r => setTimeout(r, ms));
}

function showToast(msg) {
    document.querySelectorAll('.toast').forEach(t => t.remove());
    const t = document.createElement('div');
    t.className = 'toast';
    t.style.color = '#ffffff';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => {
        t.style.transition = 'opacity .4s';
        t.style.opacity = '0';
        setTimeout(() => t.remove(), 400);
    }, 4500);
}

function openGuide() {
    document.getElementById('gmodal').classList.add('active');
}

function closeGuide() {
    document.getElementById('gmodal').classList.remove('active');
}

// spinner keyframe
const styleEl = document.createElement('style');
styleEl.textContent = '@keyframes spin360{to{transform:rotate(360deg)}}';
document.head.appendChild(styleEl);

// ── BANK TRANSFER FUNCTIONS ──
let bankPollingInterval = null;
let currentRequestCode = null;
let currentDepositId = null;
let bankDetailsLoaded = false;

const BANK_PRESETS = [500, 1000, 2500, 5000, 10000];

function buildBankPresets() {
    const container = document.getElementById('bankPresets');
    if (!container || container.children.length) return;
    BANK_PRESETS.forEach(v => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'preset-btn';
        btn.textContent = '$' + v.toLocaleString();
        btn.onclick = () => {
            document.getElementById('bankAmount').value = v;
            container.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        };
        container.appendChild(btn);
    });
}

// ── RESTORE BANK TRANSFER STATE ON PAGE LOAD ──
async function restoreBankTransferState() {
    try {
        const response = await fetch('{{ route("bank.transfer.status") }}', {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.success && data.status === 'details_sent') {
            currentRequestCode = data.request_code;
            currentDepositId = data.deposit_id;

            showBankDetails(data.bank_details, data.deposit_id);
            document.getElementById('bankStep1').style.display = 'none';
            document.getElementById('bankStep2').style.display = 'block';
            document.getElementById('bankWaitingState').style.display = 'none';
            document.getElementById('bankDetailsDisplay').style.display = 'block';
            document.getElementById('bankDepositId').value = data.deposit_id;
            document.getElementById('bankTransferAmount').value = data.amount;
            document.getElementById('bankProofAmount').value = data.amount;

            bankDetailsLoaded = true;

            document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.pay-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('mc-bank').classList.add('selected');
            document.getElementById('panel-bank').classList.add('active');
            document.getElementById('s2').classList.add('active');

            showToast('✅ Bank details are ready!');

        } else if (data.success && data.status === 'pending') {
            currentRequestCode = data.request_code;
            currentDepositId = data.deposit_id;

            document.getElementById('bankRequestCodeDisplay').textContent = data.request_code;
            document.getElementById('bankStep1').style.display = 'none';
            document.getElementById('bankStep2').style.display = 'block';
            document.getElementById('bankWaitingState').style.display = 'block';
            document.getElementById('bankDetailsDisplay').style.display = 'none';

            document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.pay-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('mc-bank').classList.add('selected');
            document.getElementById('panel-bank').classList.add('active');
            document.getElementById('s2').classList.add('active');

            startBankPolling();
        }
    } catch (error) {
        console.error('Failed to restore bank transfer state:', error);
    }
}

// 1. USER SUBMITS REQUEST
async function requestBankTransfer(e) {
    e.preventDefault();
    const btn = document.getElementById('bankRequestBtn');
    const text = document.getElementById('bankRequestText');

    const country = document.getElementById('bankCountry').value;
    const amount = document.getElementById('bankAmount').value;

    if (!country || !amount || amount < 1) {
        showToast('Please select your country and enter a valid amount.');
        return false;
    }

    btn.disabled = true;
    text.innerHTML = '<span class="loading-spinner"></span> Submitting request...';

    try {
        const response = await fetch('{{ route("bank.transfer.request") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ country, amount })
        });

        const data = await response.json();

        if (data.success) {
            currentRequestCode = data.request_code;
            currentDepositId = data.deposit_id;

            document.getElementById('bankRequestCodeDisplay').textContent = data.request_code;
            document.getElementById('bankStep1').style.display = 'none';
            document.getElementById('bankStep2').style.display = 'block';
            document.getElementById('bankWaitingState').style.display = 'block';
            document.getElementById('bankDetailsDisplay').style.display = 'none';

            showToast('✅ Request submitted! System will send bank details shortly.');
            beep();

            startBankPolling();
        } else {
            showToast('❌ ' + data.message);
        }
    } catch (error) {
        showToast('❌ Failed to submit request. Please try again.');
        console.error(error);
    } finally {
        btn.disabled = false;
        text.innerHTML = '<iconify-icon icon="ph:paper-plane-tilt-fill" class="mr-2"></iconify-icon> Submit Request';
    }

    return false;
}

// 2. POLL FOR BANK DETAILS (auto-refresh)
function startBankPolling() {
    if (bankPollingInterval) clearInterval(bankPollingInterval);
    checkBankStatus();
    bankPollingInterval = setInterval(checkBankStatus, 30000);
}

async function checkBankStatus() {
    try {
        const response = await fetch('{{ route("bank.transfer.status") }}', {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const data = await response.json();

        if (data.success && data.status === 'details_sent') {
            showBankDetails(data.bank_details, data.deposit_id);
            document.getElementById('bankWaitingState').style.display = 'none';
            document.getElementById('bankDetailsDisplay').style.display = 'block';
            document.getElementById('bankDepositId').value = data.deposit_id;
            document.getElementById('bankTransferAmount').value = data.amount;
            document.getElementById('bankProofAmount').value = data.amount;
            bankDetailsLoaded = true;

            if (bankPollingInterval) {
                clearInterval(bankPollingInterval);
                bankPollingInterval = null;
            }

            showToast('✅ Bank details received! Check your email and make the transfer.');
            beep();
        } else if (data.success && data.status === 'pending') {
            const waitingMsg = document.querySelector('#bankWaitingMessage');
            if (waitingMsg) {
                waitingMsg.textContent = 'Fetching your bank details. Please wait... (Last checked: ' + new Date().toLocaleTimeString() + ')';
            }
        }
    } catch (error) {
        console.error('Failed to check status:', error);
    }
}

// 3. SHOW BANK DETAILS
function showBankDetails(details, depositId) {
    const container = document.getElementById('bankDetailsContent');
    const refCode = currentRequestCode || details.reference_code || 'N/A';

    const fields = [
        { label: 'Bank Name', value: details.bank_name, icon: 'ph:bank-fill' },
        { label: 'Account Name', value: details.account_name, icon: 'ph:user-fill' },
        { label: 'Account Number', value: details.account_number, icon: 'ph:credit-card-fill', copy: true },
        { label: 'Reference Code', value: refCode, icon: 'ph:hash-fill', copy: true, highlight: true },
        { label: 'SWIFT Code', value: details.swift_code || 'N/A', icon: 'ph:globe-fill' },
        { label: 'Routing Number', value: details.routing_number || 'N/A', icon: 'ph:arrows-clockwise-fill' },
        { label: 'IBAN', value: details.iban || 'N/A', icon: 'ph:barcode-fill' },
        { label: 'Sort Code', value: details.sort_code || 'N/A', icon: 'ph:grid-fill' },
    ];

    container.innerHTML = fields.filter(f => f.value !== 'N/A').map(f => `
        <div class="bank-detail-item">
            <div class="bank-detail-label">
                <iconify-icon icon="${f.icon}" class="mr-1"></iconify-icon>
                ${f.label}
            </div>
            <div class="bank-detail-value ${f.highlight ? 'reference-code-highlight' : ''}">
                ${f.value}
                ${f.copy ? `<button onclick="copyText('${f.value}')" class="copy-btn-sm" style="margin-left:0.5rem;font-size:0.7rem;background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.3);border-radius:4px;padding:0.1rem 0.4rem;cursor:pointer;">Copy</button>` : ''}
            </div>
        </div>
    `).join('');

    if (details.instructions) {
        const div = document.createElement('div');
        div.className = 'bank-detail-item col-span-full';
        div.innerHTML = `
            <div class="bank-detail-label">📝 Additional Instructions</div>
            <div class="text-sm text-gray-600 mt-1" style="word-wrap:break-word;white-space:normal;">${details.instructions}</div>
        `;
        container.appendChild(div);
    }

    window._bankDetails = details;
    window._bankDetails.reference_code = refCode;
}

// 4. COPY BANK DETAILS
function copyBankDetails() {
    const d = window._bankDetails;
    if (!d) {
        showToast('No bank details to copy');
        return;
    }

    const ref = d.reference_code || currentRequestCode || 'N/A';
    let text = '🏦 BANK TRANSFER\n';
    text += '═'.repeat(30) + '\n';
    text += `Bank: ${d.bank_name}\n`;
    text += `Account: ${d.account_name}\n`;
    text += `Account #: ${d.account_number}\n`;
    text += `Ref Code: ${ref}\n`;
    if (d.swift_code && d.swift_code !== 'N/A') text += `SWIFT: ${d.swift_code}\n`;
    if (d.iban && d.iban !== 'N/A') text += `IBAN: ${d.iban}\n`;
    if (d.instructions) text += `\n${d.instructions}\n`;

    navigator.clipboard.writeText(text).then(() => {
        showToast('✅ Copied!');
    });
}

// 5. HANDLE PROOF UPLOAD
function handleBankProof(input) {
    const file = input.files[0];
    const zone = document.getElementById('bankProofZone');
    const submit = document.getElementById('bankProofSubmit');

    if (!file) return;

    if (file.size > 10 * 1024 * 1024) {
        showToast('File too large. Max 10MB.');
        input.value = '';
        return;
    }

    if (!['image/jpeg', 'image/jpg', 'image/png', 'image/webp'].includes(file.type)) {
        showToast('Invalid type. Use PNG, JPG or WEBP.');
        input.value = '';
        return;
    }

    zone.classList.add('has-file');
    zone.innerHTML = `
        <iconify-icon icon="ph:check-circle-fill" style="font-size:2.5rem;color:#16a34a;display:block;margin:0 auto .5rem;"></iconify-icon>
        <p style="font-size:.875rem;font-weight:600;color:#111827;">${file.name}</p>
        <p style="font-size:.72rem;color:#9ca3af;margin-top:4px;">Click to change</p>
    `;

    const amount = document.getElementById('bankTransferAmount').value;
    submit.disabled = !(amount > 0);
}

document.getElementById('bankTransferAmount')?.addEventListener('input', function() {
    const hasFile = document.getElementById('bankProofInput').files.length > 0;
    document.getElementById('bankProofSubmit').disabled = !(this.value > 0 && hasFile);
});

// 6. SUBMIT BANK PROOF
async function submitBankProof(e) {
    e.preventDefault();
    const btn = document.getElementById('bankProofSubmit');
    const text = document.getElementById('bankProofText');
    const depositId = document.getElementById('bankDepositId').value;
    const amount = document.getElementById('bankTransferAmount').value;
    const file = document.getElementById('bankProofInput').files[0];

    if (!depositId || !amount || !file) {
        showToast('Please fill all required fields.');
        return false;
    }

    const formData = new FormData();
    formData.append('deposit_id', depositId);
    formData.append('amount', amount);
    formData.append('proof', file);

    btn.disabled = true;
    text.innerHTML = '<span class="loading-spinner"></span> Submitting...';

    try {
        const response = await fetch('{{ route("bank.transfer.submit-proof") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast('✅ ' + data.message);
            if (bankPollingInterval) {
                clearInterval(bankPollingInterval);
                bankPollingInterval = null;
            }
            setTimeout(() => {
                window.location.href = '{{ route("user.deposit-history") }}';
            }, 2000);
        } else {
            showToast('❌ ' + data.message);
        }
    } catch (error) {
        showToast('❌ Failed to submit proof. Please try again.');
        console.error(error);
    } finally {
        btn.disabled = false;
        text.innerHTML = '<iconify-icon icon="ph:check-circle-fill" class="mr-2"></iconify-icon> Submit Proof';
    }

    return false;
}

// RESET BANK TRANSFER
function resetBankTransfer() {
    document.getElementById('bankStep1').style.display = 'block';
    document.getElementById('bankStep2').style.display = 'none';
    document.getElementById('bankRequestBtn').disabled = false;
    document.getElementById('bankProofInput').value = '';
    bankDetailsLoaded = false;

    if (bankPollingInterval) {
        clearInterval(bankPollingInterval);
        bankPollingInterval = null;
    }

    document.getElementById('bankProofZone').innerHTML = `
        <iconify-icon icon="ph:upload-bold" style="font-size:2.5rem;color:#d1d5db;display:block;margin:0 auto .5rem;"></iconify-icon>
        <p class="text-sm font-medium text-gray-500">Click to upload proof</p>
        <p class="text-xs text-gray-400 mt-1">PNG · JPG · WEBP — max 10MB</p>
    `;
    document.getElementById('bankProofSubmit').disabled = true;
    document.getElementById('bankDepositId').value = '';
    currentRequestCode = null;
    currentDepositId = null;
    window._bankDetails = null;

    document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
    document.querySelectorAll('.pay-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('mc-crypto').classList.add('selected');
    document.getElementById('panel-crypto').classList.add('active');
    document.getElementById('s2').classList.remove('active');
}

function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('✅ Copied!');
    }).catch(() => {
        const el = document.createElement('textarea');
        el.value = text;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
        showToast('✅ Copied!');
    });
}

// ── CLEAR BANK TRANSFER STATE ──
function clearBankTransferState() {
    if (!confirm('⚠️ Clear your bank transfer request?')) return;

    localStorage.removeItem('bank_request_code');
    localStorage.removeItem('bank_deposit_id');
    localStorage.removeItem('bank_details_loaded');

    currentRequestCode = null;
    currentDepositId = null;
    bankDetailsLoaded = false;
    window._bankDetails = null;

    if (bankPollingInterval) {
        clearInterval(bankPollingInterval);
        bankPollingInterval = null;
    }

    location.reload();
}

// ── ON PAGE LOAD ──
document.addEventListener('DOMContentLoaded', function() {
    if (localStorage.getItem('wallets_generated') === 'true') {
        document.getElementById('genSection').style.display = 'none';
        document.getElementById('walletSection').style.display = 'block';
        document.getElementById('cryptoActions').style.display = 'flex';
        fetchWallets();
    }

    restoreBankTransferState();
});
</script>

@endsection