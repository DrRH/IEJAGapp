{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.tabler')

@section('title','Panel')

@section('content')
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Bienvenido</h3>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    Autenticado como <strong>{{ auth()->user()->name }}</strong>.
                </p>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-link-pop">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="avatar me-3">
                        <i class="ti ti-report-analytics"></i>
                    </span>
                    <div>
                        <div class="h3 mb-0">42</div>
                        <div class="text-secondary">Indicador 1</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="card card-link-pop">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="avatar me-3">
                        <i class="ti ti-users"></i>
                    </span>
                    <div>
                        <div class="h3 mb-0">128</div>
                        <div class="text-secondary">Usuarios</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
