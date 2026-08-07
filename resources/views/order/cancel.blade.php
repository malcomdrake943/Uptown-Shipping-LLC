@extends('layouts.app')
@section('title', 'Payment Cancelled')
@section('content')
<div class="max-w-lg mx-auto px-4 text-center animate-slide-up">
    <div class="glass-light rounded-3xl p-10 shadow-2xl">
        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-display font-bold text-gray-900 mb-3">Payment Cancelled</h1>
        <p class="text-gray-500 mb-8">Your payment was cancelled and you have not been charged. Your order was not placed.</p>
        <a href="{{ route('order.index') }}" class="btn-primary text-white px-8 py-4 rounded-2xl font-bold inline-block">
            ← Try Again
        </a>
    </div>
</div>
@endsection
