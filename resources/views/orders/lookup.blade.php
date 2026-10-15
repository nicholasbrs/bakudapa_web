@extends('layouts.app')

@section('title', 'Track Your Order — BAKU DAPA')

@section('content')
<section class="py-20">
    <div class="max-w-xl mx-auto px-4 sm:px-6">
        <div class="glass-card rounded-3xl p-8 text-center space-y-6">
            <span class="font-mono-ai text-xs uppercase tracking-[0.2em] text-[#86EFAC]">Order Tracker</span>
            <h1 class="font-display-ai text-3xl font-bold text-white">Track Your Baku Dapa Order</h1>
            <p class="text-sm text-[#94A898]">
                Enter your Order Code (e.g. <code class="text-[#E2C07D]">BD-ABC123</code>) to view live kitchen telemetry or upload your payment receipt.
            </p>

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-red-500/20 border border-red-400/40 text-xs text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('order.lookup') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="code" required placeholder="Enter Order Code (e.g. BD-X92810)"
                       class="flex-1 px-4 py-3.5 rounded-xl bg-[#050806] border border-white/15 font-mono-ai text-sm text-white uppercase focus:outline-none focus:border-[#4E8752]">
                <button type="submit" class="px-6 py-3.5 rounded-xl bg-[#4E8752] hover:bg-[#3d6e40] text-white font-display-ai font-bold text-sm transition">
                    Track Now →
                </button>
            </form>
        </div>
    </div>
</section>
@endsection