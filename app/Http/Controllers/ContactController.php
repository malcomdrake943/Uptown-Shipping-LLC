<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Display the Contact Us page.
     */
    public function index(): View
    {
        $contactTitle = Setting::get('contact_title', 'Get in Touch with Jubilee Direct');
        $contactSubtitle = Setting::get('contact_subtitle', 'Have questions about an order, shipping options, or custom quotes? Our team is here to assist you.');
        $contactEmail = Setting::get('contact_email', 'support@jubileedirect.com');
        $contactPhone = Setting::get('contact_phone', '+1 (800) 555-0199');
        $contactWhatsapp = Setting::get('contact_whatsapp', '+1 (800) 555-0199');
        $contactAddress = Setting::get('contact_address', '123 Commerce Boulevard, Suite 400, New York, NY 10001, United States');
        $contactWorkingHours = Setting::get('contact_working_hours', "Monday – Friday: 9:00 AM – 6:00 PM EST\nSaturday: 10:00 AM – 4:00 PM EST\nSunday: Closed");
        $contactFormIntro = Setting::get('contact_form_intro', 'Send us a message and our procurement specialists will get back to you within 24 hours.');
        $contactShippingInfo = Setting::get('contact_shipping_info', "We offer transparent cross-border shipping solutions tailored to your cargo size and urgency:\n\n• Express Air (3–7 Days) – Expedited courier for fast doorstep delivery\n• Standard Air (7–14 Days) – Reliable and economical global air freight\n• Sea Freight (4–8 Weeks) – Best value for bulk, heavy, or oversized items\n\nOur system calculates the exact shipping charge after you submit your product link and package specifications.");

        return view('contact', compact(
            'contactTitle',
            'contactSubtitle',
            'contactEmail',
            'contactPhone',
            'contactWhatsapp',
            'contactAddress',
            'contactWorkingHours',
            'contactFormIntro',
            'contactShippingInfo'
        ));
    }

    /**
     * Handle public contact form submission.
     */
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|max:150',
            'phone'        => 'nullable|string|max:40',
            'order_number' => 'nullable|string|max:50',
            'inquiry_type' => 'required|string|max:50',
            'message'      => 'required|string|min:10|max:3000',
        ]);

        // Log inquiry for records
        Log::info('Contact Form Submission received', [
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone'        => $validated['phone'] ?? null,
            'order_number' => $validated['order_number'] ?? null,
            'inquiry_type' => $validated['inquiry_type'],
            'message'      => $validated['message'],
            'ip'           => $request->ip(),
        ]);

        return redirect()->route('contact')->with('contact_success', 'Thank you! Your message has been received. Our team will review your inquiry and get back to you shortly.');
    }
}
