@extends('layouts.app')

@section('title', 'Kampanya Analitiği - ' . $campaign->name)

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $campaign->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">Email Tracking ve Analitik Raporları</p>
        </div>
        <a href="{{ route('campaigns.show', $campaign) }}"
           class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left mr-2"></i>Geri
        </a>
    </div>
</div>

<!-- Main Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Toplam Gönderim</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['sent']) }}</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-paper-plane text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Açılma Oranı</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['open_rate'] }}%</p>
                <p class="text-xs text-gray-500 mt-1">{{ number_format($stats['unique_opens']) }} kişi</p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-envelope-open text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Tıklama Oranı</p>
                <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['click_rate'] }}%</p>
                <p class="text-xs text-gray-500 mt-1">{{ number_format($stats['unique_clicks']) }} kişi</p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-mouse-pointer text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Toplam Etkileşim</p>
                <p class="text-3xl font-bold text-orange-600 mt-2">{{ number_format($stats['total_opens'] + $stats['total_clicks']) }}</p>
                <p class="text-xs text-gray-500 mt-1">açılma + tıklama</p>
            </div>
            <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-line text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Device Stats -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Cihaz Dağılımı</h2>
        <canvas id="deviceChart"></canvas>
        <div class="mt-4 space-y-2">
            @foreach($deviceStats as $device)
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <i class="fas fa-{{ $device->device === 'mobile' ? 'mobile-alt' : ($device->device === 'tablet' ? 'tablet-alt' : 'desktop') }} text-gray-500 mr-2"></i>
                    <span class="text-sm text-gray-700 capitalize">{{ $device->device ?? 'Bilinmeyen' }}</span>
                </div>
                <span class="text-sm font-medium text-gray-900">{{ number_format($device->count) }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Browser Stats -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Tarayıcı Dağılımı</h2>
        <canvas id="browserChart"></canvas>
        <div class="mt-4 space-y-2">
            @foreach($browserStats as $browser)
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-700">{{ $browser->browser ?? 'Bilinmeyen' }}</span>
                <span class="text-sm font-medium text-gray-900">{{ number_format($browser->count) }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Hourly Opens Chart -->
<div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Saatlik Açılma Grafiği</h2>
    <canvas id="hourlyChart"></canvas>
</div>

<!-- Top Links -->
@if($topLinks->isNotEmpty())
<div class="bg-white rounded-lg border border-gray-200 mb-8">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">En Çok Tıklanan Linkler</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tıklama Sayısı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tıklama Oranı</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($topLinks as $link)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <a href="{{ $link->original_url }}" target="_blank" class="text-blue-600 hover:underline">
                            {{ Str::limit($link->original_url, 60) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ number_format($link->click_count) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ $stats['sent'] > 0 ? round(($link->click_count / $stats['sent']) * 100, 2) : 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Geographic Distribution -->
@if($countryStats->isNotEmpty())
<div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Coğrafi Dağılım</h2>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach($countryStats as $country)
        <div class="text-center p-4 bg-gray-50 rounded-lg">
            <p class="text-2xl font-bold text-gray-900">{{ number_format($country->count) }}</p>
            <p class="text-sm text-gray-600 mt-1">{{ $country->country ?? 'Bilinmeyen' }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recent Activity Tabs -->
<div class="bg-white rounded-lg border border-gray-200" x-data="{ tab: 'opens' }">
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button @click="tab = 'opens'"
                    :class="tab === 'opens' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-4 text-sm font-medium border-b-2 transition">
                Son Açılmalar ({{ $recentOpens->count() }})
            </button>
            <button @click="tab = 'clicks'"
                    :class="tab === 'clicks' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-4 text-sm font-medium border-b-2 transition">
                Son Tıklamalar ({{ $recentClicks->count() }})
            </button>
        </nav>
    </div>

    <!-- Opens Tab -->
    <div x-show="tab === 'opens'" class="p-6">
        @if($recentOpens->isEmpty())
            <p class="text-gray-500 text-center py-8">Henüz açılma kaydı yok</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alıcı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cihaz</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarayıcı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Konum</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentOpens as $open)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $open->recipient?->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $open->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <i class="fas fa-{{ $open->device === 'mobile' ? 'mobile-alt' : ($open->device === 'tablet' ? 'tablet-alt' : 'desktop') }} mr-1"></i>
                                {{ ucfirst($open->device ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $open->browser ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $open->city ? $open->city . ', ' : '' }}{{ $open->country ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $open->opened_at->format('d.m.Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Clicks Tab -->
    <div x-show="tab === 'clicks'" x-cloak class="p-6">
        @if($recentClicks->isEmpty())
            <p class="text-gray-500 text-center py-8">Henüz tıklama kaydı yok</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alıcı</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">URL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cihaz</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentClicks as $click)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $click->recipient?->name ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $click->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-blue-600">
                                <a href="{{ $click->original_url }}" target="_blank" class="hover:underline">
                                    {{ Str::limit($click->original_url, 50) }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <i class="fas fa-{{ $click->device === 'mobile' ? 'mobile-alt' : ($click->device === 'tablet' ? 'tablet-alt' : 'desktop') }} mr-1"></i>
                                {{ ucfirst($click->device ?? 'N/A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $click->clicked_at->format('d.m.Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Device Chart
    const deviceCtx = document.getElementById('deviceChart').getContext('2d');
    new Chart(deviceCtx, {
        type: 'doughnut',
        data: {
            labels: @json($deviceStats->pluck('device')->map(fn($d) => ucfirst($d ?? 'Unknown'))),
            datasets: [{
                data: @json($deviceStats->pluck('count')),
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Browser Chart
    const browserCtx = document.getElementById('browserChart').getContext('2d');
    new Chart(browserCtx, {
        type: 'bar',
        data: {
            labels: @json($browserStats->pluck('browser')->map(fn($b) => $b ?? 'Unknown')),
            datasets: [{
                label: 'Açılma',
                data: @json($browserStats->pluck('count')),
                backgroundColor: '#667eea',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Hourly Chart
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'line',
        data: {
            labels: @json($hourlyOpens->pluck('hour')->map(fn($h) => $h . ':00')),
            datasets: [{
                label: 'Açılma Sayısı',
                data: @json($hourlyOpens->pluck('count')),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
</script>
@endpush
@endsection
