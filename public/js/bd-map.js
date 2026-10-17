// Helper peta bersama (order page, track page). Membutuhkan Leaflet (window.L).
window.BDMap = {
    tileUrl: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors',

    available() {
        return typeof window.L !== 'undefined';
    },

    addTiles(map) {
        return L.tileLayer(this.tileUrl, { maxZoom: 19, attribution: this.attribution }).addTo(map);
    },

    shopIcon() {
        return L.divIcon({
            className: '',
            html: '<div class="shop-marker"><span>🍲</span></div>',
            iconSize: [44, 44],
            iconAnchor: [22, 53],
            popupAnchor: [0, -58],
        });
    },

    pinIcon() {
        return L.divIcon({
            className: '',
            html: '<div class="shop-marker"><span>📍</span></div>',
            iconSize: [44, 44],
            iconAnchor: [22, 53],
            popupAnchor: [0, -58],
        });
    },

    viewUrl(lat, lng) {
        return 'https://www.google.com/maps/search/?api=1&query=' + lat + ',' + lng;
    },

    dirUrl(lat, lng) {
        return 'https://www.google.com/maps/dir/?api=1&destination=' + lat + ',' + lng;
    },

    esc(str) {
        return String(str).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    },

    toast(message) {
        const old = document.querySelector('.bd-toast');
        if (old) old.remove();
        const el = document.createElement('div');
        el.className = 'bd-toast';
        el.textContent = message;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 2600);
    },
};
