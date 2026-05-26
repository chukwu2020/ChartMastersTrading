{{-- resources/views/dashboard/strategies/learn.blade.php --}}
{{--
    CONTROLLER NOTE: Pass $adminProfile to this view.
    In your StrategiesController@learn:
        $adminProfile = \App\Models\User::where('role','admin')->first();
        return view('dashboard.strategies.learn', compact('strategy','enrollment','adminProfile'));
--}}
@extends('layout.user')

@section('content')
<style>
    :root {
        --pg: #9EDD05;
        --dg: #0C3A30;
        --ag: #8AC304;
    }

    /* ── Grid layout ── */
    .learn-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 1.5rem;
        align-items: start;
    }

    @media(max-width:1024px) {
        .learn-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ── Sidebar ── */
    .l-sidebar {
        background: white;
        border: 1.5px solid #e5e7eb;
        border-radius: 1.25rem;
        overflow: hidden;
        position: sticky;
        top: 1.25rem;
        max-height: calc(100vh - 2.5rem);
        overflow-y: auto;
    }

    .l-sidebar::-webkit-scrollbar {
        width: 4px;
    }

    .l-sidebar::-webkit-scrollbar-thumb {
        background: var(--pg);
        border-radius: 2px;
    }

    .sb-head {
        background: linear-gradient(135deg, #0C3A30, #1a5c46);
        padding: 1.25rem;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .sb-head h2 {
        color: #ffffff !important;
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .sb-pbar {
        width: 100%;
        height: 5px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 3px;
        overflow: hidden;
    }

    .sb-pfill {
        height: 100%;
        background: var(--pg);
        border-radius: 3px;
        transition: width 0.5s ease;
    }

    .mod-nav {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0.75rem 1.25rem;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s;
        font-size: 0.82rem;
        color: #374151;
        user-select: none;
    }

    .mod-nav:last-child {
        border-bottom: none;
    }

    .mod-nav:hover {
        background: #f9fafb;
        color: var(--dg);
    }

    .mod-nav.active {
        background: linear-gradient(135deg, rgba(158, 221, 5, 0.09), rgba(138, 195, 4, 0.05));
        color: var(--dg);
        font-weight: 700;
        border-left: 3px solid var(--pg);
    }

    .mn-num {
        width: 26px;
        height: 26px;
        background: #f3f4f6;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        color: #6b7280;
        flex-shrink: 0;
        transition: all 0.2s;
    }

    .mod-nav.active .mn-num {
        background: linear-gradient(135deg, var(--pg), var(--ag));
        color: var(--dg);
    }

    .mod-nav.done .mn-num {
        background: #dcfce7;
        color: #16a34a;
    }

    /* ── Content area ── */
    .mod-card {
        background: white;
        border: 1.5px solid #e5e7eb;
        border-radius: 1.25rem;
        overflow: hidden;
        animation: fadeUp 0.3s ease;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(8px)
        }

        to {
            opacity: 1;
            transform: translateY(0)
        }
    }

 .mod-card-head {
    background: linear-gradient(135deg, #0C3A30, #0d4535);
    padding: 1.5rem 1.75rem;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.mod-card-head::after {
    content: '';
    position: absolute;
    top: -40%;
    right: -10%;
    width: 40%;
    height: 180%;
    background: rgba(158, 221, 5, 0.05);
    transform: rotate(20deg);
    z-index: 0;
    pointer-events: none;
}

.mod-card-head > * {
    position: relative;
    z-index: 2;
}
    .mod-card-head h1 {
        color: #ffffff !important;
        font-size: 1.35rem;
        font-weight: 700;
    }

    .mod-card-head p {
        color: rgba(255, 255, 255, 0.6) !important;
        font-size: 0.82rem;
        margin-top: 4px;
    }

    .mod-body {
        padding: 1.75rem;
    }

    /* ── About This Course (shown in learn page) ── */
    .about-course-panel {
        background: linear-gradient(135deg, #f8fff5, #f0fdf4);
        border: 1.5px solid rgba(158, 221, 5, 0.25);
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.75rem;
    }

    .about-course-panel h3 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--dg);
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 0.875rem;
    }

    .about-course-panel h3 iconify-icon {
        color: var(--pg);
        font-size: 1.1rem;
    }

    .about-course-panel .prose-text {
        font-size: 0.9rem;
        color: #374151;
        line-height: 1.8;
    }

    /* ── Instructor strip ── */
    .instr-strip {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        background: linear-gradient(135deg, #f8fff5, #f0fdf4);
        border: 1.5px solid rgba(158, 221, 5, 0.2);
        border-radius: 10px;
        margin-bottom: 1.75rem;
    }

    .is-av {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--pg);
        flex-shrink: 0;
    }

    .is-init {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--pg), var(--ag));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        color: var(--dg);
        flex-shrink: 0;
    }

    /* Live Trading CTA */
    .live-trading-cta {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #0C3A30, #1a5c46);
        border-radius: 1rem;
        border: 2px solid rgba(158, 221, 5, 0.3);
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .live-trading-cta::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 40%;
        height: 200%;
        background: rgba(158, 221, 5, 0.06);
        transform: rotate(20deg);
    }

    .live-trading-cta:hover {
        border-color: rgba(158, 221, 5, 0.6);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .live-dot {
        width: 10px;
        height: 10px;
        background: #ef4444;
        border-radius: 50%;
        animation: livePulse 1.5s ease infinite;
        flex-shrink: 0;
    }

    @keyframes livePulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1)
        }

        50% {
            opacity: 0.6;
            transform: scale(1.3)
        }
    }

    .live-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: linear-gradient(135deg, var(--pg), var(--ag));
        color: var(--dg);
        font-weight: 700;
        font-size: 0.875rem;
        border-radius: 10px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s;
    }

    .live-btn:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 12px rgba(158, 221, 5, 0.35);
        color: var(--dg);
        text-decoration: none;
    }

    /* Video embed */
    .vid-wrap {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        border-radius: 0.875rem;
        background: #000;
        margin-bottom: 1.5rem;
    }

    .vid-wrap iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
        border-radius: 0.875rem;
    }

    .vid-ext-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #0C3A30, #1a5c46);
        color: var(--pg) !important;
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 10px;
        text-decoration: none;
        margin-bottom: 1.5rem;
        transition: all 0.2s;
    }

    .vid-ext-btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
        text-decoration: none;
    }

    /* Prose */
    .mod-prose {
        font-size: 0.9375rem;
        color: #374151;
        line-height: 1.85;
    }

    /* Nav + complete row */
    .nav-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #f3f4f6;
    }

    .btn-prev {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        background: white;
        border: 1.5px solid #e5e7eb;
        color: var(--dg);
        transition: all 0.2s;
    }

    .btn-prev:hover {
        border-color: var(--pg);
        background: #f8fff5;
    }

    .btn-next {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        background: linear-gradient(135deg, var(--pg), var(--ag));
        color: var(--dg);
        border: none;
        transition: all 0.2s;
    }

    .btn-next:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(158, 221, 5, 0.3);
    }

    button:disabled {
        opacity: 0.35;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    .btn-complete {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        background: #f0fdf4;
        border: 1.5px solid #86efac;
        color: #15803d;
        transition: all 0.2s;
    }

    .btn-complete:hover:not(.done) {
        background: #dcfce7;
        border-color: #4ade80;
    }

    .btn-complete.done {
        background: linear-gradient(135deg, #16a34a, #22c55e);
        color: white !important;
        border-color: transparent;
        cursor: default;
    }

    .btn-complete.loading {
        opacity: 0.7;
        cursor: wait;
    }

    /* Toast */
    .l-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        padding: 0.875rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        z-index: 9999;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        animation: toastIn 0.3s ease;
    }

    @keyframes toastIn {
        from {
            opacity: 0;
            transform: translateX(40px)
        }

        to {
            opacity: 1;
            transform: translateX(0)
        }
    }

    .l-toast.success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .l-toast.error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .l-toast.info {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93c5fd;
    }

    .spin {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-top-color: white;
        border-radius: 50%;
        animation: sp 0.8s linear infinite;
        vertical-align: middle;
        margin-right: 5px;
    }

    @keyframes sp {
        to {
            transform: rotate(360deg)
        }
    }
</style>

<div class="dashboard-main-body">

    {{-- Breadcrumb --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-5">
        <h5 class="font-semibold mb-0" style="color:var(--dg);">Learning</h5>
        <ul class="flex items-center gap-[6px] text-sm">
            <li><a href="{{ route('user_dashboard') }}" class="flex items-center gap-1 hover:text-[#9EDD05]" style="color:var(--dg);">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="text-lg"></iconify-icon> Dashboard
                </a></li>
            <li>-</li>
            <li><a href="{{ route('strategies.strategyindex') }}" class="hover:text-[#9EDD05]" style="color:var(--dg);">Academy</a></li>
            <li>-</li>
            <li><a href="{{ route('strategies.strategyshow', $strategy->id) }}" class="hover:text-[#9EDD05]" style="color:var(--dg);">{{ Str::limit($strategy->name,22) }}</a></li>
            <li>-</li>
            <li class="font-medium" style="color:var(--pg);">Learn</li>
        </ul>
    </div>

    @php
    $mods = $strategy->modules
    ? (is_array($strategy->modules) ? $strategy->modules : (json_decode($strategy->modules,true) ?? []))
    : [];
    $total = count($mods);
    // Instructor from ServerFeed
    $instrName = $adminProfile->admin_name ?? 'Trading Instructor';

    $instrAvatar = !empty($adminProfile->admin_profile_image)
    ? 'admins/' . $adminProfile->admin_profile_image
    : null;

    $instrInitial = strtoupper(substr($instrName, 0, 1));
    @endphp

    @if($total === 0)
    <div class="bg-white rounded-2xl border border-gray-200 p-14 text-center">
        <iconify-icon icon="ph:book-open-fill" class="text-5xl text-gray-300 mb-4"></iconify-icon>
        <h3 class="text-xl font-bold mb-2" style="color:var(--dg);">Content Coming Soon</h3>
        <p class="text-gray-500 mb-5">The instructor is preparing course materials. Check back soon!</p>
        <a href="{{ route('strategies.strategyindex') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold" style="background:var(--pg);color:var(--dg);text-decoration:none;">
            <iconify-icon icon="ph:arrow-left-bold"></iconify-icon> Back to Academy
        </a>
    </div>
    @else

    <div class="learn-grid">

        {{-- ── Sidebar ── --}}
        <div class="l-sidebar">
            <div class="sb-head">
                <h2>{{ Str::limit($strategy->name,34) }}</h2>
                <div class="flex justify-between text-xs mb-1">
                    <span style="color:#fff!important;">Progress</span>
                    <span style="color:#fff!important;" id="pct-label">{{ $enrollment->progress }}%</span>
                </div>
                <div class="sb-pbar">
                    
                    <div class="sb-pfill" id="sidebar-bar" style="width:{{ $enrollment->progress }}%;"></div>
                   
                </div>
                <p class="text-xs mt-1.5" style="color:#fff !important;;">
                    <span style="color:#fff !important;" id="done-count">0</span> / {{ $total }} modules complete
                </p>
            </div>
            @foreach($mods as $i => $mod)
            @php $mt = is_array($mod) ? ($mod['title'] ?? 'Module '.($i+1)) : ($mod->title ?? 'Module '.($i+1)); @endphp
            <div class="mod-nav {{ $i===0?'active':'' }}" id="nav-{{ $i }}" onclick="goTo({{ $i }})">
                <div class="mn-num" id="mn-{{ $i }}">
                    <iconify-icon id="mn-icon-{{ $i }}" icon="{{ $i===0?'ph:play-fill':'ph:dot-fill' }}"></iconify-icon>
                </div>
                <span class="leading-snug text-xs">{{ $mt }}</span>
            </div>
            @endforeach
        </div>

        {{-- ── Main ── --}}
        <div class="l-main">
            @foreach($mods as $i => $mod)
            @php
            $mt = is_array($mod) ? ($mod['title'] ?? 'Module '.($i+1)) : ($mod->title ?? 'Module '.($i+1));
            $mc = is_array($mod) ? ($mod['content'] ?? '') : ($mod->content ?? '');
            $mv = is_array($mod) ? ($mod['video_url']?? null) : ($mod->video_url?? null);
            $ytId = null;
            if($mv && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $mv, $ym)) {
            $ytId = $ym[1];
            }
            @endphp
            <div class="mod-card" id="mod-{{ $i }}" style="{{ $i>0?'display:none;':'' }}">
                <div class="mod-card-head">
           
                        <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span style="padding:3px 10px;background:rgba(158,221,5,0.15);border:1px solid rgba(158,221,5,0.3);border-radius:30px;font-size:0.7rem;font-weight:700;color:#9EDD05 !important;">
                                Module {{ $i+1 }} of {{ $total }}
                            </span>
                        </div>
                        <h1>{{ $mt }}</h1>
                        <p>
                            {{ $strategy->name }} · Instructor: {{ $instrName }}
                        </p>
                    </div>
                </div>
                <div class="mod-body">

                    {{-- ── About This Course (long_description) — shown on the FIRST module only, collapsed on others ── --}}
                   @if($strategy->long_description)
                    <div class="about-course-panel">
                        <h3>
                            <iconify-icon style="color:#9EDD05 !important;" icon="ph:info-fill"></iconify-icon>
                            About This Course
                        </h3>
                       
                            <div class="prose-text" id="aboutText-{{ $i }}">
                            {!! nl2br(e(Str::limit($strategy->long_description, 400))) !!}
                        </div>
                        @if(strlen($strategy->long_description) > 400)
                        <div id="aboutFull-{{ $i }}" style="display:none;">
                            <div class="prose-text">{!! nl2br(e($strategy->long_description)) !!}</div>
                        </div>
                       
                        <button onclick="toggleAbout({{ $i }})"
                            class="mt-3 text-xs font-bold flex items-center gap-1"
                            style="color:var(--dg);background:none;border:none;cursor:pointer;padding:0;">
                          <iconify-icon id="aboutToggleIcon-{{ $i }}"icon="ph:caret-down-bold"></iconify-icon>
                         <span id="aboutToggleTxt-{{ $i }}">Read more</span>
                        </button>
                        @endif
                    </div>
                    @endif

                    {{-- ── Instructor strip ── --}}
                    <div class="instr-strip">
                        @if($instrAvatar)
                        <img src="{{ Storage::url($instrAvatar) }}" alt="{{ $instrName }}" class="is-av">
                        @else
                        <div class="is-init">{{ $instrInitial }}</div>
                        @endif
                        <div>
                            <p class="text-xs font-bold" style="color:var(--dg);">{{ $instrName }}</p>
                            <p class="text-xs text-gray-400">Your Instructor · Professional Trader</p>
                        </div>
                        <a href="{{ route('strategies.strategyshow', $strategy->id) }}" class="ml-auto text-xs font-semibold" style="color:var(--dg);text-decoration:none;">
                            Course Info →
                        </a>
                    </div>

                    {{-- ▶ LIVE TRADING CTA --}}
                    <div class="live-trading-cta">
                        <div class="relative z-10 flex items-center gap-4 flex-1">
                            <div class="live-dot"></div>
                            <div>
                                <p class="font-bold text-sm" style="color:#ffffff !important;">Join Live Trading Session</p>
                                <p class="text-xs" style="color:rgba(255,255,255,0.6) !important;">Watch your instructor trade in real-time and learn by doing</p>
                            </div>
                        </div>
                        <a href="{{ route('user_live') }}" class="live-btn relative z-10">
                            <iconify-icon icon="ph:broadcast-fill" class="text-base"></iconify-icon>
                            Join Live
                        </a>
                    </div>




                    {{-- Nav row --}}
                    <div class="nav-row">
                        <button class="btn-prev" onclick="goTo({{ $i-1 }})" {{ $i===0?'disabled':'' }}>
                            <iconify-icon icon="ph:arrow-left-bold"></iconify-icon> Previous
                        </button>

                        <button class="btn-complete" id="complete-{{ $i }}" onclick="markDone({{ $i }})">
                            <iconify-icon icon="ph:check-circle-fill"></iconify-icon>
                            <span id="complete-txt-{{ $i }}">Mark as Complete</span>
                        </button>

                        @if($i < $total - 1)
                            <button class="btn-next" onclick="goTo({{ $i+1 }})">
                            Next <iconify-icon icon="ph:arrow-right-bold"></iconify-icon>
                            </button>
                            @else
                            <a href="{{ route('strategies.strategyindex') }}" class="btn-next" style="text-decoration:none;">
                                Finish <iconify-icon icon="ph:check-bold"></iconify-icon>
                            </a>
                            @endif
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Completion screen --}}
            <div id="completion-screen" style="display:none;" class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                <div style="background:linear-gradient(135deg,#0C3A30,#1a5c46);padding:3rem 2rem;text-align:center;">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center" style="background:rgba(158,221,5,0.2);border:3px solid rgba(158,221,5,0.5);">
                        <iconify-icon icon="ph:check-fat-fill" style="font-size:2.5rem;color:#9EDD05;"></iconify-icon>
                    </div>
                    <h2 style="color:#ffffff!important;font-size:2rem;font-weight:800;margin-bottom:0.5rem;">Course Completed! </h2>
                    <p style="color:rgba(255,255,255,0.7)!important;">You've completed all {{ $total }} modules of {{ $strategy->name }}.</p>
                </div>
                <div class="p-8 text-center">
                    <p class="text-gray-600 mb-6">Your certificate has been earned. Continue growing your trading skills.</p>
                    <div class="flex items-center justify-center gap-4 flex-wrap">
                        <a href="{{ route('strategies.strategyindex') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold" style="background:var(--pg);color:var(--dg);text-decoration:none;">
                            <iconify-icon icon="ph:books-fill"></iconify-icon> Browse More Courses
                        </a>
                        <a href="{{ route('user_live') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold" style="background:#0C3A30;color:#9EDD05 !important;text-decoration:none;">
                            <iconify-icon icon="ph:broadcast-fill"></iconify-icon> Join Live Trading
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @endif
</div>

<script>
const TOTAL      = {{ $total }};
const CSRF       = '{{ csrf_token() }}';
const INIT_PCT   = {{ intval($enrollment->progress ?? 0) }};
const STORAGEKEY = 'strategy_progress_{{ $strategy->id }}';

let doneSet = new Set();
let currentIdx = 0;

/* ─────────────────────────────
   LOCAL STORAGE
───────────────────────────── */
function saveLocalProgress() {
    localStorage.setItem(STORAGEKEY, JSON.stringify({
        completed: [...doneSet],
        current: currentIdx,
        completedCourse: doneSet.size >= TOTAL
    }));
}

function loadLocalProgress() {
    const saved = localStorage.getItem(STORAGEKEY);

    if (!saved) {
        // fallback from DB %
        const doneMods = Math.round((INIT_PCT / 100) * TOTAL);

        for (let i = 0; i < doneMods; i++) {
            doneSet.add(i);
        }

        return;
    }

    try {
        const parsed = JSON.parse(saved);

        if (parsed.completed) {
            parsed.completed.forEach(i => doneSet.add(i));
        }

        currentIdx = parsed.current ?? 0;

    } catch (e) {
        console.error(e);
    }
}

/* ─────────────────────────────
   MODULE NAVIGATION
───────────────────────────── */
function goTo(idx) {
    if (idx < 0 || idx >= TOTAL) return;

    document.querySelectorAll('[id^="mod-"]').forEach((el, i) => {
        el.style.display = (i === idx) ? 'block' : 'none';
    });

    document.querySelectorAll('.mod-nav').forEach((el, i) => {
        el.classList.toggle('active', i === idx);

        const icon = document.getElementById('mn-icon-' + i);

        if (icon && !doneSet.has(i)) {
            icon.setAttribute(
                'icon',
                i === idx ? 'ph:play-fill' : 'ph:dot-fill'
            );
        }
    });

    currentIdx = idx;

    saveLocalProgress();

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

/* ─────────────────────────────
   MARK COMPLETE
───────────────────────────── */
function markDone(idx) {

    const btn = document.getElementById('complete-' + idx);

    if (
        !btn ||
        btn.classList.contains('done') ||
        btn.classList.contains('loading')
    ) return;

    btn.classList.add('loading');
    btn.disabled = true;

    document.getElementById(
        'complete-txt-' + idx
    ).innerHTML = '<span class="spin"></span>Saving...';

    doneSet.add(idx);

    saveLocalProgress();

    const newPct = Math.round((doneSet.size / TOTAL) * 100);

    fetch('/strategies/{{ $strategy->id }}/progress', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            progress: newPct
        }),
    })
    .then(r => r.json())
    .then(data => {

        btn.classList.remove('loading');
        btn.classList.add('done');
        btn.disabled = true;

        document.getElementById(
            'complete-txt-' + idx
        ).textContent = 'Completed ✓';

        const navItem = document.getElementById('nav-' + idx);
        const iconEl  = document.getElementById('mn-icon-' + idx);

        if (navItem) navItem.classList.add('done');

        if (iconEl) {
            iconEl.setAttribute('icon', 'ph:check-fill');
        }

        updateProgressUI(newPct);

        saveLocalProgress();

        showToast('Module completed successfully!', 'success');

        /* COURSE FINISHED */
        if (doneSet.size >= TOTAL) {

            localStorage.setItem(
                STORAGEKEY + '_finished',
                'yes'
            );

            setTimeout(() => {
                showCompletion();
            }, 700);

            return;
        }

        /* GO NEXT */
        const nextModule = idx + 1;

        if (nextModule < TOTAL) {
            setTimeout(() => {
                goTo(nextModule);
            }, 700);
        }
    })
    .catch(err => {

        doneSet.delete(idx);

        saveLocalProgress();

        btn.classList.remove('loading');
        btn.disabled = false;

        document.getElementById(
            'complete-txt-' + idx
        ).textContent = 'Mark as Complete';

        showToast(
            'Could not save progress. Try again.',
            'error'
        );

        console.error(err);
    });
}

/* ─────────────────────────────
   UPDATE UI
───────────────────────────── */
function updateProgressUI(pct) {

    const bar   = document.getElementById('sidebar-bar');
    const label = document.getElementById('pct-label');
    const count = document.getElementById('done-count');

    if (bar) {
        bar.style.width = pct + '%';
    }

    if (label) {
        label.textContent = pct + '%';
    }

    if (count) {
        count.textContent = doneSet.size;
    }
}

/* ─────────────────────────────
   APPLY COMPLETED STATE
───────────────────────────── */
function applyCompletedState() {

    doneSet.forEach(i => {

        const btn  = document.getElementById('complete-' + i);
        const txt  = document.getElementById('complete-txt-' + i);
        const nav  = document.getElementById('nav-' + i);
        const icon = document.getElementById('mn-icon-' + i);

        if (btn) {
            btn.classList.add('done');
            btn.disabled = true;
        }

        if (txt) {
            txt.textContent = 'Completed ✓';
        }

        if (nav) {
            nav.classList.add('done');
        }

        if (icon) {
            icon.setAttribute('icon', 'ph:check-fill');
        }
    });

    const pct = Math.round((doneSet.size / TOTAL) * 100);

    updateProgressUI(pct);
}

/* ─────────────────────────────
   COMPLETION SCREEN
───────────────────────────── */
function showCompletion() {

    document.querySelectorAll('[id^="mod-"]').forEach(el => {
        el.style.display = 'none';
    });

    const cs = document.getElementById('completion-screen');

    if (cs) {
        cs.style.display = 'block';
    }

    showToast(
        '🎉 Course completed successfully!',
        'success'
    );
}

/* ─────────────────────────────
   TOAST
───────────────────────────── */
function showToast(msg, type = 'success') {

    document.querySelectorAll('.l-toast').forEach(t => t.remove());

    const t = document.createElement('div');

    t.className = 'l-toast ' + type;

    t.textContent = msg;

    document.body.appendChild(t);

    setTimeout(() => {

        t.style.transition = 'opacity 0.4s';

        t.style.opacity = '0';

        setTimeout(() => t.remove(), 400);

    }, 3500);
}

/* ─────────────────────────────
   ABOUT TOGGLE
───────────────────────────── */
const aboutExpanded = {};

function toggleAbout(index) {

    aboutExpanded[index] = !aboutExpanded[index];

    document.getElementById('aboutText-' + index).style.display =
        aboutExpanded[index] ? 'none' : 'block';

    document.getElementById('aboutFull-' + index).style.display =
        aboutExpanded[index] ? 'block' : 'none';

    document.getElementById('aboutToggleTxt-' + index).textContent =
        aboutExpanded[index] ? 'Show less' : 'Read more';

    document.getElementById('aboutToggleIcon-' + index)
        .setAttribute(
            'icon',
            aboutExpanded[index]
                ? 'ph:caret-up-bold'
                : 'ph:caret-down-bold'
        );
}
/* ─────────────────────────────
   INIT
───────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {

    loadLocalProgress();

    applyCompletedState();

    const isFinished = localStorage.getItem(
        STORAGEKEY + '_finished'
    );

    if (isFinished === 'yes' || doneSet.size >= TOTAL) {

        showCompletion();

        return;
    }

    /* AUTO OPEN FIRST UNFINISHED */
    let firstUnfinished = 0;

    for (let i = 0; i < TOTAL; i++) {
        if (!doneSet.has(i)) {
            firstUnfinished = i;
            break;
        }
    }

    goTo(firstUnfinished);
});
</script>
@endsection