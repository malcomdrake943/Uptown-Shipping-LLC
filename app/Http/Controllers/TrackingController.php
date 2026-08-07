<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Notifications\OrderConfirmedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class TrackingController extends Controller
{
    /**
     * Display the tracking page based on order ID/number only (public, unsigned).
     */
    public function trackPublic(Request $request): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'order_number' => 'required|string',
        ]);

        $order = Order::where('order_number', trim($request->order_number))->first();

        if (! $order) {
            return back()->withErrors(['order_number' => 'No order found with that order number.']);
        }

        $statusHistory = $order->statusHistory()->orderBy('created_at')->get();
        $isPublic = true;

        return view('order.track', compact('order', 'statusHistory', 'isPublic'));
    }

    /**
     * Display the tracking page for a signed URL.
     */
    public function show(Request $request, Order $order): \Illuminate\View\View
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This tracking link has expired or is invalid.');
        }

        $statusHistory = $order->statusHistory()->orderBy('created_at')->get();

        return view('order.track', compact('order', 'statusHistory'));
    }

    /**
     * Resend the magic link to the customer's email if it matches the order.
     */
    public function resend(Request $request): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'order_number' => 'required|string',
            'email'        => 'required|email',
        ]);

        $order = Order::where('order_number', $request->order_number)
            ->where('customer_email', $request->email)
            ->first();

        if (! $order) {
            return back()->withErrors(['email' => 'No order found matching that email and order number.']);
        }

        $order->notify(new OrderConfirmedNotification($order));

        return redirect()->route('order.resend-success');
    }

    public function resendSuccess(): \Illuminate\View\View
    {
        return view('order.resend-success');
    }
}
