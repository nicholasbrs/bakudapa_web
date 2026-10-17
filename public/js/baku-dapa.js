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
        quickAddBtn.setAttribute('onclick', "quickOrder(1, 'Cakalang Fufu Rabe Rica', 95000, 'HALAL')");
        btnCakalang.className = 'px-3 py-1 rounded-lg text-xs font-semibold bg-[#4E8752] text-white transition';
        btnWoku.className = 'px-3 py-1 rounded-lg text-xs font-semibold bg-white/5 text-[#94A898] hover:text-white transition';
    } else {
        img.src = 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=1000&q=85';
        title.textContent = 'Ayam Woku Belanga';
        sub.textContent = 'Organic Free-Range Chicken • Fresh Woku Herb Paste';
        price.textContent = 'IDR 110K';
        quickAddBtn.setAttribute('onclick', "quickOrder(2, 'Ayam Woku Belanga', 110000, 'HALAL')");
        btnWoku.className = 'px-3 py-1 rounded-lg text-xs font-semibold bg-[#4E8752] text-white transition';
        btnCakalang.className = 'px-3 py-1 rounded-lg text-xs font-semibold bg-white/5 text-[#94A898] hover:text-white transition';
    }
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

function addToCart(id, name, price, category) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.qty += 1;
    } else {
        cart.push({ id, name, price, category, qty: 1 });
    }
    saveCart();
    renderCart();
    if (typeof window.syncCheckoutPage === 'function') window.syncCheckoutPage();
}

// Dipakai tombol di halaman utama: tambahkan ke bag lalu buka halaman order.
// (Bag hanya ada di halaman order, jadi dari home langsung diarahkan ke sana.)
function quickOrder(id, name, price, category) {
    addToCart(id, name, price, category);
    window.location.href = (window.BD && window.BD.orderUrl) ? window.BD.orderUrl : '/order';
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

// Memperbarui badge jumlah item bila ada di halaman (mis. floating bag di halaman order).
function renderCart() {
    const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
    document.querySelectorAll('[data-cart-count]').forEach(el => { el.textContent = totalItems; });
}
