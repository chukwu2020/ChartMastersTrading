{{-- resources/views/admin/strategies/index.blade.php --}}
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

    /* 🔥 TABLE CONTAINER WITH SCROLLING */
    .table-container {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 500px; /* Adjust this value as needed */
        position: relative;
    }

    /* 🔥 STICKY TABLE HEADER */
    .table-container table {
        width: 100%;
        border-collapse: collapse;
    }

    .table-container thead th {
        position: sticky;
        top: 0;
        background: #f9fafb;
        z-index: 10;
        border-bottom: 2px solid #e5e7eb;
    }

    /* Custom scrollbar styling */
    .table-container::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: var(--primary-green);
        border-radius: 10px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: var(--accent-green);
    }
</style>

<div class="dashboard-main-body">
    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
        <div>
            <h5 class="text-2xl font-bold" style="color: #0C3A30;">Trading Courses & Strategies</h5>
            <p class="text-sm text-gray-500 mt-1">Manage educational courses and trading strategies</p>
        </div>
        <ul class="flex items-center gap-[6px]">
            <li><a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color:#0C3A30;">
                <iconify-icon icon="solar:home-smile-angle-outline"></iconify-icon> Dashboard</a>
            </li>
            <li>-</li>
            <li class="font-medium" style="color:#9EDD05;">Courses</li>
        </ul>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="stats-card"><p class="text-sm text-gray-500 mb-1">Total Courses</p><p class="text-2xl font-bold" style="color:#0C3A30;">{{ $stats['total_strategies'] }}</p></div>
        <div class="stats-card" style="border-left-color:#10b981;"><p class="text-sm text-gray-500 mb-1">Active Courses</p><p class="text-2xl font-bold text-green-600">{{ $stats['active_strategies'] }}</p></div>
        <div class="stats-card" style="border-left-color:#f59e0b;"><p class="text-sm text-gray-500 mb-1">Total Enrollments</p><p class="text-2xl font-bold text-yellow-600">{{ $stats['total_enrollments'] }}</p></div>
        <div class="stats-card" style="border-left-color:#8b5cf6;"><p class="text-sm text-gray-500 mb-1">Active Enrollments</p><p class="text-2xl font-bold text-purple-600">{{ $stats['active_enrollments'] }}</p></div>
        <div class="stats-card" style="border-left-color:#06b6d4;"><p class="text-sm text-gray-500 mb-1">Total Revenue</p><p class="text-2xl font-bold text-cyan-600">${{ number_format($stats['total_revenue'], 2) }}</p></div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h6 class="text-lg font-bold" style="color:#0C3A30;">All Courses</h6>
            <a href="{{ route('admin.strategies.strategycreate') }}" class="px-4 py-2 bg-[#9EDD05] text-[#0C3A30] rounded-lg hover:bg-[#8AC304] transition font-semibold">
                <iconify-icon icon="ph:plus-fill" class="inline mr-1"></iconify-icon> Add New Course
            </a>
        </div>
        
        <!-- 🔥 TABLE CONTAINER WITH SCROLLING -->
        <div class="table-container">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky top-0 bg-gray-50">Course</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky top-0 bg-gray-50">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky top-0 bg-gray-50">Enrollments</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky top-0 bg-gray-50">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase sticky top-0 bg-gray-50">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase sticky top-0 bg-gray-50">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($strategies as $strategy)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($strategy->cover_image)
                                <img src="{{ Storage::url($strategy->cover_image) }}" class="w-10 h-10 rounded-lg object-cover">
                                @endif
                                <div>
                                    <p class="font-medium text-gray-900">{{ $strategy->name }}</p>
                                    <p class="text-xs text-gray-500">{{ Str::limit($strategy->description, 60) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-green-600">${{ number_format($strategy->price, 2) }}</td>
                        <td class="px-6 py-4">{{ $strategy->enrollments_count }}</td>
                        <td class="px-6 py-4">
                            @if($strategy->is_active)
                            <span class="inline-flex px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Active</span>
                            @else
                            <span class="inline-flex px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-semibold">Inactive</span>
                            @endif
                            @if($strategy->is_popular)
                            <span class="inline-flex ml-1 px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">Popular</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $strategy->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.strategies.strategyenrollments', $strategy->id) }}" class="text-blue-600 hover:text-blue-800 transition" title="View Enrollments">
                                    <iconify-icon icon="ph:users-fill" class="text-xl"></iconify-icon>
                                </a>
                                <a href="{{ route('admin.strategies.strategyedit', $strategy->id) }}" class="text-blue-600 hover:text-blue-800 transition" title="Edit">
                                    <iconify-icon icon="ph:pencil-fill" class="text-xl"></iconify-icon>
                                </a>
                                <form action="{{ route('admin.strategies.strategydestroy', $strategy->id) }}" method="POST" class="inline" onsubmit="return confirm('⚠️ Delete this course? This action cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition" title="Delete">
                                        <iconify-icon icon="ph:trash-fill" class="text-xl"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <iconify-icon icon="ph:book-open-fill" class="text-5xl text-gray-300 mb-3"></iconify-icon>
                                <p class="text-gray-500 font-medium">No courses found</p>
                                <p class="text-gray-400 text-sm mt-1">Click "Add New Course" to create your first trading course</p>
                                <a href="{{ route('admin.strategies.strategycreate') }}" class="mt-4 px-4 py-2 bg-[#9EDD05] text-[#0C3A30] rounded-lg hover:bg-[#8AC304] transition font-semibold">
                                    <iconify-icon icon="ph:plus-fill" class="inline mr-1"></iconify-icon> Create First Course
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($strategies->hasPages())
        <div class="p-6 border-t border-gray-200">
            {{ $strategies->links() }}
        </div>
        @endif
    </div>
</div>
@endsection