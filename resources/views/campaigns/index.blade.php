@extends('layouts.app')

@section('title', 'Kampanyalar')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Email Kampanyaları</h1>
        <p class="mt-1 text-sm text-gray-600">Tüm email kampanyalarınızı yönetin</p>
    </div>
    <a href="{{ route('campaigns.create') }}"
       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>
        Yeni Kampanya
    </a>
</div>

@if($campaigns->isEmpty())
    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
        <i class="fas fa-envelope text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Henüz kampanya yok</h3>
        <p class="text-gray-600 mb-6">İlk email kampanyanızı oluşturarak başlayın</p>
        <a href="{{ route('campaigns.create') }}"
           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Kampanya Oluştur
        </a>
    </div>
@else
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kampanya</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İlerleme</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alıcılar</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($campaigns as $campaign)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $campaign->name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($campaign->subject, 50) }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'scheduled' => 'bg-blue-100 text-blue-800',
                                'processing' => 'bg-yellow-100 text-yellow-800',
                                'completed' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                            ];
                            $statusLabels = [
                                'draft' => 'Taslak',
                                'scheduled' => 'Planlandı',
                                'processing' => 'İşleniyor',
                                'completed' => 'Tamamlandı',
                                'failed' => 'Başarısız',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$campaign->status] }}">
                            {{ $statusLabels[$campaign->status] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $campaign->progress_percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-700">{{ number_format($campaign->progress_percentage, 0) }}%</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $campaign->sent_count }}/{{ $campaign->total_recipients }} gönderildi
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($campaign->recipients_count) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $campaign->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('campaigns.show', $campaign) }}"
                           class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('campaigns.destroy', $campaign) }}"
                              method="POST"
                              class="inline"
                              onsubmit="return confirm('Bu kampanyayı silmek istediğinizden emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $campaigns->links() }}
    </div>
@endif
@endsection
