@extends('layout.user')

@section('content')

<div class="max-w-2xl mx-auto mt-10 mb-10">
    @php
        $kycStatus = auth()->user()->kycStatus ?? null;
        $kycData = auth()->user()->userKyc ?? null;
    @endphp

    {{-- CASE 1: KYC Already Approved --}}
    @if($kycStatus === 'approved')
    <div class="bg-white shadow-lg rounded-xl overflow-hidden" style="background-image: url(assets/images/hero/hero-image-1.svg); background-repeat: repeat; background-size: cover; background-position: center;">
        <div class="p-8 text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                <iconify-icon icon="ph:check-circle-fill" class="text-4xl text-green-600"></iconify-icon>
            </div>
            <h2 class="text-2xl font-bold text-green-700 mb-2">Identity Verified!</h2>
            <p class="text-gray-600 mb-4">Your identity has been successfully verified. You have full access to all platform features.</p>
            <div class="bg-green-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-700">
                    <iconify-icon icon="ph:shield-check-fill" class="inline mr-1"></iconify-icon>
                    Verified on: {{ $kycData?->updated_at?->format('M d, Y') ?? 'N/A' }}
                </p>
            </div>
            <a href="{{ route('user_dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold transition-all" style="background-color: #9EDD05; color: #0C3A30;">
                <iconify-icon icon="ph:house-bold"></iconify-icon>
                Back to Dashboard
            </a>
        </div>
    </div>

    {{-- CASE 2: KYC Pending Approval --}}
    @elseif($kycStatus === 'pending')
    <div class="bg-white shadow-lg rounded-xl overflow-hidden" style="background-image: url(assets/images/hero/hero-image-1.svg); background-repeat: repeat; background-size: cover; background-position: center;">
        <div class="p-8 text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-yellow-100 rounded-full flex items-center justify-center">
                <iconify-icon icon="ph:clock-fill" class="text-4xl text-yellow-600"></iconify-icon>
            </div>
            <h2 class="text-2xl font-bold text-yellow-700 mb-2">Verification in Progress</h2>
            <p class="text-gray-600 mb-4">Your KYC documents are currently being reviewed by our team.</p>
            <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-yellow-700">
                    <iconify-icon icon="ph:info-fill" class="inline mr-1"></iconify-icon>
                    Submitted on: {{ $kycData?->created_at?->format('M d, Y') ?? 'N/A' }}
                </p>
                <p class="text-xs text-gray-500 mt-2">We typically review KYC submissions within 24-48 hours.</p>
            </div>
            <a href="{{ route('user_dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold transition-all" style="background-color: #9EDD05; color: #0C3A30;">
                <iconify-icon icon="ph:house-bold"></iconify-icon>
                Back to Dashboard
            </a>
        </div>
    </div>

    {{-- CASE 3: KYC Rejected --}}
    @elseif($kycStatus === 'rejected')
    <div class="bg-white shadow-lg rounded-xl overflow-hidden" style="background-image: url(assets/images/hero/hero-image-1.svg); background-repeat: repeat; background-size: cover; background-position: center;">
        <div class="p-8">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <iconify-icon icon="ph:x-circle-fill" class="text-4xl text-red-600"></iconify-icon>
                </div>
                <h2 class="text-2xl font-bold text-red-700 mb-2">Verification Failed</h2>
                <p class="text-gray-600">Your KYC submission was rejected. Please review the reason below and resubmit.</p>
            </div>

            @if($kycData?->admin_note)
            <div class="bg-red-50 rounded-lg p-4 mb-6 border-l-4 border-red-500">
                <p class="text-sm font-semibold text-red-700 mb-1">Rejection Reason:</p>
                <p class="text-sm text-red-600">{{ $kycData->admin_note }}</p>
            </div>
            @endif

            <div class="bg-amber-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-amber-700 flex items-center gap-2">
                    <iconify-icon icon="ph:lightbulb-fill"></iconify-icon>
                    Tips for successful verification:
                </p>
                <ul class="text-xs text-amber-600 mt-2 space-y-1 list-disc list-inside">
                    <li>Ensure your ID document is clearly visible and not blurry</li>
                    <li>Make sure all 4 corners of the ID are visible in the photo</li>
                    <li>Your selfie should clearly show your face matching the ID</li>
                    <li>Avoid using flash that causes glare on the document</li>
                </ul>
            </div>

            {{-- Resubmit Form --}}
            <form action="{{ route('user.kyc.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block mb-2 font-medium text-gray-700">Upload Government ID / Passport</label>
                    <input type="file" name="id_document" required class="w-full border-2 border-gray-300 rounded-lg p-2 focus:border-[#9EDD05] focus:outline-none transition">
                    <p class="text-xs text-gray-400 mt-1">Accepted: JPG, PNG, PDF (Max 5MB)</p>
                    @error('id_document') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-2 font-medium text-gray-700">Upload Selfie of Yourself</label>
                    <input type="file" name="utility_bill" required class="w-full border-2 border-gray-300 rounded-lg p-2 focus:border-[#9EDD05] focus:outline-none transition">
                    <p class="text-xs text-gray-400 mt-1">Take a clear selfie holding your ID or just your face clearly visible</p>
                    @error('utility_bill') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-center mt-6">
                    <button type="submit" id="kycSubmitBtn" class="px-8 py-3 rounded-lg font-semibold shadow-lg flex items-center justify-center gap-2 transition-all duration-300 hover:shadow-xl transform hover:scale-105" style="background-color: #9EDD05; color: #0C3A30;" onclick="handleKycSubmit(this)">
                        <span>Resubmit KYC</span>
                        <svg id="spinner" class="w-5 h-5 animate-spin hidden text-[#0C3A30]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 018 8h-4l3 3 3-3h-4a8 8 0 01-8 8v-4l-3 3 3 3v-4a8 8 0 01-8-8z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- CASE 4: No KYC Submitted Yet --}}
    @else
    <div class="bg-white shadow-lg rounded-xl overflow-hidden" style="background-image: url(assets/images/hero/hero-image-1.svg); background-repeat: repeat; background-size: cover; background-position: center;">
        <div class="p-8">
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                    <iconify-icon icon="ph:identification-badge-fill" class="text-4xl text-blue-600"></iconify-icon>
                </div>
                <h2 class="text-2xl font-bold" style="color: #0C3A30;">Verify Your Identity</h2>
                <p class="text-gray-600 mt-2">Please complete KYC verification to unlock all platform features</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <iconify-icon icon="ph:shield-check-fill" class="text-2xl text-[#9EDD05] mx-auto mb-2"></iconify-icon>
                    <p class="text-xs text-gray-600">Enhanced Security</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <iconify-icon icon="ph:arrow-up-bold" class="text-2xl text-[#9EDD05] mx-auto mb-2"></iconify-icon>
                    <p class="text-xs text-gray-600">Higher Withdrawal Limits</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <iconify-icon icon="ph:check-circle-fill" class="text-2xl text-[#9EDD05] mx-auto mb-2"></iconify-icon>
                    <p class="text-xs text-gray-600">Verified Badge</p>
                </div>
            </div>

            <form action="{{ route('user.kyc.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block mb-2 font-medium text-gray-700">Upload Government ID / Passport <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-[#9EDD05] transition cursor-pointer" onclick="document.getElementById('id_document').click()">
                        <iconify-icon icon="ph:upload-bold" class="text-2xl text-gray-400 mb-2"></iconify-icon>
                        <p class="text-sm text-gray-500">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-400">JPG, PNG, PDF (Max 5MB)</p>
                    </div>
                    <input type="file" name="id_document" id="id_document" required class="hidden" accept="image/*,application/pdf">
                    <span id="id_document_name" class="text-xs text-green-600 block mt-1"></span>
                    @error('id_document') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-2 font-medium text-gray-700">Upload Selfie of Yourself <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-[#9EDD05] transition cursor-pointer" onclick="document.getElementById('selfie').click()">
                        <iconify-icon icon="ph:camera-bold" class="text-2xl text-gray-400 mb-2"></iconify-icon>
                        <p class="text-sm text-gray-500">Click to upload your selfie</p>
                        <p class="text-xs text-gray-400">Take a clear photo showing your face</p>
                    </div>
                    <input type="file" name="utility_bill" id="selfie" required class="hidden" accept="image/*">
                    <span id="selfie_name" class="text-xs text-green-600 block mt-1"></span>
                    @error('utility_bill') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-center mt-6">
                    <button type="submit" id="kycSubmitBtn" class="px-8 py-3 rounded-lg font-semibold shadow-lg flex items-center justify-center gap-2 transition-all duration-300 hover:shadow-xl transform hover:scale-105" style="background-color: #9EDD05; color: #0C3A30;" onclick="handleKycSubmit(this)">
                        <span>Submit KYC</span>
                        <svg id="spinner" class="w-5 h-5 animate-spin hidden text-[#0C3A30]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 018 8h-4l3 3 3-3h-4a8 8 0 01-8 8v-4l-3 3 3 3v-4a8 8 0 01-8-8z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<script>
    function handleKycSubmit(btn) {
        btn.disabled = true;
        btn.style.backgroundColor = '#cccccc';
        btn.querySelector('span').innerText = 'Submitting...';
        btn.querySelector('#spinner').classList.remove('hidden');
        btn.form.submit();
    }

    // Show selected file names
    document.getElementById('id_document')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            document.getElementById('id_document_name').textContent = '✓ ' + fileName;
        }
    });

    document.getElementById('selfie')?.addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            document.getElementById('selfie_name').textContent = '✓ ' + fileName;
        }
    });
</script>

@endsection