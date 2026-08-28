{{-- resources/views/admin/index.blade.php --}}
@extends('layout.admin')
@section('content')

<style>
    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ef4444;
        color: white;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 50%;
        min-width: 18px;
        text-align: center;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .bank-transfer-notification {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 2px solid #f59e0b;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        display: none;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .bank-transfer-notification.show {
        display: flex;
    }

    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-left: 4px solid #9EDD05;
        transition: all 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .quick-action-card {
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .quick-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .quick-action-card .icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>

<div class="dashboard-main-body">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <h6 class="font-semibold mb-0 text-2xl" style="color: #0C3A30;">Dashboard</h6>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color: #0C3A30;">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="font-medium" style="color: #9EDD05;">Admin</li>
        </ul>
    </div>

    <!-- ════════════════════════════════════════════════ -->
    <!-- BANK TRANSFER NOTIFICATION ALERT               -->
    <!-- ════════════════════════════════════════════════ -->
    <div class="bank-transfer-notification" id="bankTransferAlert">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0">
                <iconify-icon icon="ph:bank-fill" class="text-yellow-600 text-xl"></iconify-icon>
            </div>
            <div>
                <p class="font-semibold text-yellow-800">
                    <span id="bankTransferCount">0</span> New Bank Transfer Request(s)
                </p>
                <p class="text-sm text-yellow-700">Users are waiting for you to send bank details.</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.bank-transfers.requests') }}" 
               class="px-5 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-semibold flex items-center gap-2">
                <iconify-icon icon="ph:eye-fill"></iconify-icon>
                View Requests
            </a>
            <button onclick="dismissBankAlert()" class="px-3 py-2 text-yellow-700 hover:bg-yellow-100 rounded-lg transition">
                <iconify-icon icon="ph:x-bold"></iconify-icon>
            </button>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════ -->
    <!-- STATS CARDS                                    -->
    <!-- ════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-6">
        <!-- Total Users -->
        <div class="stats-card" style="border-left-color: #06b6d4;">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-neutral-900 mb-1">Total Users</p>
                    <h6 class="mb-0 text-2xl font-bold">{{ number_format($totalUsers) }}</h6>
                </div>
                <div class="w-[50px] h-[50px] bg-cyan-600 rounded-full flex justify-center items-center">
                    <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Total Deposits -->
        <div class="stats-card" style="border-left-color: #10b981;">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-neutral-900 mb-1">Total Available Balance</p>
                    <h6 class="mb-0 text-2xl font-bold text-green-600">${{ number_format($totalDeposits, 2) }}</h6>
                </div>
                <div class="w-[50px] h-[50px] bg-green-600 rounded-full flex justify-center items-center">
                    <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Pending Deposits -->
        <div class="stats-card" style="border-left-color: #f59e0b;">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-neutral-900 mb-1">Pending Deposits</p>
                    <h6 class="mb-0 text-2xl font-bold text-yellow-600">{{ $pendingDepositsCount }}</h6>
                </div>
                <div class="w-[50px] h-[50px] bg-yellow-600 rounded-full flex justify-center items-center">
                    <iconify-icon icon="hugeicons:bitcoin-circle" class="text-white text-2xl"></iconify-icon>
                </div>
            </div>
        </div>

        <!-- Pending Withdrawals -->
        <div class="stats-card" style="border-left-color: #8b5cf6;">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-neutral-900 mb-1">Pending Withdrawals</p>
                    <h6 class="mb-0 text-2xl font-bold text-purple-600">{{ $pendingWithdrawalsCount }}</h6>
                </div>
                <div class="w-[50px] h-[50px] bg-purple-600 rounded-full flex justify-center items-center">
                    <iconify-icon icon="hugeicons:money-send-square" class="text-white text-2xl"></iconify-icon>
                </div>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════ -->
    <!-- QUICK ACTION CARDS                             -->
    <!-- ════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">

        <!-- User ID Verification -->
        <a href="{{ route('admin.kyc.index') }}" class="quick-action-card bg-gradient-to-r from-green-600/10 to-white border border-gray-200 hover:border-green-500 transition no-underline">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-neutral-900 mb-1">User ID Verification</p>
                    <span class="text-sm text-green-600">Click to review →</span>
                </div>
                <div class="icon-wrapper bg-green-600">
                    <iconify-icon icon="ph:identification-badge-fill" class="text-white text-2xl"></iconify-icon>
                </div>
            </div>
        </a>

        <!-- Create Payout -->
        <a href="{{ route('admin.payouts.create') }}" class="quick-action-card bg-gradient-to-r from-blue-600/10 to-white border border-gray-200 hover:border-blue-500 transition no-underline">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-neutral-900 mb-1">Create Payout</p>
                    <span class="text-sm text-blue-600">Create new payout →</span>
                </div>
                <div class="icon-wrapper bg-blue-600">
                    <iconify-icon icon="ph:currency-dollar-bold" class="text-white text-2xl"></iconify-icon>
                </div>
            </div>
        </a>

        <!-- Edit User Balance -->
        <a href="{{ route('user.index') }}" class="quick-action-card bg-gradient-to-r from-red-600/10 to-white border border-gray-200 hover:border-red-500 transition no-underline">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-neutral-900 mb-1">Edit User Balance</p>
                    <span class="text-sm text-red-600">Manage users →</span>
                </div>
                <div class="icon-wrapper bg-red-600">
                    <iconify-icon icon="fa6-solid:file-invoice-dollar" class="text-white text-2xl"></iconify-icon>
                </div>
            </div>
        </a>

        <!-- Update Server Names -->
        <a href="{{ route('admin.feeds') }}" class="quick-action-card bg-gradient-to-r from-indigo-600/10 to-white border border-gray-200 hover:border-indigo-500 transition no-underline">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-neutral-900 mb-1">Update Server Names</p>
                    <span class="text-sm text-indigo-600">Manage servers →</span>
                </div>
                <div class="icon-wrapper bg-indigo-600">
                    <iconify-icon icon="ph:server-fill" class="text-white text-2xl"></iconify-icon>
                </div>
            </div>
        </a>

        <!-- Message Client to Email -->
        <a href="{{ route('admin.user.message.form', $user->id ?? 1) }}" class="quick-action-card bg-gradient-to-r from-pink-600/10 to-white border border-gray-200 hover:border-pink-500 transition no-underline">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-neutral-900 mb-1">Message Client to Email</p>
                    <span class="text-sm text-pink-600">Send email →</span>
                </div>
                <div class="icon-wrapper bg-pink-600">
                    <iconify-icon icon="ph:envelope-simple-fill" class="text-white text-2xl"></iconify-icon>
                </div>
            </div>
        </a>

        <!-- View Messages -->
        <a href="{{ route('admin.messages.index') }}" class="quick-action-card bg-gradient-to-r from-teal-600/10 to-white border border-gray-200 hover:border-teal-500 transition no-underline">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="font-medium text-neutral-900 mb-1">View Messages</p>
                    <span class="text-sm text-teal-600">View all →</span>
                </div>
                <div class="icon-wrapper bg-teal-600">
                    <iconify-icon icon="ph:chat-circle-text-fill" class="text-white text-2xl"></iconify-icon>
                </div>
            </div>
        </a>
    </div>
</div>

<script>
    // ─────────────────────────────────────────
    // CHECK BANK TRANSFER NOTIFICATIONS
    // ─────────────────────────────────────────
    function checkBankTransferNotifications() {
        fetch('{{ route("admin.bank-transfers.notifications") }}')
            .then(response => response.json())
            .then(data => {
                const alert = document.getElementById('bankTransferAlert');
                const countEl = document.getElementById('bankTransferCount');
                
                if (data.has_pending && data.count > 0) {
                    alert.classList.add('show');
                    countEl.textContent = data.count;
                } else {
                    alert.classList.remove('show');
                }
            })
            .catch(error => console.error('Error checking notifications:', error));
    }

    function dismissBankAlert() {
        document.getElementById('bankTransferAlert').classList.remove('show');
    }

    // Check for notifications on load and every 60 seconds
    document.addEventListener('DOMContentLoaded', function() {
        checkBankTransferNotifications();
        setInterval(checkBankTransferNotifications, 60000);
    });
</script>

@endsection