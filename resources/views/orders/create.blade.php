@extends('layouts.app')

@section('title', 'Order & Checkout — BAKU DAPA')

@section('content')
<section class="py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-10">
            <span class="font-mono-ai text-xs uppercase tracking-[0.2em] text-[#86EFAC] font-semibold">Direct Web Ordering</span>
            <h1 class="font-display-ai text-3xl sm:text-5xl font-bold text-white mt-1">
                Build Your Order & Checkout
            </h1>
            <p class="text-sm text-[#94A898] mt-2">
                Select your dishes, choose Delivery or Self-Pickup, and complete your manual payment with live order status tracking.
            </p>
        </div>

        @if(\$errors->any())
            <div class="glass-card border-red-500/50 p-4 rounded-2xl mb-8 text-sm text-red-300">
                <p class="font-bold text-red-400 mb-1">Please check your form:</p>
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors->all() as$err): ?>
                        <li>{{ \$err }}</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        @endif

        <form action="{{ route('order.store') }}" method="POST" id="checkout-form" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            @csrf
            <input type="hidden" name="cart_json" id="cart-json-input">
            <input type="hidden" name="fulfillment_type" id="fulfillment-type-input" value="{{ old('fulfillment_type', 'delivery') }}">
            <input type="hidden" name="payment_method" id="payment-method-input" value="{{ old('payment_method', 'QRIS') }}">

            <div class="lg:col-span-7 space-y-8">

                <!-- STEP 1: SELECT DISHES -->
                <div class="glass-card rounded-3xl p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <span class="font-mono-ai w-7 h-7 rounded-lg bg-[#4E8752] text-white text-xs font-bold flex items-center justify-center">01</span>
                            <h2 class="font-display-ai text-xl font-bold text-white">Select Dishes</h2>
                        </div>
                        <span class="font-mono-ai text-xs text-[#86EFAC]">Click + to add to your order</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($menus as$menu): ?>
                        <div class="p-3.5 rounded-2xl bg-[#050806]/70 border border-white/10 hover:border-[#4E8752]/50 transition flex items-center gap-3.5">
                            <img src="{{ $menu['image'] }}" alt="{{ $menu['name'] }}" class="w-16 h-16 rounded-xl object-cover shrink-0">
                            <div class="flex-1 min-w-0">
                                <span class="font-mono-ai text-[10px] px-2 py-0.5 rounded {{ \$menu['category'] === 'HALAL' ? 'bg-[#1B4323] text-[#4ADE80]' : 'bg-amber-500/20 text-amber-300' }}">
                                    {{ \$menu['category'] }}
                                </span>
                                <h3 class="font-display-ai text-sm font-bold text-white truncate mt-1">{{ \$menu['name'] }}</h3>
                                <div class="flex items-center justify-between mt-1.5">
                                    <span class="font-mono-ai text-xs text-[#E2C07D]">IDR {{ number_format(\$menu['price']) }}</span>
                                    <button type="button"
                                            onclick="addToCart({{ $menu['id'] }}, '{{$menu['name'] }}', {{ $menu['price'] }}, '{{$menu['category'] }}', false)"
                                            class="px-2.5 py-1 rounded-lg bg-[#4E8752] hover:bg-[#3d6e40] text-white font-mono-ai text-xs font-bold transition">
                                        + Add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- STEP 2: CUSTOMER DETAILS -->
                <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                    <div class="flex items-center gap-3">
                        <span class="font-mono-ai w-7 h-7 rounded-lg bg-[#4E8752] text-white text-xs font-bold flex items-center justify-center">02</span>
                        <h2 class="font-display-ai text-xl font-bold text-white">Customer Information</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono-ai uppercase text-[#94A898] mb-2">Full Name *</label>
                            <input type="text" name="customer_name" value="{{ old('customer_name') }}" required placeholder="e.g. Nicholas Barus"
                                   class="w-full px-4 py-3 rounded-xl bg-[#050806]/90 border border-white/15 text-sm text-white focus:outline-none focus:border-[#4E8752]">
                        </div>
                        <div>
                            <label class="block text-xs font-mono-ai uppercase text-[#94A898] mb-2">Phone / WhatsApp Number *</label>
                            <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required placeholder="e.g. 081234567890"
                                   class="w-full px-4 py-3 rounded-xl bg-[#050806]/90 border border-white/15 text-sm text-white focus:outline-none focus:border-[#4E8752]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-mono-ai uppercase text-[#94A898] mb-2">Email Address <span class="text-white/40">(Optional)</span></label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="name@example.com"
                               class="w-full px-4 py-3 rounded-xl bg-[#050806]/90 border border-white/15 text-sm text-white focus:outline-none focus:border-[#4E8752]">
                    </div>
                </div>

                <!-- STEP 3: FULFILLMENT METHOD -->
                <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                    <div class="flex items-center gap-3">
                        <span class="font-mono-ai w-7 h-7 rounded-lg bg-[#4E8752] text-white text-xs font-bold flex items-center justify-center">03</span>
                        <h2 class="font-display-ai text-xl font-bold text-white">Receiving Method (Delivery or Pickup)</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" id="tab-delivery" onclick="setFulfillment('delivery')"
                                class="p-4 rounded-2xl border border-[#86EFAC] bg-[#1B4323]/60 text-left transition flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#4E8752]/40 flex items-center justify-center text-[#86EFAC] shrink-0">
                                🚚
                            </div>
                            <div>
                                <p class="font-display-ai text-sm font-bold text-white">Courier Delivery</p>
                                <p class="text-xs text-[#94A898] mt-0.5">Sent to your doorstep (+IDR 25,000 flat cold-chain)</p>
                            </div>
                        </button>

                        <button type="button" id="tab-pickup" onclick="setFulfillment('pickup')"
                                class="p-4 rounded-2xl border border-white/15 bg-[#050806]/60 text-left transition flex items-start gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-[#E2C07D] shrink-0">
                                🏪
                            </div>
                            <div>
                                <p class="font-display-ai text-sm font-bold text-white">Self-Pickup at Kitchen</p>
                                <p class="text-xs text-[#94A898] mt-0.5">Pick up directly at Baku Dapa (FREE Shipping)</p>
                            </div>
                        </button>
                    </div>

                    <div id="delivery-fields" class="space-y-4 pt-2">
                        <div>
                            <label class="block text-xs font-mono-ai uppercase text-[#94A898] mb-2">City / Regency *</label>
                            <input type="text" name="delivery_city" id="input-delivery-city" value="{{ old('delivery_city') }}" placeholder="e.g. South Tangerang, Jakarta, Surabaya, Bandung"
                                   class="w-full px-4 py-3 rounded-xl bg-[#050806]/90 border border-white/15 text-sm text-white focus:outline-none focus:border-[#4E8752]">
                        </div>
                        <div>
                            <label class="block text-xs font-mono-ai uppercase text-[#94A898] mb-2">Complete Delivery Address *</label>
                            <textarea name="delivery_address" id="input-delivery-address" rows="3" placeholder="Street name, house/unit number, district, postal code..."
                                      class="w-full px-4 py-3 rounded-xl bg-[#050806]/90 border border-white/15 text-sm text-white focus:outline-none focus:border-[#4E8752]">{{ old('delivery_address') }}</textarea>
                        </div>
                    </div>

                    <div id="pickup-fields" class="hidden pt-2">
                        <div class="p-5 rounded-2xl bg-[#1B4323]/35 border border-[#4E8752]/50 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-mono-ai text-xs uppercase tracking-wider text-[#86EFAC] font-semibold">📍 Baku Dapa Pickup Point (Placeholder)</span>
                                <span class="font-mono-ai text-xs px-2.5 py-0.5 rounded-full bg-[#4E8752] text-white">FREE PICKUP</span>
                            </div>
                            <p class="font-display-ai text-base font-bold text-white">Baku Dapa Central Kitchen & Experience Hub</p>
                            <p class="text-xs text-[#F2F7F4] leading-relaxed">
                                Jl. Sam Ratulangi No. 88, Menteng, Central Jakarta 10350 (Placeholder Address — Operating Hours: 09:00 - 20:00 WIB)
                            </p>
                            <p class="text-[11px] text-[#E2C07D]">
                                * Show your Order Code when our tracker status turns into "Ready for Pickup".
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-mono-ai uppercase text-[#94A898] mb-2">Order Notes <span class="text-white/40">(Optional)</span></label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="e.g. Extra spicy dabu-dabu, send at 12 PM, separate chili..."
                               class="w-full px-4 py-3 rounded-xl bg-[#050806]/90 border border-white/15 text-sm text-white focus:outline-none focus:border-[#4E8752]">
                    </div>
                </div>

                <!-- STEP 4: PAYMENT METHOD -->
                <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-5">
                    <div class="flex items-center gap-3">
                        <span class="font-mono-ai w-7 h-7 rounded-lg bg-[#4E8752] text-white text-xs font-bold flex items-center justify-center">04</span>
                        <h2 class="font-display-ai text-xl font-bold text-white">Select Payment Method</h2>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        <?php 
                            \$first = true;
                            foreach (\$paymentMethods as $code =>$method): 
                        ?>
                        <button type="button" onclick="selectPayment('{{ $code }}')" data-pay="{{ $code }}"
                                class="pay-method-btn p-3.5 rounded-2xl border {{ \$first ? 'border-[#86EFAC] bg-[#1B4323]/60' : 'border-white/15 bg-[#050806]/60' }} text-center transition">
                            <span class="font-mono-ai text-sm font-bold text-white block">{{ \$code }}</span>
                            <span class="text-[10px] text-[#94A898] block mt-0.5">{{ \$method['type'] === 'qris' ? 'Scan QR' : 'Bank Transfer' }}</span>
                        </button>
                        <?php 
                            \$first = false;
                            endforeach; 
                        ?>
                    </div>
                    <p class="text-xs text-[#94A898]">
                        * Account number / QRIS code and receipt upload dropzone will be shown immediately on the next screen after you lock your order.
                    </p>
                </div>

            </div>

            <!-- RIGHT COLUMN: SUMMARY -->
            <div class="lg:col-span-5 lg:sticky lg:top-28">
                <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-white/10 pb-4">
                        <h2 class="font-display-ai text-2xl font-bold text-white">Order Summary</h2>
                        <span id="checkout-item-count" class="font-mono-ai text-xs px-3 py-1 rounded-full bg-[#1B4323] text-[#86EFAC]">0 Items</span>
                    </div>

                    <div id="checkout-items-list" class="space-y-3 max-h-80 overflow-y-auto pr-1"></div>

                    <div class="border-t border-white/10 pt-4 space-y-2.5 text-sm">
                        <div class="flex justify-between text-[#94A898]">
                            <span>Dishes Subtotal</span>
                            <span id="summary-subtotal" class="font-mono-ai text-white">IDR 0</span>
                        </div>
                        <div class="flex justify-between text-[#94A898]">
                            <span id="summary-shipping-label">Cold-Chain Delivery Fee</span>
                            <span id="summary-shipping" class="font-mono-ai text-white">IDR 25,000</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-white pt-3 border-t border-white/10">
                            <span>Estimated Total</span>
                            <span id="summary-grand-total" class="font-mono-ai text-xl text-[#86EFAC]">IDR 0</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 rounded-2xl bg-gradient-to-r from-[#4E8752] to-[#1B4323] border border-[#86EFAC]/40 text-white font-display-ai font-bold text-base shadow-xl shadow-[#4E8752]/25 hover:scale-[1.01] transition">
                        Place Order & Proceed to Payment →
                    </button>
                </div>
            </div>

        </form>
    </div>
</section>

@push('scripts')
<script>
    let currentFulfillment = '{{ old('fulfillment_type', 'delivery') }}';

    function setFulfillment(type) {
        currentFulfillment = type;
        document.getElementById('fulfillment-type-input').value = type;

        const tabDelivery = document.getElementById('tab-delivery');
        const tabPickup = document.getElementById('tab-pickup');
        const deliveryFields = document.getElementById('delivery-fields');
        const pickupFields = document.getElementById('pickup-fields');

        if (type === 'delivery') {
            tabDelivery.className = 'p-4 rounded-2xl border border-[#86EFAC] bg-[#1B4323]/60 text-left transition flex items-start gap-3';
            tabPickup.className = 'p-4 rounded-2xl border border-white/15 bg-[#050806]/60 text-left transition flex items-start gap-3';
            deliveryFields.classList.remove('hidden');
            pickupFields.classList.add('hidden');
        } else {
            tabPickup.className = 'p-4 rounded-2xl border border-[#86EFAC] bg-[#1B4323]/60 text-left transition flex items-start gap-3';
            tabDelivery.className = 'p-4 rounded-2xl border border-white/15 bg-[#050806]/60 text-left transition flex items-start gap-3';
            deliveryFields.classList.add('hidden');
            pickupFields.classList.remove('hidden');
        }
        syncCheckoutPage();
    }

    function selectPayment(method) {
        document.getElementById('payment-method-input').value = method;
        document.querySelectorAll('.pay-method-btn').forEach(btn => {
            if (btn.dataset.pay === method) {
                btn.className = 'pay-method-btn p-3.5 rounded-2xl border border-[#86EFAC] bg-[#1B4323]/60 text-center transition';
            } else {
                btn.className = 'pay-method-btn p-3.5 rounded-2xl border border-white/15 bg-[#050806]/60 text-center transition';
            }
        });
    }

    window.syncCheckoutPage = function() {
        const listEl = document.getElementById('checkout-items-list');
        const countEl = document.getElementById('checkout-item-count');
        const subEl = document.getElementById('summary-subtotal');
        const shipEl = document.getElementById('summary-shipping');
        const grandEl = document.getElementById('summary-grand-total');
        const jsonInput = document.getElementById('cart-json-input');

        jsonInput.value = JSON.stringify(cart);

        const totalQty = cart.reduce((s, i) => s + i.qty, 0);
        const subtotal = cart.reduce((s, i) => s + (i.price * i.qty), 0);
        const shipping = (currentFulfillment === 'delivery' && subtotal > 0) ? 25000 : 0;

        countEl.textContent = `${totalQty} Items`;
        subEl.textContent = 'IDR ' + subtotal.toLocaleString('en-US');
        shipEl.textContent = shipping === 0 ? 'FREE' : 'IDR ' + shipping.toLocaleString('en-US');
        grandEl.textContent = 'IDR ' + (subtotal + shipping).toLocaleString('en-US');

        if (cart.length === 0) {
            listEl.innerHTML = `<div class="p-6 rounded-2xl bg-[#050806]/60 border border-white/10 text-center text-xs text-[#94A898]">No dishes selected yet. Click <strong>+ Add</strong> on the dishes on the left.</div>`;
            return;
        }

        listEl.innerHTML = cart.map(item => `
            <div class="p-3.5 rounded-2xl bg-[#050806]/80 border border-white/10 flex items-center justify-between gap-3">
                <div>
                    <span class="font-mono-ai text-[10px] text-[#86EFAC]">${item.category}</span>
                    <h4 class="font-display-ai text-sm font-bold text-white">${item.name}</h4>
                    <p class="font-mono-ai text-xs text-[#94A898]">IDR ${(item.price * item.qty).toLocaleString('en-US')}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="updateCartQty(${item.id}, -1)" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs">-</button>
                    <span class="font-mono-ai text-xs w-5 text-center">${item.qty}</span>
                    <button type="button" onclick="updateCartQty(${item.id}, 1)" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs">+</button>
                </div>
            </div>
        `).join('');
    };

    document.addEventListener('DOMContentLoaded', () => {
        setFulfillment(currentFulfillment);
        syncCheckoutPage();
    });
</script>
@endpush
@endsection