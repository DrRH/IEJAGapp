@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-xl py-12">
    @if(session('error'))
        <div class="bg-red-100 text-red-800 rounded p-3 mb-4">
            {{ session('error') }}
        </div>
    @endif
    <div class="bg-white rounded-xl shadow p-8 text-center">
        <h1 class="text-2xl font-bold mb-6">Iniciar sesión</h1>
        <a href="{{ route('google.redirect') }}"
           class="inline-block px-5 py-3 rounded-lg shadow bg-blue-600 text-white font-medium">
            Entrar con Google
        </a>
    </div>
</div>
@endsection
