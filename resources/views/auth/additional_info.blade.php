{{-- resources/views/auth/additional-info.blade.php --}}
@extends('layout.app')

@section('content')

<style>
    .additional-info-page {
        padding: 40px 0;
        background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%);
        min-height: 100vh;
    }

    .progress-steps {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
        position: relative;
        gap: 20px;
    }

    .step {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: white;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #94a3b8;
        position: relative;
        z-index: 2;
    }

    .step.active {
        background: #8bc905;
        border-color: #8bc905;
        color: white;
    }

    .step.completed {
        background: #0C3A30;
        border-color: #0C3A30;
        color: white;
    }

    .step-line {
        width: 100px;
        height: 2px;
        background: #e2e8f0;
        position: absolute;
        top: 22px;
    }

    .form-section {
        background: white;
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
        border: 1px solid rgba(139, 201, 5, 0.1);
        transition: all 0.3s ease;
    }

    .form-section:hover {
        box-shadow: 0 25px 70px rgba(139, 201, 5, 0.15);
    }

    .form-section h3 {
        color: #0C3A30;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .form-section .section-desc {
        color: #64748b;
        margin-bottom: 30px;
        font-size: 14px;
    }

    .section-title {
        color: #0C3A30;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #8bc905;
        display: inline-block;
    }

    .option-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .option-card:hover {
        border-color: #8bc905;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(139, 201, 5, 0.2);
    }

    .option-card.selected {
        border-color: #8bc905;
        background: linear-gradient(135deg, rgba(139, 201, 5, 0.05) 0%, rgba(139, 201, 5, 0.1) 100%);
    }

    .option-card input[type="radio"] {
        margin-top: 3px;
        flex-shrink: 0;
    }

    /* Admin Selection Styles */
    .admin-select-wrapper {
        background: white;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 16px;
        margin-top: 16px;
    }

    .admin-option {
        display: flex;
        align-items: center;
        padding: 12px;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .admin-option:hover {
        background: #f8fafc;
        border-color: #8bc905;
    }

    .admin-option.selected {
        background: rgba(139, 201, 5, 0.1);
        border-color: #8bc905;
    }

    .admin-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #8bc905;
        color: #0C3A30;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 12px;
        flex-shrink: 0;
        overflow: hidden;
    }

    .admin-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .admin-info {
        flex: 1;
    }

    .admin-info h6 {
        font-size: 13px;
        font-weight: 600;
        color: #0C3A30;
        margin-bottom: 2px;
    }

    .admin-info p {
        font-size: 11px;
        color: #64748b;
        margin: 0;
    }

    .admin-server-badge {
        background: #e2e8f0;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 9px;
        color: #0C3A30;
    }

    .save-btn {
        background: #8bc905;
        color: white;
        border: none;
        padding: 16px 40px;
        border-radius: 50px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        box-shadow: 0 10px 20px rgba(139, 201, 5, 0.2);
    }

    .save-btn:hover {
        background: #76ad03;
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(139, 201, 5, 0.3);
    }

    .hidden {
        display: none !important;
    }

    .goals-grid,
    .assets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .goal-item,
    .asset-item {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .goal-item:hover,
    .asset-item:hover {
        border-color: #8bc905;
        background: #f8fafc;
    }

    .goal-item.selected,
    .asset-item.selected {
        border-color: #8bc905;
        background: rgba(139, 201, 5, 0.05);
    }

    .goal-item input[type="checkbox"],
    .asset-item input[type="checkbox"] {
        margin-top: 3px;
        flex-shrink: 0;
    }

    .search-wrapper {
        position: relative;
        margin-bottom: 15px;
    }

    .search-wrapper i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        z-index: 1;
    }

    .search-wrapper input {
        padding-left: 35px;
        border-radius: 50px;
        border: 1px solid #e2e8f0;
        height: 45px;
        width: 100%;
    }

    .search-wrapper input:focus {
        border-color: #8bc905;
        box-shadow: 0 0 0 2px rgba(139, 201, 5, 0.2);
        outline: none;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .page-header {
        background: linear-gradient(145deg, #0C3A30, #062018);
        padding: 30px 0;
        margin-bottom: 40px;
        border-bottom: 3px solid #8bc905;
    }

    .page-header h1 {
        color: white;
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }

    .page-header .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 5px 0 0;
    }

    .page-header .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
    }

    .page-header .breadcrumb-item.active {
        color: #8bc905;
    }

    .form-control {
        border: 2px solid #e2e8f0 !important;
        border-radius: 8px !important;
    }

    .form-control:focus {
        border-color: #8bc905 !important;
        box-shadow: 0 0 0 2px rgba(139, 201, 5, 0.2) !important;
        outline: none;
    }

    .win-rate-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .win-rate-badge.high {
        background: #dcfce7;
        color: #166534;
    }

    .win-rate-badge.medium {
        background: #fef9c3;
        color: #a16207;
    }

    .win-rate-badge.low {
        background: #fee2e2;
        color: #b91c1c;
    }

    .profit-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .profit-badge.positive {
        background: #dcfce7;
        color: #166534;
    }

    .profit-badge.negative {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Responsive fixes */
    @media (max-width: 768px) {
        .form-section {
            padding: 25px;
        }

        .goals-grid,
        .assets-grid {
            grid-template-columns: 1fr;
        }

        .step-line {
            width: 50px;
        }

        .admin-option {
            flex-wrap: wrap;
        }

        .admin-option .flex-shrink-0 {
            margin-bottom: 10px;
        }

        .admin-server-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .admin-option {
            position: relative;
            padding-top: 15px;
        }
    }

    .breadcrumb-item::after,
    .breadcrumb-item::before,
    .breadcrumb li::after,
    .breadcrumb li::before {
        content: none !important;
        display: none !important;
    }

    /* Corporate account specific styles */
    .corporate-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .corporate-header .badge {
        background: #8bc905;
        color: #0C3A30;
        font-size: 14px;
        padding: 8px 20px;
        border-radius: 50px;
    }
</style>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('signup') }}">Sign Up</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Complete Profile</li>
                    </ol>
                </nav>
                <h1>Complete Your Trading Profile</h1>
            </div>
        </div>
    </div>
</div>

<!-- Progress Steps -->
<div class="container">
    <div class="progress-steps">
        <div class="step completed">1</div>
        <div class="step-line" style="left: 22%;"></div>
        <div class="step active">2</div>
        <div class="step-line" style="right: 22%;"></div>
        <div class="step">3</div>
    </div>
</div>

<!-- Additional Info Page -->
<div class="additional-info-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <form id="additionalInfoForm" action="{{ route('user.additional.info.save') }}" method="POST">
                    @csrf

                    <!-- Hidden account type - always corporate -->
                    <input type="hidden" name="account_type" value="corporate">

                    <!-- Trading Experience -->
                    <div class="form-section" data-aos="fade-up">
                        <h3>Your Trading Experience - click to select</h3>
                        <p class="section-desc">Help us understand your background</p>

                        <div class="mb-4">
                            <label class="section-title">
                                What is your experience with Stock & Cryptocurrency Investing?
                            </label>

                            <p class="text-muted small mb-3">
                                No matter your level, our team will guide you step-by-step to help you make confident and informed investment decisions.
                            </p>

                            <div class="row g-3 mt-2">

                                <!-- Experienced -->
                                <div class="col-md-4">
                                    <div class="option-card {{ old('stock_experience') == 'yes' ? 'selected' : '' }}" onclick="selectExperience('yes')">
                                        <input type="radio" name="stock_experience" id="exp_yes" value="yes" class="form-check-input" {{ old('stock_experience') == 'yes' ? 'checked' : '' }} required>
                                        <div>
                                            <strong>Experienced Investor</strong>
                                            <p class="small text-muted mb-0">
                                                I have traded Stocks and/or Cryptocurrencies before and understand the market basics.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Intermediate -->
                                <div class="col-md-4">
                                    <div class="option-card {{ old('stock_experience') == 'no' ? 'selected' : '' }}" onclick="selectExperience('no')">
                                        <input type="radio" name="stock_experience" id="exp_no" value="no" class="form-check-input" {{ old('stock_experience') == 'no' ? 'checked' : '' }}>
                                        <div>
                                            <strong>Some Experience</strong>
                                            <p class="small text-muted mb-0">
                                                I have a basic understanding of Stocks or Crypto and have made a few investments.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Beginner -->
                                <div class="col-md-4">
                                    <div class="option-card {{ old('stock_experience') == 'novice' ? 'selected' : '' }}" onclick="selectExperience('novice')">
                                        <input type="radio" name="stock_experience" id="exp_novice" value="novice" class="form-check-input" {{ old('stock_experience') == 'novice' ? 'checked' : '' }}>
                                        <div>
                                            <strong>Beginner</strong>
                                            <p class="small text-muted mb-0">
                                                I'm new to investing, but I'm ready to learn I'll need guidance to get started.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <small class="text-success d-block mt-2">
                                ✔ Don't worry — we provide expert guidance, tools, and support tailored to your experience level.
                            </small>

                            @error('stock_experience')
                            <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Trading frequency (past 2 years)</label>
                                <select name="trading_frequency" class="form-control" required>
                                    <option value="">Select frequency</option>
                                    <option value="0-10" {{ old('trading_frequency') == '0-10' ? 'selected' : '' }}>0-10 times</option>
                                    <option value="11-40" {{ old('trading_frequency') == '11-40' ? 'selected' : '' }}>11-40 times</option>
                                    <option value="40+" {{ old('trading_frequency') == '40+' ? 'selected' : '' }}>More than 40 times</option>
                                </select>
                                @error('trading_frequency') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Transaction volume (past 2 years)</label>
                                <select name="transaction_volume" class="form-control" required>
                                    <option value="">Select volume</option>
                                    <option value="under_10k" {{ old('transaction_volume') == 'under_10k' ? 'selected' : '' }}>Under $10,000</option>
                                    <option value="10k_50k" {{ old('transaction_volume') == '10k_50k' ? 'selected' : '' }}>$10,000 - $50,000</option>
                                    <option value="50k_250k" {{ old('transaction_volume') == '50k_250k' ? 'selected' : '' }}>$50,000 - $250,000</option>
                                    <option value="over_250k" {{ old('transaction_volume') == 'over_250k' ? 'selected' : '' }}>Over $250,000</option>
                                </select>
                                @error('transaction_volume') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Investment Goals -->
                    <div class="form-section" data-aos="fade-up" data-aos-delay="100">
                        <h3>Your Investment Goals</h3>
                        <p class="section-desc">What are you looking to achieve? (Select all that apply)</p>

                        <div class="goals-grid">
                            <div class="goal-item {{ in_array('diversification', old('investment_goal', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('goal_diversification', this)">
                                <input type="checkbox" name="investment_goal[]" value="diversification" id="goal_diversification" class="form-check-input" {{ in_array('diversification', old('investment_goal', [])) ? 'checked' : '' }}>
                                <div>
                                    <strong>Diversification & performance</strong>
                                    <p class="small text-muted mb-0">Access to alternative investments and better trading performance</p>
                                </div>
                            </div>

                            <div class="goal-item {{ in_array('fixed_income', old('investment_goal', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('goal_fixed_income', this)">
                                <input type="checkbox" name="investment_goal[]" value="fixed_income" id="goal_fixed_income" class="form-check-input" {{ in_array('fixed_income', old('investment_goal', [])) ? 'checked' : '' }}>
                                <div>
                                    <strong>Generate consistent fixed income</strong>
                                    <p class="small text-muted mb-0">Lower-return but consistent income distributions</p>
                                </div>
                            </div>

                            <div class="goal-item {{ in_array('venture_capital', old('investment_goal', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('goal_venture', this)">
                                <input type="checkbox" name="investment_goal[]" value="venture_capital" id="goal_venture" class="form-check-input" {{ in_array('venture_capital', old('investment_goal', [])) ? 'checked' : '' }}>
                                <div>
                                    <strong>Venture capital & Long-term wealth</strong>
                                    <p class="small text-muted mb-0">Long-term investments in private companies</p>
                                </div>
                            </div>

                            <div class="goal-item {{ in_array('other', old('investment_goal', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('goal_other', this)">
                                <input type="checkbox" name="investment_goal[]" value="other" id="goal_other" class="form-check-input" {{ in_array('other', old('investment_goal', [])) ? 'checked' : '' }}>
                                <div>
                                    <strong>Other goals</strong>
                                    <p class="small text-muted mb-0">Different investment objectives</p>
                                </div>
                            </div>
                        </div>
                        @error('investment_goal') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Asset Classes -->
                    <div class="form-section" data-aos="fade-up" data-aos-delay="200">
                        <h3>Which asset classes interest you?</h3>
                        <p class="section-desc">Choose one or more options</p>

                        <div class="assets-grid">
                            <div class="asset-item {{ in_array('stocks', old('asset_classes', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('asset_stocks', this)">
                                <input type="checkbox" name="asset_classes[]" value="stocks" id="asset_stocks" class="form-check-input" {{ in_array('stocks', old('asset_classes', [])) ? 'checked' : '' }}>
                                <label for="asset_stocks" class="fw-bold">Stocks</label>
                            </div>

                            <div class="asset-item {{ in_array('crypto', old('asset_classes', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('asset_crypto', this)">
                                <input type="checkbox" name="asset_classes[]" value="crypto" id="asset_crypto" class="form-check-input" {{ in_array('crypto', old('asset_classes', [])) ? 'checked' : '' }}>
                                <label for="asset_crypto" class="fw-bold">Cryptocurrency</label>
                            </div>

                            <div class="asset-item {{ in_array('venture', old('asset_classes', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('asset_venture', this)">
                                <input type="checkbox" name="asset_classes[]" value="venture" id="asset_venture" class="form-check-input" {{ in_array('venture', old('asset_classes', [])) ? 'checked' : '' }}>
                                <label for="asset_venture" class="fw-bold">Venture Capital</label>
                            </div>

                            <div class="asset-item {{ in_array('realestate', old('asset_classes', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('asset_realestate', this)">
                                <input type="checkbox" name="asset_classes[]" value="realestate" id="asset_realestate" class="form-check-input" {{ in_array('realestate', old('asset_classes', [])) ? 'checked' : '' }}>
                                <label for="asset_realestate" class="fw-bold">Real Estate</label>
                            </div>
                        </div>
                        @error('asset_classes') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Corporate Account Section - Admin Selection -->
                    <div class="form-section" data-aos="fade-up" data-aos-delay="300">
                        <div class="corporate-header">
                            <h3>Select Your Trading Admin</h3>
                            <p class="section-desc">Choose an expert trader to copy trades from</p>
                            <span class="badge">Corporate Account</span>
                        </div>

                        <!-- Admin Selection Area -->
                        <div id="corporateAdminArea" class="admin-select-wrapper">

                            <!-- Header with count and search -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 pb-2 gap-2" style="border-bottom: 1px dashed #e2e8f0;">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 style="font-size: 14px; color: #0C3A30; font-weight: 600; margin: 0;">
                                        <i class="ri-user-star-line me-2" style="color: #8bc905;"></i>
                                        Choose your admin to copy trades
                                        @if(isset($feeds) && $feeds->count() > 0)
                                        <span class="badge rounded-pill" style="background: #8bc90520; color: #0C3A30; font-size: 11px; padding: 4px 10px;">
                                            <span id="adminCount">{{ $feeds->count() }}</span> available
                                        </span>
                                        @endif
                                    </h5>
                                </div>

                                <!-- Search Bar -->
                                <div class="search-wrapper" style="position: relative; min-width: 250px;">
                                    <i class="ri-search-line" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                                    <input type="text"
                                        id="adminSearch"
                                        class="form-control form-control-sm"
                                        placeholder="Search by name or server..."
                                        style="padding-left: 35px; height: 38px; border-radius: 20px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 13px;">
                                    <button type="button"
                                        class="btn btn-sm"
                                        onclick="clearSearch()"
                                        style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; padding: 0; display: none;"
                                        id="clearSearchBtn">
                                        <i class="ri-close-line" style="font-size: 16px;"></i>
                                    </button>
                                </div>
                            </div>

                            @if(isset($feeds) && $feeds->count() > 0)
                            <!-- Search Results Count -->
                            <div id="searchResultsInfo" class="mb-2 px-1" style="display: none;">
                                <small style="color: #64748b;">
                                    Found <span id="resultsCount">0</span> matching admins
                                </small>
                            </div>

                            <!-- Admin List with Search Filter -->
                            <div class="row g-3" style="max-height: 450px; overflow-y: auto; padding-right: 4px; scrollbar-width: thin; scrollbar-color: #8bc905 #e2e8f0;" id="adminList">
                                @foreach($feeds as $feed)
                                <div class="col-12 admin-item"
                                    data-admin-name="{{ strtolower($feed->admin_name) }}"
                                    data-server-name="{{ strtolower($feed->server_name) }}"
                                    data-profit="{{ $feed->profit_margin }}">

                                    <div class="admin-option d-flex align-items-center p-3 rounded-3 w-100 {{ old('copy_admin_id') == $feed->id ? 'selected' : '' }}"
                                        onclick="selectCorporateAdmin('{{ $feed->id }}', '{{ addslashes($feed->admin_name) }}', '{{ addslashes($feed->server_name) }}')"
                                        style="background: {{ old('copy_admin_id') == $feed->id ? '#f0f9e8' : '#ffffff' }};
                                                   border: 1px solid {{ old('copy_admin_id') == $feed->id ? '#8bc905' : '#edf2f7' }};
                                                   transition: all 0.2s ease;
                                                   cursor: pointer;">

                                        <!-- Avatar -->
                                        <div class="flex-shrink-0 me-3">
                                            @if($feed->admin_profile_image)
                                            <div class="rounded-circle overflow-hidden border-2"
                                                style="width: 50px; height: 50px; border-color: {{ old('copy_admin_id') == $feed->id ? '#8bc905' : '#e2e8f0' }};">
                                                <img src="{{ asset('storage/admins/'.$feed->admin_profile_image) }}"
                                                    class="w-100 h-100 object-fit-cover">
                                            </div>
                                            @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px; background: #8bc90520; color: #0C3A30; border: 2px solid {{ old('copy_admin_id') == $feed->id ? '#8bc905' : '#e2e8f0' }};">
                                                <span style="font-weight: 700; font-size: 18px;">
                                                    {{ strtoupper(substr($feed->admin_name, 0, 1)) }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                                <h6 style="font-size: 17px; font-weight: 700; color: #0C3A30; margin: 0;">
                                                    {{ $feed->admin_name }}
                                                </h6>

                                                <span class="admin-server-badge ms-2"
                                                    style="background: #8bc905; color: white; padding: 4px 10px; border-radius: 14px; font-size: 12px; font-weight: 600;">
                                                    Copy
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center gap-3 mb-2 flex-wrap">
                                                <span style="font-size: 14px; font-weight: 600; color: #1e293b;">
                                                    <i class="ri-server-line me-1" style="font-size: 12px;"></i>
                                                    {{ $feed->server_name }}
                                                </span>

                                                <!-- Profit Badge -->
                                                <span class="profit-badge {{ $feed->profit_margin >= 0 ? 'positive' : 'negative' }}">
                                                    <i class="ri-money-dollar-circle-line me-1"></i>
                                                    Profit = ${{ number_format(abs($feed->profit_margin), 2) }}
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center gap-4 flex-wrap">
                                                <small style="font-size: 12px; font-weight: 600; color: #475569;">
                                                    <i class="ri-user-line text-black-50 me-1"></i>
                                                    {{ number_format($feed->active_members) }} active
                                                </small>

                                                <small style="font-size: 12px; font-weight: 600; color: #475569;">
                                                    <i class="ri-file-copy-line me-1"></i>
                                                    {{ number_format($feed->copying_trades ?? 0) }} copying
                                                </small>

                                                <!-- Win Rate Badge with Color Classes -->
                                                @php
                                                $winRate = $feed->win_rate ?? 0;
                                                $winRateClass = 'low';
                                                if ($winRate >= 70) {
                                                $winRateClass = 'high';
                                                } elseif ($winRate >= 50) {
                                                $winRateClass = 'medium';
                                                }
                                                @endphp

                                                <span class="win-rate-badge {{ $winRateClass }}">
                                                    <i class="ri-trophy-line me-1"></i>
                                                    win rate = {{ number_format($winRate, 1) }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- No Results Message -->
                            <div id="noSearchResults" class="text-center py-5" style="display: none;">
                                <div class="mb-3">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                        style="width: 64px; height: 64px; background: #8bc90510;">
                                        <i class="ri-search-line" style="color: #8bc905; font-size: 24px;"></i>
                                    </div>
                                </div>
                                <p class="text-dark mb-1" style="font-size: 14px; font-weight: 500;">No matching admins found</p>
                                <p class="text-muted" style="font-size: 12px;">Try adjusting your search terms</p>
                                <button class="btn btn-sm mt-2" onclick="clearSearch()" style="background: #8bc905; color: white; border: none; padding: 6px 16px; border-radius: 20px;">
                                    Clear Search
                                </button>
                            </div>

                            <!-- Selected indicator -->
                            <div class="mt-3 text-center" id="selectedAdminIndicator" style="display: none;">
                                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill" style="background: #8bc90510; border: 1px solid #8bc90530;">
                                    <i class="ri-check-line" style="color: #8bc905; font-size: 14px;"></i>
                                    <span style="font-size: 12px; color: #0C3A30;">Selected: <span id="selectedAdminName"></span></span>
                                </div>
                            </div>

                            <!-- Hidden inputs for corporate selection -->
                            <input type="hidden" name="copy_admin_id" id="copy_admin_id" value="{{ old('copy_admin_id') }}">
                            <input type="hidden" name="copy_admin_name" id="copy_admin_name" value="{{ old('copy_admin_name') }}">
                            <input type="hidden" name="copy_server_name" id="copy_server_name" value="{{ old('copy_server_name') }}">

                            @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                        style="width: 64px; height: 64px; background: #8bc90510;">
                                        <i class="fa-solid fa-user-slash" style="color: #8bc905; font-size: 24px;"></i>
                                    </div>
                                </div>
                                <p class="text-dark mb-1" style="font-size: 14px; font-weight: 500;">No admins available yet</p>
                                <p class="text-muted" style="font-size: 12px;">Check back later for available trading admins</p>
                            </div>
                            @endif
                        </div>
                        @error('copy_admin_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Financial Information -->
                    <div class="form-section" data-aos="fade-up" data-aos-delay="400">
                        <h3>Financial Information</h3>
                        <p class="section-desc">This helps us personalize your experience</p>

                        <div class="row">
                           <div class="col-md-6 mb-3">
    <label class="form-label fw-semibold">Investment Amount <span class="text-muted">($)</span></label>
    <div class="d-flex gap-2">
        <input type="number" name="investment_amount" id="investmentAmount" class="form-control" value="{{ old('investment_amount', 1000) }}" min="0" step="100" required style="flex: 1;">
        <button type="button" id="quickAddBtn" class="btn btn-sm px-3" style="background: #8bc905; color: white; border-radius: 8px; white-space: nowrap;">
            + Add $500
        </button>
    </div>
    <small class="text-muted d-block mt-1">
        <i class="fas fa-arrow-up text-success me-1"></i> add more / 
        <i class="fas fa-arrow-down text-danger me-1"></i> reduce amount
    </small>
    @error('investment_amount') <span class="text-danger small">{{ $message }}</span> @enderror
</div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Financial Alternative</label>
                                <input type="text" name="financial_alternative" class="form-control" value="{{ old('financial_alternative', 'Pension') }}" placeholder="e.g., Pension, Savings">
                                @error('financial_alternative') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Annual Income</label>
                                <select name="annual_income" class="form-control" required>
                                    <option value="">Select income range</option>
                                    <option value="0-14999" {{ old('annual_income') == '0-14999' ? 'selected' : '' }}>$0 - $14,999</option>
                                    <option value="15000-49999" {{ old('annual_income') == '15000-49999' ? 'selected' : '' }}>$15,000 - $49,999</option>
                                    <option value="50000-99999" {{ old('annual_income') == '50000-99999' ? 'selected' : '' }}>$50,000 - $99,999</option>
                                    <option value="100000+" {{ old('annual_income') == '100000+' ? 'selected' : '' }}>$100,000+</option>
                                </select>
                                @error('annual_income') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Source of Initial Deposit</label>
                                <select name="deposit_source" class="form-control" required>
                                    <option value="">Select source</option>
                                    <option value="savings" {{ old('deposit_source') == 'savings' ? 'selected' : '' }}>Savings</option>
                                    <option value="pension" {{ old('deposit_source') == 'pension' ? 'selected' : '' }}>Pension</option>
                                    <option value="inheritance" {{ old('deposit_source') == 'inheritance' ? 'selected' : '' }}>Inheritance</option>
                                    <option value="investment" {{ old('deposit_source') == 'investment' ? 'selected' : '' }}>Previous Investments</option>
                                    <option value="other" {{ old('deposit_source') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('deposit_source') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Source of Ongoing Deposits</label>
                                <select name="ongoing_deposit_source" class="form-control" required>
                                    <option value="">Select source</option>
                                    <option value="income" {{ old('ongoing_deposit_source') == 'income' ? 'selected' : '' }}>Regular Income</option>
                                    <option value="pension" {{ old('ongoing_deposit_source') == 'pension' ? 'selected' : '' }}>Pension</option>
                                    <option value="business" {{ old('ongoing_deposit_source') == 'business' ? 'selected' : '' }}>Business Profits</option>
                                    <option value="investments" {{ old('ongoing_deposit_source') == 'investments' ? 'selected' : '' }}>Investment Returns</option>
                                    <option value="other" {{ old('ongoing_deposit_source') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('ongoing_deposit_source') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="save-btn" id="submitBtn">
                        <span class="btn-text">Complete  Registration</span>
                        <span class="btn-loader hidden">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Experience selection
    function selectExperience(value) {
        document.getElementById('exp_yes').checked = (value === 'yes');
        document.getElementById('exp_no').checked = (value === 'no');
        document.getElementById('exp_novice').checked = (value === 'novice');

        // Update UI
        document.querySelectorAll('[onclick^="selectExperience"]').forEach(card => {
            card.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');
    }

    // Toggle checkbox for goal/asset selection
    function toggleCheckbox(id, element) {
        const checkbox = document.getElementById(id);
        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
            element.classList.add('selected');
        } else {
            element.classList.remove('selected');
        }
    }

    // Corporate admin selection
    function selectCorporateAdmin(id, name, server) {
        document.getElementById('copy_admin_id').value = id;
        document.getElementById('copy_admin_name').value = name;
        document.getElementById('copy_server_name').value = server;

        // Update UI
        document.querySelectorAll('.admin-option').forEach(option => {
            option.classList.remove('selected');
            option.style.background = '#ffffff';
            option.style.borderColor = '#edf2f7';

            const avatar = option.querySelector('.rounded-circle');
            if (avatar) {
                avatar.style.borderColor = '#e2e8f0';
            }
        });

        const selectedOption = event.currentTarget;
        selectedOption.classList.add('selected');
        selectedOption.style.background = '#f0f9e8';
        selectedOption.style.borderColor = '#8bc905';

        const selectedAvatar = selectedOption.querySelector('.rounded-circle');
        if (selectedAvatar) {
            selectedAvatar.style.borderColor = '#8bc905';
        }

        const indicator = document.getElementById('selectedAdminIndicator');
        const selectedNameSpan = document.getElementById('selectedAdminName');
        if (indicator && selectedNameSpan) {
            selectedNameSpan.textContent = name;
            indicator.style.display = 'block';
        }
    }

    // Search functionality
    function searchAdmins() {
        const searchInput = document.getElementById('adminSearch');
        if (!searchInput) return;

        const searchWrapper = document.querySelector('.search-wrapper');
        if (searchWrapper) {
            searchWrapper.style.opacity = '0.7';
        }

        const searchTerm = searchInput.value.toLowerCase().trim();
        const adminItems = document.querySelectorAll('.admin-item');
        const clearBtn = document.getElementById('clearSearchBtn');
        const searchResultsInfo = document.getElementById('searchResultsInfo');
        const resultsCountSpan = document.getElementById('resultsCount');
        const noResultsDiv = document.getElementById('noSearchResults');
        const adminList = document.getElementById('adminList');

        let visibleCount = 0;

        // Show/hide clear button
        if (clearBtn) {
            clearBtn.style.display = searchTerm.length > 0 ? 'block' : 'none';
        }

        // Filter admin items
        adminItems.forEach(item => {
            const adminName = item.getAttribute('data-admin-name');
            const serverName = item.getAttribute('data-server-name');

            if (searchTerm === '' || adminName.includes(searchTerm) || serverName.includes(searchTerm)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Update results count and visibility
        if (searchTerm.length > 0) {
            if (resultsCountSpan) resultsCountSpan.textContent = visibleCount;
            if (searchResultsInfo) searchResultsInfo.style.display = 'block';

            if (visibleCount === 0) {
                if (adminList) adminList.style.display = 'none';
                if (noResultsDiv) noResultsDiv.style.display = 'block';
            } else {
                if (adminList) adminList.style.display = 'block';
                if (noResultsDiv) noResultsDiv.style.display = 'none';
            }
        } else {
            if (searchResultsInfo) searchResultsInfo.style.display = 'none';
            if (adminList) adminList.style.display = 'block';
            if (noResultsDiv) noResultsDiv.style.display = 'none';
        }

        // Remove loading indicator
        if (searchWrapper) {
            setTimeout(() => {
                searchWrapper.style.opacity = '1';
            }, 200);
        }
    }

    function clearSearch() {
        const searchInput = document.getElementById('adminSearch');
        if (searchInput) {
            searchInput.value = '';
            searchAdmins();
            searchInput.focus();
        }
    }

    function initializeSearch() {
        const searchInput = document.getElementById('adminSearch');
        if (searchInput) {
            // Remove any existing event listeners
            const newSearchInput = searchInput.cloneNode(true);
            searchInput.parentNode.replaceChild(newSearchInput, searchInput);

            // Add new event listener with debounce
            let timeout;
            newSearchInput.addEventListener('keyup', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    searchAdmins();
                }, 300);
            });

            // Add input event for immediate feedback
            newSearchInput.addEventListener('input', function() {
                if (this.value === '') {
                    searchAdmins();
                }
            });

            // Update the clear button functionality
            const clearBtn = document.getElementById('clearSearchBtn');
            if (clearBtn) {
                clearBtn.onclick = function() {
                    clearSearch();
                };
            }
        }
    }

    // Form submission loader
    document.getElementById('additionalInfoForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const btnText = btn.querySelector('.btn-text');
        const btnLoader = btn.querySelector('.btn-loader');

        btn.disabled = true;
        btnText.classList.add('hidden');
        btnLoader.classList.remove('hidden');
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Preload admin images
        const adminImages = document.querySelectorAll('.admin-option img');
        adminImages.forEach(img => {
            const src = img.getAttribute('src');
            if (src) {
                const preloadLink = document.createElement('link');
                preloadLink.rel = 'preload';
                preloadLink.as = 'image';
                preloadLink.href = src;
                document.head.appendChild(preloadLink);
            }
        });

        // Initialize search
        setTimeout(() => {
            initializeSearch();
        }, 100);

        // Check for previously selected admin
        const copyAdminId = document.getElementById('copy_admin_id').value;
        if (copyAdminId) {
            document.querySelectorAll('.admin-option').forEach(option => {
                if (option.getAttribute('onclick')?.includes(`'${copyAdminId}'`)) {
                    option.classList.add('selected');
                    option.style.background = '#f0f9e8';
                    option.style.borderColor = '#8bc905';

                    const adminName = document.getElementById('copy_admin_name').value;
                    if (adminName) {
                        const indicator = document.getElementById('selectedAdminIndicator');
                        const selectedNameSpan = document.getElementById('selectedAdminName');
                        if (indicator && selectedNameSpan) {
                            selectedNameSpan.textContent = adminName;
                            indicator.style.display = 'block';
                        }
                    }
                }
            });
        }
    });

    // Quick add button
const quickAddBtn = document.getElementById('quickAddBtn');
const investmentInput = document.getElementById('investmentAmount');

if (quickAddBtn && investmentInput) {
    quickAddBtn.addEventListener('click', function() {
        let currentValue = parseInt(investmentInput.value) || 0;
        investmentInput.value = currentValue + 500;
    });
}
</script>

<style>
    .hidden {
        display: none !important;
    }

    .btn-loader {
        margin-left: 10px;
    }
</style>

@endsection