@extends('layout.user')

@section('content')

@php
    $traderName = $copyAdmin->admin_name
        ?? $user->copy_admin_name
        ?? 'Your Trader';

    $hasTrader = !empty($copyAdmin) || !empty($user->copy_admin_id);
@endphp

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#0a0a0f] to-[#1a1a2e] px-4">

    <div class="max-w-2xl w-full bg-[#0f0f1a]/80 backdrop-blur-xl rounded-2xl border border-[#2a2a3e] shadow-2xl overflow-hidden">

        {{-- Header --}}
        <div class="relative bg-gradient-to-br from-[#1a1a2e] to-[#0a0a0f] p-6 text-center border-b border-[#2a2a3e]">

            {{-- Glow effect --}}
            <div class="absolute inset-0 bg-gradient-to-r from-[#00ff88]/5 to-[#00d4ff]/5"></div>

            <div class="relative w-16 h-16 mx-auto rounded-full  flex items-center justify-center mb-2 ">

                @if(isset($copyAdmin) && $copyAdmin->admin_profile_image)
                    <img
                        src="{{ asset('storage/admins/'.$copyAdmin->admin_profile_image) }}"
                        class="w-11 h-11 rounded-full object-cover"
                        alt="{{ $traderName }}">
                @else
                    <iconify-icon
                        icon="ph:user-circle-fill"
                        style="font-size:40px;color:#00ff88;">
                    </iconify-icon>
                @endif

            </div>


           

        </div>

        {{-- Body --}}
        <div class="p-8 text-center">

         @if($hasTrader)
    <div class="mb-6 p-4 bg-[#ff4444]/5 border-2 border-[#ff4444] rounded-lg shadow-lg shadow-[#ff4444]/20">
        <div class="flex items-start gap-3">
            <span class="text-[#ff4444] text-xl">⚠️</span>
            <div class="flex-1">
                <p class="text-[#ff4444] font-mono text-sm font-bold mb-1">
                    ACCESS RESTRICTED
                </p>
                <p class="text-gray-300 text-sm">
                    Your Admin <b class="text-[#ff4444]">{{ $traderName }}</b> has not granted you live trading access yet
                </p>
            </div>
        </div>
    </div>
@endif

            <div class="mb-6 p-4 bg-[#00ff88]/5 border border-[#00ff88]/20 rounded-lg">
                <p class="text-[#00ff88] font-mono text-sm mb-1">
                   FUNDING REQUIRED 
                </p>
                <p class="text-gray-300 text-sm">
                    Your account needs to be funded before you can be positioned and start live trading
                </p>
            </div>

            {{-- Balance Card --}}
            <div class="bg-[#0a0a0f] border border-[#2a2a3e] rounded-lg p-5 mb-6">

                <p class="text-xs font-mono tracking-wider text-gray-500 mb-1">
                    AVAILABLE BALANCE
                </p>

                <h3 class="text-3xl font-bold text-[#00ff88] font-mono">
                    ${{ number_format($user->available_balance ?? 0, 2) }}
                </h3>

                @if(($user->available_balance ?? 0) == 0)
                    <p class="text-xs text-gray-500 mt-2 font-mono">
                         Fund to unlock live trading
                    </p>
                @endif

            </div>

            {{-- Buttons --}}
            <div class="flex flex-wrap justify-center gap-3">

                <a href="{{ route('user.deposit') }}"
                   class="relative px-6 py-3 rounded-lg font-bold font-mono text-sm tracking-wider overflow-hidden group"
                   style="background: linear-gradient(135deg, #00ff88, #00d4ff); color: #0a0a0f;">

                    <span class="relative z-10"> FUND ACCOUNT</span>
                    <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>

                </a>

                @if(!$hasTrader)
                    <a href="{{ route('copytrading.index') }}"
                       class="px-6 py-3 rounded-lg font-bold font-mono text-sm border border-[#2a2a3e] text-gray-300 hover:border-[#00ff88] hover:text-[#00ff88] transition-all">
                        👤 SELECT TRADER
                    </a>
                @endif

            </div>

            {{-- Web3 Status Note --}}
            <div class="mt-6 pt-4 border-t border-[#2a2a3e]">
                <p class="text-xs font-mono text-gray-500">
                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#ff4444] animate-pulse mr-1"></span>
                    STATUS: <span class="text-[#ff4444]">INACTIVE</span> — Fund account & await trader approval to unlock live trading
                </p>
            </div>

        </div>

    </div>

</div>

@endsection