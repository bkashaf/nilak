@extends('installer.layout')

@section('title', 'Installer | Summary')
@section('step', __('installer.wizard.summary') . ' 5/5')
@section('step_slug', 'summary')

@section('content')
    <h1 class="h3 mb-3">{{ __('installer.summary.title') }}</h1>

    @if(($result['ok'] ?? false))
        <div class="alert alert-success">{{ $result['message'] ?? __('installer.summary.success') }}</div>
    @else
        <div class="alert alert-danger">{{ $result['message'] ?? __('installer.summary.failed') }}</div>
    @endif

    <div class="help-box mb-4">
        <div class="fw-semibold mb-2">{{ __('installer.summary.env_preview') }}</div>
        <pre class="mb-0 small" style="white-space: pre-wrap;">{{ implode("\n", $envPreview) }}</pre>
    </div>

    <div class="card border-0 bg-light mb-4">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ __('installer.summary.store_summary') }}</h2>
            <div class="row g-2 small">
                <div class="col-md-6"><strong>{{ __('installer.summary.store_name') }}:</strong> {{ $store['store_name'] ?? '-' }}</div>
                <div class="col-md-3"><strong>{{ __('installer.summary.locale') }}:</strong> {{ $store['default_locale'] ?? '-' }}</div>
                <div class="col-md-3"><strong>{{ __('installer.summary.timezone') }}:</strong> {{ $store['timezone'] ?? '-' }}</div>
                <div class="col-md-6"><strong>{{ __('installer.summary.currency') }}:</strong> {{ $store['currency_label'] ?? '-' }}</div>
                <div class="col-md-6"><strong>{{ __('installer.summary.logo') }}:</strong> {{ !empty($store['store_logo_path']) ? $store['store_logo_path'] : __('installer.summary.logo_not_set') }}</div>
            </div>
        </div>
    </div>

    <div class="alert alert-warning">
        {{ __('installer.summary.warning') }}
    </div>

    <form method="POST" action="{{ route('install.run') }}" class="card border-0 bg-light mb-4">
        @csrf
        <div class="card-body">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="confirm_apply" name="confirm_apply" required>
                <label class="form-check-label" for="confirm_apply">
                    {{ __('installer.summary.confirm') }}
                </label>
            </div>

            <button class="btn btn-success">{{ __('installer.summary.btn_run') }}</button>
        </div>
    </form>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('install.store-settings') }}" class="btn btn-outline-secondary">{{ __('installer.summary.btn_back_store') }}</a>
        <a href="{{ route('install.database') }}" class="btn btn-outline-secondary">{{ __('installer.summary.btn_back_db') }}</a>
    </div>
@endsection
