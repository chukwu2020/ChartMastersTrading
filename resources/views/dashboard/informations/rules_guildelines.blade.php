@extends('layout.user')

@section('content')
@php
    use App\Models\Plan;
    use App\Models\Investment;
    
    $plans = Plan::orderBy('minimum_amount', 'asc')->get();
    $userInvestments = Investment::where('user_id', auth()->id())->with('plan')->get();
    $highestPlan = $userInvestments->sortByDesc(fn($i) => $i->plan->minimum_amount)->first();
    $currentLevel = $highestPlan ? $plans->search(fn($p) => $p->id == $highestPlan->plan_id) : null;
    $nextPlan = ($currentLevel !== null && $currentLevel + 1 < $plans->count()) ? $plans->get($currentLevel + 1) : null;
@endphp

<div class="w-full min-h-screen bg-cover bg-center bg-no-repeat">
    <div class="max-w-7xl mx-auto px-4 md:px-6 py-10 relative z-10">
        
        <!-- Header -->
        <div class="flex items-center justify-between gap-2 mb-6">
            <h5 class="font-semibold text-lg md:text-xl" style="color: #0C3A30;">Rules & Guidelines</h5>
            <ul class="flex items-center gap-[6px]">
                <li class="font-medium">
                    <a href="{{ route('user_dashboard') }}" class="flex items-center gap-2" 
                        style="color: #0C3A30;"
                        onmouseover="this.style.color='#9EDD05';" 
                        onmouseout="this.style.color='#0C3A30';">
                        <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                        Dashboard
                    </a>
                </li>
                <li>-</li>
                <li class="font-medium" style="color: #9EDD05;">R & G</li>
            </ul>
        </div>

        <!-- Hero Section -->
      <!-- Hero Section -->
<div class="bg-gradient-to-r from-[#0C3A30] to-[#1a5a4a] rounded-2xl p-8 mb-8 text-white">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">

        <!-- LEFT CONTENT -->
        <div>
            <h1 class="text-2xl md:text-3xl font-bold mb-2">
                Investment Rules & Guidelines
            </h1>
            <p class="text-emerald-200">
                Understanding how our tiered investment system works
            </p>
        </div>

        <!-- RIGHT CARD -->
        @if($currentLevel !== null)
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-xl px-5 py-3 min-w-[180px] text-center shadow-sm">
                
                <p class="text-xs uppercase tracking-wide text-emerald-200 mb-1">
                    Your Current Level
                </p>

                <p class="text-lg font-semibold text-white">
                    {{ $highestPlan->plan->name }}
                </p>

            </div>
        @endif

    </div>
</div>

        <!-- Core Rules -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100" style=" background-image: url('assets/images/hero/hero-image-1.svg'); background-size: cover; background-position: center;">
                <div class="w-10 h-10 rounded-lg bg-[#9EDD05]/20 flex items-center justify-center mb-4">
                    <iconify-icon icon="ph:shield-check" class="text-2xl" style="color: #9EDD05;"></iconify-icon>
                </div>
                <h3 class="font-bold text-lg mb-2" style="color: #0C3A30;">Progressive Access</h3>
                <p class="text-gray-600 text-sm">Each investment plan unlocks new features and withdrawal capabilities. Start from the base level and grow your way up.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100"style=" background-image: url('assets/images/hero/hero-image-1.svg'); background-size: cover; background-position: center;">
                <div class="w-10 h-10 rounded-lg bg-[#9EDD05]/20 flex items-center justify-center mb-4">
                    <iconify-icon icon="ph:currency-circle-dollar" class="text-2xl" style="color: #9EDD05;"></iconify-icon>
                </div>
                <h3 class="font-bold text-lg mb-2" style="color: #0C3A30;">Withdrawal Eligibility</h3>
                <p class="text-gray-600 text-sm">Withdrawal access is granted based on your investment tier. Higher tiers provide more flexibility and faster processing.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100" style=" background-image: url('assets/images/hero/hero-image-1.svg'); background-size: cover; background-position: center;">
                <div class="w-10 h-10 rounded-lg bg-[#9EDD05]/20 flex items-center justify-center mb-4">
                    <iconify-icon icon="ph:chart-line-up" class="text-2xl" style="color: #9EDD05;"></iconify-icon>
                </div>
                <h3 class="font-bold text-lg mb-2" style="color: #0C3A30;">Risk Management</h3>
                <p class="text-gray-600 text-sm">Our tiered structure ensures you understand the platform before accessing advanced features and withdrawals.</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100" style=" background-image: url('assets/images/hero/hero-image-1.svg'); background-size: cover; background-position: center;">
                <div class="w-10 h-10 rounded-lg bg-[#9EDD05]/20 flex items-center justify-center mb-4">
                    <iconify-icon icon="ph:headset" class="text-2xl" style="color: #9EDD05;"></iconify-icon>
                </div>
                <h3 class="font-bold text-lg mb-2" style="color: #0C3A30;">24/7 Support</h3>
                <p class="text-gray-600 text-sm">Dedicated support team available around the clock to assist with any questions or concerns.</p>
            </div>
        </div>

        <!-- Investment Plans -->
<h2 class="text-xl font-bold mb-6" style="color: #0C3A30;">Investment Plans</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    @php
        $sortedPlans = $plans->sortBy('minimum_amount');
        $secondLowestPlan = $sortedPlans->skip(1)->first();
    @endphp

    @foreach($plans as $index => $plan)
        @php
            $isActive = $highestPlan && $highestPlan->plan_id == $plan->id;
            $isLocked = $currentLevel !== null && $plans->search(fn($p) => $p->id == $highestPlan->plan_id) < $index;
            $features = $plan->features_list;
            $assets = $plan->assets_list;
            $isPopular = $plan->popular_badge || ($secondLowestPlan && $plan->id == $secondLowestPlan->id);
        @endphp
        
        <div class="bg-white rounded-xl shadow-sm border relative {{ $isActive ? 'border-[#9EDD05] ring-2 ring-[#9EDD05]/20' : 'border-gray-200' }} overflow-hidden hover:shadow-md transition-all" style=" background-image: url('assets/images/hero/hero-image-1.svg'); background-size: cover; background-position: center;">
            @if($isPopular)
<div class="absolute top-0 left-0 z-10">
    <div class="popular-badge">
        ⭐ Popular
    </div>
</div>
@endif

<style>
    .popular-badge {
    background: linear-gradient(to right, #9EDD05, #8AC304) !important;
    color: #0C3A30;
    font-size: 12px;
    font-weight: bold;
    padding: 4px 16px;
    border-bottom-right-radius: 12px;
    display: inline-block;
}
</style>
            
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-xl" style="color: #0C3A30;">{{ $plan->name }}</h3>
                                <p class="text-sm text-gray-500 mt-1">Level {{ $index + 1 }}</p>
                            </div>
                           
                            
           
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-2xl font-bold" style="color: #0C3A30;">${{ number_format($plan->minimum_amount) }}</p>
                            <p class="text-xs text-gray-500">Minimum investment</p>
                        </div>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Return Rate</span>
                                <span class="font-semibold" style="color: #9EDD05;">{{ $plan->interest_rate }}%</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Duration</span>
                                <span class="font-semibold" style="color: #0C3A30;">{{ $plan->duration }} {{ ucfirst($plan->duration_unit) }}</span>
                            </div>
                            @if($plan->trading_style)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Style</span>
                                <span class="font-semibold">{{ $plan->trading_style }}</span>
                            </div>
                            @endif
                            @if($plan->risk_level)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Risk</span>
                                <span class="font-semibold">{{ $plan->risk_level }}</span>
                            </div>
                            @endif
                        </div>
                        
                        @if(count($features) > 0)
                            <div class="mb-4">
                                <p class="text-xs font-semibold text-gray-500 mb-2">FEATURES</p>
                                <div class="space-y-1">
                                    @foreach(array_slice($features, 0, 3) as $feature)
                                    <div class="flex items-center gap-2 text-sm">
                                        <iconify-icon icon="ph:check-circle" class="text-sm" style="color: #9EDD05;"></iconify-icon>
                                        <span class="text-gray-600">{{ $feature }}</span>
                                    </div>
                                    @endforeach
                                    @if(count($features) > 3)
                                        <p class="text-xs text-gray-400 mt-1">+{{ count($features) - 3 }} more features</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="pt-4 border-t border-gray-100">
                            @if($isActive)
                                <div class="text-center py-2 rounded-lg bg-[#9EDD05]/10 text-[#0C3A30] font-semibold text-sm">
                                    Currently Active
                                </div>
                            @elseif($isLocked)
                                <div class="text-center py-2 rounded-lg bg-gray-100 text-gray-500 text-sm">
                                    Complete lower tier first
                                </div>
                            @else
                                <a href="{{ route('plan.dashboard') }}"  style="background-color: #9EDD05 !important;"
                                   class="block text-center py-2 rounded-lg border-2 border-[#9EDD05] text-[#0C3A30] font-semibold text-sm hover:bg-[#9EDD05] hover:text-white transition-all">
                                    Invest Now
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Withdrawal Rules -->
        <div class="bg-gradient-to-r from-[#0C3A30] to-[#1a5a4a] rounded-2xl p-8 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <iconify-icon icon="ph:cash" class="text-2xl text-[#9EDD05]"></iconify-icon>
                <h2 class="text-xl font-bold text-white">Withdrawal Rules by Tier</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($plans as $index => $plan)
                    <div class="bg-white/10 rounded-xl p-4 border border-white/20" style=" background-image: url('assets/images/hero/hero-image-1.svg'); background-size: cover; background-position: center;" >
                        <p class="font-bold text-white text-sm mb-2">{{ $plan->name }}</p>
                      @php
    $steps = [
        0 => 'No access',
        1 => 'Limited',
        2 => 'Full access'
    ];
    
    $progressWidth = ($index / 2) * 100;
    if($index > 2) $progressWidth = 100;
@endphp

<div class="space-y-1">
    <p class="text-xs text-gray-300">
        @if($index == 0)
             No withdrawal access yet
        @elseif($index == 1)
             Withdraw after completion
        @else
             Full withdrawal access
        @endif
    </p>
    
    @if($index < 2)
    <div class="w-full bg-gray-700 rounded-full h-1">
        <div class="bg-green-500 h-1 rounded-full transition-all duration-500" 
             style="width: {{ $progressWidth }}%"></div>
    </div>
    <p class="text-[10px] text-gray-500">
        {{ $index == 0 ? 'Need 1 more participation on the next plan' : 'Complete current plan for full access' }}
    </p>
    @endif
</div>               </div>
                @endforeach
            </div>
            
            <div class="mt-6 bg-white/5 rounded-xl p-4">
                <p class="text-sm text-gray-300">
                    <span class="font-semibold text-white">Note:</span> Withdrawals are processed after the investment duration is complete. Higher tiers may have reduced waiting periods and lower fees.

                     Withdrawals are processed only after the applicable 10% administrative fee has been paid. this fee is not deducted from your available asccount balance and must be paid separately using and external payment method.
                    Once the administrative fee has been confirmed, your withdrawal request will be processed .
                </p>
            </div>
                    
        
        </div>

        <!-- User Progress -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8" style=" background-image: url('assets/images/hero/hero-image-1.svg'); background-size: cover; background-position: center;">
            <div class="flex items-center gap-3 mb-4">
                <iconify-icon icon="ph:trend-up" class="text-2xl" style="color: #9EDD05;"></iconify-icon>
                <h2 class="text-xl font-bold" style="color: #0C3A30;">Your Progress</h2>
            </div>
            
            @if($currentLevel !== null)
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Current Plan</span>
                        <span class="font-semibold" style="color: #0C3A30;">{{ $highestPlan->plan->name }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full" style="width: {{ (($currentLevel + 1) / $plans->count()) * 100 }}%; background: #9EDD05;"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Level {{ $currentLevel + 1 }} of {{ $plans->count() }}</p>
                </div>
                
                @if($nextPlan)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-2">Next Tier: <span class="font-semibold" style="color: #0C3A30;">{{ $nextPlan->name }}</span></p>
                        <p class="text-xs text-gray-500">Minimum investment: ${{ number_format($nextPlan->minimum_amount) }}</p>
                        <a href="{{ route('plan.dashboard') }}" class="inline-block mt-3 text-sm font-semibold" style="color: #9EDD05;">Upgrade Now →</a>
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <p class="text-gray-600 mb-3">You haven't started any investment yet</p>
                    <a href="{{ route('plan.dashboard') }}" class="inline-block px-6 py-2 rounded-lg" style="background: #9EDD05; color: #0C3A30; font-weight: 600;">
                        Start Your First Investment
                    </a>
                </div>
            @endif
        </div>

        <!-- FAQ Section -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100" style=" background-image: url('assets/images/hero/hero-image-1.svg'); background-size: cover; background-position: center;">
            <div class="flex items-center gap-3 mb-6">
                <iconify-icon icon="ph:question" class="text-2xl" style="color: #9EDD05;"></iconify-icon>
                <h2 class="text-xl font-bold" style="color: #0C3A30;">Frequently Asked Questions</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4"  >
                @php
                    $faqs = [
                        ['q' => 'Why is withdrawal not available on the first plan?', 'a' => 'The first tier is designed as a learning phase to help you understand the platform and daily profit mechanics without the complexity of withdrawals.'],
                        ['q' => 'When can I withdraw my profits?', 'a' => 'Withdrawal eligibility begins from the second tier onward. Each plan has specific withdrawal rules based on your investment level.'],
                        ['q' => 'How do I upgrade to a higher plan?', 'a' => 'Simply invest in a higher-tier plan. Your existing investments remain active and you\'ll gain access to additional benefits.'],
                        ['q' => 'Are there any fees for withdrawals?', 'a' => 'Fees vary by plan tier. Higher tiers have reduced or zero fees. Check each plan\'s details for specific fee structures.'],
                        ['q' => 'Can I withdraw before the duration ends?', 'a' => 'Early withdrawal options are available on select plans. Higher tiers offer more flexibility with early withdrawal.'],
                        ['q' => 'What happens if I invest in multiple plans?', 'a' => 'You can hold multiple investments across different tiers. Your highest active plan determines your withdrawal privileges.'],
                    ];
                @endphp
                
                @foreach($faqs as $faq)
                <details class="group">
                    <summary class="flex justify-between items-center cursor-pointer p-3 rounded-lg hover:bg-gray-50 transition">
                        <span class="font-medium text-sm" style="color: #0C3A30;">{{ $faq['q'] }}</span>
                        <iconify-icon icon="ph:plus" class="text-lg group-open:hidden" style="color: #9EDD05;"></iconify-icon>
                        <iconify-icon icon="ph:minus" class="text-lg hidden group-open:inline" style="color: #9EDD05;"></iconify-icon>
                    </summary>
                    <p class="text-sm text-gray-600 p-3 pt-0">{{ $faq['a'] }}</p>
                </details>
                @endforeach
            </div>
        </div>
        
    </div>
</div>
<style>
     
    .custom-badge {
        position: absolute;
        top: 0;
        left: 0;
        background: linear-gradient(135deg, #ff6b6b, #ff4757);
        color: white;
        padding: 6px 16px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        border-radius: 16px 0 16px 0;
        box-shadow: 0 4px 12px rgba(255, 71, 87, 0.4);
        z-index: 5;
        text-transform: uppercase;
    }
</style>
@endsection