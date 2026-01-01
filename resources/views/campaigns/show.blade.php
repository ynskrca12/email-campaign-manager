@extends('layouts.app')

@section('title', $campaign->name)

@section('content')
<div x-data="{
    showImportModal: false,
    showAddRecipientModal: false,
    refreshing: false,
    async refresh() {
        this.refreshing = true;
        await new Promise(resolve => setTimeout(resolve, 500));
        window.location.reload();
    }
}">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-start mb-2">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $campaign->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $campaign->subject }}</p>
            </div>
            <div class="flex space-x-2">
                <!-- Analytics Button -->
                <a href="{{ route('campaigns.analytics', $campaign) }}"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-chart-bar mr-2"></i>Analitik Rapor
                </a>
                @if($campaign->status === 'draft' || $campaign->status === 'scheduled')
                    <form action="{{ route('campaigns.start', $campaign) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Bu kampanyayı başlatmak istediğinizden emin misiniz?')"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition">
                            <i class="fas fa-play mr-2"></i>Kampanyayı Başlat
                        </button>
                    </form>
                @endif

                @if($campaign->status === 'processing')
                    <form action="{{ route('campaigns.pause', $campaign) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition">
                            <i class="fas fa-pause mr-2"></i>Duraklat
                        </button>
                    </form>
                @endif

                <button @click="refresh()"
                        :disabled="refreshing"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition disabled:opacity-50">
                    <i class="fas fa-sync-alt mr-2" :class="{ 'fa-spin': refreshing }"></i>Yenile
                </button>

                <a href="{{ route('campaigns.index') }}"
                   class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Geri
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Toplam Alıcı</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Gönderilen</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['sent']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Bekleyen</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Başarısız</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['failed']) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700">Kampanya İlerlemesi</span>
            <span class="text-sm font-medium text-gray-900">{{ number_format($stats['progress'], 1) }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-blue-600 h-4 rounded-full transition-all duration-500"
                 style="width: {{ $stats['progress'] }}%"></div>
        </div>
        <div class="mt-2 text-xs text-gray-500">
            {{ $stats['sent'] }} / {{ $stats['total'] }} email gönderildi
        </div>
    </div>

    <!-- Campaign Details & Recipients -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Campaign Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Kampanya Detayları</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Durum</dt>
                        <dd class="mt-1">
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
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$campaign->status] }}">
                                {{ $statusLabels[$campaign->status] }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Gönderen</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $campaign->from_name }} ({{ $campaign->from_email }})</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Gecikme</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $campaign->delay_between_emails }} saniye</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Oluşturulma</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $campaign->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @if($campaign->started_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Başlangıç</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $campaign->started_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @endif
                    @if($campaign->completed_at)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tamamlanma</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $campaign->completed_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @endif
                </dl>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Email Önizleme</h3>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600 max-h-60 overflow-y-auto">
                        {{ Str::limit($campaign->body, 300) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Recipients -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Alıcı Listesi</h2>
                        <div class="flex space-x-2">
                            <button @click="showAddRecipientModal = true"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-plus mr-2"></i>Tek Alıcı Ekle
                            </button>
                            <button @click="showImportModal = true"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-file-excel mr-2"></i>Excel İçe Aktar
                            </button>
                        </div>
                    </div>
                </div>

                @if($campaign->recipients->isEmpty())
                    <div class="p-12 text-center">
                        <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Henüz alıcı yok</h3>
                        <p class="text-gray-600 mb-4">Excel dosyası ile toplu alıcı ekleyin veya tek tek ekleyin</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İsim</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($campaign->recipients as $recipient)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $recipient->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $recipient->name ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $recipientStatusColors = [
                                                'pending' => 'bg-gray-100 text-gray-800',
                                                'sent' => 'bg-green-100 text-green-800',
                                                'failed' => 'bg-red-100 text-red-800',
                                                'bounced' => 'bg-orange-100 text-orange-800',
                                            ];
                                            $recipientStatusLabels = [
                                                'pending' => 'Bekliyor',
                                                'sent' => 'Gönderildi',
                                                'failed' => 'Başarısız',
                                                'bounced' => 'Geri Döndü',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $recipientStatusColors[$recipient->status] }}">
                                            {{ $recipientStatusLabels[$recipient->status] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $recipient->sent_at ? $recipient->sent_at->format('d.m.Y H:i') : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div x-show="showImportModal"
         x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         @click.self="showImportModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Excel Dosyası İçe Aktar</h3>
                <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('recipients.import', $campaign) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Excel Dosyası (.xlsx, .xls, .csv)</label>
                    <input type="file"
                           name="file"
                           accept=".xlsx,.xls,.csv"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                    <p class="mt-2 text-xs text-gray-500">
                        Excel dosyanızda "email" ve "name" kolonları olmalıdır.
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-blue-800 font-medium mb-2">Örnek Excel Formatı:</p>
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-blue-100">
                                <th class="px-2 py-1 text-left">email</th>
                                <th class="px-2 py-1 text-left">name</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr>
                                <td class="px-2 py-1 border">ornek@mail.com</td>
                                <td class="px-2 py-1 border">Ahmet Yılmaz</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            @click="showImportModal = false"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        İptal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                        <i class="fas fa-upload mr-2"></i>İçe Aktar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Recipient Modal -->
    <div x-show="showAddRecipientModal"
         x-cloak
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
         @click.self="showAddRecipientModal = false">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Tek Alıcı Ekle</h3>
                <button @click="showAddRecipientModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('recipients.store', $campaign) }}" method="POST">
                @csrf
                <div class="space-y-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email"
                               name="email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="ornek@mail.com"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">İsim</label>
                        <input type="text"
                               name="name"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ahmet Yılmaz">
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button"
                            @click="showAddRecipientModal = false"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        İptal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        <i class="fas fa-plus mr-2"></i>Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
