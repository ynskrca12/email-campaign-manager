@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    <p class="mt-1 text-sm text-gray-600">Email kampanya yönetim sisteminize genel bakış</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Toplam Kampanya</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_campaigns']) }}</p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-bullhorn text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Aktif Kampanya</p>
                <p class="text-3xl font-bold text-yellow-600 mt-2">{{ number_format($stats['active_campaigns']) }}</p>
            </div>
            <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-spinner text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Gönderilen Email</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ number_format($stats['total_emails_sent']) }}</p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600">Başarısız Email</p>
                <p class="text-3xl font-bold text-red-600 mt-2">{{ number_format($stats['total_emails_failed']) }}</p>
            </div>
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Chart and Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Daily Stats Chart -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Son 7 Gün Email İstatistikleri</h2>
        <canvas id="emailChart"></canvas>
    </div>

    <!-- Recent Campaigns -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Son Kampanyalar</h2>
        @if($recentCampaigns->isEmpty())
            <p class="text-gray-500 text-center py-8">Henüz kampanya yok</p>
        @else
            <div class="space-y-3">
                @foreach($recentCampaigns as $campaign)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition">
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-900">{{ $campaign->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $campaign->sent_count }}/{{ $campaign->total_recipients }} gönderildi
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'processing' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-green-100 text-green-800',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$campaign->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($campaign->status) }}
                        </span>
                        <a href="{{ route('campaigns.show', $campaign) }}"
                           class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Recent Email Logs -->
<div class="bg-white rounded-lg border border-gray-200">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Son Email Logları</h2>
    </div>
    @if($recentLogs->isEmpty())
        <div class="p-12 text-center">
            <p class="text-gray-500">Henüz email gönderilmedi</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alıcı</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Konu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentLogs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $log->to_name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $log->to_email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ Str::limit($log->subject, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->status === 'sent')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Gönderildi
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Başarısız
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $log->sent_at->format('d.m.Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('emailChart').getContext('2d');

    const dates = @json($dailyStats->pluck('date'));
    const sent = @json($dailyStats->pluck('sent'));
    const failed = @json($dailyStats->pluck('failed'));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.map(date => {
                const d = new Date(date);
                return d.toLocaleDateString('tr-TR', { month: 'short', day: 'numeric' });
            }),
            datasets: [
                {
                    label: 'Gönderilen',
                    data: sent,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Başarısız',
                    data: failed,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
