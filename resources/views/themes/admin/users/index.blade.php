@extends('themes.admin.layouts.master')

@section('title', 'مدیریت کاربران')

@section('content')
    <h1>مدیریت کاربران</h1>

    @if (session('success'))
        <div style="color:green; margin-bottom:10px;">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div style="color:#b00020; margin-bottom:10px;">{{ session('error') }}</div>
    @endif

    <div style="margin-bottom:12px;">
        <a href="{{ route('admin.users.create') }}">ایجاد کاربر جدید</a>
    </div>

    <table border="1" cellpadding="8" cellspacing="0" style="width:100%; text-align:right;">
        <thead>
            <tr>
                <th>شناسه</th>
                <th>نام</th>
                <th>ایمیل</th>
                <th>تاریخ ایجاد</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" style="margin-left:8px;">ویرایش</a>

                        @php
                            $canDelete = auth()->check()
                                && auth()->id() !== $user->id
                                && ! (method_exists($user, 'hasRole') && $user->hasRole('admin'));
                        @endphp

                        @if ($canDelete)
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('آیا از حذف این کاربر مطمئن هستید؟')">حذف</button>
                            </form>
                        @else
                            <span style="color:#666; margin-left:8px;">حذف غیرمجاز</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">هیچ کاربری یافت نشد</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:12px; text-align:center;">
        {{ $users->links() }}
    </div>
@endsection
