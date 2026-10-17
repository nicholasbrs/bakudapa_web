@extends('layouts.app')

@section('content')

<!-- ==========================================
     1. HERO SECTION
=========================================== -->
<section class="relative pt-10 pb-20 lg:pt-16 lg:pb-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Column: Copywriting & CTA -->
            <div class="lg:col-span-7 space-y-6 reveal-on-scroll">
                <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full bg-[#0D140F]/65 backdrop-blur-md border border-[#4E8752]/40">
                    <span class="w-2 h-2 rounded-full bg-[#4ADE80] animate-pulse"></span>
                    <span class="font-mono-ai text-[11px] uppercase tracking-[0.15em] text-[#A7F3D0] font-medium">
                        100% Authentic Minahasan Spices • Vacuum-Sealed Fresh
                    </span>
                </div>

                <!-- Headline in English -->
                <h1 class="font-display-ai text-4xl sm:text-5xl lg:text-6xl font-bold leading-[1.08] tracking-tight text-white">
                    Authentic Manado Flavors,
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#86EFAC] via-[#E2C07D] to-[#4ADE80]">
                        Delivered Fresh to Your Dining Table.
                    </span>
                </h1>

                <p class="text-base sm:text-lg text-[#94A898] max-w-2xl leading-relaxed">
                    Experience the bold, smoky richness of <strong class="text-white font-medium">Cakalang Fufu Rabe Rica</strong> and <strong class="text-white font-medium">Ayam Woku Belanga</strong> crafted by native Manadonese chefs at <strong class="text-white font-medium">Baku Dapa</strong>. Vacuum-sealed immediately after cooking—ready to serve in 5 minutes anywhere across the country.
                </p>

                <!-- Primary CTA Buttons -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="{{ route('order.create') }}" class="px-7 py-4 rounded-2xl bg-gradient-to-r from-[#4E8752] to-[#1B4323] border border-[#86EFAC]/40 text-white font-semibold text-base shadow-xl shadow-[#4E8752]/25 hover:scale-[1.02] transition flex items-center gap-3">
                        <span>Order Now</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="#shipping" class="bg-[#0D140F]/65 backdrop-blur-md border border-white/15 px-6 py-4 rounded-2xl text-sm font-semibold text-[#F2F7F4] hover:border-[#4E8752] transition">
                        Check Delivery Coverage
                    </a>
                </div>

                <!-- Trust Metrics -->
                <div class="pt-6 grid grid-cols-3 gap-4 border-t border-white/10 max-w-lg">
                    <div>
                        <p class="font-mono-ai text-2xl sm:text-3xl font-bold text-[#86EFAC]">4.9/5.0</p>
                        <p class="text-xs text-[#94A898] mt-0.5">2,400+ Verified Reviews</p>
                    </div>
                    <div>
                        <p class="font-mono-ai text-2xl sm:text-3xl font-bold text-[#E2C07D]">38+ Cities</p>
                        <p class="text-xs text-[#94A898] mt-0.5">Cold-Chain Network</p>
                    </div>
                    <div>
                        <p class="font-mono-ai text-2xl sm:text-3xl font-bold text-white">100% Split</p>
                        <p class="text-xs text-[#94A898] mt-0.5">Halal & Non-Halal Kitchens</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Best-Seller Interactive Banner -->
            <div class="lg:col-span-5 reveal-on-scroll delay-200">
                <div class="glass-card rounded-3xl p-4 sm:p-5 relative overflow-hidden">
                    <!-- Top Dish Switcher -->
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <span class="font-mono-ai text-[11px] uppercase tracking-wider text-[#86EFAC] font-semibold flex items-center gap-1.5">
                            ★ #1 Best-Selling Dish
                        </span>
                        <div class="flex gap-1.5">
                            <button type="button" onclick="switchHeroDish('cakalang')" id="btn-hero-cakalang" class="px-3 py-1 rounded-lg text-xs font-semibold bg-[#4E8752] text-white transition">
                                Cakalang Rabe Rica
                            </button>
                            <button type="button" onclick="switchHeroDish('woku')" id="btn-hero-woku" class="px-3 py-1 rounded-lg text-xs font-semibold bg-white/5 text-[#94A898] hover:text-white transition">
                                Ayam Woku
                            </button>
                        </div>
                    </div>

                    <!-- Dish Image Banner -->
                    <div class="relative rounded-2xl overflow-hidden aspect-[4/3] mb-4 border border-white/10">
                        <img id="hero-dish-img" 
                             src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1000&q=85" 
                             alt="Cakalang Fufu Rabe Rica" 
                             class="w-full h-full object-cover transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#050806] via-transparent to-transparent"></div>

                        <!-- Floating Badge -->
                        <div class="font-mono-ai absolute top-3 left-3 px-3 py-1 rounded-full bg-[#050806]/85 backdrop-blur-md border border-[#4ADE80]/40 text-[11px] font-semibold text-[#4ADE80]">
                            ✓ HALAL CERTIFIED • Vacuum Sealed
                        </div>

                        <div class="absolute bottom-3 left-3 right-3 flex items-end justify-between">
                            <div>
                                <p id="hero-dish-title" class="font-display-ai text-2xl font-bold text-white">Cakalang Fufu Rabe Rica</p>
                                <p id="hero-dish-sub" class="text-xs text-[#94A898]">Wood-Smoked Skipjack Tuna • Red Chili & Lime Leaves</p>
                            </div>
                            <span id="hero-dish-price" class="font-mono-ai px-3 py-1.5 rounded-xl bg-[#4E8752] text-white font-bold text-sm">
                                IDR 95K
                            </span>
                        </div>
                    </div>

                    <!-- Quick Add From Hero -->
                    <div class="flex items-center justify-between gap-3 pt-1">
                        <div class="text-xs text-[#94A898]">
                            Pack Size <strong class="text-white">350g (2-3 Servings)</strong> • Lasts 30 days in freezer
                        </div>
                        <button type="button" id="hero-quick-add-btn" onclick="quickOrder(1, 'Cakalang Fufu Rabe Rica', 95000, 'HALAL')" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-[#4E8752] text-white text-xs font-semibold shrink-0 transition border border-white/15">
                            Order This →
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ==========================================
     2. WHY US / KEUNGGULAN KAMI (4 ICONS)
=========================================== -->
<section id="why-us" class="py-16 lg:py-24 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12 reveal-on-scroll">
            <span class="font-mono-ai text-xs uppercase tracking-[0.2em] text-[#86EFAC] font-semibold">Why Choose Baku Dapa</span>
            <h2 class="font-display-ai text-3xl sm:text-4xl font-bold text-white mt-2">
                Engineered for Peak Freshness & Authentic Taste
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: 100% Authentic Spices -->
            <div class="glass-card rounded-2xl p-6 reveal-on-scroll delay-100">
                <div class="w-12 h-12 rounded-xl bg-[#1B4323]/60 border border-[#4E8752]/40 flex items-center justify-center mb-5 text-[#86EFAC]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h3 class="font-display-ai text-lg font-bold text-white mb-2">100% Native Spices</h3>
                <p class="text-sm text-[#94A898] leading-relaxed">
                    Crafted with curly rica chilies, woku herbs, lemongrass, kemangi basil, and smoked fish sourced directly from North Sulawesi—zero artificial preservatives.
                </p>
            </div>

            <!-- Card 2: Hygienic Vacuum Packaging -->
            <div class="glass-card rounded-2xl p-6 reveal-on-scroll delay-200">
                <div class="w-12 h-12 rounded-xl bg-[#1B4323]/60 border border-[#4E8752]/40 flex items-center justify-center mb-5 text-[#86EFAC]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="font-display-ai text-lg font-bold text-white mb-2">Hygienic Vacuum Seal</h3>
                <p class="text-sm text-[#94A898] leading-relaxed">
                    Food-grade multi-layer vacuum technology locks in moisture, smoky aroma, and rich spice oils while ensuring 100% leak-proof transit.
                </p>
            </div>

            <!-- Card 3: Nationwide Cold-Chain Shipping -->
            <div class="glass-card rounded-2xl p-6 reveal-on-scroll delay-300">
                <div class="w-12 h-12 rounded-xl bg-[#1B4323]/60 border border-[#4E8752]/40 flex items-center justify-center mb-5 text-[#86EFAC]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-display-ai text-lg font-bold text-white mb-2">Nationwide Delivery</h3>
                <p class="text-sm text-[#94A898] leading-relaxed">
                    Integrated with Cold-Chain logistics (Paxel, JNE YES, Priority Air Cargo) packed with thermal aluminum liners and ice gel packs to major cities.
                </p>
            </div>

            <!-- Card 4: Clear Halal / Non-Halal Separation -->
            <div class="glass-card rounded-2xl p-6 reveal-on-scroll delay-400">
                <div class="w-12 h-12 rounded-xl bg-[#1B4323]/60 border border-[#4E8752]/40 flex items-center justify-center mb-5 text-[#E2C07D]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="font-display-ai text-lg font-bold text-white mb-2">Strict Halal / Non-Halal</h3>
                <p class="text-sm text-[#94A898] leading-relaxed">
                    Complete transparency: cookware, kitchen zones, and security packaging seals are 100% separated between <strong class="text-[#4ADE80]">Halal (Green Seal)</strong> and <strong class="text-[#F59E0B]">Non-Halal (Red Seal)</strong>.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     3. PROMO & PERKS (menggantikan section menu)
     Menu lengkap sekarang hanya ada di halaman Order.
=========================================== -->
<section id="promo" class="py-16 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-10 reveal-on-scroll">
            <span class="font-mono-ai text-xs uppercase tracking-[0.2em] text-[#E2C07D] font-semibold">Current Offers</span>
            <h2 class="font-display-ai text-3xl sm:text-4xl font-bold text-white mt-2">
                Perks That Come With Every Order
            </h2>
            <p class="text-sm text-[#94A898] mt-2 max-w-2xl">
                Pick your dishes on the order page, pin your location on the map, and track your order live from kitchen to doorstep.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Main banner -->
            <a href="{{ route('order.create') }}" class="group relative lg:col-span-2 min-h-[320px] rounded-3xl overflow-hidden border border-white/10 flex reveal-on-scroll">
                <img src="https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=1400&q=80"
                     alt="Ayam Woku Belanga" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
                <div class="absolute inset-0 bg-gradient-to-r from-[#050806] via-[#050806]/80 to-transparent"></div>
                <div class="relative p-8 sm:p-10 flex flex-col justify-end max-w-xl space-y-3">
                    <span class="self-start font-mono-ai text-[11px] uppercase tracking-wider px-3 py-1 rounded-full bg-[#4E8752] text-white font-semibold">
                        Free Shipping
                    </span>
                    <h3 class="font-display-ai text-3xl sm:text-4xl font-bold text-white leading-tight">
                        Pick Up at Our Kitchen, Pay Zero Delivery Fee
                    </h3>
                    <p class="text-sm text-[#F2F7F4]/80 leading-relaxed">
                        Choose self-pickup at checkout and skip the delivery fee. We show our exact pin-point on the map so it is easy to find.
                    </p>
                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-[#86EFAC] pt-1">
                        Order for Pickup
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </span>
                </div>
            </a>

            <!-- Side banners -->
            <div class="grid grid-rows-2 gap-6">
                <a href="{{ route('order.create') }}" class="group relative rounded-3xl overflow-hidden border border-white/10 min-h-[150px] flex reveal-on-scroll delay-100">
                    <img src="https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80"
                         alt="Cakalang Fufu" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#050806] via-[#050806]/70 to-[#050806]/20"></div>
                    <div class="relative p-6 flex flex-col justify-end">
                        <span class="font-mono-ai text-[10px] uppercase tracking-wider text-[#E2C07D]">Cold-Chain Delivery</span>
                        <h3 class="font-display-ai text-xl font-bold text-white mt-1">
                            Flat IDR {{ number_format(config('bakudapa.delivery_fee')) }} Delivery
                        </h3>
                        <p class="text-xs text-[#94A898] mt-1">Vacuum-sealed with thermal foil and ice gel.</p>
                    </div>
                </a>

                <a href="{{ route('order.create') }}" class="group relative rounded-3xl overflow-hidden border border-white/10 min-h-[150px] flex reveal-on-scroll delay-200">
                    <img src="https://images.unsplash.com/photo-1514944298352-f4d732142f74?auto=format&fit=crop&w=800&q=80"
                         alt="Crispy Pork Tore Rica" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#050806] via-[#050806]/70 to-[#050806]/20"></div>
                    <div class="relative p-6 flex flex-col justify-end">
                        <span class="font-mono-ai text-[10px] uppercase tracking-wider text-[#4ADE80]">Two Separate Kitchens</span>
                        <h3 class="font-display-ai text-xl font-bold text-white mt-1">Halal &amp; Non-Halal, Clearly Sealed</h3>
                        <p class="text-xs text-[#94A898] mt-1">Every dish is labeled before you add it to your bag.</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- How it works -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach([
                ['01', 'Pick your dishes', 'Browse the full menu on the order page.'],
                ['02', 'Add details & pin location', 'Drop a pin on the map, or choose pickup.'],
                ['03', 'Pay & track live', 'Upload your receipt and follow every stage.'],
            ] as $step)
            <div class="glass-card rounded-2xl p-5 flex items-start gap-4 reveal-on-scroll">
                <span class="font-mono-ai w-8 h-8 rounded-lg bg-[#4E8752] text-white text-xs font-bold flex items-center justify-center shrink-0">{{ $step[0] }}</span>
                <div>
                    <p class="font-display-ai text-sm font-bold text-white">{{ $step[1] }}</p>
                    <p class="text-xs text-[#94A898] mt-0.5">{{ $step[2] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ==========================================
     4. DELIVERY COVERAGE CHECKER
=========================================== -->
<section id="shipping" class="py-16 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="glass-card rounded-3xl p-6 sm:p-10 lg:p-12 reveal-on-scroll">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <div class="lg:col-span-5 space-y-4">
                    <span class="font-mono-ai text-xs uppercase tracking-[0.2em] text-[#86EFAC] font-semibold">Nationwide Cold-Chain</span>
                    <h2 class="font-display-ai text-3xl sm:text-4xl font-bold text-white">
                        Check Delivery Coverage to Your City
                    </h2>
                    <p class="text-sm text-[#94A898] leading-relaxed">
                        We dispatch daily from the <strong>Baku Dapa</strong> central kitchen using <strong>Double Vacuum + Thermal Foil + Ice Gel Packs</strong> so your dishes arrive fresh and chilled across islands.
                    </p>

                    <!-- Quick City Search Input -->
                    <div class="pt-2">
                        <label for="city-search-input" class="font-mono-ai block text-xs font-semibold uppercase tracking-wider text-[#E2C07D] mb-2">
                            Enter Your City Name:
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="city-search-input" placeholder="e.g. Surabaya, Bandung, Bali, Medan..."
                                   class="w-full px-4 py-3 rounded-xl bg-[#050806]/90 border border-white/15 text-sm text-white placeholder-[#94A898]/60 focus:outline-none focus:border-[#4E8752]">
                            <button type="button" onclick="checkCityCoverage()" class="px-5 py-3 rounded-xl bg-[#4E8752] hover:bg-[#3d6e40] text-white text-xs font-bold shrink-0 transition">
                                Check City
                            </button>
                        </div>
                        <p id="city-search-result" class="text-xs text-[#86EFAC] mt-2.5 hidden"></p>
                    </div>
                </div>

                <!-- Shipping Zone Cards -->
                <div class="lg:col-span-7 grid grid-cols-1 gap-4">
                    @foreach($shippingZones as $key => $zone)
                    <div class="p-5 rounded-2xl bg-[#050806]/70 border border-white/10 hover:border-[#4E8752]/50 transition">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <h3 class="font-display-ai text-base font-bold text-white">{{ $zone['region'] }}</h3>
                            <span class="font-mono-ai px-3 py-1 rounded-full bg-[#1B4323]/70 border border-[#4E8752]/50 text-xs font-semibold text-[#86EFAC]">
                                {{ $zone['eta'] }}
                            </span>
                        </div>
                        <p class="text-xs text-[#94A898] mb-1">
                            <strong class="text-white">Supported Couriers:</strong> {{ $zone['couriers'] }}
                        </p>
                        <p class="text-xs text-[#94A898]">
                            <strong class="text-[#E2C07D]">Packaging Standard:</strong> {{ $zone['packaging'] }} — <em>{{ $zone['guarantee'] }}</em>
                        </p>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ==========================================
     5. INTERCITY CUSTOMER TESTIMONIALS
=========================================== -->
<section id="testimonials" class="py-16 lg:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12 reveal-on-scroll">
            <span class="font-mono-ai text-xs uppercase tracking-[0.2em] text-[#E2C07D] font-semibold">Verified Intercity Arrivals</span>
            <h2 class="font-display-ai text-3xl sm:text-4xl font-bold text-white mt-2">
                How Our Food Arrives Across Different Cities
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($testimonials as $testi)
            <div class="glass-card rounded-3xl p-6 flex flex-col justify-between space-y-5 reveal-on-scroll">
                <div class="space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-amber-400 text-sm tracking-widest">★★★★★</span>
                        <span class="font-mono-ai px-2.5 py-1 rounded-full bg-[#1B4323]/70 border border-[#4E8752]/40 text-[10px] font-semibold text-[#86EFAC]">
                            {{ $testi['badge'] }}
                        </span>
                    </div>

                    <p class="text-sm text-[#F2F7F4] leading-relaxed">
                        "{{ $testi['comment'] }}"
                    </p>
                </div>

                <div class="pt-4 border-t border-white/10">
                    <p class="font-display-ai text-sm font-bold text-white">{{ $testi['name'] }}</p>
                    <p class="text-xs text-[#94A898]">{{ $testi['city'] }} • via {{ $testi['courier'] }}</p>
                    <p class="font-mono-ai text-[11px] text-[#E2C07D] mt-1">Ordered: {{ $testi['product'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection