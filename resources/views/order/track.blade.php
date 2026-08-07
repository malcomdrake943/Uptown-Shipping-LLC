@extends('layouts.app')
@section('title', 'Track Order ' . $order->order_number)
@section('meta_description', 'Track your Uptown Towing & Shipping LLC order ' . $order->order_number . '.')

@section('content')
<div class="max-w-2xl mx-auto px-4">
    <!-- Order Header -->
    <div class="text-center mb-8 animate-fade-in">
        <p class="text-white/50 text-sm mb-1">Order Tracking</p>
        <h1 class="text-3xl font-display font-bold text-white mb-2">{{ $order->order_number }}</h1>
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-sm font-semibold
            @php
            $statusColors = [
                'pending'      => 'bg-gray-100 text-gray-700',
                'under_review' => 'bg-amber-100 text-amber-700',
                'purchased'    => 'bg-blue-100 text-blue-700',
                'shipped'      => 'bg-brand-100 text-brand-700',
                'delivered'    => 'bg-green-100 text-green-700',
                'cancelled'    => 'bg-red-100 text-red-700',
                'refunded'     => 'bg-red-100 text-red-700',
            ];
            @endphp
            {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-700' }}">
            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
        </div>
    </div>

    <div class="glass-light rounded-3xl shadow-2xl overflow-hidden animate-slide-up">
        <!-- Product Info -->
        <div class="p-6 border-b border-gray-100">
            <h2 class="font-display font-bold text-gray-900 mb-3">Order Details</h2>
            <div class="flex items-start gap-4">
                @if($order->product_image_url)
                <img src="{{ $order->product_image_url }}" alt="{{ $order->product_name }}"
                     class="w-16 h-16 object-cover rounded-xl border border-gray-100 flex-shrink-0"
                     onerror="this.style.display='none'">
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800">{{ $order->product_name ?? 'Product' }}</p>
                    @if(empty($isPublic))
                    <a href="{{ $order->product_url }}" target="_blank" class="text-brand-600 text-xs hover:underline break-all">
                        {{ Str::limit($order->product_url, 60) }}
                    </a>
                    @endif
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">Qty: {{ $order->quantity }}</span>
                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full capitalize">{{ $order->size_tier }}</span>
                        @if(empty($isPublic))
                        <span class="bg-brand-50 text-brand-700 text-xs px-2 py-0.5 rounded-full font-semibold">${{ number_format($order->total_charged, 2) }} paid</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tracking Number -->
        @if($order->tracking_number)
        <div class="p-6 border-b border-gray-100 bg-brand-50">
            <h2 class="font-display font-bold text-gray-900 mb-3">📦 Tracking Information</h2>
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <p class="text-sm text-gray-500">{{ $order->tracking_carrier }}</p>
                    <p class="font-bold text-gray-800 text-lg font-mono">{{ $order->tracking_number }}</p>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ $order->tracking_number }}')"
                        class="px-4 py-2 bg-brand-100 hover:bg-brand-200 text-brand-700 text-sm font-medium rounded-xl transition-colors">
                    Copy
                </button>
            </div>
        </div>
        @endif

        <!-- Status Timeline -->
        <div class="p-6">
            <h2 class="font-display font-bold text-gray-900 mb-5">Status History</h2>
            <div class="relative">
                <!-- Vertical line -->
                <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-gray-200"></div>

                <div class="space-y-5">
                    @forelse($statusHistory as $history)
                    <div class="flex items-start gap-4 relative">
                        <div class="status-timeline-dot flex-shrink-0 mt-0.5
                            @php
                            $dotColors = [
                                'pending'      => 'bg-gray-400 shadow-gray-200',
                                'under_review' => 'bg-amber-400 shadow-amber-200',
                                'purchased'    => 'bg-blue-500 shadow-blue-200',
                                'shipped'      => 'bg-brand-500 shadow-brand-200',
                                'delivered'    => 'bg-green-500 shadow-green-200',
                                'cancelled'    => 'bg-red-500 shadow-red-200',
                                'refunded'     => 'bg-red-500 shadow-red-200',
                            ];
                            @endphp
                            {{ $dotColors[$history->status] ?? 'bg-gray-400' }}"></div>
                        <div class="flex-1 pb-2">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <span class="font-semibold text-gray-800 text-sm capitalize">
                                    {{ ucfirst(str_replace('_', ' ', $history->status)) }}
                                </span>
                                <span class="text-gray-400 text-xs">{{ $history->created_at->format('M j, Y g:i A') }}</span>
                            </div>
                            @if($history->note)
                            <p class="text-gray-500 text-xs mt-0.5">{{ $history->note }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400 text-sm">No status history yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        @if(empty($isPublic))
        <!-- Shipping Address -->
        <div class="p-6 border-t border-gray-100 bg-gray-50">
            <h2 class="font-semibold text-gray-700 text-sm mb-2">Shipping To</h2>
            <p class="text-gray-600 text-sm">
                {{ $order->customer_name }}<br>
                {{ $order->shipping_address['line1'] }}
                @if(!empty($order->shipping_address['line2']))<br>{{ $order->shipping_address['line2'] }}@endif
                <br>{{ $order->shipping_address['city'] }},
                @if(!empty($order->shipping_address['state'])) {{ $order->shipping_address['state'] }}, @endif
                {{ $order->shipping_address['postal_code'] }}<br>
                {{ $order->shipping_address['country'] }}
            </p>
        </div>
        @endif
    </div>

    @if(empty($isPublic))
    <!-- Resend Link -->
    <div class="mt-6 text-center">
        <p class="text-white/40 text-sm">Lost your tracking link?
            <button onclick="document.getElementById('resend-modal').classList.remove('hidden')"
                    class="text-white/70 hover:text-white underline transition-colors">
                Resend it
            </button>
        </p>
    </div>
    @endif
</div>
@endsection
