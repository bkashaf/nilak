@extends('installer.layout')

@section('title', 'Installer | Database')
@section('step', __('installer.wizard.database') . ' 3/5')
@section('step_slug', 'database')

@section('content')
    <h1 class="h3 mb-3">{{ __('installer.database.title') }}</h1>
    <p class="text-muted mb-4">{{ __('installer.database.desc') }}</p>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('install.database.test') }}" class="row g-3">
        @csrf

        <div class="col-md-4">
            <label class="form-label">{{ __('installer.database.db_host') }}</label>
            <input name="db_host" class="form-control" value="{{ old('db_host', $defaults['db_host']) }}" required>
        </div>

        <div class="col-md-2">
            <label class="form-label">{{ __('installer.database.db_port') }}</label>
            <input name="db_port" class="form-control" value="{{ old('db_port', $defaults['db_port']) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('installer.database.db_name') }}</label>
            <input name="db_database" class="form-control" value="{{ old('db_database', $defaults['db_database']) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('installer.database.db_user') }}</label>
            <input name="db_username" class="form-control" value="{{ old('db_username', $defaults['db_username']) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('installer.database.db_pass') }}</label>
            <input type="password" name="db_password" class="form-control" value="{{ old('db_password', $defaults['db_password']) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('installer.database.app_url') }}</label>
            <input name="app_url" class="form-control" value="{{ old('app_url', $defaults['app_url']) }}" required>
        </div>

        <div class="col-12"><hr></div>

        <div class="col-md-4">
            <label class="form-label">{{ __('installer.database.admin_name') }}</label>
            <input name="admin_name" class="form-control" value="{{ old('admin_name', $defaults['admin_name']) }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">{{ __('installer.database.admin_email') }}</label>
            <input name="admin_email" type="email" class="form-control" value="{{ old('admin_email', $defaults['admin_email']) }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">{{ __('installer.database.admin_mobile') }}</label>
            <input name="admin_mobile" class="form-control" value="{{ old('admin_mobile') }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('installer.database.admin_password') }}</label>
            <input name="admin_password" type="password" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ __('installer.database.admin_password_confirm') }}</label>
            <input name="admin_password_confirmation" type="password" class="form-control" required>
        </div>

        <div class="col-12 help-box">
            {{ __('installer.database.help') }}
        </div>

        <div class="col-12 d-flex gap-2 flex-wrap">
            <a href="{{ route('install.requirements') }}" class="btn btn-outline-secondary">{{ __('installer.common.back') }}</a>
            <button class="btn btn-primary">{{ __('installer.database.btn_submit') }}</button>
        </div>
    </form>
@endsection
