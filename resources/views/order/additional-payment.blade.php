@extends('layouts.app')
@section('title', 'Additional Payment Required')
@section('content')
<div class="max-w-lg mx-auto px-4 animate-slide-up">
    <div class="glass-light rounded-3xl p-8 shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-display font-bold text-gray-900 mb-2">Additional Payment Required</h1>
            <p class="text-gray-500 text-sm">Order {{ $order->order_number }}</p>
        </div>

        <div class="bg-gray-50 rounded-2xl p-5 mb-6">
            <p class="text-gray-600 text-sm">The actual cost of your item was slightly different from your estimate. An additional payment is needed to complete your order.</p>
        </div>

        <div id="payment-element" class="mb-6 p-4 border border-gray-200 rounded-xl min-h-16"></div>
        <div id="payment-message" class="text-red-600 text-sm mb-4 hidden"></div>

        <button id="pay-btn" class="btn-primary w-full text-white py-4 rounded-2xl font-bold">
            Complete Payment
        </button>
    </div>
</div>

<script>
(function() {
    const stripe = Stripe('{{ config('cashier.key') }}');
    const clientSecret = '{{ $clientSecret }}';

    const elements = stripe.elements({ clientSecret });
    const paymentElement = elements.create('payment');
    paymentElement.mount('#payment-element');

    document.getElementById('pay-btn').addEventListener('click', async () => {
        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: '{{ route("order.success") }}?session_id=additional',
            }
        });

        if (error) {
            const msg = document.getElementById('payment-message');
            msg.textContent = error.message;
            msg.classList.remove('hidden');
        }
    });
})();
</script>
@endsection
