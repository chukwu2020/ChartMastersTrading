@extends('layout.app')

@section('content')
<style>
    /* CSS to reduce header gap */
    .page-banner-area {
        margin-top: -1rem;
        padding-top: 4rem !important;
    }

    @media (max-width: 768px) {
        .page-banner-area {
            margin-top: -1.5rem;
        }
    }

    /* Professional form styling */
    .join-options {
        background: linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .join-option-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .join-option-card:hover {
        border-color: #8bc905;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(139, 201, 5, 0.2);
    }

    .join-option-card.selected {
        border-color: #8bc905;
        background: linear-gradient(135deg, rgba(139, 201, 5, 0.05) 0%, rgba(139, 201, 5, 0.1) 100%);
        box-shadow: 0 0 0 3px rgba(139, 201, 5, 0.2);
    }

    .join-option-card i {
        font-size: 24px;
        color: #8bc905;
        margin-bottom: 8px;
    }

    .join-option-card h5 {
        font-size: 14px;
        font-weight: 600;
        color: #0C3A30;
        margin-bottom: 4px;
    }

    .join-option-card p {
        font-size: 11px;
        color: #64748b;
        margin: 0;
    }

    .selected-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #8bc905;
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .join-option-card.selected .selected-badge {
        opacity: 1;
    }

    .other-input {
        margin-top: 16px;
        animation: slideDown 0.3s ease;
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

    .section-divider {
        position: relative;
        text-align: center;
        margin: 30px 0 20px;
    }

    .section-divider::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, #8bc905, transparent);
    }

    .section-divider span {
        background: white;
        padding: 0 20px;
        color: #0C3A30;
        font-weight: 600;
        font-size: 14px;
        position: relative;
        z-index: 1;
    }

    /* Wikipedia-style notice */
    .wikipedia-notice {
        background: #f8f9fa;
        border: 1px solid #a2a9b1;
        border-left: 4px solid #8bc905;
        padding: 16px;
        margin-bottom: 24px;
        border-radius: 4px;
        font-size: 13px;
        color: #202122;
    }

    .wikipedia-notice i {
        color: #8bc905;
        margin-right: 8px;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }

    .password-toggle:hover {
        color: #0c3a30;
    }

    .form-control.pe-5 {
        padding-right: 2.5rem !important;
    }

    #registerBtn {
        position: relative;
        padding: 12px 20px;
        background-color: #8bc905 !important;
        color: white !important;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    #registerBtn:hover {
        background-color: #76ad03 !important;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(139, 201, 5, 0.4);
    }

    #registerBtn:disabled {
        background-color: #ccc !important;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .hidden {
        display: none !important;
    }
</style>

<!-- Start Compact Page Banner Area -->
<div class="page-banner-area position-relative py-3" style="background-image: url(assets/images/hero/hero-image-2.svg); background-size: cover; background-position: center;">
    <div class="container">
        <div class="page-banner-content text-center">
            <h1 style="font-size: 20px; margin: 0; color: #0C3A30;">Join Our Trading Community</h1>
            <ul style="margin: 2px 0 0; padding: 0; font-size: 12px;">
                <li style="display: inline;"><a href="/" style="color: #8bc905; text-decoration: none;">Home</a></li>
                <li style="display: inline; color: #666;"> / Sign Up</li>
            </ul>
        </div>
    </div>
</div>
<!-- End Compact Page Banner Area -->

<!-- Start My Account Page -->
<div class="my-account-page ptb-80 overflow-hidden">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-10 col-md-12" data-cues="slideInLeft" data-duration="800">

                <!-- Wikipedia-style notice -->
                <div class="wikipedia-notice">
                    <i class="ri-information-line"></i>
                    <strong>Welcome to the trading community!</strong> Please tell us a bit about yourself to help us personalize your experience.
                </div>

                <form id="registerForm" action="{{ route('user.create') }}" method="POST" class="login-form bg-color-fffaeb radius-30">
                    @csrf
                    <h3>Create Your Account</h3>

                    <!-- Basic Information Section -->
                    <div class="row">
                        <div class="mb-3 col-md-6">
                            <label for="name">Full Name</label>
                            <input type="text" class="form-control" value="{{ old('name') }}" name="name" placeholder="Enter your full name">
                            <span class="text-danger">@error('name') {{ $message }} @enderror</span>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="">Username on Discord</label>
                            <input type="text" class="form-control" value="{{ old('username') }}" name="username" placeholder="Choose a username">
                            <span class="text-danger">@error('username') {{ $message }} @enderror</span>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="">Email</label>
                            <input type="email" class="form-control" value="{{ old('email') }}" name="email" placeholder="your@email.com">
                            <span class="text-danger">@error('email') {{ $message }} @enderror</span>
                        </div>

                        <div class="mb-3 col-md-6">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" class="form-control" inputmode="tel" placeholder="Phone number" value="{{ old('phone') }}">
                            <span class="text-danger">@error('phone') {{ $message }} @enderror</span>
                        </div>

                        <div class="mb-3 col-md-6 position-relative">
                            <label for="">Password</label>
                            <input type="password" id="regPassword" class="form-control pe-5" name="password" placeholder="Create a strong password" value="{{ old('password') }}">
                            <span class="password-toggle" onclick="toggleRegPassword()">
                                <i class="ri-eye-line" id="regToggleIcon"></i>
                            </span>
                            <span class="text-danger">@error('password') {{ $message }} @enderror</span>
                        </div>

                        <!-- <div class="mb-3 col-md-6 position-relative">
                            <label for="">Confirm Password</label>
                            <input type="password" id="regConfirmPassword" class="form-control pe-5" name="password_confirmation" placeholder="Confirm your password" value="{{ old('password_confirmation') }}">
                            <span class="password-toggle" onclick="toggleRegConfirmPassword()">
                                <i class="ri-eye-line" id="regConfirmToggleIcon"></i>
                            </span>
                            <span class="text-danger">@error('password_confirmation') {{ $message }} @enderror</span>
                        </div> -->

                        <div class="mb-3 col-md-6">
                            <label for="country">Country</label>
                            <select name="country" id="country" class="form-control pe-5" required>
                                <option value="" disabled {{ old('country') ? '' : 'selected' }}>Select your country ▼</option>
                                @php
                                $countries = [
                                'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Antigua and Barbuda', 'Argentina', 'Armenia', 'Australia',
                                'Austria', 'Azerbaijan', 'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin',
                                'Bhutan', 'Bolivia', 'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi',
                                'Côte d\'Ivoire', 'Cabo Verde', 'Cambodia', 'Cameroon', 'Canada', 'Central African Republic', 'Chad', 'Chile', 'China',
                                'Colombia', 'Comoros', 'Congo', 'Costa Rica', 'Croatia', 'Cuba', 'Cyprus', 'Czech Republic',
                                'Democratic Republic of the Congo', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt',
                                'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Eswatini', 'Ethiopia', 'Fiji', 'Finland', 'France', 'Gabon',
                                'Gambia', 'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana',
                                'Haiti', 'Holy See', 'Honduras', 'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel',
                                'Italy', 'Jamaica', 'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kiribati', 'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia',
                                'Lebanon', 'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Madagascar', 'Malawi',
                                'Malaysia', 'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico', 'Micronesia',
                                'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal',
                                'Netherlands', 'New Zealand', 'Nicaragua', 'Niger', 'Nigeria', 'North Korea', 'North Macedonia', 'Norway', 'Oman',
                                'Pakistan', 'Palau', 'Palestine State', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru', 'Philippines', 'Poland',
                                'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia', 'Saint Vincent and the Grenadines',
                                'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia', 'Seychelles', 'Sierra Leone',
                                'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'South Korea', 'South Sudan', 'Spain',
                                'Sri Lanka', 'Sudan', 'Suriname', 'Sweden', 'Switzerland', 'Syria','Taiwan', 'Tajikistan', 'Tanzania', 'Thailand', 'Timor-Leste',
                                'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine',
                                'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Venezuela', 'Vietnam',
                                'Yemen', 'Zambia', 'Zimbabwe'
                                ];
                                @endphp
                                @foreach ($countries as $country)
                                <option value="{{ $country }}" {{ old('country') == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                
                        
                    </div>

                    <!-- Section Divider -->
                    <div class="section-divider">
                        <span>Community Preferences</span>
                    </div>

                    <!-- Where are you joining from? Section -->
                    <div class="join-options mb-4">
                        <h4 class="mb-3" style="font-size: 16px; color: #0C3A30; font-weight: 600;">
                            <i class="ri-map-pin-line me-2" style="color: #8bc905;"></i>
                            Where are you joining us from?
                        </h4>

                        <div class="row g-3">
                            <!-- Discord Option -->
                            <div class="col-md-4">
                                <div class="join-option-card {{ old('join_source') == 'discord' ? 'selected' : '' }}" onclick="selectJoinSource(event, 'discord')">
                                    <div class="selected-badge">✓</div>
                                    <i class="ri-discord-line" style="color:blue"></i>
                                    <h5>Discord</h5>
                                    <p>Join via our Discord community</p>
                                </div>
                            </div>

                            <!-- Telegram Option -->
                            <div class="col-md-4">
                                <div class="join-option-card {{ old('join_source') == 'telegram' ? 'selected' : '' }}" onclick="selectJoinSource(event, 'telegram')">
                                    <div class="selected-badge">✓</div>
                                    <i class="ri-telegram-line" style="color: aqua;"></i>
                                    <h5>Telegram</h5>
                                    <p>Join via our Telegram group</p>
                                </div>
                            </div>

                            <!-- Other Option -->
                            <div class="col-md-4">
                                <div class="join-option-card {{ old('join_source') == 'other' ? 'selected' : '' }}" onclick="selectJoinSource(event, 'other')">
                                    <div class="selected-badge">✓</div>
                                    <i class="ri-global-line"></i>
                                    <h5>Other</h5>
                                    <p>Social media, friend, etc.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden input for join_source -->
                        <input type="hidden" name="join_source" id="join_source" value="{{ old('join_source') }}">

                        <!-- Other source input (shown only when 'other' is selected) -->
                        <div id="otherSourceInput" class="other-input {{ old('join_source') == 'other' ? '' : 'hidden' }}">
                            <label for="join_source_other">Please specify:</label>
                            <input type="text" class="form-control" name="join_source_other" id="join_source_other" value="{{ old('join_source_other') }}" placeholder="e.g., Facebook, Twitter, Friend referral">
                        </div>
                    </div>

                    <button id="registerBtn" type="submit" class="default-btn w-100 text-center bg-[#8bc905] text-white relative">
                        <span class="btn-text">Create Account & Join Community</span>
                        <span class="btn-loader hidden absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 50 50">
                                <circle cx="25" cy="25" r="20" stroke="#fff" stroke-width="5" fill="none" stroke-linecap="round">
                                    <animateTransform
                                        attributeName="transform"
                                        type="rotate"
                                        dur="1s"
                                        from="0 25 25"
                                        to="360 25 25"
                                        repeatCount="indefinite" />
                                </circle>
                            </svg>
                        </span>
                    </button>

                    <p class="text-center mt-3 w-100">
                        Already Have An Account?
                        <a href="{{ route('login') }}" style="background-color: #8bc905;"
                            class="inline-block ml-2 px-4 py-2 bg-[#8bc905] text-white rounded">
                            Login
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End My Account Page -->

<script>
// Password toggle
function toggleRegPassword() {
    const input = document.getElementById('regPassword');
    const icon = document.getElementById('regToggleIcon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
    } else {
        input.type = 'password';
        icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
    }
}


// Select join source (FIXED)
function selectJoinSource(event, source) {
    document.getElementById('join_source').value = source;

    document.querySelectorAll('.join-option-card').forEach(card => {
        card.classList.remove('selected');
    });

    event.currentTarget.classList.add('selected');

    const otherInput = document.getElementById('otherSourceInput');

    if (source === 'other') {
        otherInput.classList.remove('hidden');
    } else {
        otherInput.classList.add('hidden');
        document.getElementById('join_source_other').value = '';
    }

    validateForm();
}

// Validation
function validateForm() {
const requiredFields = [
    document.querySelector('input[name="name"]'),
    document.querySelector('input[name="username"]'),
    document.querySelector('input[name="email"]'),
    document.querySelector('input[name="phone"]'),
    document.getElementById('regPassword'),
    document.getElementById('country'),
];

    const joinSource = document.getElementById('join_source').value;
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field || !field.value.trim()) {
            isValid = false;
        }
    });

    if (!joinSource) {
        isValid = false;
    }

    if (joinSource === 'other') {
        const other = document.getElementById('join_source_other').value;
        if (!other.trim()) {
            isValid = false;
        }
    }

    document.getElementById('registerBtn').disabled = !isValid;
}

// Loader
document.getElementById('registerForm').addEventListener('submit', function() {
    const btn = document.getElementById('registerBtn');
    const text = btn.querySelector('.btn-text');
    const loader = btn.querySelector('.btn-loader');

    btn.disabled = true;
    text.style.display = 'none';
    loader.classList.remove('hidden');
});

// Init
document.addEventListener('DOMContentLoaded', function () {
    const joinSource = document.getElementById('join_source').value;

    if (joinSource) {
        document.querySelectorAll('.join-option-card').forEach(card => {
            if (card.getAttribute('onclick')?.includes(joinSource)) {
                card.classList.add('selected');
            }
        });

        if (joinSource === 'other') {
            document.getElementById('otherSourceInput')?.classList.remove('hidden');
        }
    }

    // Attach listeners
    document.querySelectorAll('input, select').forEach(el => {
        el.addEventListener('input', validateForm);
    });

    validateForm();
});
</script>
<style>
    .bg-color-fffaeb {
        background-color: #fffaf0;
        padding: 40px;
        border-radius: 30px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .login-form h3 {
        color: #0C3A30;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .form-label {
        font-weight: 500;
        color: #333;
    }

    .form-control {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px 15px;
    }

    .form-control:focus {
        border-color: #8bc905;
        box-shadow: 0 0 0 0.2rem rgba(139, 201, 5, 0.25);
    }
</style>
@endsection