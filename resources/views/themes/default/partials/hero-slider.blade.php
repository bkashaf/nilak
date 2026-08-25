@php
    $defaultImage = asset('themes/default/images/no-image.svg');
@endphp

@if(($slides ?? collect())->count())
    <div id="{{ $sliderId ?? 'heroSlider' }}" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="carousel-inner rounded overflow-hidden">
            @foreach($slides as $slide)
                @php
                    $desktopImage = $slide->image_path ? asset($slide->image_path) : $defaultImage;
                    $mobileImage = $slide->mobile_image_path ? asset($slide->mobile_image_path) : $desktopImage;
                    $desktopPos = ($slide->focal_x ?? 50) . '% ' . ($slide->focal_y ?? 50) . '%';
                    $mobilePos = ($slide->mobile_focal_x ?? 50) . '% ' . ($slide->mobile_focal_y ?? 50) . '%';
                @endphp
                <div class="carousel-item {{ $loop->first ? 'active' : '' }} position-relative">
                    <picture>
                        <source media="(max-width: 767.98px)" srcset="{{ $mobileImage }}">
                        <img
                            src="{{ $desktopImage }}"
                            alt="{{ $slide->title ?? 'slider' }}"
                            class="w-100 slider-image"
                            style="height:420px; object-fit:cover; object-position: {{ $desktopPos }};"
                            data-mobile-pos="{{ $mobilePos }}"
                        >
                    </picture>

                    <div class="position-absolute top-50 start-50 translate-middle text-center text-white px-3" style="max-width: 760px;">
                        <h2 class="fw-bold display-6">{{ $slide->title ?? '' }}</h2>
                        @if($slide->subtitle)
                            <p class="fs-5 mb-2">{{ $slide->subtitle }}</p>
                        @endif
                        @if($slide->description)
                            <p class="mb-3">{{ $slide->description }}</p>
                        @endif
                        @if($slide->link_url)
                            <a href="{{ $slide->link_url }}" class="btn btn-light btn-lg mt-2">{{ $slide->link_text ?? __('messages.shop') }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($slides->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#{{ $sliderId ?? 'heroSlider' }}" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#{{ $sliderId ?? 'heroSlider' }}" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        @endif
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const images = document.querySelectorAll('.slider-image[data-mobile-pos]');
        const isMobile = window.matchMedia('(max-width: 767.98px)').matches;
        if (!isMobile) return;

        images.forEach(function (img) {
            img.style.objectPosition = img.getAttribute('data-mobile-pos');
        });
    });
    </script>
@endif
