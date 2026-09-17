@extends('layouts.app')

@section('title', 'Place an Order')
@section('meta_description', 'Order any product from Amazon, eBay or any online store. We buy it and ship it to you internationally.')

@section('content')
    <!-- CryptoJS library for CardNest AES-128 decryption -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>

    <div class="max-w-6xl mx-auto px-4"
        x-data="orderWizard({{ json_encode($feeRules) }}, {{ json_encode($sizeFeeRules) }}, {{ json_encode($platforms) }})"
        x-init="init()" x-cloak>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- ── Left Column: Portal Header & Details (lg:col-span-5) ────────────── -->
            <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-24">
                
                <!-- Hero Header -->
                <div class="text-left animate-fade-in space-y-4">
                    <div class="inline-flex items-center gap-3 bg-white/5 border border-white/10 shadow-inner rounded-full px-4 py-1.5 backdrop-blur-sm">
                        <span class="text-sm select-none">🇺🇸</span>
                        <span class="text-[9px] text-white/70 font-bold tracking-widest uppercase">USA to Ghana Cargo</span>
                        <span class="text-sm select-none">🇬🇭</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-display font-extrabold text-white leading-tight">
                        Shop in USA.<br><span class="gradient-text">Ship to Ghana.</span>
                    </h1>
                    <p class="text-white/60 text-sm leading-relaxed">
                        Paste any product URL from Amazon, eBay, or any online store. We purchase it on your behalf and deliver it straight to your door in Ghana.
                    </p>
                    <div class="inline-block bg-brand-500/20 text-brand-300 rounded-2xl px-4 py-1.5 text-xs font-semibold border border-brand-500/40 leading-none">
                        Pickup and Delivery Service Available
                    </div>
                </div>

                <!-- Contact & Flyer Card -->
                <div class="glass rounded-3xl p-6 border border-white/10 shadow-2xl space-y-4">
                    <h3 class="text-brand-300 font-display font-bold text-sm flex items-center gap-2">
                        <span>📦</span> Uptown Cargo Services
                    </h3>
                    <ul class="space-y-3 text-xs text-white/70">
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-300 text-sm">✓</span>
                            <span><strong>All Year Round Shipping</strong> from the USA to major cities in Ghana.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-300 text-sm">✓</span>
                            <span><strong>Hassle-Free Forwarding</strong>: Paste your items and we handle purchase, cargo consolidation, customs clearance, and delivery.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-brand-300 text-sm">✓</span>
                            <span><strong>Secure Logistics</strong> with active tracking on every step of your order.</span>
                        </li>
                    </ul>

                    <!-- Contact items from flyer -->
                    <div class="pt-4 border-t border-white/5 space-y-2.5 text-xs text-white/50">
                        <div class="flex items-center gap-2">
                            <span>📞</span> <a href="tel:8042395736" class="hover:text-white transition-colors">555-123-4567</a>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>✉️</span> <a href="mailto:support@uptownservices.net" class="hover:text-white transition-colors">support@uptownservices.net</a>
                        </div>
                        <div class="flex items-start gap-2">
                            <span>📍</span> <span class="leading-normal">6341 Dawnfield Lane, Henrico VA 23231</span>
                        </div>
                    </div>
                </div>

                <!-- Integrated Quick Tracking Card -->
                <div class="glass rounded-3xl p-6 border border-white/10 shadow-2xl space-y-4">
                    <h3 class="text-white font-display font-bold text-sm flex items-center gap-2">
                        <span>🔍</span> Track Order Status
                    </h3>
                    <p class="text-white/50 text-xs leading-normal">
                        Enter your order number (e.g. PP-1042) to instantly check the status of your shipment.
                    </p>
                    <form action="{{ route('order.track-public') }}" method="GET" class="flex gap-2">
                        <input type="text" name="order_number" required placeholder="Order Number"
                            class="input-field bg-white/10 border border-white/10 text-white placeholder-white/40 text-xs px-3 py-2.5 rounded-xl flex-1 focus:outline-none focus:border-brand-300">
                        <button type="submit" class="btn-primary text-white text-xs font-semibold px-4 py-2.5 rounded-xl whitespace-nowrap">
                            Track
                        </button>
                    </form>
                </div>
            </div>

            <!-- ── Right Column: Interactive Accordion Flow Checker (lg:col-span-7) ───── -->
            <div class="lg:col-span-7 space-y-4">
                
                <!-- ══ STEP 1: Platform Selection ════════════════════════════════════════ -->
                <div class="glass-light rounded-3xl overflow-hidden shadow-xl border transition-all duration-300"
                    :class="step === 1 ? 'border-brand-500 ring-4 ring-brand-500/10' : 'border-gray-200/50 opacity-95'">
                    
                    <!-- Panel Header -->
                    <div class="p-6 flex items-center justify-between cursor-pointer select-none"
                        @click="if(selectedPlatformId) { step = 1; }">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold font-display transition-colors"
                                :class="step > 1 ? 'bg-brand-500 text-white' : 'bg-brand-100 text-brand-900'">
                                <span x-show="step > 1">✓</span>
                                <span x-show="step <= 1">1</span>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-gray-900 text-base">Select Online Store</h3>
                                <p class="text-xs text-gray-500" x-show="step === 1">Choose a partner store to start shopping</p>
                            </div>
                        </div>
                        
                        <!-- Summary when collapsed -->
                        <div x-show="step > 1 && getSelectedPlatform()" class="flex items-center gap-2 bg-brand-50/70 border border-brand-100 px-3 py-1 rounded-full text-xs">
                            <img :src="'/storage/' + getSelectedPlatform()?.logo" class="w-4 h-4 object-contain rounded-full" x-show="getSelectedPlatform()?.logo">
                            <span class="font-bold text-gray-800" x-text="getSelectedPlatform()?.name"></span>
                        </div>
                    </div>

                    <!-- Panel Body -->
                    <div x-show="step === 1" x-collapse class="p-6 pt-0 border-t border-gray-100/60">
                        <p class="text-gray-500 text-sm mb-6 mt-4">Select one of our partner platforms below to buy your products.</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                            <template x-for="p in platforms" :key="p.id">
                                <div class="relative flex flex-col justify-between p-4 rounded-xl border transition-all duration-300 cursor-pointer overflow-hidden group select-none"
                                    :class="selectedPlatformId === p.id ? 'bg-brand-50 border-brand-500 ring-2 ring-brand-500/20' : 'bg-white/50 border-gray-200/60 hover:bg-white hover:border-brand-300 hover:shadow-lg'"
                                    @click="selectPlatform(p)">

                                    <!-- Selection badge -->
                                    <div class="absolute top-2.5 right-2.5 w-4 h-4 rounded-full flex items-center justify-center border text-[10px]"
                                        :class="selectedPlatformId === p.id ? 'bg-brand-600 border-brand-600 text-white' : 'border-gray-300 text-transparent'">
                                        ✓
                                    </div>

                                    <!-- Brand Logo & Name -->
                                    <div class="flex flex-col items-center text-center mt-2 flex-grow">
                                        <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center p-2 shadow-sm border border-gray-100 group-hover:scale-105 transition-transform duration-300 mb-2">
                                            <img :src="'/storage/' + p.logo" :alt="p.name" class="w-full h-full object-contain rounded-full">
                                        </div>
                                        <span class="font-bold text-gray-900 text-xs" x-text="p.name"></span>
                                    </div>

                                    <!-- Visit Store Button -->
                                    <div class="mt-3 pt-2.5 border-t border-gray-100/60 w-full text-center">
                                        <a :href="p.url" target="_blank" @click.stop
                                            class="inline-block text-[10px] font-semibold text-brand-600 hover:text-brand-700 bg-brand-50/50 hover:bg-brand-50 px-2 py-1 rounded-lg border border-brand-200/50 transition-colors w-full">
                                            Visit Store ↗
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Validation Error -->
                        <div x-show="!selectedPlatformId && step1Error" x-text="step1Error"
                            class="text-red-600 text-xs text-center mb-4 bg-red-50 rounded-xl px-4 py-2.5"></div>

                        <!-- Continue Button -->
                        <button
                            @click="if(selectedPlatformId) { step = 2; } else { step1Error = 'Please select a platform to proceed.'; }"
                            :disabled="!selectedPlatformId"
                            class="btn-primary w-full text-white py-3.5 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            Continue to Product Details
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ══ STEP 2: Product & Pricing ══════════════════════════════════════════ -->
                <div class="glass-light rounded-3xl overflow-hidden shadow-xl border transition-all duration-300"
                    :class="step === 2 ? 'border-brand-500 ring-4 ring-brand-500/10' : 'border-gray-200/50 opacity-95'">
                    
                    <!-- Panel Header -->
                    <div class="p-6 flex items-center justify-between cursor-pointer select-none"
                        @click="if(selectedPlatformId && step > 1) { step = 2; }">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold font-display transition-colors"
                                :class="step > 2 ? 'bg-brand-500 text-white' : 'bg-brand-100 text-brand-900'">
                                <span x-show="step > 2">✓</span>
                                <span x-show="step <= 2">2</span>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-gray-900 text-base">Product & Price</h3>
                                <p class="text-xs text-gray-500" x-show="step === 2">Provide the item link and size tier</p>
                            </div>
                        </div>
                        
                        <!-- Summary when collapsed -->
                        <div x-show="step > 2" class="text-right text-xs">
                            <p class="font-bold text-gray-800 text-xs line-clamp-1" x-text="productName || 'Fetched Product'"></p>
                            <p class="text-brand-700 font-semibold" x-text="'$' + computedTotal.toFixed(2)"></p>
                        </div>
                    </div>

                    <!-- Panel Body -->
                    <div x-show="step === 2" x-collapse class="p-6 pt-0 border-t border-gray-100/60">
                        <!-- Selected Store Banner -->
                        <div x-show="selectedPlatformId"
                            class="flex items-center justify-between bg-brand-50/70 border border-brand-100 rounded-xl px-4 py-2 mt-4 mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-white flex items-center justify-center p-1 border border-gray-100 shadow-xs">
                                    <template x-if="getSelectedPlatform()?.logo">
                                        <img :src="'/storage/' + getSelectedPlatform()?.logo" :alt="getSelectedPlatform()?.name" class="w-full h-full object-contain rounded-full">
                                    </template>
                                </div>
                                <span class="text-xs text-gray-900 font-bold" x-text="getSelectedPlatform()?.name"></span>
                            </div>
                            <button @click="step = 1" class="text-[10px] font-semibold text-brand-600 hover:text-brand-700 hover:underline">
                                Change Store
                            </button>
                        </div>

                        <!-- URL Input -->
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Product Link (URL)</label>
                            <div class="flex gap-2">
                                <input type="url" x-model="productUrl" placeholder="https://amazon.com/dp/... or eBay link"
                                    class="input-field flex-1 px-3.5 py-2.5 rounded-xl text-gray-800 text-xs"
                                    @input.debounce.500ms="productFetchResult = null">
                                <button @click="fetchProduct()" :disabled="!productUrl || fetchingProduct"
                                    class="btn-primary text-white px-4 py-2.5 rounded-xl text-xs font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1.5 whitespace-nowrap">
                                    <svg x-show="fetchingProduct" class="spinner w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z" />
                                    </svg>
                                    <span x-text="fetchingProduct ? 'Fetching...' : 'Fetch'"></span>
                                </button>
                            </div>
                        </div>

                        <!-- Product Card (success) -->
                        <div x-show="productFetchResult && productFetchResult.status === 'done' && productFetchResult.data"
                            x-transition class="fee-card rounded-xl p-4 mb-4 mt-2">
                            <div class="flex gap-3">
                                <img x-show="productFetchResult?.data?.image_url" :src="productFetchResult?.data?.image_url"
                                    class="w-16 h-16 object-cover rounded-lg border border-white/50 flex-shrink-0"
                                    x-on:error="$el.style.display='none'">
                                <div class="flex-1 min-w-0">
                                    <span class="platform-badge px-2 py-0.5 rounded-full text-white text-[9px] font-bold"
                                        :class="productFetchResult?.data?.platform === 'amazon' ? 'bg-orange-500' : 'bg-brand-500'"
                                        x-text="productFetchResult?.data?.platform?.toUpperCase() ?? 'OTHER'"></span>
                                    <p class="font-semibold text-gray-900 text-xs line-clamp-1 mt-1" x-text="productFetchResult?.data?.name"></p>
                                    <p x-show="productFetchResult?.data?.price" class="text-brand-600 font-bold text-xs mt-1">
                                        Detected price: $<span x-text="productFetchResult?.data?.price?.toFixed(2)"></span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Fetch Failed / Manual Entry Notice -->
                        <div x-show="productFetchResult && (productFetchResult.status === 'failed' || !productFetchResult.data)"
                            x-transition class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 mb-4">
                            <p class="text-amber-800 text-xs font-semibold">We couldn't fetch details automatically</p>
                            <p class="text-amber-700 text-[10px] mt-0.5">Bot protection blocked retrieval. Please complete fields manually. Your order will not be affected.</p>
                        </div>

                        <!-- Product Name -->
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Product Name <span class="text-gray-400 font-normal">(optional)</span></label>
                            <input type="text" x-model="productName" placeholder="e.g. Sony WH-1000XM5 Headphones"
                                class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs">
                        </div>

                        <!-- Price, Qty, Size Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Price (USD) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-medium text-xs">$</span>
                                    <input type="number" x-model.number="estimatedPrice" min="0.01" step="0.01" placeholder="0.00"
                                        :readonly="productFetchResult && productFetchResult.data && productFetchResult.data.price"
                                        :class="productFetchResult && productFetchResult.data && productFetchResult.data.price ? 'bg-gray-100 cursor-not-allowed opacity-80' : ''"
                                        class="input-field w-full pl-6 pr-3 py-2.5 rounded-xl text-gray-800 text-xs" @input="recalculateFees()">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Quantity <span class="text-red-500">*</span></label>
                                <input type="number" x-model.number="quantity" min="1" max="100"
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs" @input="recalculateFees()">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Package Size <span class="text-red-500">*</span></label>
                                <select x-model="sizeTier" class="input-field w-full px-3 py-2.5 rounded-xl text-gray-800 text-xs" @change="recalculateFees()">
                                    <option value="">Select size</option>
                                    <option value="small">📦 Small</option>
                                    <option value="medium">📦 Medium (+$5)</option>
                                    <option value="large">📦 Large (+$12)</option>
                                    <option value="oversized">🏗️ Oversized (quote)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Preferred Shipping Method -->
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Preferred Shipping Method <span class="text-red-500">*</span></label>
                            <select x-model="shippingMethod" class="input-field w-full px-3 py-2.5 rounded-xl text-gray-800 text-xs" @change="recalculateFees()">
                                <option value="">Select Shipping Method</option>
                                @foreach($deliveryOptions as $option)
                                    <option value="{{ $option->name }}">{{ $option->name }} ({{ $option->duration }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Live Fee Breakdown -->
                        <div x-show="sizeTier && estimatedPrice > 0" x-transition class="fee-card rounded-xl p-4 mb-4">
                            <!-- Oversized: manual quote notice -->
                            <div x-show="sizeFeeRules[sizeTier]?.requires_manual_quote" class="text-center py-1">
                                <p class="text-brand-700 font-bold text-xs">🏗️ Oversized items require a manual quote</p>
                                <p class="text-gray-500 text-[10px] mt-0.5">We'll review your order and email a quote in 1-2 days.</p>
                            </div>

                            <!-- Normal fee breakdown -->
                            <div x-show="!sizeFeeRules[sizeTier]?.requires_manual_quote" class="space-y-2 text-xs">
                                <div class="flex justify-between text-gray-600">
                                    <span>Product subtotal</span>
                                    <span>$<span x-text="(estimatedPrice * quantity).toFixed(2)"></span></span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Service fee (<span x-text="getFeePercent()"></span>%)</span>
                                    <span>$<span x-text="computedTierFee.toFixed(2)"></span></span>
                                </div>
                                <div x-show="computedSizeFee > 0" class="flex justify-between text-gray-600">
                                    <span>Handling fee (<span x-text="sizeTier"></span>)</span>
                                    <span>$<span x-text="computedSizeFee.toFixed(2)"></span></span>
                                </div>
                                <div class="border-t border-brand-200 pt-2 mt-2 flex justify-between">
                                    <span class="font-bold text-gray-900">Total Estimate</span>
                                    <span class="font-bold text-brand-700 text-sm">$<span x-text="computedTotal.toFixed(2)"></span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Error -->
                        <div x-show="step1Error" x-text="step1Error" class="text-red-600 text-xs text-center mb-4 bg-red-50 rounded-xl px-4 py-2.5"></div>

                        <!-- Panel Buttons -->
                        <div class="flex gap-2">
                            <button @click="step = 1" class="flex-1 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 transition-colors">
                                ← Back
                            </button>
                            <button @click="validateStep1()" class="flex-2 btn-primary text-white px-6 py-3 rounded-2xl text-xs font-bold flex items-center justify-center gap-1.5">
                                Continue
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ══ STEP 3: Customer & Shipping Details ════════════════════════════════ -->
                <div class="glass-light rounded-3xl overflow-hidden shadow-xl border transition-all duration-300"
                    :class="step === 3 ? 'border-brand-500 ring-4 ring-brand-500/10' : 'border-gray-200/50 opacity-95'">
                    
                    <!-- Panel Header -->
                    <div class="p-6 flex items-center justify-between cursor-pointer select-none"
                        @click="if(selectedPlatformId && step > 2) { step = 3; }">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold font-display transition-colors"
                                :class="step > 3 ? 'bg-brand-500 text-white' : 'bg-brand-100 text-brand-900'">
                                <span x-show="step > 3">✓</span>
                                <span x-show="step <= 3">3</span>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-gray-900 text-base">Delivery Info</h3>
                                <p class="text-xs text-gray-500" x-show="step === 3">Enter your shipping address in Ghana</p>
                            </div>
                        </div>
                        
                        <!-- Summary when collapsed -->
                        <div x-show="step > 3" class="text-right text-xs">
                            <p class="font-semibold text-gray-800" x-text="customerName"></p>
                            <p class="text-gray-500" x-text="city + ', ' + country"></p>
                        </div>
                    </div>

                    <!-- Panel Body -->
                    <div x-show="step === 3" x-collapse class="p-6 pt-0 border-t border-gray-100/60">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 mt-4 mb-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" x-model="customerName" placeholder="John Doe"
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email Address <span class="text-red-500">*</span></label>
                                <input type="email" x-model="customerEmail" placeholder="john@example.com"
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" x-model="customerPhone" placeholder="+233..."
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Address Line 1 <span class="text-red-500">*</span></label>
                                <input type="text" x-model="addressLine1" placeholder="Street Address, House No."
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Address Line 2 <span class="text-gray-400 font-normal">(optional)</span></label>
                                <input type="text" x-model="addressLine2" placeholder="Apartment, unit, suite..."
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">City / Town <span class="text-red-500">*</span></label>
                                <input type="text" x-model="city" placeholder="Accra"
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">State / Region</label>
                                <input type="text" x-model="state" placeholder="Greater Accra"
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Postal Code / GPS <span class="text-red-500">*</span></label>
                                <input type="text" x-model="postalCode" placeholder="GA-184-2931"
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Country <span class="text-red-500">*</span></label>
                                <input type="text" x-model="country" placeholder="Ghana"
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Order Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                                <textarea x-model="customerNotes" rows="2" placeholder="Color, size, or special handling requests..."
                                    class="input-field w-full px-3.5 py-2.5 rounded-xl text-gray-800 text-xs resize-none"></textarea>
                            </div>
                        </div>

                        <!-- Validation Error -->
                        <div x-show="step2Error" x-text="step2Error" class="text-red-600 text-xs text-center mt-3 bg-red-50 rounded-xl px-4 py-2.5"></div>

                        <!-- Panel Buttons -->
                        <div class="flex gap-2 mt-4">
                            <button @click="step = 2" class="flex-1 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 transition-colors">
                                ← Back
                            </button>
                            <button @click="validateStep2()" class="flex-2 btn-primary text-white px-6 py-3 rounded-2xl text-xs font-bold flex items-center justify-center gap-1.5">
                                Continue
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ══ STEP 4: Payment / Review ═══════════════════════════════════════════ -->
                <div class="glass-light rounded-3xl overflow-hidden shadow-xl border transition-all duration-300"
                    :class="step === 4 ? 'border-brand-500 ring-4 ring-brand-500/10' : 'border-gray-200/50 opacity-95'">
                    
                    <!-- Panel Header -->
                    <div class="p-6 flex items-center justify-between cursor-pointer select-none"
                        @click="if(selectedPlatformId && step > 3) { step = 4; }">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold font-display transition-colors"
                                :class="step === 4 ? 'bg-brand-500 text-white' : 'bg-brand-100 text-brand-900'">
                                <span>4</span>
                            </div>
                            <div>
                                <h3 class="font-display font-bold text-gray-900 text-base">Secure Checkout</h3>
                                <p class="text-xs text-gray-500" x-show="step === 4">Review your details and pay securely</p>
                            </div>
                        </div>
                    </div>

                    <!-- Panel Body -->
                    <div x-show="step === 4" x-collapse class="p-6 pt-0 border-t border-gray-100/60">
                        
                        <!-- Order Summary Card -->
                        <div class="bg-gray-50 rounded-2xl p-4 mt-4 mb-4 space-y-2.5 border border-gray-200/60">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-brand-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 text-xs truncate" x-text="productName || 'Item Link'"></p>
                                    <p class="text-gray-500 text-[10px] mt-0.5">Qty: <span x-text="quantity"></span> · <span x-text="sizeTier" class="capitalize"></span> size</p>
                                </div>
                            </div>
                            <div class="border-t border-gray-200/60 pt-2.5 space-y-1.5 text-xs">
                                <div class="flex justify-between text-gray-600">
                                    <span>Product subtotal</span>
                                    <span>$<span x-text="(estimatedPrice * quantity).toFixed(2)"></span></span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Service fee</span>
                                    <span>$<span x-text="computedTierFee.toFixed(2)"></span></span>
                                </div>
                                <div x-show="computedSizeFee > 0" class="flex justify-between text-gray-600">
                                    <span>Handling fee</span>
                                    <span>$<span x-text="computedSizeFee.toFixed(2)"></span></span>
                                </div>
                                <div class="flex justify-between font-bold text-gray-900 pt-2 border-t border-gray-200/60">
                                    <span>Total Due Today</span>
                                    <span class="text-brand-700 font-bold">$<span x-text="computedTotal.toFixed(2)"></span></span>
                                </div>
                            </div>
                            <div class="border-t border-gray-200/60 pt-2 text-[10px] text-gray-500 leading-normal">
                                <p><strong>Shipping to:</strong> <span x-text="customerName"></span>, <span x-text="addressLine1"></span>, <span x-text="city"></span>, <span x-text="country"></span></p>
                                <p class="mt-0.5"><strong>Email invoice:</strong> <span x-text="customerEmail"></span></p>
                            </div>
                        </div>

                        <!-- Oversized: no payment -->
                        <div x-show="sizeFeeRules[sizeTier]?.requires_manual_quote"
                            class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-4">
                            <div class="flex gap-2.5">
                                <span class="text-lg">🏗️</span>
                                <div>
                                    <p class="font-bold text-amber-900 text-xs">Manual quote pending</p>
                                    <p class="text-amber-700 text-[10px] mt-0.5">Oversized packages require custom quotes. We'll email a payment link in 1–2 business days. No payment is taken now.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Options Selector -->
                        <div x-show="!sizeFeeRules[sizeTier]?.requires_manual_quote" class="mb-4">
                            <label class="block text-[11px] font-semibold text-gray-700 mb-2">Select Payment Method</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" @click="paymentMethod = 'card'"
                                    class="flex items-center justify-center gap-2 p-3 rounded-xl border text-xs font-bold transition-all select-none"
                                    :class="paymentMethod === 'card' ? 'bg-brand-50 border-brand-500 text-brand-900 ring-2 ring-brand-500/20 shadow-xs' : 'bg-white/80 border-gray-200 text-gray-600 hover:bg-white hover:border-gray-300'">
                                    <span>💳</span>
                                    <span>Card Payment</span>
                                </button>

                                <button type="button" @click="paymentMethod = 'momo'; showMomoModal = true"
                                    class="flex items-center justify-center gap-2 p-3 rounded-xl border text-xs font-bold transition-all select-none"
                                    :class="paymentMethod === 'momo' ? 'bg-brand-50 border-brand-500 text-brand-900 ring-2 ring-brand-500/20 shadow-xs' : 'bg-white/80 border-gray-200 text-gray-600 hover:bg-white hover:border-gray-300'">
                                    <span>📱</span>
                                    <span>Mobile Money</span>
                                </button>
                            </div>
                        </div>

                        <!-- Mobile Money Details Popup Modal -->
                        <div x-show="showMomoModal" x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in"
                            @keydown.escape.window="showMomoModal = false">
                            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-amber-200/50 relative text-left"
                                @click.away="showMomoModal = false">
                                
                                <!-- Close Button -->
                                <button type="button" @click="showMomoModal = false"
                                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <!-- Header Icon & Title -->
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl shadow-sm">
                                        📱
                                    </div>
                                    <div>
                                        <h3 class="font-display font-bold text-lg text-gray-900">Mobile Money Payment</h3>
                                        <p class="text-xs text-amber-600 font-medium">Customer Support Instructions</p>
                                    </div>
                                </div>

                                <!-- Popup Message -->
                                <div class="bg-amber-50/90 border border-amber-200 rounded-2xl p-4 mb-5">
                                    <p class="text-sm text-amber-950 leading-relaxed font-medium">
                                        To Pay with mobile money, contact our customer support team on this number <a href="tel:{{ str_replace(['-', ' '], '', $supportPhone) }}" class="font-bold underline text-brand-700 hover:text-brand-900">{{ $supportPhone }}</a>, and you will be provided with the mobile money details. In meantime, you can complete your order for processing
                                    </p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <a href="tel:{{ str_replace(['-', ' '], '', $supportPhone) }}"
                                        class="flex-1 btn-primary text-white text-xs font-bold py-3 px-4 rounded-xl text-center flex items-center justify-center gap-2 shadow-sm">
                                        <span>📞</span> Call: {{ $supportPhone }}
                                    </a>
                                    <button type="button" @click="showMomoModal = false"
                                        class="py-3 px-5 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                        Continue Order
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile Money Payment Info Card -->
                        <div x-show="!sizeFeeRules[sizeTier]?.requires_manual_quote && paymentMethod === 'momo'"
                            class="bg-amber-50/90 border border-amber-200/90 rounded-2xl p-5 mb-4 space-y-3 animate-fade-in shadow-xs">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-amber-950 font-bold text-xs">
                                    <span class="text-base">📱</span>
                                    <span>Mobile Money Selected</span>
                                </div>
                                <button type="button" @click="showMomoModal = true" class="text-xs font-bold text-brand-700 hover:underline">
                                    View Instructions ↗
                                </button>
                            </div>
                            <p class="text-xs text-amber-900 leading-relaxed font-normal">
                                To Pay with mobile money, contact our customer support team on this number <a href="tel:{{ str_replace(['-', ' '], '', $supportPhone) }}" class="font-bold underline text-brand-700 hover:text-brand-900">{{ $supportPhone }}</a>, and you will be provided with the mobile money details. In meantime, you can complete your order for processing
                            </p>
                            <div class="pt-2 border-t border-amber-200/60 flex items-center justify-between text-[11px] text-amber-900">
                                <span class="font-medium">Support Number:</span>
                                <a href="tel:{{ str_replace(['-', ' '], '', $supportPhone) }}" class="font-bold text-brand-800 hover:underline inline-flex items-center gap-1.5 bg-white/90 px-3 py-1.5 rounded-lg border border-amber-200 shadow-xs">
                                    <span>📞</span> {{ $supportPhone }}
                                </a>
                            </div>
                        </div>

                        <!-- Scan Card & Card Info Section -->
                        <div x-show="!sizeFeeRules[sizeTier]?.requires_manual_quote && paymentMethod === 'card'"
                            class="bg-gray-50 rounded-2xl p-5 mb-4 border border-gray-200/60">
                            
                            <p class="text-[10px] text-gray-500 mb-3 font-medium bg-brand-50 border border-brand-100 rounded-xl p-2.5 flex items-start gap-2 leading-relaxed">
                                <span>🔒</span>
                                <span>For secure processing, please scan your card or enter details below. Payments are fully encrypted.</span>
                            </p>

                            <div class="flex flex-col items-center justify-center py-2">
                                <!-- QR Code Container -->
                                <div class="w-full max-w-sm flex flex-col items-center justify-center border border-dashed border-gray-300 rounded-xl p-4 bg-white text-center min-h-[180px] shadow-xs">
                                    <template x-if="scanStatus === 'polling' && scanUrl">
                                        <div class="animate-fade-in">
                                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=' + encodeURIComponent(scanUrl)"
                                                alt="Card Scan QR Code" class="w-32 h-32 mb-2 rounded-lg border border-gray-100 shadow-xs mx-auto" />
                                            <p class="text-xs font-bold text-gray-800">Scan QR Code</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">Point your mobile camera to scan and prefill card</p>
                                        </div>
                                    </template>
                                    <template x-if="scanStatus !== 'polling'">
                                        <div>
                                            <button type="button" @click="startCardScan()" class="btn-primary text-white text-xs font-semibold px-4 py-2.5 rounded-xl flex items-center gap-1.5 mx-auto">
                                                <span>📸</span> Scan Card with Mobile
                                            </button>
                                            <p class="text-[10px] text-gray-400 mt-2">Recommended for secure mobile checkouts</p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Manual Card Inputs -->
                            <div class="mt-4 space-y-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-gray-700 mb-1">Card Number</label>
                                    <input type="text" x-model="cardNumber" placeholder="1234 5678 1234 5678"
                                        :readonly="scanStatus === 'completed'"
                                        :class="scanStatus === 'completed' ? 'bg-gray-100 cursor-not-allowed opacity-80' : ''"
                                        class="input-field w-full px-3.5 py-2 rounded-xl text-gray-800 text-xs">
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-semibold text-gray-700 mb-1">Expiration Date</label>
                                        <div class="flex gap-2">
                                            <input type="text" x-model="cardExpiryMonth" placeholder="MM" maxLength="2"
                                                :readonly="scanStatus === 'completed'"
                                                :class="scanStatus === 'completed' ? 'bg-gray-100 cursor-not-allowed opacity-80' : ''"
                                                class="input-field w-full px-3 py-2 rounded-xl text-gray-800 text-xs text-center">
                                            <input type="text" x-model="cardExpiryYear" placeholder="YY" maxLength="2"
                                                :readonly="scanStatus === 'completed'"
                                                :class="scanStatus === 'completed' ? 'bg-gray-100 cursor-not-allowed opacity-80' : ''"
                                                class="input-field w-full px-3 py-2 rounded-xl text-gray-800 text-xs text-center">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-gray-700 mb-1">CVC</label>
                                        <input type="password" x-model="cardCvc" placeholder="123" maxLength="4"
                                            class="input-field w-full px-3 py-2 rounded-xl text-gray-800 text-xs text-center">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Error -->
                        <div x-show="paymentError" x-text="paymentError" class="text-red-600 text-xs text-center mb-4 bg-red-50 rounded-xl px-4 py-2.5"></div>

                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button @click="step = 3" class="flex-1 py-3 rounded-2xl border-2 border-gray-200 text-gray-600 text-xs font-semibold hover:bg-gray-50 transition-colors">
                                ← Back
                            </button>
                            <button @click="submitOrder()" :disabled="submitting"
                                class="flex-2 btn-primary text-white px-6 py-3 rounded-2xl text-xs font-bold flex items-center justify-center gap-1.5 disabled:opacity-60">
                                <svg x-show="submitting" class="spinner w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.4 0 0 5.4 0 12h4z" />
                                </svg>
                                <template x-if="sizeFeeRules[sizeTier]?.requires_manual_quote">
                                    <span x-text="submitting ? 'Submitting...' : 'Submit Quote Request'"></span>
                                </template>
                                <template x-if="!sizeFeeRules[sizeTier]?.requires_manual_quote">
                                    <span x-text="submitting ? 'Processing Order...' : (paymentMethod === 'momo' ? 'Complete Order with Mobile Money' : 'Pay $' + computedTotal.toFixed(2) + ' Securely')"></span>
                                </template>
                            </button>
                        </div>

                        <p class="text-center text-[10px] text-gray-400 mt-3.5">
                            By placing this order you agree to our terms. Track code sent instantly to your email.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function orderWizard(feeRules, sizeFeeRules, platforms) {
            return {
                // Navigation
                step: 1,

                platforms,
                selectedPlatformId: null,
                authToken: null,

                selectPlatform(platform) {
                    this.selectedPlatformId = platform.id;
                    this.step = 2;
                },

                getSelectedPlatform() {
                    return this.platforms.find(p => p.id === this.selectedPlatformId);
                },

                // Step 2: Product (originally Step 1)
                productUrl: '',
                productName: '',
                productImageUrl: '',
                estimatedPrice: '',
                quantity: 1,
                sizeTier: '',
                shippingMethod: '',
                fetchingProduct: false,
                productFetchResult: null,
                fetchJobKey: null,
                fetchPollInterval: null,
                step1Error: '',

                // Step 2: Customer
                customerName: '',
                customerEmail: '',
                customerPhone: '',
                addressLine1: '',
                addressLine2: '',
                city: '',
                state: '',
                postalCode: '',
                country: 'Ghana',
                customerNotes: '',
                step2Error: '',

                // Step 3: Payment & Scan
                paymentMethod: 'card', // 'card' or 'momo'
                showMomoModal: false,
                paymentError: '',
                submitting: false,
                scanId: null,
                scanUrl: null,
                scanStatus: 'idle', // 'idle', 'initiating', 'polling', 'completed', 'failed'
                cardNumber: '',
                cardExpiryMonth: '',
                cardExpiryYear: '',
                cardCvc: '',
                scanPollInterval: null,
                stripePublishableKey: '{{ config('cashier.key') }}',

                // Fee data
                feeRules,
                sizeFeeRules,
                computedTierFee: 0,
                computedSizeFee: 0,
                computedTotal: 0,

                init() {
                    // If URL param has pre-filled URL
                    const urlParam = new URLSearchParams(window.location.search).get('url');
                    if (urlParam) {
                        this.productUrl = urlParam;
                        try {
                            // Try to detect platform automatically
                            const host = new URL(urlParam).hostname.toLowerCase();
                            const matchedPlatform = this.platforms.find(p => {
                                try {
                                    const pHost = new URL(p.url).hostname.toLowerCase();
                                    return host.includes(pHost) || pHost.includes(host);
                                } catch {
                                    return false;
                                }
                            });
                            if (matchedPlatform) {
                                this.selectedPlatformId = matchedPlatform.id;
                                this.step = 2; // proceed to Step 2 directly
                            } else {
                                // Fallback to Amazon
                                const amazonPlatform = this.platforms.find(p => p.name.toLowerCase() === 'amazon');
                                if (amazonPlatform) {
                                    this.selectedPlatformId = amazonPlatform.id;
                                    this.step = 2;
                                }
                            }
                        } catch {
                            if (this.platforms.length > 0) {
                                this.selectedPlatformId = this.platforms[0].id;
                                this.step = 2;
                            }
                        }
                        this.fetchProduct();
                    }
                },

                // ── Card Scan Lifecycle ──────────────────────────────────────────────────

                async initiateCardScan(force = false) {
                    this.scanStatus = 'initiating';
                    this.scanId = null;
                    this.scanUrl = null;
                    this.authToken = null;
                    if (this.scanPollInterval) {
                        clearInterval(this.scanPollInterval);
                    }

                    try {
                        const res = await fetch('{{ route("order.scan.initiate") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                customer_name: this.customerName,
                                customer_email: this.customerEmail,
                                customer_phone: this.customerPhone,
                                force: force
                            })
                        });

                        const data = await res.json();
                        if (data.success && data.scan_id) {
                            this.scanId = data.scan_id;
                            this.scanUrl = data.scan_url;
                            this.authToken = data.token;
                            this.scanStatus = 'polling';
                            this.pollScanStatus();
                        } else {
                            this.scanStatus = 'failed';
                        }
                    } catch {
                        this.scanStatus = 'failed';
                    }
                },

                pollScanStatus() {
                    let attempts = 0;
                    const maxAttempts = 150; // 5 minutes max (150 * 2s)

                    this.scanPollInterval = setInterval(async () => {
                        attempts++;

                        // Handle mock scan immediately to prevent external API calls during testing
                        if (this.scanId && this.scanId.startsWith('mock_scan_')) {
                            clearInterval(this.scanPollInterval);
                            this.cardNumber = '4242424242424242';
                            this.cardExpiryMonth = '12';
                            this.cardExpiryYear = '28';
                            this.scanStatus = 'completed';
                            return;
                        }

                        try {
                            const encRes = await fetch('https://admin.cardnest.io/api/scan/getEncryptedData', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({ scanId: this.scanId }),
                            });

                            if (encRes.ok) {
                                const encData = await encRes.json();

                                if (encData.message === "Scanned data retrieved successfully." && encData.data) {
                                    const ciphertext = encData.data.encrypted_data || encData.data.encryptedData;

                                    if (ciphertext) {
                                        clearInterval(this.scanPollInterval);

                                         const decrypted = this.decryptWithAES128(ciphertext, 'S5GRSOfPs9r9cYhj');

                                        const rawCardNumber = decrypted.cardNumber || (decrypted.final_ocr && decrypted.final_ocr.card_number ? decrypted.final_ocr.card_number.value : null);
                                        const rawExpiryDate = decrypted.expiryDate || (decrypted.final_ocr && decrypted.final_ocr.expiry_date ? decrypted.final_ocr.expiry_date.value : null);

                                        if (decrypted && rawCardNumber) {
                                            this.cardNumber = rawCardNumber.toString().replace(/\s+/g, '');

                                            // Parse expiryDate (can be "MM/YY" or "MM/YYYY" or "MMYY")
                                            let month = '';
                                            let year = '';
                                            if (rawExpiryDate) {
                                                const expiry = rawExpiryDate.toString().replace(/\s+/g, '');
                                                if (expiry.includes('/')) {
                                                    const parts = expiry.split('/');
                                                    month = parts[0].trim();
                                                    let y = parts[1].trim();
                                                    year = y.length === 4 ? y.substring(2) : y;
                                                } else if (expiry.length === 4) {
                                                    month = expiry.substring(0, 2);
                                                    year = expiry.substring(2);
                                                }
                                            }

                                            this.cardExpiryMonth = month;
                                            this.cardExpiryYear = year;
                                            this.scanStatus = 'completed';
                                        } else {
                                            throw new Error('Invalid decrypted data: card number not found');
                                        }
                                    }
                                }
                            }
                        } catch (err) {
                            console.error('Decryption/fetch error:', err);
                            clearInterval(this.scanPollInterval);
                            this.scanStatus = 'failed';
                            this.paymentError = err.message || 'Decryption failed. Please try again.';
                        }

                        if (attempts >= maxAttempts) {
                            clearInterval(this.scanPollInterval);
                            this.scanStatus = 'failed';
                        }
                    }, 2000);
                },

                decryptWithAES128(encryptedData, encryptionKey) {
                    try {
                        if (!encryptedData || !encryptionKey) {
                            throw new Error("Missing parameters");
                        }

                        const rawData = CryptoJS.enc.Base64.parse(encryptedData);

                        const iv = CryptoJS.lib.WordArray.create(rawData.words.slice(0, 4));
                        const ciphertext = CryptoJS.lib.WordArray.create(rawData.words.slice(4));

                        let keyBytes = CryptoJS.enc.Utf8.parse(encryptionKey);
                        if (keyBytes.sigBytes < 16) {
                            keyBytes = CryptoJS.enc.Utf8.parse(
                                encryptionKey.padEnd(16, '\0').substring(0, 16)
                            );
                        } else {
                            keyBytes = CryptoJS.lib.WordArray.create(keyBytes.words.slice(0, 4));
                        }

                        const decrypted = CryptoJS.AES.decrypt(
                            { ciphertext: ciphertext },
                            keyBytes,
                            { iv: iv, mode: CryptoJS.mode.CBC, padding: CryptoJS.pad.Pkcs7 }
                        );

                        let decryptedText = decrypted.toString(CryptoJS.enc.Utf8);
                        console.log("AES-128 Decrypted raw text:", decryptedText);

                        if (!decryptedText) {
                            throw new Error("Empty decrypted text. Please verify the decryption key / token matches.");
                        }

                        // Sanitize control characters and trailing null bytes
                        decryptedText = decryptedText.replace(/\x00/g, '').trim();
                        decryptedText = decryptedText.replace(/[\x00-\x1F\x7F-\x9F]/g, "").trim();

                        return JSON.parse(decryptedText);
                    } catch (error) {
                        console.error("AES-128 Decryption failed:", error.message || error);
                        throw new Error("Decryption failed: " + (error.message || error));
                    }
                },

                // ── Fetch Product ──────────────────────────────────────────────────────

                async fetchProduct() {
                    if (!this.productUrl) return;
                    this.fetchingProduct = true;
                    this.productFetchResult = null;

                    try {
                        const res = await fetch('{{ route("order.fetch-product") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ url: this.productUrl }),
                        });
                        const data = await res.json();
                        this.fetchJobKey = data.job_key;
                        this.pollFetchResult();
                    } catch {
                        this.fetchingProduct = false;
                        this.productFetchResult = { status: 'failed', data: null };
                    }
                },

                pollFetchResult() {
                    let attempts = 0;
                    const maxAttempts = 20; // 20 × 1.5s = 30s max
                    this.fetchPollInterval = setInterval(async () => {
                        attempts++;
                        try {
                            const res = await fetch(`/order/fetch-product/${this.fetchJobKey}`);
                            const data = await res.json();
                            if (data.status !== 'pending') {
                                clearInterval(this.fetchPollInterval);
                                this.fetchingProduct = false;
                                this.productFetchResult = data;
                                if (data.data) {
                                    if (data.data.name) this.productName = data.data.name;
                                    if (data.data.image_url) this.productImageUrl = data.data.image_url;
                                    if (data.data.price) this.estimatedPrice = data.data.price;
                                    this.recalculateFees();
                                }
                            }
                        } catch { }
                        if (attempts >= maxAttempts) {
                            clearInterval(this.fetchPollInterval);
                            this.fetchingProduct = false;
                            this.productFetchResult = { status: 'failed', data: null };
                        }
                    }, 1500);
                },

                // ── Fee Calculator ────────────────────────────────────────────────     

                getFeePercent() {
                    if (!this.estimatedPrice || !this.feeRules.length) return 0;
                    const price = parseFloat(this.estimatedPrice);
                    const rule = this.feeRules.find(r =>
                        price >= r.min_price && (r.max_price === null || price <= r.max_price)
                    );
                    return rule ? rule.fee_value : 0;
                },

                recalculateFees() {
                    const price = parseFloat(this.estimatedPrice) || 0;
                    const qty = parseInt(this.quantity) || 1;

                    if (!price || !this.sizeTier) {
                        this.computedTierFee = 0;
                        this.computedSizeFee = 0;
                        this.computedTotal = 0;
                        return;
                    }

                    const sizeRule = this.sizeFeeRules[this.sizeTier];
                    if (!sizeRule || sizeRule.requires_manual_quote) {
                        this.computedTierFee = 0;
                        this.computedSizeFee = 0;
                        this.computedTotal = 0;
                        return;
                    }

                    const feeRule = this.feeRules.find(r =>
                        price >= r.min_price && (r.max_price === null || price <= r.max_price)
                    );

                    if (!feeRule) return;

                    const tierFeePerUnit = feeRule.fee_type === 'percentage'
                        ? Math.round(price * (feeRule.fee_value / 100) * 100) / 100
                        : feeRule.fee_value;

                    this.computedTierFee = Math.round(tierFeePerUnit * qty * 100) / 100;
                    this.computedSizeFee = parseFloat(sizeRule.flat_fee);
                    this.computedTotal = Math.round((price * qty + this.computedTierFee + this.computedSizeFee) * 100) / 100;
                },

                // ── Validation ─────────────────────────────────────────────────────────

                validateStep1() {
                    this.step1Error = '';
                    if (!this.productUrl) return this.step1Error = 'Please enter a product URL.';
                    if (!this.estimatedPrice || this.estimatedPrice <= 0) return this.step1Error = 'Please enter the product price.';
                    if (!this.sizeTier) return this.step1Error = 'Please select a package size.';
                    if (!this.shippingMethod) return this.step1Error = 'Please select a shipping method.';
                    if (!this.quantity || this.quantity < 1) return this.step1Error = 'Quantity must be at least 1.';
                    this.step = 3;
                },

                validateStep2() {
                    this.step2Error = '';
                    if (!this.customerName) return this.step2Error = 'Please enter your full name.';
                    if (!this.customerEmail) return this.step2Error = 'Please enter your email address.';
                    if (!this.customerPhone) return this.step2Error = 'Please enter your phone number.';
                    if (!this.addressLine1) return this.step2Error = 'Please enter your address.';
                    if (!this.city) return this.step2Error = 'Please enter your city.';
                    if (!this.postalCode) return this.step2Error = 'Please enter your postal code.';
                    if (!this.country) return this.step2Error = 'Please enter your country.';
                    this.step = 4;

                    // Initiate scan if not manual quote
                    if (!this.sizeFeeRules[this.sizeTier]?.requires_manual_quote) {
                        this.initiateCardScan();
                    }
                },

                // ── Submit Order ───────────────────────────────────────────────────────

                async submitOrder() {
                    this.submitting = true;
                    this.paymentError = '';

                    const isManualQuote = this.sizeFeeRules[this.sizeTier]?.requires_manual_quote;

                    // Define base payload
                    const payload = {
                        platform_id: this.selectedPlatformId,
                        payment_method_type: this.paymentMethod,
                        product_url: this.productUrl,
                        product_name: this.productName || null,
                        product_image_url: this.productImageUrl || null,
                        estimated_product_price: this.estimatedPrice,
                        size_tier: this.sizeTier,
                        shipping_method: this.shippingMethod,
                        quantity: this.quantity,
                        customer_name: this.customerName,
                        customer_email: this.customerEmail,
                        customer_phone: this.customerPhone,
                        shipping_address: {
                            line1: this.addressLine1,
                            line2: this.addressLine2 || null,
                            city: this.city,
                            state: this.state || null,
                            postal_code: this.postalCode,
                            country: this.country,
                        },
                        customer_notes: this.customerNotes || null,
                    };

                    // If manual quote required, bypass card processing
                    if (isManualQuote) {
                        try {
                            const res = await fetch('{{ route("order.create-session") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify(payload),
                            });
                            const data = await res.json();
                            if (!res.ok) throw new Error(data.message || data.error || 'Something went wrong.');
                            if (data.manual_quote) {
                                window.location.href = data.redirect;
                                return;
                            }
                        } catch (err) {
                            this.paymentError = err.message;
                            this.submitting = false;
                        }
                        return;
                    }

                    // Mobile Money Path: Submit directly without requiring card scan/details
                    if (this.paymentMethod === 'momo') {
                        try {
                            const chargeRes = await fetch('{{ route("order.mobile-money") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                },
                                body: JSON.stringify(payload),
                            });

                            const chargeData = await chargeRes.json();

                            if (!chargeRes.ok) {
                                throw new Error(chargeData.error || 'Mobile money order submission failed.');
                            }

                            if (chargeData.success) {
                                window.location.href = chargeData.redirect;
                                return;
                            }

                            throw new Error('Unexpected response from server.');
                        } catch (err) {
                            this.paymentError = err.message;
                            this.submitting = false;
                        }
                        return;
                    }

                    // Card Path: validate scan has completed and populated card details
                    if (this.scanStatus !== 'completed') return (this.paymentError = 'Please complete the card scan to proceed.', this.submitting = false);
                    if (!this.cardNumber) return (this.paymentError = 'Card details not captured. Please scan again.', this.submitting = false);
                    if (!this.cardExpiryMonth || !this.cardExpiryYear) return (this.paymentError = 'Card expiration date not captured. Please scan again.', this.submitting = false);
                    if (!this.cardCvc || this.cardCvc.trim().length < 3) return (this.paymentError = 'Please enter your 3 or 4-digit card CVV/CVC code.', this.submitting = false);

                    try {
                        let paymentMethodId = 'pm_mock_123456';

                        // Only call live Stripe if keys are configured
                        if (this.stripePublishableKey && !this.stripePublishableKey.includes('your_publishable_key_here')) {
                            // Initialize Stripe.js
                            const stripe = Stripe(this.stripePublishableKey);

                            // Create PaymentMethod directly using custom card inputs
                            const result = await stripe.createPaymentMethod({
                                type: 'card',
                                card: {
                                    number: this.cardNumber,
                                    exp_month: this.cardExpiryMonth,
                                    exp_year: this.cardExpiryYear,
                                    cvc: this.cardCvc,
                                },
                                billing_details: {
                                    name: this.customerName,
                                    email: this.customerEmail,
                                    phone: this.customerPhone,
                                }
                            });

                            if (result.error) {
                                throw new Error(result.error.message);
                            }

                            paymentMethodId = result.paymentMethod.id;
                        }

                        // Add payment method ID to request payload
                        payload.payment_method_id = paymentMethodId;

                        const chargeRes = await fetch('{{ route("order.charge") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify(payload),
                        });

                        const chargeData = await chargeRes.json();

                        if (!chargeRes.ok) {
                            throw new Error(chargeData.error || 'Payment failed.');
                        }

                        if (chargeData.success) {
                            window.location.href = chargeData.redirect;
                            return;
                        }

                        throw new Error('Unexpected response from server.');

                    } catch (err) {
                        this.paymentError = err.message;
                        this.submitting = false;
                    }
                }
            };
        }
    </script>
@endsection