@extends('layout.admin')
@section('content')

<style>
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-sent { background: #d1fae5; color: #065f46; }
    .status-expired { background: #fee2e2; color: #991b1b; }
    .status-completed { background: #dbeafe; color: #1e40af; }

    .respond-btn {
        background: linear-gradient(135deg, #9EDD05, #8AC304);
        color: #0C3A30;
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .respond-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(158, 221, 5, 0.3);
        color: #0C3A30;
        text-decoration: none;
    }

    .request-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
        position: relative;
    }

    .request-card:hover {
        border-color: #9EDD05;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .request-card.pending {
        border-left: 4px solid #f59e0b;
    }

    .request-card.sent {
        border-left: 4px solid #10b981;
    }

    .pulse-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #ef4444;
        animation: pulse-dot 1.5s infinite;
        margin-right: 6px;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
    }
</style>

<div class="dashboard-main-body">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <div>
            <h5 class="text-2xl font-bold" style="color: #0C3A30;">Bank Transfer Requests</h5>
            <p class="text-sm text-gray-500 mt-1">Review and respond to user bank transfer requests</p>
        </div>
        <ul class="flex items-center gap-[6px]">
            <li class="font-medium">
                <a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color: #0C3A30;">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="font-medium" style="color: #9EDD05;">Bank Transfers</li>
        </ul>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
        <iconify-icon icon="ph:check-circle-fill" class="text-xl"></iconify-icon>
        {{ session('success') }}
    </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500 mb-1">Pending Requests</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $bankTransfers->where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500">
            <p class="text-sm text-gray-500 mb-1">Details Sent</p>
            <p class="text-2xl font-bold text-green-600">{{ $bankTransfers->where('status', 'details_sent')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 mb-1">Total Requests</p>
            <p class="text-2xl font-bold text-blue-600">{{ $bankTransfers->count() }}</p>
        </div>
    </div>

    <!-- Requests List -->
    <div class="grid grid-cols-1 gap-4">
        @forelse($bankTransfers as $bt)
        <div class="request-card {{ $bt->status === 'pending' ? 'pending' : 'sent' }}">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <!-- Left: User Info -->
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-[#9EDD05] bg-opacity-20 flex items-center justify-center flex-shrink-0">
                        <span class="text-lg font-bold" style="color: #0C3A30;">
                            {{ strtoupper(substr($bt->user->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $bt->user->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-500">{{ $bt->user->email ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-400 font-mono">Code: <strong>{{ $bt->request_code }}</strong></p>
                    </div>
                </div>

                <!-- Center: Details -->
                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <p class="text-xs text-gray-400">Amount</p>
                        <p class="font-bold text-green-600">${{ number_format($bt->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Country</p>
                        <p class="font-medium text-gray-700">{{ $bt->country }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Requested</p>
                        <p class="text-sm text-gray-600">{{ $bt->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Status</p>
                        @php
                            $statusClass = match($bt->status) {
                                'pending' => 'status-pending',
                                'details_sent' => 'status-sent',
                                'completed' => 'status-completed',
                                'expired' => 'status-expired',
                                default => 'status-pending',
                            };
                            $statusLabel = match($bt->status) {
                                'pending' => 'Pending',
                                'details_sent' => 'Details Sent',
                                'completed' => 'Completed',
                                'expired' => 'Expired',
                                default => $bt->status,
                            };
                        @endphp
                        <span class="status-badge {{ $statusClass }}">
                            @if($bt->status === 'pending')
                            <span class="pulse-dot"></span>
                            @endif
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <!-- Right: Action -->
                <div>
                    @if($bt->status === 'pending')
                    <a href="{{ route('admin.bank-transfers.send-details', $bt->id) }}" 
                       class="respond-btn">
                        <iconify-icon icon="ph:paper-plane-tilt-fill"></iconify-icon>
                        Send Details
                    </a>
                    @elseif($bt->status === 'details_sent')
                    <span class="inline-flex items-center gap-1 text-green-600 font-semibold">
                        <iconify-icon icon="ph:check-circle-fill"></iconify-icon>
                        Sent
                    </span>
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                <iconify-icon icon="ph:bank-bold" class="text-4xl text-gray-400"></iconify-icon>
            </div>
            <p class="text-gray-500 text-lg mb-2">No Bank Transfer Requests</p>
            <p class="text-gray-400 text-sm">When users request bank transfers, they will appear here.</p>
        </div>
        @endforelse
    </div>
</div>

<script>
    // Auto-refresh for pending counts (optional)
    function refreshPendingCount() {
        fetch('{{ route("admin.bank-transfers.notifications") }}')
            .then(r => r.json())
            .then(data => {
                // Update badge or notification if needed
            })
            .catch(e => console.log('Refresh error:', e));
    }
    setInterval(refreshPendingCount, 60000);
</script>

@endsection