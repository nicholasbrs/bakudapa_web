<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $shippingZones = [
            'jabodetabek' => [
                'region' => 'Greater Jakarta (Jabodetabek)',
                'eta' => '6 - 24 Hours (Instant / Same Day)',
                'couriers' => 'GoSend, GrabExpress, Paxel Same Day',
                'packaging' => 'Food-Grade Vacuum Pack + Thermal Foil Pouch + Ice Gel',
                'guarantee' => '100% Freshness Guaranteed on Same-Day Arrival'
            ],
            'jawa' => [
                'region' => 'Bandung, Surabaya, Semarang, Yogyakarta, Malang & All Java',
                'eta' => '1-Day Delivery (Next-Day Cold-Chain)',
                'couriers' => 'Paxel Cold-Chain (4°C Chiller), JNE YES, TIKI ONS',
                'packaging' => 'Double Embossed Vacuum + Insulated Foil + 2x Ice Gel Packs',
                'guarantee' => '100% Money-Back or Free Replacement if Seal is Compromised'
            ],
            'luar_jawa' => [
                'region' => 'Bali, Medan, Makassar, Balikpapan, Batam & Major Cities',
                'eta' => '1 - 2 Days (Priority Air Cargo / Next Day)',
                'couriers' => 'JNE YES, Paxel (Bali & Medan), Lion Parcel BOSSPACK',
                'packaging' => 'Blast-Frozen (-18°C) + Vacuum + Hard Styrofoam Box + Ice Gel',
                'guarantee' => 'Safe for up to 96 Hours in Transit with Zero Preservatives'
            ]
        ];

        $testimonials = [
            [
                'name' => 'Clarissa T.',
                'city' => 'Surabaya, East Java',
                'courier' => 'Paxel Cold-Chain',
                'rating' => 5,
                'product' => 'Cakalang Fufu Rabe Rica & Ayam Woku',
                'comment' => 'I was hesitant to order cooked food to Surabaya at first. When the box arrived, the interior was still ice-cold thanks to the thermal foil and ice gel. Stir-fried the Cakalang for 3 minutes and my whole kitchen smelled like Manado!',
                'badge' => 'Arrived Chilled & 100% Vacuum Intact'
            ],
            [
                'name' => 'Hendra Wijaya',
                'city' => 'Denpasar, Bali',
                'courier' => 'JNE YES (24 Hours)',
                'rating' => 5,
                'product' => 'Crispy Pork Tore Rica & Tinoransak',
                'comment' => 'Shipped to Bali overnight. Love how the meat and sambal are vacuum-packed separately, and the Non-Halal red seal is super clear and professional. Five-star restaurant quality right at home.',
                'badge' => 'Overnight Delivery to Bali'
            ],
            [
                'name' => 'Dr. Nadia Rahma',
                'city' => 'Bandung, West Java',
                'courier' => 'Paxel Same Day',
                'rating' => 5,
                'product' => 'Halal Family Bundle (Ayam Woku & Beef Garo)',
                'comment' => 'As a Muslim customer, I have complete peace of mind ordering here because their Halal and Non-Halal kitchens are 100% separated with distinct green security seals. The fresh herbs taste incredible!',
                'badge' => 'Verified Halal Green Seal'
            ]
        ];

        return view('home', compact('shippingZones', 'testimonials'));
    }
}