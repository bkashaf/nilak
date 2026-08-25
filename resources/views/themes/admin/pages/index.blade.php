@extends('themes.admin.layouts.master')

@section('title', 'مدیریت صفحات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1">مدیریت صفحات</h1>
            <p class="text-muted mb-0">ایجاد صفحات محتوایی و نمایش آن ها در منوی سایت</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">ایجاد صفحه جدید</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>عنوان</th>
                        <th>Slug</th>
                        <th>وضعیت</th>
                        <th>نمایش در منو</th>
                        <th>ترتیب</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                        <tr>
                            <td>{{ $page->title }}</td>
                            <td dir="ltr">{{ $page->slug }}</td>
                            <td>
                                <span class="badge {{ $page->is_published ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $page->is_published ? 'منتشر شده' : 'پیش نویس' }}
                                </span>
                            </td>
                            <td>{{ $page->show_in_menu ? 'بله' : 'خیر' }}</td>
                            <td>{{ $page->menu_order }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-outline-primary">ویرایش</a>
                                    <form method="POST" action="{{ route('admin.pages.destroy', $page) }}" onsubmit="return confirm('حذف صفحه انجام شود؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">صفحه ای ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $pages->links() }}</div>
    </div>
@endsection
