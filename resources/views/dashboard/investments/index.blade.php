@extends('layout.user')

@section('content')
<style>
    * {
        color: #0C3A30 !important;
    }
    :root {
        --primary-green: #9EDD05 !important;
        --dark-green: #0C3A30 !important;
        --accent-green: #8AC304 !important;
        --completed-bg: #f0f9ff;
        --completed-border: #3b82f6;
    }

    /* Improved Tab Container - Better spacing for all screen sizes */
    .invest-tab-container {
        display: flex;
        gap: 0.5rem;
        background: #f3f4f6;
        padding: 0.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }

    /* Hide scrollbar for cleaner look on desktop */
    .invest-tab-container::-webkit-scrollbar {
        height: 3px;
    }

    .invest-tab-container::-webkit-scrollbar-track {
        background: #e5e7eb;
        border-radius: 10px;
    }

    .invest-tab-container::-webkit-scrollbar-thumb {
        background: var(--primary-green);
        border-radius: 10px;
    }

    .invest-tab-btn {
        flex: 0 0 auto;
        padding: 0.75rem 1.25rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: transparent;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    /* Tablet adjustments */
    @media (max-width: 768px) {
        .invest-tab-btn {
            padding: 0.65rem 1rem;
            font-size: 0.8rem;
            gap: 0.4rem;
        }
        
        .invest-tab-btn span {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
    }

    /* Mobile adjustments - Make tabs smaller to fit */
    @media (max-width: 480px) {
        .invest-tab-container {
            gap: 0.35rem;
            padding: 0.4rem;
        }
        
        .invest-tab-btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.7rem;
            gap: 0.3rem;
        }
        
        .invest-tab-btn iconify-icon {
            font-size: 0.9rem;
        }
        
        .invest-tab-btn span {
            font-size: 0.6rem;
            padding: 0.15rem 0.35rem;
        }
    }

    /* Very small screens - Show icons only? Alternative approach */
    @media (max-width: 380px) {
        .invest-tab-btn .tab-text {
            display: none;
        }
        
        .invest-tab-btn {
            padding: 0.5rem;
        }
        
        .invest-tab-btn iconify-icon {
            font-size: 1.1rem;
        }
    }

    .invest-tab-btn:hover {
        background: #e5e7eb;
        color: #374151;
    }

    .invest-tab-btn.active {
        background: white;
        color: var(--dark-green) !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .invest-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 20px;
        padding: 1rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .invest-card:hover {
        border-color: var(--primary-green) !important;
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(158, 221, 5, 0.15);
    }

    .invest-card.completed-ready {
        background: var(--completed-bg);
        border: 2px solid var(--completed-border);
    }

    .invest-card.completed-ready:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(59, 130, 246, 0.2);
    }

    .ready-badge {
        position: absolute;
        top: -12px;
        right: 20px;
        color: var(--dark-green) !important;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: bold;
        box-shadow: 0 2px 8px rgba(158, 221, 5, 0.4);
        z-index: 10;
        background: linear-gradient(135deg, var(--primary-green) !important, var(--accent-green) !important);
    }

    .withdraw-btn {
        background: var(--primary-green) !important;
        color: var(--dark-green) !important;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        width: 100%;
        font-size: 1rem;
    }

    .withdraw-btn:hover {
        background: var(--accent-green) !important;
        transform: translateY(-1px);
    }

    .withdraw-btn-ready {
        color: var(--dark-green) !important;
        background: linear-gradient(135deg, var(--primary-green) !important, var(--accent-green) !important);
    }

    .withdraw-btn-ready:hover {
        background: linear-gradient(135deg, var(--accent-green) !important, var(--primary-green) !important);
    }

    .take-profit-btn {
        background: #fbbf24;
        color: #78350f;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        width: 100%;
        margin-bottom: 0.75rem;
    }

    .take-profit-btn:hover {
        background: #f59e0b;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 16px;
        border: 2px dashed #e5e7eb;
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background-color: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background-color: var(--primary-green) !important;
        transition: width 0.3s ease;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .alert-success {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }

    .alert-error {
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }

    .total-return {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--primary-green) !important;
    }

    .trading-style-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .feature-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        background: #f3f4f6;
        border-radius: 20px;
        font-size: 11px;
        color: #374151;
        margin-right: 4px;
        margin-bottom: 4px;
    }

    .feature-tag i, .feature-tag iconify-icon {
        font-size: 12px;
    }

    .asset-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        background: #e5e7eb;
        border-radius: 20px;
        font-size: 10px;
        color: #374151;
        margin-right: 4px;
        margin-bottom: 4px;
    }

    button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .plan-name {
        color: var(--dark-green) !important;
    }
</style>

<div class="w-full min-h-screen bg-cover bg-center bg-no-repeat">
    <div class="max-w-7xl mx-auto px-4 md:px-6 py-10 relative z-10">

        <!-- Page Header -->
        <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
            <h5 class="font-semibold text-lg md:text-xl" style="color: var(--dark-green) !important;">My Investments</h5>
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
                <li>-</li>
                <li class="font-medium" style="color: var(--primary-green) !important;">Investment</li>
            </ul>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="alert alert-success" role="alert">
            <iconify-icon icon="ph:check-circle-fill" style="font-size: 1.5rem; margin-right: 8px; vertical-align: middle; color: var(--primary-green) !important;"></iconify-icon>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-error" role="alert">
            <iconify-icon icon="ph:x-circle-fill" style="font-size: 1.5rem; margin-right: 8px; vertical-align: middle;"></iconify-icon>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Tabs - Now properly sized to fit all three tabs -->
        <div class="invest-tab-container">
            <button class="invest-tab-btn active" onclick="switchInvestTab('active')">
                <iconify-icon icon="ph:play-fill"></iconify-icon>
                <span class="tab-text">Active</span>
                <span class="text-white text-xs px-2 py-0.5 rounded-full" style="background: var(--primary-green) !important; color: var(--dark-green) !important;">
                    {{ $activeInvestments->count() }}
                </span>
            </button>
            <button class="invest-tab-btn" onclick="switchInvestTab('completed')">
                <iconify-icon icon="ph:check-circle-fill" style="color:#9EDD05 !important;"></iconify-icon>
                <span class="tab-text">Completed</span>
                @if($completedInvestments->count() > 0)
                <span class="text-white text-xs px-2 py-0.5 rounded-full" style="background: var(--primary-green) !important; color: var(--dark-green) !important;">
                    {{ $completedInvestments->count() }}
                </span>
                @endif
            </button>

            <button class="invest-tab-btn" onclick="window.location.href='{{ route('user.withdrawn.investments') }}'">
                <iconify-icon icon="ph:arrow-right-fill"></iconify-icon>
                <span class="tab-text">Withdrawn</span>
            </button>
        </div>

        <!-- ACTIVE TAB -->
        <div id="activeTab" class="tab-content">
            @if($activeInvestments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($activeInvestments as $investment)
                @php
                    $progress = $investment->progress_percentage;
                    $totalExpectedProfit = ($investment->amount_invested * $investment->plan->interest_rate) / 100;
                    $earnedProfit = ($totalExpectedProfit * $progress) / 100;
                    $alreadyTakenProfit = $investment->withdrawals()->where('type', 'profit')->sum('amount');
                    $availableProfit = $earnedProfit - $alreadyTakenProfit;
                    $maxTakeProfit = $investment->amount_invested >= 12000 ? 30 : 10;
                    $canTakeProfit = $availableProfit > 0 && $alreadyTakenProfit < $maxTakeProfit && $progress < 100;
                    $endDate = \Carbon\Carbon::parse($investment->end_date);
                    $plan = $investment->plan;
                    $features = is_array($plan->features_list) ? $plan->features_list : json_decode($plan->features_list, true) ?? [];
                    $assets = is_array($plan->assets_list) ? $plan->assets_list : json_decode($plan->assets_list, true) ?? [];
                @endphp

                <div class="invest-card">
                    <!-- Plan Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-bold text-lg plan-name">{{ $plan->name }}</h3>
                            @if($plan->trading_style || $plan->risk_level)
                            <div class="flex flex-wrap gap-2 mt-1">
                                @if($plan->trading_style)
                                <span class="trading-style-tag" style="background: rgba(158, 221, 5, 0.15); color: var(--dark-green);">
                                    <iconify-icon icon="ph:chart-line-up-fill"></iconify-icon>
                                    {{ $plan->trading_style }}
                                </span>
                                @endif
                                @if($plan->risk_level)
                                <span class="trading-style-tag" style="background: rgba(158, 221, 5, 0.1); color: var(--dark-green);">
                                    <iconify-icon icon="ph:shield-check-fill"></iconify-icon>
                                    {{ $plan->risk_level }} Risk
                                </span>
                                @endif
                            </div>
                            @endif
                        </div>
                        <span class="px-2 py-1 text-xs font-medium rounded-full" style="background: rgba(158, 221, 5, 0.2); color: var(--dark-green);">
                            <iconify-icon icon="ph:play-fill" class="inline"></iconify-icon> Active
                        </span>
                    </div>

                    <!-- Investment Details -->
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Amount:</span>
                            <span class="font-semibold" style="color: #0C3A30 !important;">${{ number_format($investment->amount_invested, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">ROI:</span>
                            <span class="font-semibold" style="color: #0C3A30 !important;">{{ $plan->interest_rate }}%</span>
                        </div>
                        @if($plan->expected_roi_range)
                        <div class="flex justify-between">
                            <span class="text-gray-500">ROI Range:</span>
                            <span class="font-semibold" style="color: #0C3A30 !important;">{{ $plan->expected_roi_range }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Duration:</span>
                            <span class="font-semibold" style="color: #0C3A30 !important;">
                                {{ $investment->duration_value }} {{ ucfirst($investment->duration_unit) }}
                            </span>
                        </div>
                        @if($plan->min_duration)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Min Duration:</span>
                            <span class="font-semibold" style="color: #0C3A30 !important;">{{ $plan->min_duration }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Progress:</span>
                            <span class="font-semibold" style="color: #0C3A30 !important;">{{ number_format($progress, 1) }}%</span>
                        </div>

                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $progress }}%"></div>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Earned Profit:</span>
                            <span class="font-semibold" style="color: var(--primary-green) !important;">${{ number_format($earnedProfit, 2) }}</span>
                        </div>

                        @if($alreadyTakenProfit > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Profit Taken:</span>
                            <span class="font-semibold" style="color: var(--primary-green) !important;">${{ number_format($alreadyTakenProfit, 2) }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Plan Strategy -->
                    @if($plan->strategy)
                    <div class="mt-3 pt-2 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Strategy:</p>
                        <p class="text-xs text-gray-600">{{ Str::limit($plan->strategy, 100) }}</p>
                    </div>
                    @endif

                    <!-- Assets Traded -->
                    @if(count($assets) > 0)
                    <div class="mt-2">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Assets:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($assets as $asset)
                            <span class="asset-tag">
                                <iconify-icon icon="ph:chart-line-fill"></iconify-icon>
                                {{ $asset }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Plan Features -->
                    @if(count($features) > 0)
                    <div class="mt-2">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Features:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach($features as $feature)
                            <div class="feature-tag">
                                <iconify-icon icon="ph:check-circle-fill" style="color: var(--primary-green);"></iconify-icon>
                                <span>{{ $feature }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    @php
                        $canTakeProfit = \Carbon\Carbon::parse($investment->created_at)->addHours(5)->isPast();
                    @endphp

                    @if($canTakeProfit && $availableProfit > 0 && $alreadyTakenProfit < $maxTakeProfit)
                        <form method="POST" action="{{ route('investments.takeProfit', $investment->id) }}" onsubmit="handleTakeProfit(this)">
                            @csrf
                            <button type="submit" class="take-profit-btn">
                                <iconify-icon icon="ph:currency-dollar-simple" class="inline"></iconify-icon>
                                Take Profit (Max ${{ $maxTakeProfit }})
                            </button>
                        </form>
                    @elseif(!$canTakeProfit)
                        <button type="button" class="take-profit-btn opacity-50 cursor-not-allowed" disabled>
                            Profit available in {{ \Carbon\Carbon::parse($investment->created_at)->addHours(5)->diffForHumans() }}
                        </button>
                    @elseif($availableProfit <= 0)
                        <button type="button" class="take-profit-btn opacity-50 cursor-not-allowed" disabled>
                            No profit available yet
                        </button>
                    @elseif($alreadyTakenProfit >= $maxTakeProfit)
                        <button type="button" class="take-profit-btn opacity-50 cursor-not-allowed" disabled>
                            Max profit limit reached (${{ $maxTakeProfit }})
                        </button>
                    @endif

                    <form method="POST" action="{{ route('investments.withdraw', $investment->id) }}" onsubmit="handleWithdraw(this)">
                        @csrf
                        <button type="submit" class="withdraw-btn">
                            <iconify-icon icon="ph:arrow-square-out" class="inline"></iconify-icon>
                            <span class="btn-text">Withdraw</span>
                        </button>
                    </form>
                    <p class="text-xs text-center text-gray-500 mt-2">
                        ⏱️ Ends {{ $endDate->diffForHumans(now(), ['parts' => 2]) }}
                    </p>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <iconify-icon icon="ph:play-fill" class="text-4xl text-gray-400"></iconify-icon>
                <h6 class="text-lg font-semibold text-gray-700 mb-2">No Active Investments</h6>
                <p class="text-gray-500">Start copy trading to see your active investments here</p>
                <a href="{{ route('copy-trading.index') }}"
                   class="inline-block mt-4 px-4 py-2 rounded-lg transition" 
                   style="background: var(--primary-green) !important; color: var(--dark-green) !important;"
                   onmouseover="this.style.background='var(--accent-green)';" 
                   onmouseout="this.style.background='var(--primary-green)';">
                    Start Copy Trading
                </a>
            </div>
            @endif
        </div>

        <!-- COMPLETED TAB -->
        <div id="completedTab" class="tab-content hidden">
            @if($completedInvestments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($completedInvestments as $investment)
                @php
                    $totalProfit = ($investment->amount_invested * $investment->plan->interest_rate) / 100;
                    $alreadyTakenProfit = $investment->withdrawals()->where('type', 'profit')->sum('amount');
                    $remainingProfit = max($totalProfit - $alreadyTakenProfit, 0);
                    $totalReturn = $investment->amount_invested + $remainingProfit;
                    $plan = $investment->plan;
                    $features = is_array($plan->features_list) ? $plan->features_list : json_decode($plan->features_list, true) ?? [];
                @endphp

                <div class="invest-card completed-ready">
                    

                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-bold text-lg plan-name">{{ $plan->name }}</h3>
                            @if($plan->trading_style || $plan->risk_level)
                            <div class="flex flex-wrap gap-2 mt-1">
                                @if($plan->trading_style)
                                <span class="trading-style-tag" style="background: rgba(158, 221, 5, 0.15); color: var(--dark-green);">
                                    <iconify-icon style="color:#9EDD05 !important;" icon="ph:chart-line-up-fill"></iconify-icon>
                                    {{ $plan->trading_style }}
                                </span>
                                @endif
                                @if($plan->risk_level)
                                <span class="trading-style-tag" style="background: rgba(158, 221, 5, 0.1); color: var(--dark-green);">
                                    <iconify-icon style="color:#9EDD05 !important;" icon="ph:shield-check-fill"></iconify-icon>
                                    {{ $plan->risk_level }} Risk
                                </span>
                                @endif
                            </div>
                            @endif
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full" style=" display:flex; flex-wrap:no-wrap !important; background: rgba(158, 221, 5, 0.2); color: var(--dark-green);">
                            <iconify-icon icon="ph:check-circle-fill" style="color:#9EDD05 !important;" class="inline"></iconify-icon> Completed
                        </span>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Amount:</span>
                            <span class="font-semibold">${{ number_format($investment->amount_invested, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">ROI:</span>
                            <span class="font-semibold" style="color: #0C3A30 !important;">{{ $plan->interest_rate }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Profit:</span>
                            <span class="font-semibold" style="color: #0C3A30 !important;">${{ number_format($totalProfit, 2) }}</span>
                        </div>
                        @if($alreadyTakenProfit > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Taken:</span>
                            <span class="font-semibold" style="color: #0C3A30 !important;">${{ number_format($alreadyTakenProfit, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between border-t border-blue-200 pt-2 mt-2">
                            <span class="font-bold text-gray-700">Total:</span>
                            <span class="total-return" style="color: #0C3A30 !important;">${{ number_format($totalReturn, 2) }}</span>
                        </div>
                    </div>

                    <!-- Plan Features for Completed -->
                    @if(count($features) > 0)
                    <div class="mt-3 pt-2 border-t border-blue-100">
                        <p class="text-xs font-semibold text-gray-500 mb-1">Features:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($features, 0, 3) as $feature)
                            <div class="feature-tag">
                                <iconify-icon icon="ph:check-circle-fill" style="color:#9EDD05 !important;"></iconify-icon>
                                <span>{{ $feature }}</span>
                            </div>
                            @endforeach
                            @if(count($features) > 3)
                            <span class="text-xs text-gray-400">+{{ count($features) - 3 }} more</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="mt-4 pt-3 border-t border-blue-200">
                        <form method="POST" action="{{ route('investments.withdraw', $investment->id) }}" onsubmit="handleWithdraw(this)">
                            @csrf
                            <button type="submit" class="withdraw-btn withdraw-btn-ready">
                                <iconify-icon icon="ph:currency-dollar-simple" class="inline"></iconify-icon>
                                <span class="btn-text">Withdraw ${{ number_format($totalReturn, 2) }}</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <iconify-icon icon="ph:check-circle-fill" class="text-4xl text-gray-400"></iconify-icon>
                <h6 class="text-lg font-semibold text-gray-700 mb-2">No Completed Investments</h6>
                <p class="text-gray-500">Completed investments will appear here</p>
            </div>
            @endif
        </div>

    </div>
</div>

<script>
function switchInvestTab(tab) {
    document.querySelectorAll('.invest-tab-btn').forEach(btn => btn.classList.remove('active'));

    const buttons = document.querySelectorAll('.invest-tab-btn');
    if (tab === 'active') buttons[0].classList.add('active');
    if (tab === 'completed') buttons[1].classList.add('active');

    document.getElementById('activeTab').classList.add('hidden');
    document.getElementById('completedTab').classList.add('hidden');

    document.getElementById(`${tab}Tab`).classList.remove('hidden');
}

function handleWithdraw(form) {
    const button = form.querySelector('button');
    const text = button.querySelector('.btn-text');

    if (button.disabled) return false;

    button.disabled = true;
    text.innerText = 'Processing...';
    button.style.opacity = '0.7';
    button.style.cursor = 'not-allowed';

    return true;
}

function handleTakeProfit(form) {
    const button = form.querySelector('button');
    
    if (button.disabled) return false;
    
    button.disabled = true;
    button.innerHTML = '<span class="loading-spinner"></span> Processing...';
    button.style.opacity = '0.7';
    button.style.cursor = 'not-allowed';
    
    return true;
}
</script>

<style>
.loading-spinner {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(12, 58, 48, 0.3);
    border-top-color: #0C3A30;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-right: 8px;
    vertical-align: middle;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endsection