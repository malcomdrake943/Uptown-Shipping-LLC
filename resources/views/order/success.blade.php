@extends('layouts.app')
@section('title', 'Order Confirmed')
@section('content')
<div class="max-w-lg mx-auto px-4 text-center animate-slide-up">
    <div class="glass-light rounded-3xl p-10 shadow-2xl">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        @php
            $isMomo = request('payment_method') === 'momo' || str_starts_with($order?->stripe_payment_intent_id ?? '', 'momo_');
            $supportPhone = config('app.support_phone', '555-123-4567');
        @endphp
        <h1 class="text-3xl font-display font-bold text-gray-900 mb-3">Order Confirmed! 🎉</h1>
        @if($order)
            <div class="inline-block bg-brand-50 border border-brand-200 rounded-xl px-5 py-2 mb-4">
                <p class="text-brand-700 font-bold text-lg">{{ $order->order_number }}</p>
            </div>
            @if($isMomo)
                <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-4 mb-5 text-left text-xs leading-relaxed space-y-2">
                    <p class="font-bold text-sm text-amber-950 flex items-center gap-2">
                        <span>📱</span> Mobile Money Payment
                    </p>
                    <p>
                        To pay with mobile money, contact our customer support team on this number 
                        <a href="tel:{{ str_replace(['-', ' '], '', $supportPhone) }}" class="font-bold underline text-brand-700">{{ $supportPhone }}</a>, 
                        and you will be provided with the mobile money details. In the meantime, your order <strong>{{ $order->order_number }}</strong> has been submitted for processing.
                    </p>
                </div>
            @else
                <p class="text-gray-600 mb-2">Payment of <span class="font-bold text-gray-900">${{ number_format($order->total_charged, 2) }}</span> received.</p>
            @endif
        @endif
        <p class="text-gray-500 text-sm mb-8">We've sent a confirmation email with your magic tracking link. Check your inbox at <strong>{{ $order?->customer_email }}</strong>.</p>

        <div class="space-y-3 text-left bg-gray-50 rounded-2xl p-5 mb-8">
            <h3 class="font-semibold text-gray-700 text-sm mb-3">What happens next?</h3>
            @foreach([
                ['🔍', 'Our team reviews your order', 'Usually within a few hours'],
                ['🛒', 'We purchase your item', 'Directly from the retailer'],
                ['📦', 'Item is shipped to you', 'With a tracking number via email'],
                ['🏠', 'Delivered to your door', 'Right to your shipping address'],
            ] as [$icon, $title, $subtitle])
            <div class="flex items-start gap-3">
                <span class="text-lg">{{ $icon }}</span>
                <div>
                    <p class="text-gray-800 text-sm font-medium">{{ $title }}</p>
                    <p class="text-gray-500 text-xs">{{ $subtitle }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <a href="{{ route('order.index') }}"
           class="btn-primary text-white px-8 py-4 rounded-2xl font-bold inline-flex items-center gap-2">
            Place Another Order
        </a>
    </div>
</div>

@if($order)
    <iframe src="{{ route('order.receipt.download', $order->id) }}" style="display:none;"></iframe>
@endif

@endsection
