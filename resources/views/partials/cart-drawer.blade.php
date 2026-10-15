<div id="cart-drawer-backdrop" onclick="toggleCartDrawer(false)" class="fixed inset-0 bg-black/75 backdrop-blur-sm z-50 hidden"></div>

<aside id="cart-drawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-[#050806] border-l border-white/15 z-50 transform translate-x-full transition-transform duration-300 flex flex-col">
    <div class="p-6 border-b border-white/10 flex items-center justify-between">
        <div>
            <h3 class="font-display-ai text-2xl font-bold text-white">Your Order Bag</h3>
            <p class="text-xs text-[#94A898]">Proceed to Web Checkout & Payment</p>
        </div>
        <button onclick="toggleCartDrawer(false)" class="text-[#94A898] hover:text-white text-xs font-mono-ai px-3 py-1.5 rounded-lg border border-white/10">
            ESC ✕
        </button>
    </div>

    <div id="cart-items-container" class="flex-1 overflow-y-auto p-6 space-y-4">
        <p class="text-sm text-[#94A898] text-center py-12">Your order bag is currently empty.</p>
    </div>

    <div class="p-6 border-t border-white/10 bg-[#0D140F]/80 space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-sm text-[#94A898]">Estimated Subtotal</span>
            <span id="cart-total-price" class="font-mono-ai text-xl font-bold text-white">IDR 0</span>
        </div>
        <a href="{{ route('order.create') }}" class="block text-center w-full py-3.5 rounded-xl bg-gradient-to-r from-[#4E8752] to-[#1B4323] border border-[#86EFAC]/40 text-white font-bold text-sm shadow-lg hover:brightness-110 transition">
            Proceed to Web Checkout →
        </a>
    </div>
</aside>