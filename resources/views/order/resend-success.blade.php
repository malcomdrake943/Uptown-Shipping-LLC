@extends('layouts.app')
@section('title', 'Tracking Link Sent')
@section('content')
<div class="max-w-lg mx-auto px-4 text-center animate-slide-up">
    <div class="glass-light rounded-3xl p-10 shadow-2xl">
        <div class="w-20 h-20 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-display font-bold text-gray-900 mb-3">Check Your Email</h1>
        <p class="text-gray-500 mb-8">We've sent a fresh tracking link to your email address. It may take a few minutes to arrive.</p>
        <a href="{{ route('order.index') }}" class="btn-primary text-white px-8 py-4 rounded-2xl font-bold inline-block">
            ← Home
        </a>
    </div>
</div>
@endsection
