<section class="nl-block nl-contact-form">

    {{-- ✅ نمایش پیام موفقیت --}}
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    {{-- ⚠️ نمایش خطاهای اعتبارسنجی --}}
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('contact.submit') }}" method="POST">
        @csrf

        <div class="row g-3">

            <div class="col-md-6">
                <input type="text" name="name" class="form-control" placeholder="نام شما">
            </div>

            <div class="col-md-6">
                <input type="email" name="email" class="form-control" placeholder="ایمیل شما">
            </div>

            <div class="col-12">
                <textarea name="message" class="form-control" rows="5" placeholder="پیام شما" required></textarea>
            </div>

            <div class="col-12 text-end">
                <button class="btn btn-primary">ارسال پیام</button>
            </div>

        </div>
    </form>
</section>
