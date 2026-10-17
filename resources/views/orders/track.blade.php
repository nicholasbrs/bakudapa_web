@extends('layouts.app')

@section('title', 'Order #' . $order['code'] . ' — BAKU DAPA Tracker')

@section('content')
@php
    // Determine step progression index (0 to 4)
    $statusMap = [
        'unpaid' => 0,
        'rejected' => 0,
        'waiting_verification' => 0.5,
        'paid' => 1,
        'processing' => 2,
        'delivering' => 3,
        'ready_for_pickup' => 3,
        'completed' => 4,
    ];
    $currentStep = $statusMap[$order['status']] ?? 0;
    $isPickup = $order['fulfillment_type'] === 'pickup';

    // Tracker live terkunci sampai pembayaran terverifikasi admin
    $locked = in_array($order['status'], ['unpaid', 'waiting_verification', 'rejected']);
    $phase = $locked ? ($order['status'] === 'waiting_verification' ? 2 : 1) : 3;

    // Titik peta: pin pelanggan (delivery) atau lokasi toko (pickup)
    $mapLat = $isPickup ? ($shop['lat'] ?? null) : ($order['delivery_lat'] ?? null);
    $mapLng = $isPickup ? ($shop['lng'] ?? null) : ($order['delivery_lng'] ?? null);
@endphp

<section class="py-12 lg:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Flash Notifications -->
        @if(session('success'))
            <div class="glass-card border-[#4ADE80]/60 p-4 rounded-2xl text-sm text-[#86EFAC] flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="glass-card border-red-500/60 p-4 rounded-2xl text-sm text-red-300">
                @foreach($errors->all() as $err)
                    <p>• {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <!-- Area yang di-refresh otomatis ketika status berubah (lihat js/track-live.js) -->
        <div id="live-region" data-status="{{ $order['status'] }}" class="flex flex-col gap-8">

        <!-- TOP HEADER CARD -->
        <div class="order-1 glass-card rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3">
                    <span class="font-mono-ai text-xs px-3 py-1 rounded-full bg-[#1B4323] border border-[#4E8752] text-[#86EFAC]">
                        {{ strtoupper($order['fulfillment_type']) }} ORDER
                    </span>
                    <span class="font-mono-ai text-xs text-[#94A898]">{{ $order['created_at'] }}</span>
                </div>
                <h1 class="font-display-ai text-3xl sm:text-4xl font-bold text-white mt-2">
                    Order Code: <span class="font-mono-ai text-[#86EFAC]">{{ $order['code'] }}</span>
                </h1>
                <p class="text-sm text-[#94A898] mt-1">
                    Customer: <strong class="text-white">{{ $order['customer_name'] }}</strong> ({{ $order['customer_phone'] }})
                </p>
            </div>

            <!-- Live Status Pill -->
            <div class="flex flex-col items-start md:items-end gap-2">
                <span class="text-xs font-mono-ai uppercase text-[#94A898]">Current Live Status</span>
                @if($order['status'] === 'unpaid')
                    <span class="font-mono-ai px-4 py-2 rounded-xl bg-amber-500/20 border border-amber-400/50 text-amber-300 text-xs font-bold">
                        ⏳ AWAITING PAYMENT PROOF
                    </span>
                @elseif($order['status'] === 'waiting_verification')
                    <span class="font-mono-ai px-4 py-2 rounded-xl bg-cyan-500/20 border border-cyan-400/50 text-cyan-300 text-xs font-bold animate-pulse">
                        🔍 WAITING ADMIN VERIFICATION
                    </span>
                @elseif($order['status'] === 'rejected')
                    <span class="font-mono-ai px-4 py-2 rounded-xl bg-red-500/20 border border-red-400/50 text-red-300 text-xs font-bold">
                        ✕ PAYMENT PROOF REJECTED — PLEASE RE-UPLOAD
                    </span>
                @else
                    <span class="font-mono-ai px-4 py-2 rounded-xl bg-[#1B4323] border border-[#4ADE80] text-[#4ADE80] text-xs font-bold">
                        ● {{ strtoupper(str_replace('_', ' ', $order['status'])) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- =====================================================
             PROGRESS: PAY -> VERIFY -> LIVE TRACKING
        ====================================================== -->
        <div class="order-2 glass-card rounded-3xl p-5 sm:p-6">
            <div class="flex items-center">
                @foreach([1 => 'Pay', 2 => 'Verification', 3 => 'Live Tracking'] as $n => $label)
                    @php
                        $stepDone = $n < $phase || ($n === 3 && $order['status'] === 'completed');
                        $stepActive = $n === $phase && !$stepDone;
                    @endphp
                    <div class="flex items-center gap-2.5 shrink-0">
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold font-mono-ai {{ $stepDone ? 'bg-[#86EFAC] text-[#0D140F]' : ($stepActive ? 'bg-[#4E8752] text-white ring-2 ring-[#86EFAC]/50' : 'bg-white/10 text-[#94A898]') }}">{{ $stepDone ? '✓' : sprintf('%02d', $n) }}</span>
                        <span class="text-xs font-semibold {{ $stepActive ? 'text-white' : ($stepDone ? 'text-[#86EFAC]' : 'text-[#94A898]') }}">{{ $label }}</span>
                    </div>
                    @if($n < 3)
                        <div class="flex-1 h-px mx-3 {{ $n < $phase ? 'bg-[#86EFAC]' : 'bg-white/15' }}"></div>
                    @endif
                @endforeach
            </div>
            <p class="text-xs text-[#94A898] mt-4">
                @if($order['status'] === 'rejected')
                    Your last receipt was not accepted. Please upload a clear, new receipt below.
                @elseif($order['status'] === 'unpaid')
                    Transfer the exact total below and upload your receipt. Live tracking unlocks once our admin verifies your payment.
                @elseif($order['status'] === 'waiting_verification')
                    Receipt received. This page updates by itself the moment your payment is verified.
                @else
                    Payment verified. Follow every stage of your order below. This page updates automatically.
                @endif
            </p>
        </div>

        <!-- =====================================================
             INTERACTIVE & ANIMATED 4-STAGE ORDER STATUS TRACKER
        ====================================================== -->
        <div class="{{ $locked ? 'order-4' : 'order-3' }} glass-card rounded-3xl p-6 sm:p-10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-8">
                <div>
                    <span class="font-mono-ai text-xs uppercase tracking-[0.2em] text-[#E2C07D]">Interactive Live Telemetry</span>
                    <h2 class="font-display-ai text-2xl font-bold text-white">Order Journey & Animated Status</h2>
                </div>
                <span class="text-xs text-[#94A898]">Click any stage icon below to inspect details</span>
            </div>

            <div class="relative">
            @if($locked)
                <div class="absolute inset-0 z-10 flex items-center justify-center p-4">
                    <div class="max-w-md text-center p-6 rounded-2xl bg-[#0D140F]/95 border border-[#E2C07D]/40 shadow-2xl">
                        <div class="text-3xl mb-2">🔒</div>
                        <p class="font-display-ai text-lg font-bold text-white">
                            @if($order['status'] === 'waiting_verification') Verifying your payment…
                            @elseif($order['status'] === 'rejected') Receipt was rejected
                            @else Pay to unlock live tracking
                            @endif
                        </p>
                        <p class="text-xs text-[#94A898] mt-1.5 leading-relaxed">
                            @if($order['status'] === 'waiting_verification')
                                Hang tight. Your tracker unlocks here automatically as soon as the admin verifies the transfer.
                            @elseif($order['status'] === 'rejected')
                                Please upload a clear receipt below to continue.
                            @else
                                Upload your transfer receipt below. Once it is verified, every stage of your order becomes live here.
                            @endif
                        </p>
                    </div>
                </div>
            @endif
            <div class="{{ $locked ? 'opacity-25 blur-[2px] pointer-events-none select-none' : '' }}">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 relative">
                <!-- STAGE 1: PAID & VERIFIED -->
                <div onclick="showStageDetail(1)" class="cursor-pointer p-5 rounded-2xl bg-[#050806]/70 border {{ $currentStep >= 1 ? 'border-[#4ADE80]/60' : ($currentStep == 0.5 ? 'border-cyan-400/60' : 'border-white/10') }} transition hover:scale-[1.02]">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl border border-white/20 flex items-center justify-center shrink-0 {{ $currentStep == 1 ? 'status-node-active' : ($currentStep > 1 ? 'status-node-done' : 'bg-white/5') }}">
                            <!-- Animated Shield / Check SVG -->
                            <svg class="w-7 h-7 {{ $currentStep >= 1 ? 'text-white anim-float' : 'text-[#94A898]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-mono-ai text-[10px] uppercase text-[#86EFAC]">Stage 01</span>
                            <h3 class="font-display-ai text-base font-bold text-white">Payment Verified</h3>
                            <p class="text-xs text-[#94A898] mt-0.5">
                                @if($currentStep == 0.5) Waiting Admin Check @elseif($currentStep >= 1) Verified by Admin @else Pending Transfer @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- STAGE 2: KITCHEN PROCESSING -->
                <div onclick="showStageDetail(2)" class="cursor-pointer p-5 rounded-2xl bg-[#050806]/70 border {{ $currentStep >= 2 ? 'border-[#4ADE80]/60' : 'border-white/10' }} transition hover:scale-[1.02]">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl border border-white/20 flex items-center justify-center shrink-0 {{ $currentStep == 2 ? 'status-node-active' : ($currentStep > 2 ? 'status-node-done' : 'bg-white/5') }}">
                            <!-- Animated Cooking Flame SVG -->
                            <svg class="w-7 h-7 {{ $currentStep >= 2 ? 'text-[#E2C07D] anim-flame' : 'text-[#94A898]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-mono-ai text-[10px] uppercase text-[#86EFAC]">Stage 02</span>
                            <h3 class="font-display-ai text-base font-bold text-white">Cooking & Vacuum</h3>
                            <p class="text-xs text-[#94A898] mt-0.5">
                                {{ $currentStep >= 2 ? 'Freshly Cooked & Sealed' : 'Preparing Spices' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- STAGE 3: DELIVERY OR READY FOR PICKUP -->
                <div onclick="showStageDetail(3)" class="cursor-pointer p-5 rounded-2xl bg-[#050806]/70 border {{ $currentStep >= 3 ? 'border-[#4ADE80]/60' : 'border-white/10' }} transition hover:scale-[1.02]">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl border border-white/20 flex items-center justify-center shrink-0 {{ $currentStep == 3 ? 'status-node-active' : ($currentStep > 3 ? 'status-node-done' : 'bg-white/5') }}">
                            @if($isPickup)
                                <!-- Pickup Store Icon -->
                                <svg class="w-7 h-7 {{ $currentStep >= 3 ? 'text-white anim-float' : 'text-[#94A898]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            @else
                                <!-- Animated Courier Truck Icon -->
                                <svg class="w-7 h-7 {{ $currentStep >= 3 ? 'text-white anim-scooter' : 'text-[#94A898]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <span class="font-mono-ai text-[10px] uppercase text-[#86EFAC]">Stage 03</span>
                            <h3 class="font-display-ai text-base font-bold text-white">
                                {{ $isPickup ? 'Ready for Pickup' : 'On Delivery' }}
                            </h3>
                            <p class="text-xs text-[#94A898] mt-0.5">
                                {{ $isPickup ? 'Pickup at Baku Dapa' : 'Cold-Chain Courier' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- STAGE 4: COMPLETED -->
                <div onclick="showStageDetail(4)" class="cursor-pointer p-5 rounded-2xl bg-[#050806]/70 border {{ $currentStep >= 4 ? 'border-[#4ADE80]/60' : 'border-white/10' }} transition hover:scale-[1.02]">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl border border-white/20 flex items-center justify-center shrink-0 {{ $currentStep == 4 ? 'status-node-active' : 'bg-white/5' }}">
                            <svg class="w-7 h-7 {{ $currentStep >= 4 ? 'text-[#86EFAC] anim-float' : 'text-[#94A898]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-mono-ai text-[10px] uppercase text-[#86EFAC]">Stage 04</span>
                            <h3 class="font-display-ai text-base font-bold text-white">Completed</h3>
                            <p class="text-xs text-[#94A898] mt-0.5">Enjoy Your Meal!</p>
                        </div>
                    </div>
                </div>
            </div>

            </div>
            </div>

            <!-- Interactive Stage Description Box -->
            <div id="stage-detail-box" class="mt-6 p-4 rounded-2xl bg-[#1B4323]/30 border border-[#4E8752]/40 text-xs text-[#F2F7F4] flex items-center justify-between">
                <span id="stage-detail-text">
                    @if($order['status'] === 'unpaid')
                        💡 Please transfer the exact total amount and upload your payment receipt below so our Admin can verify your order.
                    @elseif($order['status'] === 'waiting_verification')
                        🔍 Your payment receipt has been received! Our Admin is currently verifying the transfer. Need to fix the receipt? Click "Withdraw Proof" below.
                    @elseif($order['status'] === 'paid')
                        ✅ Payment verified! Your order has been forwarded to the Baku Dapa kitchen team.
                    @elseif($order['status'] === 'processing')
                        🔥 Our chefs are cooking your Manadonese dishes and sealing them in food-grade vacuum packs.
                    @elseif($order['status'] === 'delivering')
                        🚚 Your package is on the way with our courier!
                    @elseif($order['status'] === 'ready_for_pickup')
                        🏪 Your order is ready for pickup at Baku Dapa Central Kitchen! Show code <strong>{{ $order['code'] }}</strong>.
                    @elseif($order['status'] === 'completed')
                        🎉 Order completed! Thank you for dining with Baku Dapa.
                    @endif
                </span>
            </div>
        </div>

        <!-- =====================================================
             PAYMENT INSTRUCTIONS + PROOF UPLOAD / WITHDRAW SECTION
        ====================================================== -->
        <div class="{{ $locked ? 'order-3' : 'order-4' }} grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- LEFT: BANK / QRIS DETAILS & UPLOAD / WITHDRAW -->
            <div class="lg:col-span-7 space-y-6">
                <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <div>
                            <span class="font-mono-ai text-xs text-[#86EFAC] uppercase">Manual Payment Gateway</span>
                            <h3 class="font-display-ai text-2xl font-bold text-white mt-0.5">{{ $selectedPayment['name'] }}</h3>
                        </div>
                        <span class="font-mono-ai text-xs px-3 py-1 rounded-lg bg-white/10 text-[#E2C07D]">
                            {{ $order['payment_method'] }}
                        </span>
                    </div>

                    @if($selectedPayment['type'] === 'qris')
                        <!-- QRIS Placeholder Box -->
                        <div class="p-6 rounded-2xl bg-[#050806]/90 border border-white/15 flex flex-col sm:flex-row items-center gap-6">
                            <div class="w-44 h-44 rounded-2xl bg-white p-3 flex flex-col items-center justify-center text-center shrink-0 shadow-lg">
                                <div class="w-full h-full border-2 border-dashed border-gray-400 rounded-xl flex flex-col items-center justify-center p-2">
                                    <span class="font-mono-ai text-xs font-bold text-gray-900">QRIS BAKU DAPA</span>
                                    <span class="text-[10px] text-gray-500 mt-1">(QR Image Placeholder — Coming Soon)</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <p class="font-mono-ai text-xs text-[#86EFAC]">{{ $selectedPayment['account_number'] }}</p>
                                <p class="font-display-ai text-lg font-bold text-white">{{ $selectedPayment['account_holder'] }}</p>
                                <p class="text-xs text-[#94A898] leading-relaxed">{{ $selectedPayment['instructions'] }}</p>
                                <div class="pt-2">
                                    <span class="text-xs text-[#94A898] block">Exact Amount to Pay (incl. Unique Code):</span>
                                    <span class="font-mono-ai text-2xl font-bold text-[#86EFAC]">IDR {{ number_format($order['grand_total']) }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Bank Transfer Account Card (Mandiri, BCA, BRI, BNI) -->
                        <div class="p-6 rounded-2xl bg-[#050806]/90 border border-white/15 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <span class="text-xs text-[#94A898] block">Account Number ({{ $order['payment_method'] }})</span>
                                    <span id="bank-acc-num" class="font-mono-ai text-2xl sm:text-3xl font-bold text-white tracking-wider">
                                        {{ $selectedPayment['account_number'] }}
                                    </span>
                                    <span class="text-xs text-[#E2C07D] block mt-1">a/n {{ $selectedPayment['account_holder'] }}</span>
                                </div>
                                <button type="button" onclick="copyText('{{ $selectedPayment['account_number'] }}')"
                                        class="px-4 py-2 rounded-xl bg-white/10 hover:bg-[#4E8752] text-white font-mono-ai text-xs font-semibold transition">
                                    Copy Number
                                </button>
                            </div>

                            <div class="pt-4 border-t border-white/10 flex items-center justify-between">
                                <div>
                                    <span class="text-xs text-[#94A898] block">Exact Transfer Amount (with 3-digit unique code)</span>
                                    <span class="font-mono-ai text-2xl font-bold text-[#86EFAC]">IDR {{ number_format($order['grand_total']) }}</span>
                                </div>
                                <button type="button" onclick="copyText('{{ $order['grand_total'] }}')"
                                        class="px-4 py-2 rounded-xl bg-white/10 hover:bg-[#4E8752] text-white font-mono-ai text-xs font-semibold transition">
                                    Copy Amount
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- PAYMENT PROOF UPLOAD OR WITHDRAW AREA -->
                    @if(!empty($order['payment_proof']))
                        <div class="p-5 rounded-2xl bg-[#1B4323]/25 border border-[#4E8752]/50 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-mono-ai text-xs text-[#86EFAC] font-bold block">✓ Uploaded Payment Receipt</span>
                                    <span class="text-[11px] text-[#94A898]">Uploaded at: {{ $order['payment_uploaded_at'] }}</span>
                                </div>
                                @if(in_array($order['status'], ['waiting_verification', 'rejected', 'unpaid']))
                                    <form action="{{ route('order.withdraw', $order['code']) }}" method="POST" onsubmit="return confirm('Withdraw this payment proof so you can upload a new one?')">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-500/20 hover:bg-red-500/40 border border-red-400/40 text-red-300 font-mono-ai text-xs font-semibold transition">
                                            ↺ Withdraw & Re-upload Proof
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="rounded-xl overflow-hidden border border-white/15 max-h-80 bg-black/50 flex items-center justify-center">
                                <img src="{{ asset('storage/' . $order['payment_proof']) }}" alt="Payment Receipt" class="max-h-72 object-contain">
                            </div>
                        </div>
                    @else
                        <!-- Upload Form -->
                        <form action="{{ route('order.upload', $order['code']) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2">
                            @csrf
                            <div>
                                <label class="block text-xs font-mono-ai uppercase text-[#E2C07D] mb-2">
                                    Switch Payment Method (Optional)
                                </label>
                                <select name="payment_method" class="w-full px-4 py-2.5 rounded-xl bg-[#050806] border border-white/15 text-xs font-mono-ai text-white">
                                    @foreach($paymentMethods as $mCode => $mData)
                                        <option value="{{ $mCode }}" {{ $order['payment_method'] === $mCode ? 'selected' : '' }}>{{ $mCode }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-mono-ai uppercase text-[#86EFAC] mb-2">
                                    Upload Transfer Receipt / Screenshot (JPG, PNG, WEBP — Max 5MB) *
                                </label>
                                <input type="file" name="payment_proof" accept="image/*" required
                                       class="w-full px-4 py-3 rounded-xl bg-[#050806] border border-dashed border-[#4E8752] text-xs text-[#94A898] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#4E8752] file:text-white hover:file:bg-[#3d6e40]">
                            </div>

                            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-[#4E8752] to-[#1B4323] border border-[#86EFAC]/40 text-white font-display-ai font-bold text-sm shadow-lg hover:brightness-110 transition">
                                Submit Payment Proof for Verification
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- RIGHT: ORDER DETAILS & ADDRESS / PICKUP -->
            <div class="lg:col-span-5 space-y-6">
                <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                    <h3 class="font-display-ai text-xl font-bold text-white border-b border-white/10 pb-3">
                        Order & Receiving Details
                    </h3>

                    <div class="space-y-2 text-xs">
                        <p class="text-[#94A898]">Method: <strong class="text-white uppercase">{{ $order['fulfillment_type'] }}</strong></p>
                        <p class="text-[#94A898]">Address / Location:</p>
                        <div class="p-3.5 rounded-xl bg-[#050806]/70 border border-white/10 text-white leading-relaxed">
                            {{ $order['delivery_address'] }}
                            @if(!empty($order['delivery_city']))
                                <div class="font-mono-ai text-[#86EFAC] mt-1">City: {{ $order['delivery_city'] }}</div>
                            @endif
                            @if(!empty($order['address_detail']))
                                <div class="text-[#E2C07D] mt-1">Landmark: {{ $order['address_detail'] }}</div>
                            @endif
                        </div>

                        @if($mapLat !== null && $mapLng !== null)
                            <div class="space-y-3 pt-2">
                                <p class="text-[#94A898]">{{ $isPickup ? 'Pickup point on map:' : 'Your pinned location:' }}</p>
                                <div class="relative isolate rounded-xl overflow-hidden border border-white/15">
                                    <div id="track-map" class="map-dark h-56 w-full"
                                         data-lat="{{ $mapLat }}" data-lng="{{ $mapLng }}"
                                         data-type="{{ $isPickup ? 'pickup' : 'delivery' }}"
                                         data-label="{{ $isPickup ? ($shop['name'] ?? 'Pickup point') : 'Delivery location' }}"></div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $mapLat }},{{ $mapLng }}" target="_blank" rel="noopener"
                                       class="px-3.5 py-2 rounded-lg bg-[#4E8752] hover:bg-[#3d6e40] text-white text-[11px] font-semibold transition">Open in Google Maps</a>
                                    @if($isPickup)
                                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $mapLat }},{{ $mapLng }}" target="_blank" rel="noopener"
                                           class="px-3.5 py-2 rounded-lg bg-white/10 hover:bg-white/20 border border-white/15 text-white text-[11px] font-semibold transition">Get Directions</a>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if(!empty($order['notes']))
                            <p class="text-[#E2C07D] pt-1">Notes: "{{ $order['notes'] }}"</p>
                        @endif
                    </div>

                    <div class="border-t border-white/10 pt-4 space-y-3">
                        <span class="font-mono-ai text-xs uppercase text-[#94A898] block">Ordered Dishes</span>
                        @foreach($order['items'] as $item)
                            <div class="flex justify-between text-xs">
                                <span class="text-white">{{ $item['qty'] }}x {{ $item['name'] }} <em class="text-[#86EFAC]">({{ $item['category'] }})</em></span>
                                <span class="font-mono-ai text-white">IDR {{ number_format($item['price'] * $item['qty']) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-white/10 pt-4 space-y-2 text-xs">
                        <div class="flex justify-between text-[#94A898]">
                            <span>Subtotal</span>
                            <span class="font-mono-ai">IDR {{ number_format($order['subtotal']) }}</span>
                        </div>
                        <div class="flex justify-between text-[#94A898]">
                            <span>Shipping Fee</span>
                            <span class="font-mono-ai">IDR {{ number_format($order['shipping_fee']) }}</span>
                        </div>
                        <div class="flex justify-between text-[#94A898]">
                            <span>Unique Verification Code</span>
                            <span class="font-mono-ai text-[#E2C07D]">+{{ $order['unique_code'] }}</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-white pt-2 border-t border-white/10">
                            <span>Total Paid</span>
                            <span class="font-mono-ai text-[#86EFAC]">IDR {{ number_format($order['grand_total']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        </div><!-- /#live-region -->

    </div>
</section>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
@endpush

@push('scripts')
<script>
    // Clear cart in localStorage once order is created and tracked
    localStorage.removeItem('bakudapa_cart');
    cart = [];
    renderCart();

    function copyText(text) {
        navigator.clipboard.writeText(text);
        alert('Copied to clipboard: ' + text);
    }

    function showStageDetail(stage) {
        const textEl = document.getElementById('stage-detail-text');
        const descriptions = {
            1: '🛡️ Stage 01 (Payment Verified): Admin checks your uploaded transfer receipt against our bank mutation (QRIS/BCA/Mandiri/BRI/BNI).',
            2: '🔥 Stage 02 (Cooking & Vacuum): Native Manadonese chefs prepare your order fresh and seal it in food-grade vacuum pouches.',
            3: '{{ $isPickup ? "🏪 Stage 03 (Ready for Pickup): Your package is ready to be picked up at Baku Dapa Central Kitchen." : "🚚 Stage 03 (On Delivery): Your vacuum-sealed package is dispatched with ice gel via cold-chain courier." }}',
            4: '🎉 Stage 04 (Completed): Package has been received by the customer. Enjoy authentic Manado flavors at your dining table!'
        };
        textEl.innerHTML = descriptions[stage];
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src="{{ asset('js/bd-map.js') }}"></script>
<script src="{{ asset('js/track-live.js') }}"></script>
@endpush
@endsection