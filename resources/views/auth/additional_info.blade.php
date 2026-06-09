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

    /* Admin Selection Styles - Copied exactly from registration form */
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
    .assets-grid,
    .learning-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .goal-item,
    .asset-item,
    .learning-item {
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
    .asset-item:hover,
    .learning-item:hover {
        border-color: #8bc905;
        background: #f8fafc;
    }

    .goal-item.selected,
    .asset-item.selected,
    .learning-item.selected {
        border-color: #8bc905;
        background: rgba(139, 201, 5, 0.05);
    }

    .goal-item input[type="checkbox"],
    .asset-item input[type="checkbox"],
    .learning-item input[type="radio"] {
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

    /* Add to your existing styles */
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
        .assets-grid,
        .learning-grid {
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

    .admin-card {
        transition: all 0.3s ease;
    }

    .admin-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(139, 201, 5, 0.15);
        border-color: #8bc905 !important;
    }

    .admin-card.selected {
        background: linear-gradient(135deg, #f0f9e8, #e8f5e0);
        border-color: #8bc905 !important;
    }

    /* Custom scrollbar for admin list */
    #adminList::-webkit-scrollbar {
        width: 6px;
    }

    #adminList::-webkit-scrollbar-track {
        background: #e2e8f0;
        border-radius: 10px;
    }

    #adminList::-webkit-scrollbar-thumb {
        background: #8bc905;
        border-radius: 10px;
    }

    #adminList::-webkit-scrollbar-thumb:hover {
        background: #76ad03;
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

                    <!-- Trading Experience -->
                    <div class="form-section" data-aos="fade-up">
                        <h3>Your Trading Experience - click to select</h3>
                        <p class="section-desc">Help us understand your background in trading and investing</p>

                        <div class="mb-4">
                            <label class="section-title">
                                What is your experience with Stock & Cryptocurrency Trading?
                            </label>

                            <div class="row g-3 mt-2">

                                <!-- Experienced -->
                                <div class="col-md-4">
                                    <div class="option-card {{ old('stock_experience') == 'yes' ? 'selected' : '' }}" onclick="selectExperience('yes')">
                                        <input type="radio" name="stock_experience" id="exp_yes" value="yes" class="form-check-input" {{ old('stock_experience') == 'yes' ? 'checked' : '' }} required>
                                        <div>
                                            <strong>Experienced Trader</strong>
                                            <p class="small text-muted mb-0">
                                                I have actively traded Stocks and/or Cryptocurrencies and understand market mechanics.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Intermediate -->
                                <div class="col-md-4">
                                    <div class="option-card {{ old('stock_experience') == 'no' ? 'selected' : '' }}" onclick="selectExperience('no')">
                                        <input type="radio" name="stock_experience" id="exp_no" value="no" class="form-check-input" {{ old('stock_experience') == 'no' ? 'checked' : '' }}>
                                        <div>
                                            <strong>Some Trading Experience</strong>
                                            <p class="small text-muted mb-0">
                                                I have made several trades and understand basic trading concepts and strategies.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Beginner -->
                                <div class="col-md-4">
                                    <div class="option-card {{ old('stock_experience') == 'novice' ? 'selected' : '' }}" onclick="selectExperience('novice')">
                                        <input type="radio" name="stock_experience" id="exp_novice" value="novice" class="form-check-input" {{ old('stock_experience') == 'novice' ? 'checked' : '' }}>
                                        <div>
                                            <strong>New to Trading</strong>
                                            <p class="small text-muted mb-0">
                                                I'm just starting my trading journey. I need education and guidance to develop strategies.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <small class="text-success d-block mt-2">
                                ✔ We provide expert guidance, trading tools, and educational resources tailored to your experience level.
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
                                    <option value="0-10" {{ old('trading_frequency') == '0-10' ? 'selected' : '' }}>0-10 trades</option>
                                    <option value="11-40" {{ old('trading_frequency') == '11-40' ? 'selected' : '' }}>11-40 trades</option>
                                    <option value="40+" {{ old('trading_frequency') == '40+' ? 'selected' : '' }}>More than 40 trades</option>
                                </select>
                                @error('trading_frequency') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Total trading volume (past 2 years)</label>
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

                    <!-- Trading Goals -->
                    <div class="form-section" data-aos="fade-up" data-aos-delay="100">
                        <h3>Your Trading & Investment Goals</h3>
                        <p class="section-desc">What are you looking to achieve? (Select all that apply)</p>

                        <div class="goals-grid">
                            <div class="goal-item {{ in_array('diversification', old('investment_goal', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('goal_diversification', this)">
                                <input type="checkbox" name="investment_goal[]" value="diversification" id="goal_diversification" class="form-check-input" {{ in_array('diversification', old('investment_goal', [])) ? 'checked' : '' }}>
                                <div>
                                    <strong>Build a Diversified Portfolio</strong>
                                    <p class="small text-muted mb-0">Access different assets and trading strategies for better performance</p>
                                </div>
                            </div>

                            <div class="goal-item {{ in_array('income', old('investment_goal', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('goal_income', this)">
                                <input type="checkbox" name="investment_goal[]" value="income" id="goal_income" class="form-check-input" {{ in_array('income', old('investment_goal', [])) ? 'checked' : '' }}>
                                <div>
                                    <strong>Generate Regular Trading Income</strong>
                                    <p class="small text-muted mb-0">Develop consistent income through active trading and strategic positions</p>
                                </div>
                            </div>

                            <div class="goal-item {{ in_array('long_term', old('investment_goal', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('goal_longterm', this)">
                                <input type="checkbox" name="investment_goal[]" value="long_term" id="goal_longterm" class="form-check-input" {{ in_array('long_term', old('investment_goal', [])) ? 'checked' : '' }}>
                                <div>
                                    <strong>Long-term Wealth Building</strong>
                                    <p class="small text-muted mb-0">Build sustainable wealth through long-term position trading and investing</p>
                                </div>
                            </div>

                            <div class="goal-item {{ in_array('short_term', old('investment_goal', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('goal_shortterm', this)">
                                <input type="checkbox" name="investment_goal[]" value="short_term" id="goal_shortterm" class="form-check-input" {{ in_array('short_term', old('investment_goal', [])) ? 'checked' : '' }}>
                                <div>
                                    <strong>Day Trading & Short-term Gains</strong>
                                    <p class="small text-muted mb-0">Capitalize on market volatility through short-term trading strategies</p>
                                </div>
                            </div>

                            <div class="goal-item {{ in_array('copy_trading', old('investment_goal', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('goal_copy', this)">
                                <input type="checkbox" name="investment_goal[]" value="copy_trading" id="goal_copy" class="form-check-input" {{ in_array('copy_trading', old('investment_goal', [])) ? 'checked' : '' }}>
                                <div>
                                    <strong>Copy Trading from Experts</strong>
                                    <p class="small text-muted mb-0">Follow and replicate trades from successful professional traders</p>
                                </div>
                            </div>

                            <div class="goal-item {{ in_array('other', old('investment_goal', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('goal_other', this)">
                                <input type="checkbox" name="investment_goal[]" value="other" id="goal_other" class="form-check-input" {{ in_array('other', old('investment_goal', [])) ? 'checked' : '' }}>
                                <div>
                                    <strong>Other Trading Goals</strong>
                                    <p class="small text-muted mb-0">Different trading objectives or strategies</p>
                                </div>
                            </div>
                        </div>
                        @error('investment_goal') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Asset Classes -->
                    <div class="form-section" data-aos="fade-up" data-aos-delay="200">
                        <h3>Which asset classes interest you?</h3>
                        <p class="section-desc">Choose one or more options that align with your trading strategy</p>

                        <div class="assets-grid">
                            <div class="asset-item {{ in_array('stocks', old('asset_classes', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('asset_stocks', this)">
                                <input type="checkbox" name="asset_classes[]" value="stocks" id="asset_stocks" class="form-check-input" {{ in_array('stocks', old('asset_classes', [])) ? 'checked' : '' }}>
                                <label for="asset_stocks" class="fw-bold">Stocks & Equities</label>
                            </div>

                            <div class="asset-item {{ in_array('crypto', old('asset_classes', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('asset_crypto', this)">
                                <input type="checkbox" name="asset_classes[]" value="crypto" id="asset_crypto" class="form-check-input" {{ in_array('crypto', old('asset_classes', [])) ? 'checked' : '' }}>
                                <label for="asset_crypto" class="fw-bold">Cryptocurrency</label>
                            </div>

                            <div class="asset-item {{ in_array('forex', old('asset_classes', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('asset_forex', this)">
                                <input type="checkbox" name="asset_classes[]" value="forex" id="asset_forex" class="form-check-input" {{ in_array('forex', old('asset_classes', [])) ? 'checked' : '' }}>
                                <label for="asset_forex" class="fw-bold">Forex</label>
                            </div>

                            <div class="asset-item {{ in_array('commodities', old('asset_classes', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('asset_commodities', this)">
                                <input type="checkbox" name="asset_classes[]" value="commodities" id="asset_commodities" class="form-check-input" {{ in_array('commodities', old('asset_classes', [])) ? 'checked' : '' }}>
                                <label for="asset_commodities" class="fw-bold">Commodities</label>
                            </div>

                            <div class="asset-item {{ in_array('options', old('asset_classes', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('asset_options', this)">
                                <input type="checkbox" name="asset_classes[]" value="options" id="asset_options" class="form-check-input" {{ in_array('options', old('asset_classes', [])) ? 'checked' : '' }}>
                                <label for="asset_options" class="fw-bold">Options & Derivatives</label>
                            </div>

                            <div class="asset-item {{ in_array('indices', old('asset_classes', [])) ? 'selected' : '' }}" onclick="toggleCheckbox('asset_indices', this)">
                                <input type="checkbox" name="asset_classes[]" value="indices" id="asset_indices" class="form-check-input" {{ in_array('indices', old('asset_classes', [])) ? 'checked' : '' }}>
                                <label for="asset_indices" class="fw-bold">Indices & ETFs</label>
                            </div>
                        </div>
                        @error('asset_classes') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Learning Style -->
                    <div class="form-section" data-aos="fade-up" data-aos-delay="250">
                        <h3>How would you like to learn?</h3>
                        <p class="section-desc">Select your preferred learning method</p>

                        <div class="learning-grid">
                            <div class="learning-item {{ old('learning_style') == 'video' ? 'selected' : '' }}" onclick="selectLearningStyle('video')">
                                <input type="radio" name="learning_style" id="learn_video" value="video" class="form-check-input" {{ old('learning_style') == 'video' ? 'checked' : '' }} required>
                                <div>
                                    <strong>Video Tutorials</strong>
                                    <p class="small text-muted mb-0">Learn through step-by-step video courses and market analysis</p>
                                </div>
                            </div>

                            <div class="learning-item {{ old('learning_style') == 'webinar' ? 'selected' : '' }}" onclick="selectLearningStyle('webinar')">
                                <input type="radio" name="learning_style" id="learn_webinar" value="webinar" class="form-check-input" {{ old('learning_style') == 'webinar' ? 'checked' : '' }}>
                                <div>
                                    <strong>Live Webinars</strong>
                                    <p class="small text-muted mb-0">Interactive live sessions with expert traders and Q&A</p>
                                </div>
                            </div>

                            <div class="learning-item {{ old('learning_style') == 'articles' ? 'selected' : '' }}" onclick="selectLearningStyle('articles')">
                                <input type="radio" name="learning_style" id="learn_articles" value="articles" class="form-check-input" {{ old('learning_style') == 'articles' ? 'checked' : '' }}>
                                <div>
                                    <strong>Articles & Guides</strong>
                                    <p class="small text-muted mb-0">In-depth written guides, strategies, and market analysis</p>
                                </div>
                            </div>

                            <div class="learning-item {{ old('learning_style') == 'mentoring' ? 'selected' : '' }}" onclick="selectLearningStyle('mentoring')">
                                <input type="radio" name="learning_style" id="learn_mentoring" value="mentoring" class="form-check-input" {{ old('learning_style') == 'mentoring' ? 'checked' : '' }}>
                                <div>
                                    <strong>One-on-One Mentoring</strong>
                                    <p class="small text-muted mb-0">Personalized guidance and coaching from experienced traders</p>
                                </div>
                            </div>

                            <div class="learning-item {{ old('learning_style') == 'community' ? 'selected' : '' }}" onclick="selectLearningStyle('community')">
                                <input type="radio" name="learning_style" id="learn_community" value="community" class="form-check-input" {{ old('learning_style') == 'community' ? 'checked' : '' }}>
                                <div>
                                    <strong>Community Forums</strong>
                                    <p class="small text-muted mb-0">Learn from peers, discuss strategies, and share experiences</p>
                                </div>
                            </div>

                            <div class="learning-item {{ old('learning_style') == 'practice' ? 'selected' : '' }}" onclick="selectLearningStyle('practice')">
                                <input type="radio" name="learning_style" id="learn_practice" value="practice" class="form-check-input" {{ old('learning_style') == 'practice' ? 'checked' : '' }}>
                                <div>
                                    <strong>Paper Trading</strong>
                                    <p class="small text-muted mb-0">Practice trading with virtual money, no real risk</p>
                                </div>
                            </div>
                        </div>
                        @error('learning_style') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <!-- Account Type Selection -->
                 
<div class="account-card selected" id="corporateCard" onclick="selectAccountType()" style="background: white; border: 2px solid #8bc905; border-radius: 20px; padding: 10px; text-align: center; cursor: pointer; transition: all 0.3s ease; position: relative;">
    
    <!-- Add this click hint badge -->
    <div style="position: absolute; top: -10px; right: 20px; background: #8bc905; color: #0C3A30; padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 600;">
        <i class="fas fa-hand-point-right" style="margin-right: 5px;"></i> Click to Select
    </div>
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <input type="hidden" name="account_type" value="corporate">
<div class="account-card selected" id="corporateCard" onclick="selectAccountType()" style="background: white;  border-radius: 20px; padding: 10px; text-align: center; cursor: pointer; transition: all 0.3s ease; ">
    <style>
        #corporateCard {
    box-shadow: 0 8px 25px rgba(139, 201, 5, 0.25);
}

#corporateCard:hover {
    box-shadow: 0 15px 35px rgba(139, 201, 5, 0.35);
    transform: translateY(-3px);
}
    </style>



                                    <div style="width: 60px; height: 60px; background: #8bc90515; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 15px auto;">
                                        <i class="ri-user-star-line" style="font-size: 30px; color: #8bc905;"></i>
                                    </div>

                                    <h4 style="color: #0C3A30; font-weight: 700; margin: 10px 0 5px;">Managed Trading Account</h4>

                                    <span style="background: #0C3A30; color: #8bc905; padding: 3px 10px; border-radius: 50px; font-size: 10px; font-weight: 600; display: inline-block;"> Expert Traders</span>

                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <p style="margin: 0 0 8px; font-size: 12px;"><i class="ri-checkbox-circle-line" style="color: #8bc905;"></i> Copy expert trades</p>
                                            <p style="margin: 0; font-size: 12px;"><i class="ri-checkbox-circle-line" style="color: #8bc905;"></i> Advanced tools</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p style="margin: 0 0 8px; font-size: 12px;"><i class="ri-checkbox-circle-line" style="color: #8bc905;"></i> Real-time tracking</p>
                                            <p style="margin: 0; font-size: 12px;"><i class="ri-checkbox-circle-line" style="color: #8bc905;"></i> 24/7 support</p>
                                        </div>
                                    </div>

                                    <div style="height: 1px; background: #8bc90520; margin: 15px 0;"></div>

                                    <div style="margin-top: 15px;">
                                        <p style="font-size: 14px; color: #64748b; margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px;">
                                            <i class="ri-bar-chart-2-line" style="color: #8bc905; font-size: 16px;"></i>
                                            <span>Execute your own trading strategies with advanced tools and analysis features.</span>
                                        </p>
                                        <p style="font-size: 14px; color: #64748b; margin: 0; display: flex; align-items: center; gap: 8px;">
                                            <i class="ri-file-copy-line" style="color: #8bc905; font-size: 16px;"></i>
                                            <span>Automatically copy trades from successful Discord/Telegram community traders.</span>
                                        </p>
                                    </div>


                                </div>
                            </div>
                        </div>

                        <!-- Admin Selection Area (hidden by default, shows when card is clicked) -->
                        <div id="corporateAdminArea" class="admin-select-wrapper mt-5" style="background: #f8fafc; border-radius: 20px; border: 1px solid #e2e8f0; padding: 24px; display: none;">

                            <!-- Header -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 gap-3" style="border-bottom: 2px solid #8bc90520;">
                                <div>
                                    <h5 style="font-size: 16px; color: #0C3A30; font-weight: 700; margin: 0;">
                                        <i class="ri-user-star-line me-2" style="color: #8bc905;"></i>
                                        Select Your Trading Expert
                                    </h5>

                                </div>

                                <!-- Search Bar -->
                                <div class="search-wrapper" style="position: relative; min-width: 260px;">
                                    <i class="ri-search-line" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                                    <input type="text"
                                        id="adminSearch"
                                        class="form-control"
                                        placeholder="Search by name or server..."
                                        style="padding-left: 40px; height: 44px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; font-size: 13px;">
                                    <button type="button"
                                        class="btn btn-sm"
                                        onclick="clearSearch()"
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; padding: 0; display: none;"
                                        id="clearSearchBtn">
                                        <i class="ri-close-line" style="font-size: 16px;"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Rest of your admin selection content remains the same -->
                            @if(isset($feeds) && $feeds->count() > 0)
                            <!-- Search Results Count -->
                            <div id="searchResultsInfo" class="mb-3 px-1" style="display: none;">
                                <small style="color: #64748b;">Found <span id="resultsCount">0</span> matching traders</small>
                            </div>

                            <!-- Stats Bar -->
                            <div class="d-flex gap-3 mb-4 flex-wrap">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-group-line" style="color: #8bc905;"></i>
                                    <span style="font-size: 12px; color: #475569;"><span id="totalExperts">{{ $feeds->count() }}</span> Experts Available</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-trophy-line" style="color: #8bc905;"></i>
                                    <span style="font-size: 12px; color: #475569;">Verified Track Record</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-shield-check-line" style="color: #8bc905;"></i>
                                    <span style="font-size: 12px; color: #475569;">Funds Protected</span>
                                </div>
                            </div>

                            <!-- Admin List -->
                            <div class="row g-3" style="max-height: 420px; overflow-y: auto; padding-right: 6px;" id="adminList">
                                @foreach($feeds as $feed)
                                <div class="col-12 admin-item"
                                    data-admin-name="{{ strtolower($feed->admin_name) }}"
                                    data-server-name="{{ strtolower($feed->server_name) }}"
                                    data-profit="{{ $feed->profit_margin }}">

                                    <div class="admin-card d-flex align-items-start p-4 rounded-4 w-100 {{ old('copy_admin_id') == $feed->id ? 'selected' : '' }}"
                                        onclick="selectCorporateAdmin('{{ $feed->id }}', '{{ $feed->admin_name }}', '{{ $feed->server_name }}')"
                                        style="background: {{ old('copy_admin_id') == $feed->id ? '#f0f9e8' : '#ffffff' }};
                           border: 2px solid {{ old('copy_admin_id') == $feed->id ? '#8bc905' : '#e2e8f0' }};
                           transition: all 0.25s ease;
                           cursor: pointer;
                           margin-bottom: 12px;">

                                        <!-- Avatar -->
                                        <div class="flex-shrink-0 me-4">
                                            @if($feed->admin_profile_image)
                                            <div class="rounded-circle overflow-hidden" style="width: 60px; height: 60px; border: 3px solid {{ old('copy_admin_id') == $feed->id ? '#8bc905' : '#e2e8f0' }};">
                                                <img src="{{ asset('storage/admins/'.$feed->admin_profile_image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                                

                                                
                                            </div>
                                            @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: linear-gradient(135deg, #8bc90520, #8bc90510); color: #0C3A30; border: 3px solid {{ old('copy_admin_id') == $feed->id ? '#8bc905' : '#e2e8f0' }};">
                                                <span style="font-weight: 700; font-size: 22px;">{{ strtoupper(substr($feed->admin_name, 0, 1)) }}</span>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                                <div>
                                                    <h5 style="font-size: 18px; font-weight: 700; color: #0C3A30; margin: 0;">{{ $feed->admin_name }}</h5>
                                                    <span style="font-size: 13px; color: #64748b;"><i class="ri-server-line me-1"></i>{{ $feed->server_name }}</span>
                                                </div>
                                                <span style="background: #8bc905; color: #0C3A30; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                                    <i class="ri-user-add-line me-1"></i> Join
                                                </span>
                                            </div>

                                            <div class="d-flex flex-wrap gap-3 mb-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="ri-user-line" style="color: #8bc905; font-size: 14px;"></i>
                                                    <span style="font-size: 13px; color: #475569;"><strong>{{ number_format($feed->active_members) }}</strong> active</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="ri-file-copy-line" style="color: #8bc905; font-size: 14px;"></i>
                                                    <span style="font-size: 13px; color: #475569;"><strong>{{ number_format($feed->copying_trades ?? 0) }}</strong> copying</span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="ri-money-dollar-circle-line" style="color: {{ $feed->profit_margin >= 0 ? '#16a34a' : '#dc2626' }}; font-size: 14px;"></i>
                                                    <span style="font-size: 13px; font-weight: 600; color: {{ $feed->profit_margin >= 0 ? '#16a34a' : '#dc2626' }};">${{ number_format(abs($feed->profit_margin), 2) }}</span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <div style="width: 100%; background: #e2e8f0; border-radius: 10px; height: 6px; max-width: 150px;">
                                                    @php $winRate = $feed->win_rate ?? 0; @endphp
                                                    <div style="width: {{ $winRate }}%; background: {{ $winRate >= 70 ? '#16a34a' : ($winRate >= 50 ? '#eab308' : '#dc2626') }}; height: 6px; border-radius: 10px;"></div>
                                                </div>
                                                <span style="font-size: 12px; font-weight: 600; color: {{ $winRate >= 70 ? '#16a34a' : ($winRate >= 50 ? '#eab308' : '#dc2626') }};"><i class="ri-trophy-line me-1"></i>{{ number_format($winRate, 1) }}% win rate</span>
                                            </div>
                                        </div>

                                        @if(old('copy_admin_id') == $feed->id)
                                        <div class="flex-shrink-0 ms-3">
                                            <i class="ri-checkbox-circle-fill" style="color: #8bc905; font-size: 24px;"></i>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- No Results Message -->
                            <div id="noSearchResults" class="text-center py-5" style="display: none;">
                                <div class="mb-3">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px; background: #8bc90510;">
                                        <i class="ri-search-line" style="color: #8bc905; font-size: 28px;"></i>
                                    </div>
                                </div>
                                <p class="text-dark mb-1" style="font-size: 15px; font-weight: 500;">No matching traders found</p>
                                <p class="text-muted" style="font-size: 13px;">Try adjusting your search terms</p>
                                <button class="btn mt-2" onclick="clearSearch()" style="background: #8bc905; color: white; border: none; padding: 8px 20px; border-radius: 25px; font-size: 13px;">Clear Search</button>
                            </div>

                            <!-- Selected indicator -->
                            <div class="mt-4 text-center" id="selectedAdminIndicator" style="display: none;">
                                <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill" style="background: #8bc90510; border: 1px solid #8bc90530;">
                                    <i class="ri-check-line" style="color: #8bc905; font-size: 16px;"></i>
                                    <span style="font-size: 13px; color: #0C3A30;">Selected Expert: <strong id="selectedAdminName"></strong></span>
                                </div>
                            </div>

                            <!-- Hidden inputs -->
                            <input type="hidden" name="copy_admin_id" id="copy_admin_id" value="{{ old('copy_admin_id') }}">
                            <input type="hidden" name="copy_admin_name" id="copy_admin_name" value="{{ old('copy_admin_name') }}">
                            <input type="hidden" name="copy_server_name" id="copy_server_name" value="{{ old('copy_server_name') }}">

                            @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 70px; height: 70px; background: #8bc90510;">
                                        <i class="ri-user-unfollow-line" style="color: #8bc905; font-size: 28px;"></i>
                                    </div>
                                </div>
                                <p class="text-dark mb-1" style="font-size: 15px; font-weight: 500;">No traders available yet</p>
                                <p class="text-muted" style="font-size: 13px;">Check back later for available trading experts</p>
                            </div>
                            @endif
                        </div>
                        @error('account_type') <span class="text-danger small d-block mt-2 text-center">{{ $message }}</span> @enderror
                    </div>

                    <!-- Financial Information -->
                    <div class="form-section" data-aos="fade-up" data-aos-delay="400">
                        <h3>Trading Capital & Financial Information</h3>
                        <p class="section-desc">This helps us customize your trading experience</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Initial Trading Capital <span class="text-muted">($)</span></label>
                                <input type="number" name="investment_amount" class="form-control" value="{{ old('investment_amount', 1000) }}" min="0" step="100" required>
                                @error('investment_amount') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Source of Trading Capital</label>
                                <select name="deposit_source" class="form-control" required>
                                    <option value="">Select source</option>
                                    <option value="savings" {{ old('deposit_source') == 'savings' ? 'selected' : '' }}>Personal Savings</option>
                                    <option value="pension" {{ old('deposit_source') == 'pension' ? 'selected' : '' }}>Pension Funds</option>
                                    <option value="inheritance" {{ old('deposit_source') == 'inheritance' ? 'selected' : '' }}>Inheritance</option>
                                    <option value="investment" {{ old('deposit_source') == 'investment' ? 'selected' : '' }}>Previous Investment Returns</option>
                                    <option value="other" {{ old('deposit_source') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('deposit_source') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="save-btn" id="submitBtn">
                        <span class="btn-text">Complete Profile & Finish Registration</span>
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

    // Learning style selection
    function selectLearningStyle(value) {
        document.getElementById('learn_video').checked = (value === 'video');
        document.getElementById('learn_webinar').checked = (value === 'webinar');
        document.getElementById('learn_articles').checked = (value === 'articles');
        document.getElementById('learn_mentoring').checked = (value === 'mentoring');
        document.getElementById('learn_community').checked = (value === 'community');
        document.getElementById('learn_practice').checked = (value === 'practice');

        // Update UI
        document.querySelectorAll('[onclick^="selectLearningStyle"]').forEach(card => {
            card.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');
    }

    // Account type selection
    // Account type is always corporate
    function selectAccountType() {
        // Always show admin area
        const adminArea = document.getElementById('corporateAdminArea');
        adminArea.classList.remove('hidden');
        setTimeout(() => {
            initializeSearch();
        }, 100);
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
    // Account type selection - shows admin area when card is clicked
    function selectAccountType() {
        const adminArea = document.getElementById('corporateAdminArea');
        if (adminArea) {
            adminArea.style.display = 'block';
            // Scroll to it smoothly
            adminArea.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            setTimeout(() => {
                initializeSearch();
            }, 100);
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

        // Check if corporate was previously selected
        const accountType = document.querySelector('input[name="account_type"]:checked')?.value;
        if (accountType === 'corporate') {
            document.getElementById('corporateAdminArea')?.classList.remove('hidden');

            setTimeout(() => {
                initializeSearch();
            }, 100);

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
        } else {
            initializeSearch();
        }
    });
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