@extends('layouts.app')

@section('title', $aboutTitle)
@section('meta_description', Str::limit(strip_tags($aboutSubtitle), 150))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 animate-fade-in">
    
    <!-- Hero Header -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-2 text-white/70 text-xs font-medium mb-6 border border-white/20">
            <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></span>
            Global Purchase Forwarding
        </div>
        <h1 class="text-4xl md:text-5xl font-display font-extrabold text-white leading-tight mb-4">
            {{ $aboutTitle }}
        </h1>
        <p class="text-white/70 text-lg md:text-xl max-w-2xl mx-auto font-light">
            {{ $aboutSubtitle }}
        </p>
    </div>

    <!-- Main Overview Card -->
    <div class="glass-light rounded-3xl p-8 md:p-12 shadow-2xl mb-12 animate-slide-up">
        <h2 class="text-2xl md:text-3xl font-display font-bold text-gray-900 mb-6 flex items-center gap-3">
            <span class="w-3 h-8 bg-brand-600 rounded-full inline-block"></span>
            Our Story & Service
        </h2>
        
        <div class="prose prose-lg text-gray-700 leading-relaxed space-y-4">
            @foreach(explode("\n\n", $aboutContent) as $paragraph)
                @if(trim($paragraph))
                    <p>{{ trim($paragraph) }}</p>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Mission & Vision Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <!-- Mission -->
        <div class="glass p-8 rounded-3xl border border-white/15 hover:border-white/30 transition-all duration-300 group">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400/20 to-amber-600/20 border border-amber-400/30 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h3 class="text-xl font-display font-bold text-white mb-3">Our Mission</h3>
            <p class="text-white/70 text-sm leading-relaxed">
                {{ $aboutMission }}
            </p>
        </div>

        <!-- Vision -->
        <div class="glass p-8 rounded-3xl border border-white/15 hover:border-white/30 transition-all duration-300 group">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-400/20 to-blue-600/20 border border-blue-400/30 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
            <h3 class="text-xl font-display font-bold text-white mb-3">Our Vision</h3>
            <p class="text-white/70 text-sm leading-relaxed">
                {{ $aboutVision }}
            </p>
        </div>
    </div>

    <!-- Core Features Grid -->
    <div class="glass-light rounded-3xl p-8 md:p-10 mb-12 shadow-xl">
        <h3 class="text-xl font-display font-bold text-gray-900 mb-8 text-center">Why Customers Trust Jubilee Direct</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center p-4">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-600 font-bold text-xl mb-3">1</div>
                <h4 class="font-bold text-gray-900 text-sm mb-1">Global Procurement</h4>
                <p class="text-xs text-gray-500">Buy directly from Amazon, eBay, Best Buy, and any major online retailer.</p>
            </div>

            <div class="text-center p-4">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-600 font-bold text-xl mb-3">2</div>
                <h4 class="font-bold text-gray-900 text-sm mb-1">Transparent Pricing</h4>
                <p class="text-xs text-gray-500">Upfront item pricing, exchange rates, and delivery fee calculation before payment.</p>
            </div>

            <div class="text-center p-4">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-600 font-bold text-xl mb-3">3</div>
                <h4 class="font-bold text-gray-900 text-sm mb-1">Live Order Tracking</h4>
                <p class="text-xs text-gray-500">Track package status from store checkout to final door delivery with magic links.</p>
            </div>

            <div class="text-center p-4">
                <div class="w-12 h-12 mx-auto rounded-2xl bg-brand-50 border border-brand-200 flex items-center justify-center text-brand-600 font-bold text-xl mb-3">4</div>
                <h4 class="font-bold text-gray-900 text-sm mb-1">Flexible Payments</h4>
                <p class="text-xs text-gray-500">Pay securely via Credit/Debit Cards, Stripe, or Mobile Money.</p>
            </div>
        </div>
    </div>

    <!-- Shipping Options Showcase -->
    <div class="glass-light rounded-3xl p-8 md:p-10 mb-12 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 border-b border-gray-200/70 pb-5">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-brand-600 bg-brand-50 border border-brand-200 px-3 py-1 rounded-full">Flexible Speeds</span>
                <h3 class="text-xl md:text-2xl font-display font-bold text-gray-900 mt-2">Available Shipping Methods</h3>
                <p class="text-xs md:text-sm text-gray-500 mt-1">Our system calculates the final shipping rate based on your product link and package weight.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white/80 p-5 rounded-2xl border border-brand-100">
                <div class="text-2xl mb-2">⚡</div>
                <h4 class="font-bold text-gray-900 text-sm">Express Air</h4>
                <p class="text-amber-600 font-extrabold text-xs mb-1.5">3–7 Days</p>
                <p class="text-xs text-gray-500 mb-3">Expedited courier delivery for urgent purchases.</p>
                <span class="text-[11px] px-2.5 py-1 rounded-md bg-gray-100 font-semibold text-gray-700">Quote Required</span>
            </div>
            <div class="bg-white/80 p-5 rounded-2xl border border-brand-100">
                <div class="text-2xl mb-2">✈️</div>
                <h4 class="font-bold text-gray-900 text-sm">Standard Air</h4>
                <p class="text-blue-600 font-extrabold text-xs mb-1.5">7–14 Days</p>
                <p class="text-xs text-gray-500 mb-3">Reliable global air freight balancing speed and economy.</p>
                <span class="text-[11px] px-2.5 py-1 rounded-md bg-gray-100 font-semibold text-gray-700">Quote Required</span>
            </div>
            <div class="bg-white/80 p-5 rounded-2xl border border-brand-100">
                <div class="text-2xl mb-2">🚢</div>
                <h4 class="font-bold text-gray-900 text-sm">Sea Freight</h4>
                <p class="text-emerald-600 font-extrabold text-xs mb-1.5">4–8 Weeks</p>
                <p class="text-xs text-gray-500 mb-3">Economical ocean container freight for bulk & heavy cargo.</p>
                <span class="text-[11px] px-2.5 py-1 rounded-md bg-gray-100 font-semibold text-gray-700">Quote Required</span>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="glass p-8 md:p-10 rounded-3xl border border-white/20 text-center">
        <h3 class="text-2xl font-display font-bold text-white mb-2">Ready to Shop Overseas?</h3>
        <p class="text-white/60 text-sm mb-6 max-w-lg mx-auto">Paste any item link and let Jubilee Direct handle purchasing, customs, and delivery for you.</p>
        <a href="{{ route('order.index') }}" class="inline-block btn-primary text-white font-semibold px-8 py-3.5 rounded-xl shadow-lg transition-transform hover:scale-105">
            Start Your Order Now →
        </a>
    </div>
</div>
@endsection
