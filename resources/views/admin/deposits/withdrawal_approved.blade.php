@extends('layout.admin')

@section('content')
<div class="dashboard-main-body p-4 sm:p-6 lg:p-8">

    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <h6 class="font-semibold mb-0">Approved Withdrawals</h6>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-primary-600">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="font-medium">Approved Withdrawals</li>
        </ul>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    @if(isset($approvedWithdrawals) && $approvedWithdrawals->isNotEmpty())
    <div class="grid grid-cols-1 gap-6">
        <div class="card border-0">
            <div class="card-body p-6 overflow-x-auto">
                <table class="min-w-[800px] w-full table mb-0 divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        32
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($approvedWithdrawals as $withdrawal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                #{{ $withdrawal->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $withdrawal->user->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $withdrawal->user->email ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                ${{ number_format($withdrawal->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $withdrawal->type }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($withdrawal->status == 'approved')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Failed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $withdrawal->created_at->format('d M, Y h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($withdrawal->type == 'balance')
                                    @if($withdrawal->status == 'approved')
                                        <button type="button"
                                                class="btn-unapprove open-unapprove-modal"
                                                data-withdrawal-id="{{ $withdrawal->id }}"
                                                data-unapprove-url="{{ route('withdraw.unapprove', $withdrawal->id) }}">
                                            Mark as Failed
                                        </button>
                                    @else
                                        <span class="text-red-600 text-sm font-semibold">Failed</span>
                                    @endif
                                @else
                                    <form action="{{ route('withdraw.delete', $withdrawal->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-6xl mb-4">
                <iconify-icon icon="ph:check-circle-fill" class="inline"></iconify-icon>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Approved Withdrawals</h3>
            <p class="text-gray-500">There are no approved withdrawal requests at the moment.</p>
        </div>
    @endif
</div>

<style>
    .btn-unapprove {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        background-color: #f59e0b;
        color: #ffffff;
        font-size: 14px;
        font-weight: 500;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-unapprove:hover {
        background-color: #d97706;
    }
    .btn-unapprove:active {
        transform: scale(0.97);
    }
    .btn-unapprove:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.4);
    }
    
    .btn-unapprove.processing {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 6px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const unapproveModal = document.getElementById('unapproveModal');
    const unapproveForm = document.getElementById('unapproveForm');
    const unapproveNote = document.getElementById('unapprove_note');
    
    // Prevent form from being submitted multiple times
    let isSubmitting = false;

    document.querySelectorAll('.open-unapprove-modal').forEach(button => {
        button.addEventListener('click', () => {
            const unapproveUrl = button.getAttribute('data-unapprove-url');
            if (unapproveUrl) {
                unapproveForm.action = unapproveUrl;
            }
            unapproveNote.value = '';
            unapproveModal.classList.remove('hidden');
            unapproveModal.style.display = 'flex';
        });
    });

    document.querySelectorAll('.close-unapprove-modal').forEach(button => {
        button.addEventListener('click', () => {
            unapproveModal.classList.add('hidden');
            unapproveModal.style.display = 'none';
        });
    });
    
    // Handle form submission with loading state and prevent double submission
    unapproveForm.addEventListener('submit', function(e) {
        // Prevent double submission
        if (isSubmitting) {
            e.preventDefault();
            return false;
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            // Disable button and show loading state
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Processing...';
            
            // Also disable all unapprove buttons to prevent clicking another one
            document.querySelectorAll('.open-unapprove-modal').forEach(btn => {
                btn.disabled = true;
                btn.classList.add('processing');
            });
        }
        
        // Allow form to submit
        return true;
    });

    unapproveModal.addEventListener('click', (e) => {
        if (e.target === unapproveModal) {
            unapproveModal.classList.add('hidden');
            unapproveModal.style.display = 'none';
        }
    });
});
</script>

{{-- Unapprove/Failed Modal --}}
<div id="unapproveModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold mb-4" style="color: #0C3A30;">Mark as Failed</h3>
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
            <p class="text-sm text-yellow-800">⚠️ This will restore the amount to the user's balance and mark as failed.</p>
        </div>
        <form id="unapproveForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="unapprove_note" class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for Failure <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="admin_note" 
                    id="unapprove_note" 
                    rows="4" 
                    required
                    maxlength="500"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#8AC304] focus:border-transparent"
                    placeholder="Please explain why this withdrawal failed..."></textarea>
                <p class="text-xs text-gray-500 mt-1">Max 500 characters. User will see this message.</p>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" 
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition close-unapprove-modal">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Confirm Failed
                </button>
            </div>
        </form>
    </div>
</div>
@endsection