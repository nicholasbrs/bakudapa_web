<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private string $storageFile = 'orders.json';

    // Daftar Menu (satu-satunya sumber data menu; halaman home tidak lagi menampilkan menu)
    private function getMenus(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Cakalang Fufu Rabe Rica',
                'category' => 'HALAL',
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
                'category' => 'HALAL',
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
                'category' => 'HALAL',
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
                'category' => 'HALAL',
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
                'category' => 'NON-HALAL',
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
                'category' => 'NON-HALAL',
                'badge' => 'Non-Halal • Dedicated Kitchen',
                'price' => 140000,
                'weight' => '450g (3-4 Servings)',
                'spicy' => 'Green Herb Heat',
                'desc' => 'Heritage Minahasan pork dish cooked with coarse green chili, ginger, lemongrass, lime leaves, and authentic charred bamboo aroma.',
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=900&q=85'
            ]
        ];
    }

    // Data toko / titik pickup (diatur di config/bakudapa.php)
    private function getShop(): array
    {
        return config('bakudapa.shop');
    }

    // Daftar Metode Pembayaran Manual (QRIS, Mandiri, BCA, BRI, BNI)
    private function getPaymentMethods(): array
    {
        return [
            'QRIS' => [
                'name' => 'QRIS (All E-Wallets & Mobile Banking)',
                'type' => 'qris',
                'account_number' => 'NMID: ID1026000889123',
                'account_holder' => 'BAKU DAPA CULINARY LAB',
                'instructions' => 'Scan the QR code below using GoPay, OVO, Dana, ShopeePay, or any Mobile Banking app.'
            ],
            'BCA' => [
                'name' => 'Bank Central Asia (BCA)',
                'type' => 'bank',
                'account_number' => '8720-4918-22',
                'account_holder' => 'PT BAKU DAPA KULINER',
                'instructions' => 'Transfer the exact total amount to our BCA account and upload your transfer receipt.'
            ],
            'MANDIRI' => [
                'name' => 'Bank Mandiri',
                'type' => 'bank',
                'account_number' => '124-00-9882716-4',
                'account_holder' => 'PT BAKU DAPA KULINER',
                'instructions' => 'Transfer via Livin by Mandiri or ATM to the account number above.'
            ],
            'BRI' => [
                'name' => 'Bank Rakyat Indonesia (BRI)',
                'type' => 'bank',
                'account_number' => '0341-01-002981-30-7',
                'account_holder' => 'PT BAKU DAPA KULINER',
                'instructions' => 'Transfer via BRImo or ATM BRI and save the transaction receipt.'
            ],
            'BNI' => [
                'name' => 'Bank Negara Indonesia (BNI)',
                'type' => 'bank',
                'account_number' => '0981-2736-45',
                'account_holder' => 'PT BAKU DAPA KULINER',
                'instructions' => 'Transfer via Wondr by BNI or Mobile Banking and upload your receipt.'
            ],
        ];
    }

    private function loadOrders(): array
    {
        if (!Storage::disk('local')->exists($this->storageFile)) {
            return [];
        }
        $json = Storage::disk('local')->get($this->storageFile);
        return json_decode($json, true) ?? [];
    }

    private function saveOrders(array $orders): void
    {
        Storage::disk('local')->put($this->storageFile, json_encode($orders, JSON_PRETTY_PRINT));
    }

    // 1. Halaman Checkout & Pemesanan (/order)
    public function create()
    {
        $menus = $this->getMenus();
        $paymentMethods = $this->getPaymentMethods();
        $shop = $this->getShop();
        $deliveryFee = (int) config('bakudapa.delivery_fee');
        return view('orders.create', compact('menus', 'paymentMethods', 'shop', 'deliveryFee'));
    }

    // 2. Simpan Pesanan Baru (POST /order)
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:120',
            'customer_phone' => 'required|string|max:30',
            'customer_email' => 'nullable|email|max:120',
            'fulfillment_type' => 'required|in:delivery,pickup',
            'delivery_address' => 'required_if:fulfillment_type,delivery|nullable|string|max:500',
            'delivery_city' => 'required_if:fulfillment_type,delivery|nullable|string|max:100',
            'delivery_lat' => 'required_if:fulfillment_type,delivery|nullable|numeric|between:-90,90',
            'delivery_lng' => 'required_if:fulfillment_type,delivery|nullable|numeric|between:-180,180',
            'delivery_place' => 'nullable|string|max:400',
            'address_detail' => 'nullable|string|max:200',
            'notes' => 'nullable|string|max:500',
            'payment_method' => 'required|in:QRIS,BCA,MANDIRI,BRI,BNI',
            'cart_json' => 'required|string'
        ], [
            'delivery_lat.required_if' => 'Please pin your delivery location on the map.',
            'delivery_lng.required_if' => 'Please pin your delivery location on the map.',
        ]);

        $items = json_decode($request->cart_json, true);
        if (!is_array($items) || count($items) === 0) {
            return back()->withErrors(['cart' => 'Please select at least 1 dish before placing an order.'])->withInput();
        }

        $subtotal = collect($items)->sum(fn($i) => ((int)$i['price']) * ((int)$i['qty']));
        $shippingFee = $request->fulfillment_type === 'delivery' ? (int) config('bakudapa.delivery_fee') : 0;
        $shop = $this->getShop();
        $isDelivery = $request->fulfillment_type === 'delivery';
        $uniqueCode = rand(101, 499); // Kode unik 3 digit untuk mempermudah cek mutasi manual
        $grandTotal = $subtotal + $shippingFee + $uniqueCode;

        $orderCode = 'BD-' . strtoupper(Str::random(3)) . rand(100, 999);

        $orders = $this->loadOrders();

        $orders[$orderCode] = [
            'code' => $orderCode,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'fulfillment_type' => $request->fulfillment_type, // 'delivery' or 'pickup'
            'delivery_city' => $isDelivery ? $request->delivery_city : null,
            'delivery_address' => $isDelivery
                ? $request->delivery_address
                : 'PICKUP AT ' . strtoupper($shop['name']) . ': ' . $shop['address'],
            'address_detail' => $isDelivery ? $request->address_detail : null,
            // Titik pin dari peta (delivery) atau koordinat toko (pickup)
            'delivery_lat' => $isDelivery ? round((float) $request->delivery_lat, 7) : (float) $shop['lat'],
            'delivery_lng' => $isDelivery ? round((float) $request->delivery_lng, 7) : (float) $shop['lng'],
            'delivery_place' => $isDelivery ? $request->delivery_place : null,
            'notes' => $request->notes,
            'payment_method' => $request->payment_method,
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'unique_code' => $uniqueCode,
            'grand_total' => $grandTotal,
            // Status: unpaid, waiting_verification, paid, processing, delivering, ready_for_pickup, completed, rejected
            'status' => 'unpaid',
            'payment_proof' => null,
            'payment_uploaded_at' => null,
            'admin_note' => null,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];

        $this->saveOrders($orders);

        return redirect()->route('order.track', ['code' => $orderCode])
            ->with('success', 'Order created! Please complete your payment and upload the receipt below.');
    }

    // 3. Halaman Pencarian Kode Order (/track)
    public function lookup(Request $request)
    {
        if ($request->filled('code')) {
            $code = strtoupper(trim($request->code));
            return redirect()->route('order.track', ['code' => $code]);
        }
        return view('orders.lookup');
    }

    // 4. Halaman Pembayaran & Pelacakan Status (/track/{code})
    public function track(string $code)
    {
        $code = strtoupper($code);
        $orders = $this->loadOrders();

        if (!isset($orders[$code])) {
            return redirect()->route('order.lookup')
                ->withErrors(['not_found' => "Order code {$code} was not found. Please check your code."]);
        }

        $order = $orders[$code];
        $paymentMethods = $this->getPaymentMethods();
        $selectedPayment = $paymentMethods[$order['payment_method']] ?? $paymentMethods['QRIS'];

        $shop = $this->getShop();

        return view('orders.track', compact('order', 'selectedPayment', 'paymentMethods', 'shop'));
    }

    // 5. Upload Bukti Pembayaran
    public function uploadProof(Request $request, string $code)
    {
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'payment_method' => 'nullable|in:QRIS,BCA,MANDIRI,BRI,BNI'
        ]);

        $code = strtoupper($code);
        $orders = $this->loadOrders();

        if (!isset($orders[$code])) {
            abort(404);
        }

        // Hapus bukti lama jika ada
        if (!empty($orders[$code]['payment_proof'])) {
            Storage::disk('public')->delete($orders[$code]['payment_proof']);
        }

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        if ($request->filled('payment_method')) {
            $orders[$code]['payment_method'] = $request->payment_method;
        }

        $orders[$code]['payment_proof'] = $path;
        $orders[$code]['payment_uploaded_at'] = now()->toDateTimeString();
        $orders[$code]['status'] = 'waiting_verification';
        $orders[$code]['updated_at'] = now()->toDateTimeString();

        $this->saveOrders($orders);

        return redirect()->route('order.track', ['code' => $code])
            ->with('success', 'Payment proof uploaded successfully! Waiting for Admin verification.');
    }

    // 6. Withdraw (Tarik Kembali) Bukti Pembayaran jika salah upload
    public function withdrawProof(string $code)
    {
        $code = strtoupper($code);
        $orders = $this->loadOrders();

        if (!isset($orders[$code])) {
            abort(404);
        }

        // Hanya bisa withdraw jika belum diverifikasi (masih waiting_verification atau rejected)
        if (in_array($orders[$code]['status'], ['waiting_verification', 'rejected', 'unpaid'])) {
            if (!empty($orders[$code]['payment_proof'])) {
                Storage::disk('public')->delete($orders[$code]['payment_proof']);
            }

            $orders[$code]['payment_proof'] = null;
            $orders[$code]['payment_uploaded_at'] = null;
            $orders[$code]['status'] = 'unpaid';
            $orders[$code]['updated_at'] = now()->toDateTimeString();

            $this->saveOrders($orders);

            return redirect()->route('order.track', ['code' => $code])
                ->with('success', 'Payment proof withdrawn. You can now upload a new receipt.');
        }

        return redirect()->route('order.track', ['code' => $code])
            ->withErrors(['locked' => 'Payment has already been verified by Admin and cannot be withdrawn.']);
    }

    // 7. Halaman Admin Dashboard (/admin/orders)
    public function adminIndex()
    {
        $orders = collect($this->loadOrders())->sortByDesc('created_at')->toArray();
        return view('orders.admin', compact('orders'));
    }

    // 8. Action Admin Verifikasi & Update Status
    public function adminUpdateStatus(Request $request, string $code)
    {
        $request->validate([
            'status' => 'required|in:unpaid,waiting_verification,paid,processing,delivering,ready_for_pickup,completed,rejected',
            'admin_note' => 'nullable|string|max:300'
        ]);

        $code = strtoupper($code);
        $orders = $this->loadOrders();

        if (!isset($orders[$code])) {
            abort(404);
        }

        $orders[$code]['status'] = $request->status;
        if ($request->filled('admin_note')) {
            $orders[$code]['admin_note'] = $request->admin_note;
        }
        $orders[$code]['updated_at'] = now()->toDateTimeString();

        $this->saveOrders($orders);

        return redirect()->route('admin.orders')
            ->with('success', "Order {$code} status updated to " . strtoupper(str_replace('_', ' ', $request->status)));
    }
}