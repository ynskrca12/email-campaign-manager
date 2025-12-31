@extends('layouts.app')

@section('title', 'Email Şablonları')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Email Şablonları</h1>
        <p class="mt-1 text-sm text-gray-600">Hazır email şablonlarınızı yönetin</p>
    </div>
    <a href="{{ route('templates.create') }}"
       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
        <i class="fas fa-plus mr-2"></i>
        Yeni Şablon
    </a>
</div>

@if($templates->isEmpty())
    <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
        <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Henüz şablon yok</h3>
        <p class="text-gray-600 mb-6">Sık kullandığınız email formatlarını şablon olarak kaydedin</p>
        <a href="{{ route('templates.create') }}"
           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>
            Şablon Oluştur
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($templates as $template)
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition">
            <div class="p-6">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $template->name }}</h3>
                    @if($template->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Aktif</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">Pasif</span>
                    @endif
                </div>

                <p class="text-sm text-gray-600 mb-2">{{ $template->subject }}</p>
                <p class="text-sm text-gray-500 line-clamp-3">{{ Str::limit($template->body, 120) }}</p>

                @if($template->available_variables)
                    <div class="mt-4 flex flex-wrap gap-1">
                        @foreach($template->available_variables as $var)
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded">{{ $var }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-gray-50 px-6 py-3 flex justify-between items-center border-t border-gray-200">
                <span class="text-xs text-gray-500">{{ $template->created_at->format('d.m.Y') }}</span>
                <div class="flex space-x-2">
                    <a href="{{ route('templates.edit', $template) }}"
                       class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('templates.destroy', $template) }}"
                          method="POST"
                          class="inline"
                          onsubmit="return confirm('Bu şablonu silmek istediğinizden emin misiniz?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $templates->links() }}
    </div>
@endif
@endsection
