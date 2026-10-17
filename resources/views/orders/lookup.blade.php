@extends('layouts.app')

@section('title', 'Track Your Order — BAKU DAPA')

@section('content')
<section class="py-20">
    <div class="max-w-xl mx-auto px-4 sm:px-6">
        <div class="glass-card rounded-3xl p-8 text-center space-y-6">
            <span class="font-label text-xs uppercase tracking-[0.2em] text-bd-gold">Order Tracker</span>
            <h1 class="font-display text-3xl font-bold text-bd-text">Track Your Baku Dapa Order</h1>
            <p class="text-sm text-bd-muted">
                Enter your Order Code (e.g. <code class="text-bd-gold">BD-ABC123</code>) to view live kitchen telemetry or upload your payment receipt.
            </p>

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-red-500/20 border border-red-400/40 text-xs text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('order.lookup') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="code" required placeholder="Enter Order Code (e.g. BD-X92810)"
                       class="flex-1 px-4 py-3.5 rounded-xl bg-bd-bg border border-white/15 font-label text-sm text-bd-text uppercase focus:outline-none focus:border-bd-cta">
                <button type="submit" class="px-6 py-3.5 rounded-xl bg-bd-cta hover:bg-bd-cta-hover text-white font-display font-bold text-sm transition">
                    Track Now →
                </button>
            </form>
        </div>
    </div>
</section>
@endsection