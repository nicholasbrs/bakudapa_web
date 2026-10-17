<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Biaya pengiriman (flat, IDR)
    |--------------------------------------------------------------------------
    */
    'delivery_fee' => 25000,

    /*
    |--------------------------------------------------------------------------
    | Lokasi toko / titik pickup
    |--------------------------------------------------------------------------
    | PENTING: lat & lng di bawah ini masih perkiraan (area Menteng, Jakarta).
    | Ganti dengan koordinat asli toko: buka Google Maps, klik kanan di lokasi
    | toko, lalu klik angka koordinat untuk menyalinnya.
    */
    'shop' => [
        'name'    => 'Baku Dapa Central Kitchen & Experience Hub',
        'address' => 'Jl. Sam Ratulangi No. 88, Menteng, Central Jakarta 10350',
        'hours'   => '09:00 - 20:00 WIB',
        'lat'     => -6.1989,
        'lng'     => 106.8330,
    ],

];
