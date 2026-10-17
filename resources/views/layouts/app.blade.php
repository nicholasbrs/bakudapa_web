<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BAKU DAPA — Premium Manadonese Culinary')</title>

    <meta name="theme-color" content="#15100D">

    <!-- Fonts: Fraunces (judul serif), Inter (isi & label), JetBrains Mono (kode pesanan & nomor rekening) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Warna diambil dari variabel CSS di public/css/custom.css (:root --bd-*).
        // Ganti tema cukup dengan mengubah nilai di sana.
        window.tailwind = window.tailwind || {};
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bd: {
                            bg: 'rgb(var(--bd-bg) / <alpha-value>)',
                            card: 'rgb(var(--bd-card) / <alpha-value>)',
                            select: 'rgb(var(--bd-select) / <alpha-value>)',
                            text: 'rgb(var(--bd-text) / <alpha-value>)',
                            muted: 'rgb(var(--bd-muted) / <alpha-value>)',
                            cta: 'rgb(var(--bd-cta) / <alpha-value>)',
                            'cta-hover': 'rgb(var(--bd-cta-hover) / <alpha-value>)',
                            'cta-deep': 'rgb(var(--bd-cta-deep) / <alpha-value>)',
                            gold: 'rgb(var(--bd-gold) / <alpha-value>)',
                            halal: 'rgb(var(--bd-halal) / <alpha-value>)',
                            rica: 'rgb(var(--bd-rica) / <alpha-value>)',
                        }
                    }
                }
            }
        };
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @stack('styles')
</head>
<body class="bg-bd-bg text-bd-text antialiased selection:bg-bd-cta selection:text-white relative min-h-screen">

    <!-- 2. ANIMATED AMBIENT AURORA GLOW BACKGROUND (filter: blur(120px)) -->
    <div class="aurora-container" id="aurora-parallax-wrapper" aria-hidden="true">

        <!-- Moving & Breathing Aurora Orbs (nuansa rica hangat) -->
        <div class="aurora-hunter aurora-orb-1 w-[600px] h-[600px] -top-32 -left-24" data-speed="0.03"></div>
        <div class="aurora-sage aurora-orb-2 w-[520px] h-[520px] top-[10%] right-[-8%]" data-speed="-0.04"></div>
        <div class="aurora-hunter aurora-orb-3 w-[680px] h-[680px] top-[45%] left-[12%] opacity-45" data-speed="0.025"></div>
        <div class="aurora-sage aurora-orb-4 w-[500px] h-[500px] bottom-[5%] right-[10%] opacity-35" data-speed="-0.03"></div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="relative z-10 flex flex-col min-h-screen">
        @include('partials.navbar')

        <main class="flex-grow">
            @yield('content')
        </main>

        @include('partials.footer')
    </div>

    <script>window.BD = { orderUrl: @json(route('order.create')) };</script>

    <!-- Custom JS (baku-dapa.js) -->
    <script src="{{ asset('js/baku-dapa.js') }}"></script>
    @stack('scripts')
</body>
</html>