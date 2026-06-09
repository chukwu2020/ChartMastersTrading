@extends('layout.admin')

@section('content')

<style>
    :root {
        --brand-green: #9EDD05;
        --brand-dark: #0C3A30;
    }

    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 50;
        background: white;
        margin: 0;
        padding: 1rem 1rem;
    }

    .table-wrapper {
        background: white;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .table-scroll {
        overflow-x: auto;
        overflow-y: auto;
        max-height: calc(100vh - 280px);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
        min-width: 1600px;
    }

    thead th {
        background: linear-gradient(135deg, #0C3A30, #1a5c47);
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.85rem 0.75rem;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
        border-right: 1px solid rgba(255, 255, 255, 0.08);
    }

    thead th:first-child {
        position: sticky;
        left: 0;
        z-index: 20;
        background: linear-gradient(135deg, #0C3A30, #1a5c47);
    }

    tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }

    tbody tr:hover {
        background: #f8fffe;
    }

    tbody td {
        padding: 0.75rem;
        color: #374151;
        vertical-align: middle;
        white-space: nowrap;
        border-right: 1px solid #f3f4f6;
    }

    tbody td:first-child {
        position: sticky;
        left: 0;
        background: white;
        z-index: 5;
        box-shadow: 2px 0 6px rgba(0, 0, 0, 0.06);
    }

    tbody tr:hover td:first-child {
        background: #f8fffe;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        min-width: 100px;
        overflow: hidden;
    }

    .avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 2px solid var(--brand-green);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        font-weight: 700;
        font-size: 0.8rem;
        color: var(--brand-dark);
        overflow: hidden;
    }

    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .user-meta {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .user-meta strong {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
        max-width: 110px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 0.2rem 0.55rem;
        border-radius: 20px;
        font-size: 0.62rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-active {
        background: #dcfce7;
        color: #15803d;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #dc2626;
    }

    .badge-pending {
        background: #fef9c3;
        color: #854d0e;
    }

    .badge-blue {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-gray {
        background: #f3f4f6;
        color: #374151;
    }

    .badge-purple {
        background: #f3e8ff;
        color: #6b21a8;
    }

    .money {
        font-weight: 700;
        font-family: monospace;
        font-size: 0.8rem;
    }

    .money.green {
        color: #16a34a;
    }

    .money.blue {
        color: #2563eb;
    }

    .money.orange {
        color: #ea580c;
    }

    .tag-list {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
        max-width: 180px;
    }

    .mini-tag {
        background: #f3f4f6;
        color: #374151;
        border-radius: 4px;
        padding: 1px 6px;
        font-size: 0.58rem;
        font-weight: 500;
        white-space: nowrap;
    }

    .code-cell {
        font-family: monospace;
        font-size: 0.72rem;
        font-weight: 700;
        color: #111827;
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-radius: 6px;
        padding: 2px 8px;
        display: inline-block;
        cursor: pointer;
        transition: background 0.2s;
    }

    .code-cell:hover {
        background: #dcfce7;
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .act-btn {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.85rem;
        text-decoration: none;
    }

    .act-btn:hover {
        transform: scale(1.12);
    }

    .act-btn.view {
        background: #dbeafe;
        color: #2563eb;
    }

    .act-btn.edit {
        background: #dcfce7;
        color: #16a34a;
    }

    .act-btn.delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .act-btn.lock {
        background: #f3f4f6;
        color: #6b7280;
    }

    .act-btn.locked {
        background: #fef9c3;
        color: #ca8a04;
    }

    .act-btn.gen {
        background: #ede9fe;
        color: #6b21a8;
    }
</style>

<!-- Sticky Header -->
<div class="sticky-header">
    <div class="flex items-center justify-between gap-2 mb-4">
        <h6 class="font-semibold mb-0" style="color:#0C3A30;">Users List</h6>
        <ul class="flex items-center gap-[6px] text-sm">
            <li><a href="{{ route('admin_dashboard') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color:#0C3A30;"><iconify-icon icon="solar:home-smile-angle-outline" class="text-lg"></iconify-icon> Dashboard</a></li>
            <li>-</li>
            <li><a href="{{ route('hidden.user') }}" class="flex items-center gap-2 hover:text-[#9EDD05]" style="color:#0C3A30;"><iconify-icon icon="solar:users-group-rounded" class="text-lg"></iconify-icon> List</a></li>
        </ul>
    </div>
</div>

<div class="dashboard-main-body">
    <div class="p-3 border-b border-gray-100 flex justify-end">
        <input type="text" id="userSearch" placeholder="Search name or email..." class="border border-gray-300 rounded-lg px-3 py-1 text-xs w-64 focus:outline-none focus:ring-2 focus:ring-green-400" onkeyup="filterUsers()">
    </div>

    <div class="grid grid-cols-12">
        <div class="col-span-12">
            <div class="card h-full p-0 rounded-xl border-0 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-scroll">
                        <table id="usersTable">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Join Source</th>
                                    <th>Phone</th>
                                    <th>Country</th>
                                    <th>Balance</th>
                                    <th>Total Invested</th>
                                    <th>Active Trades</th>
                                    <th>Invest Amount</th>
                                    <th>Learning Style</th>
                                    <th>Experience</th>
                                    <th>Trade Frequency</th>
                                    <th>Txn Volume</th>
                                    <th>Account Type</th>
                                    <th>Investment Goals</th>
                                    <th>Asset Classes</th>
                                    <th>Copy Preference</th>
                                    <th>Copy Admin</th>
                                    <th>Copy Server</th>
                                    <th>Initial Deposit Src</th>
                                    <th>Membership Code</th>
                                    <th>Membership Status</th>
                                    <th>Code Lock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                @php
                                $activeTrades = $user->investments->where('status', 'active')->count();
                                $profilePic = $user->profile->profile_pic ?? null;
                                $initials = collect(explode(' ', $user->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') ?: 'U';
                                $tradingInfo = $user->tradingInfo;

                                $investmentGoals = [];
                                $assetClasses = [];
                                if ($tradingInfo) {
                                $investmentGoals = is_array($tradingInfo->investment_goals) ? $tradingInfo->investment_goals : json_decode($tradingInfo->investment_goals ?? '[]', true) ?? [];
                                $assetClasses = is_array($tradingInfo->asset_classes) ? $tradingInfo->asset_classes : json_decode($tradingInfo->asset_classes ?? '[]', true) ?? [];
                                }

                                $profileUrl = null;
                                if ($profilePic && file_exists(public_path('uploads/profile_pics/' . $profilePic))) {
                                $profileUrl = asset('uploads/profile_pics/' . $profilePic);
                                }
                                @endphp

                                <tr class="user-row" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                                    <td>
                                        <div class="user-cell">
                                            <div class="avatar">
                                                @if($profileUrl)
                                                <img src="{{ $profileUrl }}" alt="{{ $user->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div style="display:none; width:100%; height:100%; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem; color:#0C3A30; background:#9EDD05; border-radius:50%;">{{ $initials }}</div>
                                                @else
                                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem; color:#0C3A30; background:#9EDD05; border-radius:50%;">{{ $initials }}</div>
                                                @endif
                                            </div>
                                            <div class="user-meta"><strong>{{ $user->name }}</strong></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-xs text-gray-500 truncate" style="max-width: 180px;">{{ $user->email }}</div>
                                    </td>
                                    <td><span class="badge badge-gray">{{ $user->username ?? '—' }}</span></td>
                                    <td>@if($user->active)<span class="badge badge-active">✓ Active</span>@else<span class="badge badge-inactive">✗ Inactive</span>@endif</td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td>{{ $user->join_source ? ucfirst(str_replace('_', ' ', $user->join_source)) : '—' }}</td>
                                    <td>{{ $user->phone ?? '—' }}</td>
                                    <td>{{ $user->country ?? '—' }}</td>
                                    <td><span class="money green">${{ number_format($user->available_balance ?? 0, 2) }}</span></td>
                                    <td><span class="money blue">${{ number_format($user->amount_invested ?? 0, 2) }}</span></td>
                                    <td><span class="badge {{ $activeTrades > 0 ? 'badge-blue' : 'badge-gray' }}">{{ $activeTrades }}</span></td>
                                    <td>@if($tradingInfo && $tradingInfo->investment_amount)<span class="money orange">${{ number_format($tradingInfo->investment_amount, 2) }}</span>@else<span class="text-gray-400">—</span>@endif</td>

                                    {{-- Learning Style --}}
                                    <td>
                                        @php
                                        $learningLabels = [
                                        'video' => 'Video Tutorials',
                                        'webinar' => 'Live Webinars',
                                        'articles' => 'Articles & Guides',
                                        'mentoring' => 'One-on-One Mentoring',
                                        'community' => 'Community Forums',
                                        'practice' => 'Paper Trading',
                                        ];

                                        $learningStyle = $user->tradingInfo?->learning_style;
                                        @endphp

                                        @if($learningStyle)
                                        <span class="badge badge-purple">
                                            {{ $learningLabels[$learningStyle] ?? ucfirst($learningStyle) }}
                                        </span>
                                        @else
                                        <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    {{-- Experience --}}
                                    <td>@if($tradingInfo)@php $exp = $tradingInfo->stock_experience; $expLabel = $exp === 'yes' ? 'Experienced' : ($exp === 'no' ? 'Learning' : 'Novice'); $expClass = $exp === 'yes' ? 'badge-active' : ($exp === 'no' ? 'badge-pending' : 'badge-gray'); @endphp<span class="badge {{ $expClass }}">{{ $expLabel }}</span>@else<span class="text-gray-400">—</span>@endif</td>

                                    <td>{{ $tradingInfo->trading_frequency ?? '—' }}</td>
                                    <td>{{ $tradingInfo && $tradingInfo->transaction_volume ? ucfirst(str_replace(['_', 'k'], [' ', ',000'], $tradingInfo->transaction_volume)) : '—' }}</td>
                                    <td>@if($tradingInfo && $tradingInfo->account_type)<span class="badge badge-blue">{{ ucfirst($tradingInfo->account_type) }}</span>@else<span class="text-gray-400">—</span>@endif</td>

                                    {{-- Investment Goals --}}
                                    <td>@if(count($investmentGoals))<div class="tag-list">@foreach($investmentGoals as $g)<span class="mini-tag">{{ ucfirst(str_replace('_', ' ', $g)) }}</span>@endforeach</div>@else<span class="text-gray-400">—</span>@endif</td>

                                    {{-- Asset Classes --}}
                                    <td>@if(count($assetClasses))<div class="tag-list">@foreach($assetClasses as $a)<span class="mini-tag">{{ ucfirst($a) }}</span>@endforeach</div>@else<span class="text-gray-400">—</span>@endif</td>

                                    {{-- Copy Trading --}}
                                    <td>@php $pref = $user->copy_preference; $prefLabel = $pref === 'platform_admin' ? 'Platform Admin' : ($pref === 'specific_admin' ? 'Specific Admin' : 'Not Set'); $prefClass = $pref ? 'badge-purple' : 'badge-gray'; @endphp<span class="badge {{ $prefClass }}">{{ $prefLabel }}</span></td>
                                    <td>{{ $user->copy_admin_name ?? '—' }}</td>
                                    <td>{{ $user->copy_server_name ?? '—' }}</td>
                                    <td>{{ $tradingInfo && $tradingInfo->deposit_source ? ucfirst(str_replace('_', ' ', $tradingInfo->deposit_source)) : '—' }}</td>

                                    {{-- Membership --}}
                                    <td>@if($user->membership_code)<span class="code-cell" onclick="copyCode(this, '{{ $user->membership_code }}')" title="Click to copy">{{ $user->membership_code }}</span>@else<button onclick="generateMembershipCode({{ $user->id }}, this)" class="act-btn gen" style="width:auto; border-radius:8px; padding:0 10px; font-size:0.65rem; font-weight:700; gap:4px; display:inline-flex; height:26px;"><iconify-icon icon="ph:plus-bold"></iconify-icon> Generate</button>@endif</td>
                                    <td>
    @if($user->membership_code)
        @if((int)$user->has_membership === 1)
            <span class="badge badge-active">✓ Active</span>
        @else
            <span class="badge badge-pending">⏳ Pending</span>
        @endif
    @else
        <span class="text-gray-400">—</span>
    @endif
</td>
                                    <td>@if($user->membership_code)<form method="POST" action="{{ route('admin.membership.lock', $user->id) }}" style="display:inline;">@csrf @method('PATCH')<button type="submit" class="act-btn {{ $user->membership_locked ? 'locked' : 'lock' }}" title="{{ $user->membership_locked ? 'Unlock membership' : 'Lock membership' }}"><iconify-icon icon="{{ $user->membership_locked ? 'mdi:lock-open' : 'mdi:lock' }}"></iconify-icon></button></form>@else<span class="text-gray-400">—</span>@endif</td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="action-group">
                                            <a href="#" class="act-btn view" title="View"><iconify-icon icon="heroicons:eye"></iconify-icon></a>
                                            <a href="{{ route('user.edit', $user->id) }}" class="act-btn edit" title="Edit"><iconify-icon icon="lucide:edit"></iconify-icon></a>
                                            <form method="POST" action="{{ route('user.destroy', $user->id) }}" onsubmit="return confirm('Delete {{ addslashes($user->name) }}? This cannot be undone.');" style="display:inline;">@csrf @method('DELETE')<button type="submit" class="act-btn delete" title="Delete"><iconify-icon icon="fluent:delete-24-regular"></iconify-icon></button></form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="27" style="text-align:center; padding:3rem; color:#9ca3af;"><iconify-icon icon="mdi:users-off" style="font-size:2.5rem; display:block; margin:0 auto 0.75rem;"></iconify-icon>No users found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-2 p-4 border-t border-gray-100">
                    <span class="text-sm text-gray-600">Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} entries</span>
                    <div>{{ $users->links('vendor.pagination.tailwind') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyCode(el, code) {
        navigator.clipboard.writeText(code).then(() => {
            const orig = el.textContent;
            el.textContent = 'Copied!';
            el.style.background = '#dcfce7';
            el.style.borderColor = '#16a34a';
            el.style.color = '#15803d';
            setTimeout(() => {
                el.textContent = orig;
                el.style.background = '';
                el.style.borderColor = '';
                el.style.color = '';
            }, 2000);
        });
    }

    function generateMembershipCode(userId, btn) {
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '…';
        fetch("{{ route('admin.generate.membership.code') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    user_id: userId
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    btn.disabled = false;
                    btn.innerHTML = orig;
                    alert(data.message || 'Failed to generate code.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = orig;
                alert('Something went wrong.');
            });
    }

    function filterUsers() {
        const input = document.getElementById("userSearch").value.toLowerCase();
        document.querySelectorAll(".user-row").forEach(row => {
            const match = row.dataset.name.includes(input) || row.dataset.email.includes(input);
            row.style.display = match ? "" : "none";
        });
    }
</script>

@endsection