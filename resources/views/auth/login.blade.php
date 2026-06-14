@extends('layout.app')

@section('content')
<style>
    /* Modern Login Page Styling */
    .page-banner-area {
        margin-top: -1rem;
        padding: 3rem 0 !important;
        background-size: cover;
        background-position: center;
        position: relative;
    }
    
    .page-banner-area::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        
        z-index: 1;
    }
    
    .page-banner-content {
        position: relative;
        z-index: 2;
    }
    
    .page-banner-content h1 {
        font-size: 32px !important;
        font-weight: 700;
        margin: 0;
        color: white !important;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .page-banner-content ul {
        margin: 10px 0 0;
        padding: 0;
    }
    
    .page-banner-content ul li {
        display: inline;
        color: rgba(255,255,255,0.9) !important;
        font-size: 14px;
    }
    
    .page-banner-content ul li a {
        color: white !important;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .page-banner-content ul li a:hover {
        color: #8bc905 !important;
    }
    
    @media (max-width: 768px) {
        .page-banner-area {
            margin-top: -1.5rem;
            padding: 2rem 0 !important;
        }
        .page-banner-content h1 {
            font-size: 24px !important;
        }
    }
    
    /* Alert Styling */
    .alert {
        max-width: 800px;
        margin: 20px auto;
        padding: 16px 24px;
        border-radius: 12px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    
    .alert-success {
        background: linear-gradient(135deg, #f0f9e8 0%, #e6f3d8 100%);
        border: 1px solid #8bc905;
        color: #0C3A30;
    }
    
    .alert-danger {
        background: linear-gradient(135deg, #fff1f0 0%, #ffe4e2 100%);
        border: 1px solid #ff4d4f;
        color: #cf1322;
    }
    
    .alert::before {
        font-family: 'remixicon';
        font-size: 20px;
    }
    
    .alert-success::before {
        content: '✓';
        color: #8bc905;
        font-weight: bold;
    }
    
    .alert-danger::before {
        content: '✕';
        color: #ff4d4f;
        font-weight: bold;
    }
    
    /* Login Form Styling */
    .login-form {
        background: white;
        border-radius: 24px !important;
        padding: 40px !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border: 1px solid rgba(139, 201, 5, 0.1);
        transition: all 0.3s ease;
    }
    
    .login-form:hover {
        box-shadow: 0 30px 50px rgba(139, 201, 5, 0.12);
    }
    
    .login-form h3 {
        color: #0C3A30;
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 15px;
    }
    
    .login-form h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 4px;
        background: #8bc905;
        border-radius: 2px;
    }
    
    .login-form label {
        color: #0C3A30;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }
    
    .login-form .form-control {
        height: 50px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0 20px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    
    .login-form .form-control:focus {
        border-color: #8bc905;
        background: white;
        box-shadow: 0 0 0 4px rgba(139, 201, 5, 0.1);
        outline: none;
    }
    
    .login-form .form-control.is-invalid {
        border-color: #ff4d4f;
    }
    
    .text-danger {
        font-size: 12px;
        margin-top: 5px;
        display: block;
        color: #ff4d4f;
    }
    
    /* Password Input Styling */
    .password-input-group {
        position: relative;
    }
    
    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #94a3b8;
        transition: all 0.3s ease;
        z-index: 10;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .password-toggle:hover {
        color: #8bc905;
    }
    
    .toggle-icon {
        display: inline-block;
        width: 20px;
        height: 20px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z'%3E%3C/path%3E%3Ccircle cx='12' cy='12' r='3'%3E%3C/circle%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
    }
    
    .password-toggle.show-password .toggle-icon {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24'%3E%3C/path%3E%3Cline x1='1' y1='1' x2='23' y2='23'%3E%3C/line%3E%3C/svg%3E");
    }
    
    /* Login Options */
    .login-warp {
        margin: 25px 0;
        padding: 20px 0;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .form-check {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }
    
    .form-check input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #8bc905;
        margin: 0;
    }
    
    .form-check label {
        margin: 0;
        font-weight: 400;
        color: #64748b;
        font-size: 14px;
        cursor: pointer;
    }
    
    .password-link {
        color: #64748b;
        text-decoration: none;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .password-link:hover {
        color: #8bc905;
        text-decoration: underline;
    }
    
    /* Resend verification button */
    .btn-link {
        background: none;
        border: none;
        color: #8bc905;
        font-size: 13px;
        padding: 0;
        margin-left: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-link:hover {
        color: #76ad03;
        text-decoration: underline;
    }
    
    /* Login Button */
    .default-btn {
        background: #8bc905;
        color: white;
        height: 55px;
        border: none;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 20px 0;
        position: relative;
        overflow: hidden;
    }
    
    .default-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .default-btn:hover {
        background: #76ad03;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(139, 201, 5, 0.4);
    }
    
    .default-btn:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .default-btn:active {
        transform: translateY(0);
    }
    
    /* Sign up link */
    .login-form p {
        text-align: center;
        color: #64748b;
        font-size: 15px;
        margin: 20px 0 0;
    }
    
    .login-form p a {
        color: #8bc905;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .login-form p a:hover {
        color: #76ad03;
        text-decoration: underline;
    }
    
    /* Animation */
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    [data-cues="slideInRight"] {
        animation: slideInRight 0.8s ease forwards;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .login-form {
            padding: 30px 20px !important;
        }
        
        .login-form h3 {
            font-size: 24px;
        }
        
        .login-warp {
            flex-direction: column;
            gap: 15px;
            align-items: flex-start !important;
        }
        
        .login-warp > div:last-child {
            align-self: flex-start;
        }
    }
</style>

<!-- Start Page Banner Area -->
<div class="page-banner-area position-relative" style="background-image: url(assets/images/hero/hero-image-2.svg); background-size: cover; background-position: center;">
    <div class="container"  >
        <div class="page-banner-content text-center"  >
            <h1 style="font-size: 20px; margin: 0; color: #0C3A30 !important;">Welcome Back!</h1>
            <ul style="font-size: 20px; margin: 0; color: #0C3A30 !important;">
                <li ><a style=" color: #0C3A30 !important;" href="/">Home</a></li>
                <li style=" color: #0C3A30 !important;">  My Account</li>
            </ul>
        </div>
    </div>
</div>
<!-- End Page Banner Area -->

<!-- Start My Account Page -->
<div class="my-account-page pt-80 pb-80 overflow-hidden" >
    <div class="container"  >
        <div class="row g-4 justify-content-center">
            <div class="col-lg-6 col-md-8" data-cues="slideInRight" data-duration="800">
                
                <!-- Alert Messages -->
                @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <!-- Add this after the existing alert messages -->
@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert" style="background: #fff3cd; border: 1px solid #ffeeba; color: #856404;">
        <i class="ri-information-line me-2"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
                <!-- Login Form -->
                <form action="{{ route('login') }}" method="POST" class="login-form">
                    @csrf
                    <h3>Log In To Your Account</h3>

                    <div class="row">
                        <div class="mb-4 col-12">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" 
                                   placeholder="Enter your email"
                                   required>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4 col-12">
                            <label for="password">Password</label>
                            <div class="password-input-group">
                                <input type="password" name="password" id="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Enter your password"
                                       required>
                                <span class="password-toggle">
                                    <i class="toggle-icon"></i>
                                </span>
                            </div>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex login-warp gap-4 align-items-center justify-content-between">
                        <div class="form-check">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Keep me logged in</label>
                        </div>
                        
                        <div class="d-flex align-items-center gap-3">
                            @if(session('status') === 'Please verify your email before logging in.')
                                <form method="POST" action="{{ route('password.otp.request') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-link">
                                        <i class="ri-mail-send-line me-1"></i>Resend verification
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('password.request') }}" class="password-link">
                                <i class="ri-lock-line me-1"></i>Forgot Password?
                            </a>
                        </div>
                    </div>

                    <button type="submit" class="default-btn w-100 text-center">
                        <i class="ri-login-circle-line me-2"></i>Log In
                    </button>

                    <p>
                        Don't have an account? 
                        <a href="{{ route('signup') }}" style="background-color: #8bc905;"
       class="inline-block ml-2 px-4 py-2 bg-[#8bc905] text-white rounded">Create one </a>
                    </p>

                    
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End My Account Page -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('.password-toggle');
        const password = document.querySelector('#password');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                // Toggle the eye icon
                this.classList.toggle('show-password');
            });
        }

        // Add floating label effect
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.querySelector('label')?.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.querySelector('label')?.classList.remove('focused');
                }
            });
        });
    });
</script>

@endsection