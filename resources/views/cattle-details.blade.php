<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cattle->name }} - Details | Tharup</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-50" style="font-family: 'Plus Jakarta Sans', sans-serif;">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white border-b border-gray-100 px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $cattle->name }}</h1>
                        <p class="text-sm text-gray-500">{{ $cattle->tag_id }} • {{ $cattle->breed }} •
                            {{ $cattle->age }} years old</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @if($cattle->health_score)
                        <div class="text-right">
                            <p class="text-xs text-gray-400 uppercase font-bold">Health Score</p>
                            <p
                                class="text-3xl font-bold {{ $cattle->health_score >= 70 ? 'text-emerald-600' : ($cattle->health_score >= 40 ? 'text-orange-500' : 'text-red-600') }}">
                                {{ round($cattle->health_score) }}%
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <div class="p-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Risk Level</p>
                    <p
                        class="text-2xl font-bold capitalize {{ $cattle->risk_level === 'critical' ? 'text-red-600' : ($cattle->risk_level === 'high' ? 'text-orange-500' : 'text-emerald-600') }}">
                        {{ $cattle->risk_level ?? 'Unknown' }}
                    </p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Avg Milk Yield</p>
                    <p class="text-2xl font-bold text-gray-800">{{ round($cattle->average_daily_yield ?? 0, 1) }}L</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Peak Yield</p>
                    <p class="text-2xl font-bold text-gray-800">{{ round($cattle->peak_yield ?? 0, 1) }}L</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Lactation Day</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $cattle->getLactationDay() ?? '--' }}</p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Health Trend Chart -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">📊 Health Score Trend (30 Days)</h3>
                    <canvas id="healthChart" height="250"></canvas>
                </div>

                <!-- Milk Yield Chart -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">🥛 Milk Production Trend</h3>
                    <canvas id="milkChart" height="250"></canvas>
                </div>
            </div>

            <!-- AI Analysis Section -->
            @if(isset($claudeAnalysis) && $claudeAnalysis['status'] === 'success')
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-100 rounded-2xl p-6 mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">🧠</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Claude AI Health Analysis</h3>
                            <p class="text-xs text-gray-500">Advanced veterinary insights powered by AI</p>
                        </div>
                    </div>
                    <div class="bg-white/70 p-4 rounded-xl text-sm text-gray-800 whitespace-pre-line">
                        {{ $claudeAnalysis['analysis'] }}
                    </div>
                </div>
            @endif

            <!-- Feed Optimization -->
            @if(isset($feedOptimization) && $feedOptimization['status'] === 'success')
                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-6 mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center">
                            <span class="text-white text-xl">🌾</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Feed Optimization Recommendations</h3>
                            <p class="text-xs text-gray-500">Maximize milk yield with personalized nutrition</p>
                        </div>
                    </div>
                    <div class="bg-white/70 p-4 rounded-xl text-sm text-gray-800 whitespace-pre-line">
                        {{ $feedOptimization['analysis'] }}
                    </div>
                </div>
            @endif

            <!-- Recent Health Logs -->
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-8">
                <div class="p-6 border-b border-gray-50">
                    <h3 class="font-bold text-gray-800">📝 Recent Health Logs</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-400">
                            <tr>
                                <th class="px-6 py-3 text-left">Date</th>
                                <th class="px-6 py-3 text-left">pH</th>
                                <th class="px-6 py-3 text-left">Rumination</th>
                                <th class="px-6 py-3 text-left">Temp</th>
                                <th class="px-6 py-3 text-left">Health Score</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($cattle->healthLogs->take(10) as $log)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm">{{ $log->created_at->format('M d, Y H:i') }}</td>
                                    <td
                                        class="px-6 py-4 font-semibold {{ $log->ph_level < 6.0 ? 'text-red-600' : 'text-gray-700' }}">
                                        {{ $log->ph_level }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">{{ $log->rumination_rate }}/min</td>
                                    <td class="px-6 py-4 text-gray-700">{{ $log->temperature }}°C</td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-bold {{ $log->health_score >= 70 ? 'bg-emerald-100 text-emerald-700' : ($log->health_score >= 40 ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                                            {{ round($log->health_score) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Health Score Chart
        const healthCtx = document.getElementById('healthChart').getContext('2d');
        const healthData = @json($cattle->healthLogs->take(30)->reverse()->values()->map(function ($log) {
            return [
                'date' => $log->created_at->format('M d'),
                'score' => $log->health_score ?? 0
            ];
        }));

        new Chart(healthCtx, {
            type: 'line',
            data: {
                labels: healthData.map(d => d.date),
                datasets: [{
                    label: 'Health Score',
                    data: healthData.map(d => d.score),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: value => value + '%' }
                    }
                }
            }
        });

        // Milk Production Chart
        const milkCtx = document.getElementById('milkChart').getContext('2d');
        const milkData = @json($cattle->milkProduction->take(30)->reverse()->values()->map(function ($prod) {
            return [
                'date' => $prod->production_date->format('M d'),
                'yield' => $prod->total_daily_yield
            ];
        }));

        new Chart(milkCtx, {
            type: 'bar',
            data: {
                labels: milkData.map(d => d.date),
                datasets: [{
                    label: 'Daily Yield (L)',
                    data: milkData.map(d => d.yield),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: value => value + 'L' }
                    }
                }
            }
        });
    </script>
</body>

</html>