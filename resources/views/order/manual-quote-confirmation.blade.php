@extends('layouts.app')
@section('title', 'Manual Quote Submitted')
@section('content')
<div class="max-w-lg mx-auto px-4 text-center animate-slide-up">
    <div class="glass-light rounded-3xl p-10 shadow-2xl">
        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="text-4xl">📋</span>
        </div>
        <h1 class="text-3xl font-display font-bold text-gray-900 mb-3">Quote Request Submitted!</h1>
        <p class="text-gray-600 mb-4">Because your item is <strong>oversized</strong>, our team needs to calculate exact shipping costs before collecting payment.</p>
        <p class="text-gray-500 text-sm mb-8">We'll review your request and email you a payment link within <strong>1–2 business days</strong>. No payment has been taken.</p>
        <a href="{{ route('order.index') }}" class="btn-primary text-white px-8 py-4 rounded-2xl font-bold inline-block">
            Place Another Order
        </a>
    </div>
</div>
@endsection
