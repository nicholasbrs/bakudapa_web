/* =============================================================
   BAKU DAPA — Track page: peta lokasi + pembaruan status otomatis
   - Setiap beberapa detik halaman ini diminta ulang di latar belakang.
   - Jika status pesanan berubah (mis. admin memverifikasi pembayaran),
     area #live-region diganti dengan versi terbaru tanpa reload penuh.
============================================================= */
(function () {
    'use strict';

    const POLL_MS = 8000;
    const MESSAGES = {
        unpaid: 'Waiting for your payment.',
        waiting_verification: 'Receipt received. Waiting for admin verification.',
        rejected: 'Your receipt was rejected. Please upload a new one.',
        paid: 'Payment verified! Your live tracker is now unlocked 🎉',
        processing: 'Our chefs are cooking your order 🔥',
        delivering: 'Your order is on the way 🚚',
        ready_for_pickup: 'Your order is ready for pickup 🏪',
        completed: 'Order completed. Enjoy your meal 🎉',
    };

    let trackMap = null;

    function initTrackMap() {
        const el = document.getElementById('track-map');
        if (!el || !window.BDMap || !BDMap.available()) return;

        if (trackMap) {
            try { trackMap.remove(); } catch (e) { /* container lama sudah terlepas dari DOM */ }
            trackMap = null;
        }

        const lat = parseFloat(el.dataset.lat);
        const lng = parseFloat(el.dataset.lng);
        if (isNaN(lat) || isNaN(lng)) return;

        trackMap = L.map(el, { scrollWheelZoom: false }).setView([lat, lng], 16);
        BDMap.addTiles(trackMap);
        const icon = el.dataset.type === 'pickup' ? BDMap.shopIcon() : BDMap.pinIcon();
        L.marker([lat, lng], { icon: icon })
            .addTo(trackMap)
            .bindPopup(BDMap.esc(el.dataset.label || 'Location'));
    }

    async function poll() {
        const region = document.getElementById('live-region');
        if (!region) return;
        const current = region.dataset.status;
        if (current === 'completed') return; // tidak ada lagi yang berubah

        try {
            const res = await fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                cache: 'no-store',
                credentials: 'same-origin',
            });
            if (!res.ok || res.redirected) return;

            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const next = doc.getElementById('live-region');
            if (!next || next.dataset.status === current) return;

            region.innerHTML = next.innerHTML;
            region.dataset.status = next.dataset.status;
            initTrackMap();
            BDMap.toast(MESSAGES[next.dataset.status] || 'Your order status was updated.');
        } catch (e) {
            // Koneksi sedang bermasalah: coba lagi pada putaran berikutnya
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        initTrackMap();
        setInterval(() => { if (!document.hidden) poll(); }, POLL_MS);
    });
})();
