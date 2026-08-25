@extends('installer.layout')

@section('title', 'Installer | Completed')
@section('step', __('installer.completed.done_step'))

@section('content')
    @if(!empty($hasError))
        <h1 class="h3 mb-3 text-danger">{{ __('installer.completed.error_title') }}</h1>
        <p class="text-muted mb-4">{{ __('installer.completed.error_desc') }}</p>
    @else
        <h1 class="h3 mb-3 text-success">{{ __('installer.completed.ok_title') }}</h1>
        <p class="text-muted mb-4">{{ __('installer.completed.ok_desc') }}</p>
    @endif

    <div class="card border-0 bg-light mb-4">
        <div class="card-body">
            <h2 class="h6 mb-3">{{ __('installer.completed.report_title') }}</h2>
            <ul class="list-group list-group-flush">
                @foreach($report as $item)
                    <li class="list-group-item px-0 bg-transparent">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>{{ $item['step'] }}</strong>
                            <span class="badge {{ !empty($item['ok']) ? 'text-bg-success' : 'text-bg-danger' }}">{{ !empty($item['ok']) ? __('installer.common.ok') : __('installer.common.fail') }}</span>
                        </div>
                        <div class="small text-muted">{{ $item['message'] ?? '' }}</div>
                        @if(!empty($item['output']))
                            <pre class="small mt-2 mb-0" style="white-space: pre-wrap;">{{ $item['output'] }}</pre>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('home') }}" class="btn btn-primary">{{ __('installer.completed.btn_home') }}</a>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">{{ __('installer.completed.btn_admin') }}</a>
    </div>
@endsection
