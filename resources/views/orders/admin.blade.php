@extends('layouts.app')

@section('title', 'Admin Dashboard — BAKU DAPA Order Verification')

@section('content')
<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="font-label text-xs uppercase tracking-[0.2em] text-bd-gold">Internal Management</span>
                <h1 class="font-display text-3xl sm:text-4xl font-bold text-bd-text mt-1">
                    Admin Order & Payment Verification
                </h1>
            </div>
            <a href="{{ route('order.create') }}" class="font-label text-xs px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-bd-text">
                + Create Test Order
            </a>
        </div>

        @if(session('success'))
            <div class="glass-card border-bd-halal/60 p-4 rounded-2xl text-sm text-bd-gold">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if(count($orders) === 0)
            <div class="glass-card rounded-3xl p-12 text-center text-sm text-bd-muted">
                No orders have been placed yet.
            </div>
        @else
            <div class="space-y-6">
                @foreach($orders as $order)
                <div class="glass-card rounded-3xl p-6 sm:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                        <!-- Order Info -->
                        <div class="lg:col-span-5 space-y-3">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('order.track', $order['code']) }}" class="font-code text-lg font-bold text-bd-gold hover:underline">
                                    {{ $order['code'] }}
                                </a>
                                <span class="font-label text-xs px-2.5 py-0.5 rounded-full bg-white/10 text-bd-gold uppercase">
                                    {{ $order['fulfillment_type'] }}
                                </span>
                                <span class="font-label text-xs px-2.5 py-0.5 rounded-full bg-bd-select text-bd-gold uppercase">
                                    {{ str_replace('_', ' ', $order['status']) }}
                                </span>
                            </div>

                            <p class="text-sm text-bd-text font-bold">
                                {{ $order['customer_name'] }} <span class="font-label text-xs text-bd-muted">({{ $order['customer_phone'] }})</span>
                            </p>
                            <p class="text-xs text-bd-muted">{{ $order['delivery_address'] }}</p>
                            @if(!empty($order['address_detail']))
                                <p class="text-xs text-bd-gold mt-0.5">Landmark: {{ $order['address_detail'] }}</p>
                            @endif
                            @if($order['fulfillment_type'] === 'delivery' && !empty($order['delivery_lat']) && !empty($order['delivery_lng']))
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $order['delivery_lat'] }},{{ $order['delivery_lng'] }}" target="_blank" rel="noopener"
                                   class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5 rounded-lg bg-bd-cta/30 border border-bd-cta/60 text-xs font-semibold text-bd-gold hover:bg-bd-cta/50 transition">
                                    📍 Open pin in Google Maps
                                </a>
                            @endif

                            <div class="pt-2 border-t border-white/10 text-xs space-y-1">
                                @foreach($order['items'] as $item)
                                    <div class="text-bd-text">• {{ $item['qty'] }}x {{ $item['name'] }} ({{ $item['category'] }})</div>
                                @endforeach
                                <div class="font-label text-sm font-bold text-bd-gold pt-1">
                                    Total: IDR {{ number_format($order['grand_total']) }} via {{ $order['payment_method'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Payment Proof Preview -->
                        <div class="lg:col-span-3">
                            <span class="font-label text-xs uppercase text-bd-muted block mb-2">Payment Receipt</span>
                            @if(!empty($order['payment_proof']))
                                <a href="{{ asset('storage/' . $order['payment_proof']) }}" target="_blank" class="block rounded-2xl overflow-hidden border border-bd-cta bg-black/50 max-h-44">
                                    <img src="{{ asset('storage/' . $order['payment_proof']) }}" alt="Proof" class="w-full h-40 object-cover hover:scale-105 transition">
                                </a>
                                <span class="text-xs text-bd-gold block mt-1">Click image to enlarge</span>
                            @else
                                <div class="p-6 rounded-2xl bg-bd-bg/60 border border-white/10 text-center text-xs text-bd-muted">
                                    No receipt uploaded yet
                                </div>
                            @endif
                        </div>

                        <!-- Admin Status Action Controls -->
                        <div class="lg:col-span-4 space-y-3">
                            <form action="{{ route('admin.orders.status', $order['code']) }}" method="POST" class="space-y-3">
                                @csrf
                                <label class="block font-label text-xs uppercase text-bd-gold">
                                    Verify Payment & Update Order Stage
                                </label>
                                <select name="status" class="w-full px-4 py-3 rounded-xl bg-bd-bg border border-white/15 font-label text-xs text-bd-text">
                                    <option value="unpaid" {{ $order['status'] === 'unpaid' ? 'selected' : '' }}>0. Unpaid (Awaiting Proof)</option>
                                    <option value="waiting_verification" {{ $order['status'] === 'waiting_verification' ? 'selected' : '' }}>0.5 Waiting Verification</option>
                                    <option value="paid" {{ $order['status'] === 'paid' ? 'selected' : '' }}>1. Paid (Verified by Admin)</option>
                                    <option value="processing" {{ $order['status'] === 'processing' ? 'selected' : '' }}>2. Processing (Cooking & Vacuum)</option>
                                    @if($order['fulfillment_type'] === 'pickup')
                                        <option value="ready_for_pickup" {{ $order['status'] === 'ready_for_pickup' ? 'selected' : '' }}>3. Ready for Pickup at Baku Dapa</option>
                                    @else
                                        <option value="delivering" {{ $order['status'] === 'delivering' ? 'selected' : '' }}>3. On Delivery (Courier Dispatched)</option>
                                    @endif
                                    <option value="completed" {{ $order['status'] === 'completed' ? 'selected' : '' }}>4. Completed (Received by Customer)</option>
                                    <option value="rejected" {{ $order['status'] === 'rejected' ? 'selected' : '' }}>✕ Reject Payment Proof (Ask Re-upload)</option>
                                </select>

                                <button type="submit" class="w-full py-3 rounded-xl bg-bd-cta hover:bg-bd-cta-hover text-white font-display font-bold text-xs transition">
                                    Save & Update Live Tracker
                                </button>
                            </form>

                            <a href="{{ route('order.track', $order['code']) }}" target="_blank" class="block text-center text-xs font-label text-bd-muted hover:text-bd-text">
                                View Customer Live Tracker Page ↗
                            </a>
                        </div>

                    </div>
                </div>
                @endforeach
            </div>
        @endif

    </div>
</section>
@endsection