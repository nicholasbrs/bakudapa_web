/* =============================================================
   BAKU DAPA — Order wizard
   Step 1: pilih menu (floating bag + modal + konfirmasi ulang)
   Step 2: data diri + metode penerimaan (pin peta / pickup)
   Step 3: metode pembayaran + ringkasan, lalu kirim pesanan

   Bergantung pada: baku-dapa.js (cart, saveCart, addToCart,
   updateCartQty, removeFromCart), bd-map.js, Leaflet, dan
   window.ORDER_PAGE yang diisi oleh create.blade.php.
============================================================= */
(function () {
    'use strict';

    const CFG = window.ORDER_PAGE;
    const MENU_BY_ID = {};
    CFG.menus.forEach(m => { MENU_BY_ID[m.id] = m; });

    const $ = id => document.getElementById(id);
    const esc = s => (window.BDMap ? BDMap.esc(s) : String(s));
    const fmt = n => 'IDR ' + Number(n || 0).toLocaleString('en-US');
    const toast = msg => BDMap.toast(msg);

    let currentStep = 1;
    let fulfillment = CFG.old.fulfillment === 'pickup' ? 'pickup' : 'delivery';
    let paymentMethod = CFG.old.payment || 'QRIS';
    let lastCount = 0;

    /* ---------------------------------------------------------
       CART HELPERS
    --------------------------------------------------------- */
    const subtotal = () => cart.reduce((s, i) => s + i.price * i.qty, 0);
    const itemCount = () => cart.reduce((s, i) => s + i.qty, 0);
    const shippingFee = () => (fulfillment === 'delivery' && subtotal() > 0 ? CFG.deliveryFee : 0);

    // Samakan isi bag (localStorage) dengan data menu terbaru: buang menu yang sudah tidak ada
    // dan perbarui nama/harga bila berubah.
    function syncCartWithMenu() {
        cart = cart
            .filter(i => MENU_BY_ID[i.id])
            .map(i => {
                const m = MENU_BY_ID[i.id];
                return {
                    id: m.id,
                    name: m.name,
                    price: m.price,
                    category: m.category,
                    qty: Math.max(1, Math.min(99, parseInt(i.qty, 10) || 1)),
                };
            });
        saveCart();
    }

    function addDish(id) {
        const m = MENU_BY_ID[id];
        if (!m) return;
        addToCart(m.id, m.name, m.price, m.category);
    }

    function changeQty(id, delta) {
        updateCartQty(id, delta);
    }

    /* ---------------------------------------------------------
       RENDER: DISH CONTROLS, FLOATING BAG, BAG MODAL
    --------------------------------------------------------- */
    function renderDishControls() {
        document.querySelectorAll('[data-dish-ctl]').forEach(box => {
            const id = parseInt(box.dataset.dishCtl, 10);
            if (!id) return;
            const item = cart.find(i => i.id === id);
            if (!item) {
                box.innerHTML =
                    '<button type="button" onclick="OrderPage.add(' + id + ')" ' +
                    'class="w-full px-4 py-2.5 rounded-xl bg-bd-cta hover:bg-bd-cta-hover text-white font-label text-xs font-bold transition">+ Add to Bag</button>';
            } else {
                box.innerHTML =
                    '<div class="w-full flex items-center justify-between rounded-xl border border-bd-gold/50 bg-bd-select/50 p-1">' +
                    '<button type="button" onclick="OrderPage.change(' + id + ',-1)" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 text-bd-text font-bold">−</button>' +
                    '<span class="font-label text-sm font-bold text-bd-text">' + item.qty + ' in bag</span>' +
                    '<button type="button" onclick="OrderPage.change(' + id + ',1)" class="w-9 h-9 rounded-lg bg-bd-cta hover:bg-bd-cta-hover text-white font-bold">+</button>' +
                    '</div>';
            }
        });
    }

    function renderFloatingBag() {
        const count = itemCount();
        const totalEl = $('floating-bag-total');
        if (totalEl) totalEl.textContent = fmt(subtotal());
        const label = $('floating-bag-label');
        if (label) label.textContent = count === 1 ? 'item' : 'items';
        const bag = $('floating-bag');
        if (bag) {
            bag.classList.toggle('opacity-80', count === 0);
            if (count > lastCount) {
                bag.classList.remove('bag-bump');
                void bag.offsetWidth; // restart animation
                bag.classList.add('bag-bump');
            }
        }
        lastCount = count;
    }

    function itemRowEditable(i) {
        const m = MENU_BY_ID[i.id] || {};
        return (
            '<div class="p-3 rounded-2xl bg-bd-bg/80 border border-white/10 flex items-center gap-3">' +
            '<img src="' + esc(m.image || '') + '" alt="" class="w-14 h-14 rounded-xl object-cover shrink-0">' +
            '<div class="flex-1 min-w-0">' +
            '<p class="font-label text-xs text-bd-gold">' + esc(i.category) + '</p>' +
            '<h4 class="font-display text-sm font-bold text-bd-text truncate">' + esc(i.name) + '</h4>' +
            '<p class="font-num text-xs text-bd-gold">' + fmt(i.price * i.qty) + '</p>' +
            '</div>' +
            '<div class="flex items-center gap-2 shrink-0">' +
            '<button type="button" onclick="OrderPage.change(' + i.id + ',-1)" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-bd-text">−</button>' +
            '<span class="font-label text-xs w-5 text-center text-bd-text">' + i.qty + '</span>' +
            '<button type="button" onclick="OrderPage.change(' + i.id + ',1)" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-bd-text">+</button>' +
            '</div>' +
            '</div>'
        );
    }

    function itemRowReadonly(i) {
        return (
            '<div class="flex items-center justify-between gap-3 py-2.5 border-b border-white/10 last:border-0">' +
            '<div class="min-w-0">' +
            '<p class="font-display text-sm font-bold text-bd-text truncate">' + esc(i.name) + '</p>' +
            '<p class="font-num text-xs text-bd-muted">' + i.qty + ' × ' + fmt(i.price) + ' · ' + esc(i.category) + '</p>' +
            '</div>' +
            '<span class="font-num text-sm text-bd-text shrink-0">' + fmt(i.price * i.qty) + '</span>' +
            '</div>'
        );
    }

    function renderBag() {
        const list = $('bag-items');
        if (!list) return;
        if (!cart.length) {
            list.innerHTML =
                '<div class="p-8 rounded-2xl bg-bd-bg/60 border border-white/10 text-center text-sm text-bd-muted">' +
                'Your bag is empty.<br><span class="text-xs">Close this and tap <strong class="text-bd-text">+ Add to Bag</strong> on any dish.</span></div>';
        } else {
            list.innerHTML = cart.map(itemRowEditable).join('');
        }
        const n = itemCount();
        $('bag-count-label').textContent = n + (n === 1 ? ' item' : ' items');
        $('bag-subtotal').textContent = fmt(subtotal());
        const btn = $('bag-checkout-btn');
        btn.disabled = !cart.length;
        btn.classList.toggle('opacity-40', !cart.length);
        btn.classList.toggle('cursor-not-allowed', !cart.length);
    }

    function renderConfirm() {
        $('confirm-items').innerHTML = cart.map(itemRowReadonly).join('');
        const n = itemCount();
        $('confirm-count').textContent = n + (n === 1 ? ' item' : ' items');
        $('confirm-subtotal').textContent = fmt(subtotal());
    }

    /* ---------------------------------------------------------
       MODALS
    --------------------------------------------------------- */
    function showBagView(view) {
        $('bag-view-edit').classList.toggle('hidden', view !== 'edit');
        $('bag-view-confirm').classList.toggle('hidden', view !== 'confirm');
    }

    function lockScroll(lock) {
        document.body.classList.toggle('overflow-hidden', lock);
    }

    function openBag() {
        renderBag();
        showBagView('edit');
        $('bag-modal').classList.remove('hidden');
        lockScroll(true);
    }

    function closeBag() {
        $('bag-modal').classList.add('hidden');
        lockScroll(false);
    }

    function bagToConfirm() {
        if (!cart.length) return;
        renderConfirm();
        showBagView('confirm');
    }

    function confirmOrder() {
        if (!cart.length) return;
        closeBag();
        goStep(2);
    }

    function openDish(id) {
        const m = MENU_BY_ID[id];
        if (!m) return;
        $('dish-modal-img').src = m.image;
        $('dish-modal-img').alt = m.name;
        $('dish-modal-name').textContent = m.name;
        $('dish-modal-desc').textContent = m.desc;
        $('dish-modal-price').textContent = fmt(m.price);
        $('dish-modal-weight').textContent = m.weight;
        $('dish-modal-spicy').textContent = m.spicy;
        const cat = $('dish-modal-cat');
        cat.textContent = m.category;
        cat.className = 'font-label text-xs px-2 py-0.5 rounded ' +
            (m.category === 'HALAL' ? 'bg-bd-halal/15 text-bd-halal' : 'bg-amber-500/20 text-amber-300');
        $('dish-modal-ctl').dataset.dishCtl = id;
        renderDishControls();
        $('dish-modal').classList.remove('hidden');
        lockScroll(true);
    }

    function closeDish() {
        $('dish-modal').classList.add('hidden');
        lockScroll(false);
    }

    /* ---------------------------------------------------------
       STEP 1: FILTER
    --------------------------------------------------------- */
    function filterDishes(filter) {
        document.querySelectorAll('.dish-card').forEach(card => {
            const show = filter === 'ALL' || card.dataset.category === filter;
            card.classList.toggle('hidden', !show);
        });
        document.querySelectorAll('.dish-filter-btn').forEach(btn => {
            const active = btn.dataset.filter === filter;
            btn.className = 'dish-filter-btn px-4 py-2 rounded-xl text-xs font-label font-semibold border transition ' +
                (active
                    ? 'border-bd-gold bg-bd-select/70 text-bd-text'
                    : 'border-white/15 bg-bd-bg/60 text-bd-muted hover:text-bd-text');
        });
    }

    /* ---------------------------------------------------------
       STEPPER / NAVIGATION
    --------------------------------------------------------- */
    function updateStepper() {
        for (let n = 1; n <= 3; n++) {
            const pill = $('step-pill-' + n);
            if (!pill) continue;
            const dot = pill.querySelector('.pill-dot');
            const done = n < currentStep;
            const active = n === currentStep;
            dot.className = 'pill-dot w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold font-label transition ' +
                (done ? 'bg-bd-gold text-bd-card' : active ? 'bg-bd-cta text-white ring-2 ring-bd-gold/50' : 'bg-white/10 text-bd-muted');
            dot.textContent = done ? '✓' : String(n).padStart(2, '0');
            pill.querySelector('.pill-label').className = 'pill-label text-xs font-semibold hidden sm:block ' +
                (active ? 'text-bd-text' : done ? 'text-bd-gold' : 'text-bd-muted');
        }
        [1, 2].forEach(n => {
            const line = $('step-line-' + n);
            if (line) line.className = 'step-line flex-1 h-px mx-2 sm:mx-4 ' + (n < currentStep ? 'bg-bd-gold' : 'bg-white/15');
        });
    }

    function goStep(n) {
        if (n > 1 && !cart.length) {
            toast('Your bag is empty. Please add at least one dish first.');
            n = 1;
        }
        currentStep = n;
        document.querySelectorAll('.wizard-step').forEach(el => {
            el.classList.toggle('hidden', el.id !== 'step-' + n);
        });
        updateStepper();
        const bag = $('floating-bag');
        if (bag) bag.classList.toggle('hidden', n !== 1);
        if (n === 2) {
            setFulfillment(fulfillment);
            updateStep2Total();
        }
        if (n === 3) renderRecap();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ---------------------------------------------------------
       STEP 2: FULFILLMENT + VALIDATION
    --------------------------------------------------------- */
    function setFulfillment(type) {
        fulfillment = type === 'pickup' ? 'pickup' : 'delivery';
        $('fulfillment-type-input').value = fulfillment;

        const on = 'p-4 rounded-2xl border border-bd-gold bg-bd-select/60 text-left transition flex items-start gap-3';
        const off = 'p-4 rounded-2xl border border-white/15 bg-bd-bg/60 text-left transition flex items-start gap-3 hover:border-white/30';
        $('tab-delivery').className = fulfillment === 'delivery' ? on : off;
        $('tab-pickup').className = fulfillment === 'pickup' ? on : off;

        $('delivery-panel').classList.toggle('hidden', fulfillment !== 'delivery');
        $('pickup-panel').classList.toggle('hidden', fulfillment !== 'pickup');

        // Peta dibuat saat panelnya terlihat (Leaflet butuh container yang tampil)
        if (currentStep === 2) {
            if (fulfillment === 'delivery') initDeliveryMap();
            else initPickupMap();
        }
        updateStep2Total();
    }

    function updateStep2Total() {
        const total = subtotal() + shippingFee();
        const el = $('step2-total');
        if (el) el.textContent = fmt(total);
        const note = $('step2-total-note');
        if (note) note.textContent = fulfillment === 'delivery'
            ? 'incl. ' + fmt(CFG.deliveryFee) + ' delivery'
            : 'free pickup';
    }

    function setErr(key, msg) {
        const el = document.querySelector('[data-err="' + key + '"]');
        if (!el) return;
        if (msg) {
            el.textContent = msg;
            el.classList.remove('hidden');
        } else {
            el.textContent = '';
            el.classList.add('hidden');
        }
    }

    function validateStep2() {
        const errors = [];
        const name = $('f-name').value.trim();
        const phone = $('f-phone').value.trim();
        const email = $('f-email').value.trim();
        const phoneDigits = phone.replace(/\D/g, '');

        ['name', 'phone', 'email', 'city', 'address', 'pin'].forEach(k => setErr(k, ''));

        if (name.length < 2) errors.push(['name', 'Please enter your full name.', 'f-name']);
        if (phoneDigits.length < 9 || phoneDigits.length > 15) errors.push(['phone', 'Please enter a valid phone / WhatsApp number.', 'f-phone']);
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push(['email', 'That email address does not look right.', 'f-email']);

        if (fulfillment === 'delivery') {
            if (!$('delivery-lat').value || !$('delivery-lng').value) {
                errors.push(['pin', 'Please pin your delivery location on the map (move the map, search, or use your current location).', 'map-search']);
            }
            if (!$('input-delivery-city').value.trim()) errors.push(['city', 'Please enter your city / regency.', 'input-delivery-city']);
            if ($('input-delivery-address').value.trim().length < 10) errors.push(['address', 'Please complete your delivery address (street, number, district).', 'input-delivery-address']);
        }

        errors.forEach(e => setErr(e[0], e[1]));
        if (errors.length) {
            const first = $(errors[0][2]);
            if (first) {
                first.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => first.focus({ preventScroll: true }), 350);
            }
            return false;
        }
        return true;
    }

    function toPayment() {
        if (validateStep2()) goStep(3);
    }

    /* ---------------------------------------------------------
       MAP: DELIVERY (GoFood-style center pin)
    --------------------------------------------------------- */
    let delMap = null;
    let suppressPin = false;
    let addrTouched = false;
    let cityTouched = false;
    let geoTimer = null;
    let geoSeq = 0;

    function mapUnavailable(containerId) {
        const c = $(containerId);
        if (c && !c.dataset.failed) {
            c.dataset.failed = '1';
            c.innerHTML = '<div class="h-full flex items-center justify-center p-6 text-center text-xs text-bd-muted">' +
                'The map could not be loaded. Please check your internet connection and refresh the page.</div>';
        }
    }

    function setPinStatus(isSet) {
        const el = $('pin-status');
        if (!el) return;
        if (isSet) {
            el.className = 'font-label text-xs px-2.5 py-1 rounded-full bg-bd-halal/15 text-bd-halal';
            el.textContent = '● Pin set';
        } else {
            el.className = 'font-label text-xs px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300';
            el.textContent = '○ Move the map to set your pin';
        }
    }

    function setDetected(text, muted) {
        const el = $('pin-detected');
        if (!el) return;
        el.textContent = text;
        el.className = 'text-xs leading-relaxed ' + (muted ? 'text-bd-muted' : 'text-bd-text');
    }

    function setPinFromCenter() {
        if (suppressPin || !delMap) return;
        const c = delMap.getCenter();
        const lat = c.lat.toFixed(6);
        const lng = c.lng.toFixed(6);
        $('delivery-lat').value = lat;
        $('delivery-lng').value = lng;
        setPinStatus(true);
        setErr('pin', '');
        clearTimeout(geoTimer);
        geoTimer = setTimeout(() => reverseGeocode(lat, lng), 700);
    }

    async function reverseGeocode(lat, lng) {
        const seq = ++geoSeq;
        setDetected('Detecting address…', true);
        try {
            const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1&zoom=18&accept-language=id,en&lat=' +
                encodeURIComponent(lat) + '&lon=' + encodeURIComponent(lng);
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            if (seq !== geoSeq) return; // ada pencarian lebih baru
            if (data && data.display_name) {
                $('delivery-place').value = data.display_name;
                setDetected(data.display_name, false);
                const a = data.address || {};
                const city = a.city || a.town || a.municipality || a.county || a.state_district || a.state || '';
                if (city && !cityTouched) $('input-delivery-city').value = city;
                if (!addrTouched) $('input-delivery-address').value = data.display_name;
            } else {
                setDetected('No address found for this point. Please type your address below.', true);
            }
        } catch (e) {
            if (seq !== geoSeq) return;
            setDetected('Could not detect the address automatically. Please type it below — your pin is still saved.', true);
        }
    }

    function initDeliveryMap() {
        if (!BDMap.available()) return mapUnavailable('delivery-map');

        if (delMap) {
            suppressPin = true;
            delMap.invalidateSize();
            suppressPin = false;
            return;
        }

        const savedLat = parseFloat($('delivery-lat').value);
        const savedLng = parseFloat($('delivery-lng').value);
        const hasSaved = !isNaN(savedLat) && !isNaN(savedLng);

        delMap = L.map('delivery-map', { scrollWheelZoom: 'center' })
            .setView(hasSaved ? [savedLat, savedLng] : [CFG.shop.lat, CFG.shop.lng], hasSaved ? 17 : 13);
        BDMap.addTiles(delMap);

        const pin = $('center-pin');
        delMap.on('movestart', () => pin.classList.add('is-lifting'));
        delMap.on('moveend', () => {
            pin.classList.remove('is-lifting');
            setPinFromCenter();
        });

        if (hasSaved) {
            setPinStatus(true);
            setDetected($('delivery-place').value || 'Pin restored. Adjust the map to change it.', false);
        } else {
            setPinStatus(false);
        }

        $('input-delivery-address').addEventListener('input', () => { addrTouched = true; });
        $('input-delivery-city').addEventListener('input', () => { cityTouched = true; });
        if ($('input-delivery-address').value.trim()) addrTouched = true;
        if ($('input-delivery-city').value.trim()) cityTouched = true;
    }

    function useMyLocation() {
        if (!delMap) return;
        if (!navigator.geolocation) {
            toast('Your browser does not support location access.');
            return;
        }
        const btn = $('btn-my-location');
        btn.disabled = true;
        btn.classList.add('opacity-60');
        navigator.geolocation.getCurrentPosition(
            pos => {
                btn.disabled = false;
                btn.classList.remove('opacity-60');
                addrTouched = false; // pin dari lokasi sendiri: biarkan alamat terisi otomatis
                delMap.setView([pos.coords.latitude, pos.coords.longitude], 18);
                setPinFromCenter(); // jaga-jaga bila titik sama dan peta tidak bergerak
            },
            () => {
                btn.disabled = false;
                btn.classList.remove('opacity-60');
                toast('Could not get your location. Allow location access, or search / move the map manually.');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    async function searchPlace() {
        const q = $('map-search').value.trim();
        const box = $('map-search-results');
        if (q.length < 3) {
            toast('Type at least 3 characters to search.');
            return;
        }
        box.classList.remove('hidden');
        box.innerHTML = '<div class="px-4 py-3 text-xs text-bd-muted">Searching…</div>';
        try {
            const url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&limit=5&countrycodes=id&accept-language=id,en&q=' + encodeURIComponent(q);
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const results = await res.json();
            box.innerHTML = '';
            if (!results.length) {
                box.innerHTML = '<div class="px-4 py-3 text-xs text-bd-muted">No places found. Try a nearby landmark or street name.</div>';
                return;
            }
            results.forEach(r => {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'w-full text-left px-4 py-3 text-xs text-bd-text hover:bg-bd-select/60 border-b border-white/10 last:border-0';
                b.textContent = r.display_name; // textContent: aman dari HTML dari sumber luar
                b.addEventListener('click', () => {
                    box.classList.add('hidden');
                    addrTouched = false;
                    delMap.setView([parseFloat(r.lat), parseFloat(r.lon)], 18);
                    setPinFromCenter();
                });
                box.appendChild(b);
            });
        } catch (e) {
            box.innerHTML = '<div class="px-4 py-3 text-xs text-red-300">Search is unavailable right now. Please move the map to your location instead.</div>';
        }
    }

    /* ---------------------------------------------------------
       MAP: PICKUP (lokasi toko)
    --------------------------------------------------------- */
    let pickMap = null;

    function initPickupMap() {
        if (!BDMap.available()) return mapUnavailable('pickup-map');
        if (pickMap) {
            pickMap.invalidateSize();
            return;
        }
        pickMap = L.map('pickup-map', { scrollWheelZoom: false }).setView([CFG.shop.lat, CFG.shop.lng], 16);
        BDMap.addTiles(pickMap);
        L.marker([CFG.shop.lat, CFG.shop.lng], { icon: BDMap.shopIcon() })
            .addTo(pickMap)
            .bindPopup('<strong>' + BDMap.esc(CFG.shop.name) + '</strong><br>' + BDMap.esc(CFG.shop.address))
            .openPopup();
    }

    function copyShopAddress() {
        const text = CFG.shop.name + ' — ' + CFG.shop.address;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => toast('Address copied'), () => toast(text));
        } else {
            toast(text);
        }
    }

    /* ---------------------------------------------------------
       STEP 3: PAYMENT + RECAP
    --------------------------------------------------------- */
    function selectPayment(method) {
        paymentMethod = method;
        $('payment-method-input').value = method;
        document.querySelectorAll('.pay-method-btn').forEach(btn => {
            const active = btn.dataset.pay === method;
            btn.className = 'pay-method-btn p-4 rounded-2xl border text-center transition ' +
                (active ? 'border-bd-gold bg-bd-select/60 ring-2 ring-bd-gold/30' : 'border-white/15 bg-bd-bg/60 hover:border-white/30');
        });
        const lbl = $('recap-method');
        if (lbl) lbl.textContent = method === 'QRIS' ? 'QRIS (scan QR)' : method + ' bank transfer';
    }

    function renderRecap() {
        $('recap-items').innerHTML = cart.map(itemRowReadonly).join('');
        $('recap-subtotal').textContent = fmt(subtotal());
        const ship = shippingFee();
        $('recap-shipping-label').textContent = fulfillment === 'delivery' ? 'Cold-Chain Delivery Fee' : 'Self-Pickup';
        $('recap-shipping').textContent = ship === 0 ? 'FREE' : fmt(ship);
        $('recap-total').textContent = fmt(subtotal() + ship);

        const name = $('f-name').value.trim();
        const phone = $('f-phone').value.trim();
        let where;
        if (fulfillment === 'delivery') {
            const detail = $('input-address-detail').value.trim();
            where = '<span class="text-bd-gold font-semibold">Delivery to</span><br>' +
                esc($('input-delivery-address').value.trim()) +
                (detail ? '<br><span class="text-bd-muted">Note: ' + esc(detail) + '</span>' : '') +
                '<br><span class="text-bd-muted">' + esc($('input-delivery-city').value.trim()) + '</span>';
        } else {
            where = '<span class="text-bd-gold font-semibold">Self-pickup at</span><br>' +
                esc(CFG.shop.name) + '<br><span class="text-bd-muted">' + esc(CFG.shop.address) + '</span>';
        }
        $('recap-receiver').innerHTML =
            '<p class="font-display text-sm font-bold text-bd-text">' + esc(name) + '</p>' +
            '<p class="font-label text-xs text-bd-muted mb-3">' + esc(phone) + '</p>' +
            '<p class="text-xs text-bd-text leading-relaxed">' + where + '</p>';
        selectPayment(paymentMethod);
    }

    /* ---------------------------------------------------------
       GLOBAL HOOK: dipanggil baku-dapa.js setiap isi bag berubah
    --------------------------------------------------------- */
    window.syncCheckoutPage = function () {
        $('cart-json-input').value = JSON.stringify(cart);
        renderDishControls();
        renderFloatingBag();
        if (!$('bag-modal').classList.contains('hidden')) {
            renderBag();
            if (!$('bag-view-confirm').classList.contains('hidden')) {
                if (!cart.length) showBagView('edit');
                else renderConfirm();
            }
        }
        updateStep2Total();
    };

    /* ---------------------------------------------------------
       INIT
    --------------------------------------------------------- */
    document.addEventListener('DOMContentLoaded', () => {
        syncCartWithMenu();
        lastCount = itemCount();

        filterDishes('ALL');
        selectPayment(paymentMethod);
        $('fulfillment-type-input').value = fulfillment;
        window.syncCheckoutPage();
        renderCart();

        // Enter di dalam form tidak boleh langsung mengirim pesanan
        $('checkout-form').addEventListener('submit', e => {
            if (currentStep !== 3) {
                e.preventDefault();
                if (currentStep === 2) toPayment();
                return;
            }
            if (!cart.length) {
                e.preventDefault();
                toast('Your bag is empty.');
                goStep(1);
                return;
            }
            $('cart-json-input').value = JSON.stringify(cart);
            const btn = $('place-order-btn');
            btn.disabled = true;
            btn.textContent = 'Placing your order…';
        });

        $('map-search').addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchPlace();
            }
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeBag();
                closeDish();
            }
        });

        document.addEventListener('click', e => {
            const box = $('map-search-results');
            if (box && !box.classList.contains('hidden') && !e.target.closest('#map-search-wrap')) {
                box.classList.add('hidden');
            }
        });

        let start = CFG.initialStep;
        if (start > 1 && !cart.length) start = 1;
        goStep(start);
        window.scrollTo(0, 0);
    });

    window.OrderPage = {
        add: addDish,
        change: changeQty,
        openBag, closeBag, bagToConfirm, confirmOrder,
        backToEdit: () => showBagView('edit'),
        openDish, closeDish,
        filter: filterDishes,
        goStep, toPayment,
        setFulfillment,
        selectPayment,
        useMyLocation, searchPlace, copyShopAddress,
    };
})();
