<header class="sticky top-0 z-40 border-b border-white/10 bg-bd-bg/75 backdrop-blur-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-bd-cta to-bd-cta-deep p-[1px] shadow-lg shadow-bd-cta/20">
                <div class="w-full h-full bg-bd-bg rounded-[11px] flex items-center justify-center">
                    <span class="font-display text-lg font-bold text-bd-gold">BD</span>
                </div>
            </div>
            <div>
                <span class="font-display text-xl font-bold tracking-tight text-bd-text block leading-none">BAKU DAPA</span>
                <span class="font-label text-xs uppercase tracking-[0.2em] text-bd-muted block mt-1">Manado Culinary Lab</span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-bd-muted">
            <a href="{{ route('home') }}#promo" class="hover:text-bd-text transition">Promo</a>
            <a href="{{ route('order.create') }}" class="hover:text-bd-text transition text-bd-gold font-semibold">Order Page</a>
            <a href="{{ route('order.lookup') }}" class="hover:text-bd-text transition">Track Order</a>
            <a href="{{ route('admin.orders') }}" class="font-label text-xs px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-bd-gold hover:border-bd-gold/50 transition">
                Admin Panel
            </a>
        </nav>

        <!-- Right CTA (Bag hanya ada di halaman Order) -->
        <div class="flex items-center gap-3">
            <a href="{{ route('order.create') }}" class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-bd-cta to-bd-cta-deep border border-bd-gold/30 text-white shadow-lg shadow-bd-cta/20 hover:brightness-110 transition">
                Order Now
            </a>
        </div>
    </div>
</header>