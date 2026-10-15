<header class="sticky top-0 z-40 border-b border-white/10 bg-[#050806]/75 backdrop-blur-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#4E8752] to-[#1B4323] p-[1px] shadow-lg shadow-[#4E8752]/20">
                <div class="w-full h-full bg-[#050806] rounded-[11px] flex items-center justify-center">
                    <span class="font-display-ai text-lg font-bold text-[#86EFAC]">BD</span>
                </div>
            </div>
            <div>
                <span class="font-display-ai text-xl font-bold tracking-tight text-white block leading-none">BAKU DAPA</span>
                <span class="font-mono-ai text-[10px] uppercase tracking-[0.2em] text-[#94A898] block mt-1">Manado Culinary Lab</span>
            </div>
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-[#94A898]">
            <a href="{{ route('home') }}#menu" class="hover:text-white transition">Menu</a>
            <a href="{{ route('order.create') }}" class="hover:text-white transition text-[#86EFAC] font-semibold">Order Page</a>
            <a href="{{ route('order.lookup') }}" class="hover:text-white transition">Track Order</a>
            <a href="{{ route('admin.orders') }}" class="font-mono-ai text-xs px-3 py-1.5 rounded-lg bg-white/5 border border-white/10 text-[#E2C07D] hover:border-[#E2C07D]/50 transition">
                Admin Panel
            </a>
        </nav>

        <!-- Right CTA & Cart Button -->
        <div class="flex items-center gap-3">
            <button onclick="toggleCartDrawer(true)" class="bg-[#0D140F]/65 backdrop-blur-md border border-white/15 px-4 py-2.5 rounded-xl flex items-center gap-2.5 text-sm font-medium text-white hover:border-[#4E8752] transition">
                <svg class="w-4 h-4 text-[#86EFAC]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span>Bag</span>
                <span id="cart-count-badge" class="font-mono-ai px-2 py-0.5 text-xs rounded-full bg-[#4E8752] text-white font-semibold">0</span>
            </button>

            <a href="{{ route('order.create') }}" class="hidden sm:inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-[#4E8752] to-[#1B4323] border border-[#86EFAC]/30 text-white shadow-lg shadow-[#4E8752]/20 hover:brightness-110 transition">
                Order & Checkout
            </a>
        </div>
    </div>
</header>