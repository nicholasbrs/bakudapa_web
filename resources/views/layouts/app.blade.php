<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BAKU DAPA — Premium Manadonese Culinary')</title>

    <!-- AI-Friendly Google Fonts: Space Grotesk, Inter, JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @stack('styles')
</head>
<body class="bg-[#050806] text-[#F2F7F4] antialiased selection:bg-[#4E8752] selection:text-white relative min-h-screen">

    <!-- 2. ANIMATED AMBIENT AURORA GLOW BACKGROUND (filter: blur(120px)) -->
    <div class="aurora-container" id="aurora-parallax-wrapper" aria-hidden="true">
        <!-- Subtle AI Tech Grid -->
        <div class="ai-grid-overlay"></div>

        <!-- Moving & Breathing Aurora Orbs (#1B4323 & #4E8752) -->
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