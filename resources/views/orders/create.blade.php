@extends('layouts.app')

@section('title', 'Order & Checkout — BAKU DAPA')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css">
@endpush

@section('content')
@php
    // Bila validasi server gagal, buka kembali step yang berisi field bermasalah
    $initialStep = 1;
    if ($errors->any()) {
        if ($errors->hasAny(['customer_name', 'customer_phone', 'customer_email', 'delivery_city', 'delivery_address', 'delivery_lat', 'delivery_lng', 'fulfillment_type'])) {
            $initialStep = 2;
        } elseif ($errors->hasAny(['cart', 'cart_json'])) {
            $initialStep = 1;
        } else {
            $initialStep = 3;
        }
    }
    $oldFulfillment = old('fulfillment_type', 'delivery');
    $oldPayment = old('payment_method', 'QRIS');
    $shopViewUrl = 'https://www.google.com/maps/search/?api=1&query=' . $shop['lat'] . ',' . $shop['lng'];
    $shopDirUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . $shop['lat'] . ',' . $shop['lng'];
@endphp

<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <span class="font-label text-xs uppercase tracking-[0.2em] text-bd-gold font-semibold">Direct Web Ordering</span>
            <h1 class="font-display text-3xl sm:text-5xl font-bold text-bd-text mt-1">
                Build Your Order & Checkout
            </h1>
            <p class="text-sm text-bd-muted mt-2">
                Choose your dishes, pin where you want it delivered (or pick it up), then pay and track your order live.
            </p>
        </div>

        <!-- Stepper -->
        <div class="flex items-center max-w-2xl mb-10" id="stepper">
            @foreach([1 => 'Choose Dishes', 2 => 'Your Details', 3 => 'Payment'] as $n => $label)
                <div id="step-pill-{{ $n }}" class="flex items-center gap-2.5 shrink-0">
                    <span class="pill-dot w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold font-label bg-white/10 text-bd-muted">{{ sprintf('%02d', $n) }}</span>
                    <span class="pill-label text-xs font-semibold hidden sm:block text-bd-muted">{{ $label }}</span>
                </div>
                @if($n < 3)
                    <div id="step-line-{{ $n }}" class="step-line flex-1 h-px mx-2 sm:mx-4 bg-white/15"></div>
                @endif
            @endforeach
        </div>

        @if($errors->any())
            <div class="glass-card border-red-500/50 p-4 rounded-2xl mb-8 text-sm text-red-300">
                <p class="font-bold text-red-400 mb-1">⚠ Please check your form:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('order.store') }}" method="POST" id="checkout-form" novalidate>
            @csrf
            <input type="hidden" name="cart_json" id="cart-json-input">
            <input type="hidden" name="fulfillment_type" id="fulfillment-type-input" value="{{ $oldFulfillment }}">
            <input type="hidden" name="payment_method" id="payment-method-input" value="{{ $oldPayment }}">
            <input type="hidden" name="delivery_lat" id="delivery-lat" value="{{ old('delivery_lat') }}">
            <input type="hidden" name="delivery_lng" id="delivery-lng" value="{{ old('delivery_lng') }}">
            <input type="hidden" name="delivery_place" id="delivery-place" value="{{ old('delivery_place') }}">

            <!-- =====================================================
                 STEP 1 — CHOOSE DISHES
            ====================================================== -->
            <div id="step-1" class="wizard-step">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="dish-filter-btn" data-filter="ALL" onclick="OrderPage.filter('ALL')">All Dishes</button>
                        <button type="button" class="dish-filter-btn" data-filter="HALAL" onclick="OrderPage.filter('HALAL')">Halal</button>
                        <button type="button" class="dish-filter-btn" data-filter="NON-HALAL" onclick="OrderPage.filter('NON-HALAL')">Non-Halal</button>
                    </div>
                    <p class="font-label text-xs text-bd-gold">Tap a photo to see the dish bigger</p>
                </div>

                <div id="dish-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 pb-40">
                    @foreach($menus as $menu)
                        <article class="dish-card glass-card rounded-3xl overflow-hidden flex flex-col group" data-category="{{ $menu['category'] }}">
                            <button type="button" onclick="OrderPage.openDish({{ $menu['id'] }})"
                                    class="relative block aspect-[4/3] overflow-hidden text-left"
                                    aria-label="View {{ $menu['name'] }} bigger">
                                <img src="{{ $menu['image'] }}" alt="{{ $menu['name'] }}" loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                                <span class="absolute top-3 left-3 font-label text-xs px-2 py-1 rounded-md font-bold {{ $menu['category'] === 'HALAL' ? 'bg-bd-halal/15 text-bd-halal' : 'bg-amber-400 text-[#1a1203]' }}">
                                    {{ $menu['category'] }}
                                </span>
                                <span class="absolute bottom-3 right-3 font-label text-xs px-2.5 py-1 rounded-full bg-black/60 text-bd-text backdrop-blur opacity-0 group-hover:opacity-100 transition">
                                    View bigger
                                </span>
                            </button>

                            <div class="p-5 flex flex-col flex-1">
                                <span class="font-label text-xs uppercase tracking-wider text-bd-gold">{{ $menu['badge'] }}</span>
                                <h3 class="font-display text-lg font-bold text-bd-text mt-1">{{ $menu['name'] }}</h3>
                                <p class="text-xs text-bd-muted leading-relaxed mt-2 line-clamp-3">{{ $menu['desc'] }}</p>
                                <p class="font-label text-xs text-bd-muted mt-3">{{ $menu['weight'] }} · {{ $menu['spicy'] }}</p>

                                <div class="mt-auto pt-4">
                                    <p class="font-num text-lg font-bold text-bd-gold mb-3">IDR {{ number_format($menu['price']) }}</p>
                                    <div data-dish-ctl="{{ $menu['id'] }}"></div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Floating Bag (tengah bawah, tidak terlalu ke dasar layar) -->
                <button type="button" id="floating-bag" onclick="OrderPage.openBag()"
                        class="fixed bottom-10 left-1/2 -translate-x-1/2 z-40 flex items-center gap-4 pl-5 pr-3 py-3 rounded-full bg-gradient-to-r from-bd-cta to-bd-cta-deep border border-bd-gold/50 text-white shadow-2xl shadow-black/60 hover:brightness-110 transition">
                    <svg class="w-6 h-6 text-bd-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <span class="text-left leading-tight">
                        <span class="block font-display text-sm font-bold">
                            <span data-cart-count>0</span> <span id="floating-bag-label">items</span> in bag
                        </span>
                        <span id="floating-bag-total" class="block font-num text-xs text-bd-text/80">IDR 0</span>
                    </span>
                    <span class="px-4 py-2 rounded-full bg-white/15 text-xs font-bold">View Bag →</span>
                </button>
            </div>

            <!-- =====================================================
                 STEP 2 — YOUR DETAILS + RECEIVING METHOD
            ====================================================== -->
            <div id="step-2" class="wizard-step hidden">
                <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
                    <div>
                        <h2 class="font-display text-2xl sm:text-3xl font-bold text-bd-text">Where should we bring it?</h2>
                        <p class="text-sm text-bd-muted mt-1">Tell us who you are, then choose delivery or pickup.</p>
                    </div>
                    <button type="button" onclick="OrderPage.goStep(1)" class="text-xs font-label text-bd-gold hover:text-bd-text transition">← Edit order</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    <!-- LEFT: customer + receiving method -->
                    <div class="lg:col-span-5 space-y-6">

                        <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                            <div class="flex items-center gap-3">
                                <span class="font-label w-7 h-7 rounded-lg bg-bd-cta text-white text-xs font-bold flex items-center justify-center">A</span>
                                <h3 class="font-display text-xl font-bold text-bd-text">Customer Information</h3>
                            </div>

                            <div>
                                <label for="f-name" class="block text-xs font-label uppercase text-bd-muted mb-2">Full Name *</label>
                                <input type="text" id="f-name" name="customer_name" value="{{ old('customer_name') }}" placeholder="e.g. Nicholas Barus" autocomplete="name"
                                       class="w-full px-4 py-3 rounded-xl bg-bd-bg/90 border border-white/15 text-sm text-bd-text focus:outline-none focus:border-bd-cta">
                                <p data-err="name" class="hidden text-xs text-red-400 mt-1.5"></p>
                            </div>
                            <div>
                                <label for="f-phone" class="block text-xs font-label uppercase text-bd-muted mb-2">Phone / WhatsApp Number *</label>
                                <input type="tel" id="f-phone" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="e.g. 081234567890" autocomplete="tel"
                                       class="w-full px-4 py-3 rounded-xl bg-bd-bg/90 border border-white/15 text-sm text-bd-text focus:outline-none focus:border-bd-cta">
                                <p data-err="phone" class="hidden text-xs text-red-400 mt-1.5"></p>
                            </div>
                            <div>
                                <label for="f-email" class="block text-xs font-label uppercase text-bd-muted mb-2">Email Address <span class="text-bd-muted">(Optional)</span></label>
                                <input type="email" id="f-email" name="customer_email" value="{{ old('customer_email') }}" placeholder="name@example.com" autocomplete="email"
                                       class="w-full px-4 py-3 rounded-xl bg-bd-bg/90 border border-white/15 text-sm text-bd-text focus:outline-none focus:border-bd-cta">
                                <p data-err="email" class="hidden text-xs text-red-400 mt-1.5"></p>
                            </div>
                        </div>

                        <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                            <div class="flex items-center gap-3">
                                <span class="font-label w-7 h-7 rounded-lg bg-bd-cta text-white text-xs font-bold flex items-center justify-center">B</span>
                                <h3 class="font-display text-xl font-bold text-bd-text">Receiving Method</h3>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <button type="button" id="tab-delivery" onclick="OrderPage.setFulfillment('delivery')"
                                        class="p-4 rounded-2xl border border-bd-gold bg-bd-select/60 text-left transition flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-bd-cta/40 flex items-center justify-center text-bd-gold shrink-0">🚚</div>
                                    <div>
                                        <p class="font-display text-sm font-bold text-bd-text">Courier Delivery</p>
                                        <p class="text-xs text-bd-muted mt-0.5">Pin your address on the map (+IDR {{ number_format($deliveryFee) }} flat cold-chain)</p>
                                    </div>
                                </button>
                                <button type="button" id="tab-pickup" onclick="OrderPage.setFulfillment('pickup')"
                                        class="p-4 rounded-2xl border border-white/15 bg-bd-bg/60 text-left transition flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-bd-gold shrink-0">🏪</div>
                                    <div>
                                        <p class="font-display text-sm font-bold text-bd-text">Self-Pickup</p>
                                        <p class="text-xs text-bd-muted mt-0.5">Collect at our kitchen (FREE shipping)</p>
                                    </div>
                                </button>
                            </div>

                            <div>
                                <label for="f-notes" class="block text-xs font-label uppercase text-bd-muted mb-2">Order Notes <span class="text-bd-muted">(Optional)</span></label>
                                <input type="text" id="f-notes" name="notes" value="{{ old('notes') }}" placeholder="e.g. Extra spicy dabu-dabu, send at 12 PM..."
                                       class="w-full px-4 py-3 rounded-xl bg-bd-bg/90 border border-white/15 text-sm text-bd-text focus:outline-none focus:border-bd-cta">
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT: map panels -->
                    <div class="lg:col-span-7 lg:sticky lg:top-28">

                        <!-- DELIVERY: pin point (GoFood-style) -->
                        <div id="delivery-panel" class="glass-card rounded-3xl p-6 sm:p-8 space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h3 class="font-display text-xl font-bold text-bd-text">Pin your delivery location</h3>
                                <span id="pin-status" class="font-label text-xs px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300">○ Move the map to set your pin</span>
                            </div>

                            <div id="map-search-wrap" class="relative">
                                <div class="flex gap-2">
                                    <input type="text" id="map-search" placeholder="Search a street, building or landmark..."
                                           class="flex-1 min-w-0 px-4 py-3 rounded-xl bg-bd-bg/90 border border-white/15 text-sm text-bd-text focus:outline-none focus:border-bd-cta">
                                    <button type="button" onclick="OrderPage.searchPlace()"
                                            class="px-5 rounded-xl bg-bd-cta hover:bg-bd-cta-hover text-white text-sm font-semibold transition">Search</button>
                                </div>
                                <div id="map-search-results" class="hidden absolute left-0 right-0 top-full mt-2 z-50 rounded-xl overflow-hidden bg-bd-card border border-white/15 shadow-2xl"></div>
                            </div>

                            <div class="relative isolate rounded-2xl overflow-hidden border border-white/15">
                                <div id="delivery-map" class="map-dark h-[360px] sm:h-[430px] w-full"></div>

                                <div id="center-pin" class="map-pin" aria-hidden="true">
                                    <div class="map-pin-shadow"></div>
                                    <div class="map-pin-inner">
                                        <svg viewBox="0 0 40 52" width="40" height="52"><path d="M20 1C9.5 1 1 9.2 1 19.5 1 33.5 20 51 20 51s19-17.5 19-31.5C39 9.2 30.5 1 20 1z" class="fill-bd-cta stroke-bd-gold" stroke-width="2"/><circle cx="20" cy="19.5" r="7" class="fill-bd-text"/></svg>
                                    </div>
                                </div>

                                <button type="button" id="btn-my-location" onclick="OrderPage.useMyLocation()"
                                        class="absolute bottom-3 left-3 z-[650] flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-bd-card/95 border border-white/20 text-xs font-semibold text-bd-text hover:border-bd-gold transition shadow-lg">
                                    <span class="text-bd-gold">◎</span> Use my location
                                </button>
                            </div>

                            <div class="p-4 rounded-2xl bg-bd-bg/70 border border-white/10">
                                <p class="font-label text-xs uppercase tracking-wider text-bd-gold mb-1">Detected address</p>
                                <p id="pin-detected" class="text-xs leading-relaxed text-bd-muted">Move the map so the pin sits on your door, or search above. We will fill in the address for you.</p>
                            </div>
                            <p data-err="pin" class="hidden text-xs text-red-400"></p>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
                                <div class="sm:col-span-1">
                                    <label for="input-delivery-city" class="block text-xs font-label uppercase text-bd-muted mb-2">City / Regency *</label>
                                    <input type="text" name="delivery_city" id="input-delivery-city" value="{{ old('delivery_city') }}" placeholder="e.g. Jakarta"
                                           class="w-full px-4 py-3 rounded-xl bg-bd-bg/90 border border-white/15 text-sm text-bd-text focus:outline-none focus:border-bd-cta">
                                    <p data-err="city" class="hidden text-xs text-red-400 mt-1.5"></p>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="input-address-detail" class="block text-xs font-label uppercase text-bd-muted mb-2">Landmark / Floor / Unit <span class="text-bd-muted">(Optional)</span></label>
                                    <input type="text" name="address_detail" id="input-address-detail" value="{{ old('address_detail') }}" placeholder="e.g. Blue gate, 2nd floor, next to the minimarket"
                                           class="w-full px-4 py-3 rounded-xl bg-bd-bg/90 border border-white/15 text-sm text-bd-text focus:outline-none focus:border-bd-cta">
                                </div>
                            </div>
                            <div>
                                <label for="input-delivery-address" class="block text-xs font-label uppercase text-bd-muted mb-2">Complete Delivery Address *</label>
                                <textarea name="delivery_address" id="input-delivery-address" rows="3" placeholder="Street name, house/unit number, district, postal code..."
                                          class="w-full px-4 py-3 rounded-xl bg-bd-bg/90 border border-white/15 text-sm text-bd-text focus:outline-none focus:border-bd-cta">{{ old('delivery_address') }}</textarea>
                                <p data-err="address" class="hidden text-xs text-red-400 mt-1.5"></p>
                            </div>
                        </div>

                        <!-- PICKUP: lokasi toko -->
                        <div id="pickup-panel" class="hidden glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h3 class="font-display text-xl font-bold text-bd-text">Our pickup point</h3>
                                <span class="font-label text-xs px-2.5 py-0.5 rounded-full bg-bd-cta text-white">FREE PICKUP</span>
                            </div>

                            <div class="relative isolate rounded-2xl overflow-hidden border border-white/15">
                                <div id="pickup-map" class="map-dark h-[320px] sm:h-[380px] w-full"></div>
                            </div>

                            <div class="p-5 rounded-2xl bg-bd-select/35 border border-bd-cta/50 space-y-1.5">
                                <p class="font-display text-base font-bold text-bd-text">{{ $shop['name'] }}</p>
                                <p class="text-xs text-bd-text leading-relaxed">{{ $shop['address'] }}</p>
                                <p class="font-label text-xs text-bd-gold">Open daily · {{ $shop['hours'] }}</p>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <a href="{{ $shopViewUrl }}" target="_blank" rel="noopener"
                                   class="px-4 py-2.5 rounded-xl bg-bd-cta hover:bg-bd-cta-hover text-white text-xs font-semibold transition">Open in Google Maps</a>
                                <a href="{{ $shopDirUrl }}" target="_blank" rel="noopener"
                                   class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-bd-text text-xs font-semibold transition">Get Directions</a>
                                <button type="button" onclick="OrderPage.copyShopAddress()"
                                        class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-bd-text text-xs font-semibold transition">Copy Address</button>
                            </div>
                            <p class="text-xs text-bd-gold">* Show your Order Code when your tracker status turns into "Ready for Pickup".</p>
                        </div>
                    </div>
                </div>

                <!-- Action bar -->
                <div class="mt-8 glass-card rounded-3xl p-5 sm:p-6 flex flex-wrap items-center justify-between gap-4">
                    <button type="button" onclick="OrderPage.goStep(1)"
                            class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-bd-text text-sm font-semibold transition">← Back to dishes</button>
                    <div class="text-right">
                        <p class="font-num text-xs text-bd-muted">Estimated total <span id="step2-total-note"></span></p>
                        <p id="step2-total" class="font-num text-xl font-bold text-bd-gold">IDR 0</p>
                    </div>
                    <button type="button" onclick="OrderPage.toPayment()"
                            class="px-7 py-3.5 rounded-2xl bg-gradient-to-r from-bd-cta to-bd-cta-deep border border-bd-gold/40 text-white font-display font-bold text-sm shadow-xl shadow-bd-cta/25 hover:scale-[1.01] transition">
                        Continue to Payment →
                    </button>
                </div>
            </div>

            <!-- =====================================================
                 STEP 3 — PAYMENT METHOD + FINAL SUMMARY
            ====================================================== -->
            <div id="step-3" class="wizard-step hidden">
                <div class="flex flex-wrap items-end justify-between gap-3 mb-6">
                    <div>
                        <h2 class="font-display text-2xl sm:text-3xl font-bold text-bd-text">Choose how you will pay</h2>
                        <p class="text-sm text-bd-muted mt-1">After you place the order, you get the account number / QRIS and can upload your receipt.</p>
                    </div>
                    <button type="button" onclick="OrderPage.goStep(2)" class="text-xs font-label text-bd-gold hover:text-bd-text transition">← Edit details</button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <div class="lg:col-span-7 space-y-6">
                        <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                            <h3 class="font-display text-xl font-bold text-bd-text">Payment Method</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach(['QRIS', 'BCA', 'MANDIRI', 'BRI', 'BNI'] as $method)
                                    <button type="button" onclick="OrderPage.selectPayment('{{ $method }}')" data-pay="{{ $method }}"
                                            class="pay-method-btn p-4 rounded-2xl border border-white/15 bg-bd-bg/60 text-center transition">
                                        <span class="font-label text-sm font-bold text-bd-text block">{{ $method }}</span>
                                        <span class="text-xs text-bd-muted block mt-0.5">{{ $method === 'QRIS' ? 'Scan QR' : 'Bank Transfer' }}</span>
                                    </button>
                                @endforeach
                            </div>
                            <p class="text-xs text-bd-muted">
                                Selected: <strong id="recap-method" class="text-bd-text">QRIS</strong>. Payment is verified manually by our team; once verified, your live order tracker unlocks automatically.
                            </p>
                        </div>

                        <div class="glass-card rounded-3xl p-6 sm:p-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-display text-xl font-bold text-bd-text">Receiving Details</h3>
                                <button type="button" onclick="OrderPage.goStep(2)" class="text-xs font-label text-bd-gold hover:text-bd-text transition">Edit</button>
                            </div>
                            <div id="recap-receiver"></div>
                        </div>
                    </div>

                    <div class="lg:col-span-5 lg:sticky lg:top-28">
                        <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                            <div class="flex items-center justify-between border-b border-white/10 pb-4">
                                <h3 class="font-display text-2xl font-bold text-bd-text">Order Summary</h3>
                                <button type="button" onclick="OrderPage.goStep(1)" class="text-xs font-label text-bd-gold hover:text-bd-text transition">Edit</button>
                            </div>

                            <div id="recap-items" class="max-h-72 overflow-y-auto pr-1"></div>

                            <div class="border-t border-white/10 pt-4 space-y-2.5 text-sm">
                                <div class="flex justify-between text-bd-muted">
                                    <span>Dishes Subtotal</span>
                                    <span id="recap-subtotal" class="font-num text-bd-text">IDR 0</span>
                                </div>
                                <div class="flex justify-between text-bd-muted">
                                    <span id="recap-shipping-label">Cold-Chain Delivery Fee</span>
                                    <span id="recap-shipping" class="font-num text-bd-text">IDR 0</span>
                                </div>
                                <div class="flex justify-between text-base font-bold text-bd-text pt-3 border-t border-white/10">
                                    <span>Estimated Total</span>
                                    <span id="recap-total" class="font-num text-xl text-bd-gold">IDR 0</span>
                                </div>
                                <p class="text-xs text-bd-muted leading-relaxed">
                                    A 3-digit unique code is added to your total when the order is created, so we can match your transfer. The exact amount is shown on the next screen.
                                </p>
                            </div>

                            <button type="submit" id="place-order-btn"
                                    class="w-full py-4 rounded-2xl bg-gradient-to-r from-bd-cta to-bd-cta-deep border border-bd-gold/40 text-white font-display font-bold text-base shadow-xl shadow-bd-cta/25 hover:scale-[1.01] transition disabled:opacity-60 disabled:cursor-wait">
                                Place Order & Get Payment Details →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- =========================================================
     MODAL: BAG (edit -> reconfirm)
========================================================== -->
<div id="bag-modal" class="hidden fixed inset-0 z-[70] flex items-end sm:items-center justify-center sm:p-6" role="dialog" aria-modal="true" aria-labelledby="bag-title">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="OrderPage.closeBag()"></div>

    <div class="modal-panel relative w-full sm:max-w-lg max-h-[92vh] flex flex-col rounded-t-3xl sm:rounded-3xl bg-bd-card border border-white/15 shadow-2xl">

        <!-- View 1: edit bag -->
        <div id="bag-view-edit" class="flex flex-col min-h-0">
            <div class="flex items-start justify-between p-6 pb-4 border-b border-white/10">
                <div>
                    <h3 id="bag-title" class="font-display text-2xl font-bold text-bd-text">Your Bag</h3>
                    <p id="bag-count-label" class="font-label text-xs text-bd-gold mt-0.5">0 items</p>
                </div>
                <button type="button" onclick="OrderPage.closeBag()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-bd-text" aria-label="Close">✕</button>
            </div>
            <div id="bag-items" class="px-6 py-4 overflow-y-auto space-y-3 min-h-[120px]"></div>
            <div class="p-6 pt-4 border-t border-white/10 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-bd-muted">Dishes subtotal</span>
                    <span id="bag-subtotal" class="font-num text-lg font-bold text-bd-gold">IDR 0</span>
                </div>
                <p class="text-xs text-bd-muted">Delivery fee is added in the next step, only if you choose delivery.</p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="OrderPage.closeBag()"
                            class="py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-bd-text text-sm font-semibold transition">Keep browsing</button>
                    <button type="button" id="bag-checkout-btn" onclick="OrderPage.bagToConfirm()"
                            class="py-3 rounded-xl bg-gradient-to-r from-bd-cta to-bd-cta-deep border border-bd-gold/40 text-white text-sm font-bold transition">Checkout →</button>
                </div>
            </div>
        </div>

        <!-- View 2: reconfirm -->
        <div id="bag-view-confirm" class="hidden flex flex-col min-h-0">
            <div class="p-6 pb-4 border-b border-white/10">
                <p class="font-label text-xs uppercase tracking-wider text-bd-gold">One last check</p>
                <h3 class="font-display text-2xl font-bold text-bd-text mt-1">Is your order correct?</h3>
                <p class="text-xs text-bd-muted mt-1">Please review your dishes before you fill in your details.</p>
            </div>
            <div id="confirm-items" class="px-6 py-3 overflow-y-auto"></div>
            <div class="p-6 pt-4 border-t border-white/10 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-bd-muted"><span id="confirm-count">0 items</span> · subtotal</span>
                    <span id="confirm-subtotal" class="font-num text-lg font-bold text-bd-gold">IDR 0</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" onclick="OrderPage.backToEdit()"
                            class="py-3 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-bd-text text-sm font-semibold transition">← Edit order</button>
                    <button type="button" onclick="OrderPage.confirmOrder()"
                            class="py-3 rounded-xl bg-gradient-to-r from-bd-cta to-bd-cta-deep border border-bd-gold/40 text-white text-sm font-bold transition">Yes, continue →</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================
     MODAL: DISH QUICK VIEW (foto besar)
========================================================== -->
<div id="dish-modal" class="hidden fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-8" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="OrderPage.closeDish()"></div>
    <div class="modal-panel relative w-full max-w-4xl max-h-[92vh] overflow-y-auto grid md:grid-cols-2 rounded-3xl overflow-hidden bg-bd-card border border-white/15 shadow-2xl">
        <button type="button" onclick="OrderPage.closeDish()" class="absolute top-3 right-3 z-10 w-9 h-9 rounded-xl bg-black/60 hover:bg-black/80 text-bd-text" aria-label="Close">✕</button>
        <img id="dish-modal-img" src="" alt="" class="w-full h-64 md:h-full object-cover">
        <div class="p-6 sm:p-8 flex flex-col gap-4">
            <div>
                <span id="dish-modal-cat" class="font-label text-xs px-2 py-0.5 rounded">HALAL</span>
                <h3 id="dish-modal-name" class="font-display text-2xl sm:text-3xl font-bold text-bd-text mt-3"></h3>
            </div>
            <p id="dish-modal-desc" class="text-sm text-bd-muted leading-relaxed"></p>
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-xl bg-bd-bg/70 border border-white/10">
                    <p class="font-label text-xs uppercase text-bd-muted">Portion</p>
                    <p id="dish-modal-weight" class="text-xs text-bd-text mt-1"></p>
                </div>
                <div class="p-3 rounded-xl bg-bd-bg/70 border border-white/10">
                    <p class="font-label text-xs uppercase text-bd-muted">Flavor</p>
                    <p id="dish-modal-spicy" class="text-xs text-bd-text mt-1"></p>
                </div>
            </div>
            <div class="mt-auto pt-2">
                <p id="dish-modal-price" class="font-num text-2xl font-bold text-bd-gold mb-3"></p>
                <div id="dish-modal-ctl" data-dish-ctl=""></div>
                <button type="button" onclick="OrderPage.closeDish()" class="w-full mt-3 py-2.5 rounded-xl text-xs font-semibold text-bd-muted hover:text-bd-text transition">Back to menu</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.ORDER_PAGE = {
        menus: @json($menus),
        shop: @json($shop),
        deliveryFee: {{ (int) $deliveryFee }},
        initialStep: {{ (int) $initialStep }},
        old: {
            fulfillment: @json($oldFulfillment),
            payment: @json($oldPayment)
        }
    };
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<script src="{{ asset('js/bd-map.js') }}"></script>
<script src="{{ asset('js/order-wizard.js') }}"></script>
@endpush
