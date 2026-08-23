@extends('themes.admin.layouts.master')

@php
    $users = $users ?? collect();
@endphp

@section('title', 'مدیریت کاربران')

@section('content')
    <h1>مدیریت کاربران</h1>

    <table border="1" cellpadding="8" cellspacing="0" style="width:100%; text-align:right;">
        <thead>
            <tr>
                <th>شناسه</th>
                <th>نام</th>
                <th>ایمیل</th>
                <th>تاریخ ایجاد</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;">هیچ کاربری یافت نشد</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
