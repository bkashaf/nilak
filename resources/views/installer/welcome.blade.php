@extends('installer.layout')

@section('title', 'Installer | Welcome')
@section('step', __('installer.wizard.welcome') . ' 1/5')
@section('step_slug', 'welcome')

@section('content')
    <h1 class="h3 mb-3">{{ __('installer.welcome.title') }}</h1>
    <p class="text-muted mb-4">{{ __('installer.welcome.desc') }}</p>

    <div class="help-box mb-4">
        <div class="fw-semibold mb-2">{{ __('installer.welcome.before_title') }}:</div>
        <ul class="mb-0">
            <li>{{ __('installer.welcome.before_1') }}</li>
            <li>{{ __('installer.welcome.before_2') }}</li>
            <li>{{ __('installer.welcome.before_3') }}</li>
        </ul>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('install.requirements') }}" class="btn btn-primary">{{ __('installer.welcome.btn_requirements') }}</a>
        @if(!empty($resumeUrl) && $resumeUrl !== route('install.requirements'))
            <a href="{{ route('install.resume') }}" class="btn btn-outline-primary">{{ __('installer.welcome.btn_resume') }}</a>
        @endif
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">{{ __('installer.welcome.btn_home') }}</a>
    </div>
@endsection
