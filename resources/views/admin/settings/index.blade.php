@extends('layouts.admin')

@section('title', 'Global Settings')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Global Settings</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Settings</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        
        @foreach($settings as $group => $items)
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 text-primary uppercase tracking-wider h6">{{ ucfirst($group) }} Configuration</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($items as $setting)
                            <div class="col-md-6 mb-4">
                                <label for="setting_{{ $setting->key }}" class="form-label fw-bold">{{ $setting->label }}</label>
                                @if($setting->type === 'decimal' || $setting->type === 'integer')
                                    <input type="number" step="0.01" class="form-control" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" value="{{ $setting->value }}">
                                @elseif($setting->type === 'boolean')
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" value="1" {{ $setting->value ? 'checked' : '' }}>
                                    </div>
                                @else
                                    <input type="text" class="form-control" name="settings[{{ $setting->key }}]" id="setting_{{ $setting->key }}" value="{{ $setting->value }}">
                                @endif
                                <div class="form-text mt-1">{{ $setting->description }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-end mb-5">
            <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm">
                <i class="fas fa-save me-2"></i> Save Changes
            </button>
        </div>
    </form>
</div>
@endsection
