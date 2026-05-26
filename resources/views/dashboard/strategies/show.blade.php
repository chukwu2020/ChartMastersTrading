{{-- resources/views/dashboard/strategies/show.blade.php --}}
{{--
    CONTROLLER NOTE: Pass $adminProfile to this view.
    In your StrategiesController@show:
        $adminProfile = \App\Models\User::where('role','admin')->first();
        // or however you retrieve the site admin
        return view('dashboard.strategies.show', compact('strategy','enrollment','upgrades','adminProfile'));
--}}
@extends('layout.user')

@section('content')
<style>
:root { --pg:#9EDD05; --dg:#0C3A30; --ag:#8AC304; }

/* ── Hero ── */
.course-hero {
    background:linear-gradient(135deg,#0C3A30 0%,#0d4535 55%,#134a35 100%);
    border-radius:1.5rem; padding:2.5rem;
    position:relative; overflow:hidden; margin-bottom:2rem;
}
.course-hero::before {
    content:''; position:absolute; top:-50%; right:-10%;
    width:50%; height:200%;
    background:linear-gradient(135deg,rgba(158,221,5,0.07),transparent);
    transform:rotate(18deg); pointer-events:none;
}
.course-hero::after {
    content:''; position:absolute; bottom:-40%; left:-5%;
    width:35%; height:180%;
    background:radial-gradient(ellipse,rgba(158,221,5,0.04) 0%,transparent 70%);
    pointer-events:none;
}

/* ── ALL hero text white ── */
.course-hero,
.course-hero h1,
.course-hero h2,
.course-hero h3,
.course-hero p,
.course-hero span,
.course-hero div,
.course-hero a,
.course-hero li,
.course-hero label {
    color:#ffffff !important;
}
/* except green accents */
.course-hero .accent-green { color:#9EDD05 !important; }
.course-hero .stat-lbl { color:rgba(255,255,255,0.5) !important; font-size:0.65rem; text-transform:uppercase; letter-spacing:0.05em; }
.course-hero .stat-val { color:#ffffff !important; font-weight:700; font-size:0.9rem; }

/* hero image */
.hero-img { width:100%; height:190px; object-fit:cover; border-radius:1rem; border:2px solid rgba(158,221,5,0.3); }
.hero-img-ph {
    width:100%; height:190px;
    background:linear-gradient(135deg,rgba(158,221,5,0.1),rgba(158,221,5,0.04));
    border:2px solid rgba(158,221,5,0.2); border-radius:1rem;
    display:flex; align-items:center; justify-content:center;
}

/* tag badges (inside hero) */
.htag {
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 12px; border-radius:30px;
    font-size:0.7rem; font-weight:600;
}
.ht-beg  { background:rgba(16,185,129,0.2);  color:#6ee7b7 !important; border:1px solid rgba(16,185,129,0.3); }
.ht-int  { background:rgba(59,130,246,0.2);  color:#93c5fd !important; border:1px solid rgba(59,130,246,0.3); }
.ht-adv  { background:rgba(168,85,247,0.2);  color:#d8b4fe !important; border:1px solid rgba(168,85,247,0.3); }
.ht-exp  { background:rgba(239,68,68,0.2);   color:#fca5a5 !important; border:1px solid rgba(239,68,68,0.3); }
.ht-def  { background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.9) !important; border:1px solid rgba(255,255,255,0.15); }
.ht-life { background:rgba(158,221,5,0.15);  color:#9EDD05 !important; border:1px solid rgba(158,221,5,0.3); }

/* instructor row (hero) */
.instr-row {
    display:flex; align-items:center; gap:12px;
    padding:10px 14px;
    background:rgba(255,255,255,0.07);
    border:1px solid rgba(255,255,255,0.12);
    border-radius:12px; margin-top:1rem;
}
.instr-av { width:46px; height:46px; border-radius:50%; object-fit:cover; border:2px solid var(--pg); flex-shrink:0; }
.instr-init {
    width:46px; height:46px; border-radius:50%;
    background:linear-gradient(135deg,var(--pg),var(--ag));
    display:flex; align-items:center; justify-content:center;
    font-weight:700; font-size:1.1rem; color:var(--dg); flex-shrink:0;
}


/* badge */

.popular-pill{
    position:absolute;
    top:7px;
    right:5px;
    z-index:5;
    display:inline-flex;
    align-items:center;
    gap:4px;
    background:linear-gradient(135deg,var(--pg),var(--ag));
    color:var(--dg);
    padding:2px 9px;
    border-radius:30px;
    font-size:0.62rem;
    font-weight:700;
    box-shadow:0 2px 8px rgba(158,221,5,0.35);

    animation:popularPulse 2s ease-in-out infinite;
}

/* icon spin glow */
.popular-pill iconify-icon{
    animation:starSpin 3s linear infinite;
}

@keyframes popularPulse{
    0%{
        transform:scale(1);
        box-shadow:0 2px 8px rgba(158,221,5,0.35);
    }
    50%{
        transform:scale(1.06);
        box-shadow:0 4px 18px rgba(158,221,5,0.55);
    }
    100%{
        transform:scale(1);
        box-shadow:0 2px 8px rgba(158,221,5,0.35);
    }
}

@keyframes starSpin{
    0%{
        transform:rotate(0deg);
    }
    100%{
        transform:rotate(360deg);
    }
}

/* ── Content Cards ── */
.cc {
    background:white; border:1.5px solid #e5e7eb;
    border-radius:1.25rem; padding:1.75rem;
    margin-bottom:1.25rem; transition:border-color 0.2s;
}
.cc:hover { border-color:rgba(158,221,5,0.3); }
.cc h2 {
    font-size:1.05rem; font-weight:700; color:var(--dg);
    display:flex; align-items:center; gap:8px; margin-bottom:1rem;
}
.cc h2 iconify-icon { color:var(--pg); font-size:1.2rem; }

/* objectives */
.obj-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
@media(max-width:640px){ .obj-grid{grid-template-columns:1fr;} }
.obj-item {
    display:flex; align-items:flex-start; gap:8px;
    padding:9px; background:#f8fff5; border-radius:9px;
    font-size:0.85rem; color:#374151;
}
.obj-item iconify-icon { color:var(--pg); font-size:1rem; flex-shrink:0; margin-top:1px; }

/* module preview */
.mod-preview {
    display:flex; align-items:center; gap:12px;
    padding:10px 14px;
    border:1.5px solid #e5e7eb;
    border-radius:10px; margin-bottom:8px;
    transition:all 0.2s;
}
.mod-preview:hover { border-color:rgba(158,221,5,0.3); background:#f9fafb; }
.mod-num {
    width:28px; height:28px;
    background:linear-gradient(135deg,var(--pg),var(--ag));
    color:var(--dg); border-radius:8px;
    font-size:0.72rem; font-weight:800;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
.mod-locked-icon { color:#9ca3af; font-size:0.85rem; }

/* prereq */
.prereq-item {
    display:flex; align-items:center; gap:8px;
    padding:6px 0; font-size:0.875rem; color:#374151;
}
.prereq-item::before {
    content:''; width:6px; height:6px;
    background:var(--pg); border-radius:50%; flex-shrink:0;
}

/* instructor bio card (white area, NOT hero) */
.instr-bio {
    display:flex; gap:1rem; padding:1.25rem;
    background:linear-gradient(135deg,#f8fff5,#f0fdf4);
    border:1.5px solid rgba(158,221,5,0.2); border-radius:1rem;
}
.ib-av { width:64px; height:64px; border-radius:50%; object-fit:cover; border:2px solid var(--pg); flex-shrink:0; }
.ib-init {
    width:64px; height:64px; border-radius:50%;
    background:linear-gradient(135deg,var(--pg),var(--ag));
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:1.4rem; color:var(--dg); flex-shrink:0;
}

/* ── Sidebar Pricing ── */
.pricing-card {
    background:white; border:2px solid #e5e7eb;
    border-radius:1.25rem; padding:1.5rem;
    position:sticky; top:1.5rem;
}
.price-big { font-size:2.5rem; font-weight:900; color:var(--pg); line-height:1; }

/* enrolled state */
.enrolled-state {
    background:linear-gradient(135deg,#f0fdf4,#dcfce7);
    border:2px solid rgba(22,163,74,0.3);
    border-radius:1rem; padding:1.25rem; margin-bottom:1rem;
}
.e-pill {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 10px;
    background:linear-gradient(135deg,rgba(158,221,5,0.15),rgba(138,195,4,0.1));
    border:1px solid rgba(158,221,5,0.4);
    border-radius:30px; font-size:0.65rem; font-weight:700;
    color:var(--dg); text-transform:uppercase; letter-spacing:0.04em;
}
.e-pill .dot { width:5px;height:5px;background:var(--pg);border-radius:50%;animation:blink 1.5s ease infinite; }
@keyframes blink{0%,100%{opacity:1}50%{opacity:0.3}}
.e-pbar { width:100%;height:8px;background:#d1fae5;border-radius:4px;overflow:hidden; }
.e-pfill { height:100%;background:linear-gradient(90deg,#16a34a,#22c55e);border-radius:4px; }

/* enroll button */
.btn-main {
    display:block; width:100%;
    padding:0.875rem 1rem;
    background:linear-gradient(135deg,var(--pg),var(--ag));
    color:var(--dg); font-weight:700; font-size:1rem;
    text-align:center; border-radius:0.875rem;
    border:none; cursor:pointer; transition:all 0.3s ease;
    text-decoration:none;
}
.btn-main:hover:not(:disabled) { transform:translateY(-2px); box-shadow:0 6px 16px rgba(158,221,5,0.3); color:var(--dg); text-decoration:none; }
.btn-main:disabled { opacity:0.5; cursor:not-allowed; transform:none; }

/* upgrade tiers */
.up-tier {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 12px;
    background:linear-gradient(135deg,#fef9c3,#fef3c7);
    border:1.5px solid #fde047; border-radius:10px;
    margin-bottom:8px;
}
.up-btn {
    padding:6px 14px;
    background:linear-gradient(135deg,#f59e0b,#d97706);
    color:white; font-weight:700; font-size:0.75rem;
    border-radius:8px; border:none; cursor:pointer;
    transition:all 0.2s;
}
.up-btn:hover { transform:translateY(-1px); box-shadow:0 3px 8px rgba(245,158,11,0.3); }

/* feature list */
.feat-list { list-style:none; padding:0; margin:0; }
.feat-list li {
    display:flex; align-items:center; gap:10px;
    padding:8px 0; border-bottom:1px solid #f3f4f6;
    font-size:0.875rem; color:#374151;
}
.feat-list li:last-child { border-bottom:none; }
.feat-list li iconify-icon { color:var(--pg); font-size:1.1rem; flex-shrink:0; }

/* ── Re-enroll confirm modal ── */
.modal-overlay {
    position:fixed; inset:0; background:rgba(0,0,0,0.55);
    display:flex; align-items:center; justify-content:center;
    z-index:10000; opacity:0; pointer-events:none; transition:opacity 0.25s;
}
.modal-overlay.open { opacity:1; pointer-events:all; }
.modal-box {
    background:white; border-radius:1.25rem; padding:2rem;
    max-width:420px; width:90%;
    box-shadow:0 24px 48px rgba(0,0,0,0.2);
    transform:translateY(20px); transition:transform 0.25s;
}
.modal-overlay.open .modal-box { transform:translateY(0); }
.modal-title { font-size:1.1rem; font-weight:800; color:var(--dg); margin-bottom:0.5rem; }
.modal-body  { font-size:0.875rem; color:#4b5563; margin-bottom:1.5rem; line-height:1.6; }
.modal-actions { display:flex; gap:10px; }
.modal-cancel {
    flex:1; padding:10px; border-radius:10px;
    background:#f3f4f6; color:#374151; font-weight:600; font-size:0.875rem;
    border:none; cursor:pointer;
}
.modal-confirm {
    flex:1; padding:10px; border-radius:10px;
    background:linear-gradient(135deg,var(--pg),var(--ag));
    color:var(--dg); font-weight:700; font-size:0.875rem;
    border:none; cursor:pointer;
}

/* spin + toast */
.spin { display:inline-block;width:15px;height:15px;border:2px solid rgba(12,58,48,0.3);border-top-color:var(--dg);border-radius:50%;animation:sp 0.8s linear infinite;margin-right:5px;vertical-align:middle; }
@keyframes sp{to{transform:rotate(360deg)}}
.s-toast { position:fixed;bottom:24px;right:24px;padding:0.875rem 1.5rem;border-radius:12px;font-weight:600;font-size:0.875rem;z-index:9999;box-shadow:0 8px 24px rgba(0,0,0,0.15); }
.s-toast.success{background:#dcfce7;color:#166534;border:1px solid #86efac;}
.s-toast.error  {background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;}

@media(max-width:768px){ .pricing-card{position:static;} }
</style>

<div class="dashboard-main-body">

    {{-- Breadcrumb --}}
    <div class="flex  items-center justify-between gap-2 mb-5" style="flex-wrap:wrap;">
        <h5 class="font-semibold mb-0" style="color:var(--dg);"> Details</h5>
        <ul class="flex items-center gap-[6px] text-sm">
            <li><a href="{{ route('user_dashboard') }}" class="flex items-center gap-1 hover:text-[#9EDD05]" style="color:var(--dg);">
                <iconify-icon icon="solar:home-smile-angle-outline" class="text-lg"></iconify-icon> Dashboard
            </a></li>
            <li>-</li>
            
            <li class="font-medium" style="color:var(--pg);">{{ Str::limit($strategy->name,30) }}</li>
        </ul>
    </div>

    @if(session('success'))<div class="s-toast success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="s-toast error">{{ session('error') }}</div>@endif

    @php
        $lo = $strategy->learning_objectives
            ? (is_array($strategy->learning_objectives) ? $strategy->learning_objectives : (json_decode($strategy->learning_objectives,true) ?? []))
            : [];
        $pre = $strategy->prerequisites
            ? (is_array($strategy->prerequisites) ? $strategy->prerequisites : (json_decode($strategy->prerequisites,true) ?? []))
            : [];
        $feats = $strategy->features
            ? (is_array($strategy->features) ? $strategy->features : (json_decode($strategy->features,true) ?? []))
            : [];
        $mods = $strategy->modules
            ? (is_array($strategy->modules) ? $strategy->modules : (json_decode($strategy->modules,true) ?? []))
            : [];
        $mtags = ($strategy->market_tags ?? null)
            ? (is_array($strategy->market_tags) ? $strategy->market_tags : (json_decode($strategy->market_tags,true) ?? []))
            : [];
        $dc = match($strategy->difficulty_level ?? '') {
            'beginner'=>'ht-beg','intermediate'=>'ht-int','advanced'=>'ht-adv','expert'=>'ht-exp',default=>'ht-def'
        };
        $isEnrolled = $strategy->isUserEnrolled(auth()->id());

       // Get instructor from server feeds
// Instructor from ServerFeed
$instrName = $adminProfile->admin_name ?? 'Trading Instructor';

$instrAvatar = !empty($adminProfile->admin_profile_image)
    ? 'admins/' . $adminProfile->admin_profile_image
    : null;

$instrInitial = strtoupper(substr($instrName, 0, 1));
    @endphp

    {{-- ── HERO ── --}}
    <div class="course-hero">
        <div class="relative z-10 flex flex-col lg:flex-row gap-8">

            {{-- Image --}}
            <div class="flex-shrink-0 lg:w-60">
                @if($strategy->cover_image)
                    <img src="{{ Storage::url($strategy->cover_image) }}" alt="{{ $strategy->name }}" class="hero-img">
                @else
                    <div class="hero-img-ph">
                        <iconify-icon icon="ph:chart-line-up-fill" style="font-size:4rem;color:rgba(158,221,5,0.45);"></iconify-icon>
                    </div>
                @endif
                @if($strategy->is_popular)
             <div class="popular-pill" >
                    <iconify-icon icon="ph:star-fill" style="color:white !important;"></iconify-icon> Popular
                </div>
                @endif
                
            </div>

            {{-- Info --}}
            <div class="flex-1">
                {{-- Tags --}}
                <div class="flex flex-wrap items-center gap-2 mb-4">
                    @if($strategy->difficulty_level)
                    <span class="htag {{ $dc }}">
                        <iconify-icon style="color:#8AC304 !important;"  icon="ph:chart-line-fill"></iconify-icon>
                        {{ ucfirst($strategy->difficulty_level) }}
                    </span>
                    @endif
                    @if($strategy->estimated_hours)
                    <span class="htag ht-def"><iconify-icon style="color:#8AC304 !important;" icon="ph:clock-fill"></iconify-icon> {{ $strategy->estimated_hours }}h</span>
                    @endif
                    @if($strategy->duration_days)
                    <span class="htag ht-def"><iconify-icon style="color:#8AC304 !important;" icon="ph:calendar-fill"></iconify-icon> {{ $strategy->duration_days }}-day access</span>
                    @else
                    <span class="htag ht-life"><iconify-icon  style="color:#8AC304 !important;" icon="ph:infinity-fill"></iconify-icon> Lifetime Access</span>
                    @endif
                    @foreach(array_slice($mtags,0,3) as $t)
                    <span class="htag ht-def">{{ $t }}</span>
                    @endforeach
                </div>

                {{-- Title --}}
                <h1 class="text-3xl lg:text-4xl font-bold mb-3 leading-tight">{{ $strategy->name }}</h1>

                {{-- Short description only (long_description moves to learn page) --}}
                <p class="text-base leading-relaxed mb-4">{{ $strategy->description }}</p>

                {{-- Instructor row — ADMIN profile --}}
                <div class="instr-row">
                    @if($instrAvatar)
                       <img src="{{ asset('storage/' . $instrAvatar) }}" alt="{{ $instrName }}" class="instr-av">
                    @else
                        <div class="instr-init">{{ $instrInitial }}</div>
                    @endif
                    <div>
                        <p style="color:rgba(158,221,5,0.8)!important;font-size:0.63rem;text-transform:uppercase;letter-spacing:0.06em;font-weight:700;">Instructor</p>
                        <p style="color:#ffffff!important;font-weight:700;font-size:1rem;">{{ $instrName }}</p>
                        <p style="color:rgba(255,255,255,0.5)!important;font-size:0.75rem;">Professional Trader & Mentor</p>
                    </div>
                </div>

                {{-- Quick stats --}}
                <div class="flex flex-wrap gap-6 mt-5 pt-4 border-t border-white/10">
                    <div class="flex items-center gap-2">
                        <iconify-icon style="color:#8AC304 !important;" icon="ph:book-open-fill" style="color:var(--pg);font-size:1rem;"></iconify-icon>
                        <div><div class="stat-lbl">Modules</div><div class="stat-val">{{ count($mods) }}</div></div>
                    </div>
                  
                    <div class="flex items-center gap-2">
                        <iconify-icon  style="color:#8AC304 !important;" icon="ph:certificate-fill" style="color:var(--pg);font-size:1rem;"></iconify-icon>
                        <div><div class="stat-lbl">Certificate</div><div class="stat-val">Included</div></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <iconify-icon style="color:#8AC304 !important;" icon="ph:video-camera-fill" style="color:var(--pg);font-size:1rem;"></iconify-icon>
                        <div><div class="stat-lbl">Live Trading sessions</div><div class="stat-val">Included</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── TWO COL ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Main content --}}
        <div class="lg:col-span-2">

            {{-- What You'll Learn --}}
            @if(!empty($lo))
            <div class="cc">
                <h2><iconify-icon style="color:#8AC304 !important;" icon="ph:check-square-fill"></iconify-icon> What You'll Learn</h2>
                <div class="obj-grid">
                    @foreach($lo as $obj)
                    <div class="obj-item"><iconify-icon style="color:#8AC304 !important;" icon="ph:check-circle-fill"></iconify-icon><span>{{ $obj }}</span></div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Prerequisites --}}
            @if(!empty($pre))
            <div class="cc">
                <h2><iconify-icon style="color:#8AC304 !important;" icon="ph:list-checks-fill"></iconify-icon> Requirements</h2>
                @foreach($pre as $p)
                <div class="prereq-item">{{ $p }}</div>
                @endforeach
            </div>
            @endif

            {{-- Module list — PREVIEW ONLY --}}
            @if(!empty($mods))
            <div class="cc">
                <h2><iconify-icon style="color:#8AC304 !important;" icon="ph:book-open-fill"></iconify-icon> Course Content <span style="font-size:0.85rem;font-weight:500;color:#6b7280;">({{ count($mods) }} Modules)</span></h2>
                <p class="text-sm text-gray-500 mb-4">Enroll to access full lesson content and video walkthroughs.</p>
                @foreach($mods as $i => $mod)
                @php
                    $mt = is_array($mod) ? ($mod['title'] ?? 'Module '.($i+1)) : ($mod->title ?? 'Module '.($i+1));
                    $mc = is_array($mod) ? ($mod['content'] ?? '') : ($mod->content ?? '');
                @endphp
                <div class="mod-preview">
                    <div class="mod-num">{{ $i+1 }}</div>
                    <div class="flex-1">
                        <p class="font-semibold text-sm" style="color:var(--dg);">{{ $mt }}</p>
                        @if(!empty($mc))
                        <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($mc, 80) }}</p>
                        @endif
                    </div>
                    @if(!$isEnrolled)
                    <iconify-icon style="color:#8AC304 !important;" icon="ph:lock-simple-fill" class="mod-locked-icon flex-shrink-0"></iconify-icon>
                    @else
                    <iconify-icon style="color:#8AC304 !important;" icon="ph:check-circle-fill" style="color:var(--pg);font-size:1rem;flex-shrink:0;"></iconify-icon>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            {{-- Instructor bio — uses ADMIN profile --}}
            <div class="cc">
                <h2><iconify-icon style="color:#8AC304 !important;" icon="ph:user-circle-fill"></iconify-icon> About the Instructor</h2>
                <div class="instr-bio">
                    @if($instrAvatar)
                       <img src="{{ asset('storage/' . $instrAvatar) }}" alt="{{ $instrName }}" class="ib-av">
                    @else
                        <div class="ib-init">{{ $instrInitial }}</div>
                    @endif
                    <div>
                        <p class="font-bold text-base mb-1" style="color:var(--dg);">{{ $instrName }}</p>
                        <p class="text-xs text-gray-500 mb-2">Professional Trader & Mentor</p>
                        @if(!empty($strategy->instructor_bio))
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $strategy->instructor_bio }}</p>
                        @else
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Our head instructor brings years of real-market experience across Forex, Crypto, and Futures. Every course is built on proven systems designed to get you trading confidently and consistently.
                        </p>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="pricing-card">

                {{-- Price --}}
                <div class="text-center mb-5">
                    <div class="price-big">${{ number_format($strategy->price,2) }}</div>
                    <p class="text-gray-400 text-sm mt-1">
                        @if($strategy->duration_days) {{ $strategy->duration_days }}-day access
                        @else Lifetime access @endif
                    </p>
                </div>

                {{-- ENROLLED --}}
                @if($isEnrolled && isset($enrollment) && $enrollment && $enrollment->isActive())
                <div class="enrolled-state">
                    <div class="flex items-center justify-between mb-3">
                        <div class="e-pill"><span class="dot"></span>  Enrolled</div>
                        <span class="text-xs text-gray-500">
                            @if($enrollment->expires_at) {{ $enrollment->expires_at->diffForHumans() }} left
                            @else Lifetime @endif
                        </span>
                    </div>
                    <div class="mb-3">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-green-700 font-medium">Progress</span>
                            <span class="font-bold text-green-700">{{ $enrollment->progress }}%</span>
                        </div>
                        <div class="e-pbar"><div class="e-pfill" style="width:{{ $enrollment->progress }}%"></div></div>
                    </div>
                    <a href="{{ route('strategies.strategylearn', $strategy->id) }}" class="btn-main" style="text-decoration:none;">
                       
                        Continue Learning →
                    </a>
                </div>

                {{-- Re-enroll option (with warning) --}}
                <div class="mt-3 p-3 rounded-xl" style="background:#fffbeb;border:1.5px solid #fcd34d;">
                    <p class="text-xs text-amber-700 mb-2 font-medium">Want to restart or extend this course?</p>
                    <button type="button" class="w-full py-2 px-3 rounded-lg text-xs font-bold text-amber-800 border border-amber-300 bg-amber-50 hover:bg-amber-100 transition-colors"
                        onclick="openReenrollModal()">
                        <iconify-icon style="color:#8AC304 !important;" icon="ph:arrow-counter-clockwise-bold" class="inline mr-1"></iconify-icon>
                        Enroll Again
                    </button>
                </div>

                {{-- Upgrades --}}
                @if(isset($upgrades) && $upgrades->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-bold uppercase tracking-wider text-amber-600 mb-3 flex items-center gap-1">
                        <iconify-icon style="color:#8AC304 !important;" icon="ph:arrow-square-up-right-fill"></iconify-icon> Upgrade Path
                    </p>
                    @foreach($upgrades as $up)
                    <div class="up-tier">
                        <div>
                            <p class="font-bold text-sm" style="color:var(--dg);">{{ $up->name }}</p>
                            <p class="text-xs text-gray-500">Pay +${{ number_format($up->price - $strategy->price,2) }} to upgrade</p>
                        </div>
                        <form action="{{ route('strategies.strategyupgrade', [$strategy->id, $up->id]) }}" method="POST" class="up-form">
                            @csrf
                            <button type="submit" class="up-btn up-submit">Upgrade</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- NOT ENROLLED --}}
                @else
                <form id="enrollForm" action="{{ route('strategies.strategyenroll', $strategy->id) }}" method="POST">
                    @csrf
                    @php $canAfford = auth()->user()->available_balance >= $strategy->price; @endphp
                    @if(!$canAfford)
                    <div class="mb-4 p-3 rounded-xl flex items-start gap-2" style="background:#fffbeb;border:1.5px solid #fcd34d;">
                        <iconify-icon style="color:#8AC304 !important;" icon="ph:warning-circle-fill" style="color:#f59e0b;font-size:1.1rem;flex-shrink:0;margin-top:1px;"></iconify-icon>
                        <div class="text-sm text-amber-700">
                            Insufficient balance (<strong>${{ number_format(auth()->user()->available_balance,2) }}</strong>).
                            <a href="{{ route('user.deposit') }}" class="font-bold underline">Deposit now</a>
                        </div>
                    </div>
                    @endif
                    <button type="{{ $canAfford ? 'submit' : 'button' }}" id="enrollBtn" class="btn-main" {{ !$canAfford ? 'disabled' : '' }}>
                        <span id="btnTxt">{{ $canAfford ? 'Enroll Now -> $'.number_format($strategy->price,2) : 'Insufficient Balance' }}</span>
                        <span id="btnSpin" class="spin" style="display:none;"></span>
                    </button>
                </form>
                <p class="text-center text-xs text-gray-400 mt-2">
                    Your balance: <strong style="color:var(--dg);">${{ number_format(auth()->user()->available_balance,2) }}</strong>
                </p>
                @endif

                <div class="border-t border-gray-100 my-5"></div>

                {{-- Features --}}
                <h3 class="text-sm font-bold mb-3" style="color:var(--dg);">This course includes:</h3>
                <ul class="feat-list">
                    @if(!empty($feats))
                        @foreach($feats as $f)
                        <li><iconify-icon style="color:#8AC304 !important;" icon="ph:check-circle-fill"></iconify-icon> {{ $f }}</li>
                        @endforeach
                    @else
                        <li><iconify-icon style="color:#8AC304 !important;" icon="ph:check-circle-fill"></iconify-icon> Full course access</li>
                        <li><iconify-icon style="color:#8AC304 !important;" icon="ph:video-fill"></iconify-icon> Video lesson walkthroughs</li>
                        <li><iconify-icon style="color:#8AC304 !important;" icon="ph:broadcast-fill"></iconify-icon> Live Trading sessions</li>
                        <li><iconify-icon style="color:#8AC304 !important;" icon="ph:certificate-fill"></iconify-icon> Certificate on completion</li>
                        <li><iconify-icon style="color:#8AC304 !important;" icon="ph:headset-fill"></iconify-icon> 24/7 support</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

</div>

{{-- ── Re-enroll Confirm Modal ── --}}
<div class="modal-overlay" id="reenrollModal">
    <div class="modal-box">
        <div class="modal-title">⚠️ Already Enrolled</div>
        <p class="modal-body">
            You are already enrolled in <strong>{{ $strategy->name }}</strong>. Enrolling again will deduct
            <strong>${{ number_format($strategy->price,2) }}</strong> from your balance and restart your progress.
            Are you sure you want to continue?
        </p>
        <div class="modal-actions">
            <button class="modal-cancel" onclick="closeReenrollModal()">Cancel</button>
            <button class="modal-confirm" onclick="submitReenroll()">Yes, Enroll Again</button>
        </div>
    </div>
</div>

{{-- Hidden re-enroll form --}}
<form id="reenrollForm" action="{{ route('strategies.strategyenroll', $strategy->id) }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="force" value="1">
</form>

<script>
// Enroll form — loading spinner
document.getElementById('enrollForm')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('enrollBtn');
    if (!btn || btn.disabled) { e.preventDefault(); return false; }
    btn.disabled = true;
    document.getElementById('btnTxt').textContent = 'Enrolling…';
    document.getElementById('btnSpin').style.display = 'inline-block';
});

// Upgrade forms
document.querySelectorAll('.up-form').forEach(f => {
    f.addEventListener('submit', function() {
        const btn = this.querySelector('.up-submit');
        if (!btn || btn.disabled) { event.preventDefault(); return; }
        btn.disabled = true; btn.textContent = '…';
    });
});

// Toast dismiss
document.querySelectorAll('.s-toast').forEach(t => {
    setTimeout(() => { t.style.transition='opacity 0.4s'; t.style.opacity='0'; setTimeout(()=>t.remove(),400); }, 3500);
});

// Re-enroll modal
function openReenrollModal() {
    document.getElementById('reenrollModal').classList.add('open');
}
function closeReenrollModal() {
    document.getElementById('reenrollModal').classList.remove('open');
}
function submitReenroll() {
    document.getElementById('reenrollForm').submit();
}
document.getElementById('reenrollModal').addEventListener('click', function(e) {
    if (e.target === this) closeReenrollModal();
});
</script>
@endsection