{{-- resources/views/dashboard/strategies/index.blade.php --}}
@extends('layout.user')

@section('content')
<style>
    :root {
        --pg: #9EDD05;
        --dg: #0C3A30;
        --ag: #8AC304;
    }

    .dashboard-main-body {
        width: 100%;
    }

    @media(min-width:1600px) {
        .dashboard-main-body {
            max-width: 1450px;
        }
    }

    /* ───────────────── HERO ───────────────── */
    .strat-hero {
        max-width: 1200px;
        margin: 0 auto 2rem auto;
        background: linear-gradient(135deg, #0C3A30 0%, #0d4a3a 55%, #134d35 100%);
        border-radius: 1.5rem;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow:
            0 20px 50px rgba(12, 58, 48, .18),
            inset 0 1px 0 rgba(255, 255, 255, .04);
    }

    .strat-hero::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -15%;
        width: 55%;
        height: 220%;
        background: linear-gradient(135deg,
                rgba(158, 221, 5, .08),
                rgba(158, 221, 5, .02));
        transform: rotate(18deg);
        pointer-events: none;
    }

    .strat-hero::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: -8%;
        width: 40%;
        height: 200%;
        background: radial-gradient(ellipse,
                rgba(158, 221, 5, .06) 0%,
                transparent 70%);
        pointer-events: none;
    }

    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 999px;
        font-size: .75rem;
        font-weight: 600;
        color: #fff !important;
        backdrop-filter: blur(5px);
    }

    /* ───────────────── SECTION ───────────────── */
    .sec-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .sec-head h2 {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--dg);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sec-head h2 iconify-icon {
        color: var(--pg) !important;
        font-size: 1.2rem;
    }

    /* ───────────────── CARD ───────────────── */
    .course-card {
        background: #fff;
        border: 1.5px solid #edf0f2;
        border-radius: 1.25rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        position: relative;
        transition: all .28s ease;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .03);
        height: 100%;
    }

    .course-card:hover {
        transform: translateY(-5px);
        border-color: rgba(158, 221, 5, .45);
        box-shadow:
            0 18px 40px rgba(158, 221, 5, .12),
            0 8px 18px rgba(0, 0, 0, .04);
    }

    .course-card.is-popular {
        border-color: rgba(158, 221, 5, .4);
    }

    .course-card.is-popular::before {
        content: '';
        position: absolute;
        inset: 0 0 auto 0;
        height: 4px;
        background: linear-gradient(90deg, var(--pg), var(--ag));
        z-index: 3;
    }

    /* ───────────────── IMAGE ───────────────── */
    .card-img {
        width: 100%;
        height: 170px;
        object-fit: cover;
        display: block;
        flex-shrink: 0;
    }

    .card-img-placeholder {
        width: 100%;
        height: 170px;
        background: linear-gradient(135deg, #0C3A30, #174f40);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .card-img-placeholder::after {
        content: '';
        position: absolute;
        top: -30%;
        right: -20%;
        width: 60%;
        height: 160%;
        background: rgba(158, 221, 5, .08);
        transform: rotate(20deg);
    }

    /* ───────────────── BADGES ───────────────── */

    /* POPULAR BADGE (ANIMATED) */
    .popular-pill {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 6;

        display: inline-flex;
        align-items: center;
        gap: 5px;

        background: linear-gradient(135deg, var(--pg), var(--ag));
        color: #fff !important;

        padding: 6px 11px;
        border-radius: 999px;

        font-size: .64rem;
        font-weight: 800;

        box-shadow: 0 4px 14px rgba(158, 221, 5, .35);

        animation: popularPulse 2.6s ease-in-out infinite;
        overflow: hidden;
    }

    /* spinning icon */
    .spin-icon {
        display: inline-block;
        animation: spin 1.2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .popular-pill::after {
        content: '';
        position: absolute;
        top: 0;
        left: -120%;
        width: 60%;
        height: 100%;
        background: linear-gradient(120deg,
                transparent,
                rgba(255, 255, 255, 0.55),
                transparent);
        transform: skewX(-20deg);
        animation: popularShine 3s infinite;
    }

    @keyframes popularPulse {
        0% {
            transform: scale(1);
            box-shadow: 0 4px 14px rgba(158, 221, 5, .25);
        }

        50% {
            transform: scale(1.08);
            box-shadow: 0 10px 25px rgba(158, 221, 5, .45);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 4px 14px rgba(158, 221, 5, .25);
        }
    }

    @keyframes popularShine {
        0% {
            left: -120%;
        }

        100% {
            left: 200%;
        }
    }

    /* ENROLLED */
    .enrolled-ribbon {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 6;

        display: inline-flex;
        align-items: center;
        gap: 6px;

        background: rgba(12, 58, 48, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.08);

        color: #fff !important;

        padding: 6px 11px;
        border-radius: 999px;

        font-size: .64rem;
        font-weight: 700;

        backdrop-filter: blur(10px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .enrolled-ribbon .dot {
        width: 7px;
        height: 7px;
        background: #fff !important;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.12);
        animation: blink 1.5s ease infinite;
    }

    /* ───────────────── BODY ───────────────── */
    .card-body {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .card-body h3 {
        font-size: .96rem;
        line-height: 1.4;
        color: var(--dg);
    }

    .card-desc {
        font-size: .8rem;
        line-height: 1.5;
        color: #6b7280;
    }

    /* Difficulty */
    .diff-badge {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: .62rem;
        font-weight: 700;
    }

    .diff-beginner {
        background: #d1fae5;
        color: #065f46;
    }

    .diff-intermediate {
        background: #fef3c7;
        color: #92400e;
    }

    .diff-advanced {
        background: #fee2e2;
        color: #991b1b;
    }

    .diff-expert {
        background: #f3e8ff;
        color: #6b21a8;
    }

    /* Stats */
    .card-stat {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: .68rem;
        color: #6b7280;
        font-weight: 500;
    }

    /* Features */
    .course-features {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .feature-pill {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 8px 10px;
        background: rgba(158, 221, 5, .06);
        border: 1px solid rgba(158, 221, 5, .15);
        border-radius: 11px;
    }

    .feature-pill span {
        font-size: .72rem;
        line-height: 1.4;
        color: #374151;
        font-weight: 500;
    }

    /* ───────────────── PROGRESS ───────────────── */
    .course-progress-box {
        margin-bottom: 1rem;
        padding: 14px;
        background: linear-gradient(135deg,
                rgba(158, 221, 5, .09),
                rgba(138, 195, 4, .04));
        border: 1px solid rgba(158, 221, 5, .18);
        border-radius: 16px;
    }

    .course-progress-box.completed {
        background: linear-gradient(135deg,
                rgba(158, 221, 5, .18),
                rgba(138, 195, 4, .08));
        border-color: rgba(158, 221, 5, .35);
    }

    .course-progress-top {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .course-progress-label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: .73rem;
        font-weight: 700;
        color: var(--dg);
    }

    .course-progress-percent {
        font-size: .82rem;
        font-weight: 800;
        color: var(--dg);
    }

    .course-progress-bar {
        width: 100%;
        height: 8px;
        background: rgba(12, 58, 48, .08);
        border-radius: 999px;
        overflow: hidden;
    }

    .course-progress-fill {
        height: 100%;
        border-radius: 999px;
        width: 0;
        background: linear-gradient(90deg, #9EDD05, #8AC304);
        transition: width .7s ease;
    }

    .course-progress-fill::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .45), transparent);
        animation: progressShine 2s linear infinite;
    }

    .course-progress-meta {
        margin-top: 10px;
        display: flex;
        justify-content: space-between;
        font-size: .67rem;
        font-weight: 600;
        color: #5f6b73;
    }

    /* ───────────────── BUTTONS ───────────────── */
    .card-cta,
    .card-cta-enrolled {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 15px;
        border-radius: 10px;
        font-weight: 800;
        font-size: .8rem;
        text-decoration: none;
    }

    .card-cta {
        background: linear-gradient(135deg, var(--pg), var(--ag));
        color: var(--dg);
    }

    .card-cta-enrolled {
        background: #fff;
        border: 1.5px solid rgba(158, 221, 5, .45);
        color: var(--dg);
    }

    /* ───────────────── MODAL ───────────────── */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .55);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: .25s ease;
    }

    .modal-overlay.open {
        opacity: 1;
        visibility: visible;
    }

    /* ───────────────── TOAST ───────────────── */
    .s-toast {
        position: fixed;
        right: 20px;
        bottom: 20px;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: .85rem;
        font-weight: 700;
    }

    .s-toast.success {
        background: #dcfce7;
        color: #166534;
    }

    .s-toast.error {
        background: #fee2e2;
        color: #991b1b;
    }

    /* ───────────────── ANIMATIONS ───────────────── */
    @keyframes blink {

        0%,
        100% {
            opacity: 1
        }

        50% {
            opacity: .35
        }
    }

    @keyframes progressShine {
        0% {
            transform: translateX(-120%)
        }

        100% {
            transform: translateX(120%)
        }
    }

    /* ───────────────── MOBILE ───────────────── */
    @media(max-width:640px) {
        .strat-hero {
            padding: 1.5rem;
            border-radius: 1.2rem;
        }

        .card-img,
        .card-img-placeholder {
            height: 145px;
        }

        .course-progress-meta {
            flex-direction: column;
            gap: 4px;
        }

        .card-body {
            padding: .9rem;
        }
    }
</style>
<div class="dashboard-main-body max-w-7xl mx-auto px-3 md:px-5">

    {{-- Breadcrumb --}}
    <div class="flex flex-wrap items-center justify-between gap-2 mb-5">
        <h5 class="font-semibold mb-0" style="color:var(--dg);">
            Trading Courses
        </h5>

        <ul class="flex items-center gap-[6px] text-sm">
            <li>
                <a href="{{ route('user_dashboard') }}"
                    class="flex items-center gap-1 hover:text-[#9EDD05]"
                    style="color:var(--dg);">
                    <iconify-icon
                        icon="solar:home-smile-angle-outline"
                        class="text-lg">
                    </iconify-icon>
                    Dashboard
                </a>
            </li>

            <li>-</li>

            <li class="font-medium" style="color:var(--pg);">
                Academy
            </li>
        </ul>
    </div>

    @if(session('success'))
    <div class="s-toast success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="s-toast error">
        {{ session('error') }}
    </div>
    @endif

    {{-- HERO --}}
    <div class="strat-hero">

        <div class="relative z-10 flex flex-col lg:flex-row gap-6 justify-between">

            <div class="flex-1">

                <h5 class="font-semibold lg:text-4xl font-bold mb-2 leading-tight text-white" style="color:white !important;">
                    Master Forex, Crypto<br class="hidden lg:block">
                    & Futures Trading
                </h5>

                <p class="text-base max-w-xl mb-5 text-white/80" style="color:white !important;">
                    Learn from professional traders using proven systems CRT,
                    MMXM, ICT, Quarterly Theory and more.
                </p>

                <div class="flex flex-wrap gap-2">

                    <div class="hero-chip">
                        <iconify-icon
                            icon="ph:graduation-cap-fill"
                            style="color:#9EDD05 !important;">
                        </iconify-icon>

                        {{ $strategies->count() }} Courses
                    </div>

                    <div class="hero-chip">
                        <iconify-icon
                            icon="ph:users-fill"
                            style="color:#9EDD05!important;">
                        </iconify-icon>

                        Expert Instructors
                    </div>

                    <div class="hero-chip">
                        <iconify-icon
                            icon="ph:chart-line-up-fill"
                            style="color:#9EDD05 !important;">
                        </iconify-icon>

                        Live Market Analysis
                    </div>

                </div>
            </div>

            <div class="hidden lg:flex flex-col items-center justify-center gap-3 flex-shrink-0">

                <div class="w-24 h-24 rounded-2xl flex items-center justify-center"
                    style="background:rgba(158,221,5,0.1);border:2px solid rgba(158,221,5,0.2);">

                    <iconify-icon
                        icon="ph:graduation-cap-fill"
                        style="font-size:3rem;color:#9EDD05;">
                    </iconify-icon>

                </div>

                <div class="text-center">
                    <p class="text-2xl font-black"
                        style="color:#9EDD05 !important;">
                        {{ $strategies->count() }}+
                    </p>

                    <p class="text-xs"
                        style="color:rgba(255,255,255,0.55) !important;">
                        Pro Courses
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- COURSES --}}
    <div>

        <div class="sec-head">
            <h2>
                <iconify-icon icon="ph:books-fill"></iconify-icon>
                Available Trading Courses
            </h2>

            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                {{ $strategies->count() }} courses
            </span>
        </div>

        @if($strategies->isEmpty())

        <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">

            <iconify-icon
                icon="ph:books-fill"
                class="text-5xl text-gray-300 mb-3">
            </iconify-icon>

            <p class="text-gray-500 font-semibold text-lg">
                No courses available yet.
            </p>

            <p class="text-gray-400 text-sm mt-1">
                New courses are added regularly — check back soon.
            </p>

        </div>

        @else

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-w-6xl mx-auto">

            @foreach($strategies as $strat)

            @php
            $isEnrolled = $strat->isUserEnrolled(auth()->id());

            $enrollment = $isEnrolled
            ? $strat->getUserEnrollment(auth()->id())
            : null;

            $progress = intval($enrollment->progress ?? 0);

            // LOCAL STORAGE SUPPORT
            $mods = [];

            if($strat->modules ?? null){
            $mods = is_array($strat->modules)
            ? $strat->modules
            : (json_decode($strat->modules,true) ?? []);
            }

            $totalModules = count($mods);
            @endphp

            <div class="course-card {{ $strat->is_popular ? 'is-popular' : '' }}">

                {{-- Popular --}}
                @if($strat->is_popular)
                <div class="popular-pill">
                    <iconify-icon class="spin-icon" style="color:white !important;" icon="ph:star-fill"></iconify-icon>
                    Popular
                </div>
                @endif

                {{-- Enrolled --}}
                @if($isEnrolled)
                <div class="enrolled-ribbon">
                    <span class="dot"></span>
                    Enrolled
                </div>
                @endif

                {{-- Image --}}
                @if($strat->cover_image)

                <img
                    src="{{ Storage::url($strat->cover_image) }}"
                    alt="{{ $strat->name }}"
                    class="card-img">

                @else

                <div class="card-img-placeholder">

                    <iconify-icon
                        icon="ph:chart-line-up-fill"
                        style="font-size:2.8rem;color:rgba(158,221,5,0.45);position:relative;z-index:1;">
                    </iconify-icon>

                </div>

                @endif

                <div class="card-body">

                    {{-- Difficulty --}}
                    <div class="flex flex-wrap gap-1.5 mb-2">

                        @if($strat->difficulty_level)
                        <span class="diff-badge diff-{{ $strat->difficulty_level }}">
                            {{ ucfirst($strat->difficulty_level) }}
                        </span>
                        @endif

                        @php
                        $mtags = [];

                        if($strat->market_tags ?? null){
                        $mtags = is_array($strat->market_tags)
                        ? $strat->market_tags
                        : (json_decode($strat->market_tags,true) ?? []);
                        }
                        @endphp

                        @foreach(array_slice($mtags,0,1) as $tag)

                        <span
                            style="display:inline-flex;align-items:center;padding:2px 7px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:20px;font-size:0.6rem;color:#4b5563;font-weight:500;">
                            {{ $tag }}
                        </span>

                        @endforeach
                    </div>

                    {{-- Title --}}
                    <h3 class="font-bold mb-1.5 leading-tight">
                        {{ $strat->name }}
                    </h3>

                    {{-- Description --}}
                    <div class="mb-3 flex-1">

                        <p class="card-desc mb-3">
                            {{ Str::limit($strat->description,80) }}
                        </p>
                        {{-- Features --}}
                        @php
                        $features = [];

                        if($strat->features ?? null){
                        $features = is_array($strat->features)
                        ? $strat->features
                        : (json_decode($strat->features,true) ?? []);
                        }
                        @endphp

                        @if(count($features))

                        <div class="course-features">

                            @foreach($features as $feature)

                            <div class="feature-pill">

                                <iconify-icon
                                    icon="ph:check-circle-fill"
                                    style="color:#8AC304 !important;">
                                </iconify-icon>

                                <span>{{ $feature }}</span>

                            </div>

                            @endforeach

                        </div>

                        @endif

                    </div>

                    {{-- Stats --}}
                    <div class="flex flex-wrap gap-3 mb-3">

                        @if($strat->estimated_hours)
                        <div class="card-stat">
                            <iconify-icon
                                icon="ph:clock-fill"
                                style="color:#8AC304 !important;">
                            </iconify-icon>

                            {{ $strat->estimated_hours }}h
                        </div>
                        @endif

                        @if($strat->duration_days)

                        <div class="card-stat">
                            <iconify-icon
                                icon="ph:calendar-fill"
                                style="color:#8AC304 !important;">
                            </iconify-icon>

                            {{ $strat->duration_days }}d
                        </div>

                        @else

                        <div class="card-stat">
                            <iconify-icon
                                icon="ph:infinity-fill"
                                style="color:#8AC304 !important;">
                            </iconify-icon>

                            Lifetime
                        </div>

                        @endif

                    </div>

                    {{-- PROGRESS --}}
                    @if($isEnrolled && $enrollment)

                    <div class="course-progress-box {{ $progress >= 100 ? 'completed' : '' }}"
                        data-strategy="{{ $strat->id }}"
                        data-total="{{ $totalModules }}"
                        data-db-progress="{{ $progress }}">

                        <div class="course-progress-top">

                            <div class="course-progress-label">

                                <iconify-icon
                                    icon="ph:chart-line-up-fill">
                                </iconify-icon>

                                Learning Progress

                            </div>

                            <div class="course-progress-percent progress-label-{{ $strat->id }}">
                                {{ $progress }}%
                            </div>

                        </div>

                        <div class="course-progress-bar">

                            <div class="course-progress-fill progress-fill-{{ $strat->id }}"
                                style="width:{{ $progress }}%;">
                            </div>

                        </div>

                        <div class="course-progress-meta">

                            <span class="progress-status-{{ $strat->id }}">
                                {{ $progress >= 100 ? 'Course Completed' : 'Continue Learning' }}
                            </span>

                            <span class="progress-state-{{ $strat->id }}">
                                @if($progress >= 100)
                                Completed ✓
                                @elseif($progress > 0)
                                In Progress
                                @else
                                Not Started
                                @endif
                            </span>

                        </div>

                    </div>

                    @endif

                    {{-- Footer --}}
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 mt-auto">

                        <div>
                            <p class="text-xs text-gray-400">
                                Price
                            </p>

                            <p class="text-lg font-black"
                                style="color:var(--pg);">
                                ${{ number_format($strat->price,2) }}
                            </p>
                        </div>

                        @if($isEnrolled)

                        <a href="{{ route('strategies.strategylearn', $strat->id) }}"
                            class="card-cta-enrolled">

                            <iconify-icon icon="ph:play-fill"></iconify-icon>

                            Continue

                        </a>

                        @else

                        <a href="{{ route('strategies.strategylearn', $strat->id) }}"
                            class="card-cta">

                            View →

                        </a>

                        @endif

                    </div>

                </div>
            </div>

            @endforeach

        </div>

        @endif
    </div>
</div>

{{-- MODAL --}}
<div class="modal-overlay" id="reenrollModal">

    <div class="modal-box">

        <div class="modal-title">
            ⚠️ Already Enrolled
        </div>

        <p class="modal-body" id="reenrollMsg">
            You are already enrolled in this course.
        </p>

        <div class="modal-actions">

            <button
                class="modal-cancel"
                onclick="closeReenrollModal()">
                Cancel
            </button>

            <button
                class="modal-confirm"
                id="reenrollConfirmBtn">
                Yes, Enroll Again
            </button>

        </div>
    </div>
</div>

<script>
    /* ─────────────────────────────
       TOAST AUTO DISMISS
    ───────────────────────────── */
    document.querySelectorAll('.s-toast').forEach(t => {

        setTimeout(() => {

            t.style.transition = 'opacity .4s';

            t.style.opacity = '0';

            setTimeout(() => t.remove(), 400);

        }, 3500);

    });

    /* ─────────────────────────────
       RE-ENROLL MODAL
    ───────────────────────────── */
    let _reenrollTarget = null;

    function openReenrollModal(courseId, courseName) {

        _reenrollTarget = courseId;

        document.getElementById('reenrollMsg').textContent =
            'You are already enrolled in "' +
            courseName +
            '". Are you sure you want to enroll again?';

        document.getElementById('reenrollModal')
            .classList.add('open');
    }

    function closeReenrollModal() {

        document.getElementById('reenrollModal')
            .classList.remove('open');

        _reenrollTarget = null;
    }

    document.getElementById('reenrollConfirmBtn')
        .addEventListener('click', function() {

            if (!_reenrollTarget) return;

            const f = document.createElement('form');

            f.method = 'POST';

            f.action =
                '/strategies/' +
                _reenrollTarget +
                '/enroll';

            f.innerHTML = `
                <input
                    type="hidden"
                    name="_token"
                    value="{{ csrf_token() }}">

                <input
                    type="hidden"
                    name="force"
                    value="1">
            `;

            document.body.appendChild(f);

            f.submit();
        });

    document.getElementById('reenrollModal')
        .addEventListener('click', function(e) {

            if (e.target === this) {
                closeReenrollModal();
            }
        });

    /* ─────────────────────────────
       FIX PROGRESS BAR
       USING SAME LEARN PAGE LOGIC
    ───────────────────────────── */
    document.addEventListener('DOMContentLoaded', () => {

        document.querySelectorAll('.course-progress-box')
            .forEach(box => {

                const strategyId = box.dataset.strategy;
                const total = parseInt(box.dataset.total || 0);
                const dbProgress = parseInt(box.dataset.dbProgress || 0);

                let finalProgress = dbProgress;

                // LOCAL STORAGE KEY
                const STORAGEKEY =
                    'strategy_progress_' + strategyId;

                const saved =
                    localStorage.getItem(STORAGEKEY);

                // USE LOCAL STORAGE FIRST
                if (saved) {

                    try {

                        const parsed = JSON.parse(saved);

                        if (parsed.completed) {

                            const doneCount =
                                parsed.completed.length;

                            if (total > 0) {

                                finalProgress = Math.round(
                                    (doneCount / total) * 100
                                );
                            }
                        }

                    } catch (e) {
                        console.error(e);
                    }
                }

                // UPDATE BAR
                const fill =
                    document.querySelector(
                        '.progress-fill-' + strategyId
                    );

                const label =
                    document.querySelector(
                        '.progress-label-' + strategyId
                    );

                const status =
                    document.querySelector(
                        '.progress-status-' + strategyId
                    );

                const state =
                    document.querySelector(
                        '.progress-state-' + strategyId
                    );

                if (fill) {

                    setTimeout(() => {
                        fill.style.width =
                            finalProgress + '%';
                    }, 200);
                }

                if (label) {
                    label.textContent =
                        finalProgress + '%';
                }

                // STATUS
                if (finalProgress >= 100) {

                    box.classList.add('completed');

                    if (status) {
                        status.textContent =
                            'Course Completed';
                    }

                    if (state) {
                        state.textContent =
                            'Completed ✓';
                    }

                } else if (finalProgress > 0) {

                    if (status) {
                        status.textContent =
                            'Continue Learning';
                    }

                    if (state) {
                        state.textContent =
                            'In Progress';
                    }

                } else {

                    if (status) {
                        status.textContent =
                            'Ready To Start';
                    }

                    if (state) {
                        state.textContent =
                            'Not Started';
                    }
                }
            });
    });
</script>

@endsection