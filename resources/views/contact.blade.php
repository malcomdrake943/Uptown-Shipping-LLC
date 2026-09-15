@extends('layouts.app')

@section('title', $contactTitle)
@section('meta_description', Str::limit(strip_tags($contactSubtitle), 150))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 animate-fade-in">

    <!-- Hero Header -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-4 py-2 text-white/70 text-xs font-medium mb-6 border border-white/20">
            <span class="w-2 h-2 bg-amber-400 rounded-full animate-pulse"></span>
            Customer Support & Inquiries
        </div>
        <h1 class="text-4xl md:text-5xl font-display font-extrabold text-white leading-tight mb-4">
            {{ $contactTitle }}
        </h1>
        <p class="text-white/70 text-lg md:text-xl max-w-2xl mx-auto font-light">
            {{ $contactSubtitle }}
        </p>
    </div>

    <!-- Flash Success Message -->
    @if(session('contact_success'))
        <div class="max-w-4xl mx-auto mb-8 p-5 rounded-2xl bg-emerald-500/15 border border-emerald-400/30 text-emerald-100 flex items-start gap-4 shadow-lg animate-slide-up" role="alert">
            <div class="w-8 h-8 rounded-full bg-emerald-500/30 border border-emerald-400/40 flex items-center justify-center flex-shrink-0 text-emerald-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-white text-base mb-0.5">Message Sent Successfully!</h4>
                <p class="text-emerald-200/90 text-sm leading-relaxed">{{ session('contact_success') }}</p>
            </div>
        </div>
    @endif

    <!-- Direct Contact Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        
        <!-- Email Support -->
        <div class="glass p-6 md:p-8 rounded-3xl border border-white/15 hover:border-amber-400/40 transition-all duration-300 group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400/20 to-amber-600/20 border border-amber-400/30 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-display font-bold text-white mb-1">Email Support</h3>
                <p class="text-white/60 text-xs mb-4">Drop us an email anytime. We respond within 24 hours.</p>
            </div>
            <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center gap-2 text-amber-300 hover:text-amber-200 font-semibold text-sm break-all transition-colors group-hover:underline">
                <span>{{ $contactEmail }}</span>
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        <!-- Phone & WhatsApp -->
        <div class="glass p-6 md:p-8 rounded-3xl border border-white/15 hover:border-emerald-400/40 transition-all duration-300 group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400/20 to-emerald-600/20 border border-emerald-400/30 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-display font-bold text-white mb-1">Phone & WhatsApp</h3>
                <p class="text-white/60 text-xs mb-4">Direct customer helpline and WhatsApp messaging.</p>
            </div>
            <div class="space-y-1.5">
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}" class="block text-white hover:text-emerald-300 font-semibold text-sm transition-colors">
                    📞 {{ $contactPhone }}
                </a>
                @if($contactWhatsapp)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactWhatsapp) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-emerald-400 hover:text-emerald-300 text-xs font-semibold transition-colors">
                        <span>💬 Chat on WhatsApp</span> →
                    </a>
                @endif
            </div>
        </div>

        <!-- Office & Business Hours -->
        <div class="glass p-6 md:p-8 rounded-3xl border border-white/15 hover:border-blue-400/40 transition-all duration-300 group flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-400/20 to-blue-600/20 border border-blue-400/30 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-display font-bold text-white mb-1">Operating Hours</h3>
                <div class="text-white/70 text-xs leading-relaxed space-y-1 mb-4">
                    @foreach(explode("\n", $contactWorkingHours) as $hourLine)
                        @if(trim($hourLine))
                            <div>{{ trim($hourLine) }}</div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="text-white/40 text-xs border-t border-white/10 pt-3">
                📍 {{ Str::limit($contactAddress, 65) }}
            </div>
        </div>

    </div>

    <!-- Shipping Options & Transit Times Showcase -->
    <div class="glass-light rounded-3xl p-8 md:p-10 mb-12 shadow-2xl animate-slide-up">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 border-b border-gray-200/70 pb-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-600 text-xs font-bold mb-2">
                    Global Logistics
                </div>
                <h2 class="text-2xl md:text-3xl font-display font-bold text-gray-900">
                    Shipping Speeds & Custom Quotes
                </h2>
                <p class="text-gray-600 text-sm mt-1">
                    Choose the delivery speed that matches your requirements. We calculate the exact final rate when you submit your item link and package weight.
                </p>
            </div>
            <a href="{{ route('order.index') }}" class="btn-primary text-white text-xs md:text-sm font-semibold px-5 py-3 rounded-xl whitespace-nowrap self-start md:self-center shadow-md">
                Calculate & Order Now →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Express Air -->
            <div class="bg-white/70 rounded-2xl p-6 border border-brand-100 hover:border-amber-400 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-2xl">⚡</span>
                    <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold tracking-wide uppercase">Fastest</span>
                </div>
                <h3 class="font-display font-bold text-gray-900 text-lg mb-1">Express Air</h3>
                <p class="text-amber-600 font-extrabold text-sm mb-2">3–7 Days</p>
                <div class="text-xs text-gray-500 mb-4 leading-relaxed">
                    Priority air express courier. Ideal for urgent packages, high-value electronics, and rapid doorstep delivery.
                </div>
                <div class="inline-block px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">
                    Quote Required
                </div>
            </div>

            <!-- Standard Air -->
            <div class="bg-white/70 rounded-2xl p-6 border border-brand-100 hover:border-blue-400 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-2xl">✈️</span>
                    <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-[11px] font-bold tracking-wide uppercase">Popular</span>
                </div>
                <h3 class="font-display font-bold text-gray-900 text-lg mb-1">Standard Air</h3>
                <p class="text-blue-600 font-extrabold text-sm mb-2">7–14 Days</p>
                <div class="text-xs text-gray-500 mb-4 leading-relaxed">
                    Reliable standard international air shipping balancing speed and economical shipping costs.
                </div>
                <div class="inline-block px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">
                    Quote Required
                </div>
            </div>

            <!-- Sea Freight -->
            <div class="bg-white/70 rounded-2xl p-6 border border-brand-100 hover:border-emerald-400 hover:shadow-lg transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-2xl">🚢</span>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold tracking-wide uppercase">Best Value</span>
                </div>
                <h3 class="font-display font-bold text-gray-900 text-lg mb-1">Sea Freight</h3>
                <p class="text-emerald-600 font-extrabold text-sm mb-2">4–8 Weeks</p>
                <div class="text-xs text-gray-500 mb-4 leading-relaxed">
                    Cost-effective container freight designed for bulky, oversized, or commercial bulk shipments.
                </div>
                <div class="inline-block px-3 py-1 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold">
                    Quote Required
                </div>
            </div>
        </div>

        @if($contactShippingInfo)
            <div class="bg-brand-50/70 border border-brand-200/60 rounded-2xl p-5 text-gray-700 text-xs md:text-sm leading-relaxed whitespace-pre-line">
                {{ $contactShippingInfo }}
            </div>
        @endif
    </div>

    <!-- Contact Form & Headquarters Card -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        
        <!-- Main Form (8 Cols) -->
        <div class="lg:col-span-8 glass-light rounded-3xl p-8 md:p-10 shadow-2xl">
            <h2 class="text-2xl font-display font-bold text-gray-900 mb-2 flex items-center gap-3">
                <span class="w-3 h-8 bg-brand-600 rounded-full inline-block"></span>
                Send Us a Message
            </h2>
            <p class="text-gray-600 text-sm mb-8">
                {{ $contactFormIntro }}
            </p>

            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="John Doe"
                            class="input-field w-full px-4 py-3 rounded-xl text-gray-800 text-sm">
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="john@example.com"
                            class="input-field w-full px-4 py-3 rounded-xl text-gray-800 text-sm">
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Phone / WhatsApp -->
                    <div>
                        <label for="phone" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Phone / WhatsApp (Optional)
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 555-0123"
                            class="input-field w-full px-4 py-3 rounded-xl text-gray-800 text-sm">
                        @error('phone')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order Reference # -->
                    <div>
                        <label for="order_number" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Order Number (Optional)
                        </label>
                        <input type="text" id="order_number" name="order_number" value="{{ old('order_number') }}" placeholder="e.g. PP-1042"
                            class="input-field w-full px-4 py-3 rounded-xl text-gray-800 text-sm">
                        @error('order_number')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Inquiry Type -->
                <div>
                    <label for="inquiry_type" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Inquiry Topic / Reason <span class="text-red-500">*</span>
                    </label>
                    <select id="inquiry_type" name="inquiry_type" required
                        class="input-field w-full px-4 py-3 rounded-xl text-gray-800 text-sm bg-white">
                        <option value="">Select an inquiry type...</option>
                        <option value="Shipping Quote / Freight Inquiry" {{ old('inquiry_type') == 'Shipping Quote / Freight Inquiry' ? 'selected' : '' }}>Custom Shipping Quote (Express Air / Standard Air / Sea Freight)</option>
                        <option value="Order Status / Tracking Help" {{ old('inquiry_type') == 'Order Status / Tracking Help' ? 'selected' : '' }}>Order Status / Tracking Support</option>
                        <option value="Purchase Forwarding Inquiry" {{ old('inquiry_type') == 'Purchase Forwarding Inquiry' ? 'selected' : '' }}>Purchase Forwarding Question</option>
                        <option value="Payment & Billing" {{ old('inquiry_type') == 'Payment & Billing' ? 'selected' : '' }}>Payment / Mobile Money Support</option>
                        <option value="Commercial / Bulk Orders" {{ old('inquiry_type') == 'Commercial / Bulk Orders' ? 'selected' : '' }}>Commercial / Bulk Shipment Inquiry</option>
                        <option value="General Question" {{ old('inquiry_type') == 'General Question' ? 'selected' : '' }}>General Question / Feedback</option>
                    </select>
                    @error('inquiry_type')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Message -->
                <div>
                    <label for="message" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Your Message / Item Details <span class="text-red-500">*</span>
                    </label>
                    <textarea id="message" name="message" rows="5" required placeholder="Please provide details about your inquiry, product links, estimated package weight, or any questions..."
                        class="input-field w-full px-4 py-3 rounded-xl text-gray-800 text-sm">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full sm:w-auto btn-primary text-white font-semibold px-8 py-3.5 rounded-xl shadow-lg transition-transform hover:scale-105 inline-flex items-center justify-center gap-2">
                        <span>Send Message</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Office Address Card -->
            <div class="glass p-6 md:p-8 rounded-3xl border border-white/15">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-amber-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-display font-bold text-white">Headquarters</h3>
                </div>
                <p class="text-white/70 text-sm leading-relaxed whitespace-pre-line mb-4">
                    {{ $contactAddress }}
                </p>
                <div class="text-xs text-white/50 border-t border-white/10 pt-4">
                    All global procurement, packaging consolidation, and international freight operations are managed through our logistics hub.
                </div>
            </div>

            <!-- Quick Track Order Helper -->
            <div class="glass p-6 md:p-8 rounded-3xl border border-white/15">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-blue-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-display font-bold text-white">Track an Order</h3>
                </div>
                <p class="text-white/60 text-xs leading-relaxed mb-4">
                    Looking for the current status of an existing shipment? You can instantly track your package online without submitting a form.
                </p>
                <button type="button" onclick="document.getElementById('resend-modal').classList.remove('hidden')" class="w-full py-2.5 px-4 rounded-xl border border-white/20 text-white hover:bg-white/10 font-medium text-xs transition-colors flex items-center justify-center gap-2">
                    <span>Open Order Tracker</span> →
                </button>
            </div>

            <!-- Need Help Ordering? -->
            <div class="glass p-6 md:p-8 rounded-3xl border border-white/15 text-center">
                <h4 class="font-display font-bold text-white text-base mb-2">Ready to Place an Order?</h4>
                <p class="text-white/60 text-xs mb-5">Paste product URLs from Amazon, eBay, Best Buy, or any major store.</p>
                <a href="{{ route('order.index') }}" class="inline-block w-full btn-primary text-white font-semibold py-2.5 px-4 rounded-xl text-xs shadow transition-transform hover:scale-105">
                    Start New Order
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
