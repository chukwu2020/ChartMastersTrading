@php
    use App\Models\WithdrawalCard;
    use App\Models\CopyTradingRequest;
    use App\Models\Strategy;
    
    $cardExists = auth()->check() ? WithdrawalCard::where('user_id', auth()->id())->exists() : false;
    $pendingCount = auth()->check() ? CopyTradingRequest::where('user_id', auth()->id())->where('status', 'pending')->count() : 0;
    $activeStrategiesCount = auth()->check() ? Strategy::active()->count() : 0;
@endphp

<aside class="sidebar" style="background-image: url('/assets/images/hero/hero-image-1.svg'); background-position: center; background-size: cover;">

    <!-- Close Button -->
    <button type="button" class="sidebar-close-btn lg:hidden">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>

    <!-- Logo -->
    <div class="logo-container lg:hidden">
        <img src="/assets/images/chartmasterbrandname1.png"  
             alt="ChartMasters Circle" 
             class="brand-logo">
    </div>

    <!-- Sidebar Menu -->
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu">
            
            <!-- MAIN SECTION -->
            <li class="menu-section">
                <div class="menu-section-title">
                    <iconify-icon icon="ph:compass-bold"></iconify-icon>
                    <span>Main</span>
                </div>
                <ul class="menu-items">
                    <li>
                        <a href="{{ route('user_dashboard') }}" class="menu-link {{ request()->routeIs('user_dashboard') ? 'active' : '' }}">
                            <iconify-icon icon="ph:house-bold"></iconify-icon>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user_live') }}" class="menu-link {{ request()->routeIs('user_live') ? 'active' : '' }}">
                            <iconify-icon icon="ph:chart-line-bold"></iconify-icon>
                            <span>Live Trading</span>
                            <span class="live-badge">LIVE</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- TRADING SECTION -->
            <li class="menu-section">
                <div class="menu-section-title">
                    <iconify-icon icon="ph:trend-up-bold"></iconify-icon>
                    <span>Trading</span>
                </div>
                <ul class="menu-items">
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="menu-link dropdown-trigger {{ request()->routeIs('copy-trading.*') ? 'active' : '' }}">
                            <iconify-icon icon="ph:copy-bold"></iconify-icon>
                            <span>Copy Trading</span>
                            @if($pendingCount > 0)
                                <span class="badge-pending">{{ $pendingCount }}</span>
                            @endif
                            <iconify-icon icon="ph:caret-down-bold" class="dropdown-arrow"></iconify-icon>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('copy-trading.index') }}" class="dropdown-link {{ request()->routeIs('copy-trading.index') ? 'active' : '' }}">
                                    <iconify-icon icon="ph:play-bold"></iconify-icon>
                                    <span>Start Copying</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('copy-trading.history') }}" class="dropdown-link {{ request()->routeIs('copy-trading.history') ? 'active' : '' }}">
                                    <iconify-icon icon="ph:clock-bold"></iconify-icon>
                                    <span>Copy History</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('user.investments') }}" class="menu-link {{ request()->routeIs('user.investments') ? 'active' : '' }}">
                            <iconify-icon icon="ph:chart-pie-bold"></iconify-icon>
                            <span>My Investments</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('plan.dashboard') }}" class="menu-link {{ request()->routeIs('plan.dashboard') ? 'active' : '' }}">
                            <iconify-icon icon="ph:list-numbers-bold"></iconify-icon>
                            <span>Investment Plans</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- 🆕 LEARNING SECTION - Trading Courses -->
            <li class="menu-section">
                <div class="menu-section-title">
                    <iconify-icon icon="ph:graduation-cap-bold"></iconify-icon>
                    <span>Learning</span>
                </div>
                <ul class="menu-items">
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="menu-link dropdown-trigger {{ request()->routeIs('strategies.*') ? 'active' : '' }}">
                            <iconify-icon icon="ph:book-open-bold"></iconify-icon>
                            <span>Trading Courses</span>
                            @if($activeStrategiesCount > 0)
                                <span class="badge-new">{{ $activeStrategiesCount }}</span>
                            @endif
                            <iconify-icon icon="ph:caret-down-bold" class="dropdown-arrow"></iconify-icon>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('strategies.strategyindex') }}" class="dropdown-link {{ request()->routeIs('strategies.index') ? 'active' : '' }}">
                                    <iconify-icon icon="ph:books-bold"></iconify-icon>
                                    <span>All Courses</span>
                                </a>
                            </li>
                            @php
                                $activeEnrollments = auth()->check() ? \App\Models\StrategyEnrollment::where('user_id', auth()->id())
                                    ->where('status', 'active')
                                    ->where(function($q) {
                                        $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                                    })
                                    ->count() : 0;
                            @endphp
                            @if($activeEnrollments > 0)
                            <li>
                                <a href="{{ route('strategies.strategyindex') }}#my-courses" class="dropdown-link">
                                    <iconify-icon icon="ph:play-circle-bold"></iconify-icon>
                                    <span>My Courses</span>
                                    <span class="badge-active">{{ $activeEnrollments }}</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </li>
                </ul>
            </li>

            <!-- FINANCE SECTION -->
            <li class="menu-section">
                <div class="menu-section-title">
                    <iconify-icon icon="ph:bank-bold"></iconify-icon>
                    <span>Finance</span>
                </div>
                <ul class="menu-items">
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="menu-link dropdown-trigger {{ request()->routeIs('user.deposit*') ? 'active' : '' }}">
                            <iconify-icon icon="ph:arrow-down-bold"></iconify-icon>
                            <span>Deposits</span>
                            <iconify-icon icon="ph:caret-down-bold" class="dropdown-arrow"></iconify-icon>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('user.deposit') }}" class="dropdown-link {{ request()->routeIs('user.deposit') ? 'active' : '' }}">
                                    <iconify-icon icon="ph:plus-circle-bold"></iconify-icon>
                                    <span>Make Deposit</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.deposit-history') }}" class="dropdown-link {{ request()->routeIs('user.deposit-history') ? 'active' : '' }}">
                                    <iconify-icon icon="ph:clock-bold"></iconify-icon>
                                    <span>Deposit History</span>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="javascript:void(0)" class="menu-link dropdown-trigger {{ request()->routeIs('user.withdrawals*') ? 'active' : '' }}">
                            <iconify-icon icon="ph:arrow-up-bold"></iconify-icon>
                            <span>Withdrawals</span>
                            <iconify-icon icon="ph:caret-down-bold" class="dropdown-arrow"></iconify-icon>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ route('user.withdraw.form') }}" class="dropdown-link {{ request()->routeIs('user.withdraw.form') ? 'active' : '' }}">
                                    <iconify-icon icon="ph:arrow-square-out-bold"></iconify-icon>
                                    <span>Withdraw Funds</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('user.withdrawals.list') }}" class="dropdown-link {{ request()->routeIs('user.withdrawals.list') ? 'active' : '' }}">
                                    <iconify-icon icon="ph:list-bold"></iconify-icon>
                                    <span>Withdrawal History</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>

            <!-- CARD ACTION -->
            @auth
            <div class="card-action">
                @if (!$cardExists)
                    <form action="{{ route('withdrawals.generateCard') }}" method="POST">
                        @csrf
                        <button type="submit" class="action-btn">
                            <iconify-icon icon="ph:credit-card-bold"></iconify-icon>
                            <span>Generate Card</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('withdrawals.view-card') }}" class="action-btn">
                        <iconify-icon icon="ph:credit-card-bold"></iconify-icon>
                        <span>View Your Card</span>
                    </a>
                @endif
            </div>
            @endauth

            <!-- RESOURCES SECTION -->
            <li class="menu-section">
                <div class="menu-section-title">
                    <iconify-icon icon="ph:book-open-bold"></iconify-icon>
                    <span>Resources</span>
                </div>
                <ul class="menu-items">
                    <li>
                        <a href="{{ route('rules.regulations') }}" class="menu-link {{ request()->routeIs('rules.regulations') ? 'active' : '' }}">
                            <iconify-icon icon="ph:shield-check-bold"></iconify-icon>
                            <span>Rules & Guidelines</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('dashboardpayouts') }}" class="menu-link {{ request()->routeIs('dashboardpayouts') ? 'active' : '' }}">
                            <iconify-icon icon="mdi:comment-text"></iconify-icon>
                            <span>Testimonials</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.psychology') }}" class="menu-link {{ request()->routeIs('user.psychology') ? 'active' : '' }}">
                            <iconify-icon icon="ph:brain-bold"></iconify-icon>
                            <span>Psychology</span>
                            <span class="badge-read">READ</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('termsprivacy') }}" class="menu-link {{ request()->routeIs('termsprivacy') ? 'active' : '' }}">
                            <iconify-icon icon="ph:file-text-bold"></iconify-icon>
                            <span>Privacy & Terms</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- SETTINGS SECTION with Logout -->
            <li class="menu-section">
                <div class="menu-section-title">
                    <iconify-icon icon="ph:gear-bold"></iconify-icon>
                    <span>Settings</span>
                </div>
                <ul class="menu-items">
                    <li>
                        <a href="{{ route('profile.show') }}" class="menu-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                            <iconify-icon icon="ph:user-circle-bold"></iconify-icon>
                            <span>Profile Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('user.kyc.upload') }}" class="menu-link {{ request()->routeIs('user.kyc.*') ? 'active' : '' }}">
                            <iconify-icon icon="ph:identification-badge-bold"></iconify-icon>
                            <span>KYC Verification</span>
                        </a>
                    </li>
                </ul>
                
                <!-- Logout Button inside Settings section -->
                <div class="logout-wrapper" style="margin-bottom: 6rem !important;">
                    <a href="{{ route('signout') }}" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="logout-btn">
                        <iconify-icon icon="ph:sign-out-bold"></iconify-icon>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" method="POST" action="{{ route('signout') }}" class="hidden">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</aside>

<style>
    :root {
        --primary-green: #9EDD05;
        --dark-green: #0C3A30;
        --accent-green: #8AC304;
        --hover-bg: rgba(158, 221, 5, 0.08);
        --active-bg: #9EDD05;
    }

    .sidebar {
        width: 260px;
        background-color: rgba(255, 255, 255, 0.96);
        color: var(--dark-green);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        position: fixed;
        left: 0;
        top: 0;
        height: 100vh;
        z-index: 1100;
        transition: all 0.3s ease;
        overflow-y: auto;
        border-right: 1px solid rgba(158, 221, 5, 0.15);
    }

    /* Scrollbar */
    .sidebar::-webkit-scrollbar {
        width: 3px;
    }
    .sidebar::-webkit-scrollbar-track {
        background: rgba(158, 221, 5, 0.05);
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: var(--primary-green);
        border-radius: 3px;
    }

    .logout-wrapper {
        margin-top: 12px;
        padding-top: 8px;
        border-top: 1px solid rgba(158, 221, 5, 0.15);
    }

    .logout-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 16px;
        margin: 0 8px;
        border-radius: 8px;
        text-decoration: none;
        color: #ef4444;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s ease;
        background: rgba(239, 68, 68, 0.08);
    }

    .logout-btn:hover {
        background: #ef4444;
        color: white;
        transform: translateX(4px);
    }

    .logout-btn iconify-icon {
        font-size: 1rem;
    }

    /* Logo */
    .logo-container {
        padding: 12px 10px;
        border-bottom: 3px solid rgba(158, 221, 5, 0.15);
        margin-top: 26px;
    }
    .brand-logo {
        width: 280px !important;
        height: auto;
        display: block;
        margin-top: 4rem;
    }

    /* Menu Sections */
    .menu-section {
        margin-bottom: 20px;
    }
    .menu-section-title {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 1px 16px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #9ca3af;
    }
    .menu-section-title iconify-icon {
        font-size: 14px;
        color: var(--primary-green);
    }
    .menu-items {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    /* Menu Links */
    .menu-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 16px;
        margin: 2px 12px;
        border-radius: 8px;
        text-decoration: none;
        color: #4b5563;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s ease;
        position: relative;
    }
    .menu-link iconify-icon {
        font-size: 1.1rem;
        width: 20px;
        color: #6b7280;
    }
    .menu-link:hover {
        background: var(--hover-bg);
        transform: translateX(4px);
        color: var(--dark-green);
    }
    .menu-link:hover iconify-icon {
        color: var(--primary-green);
    }
    .menu-link.active {
        background: var(--active-bg);
        color: var(--dark-green);
        font-weight: 600;
    }
    .menu-link.active iconify-icon {
        color: var(--dark-green);
    }

    /* Badges */
    .badge-pending, .badge-read, .badge-new, .badge-active {
        margin-left: auto;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        font-size: 0.6rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 20px;
    }
    .badge-new {
        background: linear-gradient(135deg, var(--primary-green), var(--accent-green));
        color: var(--dark-green);
    }
    .badge-active {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    .live-badge {
        margin-left: auto;
        background: #ef4444;
        color: white;
        font-size: 0.6rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 20px;
        animation: pulse 1.5s infinite;
    }
    .badge-read {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* Dropdown */
    .dropdown-trigger {
        cursor: pointer;
        justify-content: space-between;
    }
    .dropdown-trigger .dropdown-arrow {
        font-size: 0.75rem;
        transition: transform 0.2s ease;
        margin-left: auto;
    }
    .dropdown-trigger .dropdown-arrow.rotated {
        transform: rotate(180deg);
    }

    .sidebar-menu-area {
        flex: 1;
        overflow-y: auto;
        padding-bottom: 20px;
    }

    .sidebar-menu {
        display: flex;
        flex-direction: column;
        min-height: 100%;
        padding-bottom: 20px;
    }

    .dropdown-menu {
        list-style: none;
        padding: 4px 0 4px 44px;
        margin: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.25s ease-out;
    }
    .dropdown-menu.open {
        max-height: 300px;
    }
    .dropdown-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 12px;
        margin: 2px 0;
        border-radius: 6px;
        text-decoration: none;
        color: #6b7280;
        font-size: 0.8rem;
        transition: all 0.2s ease;
        position: relative;
    }
    .dropdown-link:hover {
        background: var(--hover-bg);
        transform: translateX(4px);
        color: var(--dark-green);
    }
    .dropdown-link.active {
        color: var(--primary-green);
        font-weight: 600;
    }
    .dropdown-link iconify-icon {
        font-size: 0.85rem;
    }
    .dropdown-link .badge-active {
        margin-left: auto;
        padding: 1px 5px;
        font-size: 0.55rem;
    }

    /* Card Action */
    .card-action {
        margin: 16px 12px 20px;
        padding: 12px 0;
        border-top: 1px solid rgba(158, 221, 5, 0.15);
        border-bottom: 1px solid rgba(158, 221, 5, 0.15);
    }
    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        background: var(--primary-green);
        color: var(--dark-green);
        padding: 10px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .action-btn:hover {
        background: var(--accent-green);
        transform: translateY(-1px);
    }

    /* Mobile */
    .sidebar-close-btn {
        display: none;
        position: absolute;
        top: 16px;
        right: 16px;
        background: none;
        border: none;
        cursor: pointer;
        z-index: 50;
    }
    .sidebar-close-btn iconify-icon {
        font-size: 24px;
        color: var(--primary-green);
    }

    @media (max-width: 1023px) {
        .sidebar {
            transform: translateX(-100%);
            width: 280px;
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .sidebar-close-btn {
            display: block;
        }
    }

    @media (min-width: 1024px) {
        .sidebar-close-btn {
            display: none;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Dropdown Toggle
        document.querySelectorAll(".dropdown-trigger").forEach(trigger => {
            trigger.addEventListener("click", function(e) {
                e.preventDefault();
                const dropdown = this.closest('.dropdown');
                const menu = dropdown.querySelector('.dropdown-menu');
                const arrow = this.querySelector('.dropdown-arrow');
                
                // Close other dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(otherMenu => {
                    if (otherMenu !== menu && otherMenu.classList.contains('open')) {
                        otherMenu.classList.remove('open');
                        const otherArrow = otherMenu.closest('.dropdown')?.querySelector('.dropdown-arrow');
                        if (otherArrow) otherArrow.classList.remove('rotated');
                    }
                });
                
                menu.classList.toggle('open');
                arrow.classList.toggle('rotated');
            });
        });

        // Close button
        const closeBtn = document.querySelector('.sidebar-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                document.querySelector('.sidebar').classList.remove('open');
            });
        }

        // Close sidebar when clicking link on mobile
        const links = document.querySelectorAll('.menu-link:not(.dropdown-trigger), .dropdown-link');
        links.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    document.querySelector('.sidebar').classList.remove('open');
                }
            });
        });

        // Handle hash navigation for My Courses
        if (window.location.hash === '#my-courses') {
            const coursesSection = document.querySelector('#my-courses-section');
            if (coursesSection) {
                coursesSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });

    // Function to open sidebar from hamburger menu
    window.openSidebar = function() {
        document.querySelector('.sidebar').classList.add('open');
    };
</script>