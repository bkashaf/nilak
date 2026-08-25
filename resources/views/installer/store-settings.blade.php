@extends('installer.layout')

@section('title', 'Installer | Store Settings')
@section('step', __('installer.wizard.store_settings') . ' 4/5')
@section('step_slug', 'store-settings')

@section('content')
    <h1 class="h3 mb-3">{{ __('installer.store.title') }}</h1>
    <p class="text-muted mb-4">{{ __('installer.store.desc') }}</p>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('install.store-settings.save') }}" class="row g-3" enctype="multipart/form-data">
        @csrf

        <div class="col-md-6">
            <label class="form-label">{{ __('installer.store.store_name') }}</label>
            <input name="store_name" class="form-control" value="{{ old('store_name', $defaults['store_name']) }}" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">{{ __('installer.store.default_locale') }}</label>
            <select name="default_locale" class="form-select" required>
                <option value="fa" @selected(old('default_locale', $defaults['default_locale']) === 'fa')>{{ __('installer.locale.fa') }}</option>
                <option value="en" @selected(old('default_locale', $defaults['default_locale']) === 'en')>{{ __('installer.locale.en') }}</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">{{ __('installer.store.timezone') }}</label>
            <input name="timezone" class="form-control" value="{{ old('timezone', $defaults['timezone']) }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">{{ __('installer.store.currency_label') }}</label>
            <input name="currency_label" class="form-control" value="{{ old('currency_label', $defaults['currency_label']) }}" required>
        </div>

        <div class="col-md-8">
            <label class="form-label">{{ __('installer.store.logo') }}</label>
            <input type="file" name="store_logo" class="form-control" accept="image/*">
            <div class="form-text">{{ __('installer.store.logo_help') }}</div>
        </div>

        @if(!empty($defaults['store_logo_path']))
            <div class="col-12">
                <div class="p-2 border rounded bg-light d-inline-flex align-items-center gap-2">
                    <span class="small text-muted">{{ __('installer.store.current_logo') }}</span>
                    <img src="{{ asset($defaults['store_logo_path']) }}" alt="logo" style="height:44px; width:auto;">
                </div>
            </div>
        @endif

        <div class="col-12 d-flex gap-2 flex-wrap">
            <a href="{{ route('install.database') }}" class="btn btn-outline-secondary">{{ __('installer.common.back') }}</a>
            <button class="btn btn-primary">{{ __('installer.store.btn_submit') }}</button>
        </div>
    </form>
@endsection
