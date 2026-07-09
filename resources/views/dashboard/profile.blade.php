@extends('layout.user')

@section('content')
<div class="dashboard-main-body min-h-screen bg-cover bg-center">
    {{-- Header Breadcrumb --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <h6 class="font-semibold mb-0 " style="color: #0c3a30;">My Profile</h6>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('user_dashboard') }}"
                    class="flex items-center gap-2 text-[#0C3A30] hover-text"
                    onmouseover="this.style.color='#9EDD05';"
                    onmouseout="this.style.color='#0C3A30';">
                    <iconify-icon icon="solar:home-smile-angle-outline" style="color: #0c3a30;" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li class="text-[#0C3A30]">-</li>
            <li class="font-medium text-[#0C3A30]">View Profile</li>
        </ul>
    </div>

    {{-- Profile Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left: Personal Info --}}
        <div class="lg:col-span-4">
            <div class="rounded-xl border border-[#9EDD05] shadow-lg bg-opacity-90 p-6 space-y-6" style="background-image: url(assets/images/hero/hero-image-1.svg);">
                {{-- Avatar --}}
                <div class="text-center border-b pb-6 border-gray-300">
                    @php
                    $profilePic = $user->profile->profile_pic ?? null;
                    $initials = collect(explode(' ', $user->name))
                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                    ->take(2)
                    ->join('') ?: 'U';
                    @endphp

                    @if ($profilePic)
                    <img src="{{ asset('storage/profile_pics/' . $profilePic) }}"
                        alt="{{ $user->name }}"onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                         class="mx-auto rounded-full flex items-center justify-center font-semibold text-3xl select-none"
                        style="width: 7rem; height: 7rem; border: 2px solid #8bc905;" />

               
                    @else
                    <div
                        class="mx-auto rounded-full flex items-center justify-center font-semibold text-3xl select-none"
                        style="width: 8rem; height: 8rem; background-color: #9EDD05; color: #0C3A30;">
                        {{ $initials }}
                    </div>
                    @endif
                </div>

                {{-- Personal Info --}}
                <div style="color: #0c3a30;">
                    <h6 class="text-xl font-bold mb-4 text-[#0C3A30]">Personal Info</h6>
                    <ul class="space-y-5 text-sm">
                        <li class="flex justify-between items-start">
                            <span class="font-semibold text-neutral-700">Full Name</span>
                            <span class="text-right text-[#0C3A30] font-medium">{{ $user->name }}</span>
                        </li>
                        <li class="flex justify-between items-start">
                            <span class="font-semibold text-neutral-700">Email</span>
                            <span class="text-right text-[#0C3A30] font-medium">{{ $user->email }}</span>
                        </li>
                        <li class="flex justify-between items-start">
                            <span class="font-semibold text-neutral-700">Phone</span>
                            <span class="text-right text-[#0C3A30] font-medium">{{ $user->phone }}</span>
                        </li>
                        <li class="flex justify-between items-start">
                            <span class="font-semibold text-neutral-700">Country</span>
                            <span class="text-right text-[#0C3A30] font-medium">{{ $user->country }}</span>
                        </li>
                        <li class="flex justify-between items-start">
                            <span class="font-semibold text-neutral-700">Card Number</span>
                            <span class="text-right text-[#0C3A30] font-medium">
                                @if ($card)
                                <div class="card-number">{{ chunk_split($card->card_number, 4, ' ') }}</div>
                                @else
                                <div class="text-gray-500">No card available</div>
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Right: Edit Profile & Change Password --}}
        <div class="lg:col-span-8">
            <div class="card h-full border-0" style="background-image: url(assets/images/hero/hero-image-1.svg);">
                <div class="card-body p-6">
                    {{-- Tabs --}}
                    <ul class="flex gap-3 text-sm font-medium mb-5" id="default-tab" role="tablist">
                        <li>
                            <button class="py-2.5 px-4 rounded-t-lg font-semibold text-base border-t-4"
                                style="color:#0C3A30; background-color:#9EDD05; border-color:#9EDD05;"
                                data-tabs-target="#edit-profile" type="button" aria-selected="true">
                                Edit Profile
                            </button>
                        </li>
                        <li>
                            <button class="py-2.5 px-4 rounded-t-lg font-semibold text-base border-t-4"
                                style="color:#0C3A30; background-color:#9EDD05; border-color:#9EDD05;"
                                data-tabs-target="#change-password" type="button" aria-selected="false">
                                Change Password
                            </button>
                        </li>
                    </ul>

                    {{-- Content Panels --}}
                    <div id="default-tab-content">
                        {{-- Edit Profile --}}
                        <div id="edit-profile">
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Profile Picture --}}
                                <div class="mb-5">
                                    <label class="block font-semibold mb-2 text-[#0C3A30]">Profile Image</label>
                                    <input type="file" name="profile_pic" class="form-control custom-input" />
                                </div>

                                {{-- Basic Info --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">Full Name</label>
                                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control custom-input" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">Email</label>
                                        <input type="email" value="{{ $user->email }}" readonly class="form-control custom-input bg-gray-100 cursor-not-allowed" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">Phone</label>
                                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control custom-input" readonly class="form-control custom-input bg-gray-100 cursor-not-allowed" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">Country</label>
                                        <input type="text" name="country" value="{{ old('country', $user->country) }}" class="form-control custom-input"  class="form-control custom-input bg-gray-100 cursor-not-allowed"/>
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-psm text-[#0C3A30] mb-2">Address</label>
                                        <input type="text" name="address" value="{{ old('address', $user->profile->address ?? '') }}" class="form-control custom-input" />
                                    </div>
                                </div>

                                {{-- Crypto Wallets --}}
                                <h6 class="text-base mt-6 mb-4 font-semibold text-[#0C3A30]">Crypto Wallets</h6>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">Bitcoin Address </label>
                                        <input type="text" name="bitcoin_address" value="{{ old('bitcoin_address', $user->profile->bitcoin_address ?? '') }}" class="form-control custom-input" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">USDT Address (USDT Trc20)</label>
                                        <input type="text" name="usdt_address" value="{{ old('usdt_address', $user->profile->usdt_address ?? '') }}" class="form-control custom-input" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">Ethereum Address</label>
                                        <input type="text" name="etherium_address" value="{{ old('etherium_address', $user->profile->etherium_address ?? '') }}" class="form-control custom-input" />
                                    </div>
                                </div>

                                {{-- Bank Information Section --}}
                                <h6 class="text-base mt-6 mb-4 font-semibold text-[#0C3A30]">Bank Information (for Digital Payment)</h6>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">Recipient Name</label>
                                        <input type="text" name="recipient_name" value="{{ old('recipient_name', $user->profile->recipient_name ?? '') }}" placeholder="Full name on account" class="form-control custom-input" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">Bank Name</label>
                                        <input type="text" name="bank_name" value="{{ old('bank_name', $user->profile->bank_name ?? '') }}" placeholder="Official bank name" class="form-control custom-input" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">Account Number</label>
                                        <input type="text" name="account_number" value="{{ old('account_number', $user->profile->account_number ?? '') }}" placeholder="Local account number" class="form-control custom-input" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">IBAN (if applicable)</label>
                                        <input type="text" name="iban" value="{{ old('iban', $user->profile->iban ?? '') }}" placeholder="International Bank Account Number" class="form-control custom-input" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">SWIFT/BIC Code</label>
                                        <input type="text" name="swift_bic" value="{{ old('swift_bic', $user->profile->swift_bic ?? '') }}" placeholder="International bank code" class="form-control custom-input" />
                                    </div>
                                    <div>
                                        <label class="block font-semibold text-sm text-[#0C3A30] mb-2">Bank Address</label>
                                        <input type="text" name="bank_address" value="{{ old('bank_address', $user->profile->bank_address ?? '') }}" placeholder="Bank branch address" class="form-control custom-input" />
                                    </div>
                                </div>

                                <button type="submit" class="mt-6 btn rounded-full px-10 py-3 font-semibold " style="background-color: #9EDD05; color:#0C3A30;">
                                    Update Profile
                                </button>
                            </form>
                        </div>

                        {{-- Change Password --}}
                        <div id="change-password" class="hidden mt-6">
                            <form action="{{ route('profile.password.update') }}" method="POST">
                                @csrf
                              
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-[#0C3A30] mb-2">Current Password</label>
                                    <input type="password" name="old_password" class="form-control custom-input" required />
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-[#0C3A30] mb-2">New Password</label>
                                    <input type="password" name="new_password" class="form-control custom-input" required />
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-[#0C3A30] mb-2">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" class="form-control custom-input" required />
                                </div>
                                <button type="submit" class="btn rounded-full px-10 py-3 font-semibold " style="background-color: #9EDD05; color:#0C3A30;">
                                    Update Password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tab Toggle Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('[data-tabs-target]');
        const contents = document.querySelectorAll('#default-tab-content > div');

        function switchTab(button) {
            tabs.forEach(btn => btn.setAttribute('aria-selected', false));
            button.setAttribute('aria-selected', true);

            contents.forEach(c => c.classList.add('hidden'));
            const target = document.querySelector(button.getAttribute('data-tabs-target'));
            if (target) target.classList.remove('hidden');
        }

        tabs.forEach(btn => {
            btn.addEventListener('click', () => switchTab(btn));
        });

        if (tabs.length) switchTab(tabs[0]);
    });
</script>

{{-- Styles --}}
<style>
    .custom-input {
        border-top: 1px solid #9EDD05 !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: none !important;
        outline: none !important;
        box-shadow: none !important;
        background-color: transparent !important;
        color: #0C3A30;
    }

    .custom-input:focus {
        border-top: 2px solid #9EDD05 !important;
    }

    .hover-text:hover {
        color: #9EDD05 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const updateForm = document.querySelector('form[action="{{ route('profile.update') }}"]');
        const updateButton = updateForm.querySelector('button[type="submit"]');

        updateForm.addEventListener('submit', function () {
            updateButton.disabled = true;
            updateButton.textContent = 'Updating...';
            updateButton.style.backgroundColor = '#B2B2B2';
            updateButton.style.color = '#666';
        });
    });
</script>

@endsection

