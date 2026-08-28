@extends('layout.admin')
@section('content')

<style>
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
    }
    .form-section h4 {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0C3A30;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    .f-input {
        width: 100%;
        padding: 0.6rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    .f-input:focus {
        border-color: #9EDD05;
        box-shadow: 0 0 0 3px rgba(158, 221, 5, 0.15);
        outline: none;
    }
    .f-label {
        display: block;
        font-size: 0.78rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.3rem;
    }
    .user-info-card {
        background: linear-gradient(135deg, #f8faf7, #eef7ea);
        border-radius: 12px;
        padding: 1.5rem;
        border-left: 4px solid #9EDD05;
        margin-bottom: 1.5rem;
    }
    .send-btn {
        background: linear-gradient(135deg, #9EDD05, #8AC304);
        color: #0C3A30;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
        position: relative;
    }
    .send-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(158, 221, 5, 0.3);
    }
    .send-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }
    .send-btn .spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(12, 58, 48, 0.2);
        border-top-color: #0C3A30;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    .send-btn.loading .spinner {
        display: inline-block;
    }
    .send-btn.loading .btn-text {
        display: none;
    }
    .send-btn.loading .btn-loading-text {
        display: inline;
    }
    .btn-loading-text {
        display: none;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Toast notification */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 10px;
        max-width: 400px;
        width: 100%;
    }
    .toast {
        background: #0C3A30;
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 12px;
        animation: slideInRight 0.4s ease;
        border-left: 4px solid #9EDD05;
    }
    .toast.error {
        border-left-color: #ef4444;
    }
    .toast.success {
        border-left-color: #9EDD05;
    }
    .toast iconify-icon {
        font-size: 1.5rem;
        flex-shrink: 0;
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(100px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .toast.fade-out {
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.4s ease;
    }
</style>

<div class="dashboard-main-body">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <div>
            <h5 class="text-2xl font-bold" style="color: #0C3A30;">Send Bank Details</h5>
            <p class="text-sm text-gray-500 mt-1">Fill in the bank account details for this user</p>
        </div>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color: #0C3A30;">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="font-medium" style="color: #9EDD05;">Send Bank Details</li>
        </ul>
    </div>

    <!-- User Info -->
    <div class="user-info-card">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Request Code</p>
                <p class="text-xl font-bold font-mono" style="color: #0C3A30;">{{ $bankTransfer->request_code }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">User</p>
                <p class="font-semibold text-gray-800">{{ $bankTransfer->user->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ $bankTransfer->user->email ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Country</p>
                <p class="font-semibold text-gray-800">{{ $bankTransfer->country }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Amount</p>
                <p class="text-2xl font-bold text-green-600">${{ number_format($bankTransfer->amount, 2) }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider">Requested</p>
                <p class="text-sm text-gray-600">{{ $bankTransfer->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.bank-transfers.send-details.post', $bankTransfer->id) }}" method="POST" id="bankDetailsForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div>
                <div class="form-section">
                    <h4><iconify-icon icon="ph:bank-fill" class="mr-2"></iconify-icon> Bank Account Details</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="f-label">Bank Name <span class="text-red-500">*</span></label>
                            <input type="text" name="bank_name" class="f-input" placeholder="e.g. JPMorgan Chase Bank" required>
                        </div>
                        <div>
                            <label class="f-label">Account Name <span class="text-red-500">*</span></label>
                            <input type="text" name="account_name" class="f-input" placeholder="e.g. ChartMasters Trading Inc." required>
                        </div>
                        <div>
                            <label class="f-label">Account Number <span class="text-red-500">*</span></label>
                            <input type="text" name="account_number" class="f-input" placeholder="e.g. 8901234567" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4><iconify-icon icon="ph:info-fill" class="mr-2"></iconify-icon> Additional Information</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="f-label">SWIFT Code</label>
                            <input type="text" name="swift_code" class="f-input" placeholder="e.g. CHASUS33">
                        </div>
                        <div>
                            <label class="f-label">Routing Number</label>
                            <input type="text" name="routing_number" class="f-input" placeholder="e.g. 021000021">
                        </div>
                        <div>
                            <label class="f-label">IBAN</label>
                            <input type="text" name="iban" class="f-input" placeholder="e.g. GB76BARC20304098765432">
                        </div>
                        <div>
                            <label class="f-label">Sort Code</label>
                            <input type="text" name="sort_code" class="f-input" placeholder="e.g. 20-30-40">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div>
                <div class="form-section">
                    <h4><iconify-icon icon="ph:map-pin-fill" class="mr-2"></iconify-icon> Bank Address</h4>
                    <div>
                        <label class="f-label">Bank Address</label>
                        <textarea name="bank_address" rows="4" class="f-input" placeholder="Full bank address including city, state, zip code..."></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h4><iconify-icon icon="ph:note-pencil-fill" class="mr-2"></iconify-icon> Transfer Instructions</h4>
                    <div>
                        <label class="f-label">Additional Instructions for User</label>
                        <textarea name="instructions" rows="5" class="f-input" placeholder="e.g. Please include the reference code in your transfer description. Transfers take 1-2 business days to process."></textarea>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="{{ route('admin.bank-transfers.requests') }}" class="px-6 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-semibold">
                        Cancel
                    </a>
                    <button type="submit" class="send-btn" id="submitBtn">
                        <span class="spinner"></span>
                        <span class="btn-text">
                            <iconify-icon icon="ph:paper-plane-tilt-fill"></iconify-icon>
                            Send Bank Details to User
                        </span>
                        <span class="btn-loading-text">
                            <iconify-icon icon="ph:spinner-gap-bold" style="animation: spin 0.8s linear infinite; display: inline-block;"></iconify-icon>
                            Sending...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bankDetailsForm');
    const submitBtn = document.getElementById('submitBtn');
    let isSubmitting = false;

    form.addEventListener('submit', function(e) {
        // Prevent double submission
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }

        // Validate required fields
        const bankName = document.querySelector('input[name="bank_name"]');
        const accountName = document.querySelector('input[name="account_name"]');
        const accountNumber = document.querySelector('input[name="account_number"]');

        if (!bankName.value.trim()) {
            e.preventDefault();
            showToast('Please enter the Bank Name', 'error');
            bankName.focus();
            return false;
        }

        if (!accountName.value.trim()) {
            e.preventDefault();
            showToast('Please enter the Account Name', 'error');
            accountName.focus();
            return false;
        }

        if (!accountNumber.value.trim()) {
            e.preventDefault();
            showToast('Please enter the Account Number', 'error');
            accountNumber.focus();
            return false;
        }

        // Mark as submitting
        isSubmitting = true;
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');

        // Show loading toast
        showToast('Sending bank details to user...', 'info');

        // Allow form to submit normally
        return true;
    });

    // Show toast notifications
    function showToast(message, type = 'info') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        let icon = 'ph:info-fill';
        if (type === 'success') icon = 'ph:check-circle-fill';
        if (type === 'error') icon = 'ph:x-circle-fill';

        toast.innerHTML = `
            <iconify-icon icon="${icon}"></iconify-icon>
            <span>${message}</span>
        `;

        container.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('fade-out');
            setTimeout(() => {
                toast.remove();
            }, 400);
        }, 5000);
    }

    // Handle form submission response (if there are validation errors)
    @if($errors->any())
        @foreach($errors->all() as $error)
            showToast('{{ $error }}', 'error');
        @endforeach
    @endif

    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif

    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
});
</script>

@endsection