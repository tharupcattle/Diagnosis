<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Tharup - AI-Powered Cattle Health Monitoring & Milk Yield Optimization">
    <title>Dashboard - Tharup Cattle Monitoring</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for Interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js for Graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar-active {
            background: #ecfdf5;
            color: #059669;
            border-right: 4px solid #059669;
        }

        @keyframes pulse-red {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .pulse-alert {
            animation: pulse-red 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Mobile responsive improvements */
        @media (max-width: 768px) {
            .mobile-hidden {
                display: none;
            }
        }

        /* Smooth transitions */
        * {
            transition: all 0.3s ease;
        }

        /* Progress bar for health score */
        .health-progress {
            transition: width 0.5s ease-in-out;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900" x-data="{ 
    mobileMenuOpen: false,
    dashboardData: {
        totalCattle: {{ $cattle->count() }},
        healthyCattle: {{ $cattle->count() - $alerts->unique('cattle_id')->count() }},
        activeAlerts: {{ $alerts->count() }},
        avgMilkYield: 0
    }
}">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar - Desktop -->
        <aside class="w-64 bg-white border-r border-gray-100 flex-shrink-0 hidden md:flex flex-col">
            <div class="p-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white font-bold">
                        T</div>
                    <span class="text-xl font-bold tracking-tight text-gray-800">Tharup</span>
                </div>
            </div>
            <nav class="flex-1 px-4 space-y-2 mt-4">
                <a href="{{ route('dashboard') }}"
                    class="sidebar-active flex items-center gap-3 px-4 py-3 rounded-xl transition font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Overview
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl transition font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2H7a1 1 0 100-2h.01zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                    All Cattle
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl transition font-medium relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                    Alert Logs
                    @if($alerts->count() > 0)
                        <span class="absolute right-4 top-3.5 w-2 h-2 bg-red-500 rounded-full pulse-alert"></span>
                    @endif
                </a>
            </nav>
            <div class="p-4 mt-auto">
                <div class="p-4 bg-emerald-50 rounded-2xl">
                    <p class="text-xs font-bold text-emerald-700 uppercase mb-2">Sync Status</p>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                        <span class="text-sm text-emerald-800 font-medium">Gateway Online</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Menu Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-lg shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Mobile Menu Overlay -->
        <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false" class="md:hidden fixed inset-0 bg-black bg-opacity-50 z-40" x-cloak></div>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="h-16 bg-white border-b border-gray-100 flex items-center justify-between px-4 md:px-8">
                <h2 class="text-lg md:text-xl font-bold text-gray-800 ml-12 md:ml-0">Livestock Overview</h2>
                <div class="flex items-center gap-2 md:gap-4">
                    <form action="{{ route('simulate') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 md:px-4 py-2 bg-red-100 text-red-600 rounded-xl font-bold text-xs hover:bg-red-200 transition">
                            Simulate Issue
                        </button>
                    </form>
                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gray-200 rounded-full overflow-hidden border-2 border-white shadow-sm">
                        <img src="https://ui-avatars.com/api/?name=Farmer+Pavi&background=059669&color=fff" alt="Avatar">
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-4 md:p-8">
                <!-- AI Symptom Checker -->
                <div class="mb-6 md:mb-8 p-4 md:p-6 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl md:rounded-3xl shadow-sm">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">🤖 AI Health Assistant (Gemini-Powered)</h3>
                            <p class="text-xs text-gray-500">Describe symptoms for instant AI analysis</p>
                        </div>
                    </div>
                    <form action="{{ route('check.symptom') }}" method="POST" class="flex flex-col md:flex-row gap-3 md:gap-4">
                        @csrf
                        <input type="text" name="symptom" placeholder="e.g. 'Yamuna has a swollen stomach' or 'Ganga is not eating'"
                            class="flex-1 bg-white border border-emerald-200 px-4 py-3 rounded-xl md:rounded-2xl focus:outline-none focus:border-emerald-500 transition text-sm md:text-base">
                        <button type="submit" class="bg-emerald-600 text-white px-6 py-3 rounded-xl md:rounded-2xl font-bold hover:bg-emerald-700 transition shadow-lg">
                            Analyze with AI
                        </button>
                    </form>

                    @if(isset($diagnosis))
                        <div class="mt-4 md:mt-6 p-4 bg-white border border-orange-200 rounded-xl md:rounded-2xl flex items-start gap-3 shadow-sm">
                            <span class="text-2xl">💡</span>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-orange-900 mb-1">AI Analysis for "{{ $diagnosis['symptom'] }}":</p>
                                <p class="text-orange-800 text-sm">{{ $diagnosis['advice'] }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Urgent Alerts -->
                @if($alerts->count() > 0)
                    <div class="mb-6 md:mb-8">
                        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">⚠️ Urgent Attention Required</h3>
                        <div class="space-y-4">
                            @foreach($alerts as $alert)
                                <div class="p-4 md:p-5 bg-red-50 border-2 border-red-200 rounded-xl md:rounded-2xl flex flex-col md:flex-row items-start gap-4 shadow-md">
                                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0 pulse-alert">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-1 gap-2">
                                            <h4 class="font-bold text-red-800 text-lg">{{ $alert->type }} - {{ $alert->cattle->name }}</h4>
                                            <span class="text-xs font-semibold bg-red-600 text-white px-3 py-1 rounded-full uppercase w-fit">{{ $alert->severity }}</span>
                                        </div>
                                        <p class="text-red-700 text-sm opacity-90 mb-3">{{ $alert->description }}</p>

                                        @if($alert->severity === 'critical' || $alert->severity === 'high')
                                            <div class="bg-white/70 p-3 md:p-4 rounded-xl md:rounded-2xl border border-red-200 mb-2">
                                                <p class="text-[10px] font-bold text-red-800 uppercase mb-2 tracking-wider">⚡ Immediate Life-Saving Steps:</p>
                                                <ul class="text-xs text-red-700 space-y-1.5 list-disc ml-4 font-medium">
                                                    <li>Isolate the animal to a dry, shaded area immediately.</li>
                                                    <li>Remove all concentrate/grain feed from the manger.</li>
                                                    <li>Provide fresh water and high-quality dry fiber (hay).</li>
                                                    <li><strong>Emergency Vet Call:</strong> 1800-425-XXXX (Livestock Helpline)</li>
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                    <button class="bg-white text-red-600 px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-red-50 transition border border-red-200 w-full md:w-auto">
                                        Fix Now
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-6 mb-6 md:mb-8">
                    <div class="bg-white p-4 md:p-6 rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm">
                        <p class="text-gray-400 text-xs md:text-sm font-semibold mb-1">Total Cattle</p>
                        <h4 class="text-2xl md:text-3xl font-bold text-gray-800">{{ $cattle->count() }}</h4>
                    </div>
                    <div class="bg-white p-4 md:p-6 rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm">
                        <p class="text-gray-400 text-xs md:text-sm font-semibold mb-1">Optimal Health</p>
                        <h4 class="text-2xl md:text-3xl font-bold text-emerald-600">
                            {{ $cattle->count() - $alerts->unique('cattle_id')->count() }}
                        </h4>
                    </div>
                    <div class="bg-white p-4 md:p-6 rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm">
                        <p class="text-gray-400 text-xs md:text-sm font-semibold mb-1">Active Alerts</p>
                        <h4 class="text-2xl md:text-3xl font-bold text-red-600">{{ $alerts->count() }}</h4>
                    </div>
                    <div class="bg-white p-4 md:p-6 rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm">
                        <p class="text-gray-400 text-xs md:text-sm font-semibold mb-1">Health Index</p>
                        <h4 class="text-2xl md:text-3xl font-bold text-gray-800">
                            {{ $cattle->where('health_score', '>', 0)->count() > 0 ? round($cattle->avg('health_score'), 0) : 84 }}%
                        </h4>
                    </div>
                </div>

                <!-- Cattle Table -->
                <div class="bg-white rounded-2xl md:rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-4 md:p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <h3 class="font-bold text-gray-800">Livestock Health Logs</h3>
                        <button class="text-emerald-600 font-bold text-sm text-left md:text-right">View Detailed Reports</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 text-gray-400 uppercase text-xs font-bold tracking-wider">
                                <tr>
                                    <th class="px-4 md:px-6 py-3 md:py-4">Cattle Name</th>
                                    <th class="px-4 md:px-6 py-3 md:py-4 mobile-hidden">pH</th>
                                    <th class="px-4 md:px-6 py-3 md:py-4 mobile-hidden">Rumination</th>
                                    <th class="px-4 md:px-6 py-3 md:py-4 mobile-hidden">Temp</th>
                                    <th class="px-4 md:px-6 py-3 md:py-4">Status</th>
                                    <th class="px-4 md:px-6 py-3 md:py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($cattle as $cow)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 md:px-6 py-4 md:py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 md:w-10 md:h-10 bg-gray-100 rounded-xl flex items-center justify-center font-bold text-gray-400 text-sm md:text-base">
                                                    {{ substr($cow->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-800 text-sm md:text-base">{{ $cow->name }}</p>
                                                    <p class="text-xs text-gray-400">{{ $cow->tag_id }} • {{ $cow->breed }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 md:px-6 py-4 md:py-5 mobile-hidden">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold {{ optional($cow->latestLog)->ph_level < 6.0 ? 'text-red-600' : 'text-gray-700' }} text-sm md:text-base">
                                                    {{ optional($cow->latestLog)->ph_level ?? '--' }}
                                                </span>
                                                @if(optional($cow->latestLog)->ph_level < 6.0)
                                                    <span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold uppercase">Acidic</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 md:px-6 py-4 md:py-5 mobile-hidden">
                                            <p class="font-medium text-gray-700 text-sm md:text-base">
                                                {{ optional($cow->latestLog)->rumination_rate ?? '--' }} <span class="text-xs text-gray-400">/min</span>
                                            </p>
                                        </td>
                                        <td class="px-4 md:px-6 py-4 md:py-5 mobile-hidden">
                                            <p class="font-medium text-gray-700 text-sm md:text-base">{{ optional($cow->latestLog)->temperature ?? '--' }}°C</p>
                                        </td>
                                        <td class="px-4 md:px-6 py-4 md:py-5">
                                            @if($cow->unresolvedAlerts->count() > 0)
                                                <span class="px-2 md:px-3 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold uppercase tracking-wider">Critical</span>
                                            @else
                                                <span class="px-2 md:px-3 py-1 bg-emerald-100 text-emerald-600 rounded-full text-xs font-bold uppercase tracking-wider">Stable</span>
                                            @endif
                                        </td>
                                        <td class="px-4 md:px-6 py-4 md:py-5 text-right">
                                            <button class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- PWA Manifest -->
    <script>
        // Service Worker for PWA (offline capability)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }
    </script>
</body>

</html>