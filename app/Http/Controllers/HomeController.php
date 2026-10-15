<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $menus = [
            [
                'id' => 1,
                'name' => 'Cakalang Fufu Rabe Rica',
                'category' => 'halal',
                'badge' => 'Best Seller #1',
                'price' => 95000,
                'weight' => '350g (2-3 Servings)',
                'spicy' => 'Medium-High Heat',
                'desc' => 'Traditional Minahasan wood-smoked skipjack tuna, hand-shredded and slow-cooked with fiery curly red chili, shallots, and fresh kaffir lime leaves.',
                'image' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=900&q=85'
            ],
            [
                'id' => 2,
                'name' => 'Ayam Woku Belanga',
                'category' => 'halal',
                'badge' => 'Chef Signature',
                'price' => 110000,
                'weight' => '500g (3-4 Servings)',
                'spicy' => 'Aromatic Spicy',
                'desc' => 'Organic free-range chicken simmered in a rich yellow Woku herb paste infused with lemon basil, pandan leaves, turmeric leaves, and lemongrass.',
                'image' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=900&q=85'
            ],
            [
                'id' => 3,
                'name' => 'Smoked Roa Chili Artisan Jar',
                'category' => 'halal',
                'badge' => '30-Day Room Temp Shelf Life',
                'price' => 75000,
                'weight' => '200g (Sealed Glass Jar)',
                'spicy' => 'Smoky & Savory Heat',
                'desc' => '100% pure smoked flying fish (Ikan Roa) from North Sulawesi waters, finely ground and slow-braised in cold-pressed coconut oil and spices.',
                'image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?auto=format&fit=crop&w=900&q=85'
            ],
            [
                'id' => 4,
                'name' => 'Beef Shank Garo Rica',
                'category' => 'halal',
                'badge' => 'Premium Slow-Braised',
                'price' => 135000,
                'weight' => '400g (3 Servings)',
                'spicy' => 'Extra Fiery',
                'desc' => 'Tender prime beef shank braised for hours in an abundant Manadonese red chili rica reduction finished with fresh kemangi basil.',
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=900&q=85'
            ],
            [
                'id' => 5,
                'name' => 'Crispy Pork Tore Rica',
                'category' => 'non-halal',
                'badge' => 'Non-Halal • Dedicated Kitchen',
                'price' => 145000,
                'weight' => '400g (3 Servings)',
                'spicy' => 'Savory & Crispy Heat',
                'desc' => 'Crackling roasted pork belly Manado-style, packed with separate roasted chili rica and fresh citrus dabu-dabu salsa.',
                'image' => 'https://images.unsplash.com/photo-1514944298352-f4d732142f74?auto=format&fit=crop&w=900&q=85'
            ],
            [
                'id' => 6,
                'name' => 'Pork Tinoransak Bamboo Spice',
                'category' => 'non-halal',
                'badge' => 'Non-Halal • Dedicated Kitchen',
                'price' => 140000,
                'weight' => '450g (3-4 Servings)',
                'spicy' => 'Green Herb Heat',
                'desc' => 'Heritage Minahasan pork dish cooked with coarse green chili, ginger, lemongrass, lime leaves, and authentic charred bamboo aroma.',
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=900&q=85'
            ]
        ];

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

        return view('home', compact('menus', 'shippingZones', 'testimonials'));
    }
}