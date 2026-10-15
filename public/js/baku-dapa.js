// Load Cart from localStorage so it persists across pages
let cart = JSON.parse(localStorage.getItem('bakudapa_cart') || '[]');

function saveCart() {
    localStorage.setItem('bakudapa_cart', JSON.stringify(cart));
}

document.addEventListener('DOMContentLoaded', () => {
    renderCart();

    // Scroll Reveal Observer
    const revealElements = document.querySelectorAll('.reveal-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, { threshold: 0.1 });

    revealElements.forEach(el => observer.observe(el));

    // Interactive Mouse Parallax on Aurora Background
    const auroraOrbs = document.querySelectorAll('#aurora-parallax-wrapper [data-speed]');
    window.addEventListener('mousemove', (e) => {
        const x = (window.innerWidth / 2 - e.clientX);
        const y = (window.innerHeight / 2 - e.clientY);

        auroraOrbs.forEach(orb => {
            const speed = parseFloat(orb.getAttribute('data-speed')) || 0.02;
            orb.style.marginLeft = `${x * speed}px`;
            orb.style.marginTop = `${y * speed}px`;
        });
    });
});

function switchHeroDish(type) {
    const img = document.getElementById('hero-dish-img');
    const title = document.getElementById('hero-dish-title');
    const sub = document.getElementById('hero-dish-sub');
    const price = document.getElementById('hero-dish-price');
    const btnCakalang = document.getElementById('btn-hero-cakalang');
    const btnWoku = document.getElementById('btn-hero-woku');
    const quickAddBtn = document.getElementById('hero-quick-add-btn');

    if (!img) return;

    if (type === 'cakalang') {
        img.src = 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1000&q=85';
        title.textContent = 'Cakalang Fufu Rabe Rica';
        sub.textContent = 'Wood-Smoked Skipjack Tuna • Red Chili & Lime Leaves';
        price.textContent = 'IDR 95K';
        quickAddBtn.setAttribute('onclick', "addToCart(1, 'Cakalang Fufu Rabe Rica', 95000, 'HALAL')");
        btnCakalang.className = 'px-3 py-1 rounded-lg text-xs font-semibold bg-[#4E8752] text-white transition';
        btnWoku.className = 'px-3 py-1 rounded-lg text-xs font-semibold bg-white/5 text-[#94A898] hover:text-white transition';
    } else {
        img.src = 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=1000&q=85';
        title.textContent = 'Ayam Woku Belanga';
        sub.textContent = 'Organic Free-Range Chicken • Fresh Woku Herb Paste';
        price.textContent = 'IDR 110K';
        quickAddBtn.setAttribute('onclick', "addToCart(2, 'Ayam Woku Belanga', 110000, 'HALAL')");
        btnWoku.className = 'px-3 py-1 rounded-lg text-xs font-semibold bg-[#4E8752] text-white transition';
        btnCakalang.className = 'px-3 py-1 rounded-lg text-xs font-semibold bg-white/5 text-[#94A898] hover:text-white transition';
    }
}

function filterMenu(category) {
    const cards = document.querySelectorAll('.menu-item-card');
    const buttons = document.querySelectorAll('.menu-filter-btn');

    buttons.forEach(btn => {
        if (btn.dataset.filter === category) {
            btn.className = 'menu-filter-btn px-4 py-2 rounded-xl text-xs font-semibold bg-[#4E8752] text-white transition';
        } else {
            btn.className = 'menu-filter-btn px-4 py-2 rounded-xl text-xs font-semibold text-[#94A898] hover:text-white transition';
        }
    });

    cards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

function checkCityCoverage() {
    const input = document.getElementById('city-search-input').value.trim();
    const resultEl = document.getElementById('city-search-result');

    if (!input) {
        resultEl.textContent = 'Please enter a city name first.';
        resultEl.classList.remove('hidden');
        return;
    }

    resultEl.innerHTML = `✓ <strong>${input}</strong> is covered by Baku Dapa's <strong>1-2 Day Cold-Chain Vacuum Delivery</strong> (includes Thermal Foil Pouch & Ice Gel).`;
    resultEl.classList.remove('hidden');
}

function addToCart(id, name, price, category, openDrawer = true) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id, name, price, category, qty: 1 });
    }
    saveCart();
    renderCart();
    if (openDrawer) toggleCartDrawer(true);
    if (typeof window.syncCheckoutPage === 'function') window.syncCheckoutPage();
}

function updateCartQty(id, delta) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    item.qty += delta;
    if (item.qty <= 0) {
        cart = cart.filter(i => i.id !== id);
    }
    saveCart();
    renderCart();
    if (typeof window.syncCheckoutPage === 'function') window.syncCheckoutPage();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    saveCart();
    renderCart();
    if (typeof window.syncCheckoutPage === 'function') window.syncCheckoutPage();
}

function renderCart() {
    const badge = document.getElementById('cart-count-badge');
    const container = document.getElementById('cart-items-container');
    const totalEl = document.getElementById('cart-total-price');

    const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
    const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

    if (badge) badge.textContent = totalItems;
    if (totalEl) totalEl.textContent = 'IDR ' + totalPrice.toLocaleString('en-US');

    if (!container) return;

    if (cart.length === 0) {
        container.innerHTML = '<p class="text-sm text-[#94A898] text-center py-12">Your order bag is currently empty.</p>';
        return;
    }

    container.innerHTML = cart.map(item => `
        <div class="glass-card p-4 rounded-2xl flex items-center justify-between gap-3">
            <div>
                <span class="font-mono-ai text-[10px] font-bold px-2 py-0.5 rounded bg-white/10 text-[#86EFAC]">${item.category}</span>
                <h4 class="font-display-ai text-sm font-bold text-white mt-1">${item.name}</h4>
                <p class="font-mono-ai text-xs text-[#94A898]">${item.qty}x IDR ${item.price.toLocaleString('en-US')}</p>
            </div>
            <div class="flex items-center gap-1.5">
                <button type="button" onclick="updateCartQty(${item.id}, -1)" class="w-7 h-7 rounded-lg bg-white/5 hover:bg-white/15 text-white text-xs">-</button>
                <span class="font-mono-ai text-xs w-5 text-center">${item.qty}</span>
                <button type="button" onclick="updateCartQty(${item.id}, 1)" class="w-7 h-7 rounded-lg bg-white/5 hover:bg-white/15 text-white text-xs">+</button>
            </div>
        </div>
    `).join('');
}

function toggleCartDrawer(show) {
    const drawer = document.getElementById('cart-drawer');
    const backdrop = document.getElementById('cart-drawer-backdrop');
    if (!drawer || !backdrop) return;
    if (show) {
        backdrop.classList.remove('hidden');
        drawer.classList.remove('translate-x-full');
    } else {
        backdrop.classList.add('hidden');
        drawer.classList.add('translate-x-full');
    }
}