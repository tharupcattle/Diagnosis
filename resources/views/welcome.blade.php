<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tharup - Advanced Cattle Health Monitoring</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .gradient-text {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white font-bold text-2xl">
                        T</div>
                    <span class="text-xl font-bold tracking-tight text-gray-800">Tharup</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#" class="text-gray-600 hover:text-emerald-600 font-medium transition">Solutions</a>
                    <a href="#" class="text-gray-600 hover:text-emerald-600 font-medium transition">How it Works</a>
                    <a href="#" class="text-gray-600 hover:text-emerald-600 font-medium transition">Testimonials</a>
                    <a href="#"
                        class="bg-emerald-600 text-white px-6 py-2 rounded-full font-semibold hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">Get
                        Started</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <span
                    class="inline-block px-4 py-1.5 mb-6 text-sm font-semibold tracking-wide text-emerald-700 uppercase bg-emerald-100 rounded-full">Protecting
                    Your Livestock Pulse</span>
                <h1 class="text-5xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                    Smart Health Monitoring for <span class="gradient-text">Healthy Cattle.</span>
                </h1>
                <p class="text-lg lg:text-xl text-gray-600 mb-10 leading-relaxed">
                    Prevent mortality through early detection of digestive issues and rumination drops.
                    Real-time data at your fingertips, saving cows and empowering farmers.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="/dashboard"
                        class="w-full sm:w-auto px-8 py-4 bg-emerald-600 text-white rounded-2xl font-bold text-lg hover:bg-emerald-700 transition shadow-xl shadow-emerald-100 flex items-center justify-center gap-2">
                        Open Dashboard
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#"
                        class="w-full sm:w-auto px-8 py-4 bg-white text-gray-800 border-2 border-gray-100 rounded-2xl font-bold text-lg hover:border-emerald-200 transition">
                        Watch Demo
                    </a>
                </div>

                <!-- Hero Image -->
                <div class="mt-16 relative">
                    <div class="absolute inset-0 bg-emerald-500 rounded-3xl blur-2xl opacity-10 -rotate-2"></div>
                    <img src="/images/hero.png" alt="Smart Farming"
                        class="relative rounded-3xl shadow-2xl border border-white/20 w-full max-w-5xl mx-auto">
                </div>
            </div>
        </div>

        <!-- Background Elements -->
        <div
            class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-[600px] h-[600px] bg-emerald-50 rounded-full blur-3xl opacity-50">
        </div>
        <div
            class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/2 w-[400px] h-[400px] bg-blue-50 rounded-full blur-3xl opacity-50">
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-12">
                <div
                    class="p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:border-emerald-200 transition group">
                    <div
                        class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-emerald-600 group-hover:text-white"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Digestion Analysis</h3>
                    <p class="text-gray-600">Real-time pH and rumination monitoring to detect bloat and acidosis before
                        they become fatal.</p>
                </div>
                <div
                    class="p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:border-emerald-200 transition group">
                    <div
                        class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 group-hover:text-white"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Instant Alerts</h3>
                    <p class="text-gray-600">Push notifications sent directly to farmers as soon as an anomaly is
                        detected in the livestock.</p>
                </div>
                <div
                    class="p-8 rounded-3xl bg-gray-50 border border-gray-100 hover:border-emerald-200 transition group">
                    <div
                        class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-orange-600 group-hover:text-white"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Predictive Health</h3>
                    <p class="text-gray-600">AI-driven insights that predict potential illness up to 24 hours in
                        advance.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 py-12 text-white">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-400">© 2026 Tharup Project. Protecting our Cattle, Empowering our Farmers.</p>
        </div>
    </footer>
</body>

</html>