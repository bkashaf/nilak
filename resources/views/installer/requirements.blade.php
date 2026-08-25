@extends('installer.layout')

@section('title', 'Installer | Requirements')
@section('step', __('installer.wizard.requirements') . ' 2/5')
@section('step_slug', 'requirements')

@section('content')
    <h1 class="h3 mb-3">{{ __('installer.requirements.title') }}</h1>
    <p class="text-muted mb-4">{{ __('installer.requirements.desc') }}</p>

    <div class="table-responsive mb-4">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>{{ __('installer.requirements.col_item') }}</th>
                    <th>{{ __('installer.requirements.col_status') }}</th>
                    <th>{{ __('installer.requirements.col_details') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($checks as $check)
                    <tr>
                        <td>{{ $check['label'] }}</td>
                        <td>
                            @php
                                $isWarning = ($check['level'] ?? 'required') === 'warning';
                                $badgeClass = $check['status']
                                    ? 'text-bg-success'
                                    : ($isWarning ? 'text-bg-warning' : 'text-bg-danger');
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ $check['status'] ? __('installer.common.ok') : ($isWarning ? __('installer.common.warn') : __('installer.common.fail')) }}
                            </span>
                        </td>
                        <td class="small text-muted">{{ $check['detail'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(!$allOk)
        <div class="alert alert-danger mb-4">{{ __('installer.requirements.fail_alert') }}</div>
    @elseif(($warningCount ?? 0) > 0)
        <div class="alert alert-warning mb-4">{{ __('installer.requirements.warn_alert', ['count' => $warningCount]) }}</div>
    @endif

    <div class="help-box mb-4">
        <div class="fw-semibold mb-2">{{ __('installer.requirements.help_title') }}</div>
        <ul class="mb-0">
            <li>{{ __('installer.requirements.help_1') }}</li>
            <li>{{ __('installer.requirements.help_2') }}</li>
            <li>{{ __('installer.requirements.help_3') }}</li>
            <li>{{ __('installer.requirements.help_4') }}</li>
        </ul>
    </div>

    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('install.welcome') }}" class="btn btn-outline-secondary">{{ __('installer.common.back') }}</a>
        <a href="{{ route('install.database') }}" class="btn btn-primary {{ $allOk ? '' : 'disabled' }}">{{ __('installer.requirements.btn_next') }}</a>
    </div>
@endsection
