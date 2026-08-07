<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Uptown Towing & Shipping LLC') – USA to Ghana Shipping</title>
    <meta name="description" content="@yield('meta_description', 'Uptown Towing & Shipping LLC — all year round shipment from USA to Ghana. Pickup and Delivery Service available.')">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50:  '#fbf8f5',
                            100: '#f4e8de',
                            200: '#ebd6c6',
                            300: '#dcb18c',
                            400: '#c89668',
                            500: '#6e2a18',
                            600: '#591f10',
                            700: '#45170b',
                            800: '#310f06',
                            900: '#1d0803',
                            950: '#0d0301',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: 0 }, '100%': { opacity: 1 } },
                        slideUp: { '0%': { opacity: 0, transform: 'translateY(16px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>

    <style>
        [x-cloak] { display: none !important; }

        .glass {
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.12);
        }

        .glass-light {
            background: rgba(255, 253, 250, 0.92);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(110, 42, 24, 0.15);
            box-shadow: 0 10px 30px -10px rgba(29, 8, 3, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.6);
        }

        .gradient-text {
            background: linear-gradient(135deg, #dcb18c, #c89668, #6e2a18);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6e2a18, #8c3620);
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px rgba(110, 42, 24, 0.4);
            border: 1px solid rgba(220, 177, 140, 0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #dcb18c, #c89668);
            color: #1d0803;
            box-shadow: 0 8px 25px rgba(220, 177, 140, 0.4), 0 0 20px rgba(220, 177, 140, 0.2);
            border-color: #dcb18c;
            transform: translateY(-2px);
        }
        .btn-primary:active { transform: translateY(0); }

        .input-field {
            background: rgba(255,255,255,0.92);
            border: 1.5px solid rgba(110, 42, 24, 0.2);
            transition: all 0.2s ease;
        }
        .input-field:focus {
            border-color: #6e2a18;
            box-shadow: 0 0 0 3px rgba(110, 42, 24, 0.15);
            outline: none;
        }

        .step-dot {
            transition: all 0.3s ease;
        }
        .step-dot.active {
            background: linear-gradient(135deg, #6e2a18, #8c3620);
            border: 1px solid rgba(220, 177, 140, 0.4);
            animation: pulse-border 2s infinite;
        }

        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(110, 42, 24, 0.6); }
            70% { box-shadow: 0 0 0 10px rgba(110, 42, 24, 0); }
            100% { box-shadow: 0 0 0 0 rgba(110, 42, 24, 0); }
        }

        .fee-card {
            background: linear-gradient(135deg, rgba(110,42,24,0.06), rgba(220,177,140,0.08));
            border: 1px solid rgba(110, 42, 24, 0.18);
            transition: all 0.3s ease;
        }

        .platform-badge {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        /* Smooth height transition for fee breakdown */
        .fee-breakdown { transition: all 0.3s ease; }

        /* Loading spinner */
        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 0.8s linear infinite; }

        .status-timeline-dot {
            width: 12px; height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 0 3px;
        }
    </style>
</head>
<body class="min-h-screen font-sans" style="background: radial-gradient(circle at 50% 30%, #45170b 0%, #1d0803 60%, #0d0301 100%);">

    <nav class="fixed top-0 left-0 right-0 z-50 py-4 px-6 bg-brand-950/70 backdrop-blur-md border-b border-white/5">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ route('order.index') }}" class="flex items-center gap-3 group">
                <img src="/images/logo_icon.png" alt="Uptown Towing & Shipping LLC Logo" class="w-10 h-10 object-contain rounded-xl bg-white p-0.5 border border-white/20 shadow-sm">
                <span class="text-white font-display font-bold text-xl tracking-tight">Uptown<span class="gradient-text"> Shipping</span></span>
            </a>
            <div class="flex items-center gap-4">
                <a href="{{ route('order.index') }}" class="text-white/70 hover:text-white text-sm font-medium transition-colors">Order</a>
                <a href="#" onclick="document.getElementById('resend-modal').classList.remove('hidden')" class="text-white/70 hover:text-white text-sm font-medium transition-colors">Track Order</a>
            </div>
        </div>
    </nav>

    <!-- Track Order Modal -->
    <div id="resend-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.7);">
        <div class="glass-light rounded-2xl p-8 max-w-md w-full shadow-2xl animate-slide-up">
            <h3 class="text-xl font-display font-bold text-gray-900 mb-2">Track Your Order</h3>
            <p class="text-gray-500 text-sm mb-6">Enter your order number to track your package status.</p>
            <form action="{{ route('order.track-public') }}" method="GET" class="space-y-4">
                <input type="text" name="order_number" required placeholder="Order Number (e.g. PP-1042)"
                    class="input-field w-full px-4 py-3 rounded-xl text-gray-800 text-sm">
                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('resend-modal').classList.add('hidden')"
                        class="flex-1 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 btn-primary text-white py-3 rounded-xl text-sm font-semibold">
                        Track Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <main class="pt-24 pb-16 min-h-screen">
        @if ($errors->any())
            <div class="max-w-2xl mx-auto px-4 mb-6">
                <div class="bg-red-500/10 border border-red-500/20 text-red-200 rounded-2xl p-4 text-sm font-medium">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="py-12 px-6 border-t border-white/10 mt-12 bg-black/20">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 text-white/60 text-sm">
            <!-- Branding Column -->
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <img src="/images/logo_icon.png" alt="Uptown Towing & Shipping LLC Logo" class="w-8 h-8 object-contain rounded-lg bg-white p-0.5 border border-white/10">
                    <span class="text-white font-display font-bold text-lg">Uptown Shipping</span>
                </div>
                <p class="text-white/40 text-xs leading-relaxed">
                    Reliable shipment and forwarding from USA to Ghana all year round. Safe delivery directly to your destination.
                </p>
            </div>
            
            <!-- Contact details from Flyer -->
            <div class="space-y-2">
                <h4 class="text-white font-semibold text-xs uppercase tracking-wider">Contact Us</h4>
                <p class="flex items-center gap-2 text-xs">
                    <span>📞</span> <a href="tel:8042395736" class="hover:text-white transition-colors">804-239-5736</a>
                </p>
                <p class="flex items-center gap-2 text-xs">
                    <span>✉️</span> <a href="mailto:deultimate143@yahoo.com" class="hover:text-white transition-colors">deultimate143@yahoo.com</a>
                </p>
                <p class="flex items-start gap-2 text-xs">
                    <span>📍</span> <span class="leading-tight">6341 Dawnfield Lane,<br>Henrico VA 23231</span>
                </p>
            </div>

            <!-- Secured & Copyright Column -->
            <div class="space-y-4 md:text-right flex flex-col md:items-end justify-between">
                <div>
                    <h4 class="text-white font-semibold text-xs uppercase tracking-wider mb-2">Secure Payments</h4>
                    <p class="text-xs">Payments processed securely by <span class="text-white font-medium">Stripe</span></p>
                </div>
                <p class="text-white/40 text-xs mt-auto">© {{ date('Y') }} Uptown Towing & Shipping LLC. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
