<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TrackingController;
use Illuminate\Support\Facades\Route;

// ── Public Pages ──────────────────────────────────────────────────────────────
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// ── Redirect root to order form ──────────────────────────────────────────────
Route::get('/', fn () => redirect()->route('order.index'));


// ── Public Order Flow ─────────────────────────────────────────────────────────
Route::prefix('order')->name('order.')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');

    // Step 1: Fetch product (dispatches job)
    Route::post('/fetch-product', [OrderController::class, 'fetchProduct'])->name('fetch-product');
    Route::get('/fetch-product/{jobKey}', [OrderController::class, 'fetchProductResult'])->name('fetch-product.result');

    // Step 1: Server-side fee calculation
    Route::post('/calculate-fees', [OrderController::class, 'calculateFees'])->name('calculate-fees');

    // Step 3: Create Stripe Checkout Session
    Route::post('/create-session', [OrderController::class, 'createSession'])->name('create-session');

    // Card Scan & Custom Payment
    Route::post('/scan/initiate', [OrderController::class, 'initiateCardScan'])->name('scan.initiate');
    Route::get('/scan/status/{scanId}', [OrderController::class, 'getScanStatus'])->name('scan.status');
    Route::post('/charge', [OrderController::class, 'processPayment'])->name('charge');
    Route::post('/mobile-money', [OrderController::class, 'processMobileMoney'])->name('mobile-money');

    // Stripe redirect pages
    Route::get('/success', [OrderController::class, 'success'])->name('success');
    Route::get('/cancel', [OrderController::class, 'cancel'])->name('cancel');

    // Manual quote confirmation (oversized)
    Route::get('/manual-quote-confirmation', [OrderController::class, 'manualQuoteConfirmation'])->name('manual-quote-confirmation');

    // Additional payment page (reconciliation)
    Route::get('/additional-payment/{order}', function (App\Models\Order $order, \Illuminate\Http\Request $request) {
        $clientSecret = $request->query('secret');
        return view('order.additional-payment', compact('order', 'clientSecret'));
    })->name('additional-payment');
});

// ── Tracking Page (magic link) ────────────────────────────────────────────────
Route::get('/track-order', [TrackingController::class, 'trackPublic'])
    ->name('order.track-public');

Route::get('/track/{order}', [TrackingController::class, 'show'])
    ->name('order.track')
    ->middleware('signed');

Route::post('/track/resend', [TrackingController::class, 'resend'])->name('order.resend');
Route::get('/track/resend-success', [TrackingController::class, 'resendSuccess'])->name('order.resend-success');

// ── Stripe Webhook (exclude CSRF) ─────────────────────────────────────────────
// Note: Excluded from CSRF via bootstrap/app.php or by using withoutMiddleware below
Route::post('/stripe/webhook', [OrderController::class, 'webhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);
