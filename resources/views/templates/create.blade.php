@extends('layouts.app')

@section('title', 'Yeni Email Şablonu')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Yeni Email Şablonu</h1>
        <p class="mt-1 text-sm text-gray-600">Tekrar kullanabileceğiniz email şablonu oluşturun</p>
    </div>

    <form action="{{ route('templates.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Şablon Adı *</label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Örn: Hoşgeldin Emaili"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Konusu *</label>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email İçeriği *</label>
                    <div class="text-xs text-gray-500 mb-2">
                        Kullanılabilir değişkenler: {{name}}, {{email}}, {{company}}
                    </div>
                    <textarea name="body"
                              rows="12"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                              placeholder="Şablon içeriğiniz..."
                              required>{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ variables: ['name', 'email'] }">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kullanılabilir Değişkenler</label>
                    <div class="flex flex-wrap gap-2 mb-2">
                        <template x-for="(variable, index) in variables" :key="index">
                            <div class="flex items-center bg-blue-50 px-3 py-1 rounded-lg">
                                <span class="text-sm text-blue-700" x-text="'{{' + variable + '}}'"></span>
                                <button type="button"
                                        @click="variables.splice(index, 1)"
                                        class="ml-2 text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                <input type="hidden" name="available_variables[]" :value="variable">
                            </div>
                        </template>
                    </div>
                    <div class="flex gap-2">
                        <input type="text"
                               x-ref="newVariable"
                               placeholder="Yeni değişken ekle"
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               @keydown.enter.prevent="if($refs.newVariable.value) { variables.push($refs.newVariable.value); $refs.newVariable.value = ''; }">
                        <button type="button"
                                @click="if($refs.newVariable.value) { variables.push($refs.newVariable.value); $refs.newVariable.value = ''; }"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('templates.index') }}"
               class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                İptal
            </a>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                Şablonu Kaydet
            </button>
        </div>
    </form>
</div>
@endsection
