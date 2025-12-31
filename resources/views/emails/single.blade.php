@extends('layouts.app')

@section('title', 'Tek Email Gönder')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Tek Email Gönder</h1>
        <p class="mt-1 text-sm text-gray-600">Hızlı email gönderimi için bu formu kullanın</p>
    </div>

    <form action="{{ route('emails.send') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Alıcı Bilgileri</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alıcı Email *</label>
                    <input type="email"
                           name="to_email"
                           value="{{ old('to_email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="alici@mail.com"
                           required>
                    @error('to_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alıcı Adı</label>
                    <input type="text"
                           name="to_name"
                           value="{{ old('to_name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ahmet Yılmaz">
                    @error('to_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <h2 class="text-lg font-semibold text-gray-900 mb-4 pt-4 border-t border-gray-200">Gönderen Bilgileri</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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
                           value="{{ old('from_name', config('mail.from.name')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Şirket Adı"
                           required>
                    @error('from_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <h2 class="text-lg font-semibold text-gray-900 mb-4 pt-4 border-t border-gray-200">Email İçeriği</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konu *</label>
                    <input type="text"
                           name="subject"
                           value="{{ old('subject') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Email konusu"
                           required>
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mesaj *</label>
                    <textarea name="body"
                              rows="12"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Email içeriğinizi buraya yazın..."
                              required>{{ old('body') }}</textarea>
                    @error('body')
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
                <i class="fas fa-paper-plane mr-2"></i>Email Gönder
            </button>
        </div>
    </form>
</div>
@endsection
