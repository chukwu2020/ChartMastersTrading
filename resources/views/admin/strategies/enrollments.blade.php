@extends('layout.admin')

@section('content')
<style>
    :root {
        --primary-green: #9EDD05;
        --dark-green: #0C3A30;
        --accent-green: #8AC304;
    }

    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border-left: 4px solid var(--primary-green);
        transition: all 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .table-container {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 500px;
    }

    .table-container thead th {
        position: sticky;
        top: 0;
        background: #f9fafb;
        z-index: 10;
        border-bottom: 2px solid #e5e7eb;
    }

    .status-badge {
        display: inline-flex;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .status-active { background: #d1fae5; color: #065f46; }
    .status-completed { background: #dbeafe; color: #1e40af; }
    .status-expired { background: #fee2e2; color: #991b1b; }
    .status-upgraded { background: #fef3c7; color: #92400e; }
</style>

<div class="dashboard-main-body">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <div>
            <h5 class="text-2xl font-bold" style="color: #0C3A30;">Course Enrollments</h5>
            <p class="text-sm text-gray-500 mt-1">{{ $strategy->name }} - Student enrollments</p>
        </div>
        <ul class="flex items-center gap-[6px]">
            <li><a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color:#0C3A30;">
                <iconify-icon icon="solar:home-smile-angle-outline"></iconify-icon> Dashboard</a>
            </li>
            <li>-</li>
            <li><a href="{{ route('admin.strategies.strategyindex') }}" class="hover:text-[#9EDD05]" style="color:#0C3A30;">Courses</a></li>
            <li>-</li>
            <li class="font-medium" style="color:#9EDD05;">Enrollments</li>
        </ul>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="stats-card">
            <p class="text-sm text-gray-500 mb-1">Total Enrollments</p>
            <p class="text-2xl font-bold" style="color:#0C3A30;">{{ $enrollments->total() }}</p>
        </div>
        <div class="stats-card" style="border-left-color:#10b981;">
            <p class="text-sm text-gray-500 mb-1">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ $enrollments->where('status', 'active')->count() }}</p>
        </div>
        <div class="stats-card" style="border-left-color:#8b5cf6;">
            <p class="text-sm text-gray-500 mb-1">Completed</p>
            <p class="text-2xl font-bold text-purple-600">{{ $enrollments->where('status', 'completed')->count() }}</p>
        </div>
        <div class="stats-card" style="border-left-color:#f59e0b;">
            <p class="text-sm text-gray-500 mb-1">Total Revenue</p>
            <p class="text-2xl font-bold text-yellow-600">${{ number_format($enrollments->sum('amount_paid'), 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h6 class="text-lg font-bold" style="color:#0C3A30;">Student List</h6>
        </div>

        <div class="table-container">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount Paid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrolled Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($enrollments as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#9EDD05]/20 flex items-center justify-center">
                                    <span class="text-sm font-bold" style="color:#0C3A30;">{{ strtoupper(substr($enrollment->user->name, 0, 1)) }}</span>
                                </div>
                                <span class="font-medium text-gray-900">{{ $enrollment->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $enrollment->user->email }}</td>
                        <td class="px-6 py-4 font-semibold text-green-600">${{ number_format($enrollment->amount_paid, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="status-badge status-{{ $enrollment->status }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-[#9EDD05] h-2 rounded-full" style="width: {{ $enrollment->progress }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600">{{ $enrollment->progress }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('M d, Y') : 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            @if($enrollment->expires_at)
                                {{ $enrollment->expires_at->format('M d, Y') }}
                                @if($enrollment->expires_at->isPast())
                                    <span class="ml-1 text-red-500 text-xs">(Expired)</span>
                                @endif
                            @else
                                <span class="text-green-600">Lifetime</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <iconify-icon icon="ph:users-fill" class="text-5xl text-gray-300 mb-3"></iconify-icon>
                                <p class="text-gray-500 font-medium">No enrollments yet</p>
                                <p class="text-gray-400 text-sm mt-1">Students haven't enrolled in this course</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($enrollments->hasPages())
        <div class="p-6 border-t border-gray-200">
            {{ $enrollments->links() }}
        </div>
        @endif

        <div class="p-6 border-t border-gray-200 bg-gray-50">
            <a href="{{ route('admin.strategies.strategyindex') }}" class="inline-flex items-center gap-2 text-[#0C3A30] hover:text-[#9EDD05] transition">
                <iconify-icon icon="ph:arrow-left-fill"></iconify-icon>
                Back to Courses
            </a>
        </div>
    </div>
</div>
@endsection