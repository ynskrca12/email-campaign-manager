@extends('layouts.app')

@section('title', 'Yeni Kampanya Oluştur')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Yeni Email Kampanyası</h1>
        <p class="mt-1 text-sm text-gray-600">Toplu email gönderimi için yeni bir kampanya oluşturun</p>
    </div>

    <form action="{{ route('campaigns.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Kampanya Bilgileri</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kampanya Adı *</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Örn: Ocak 2025 Bülten"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gönderen Email *</label>
                    <input type="email"
                           name="from_email"
                           value="{{ old('from_email', config('mail.from.address')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="info@sirket.com"
                           required>
                    @error('from_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gönderen Adı *</label>
                    <input type="text"
                           name="from_name"
                           value="Sigorta Yönetim Sistemi"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Şirket Adı"
                           required>
                    @error('from_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Konusu *</label>
                    <input type="text"
                           name="subject"
                           value="{{ old('subject') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Email konusunu girin"
                           required>
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email İçeriği *</label>
                    <div class="text-xs text-gray-500 mb-2">
                        Kullanılabilir değişkenler: @{{name}}, @{{email}}
                    </div>
                    <textarea name="body"
                            rows="10"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                            placeholder="Merhaba @{{name}},&#10;&#10;Email içeriğiniz buraya gelecek..."
                            required>{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Emailler Arası Gecikme (saniye)</label>
                    <input type="number"
                           name="delay_between_emails"
                           value="{{ old('delay_between_emails', 1) }}"
                           min="1"
                           max="60"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">Her email arasında beklenecek süre (önerilen: 1-5 saniye)</p>
                    @error('delay_between_emails')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('campaigns.index') }}"
               class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                İptal
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                Kampanya Oluştur
            </button>
        </div>
    </form>
</div>
@endsection
