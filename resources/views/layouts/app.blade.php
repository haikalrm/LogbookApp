<!doctype html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
      data-theme="theme-default"
      data-assets-path="{{ asset('/assets') }}/"
      data-template="vertical-menu-template"
      data-style="light">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

  <title>{{ config('app.name', 'Logbook') }} - @yield('title', 'Dashboard')</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/img/branding/logo-small.png') }}">

  {{-- Fonts & CSS --}}
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"/>
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/remixicon/remixicon.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-bordered.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/swiper/swiper.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/typeahead.css') }}">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

  {{-- Helpers & Config --}}
  <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
  <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
  <script src="{{ asset('assets/js/config.js') }}"></script>

  @stack('styles')
</head>
<body>
<div class="layout-wrapper layout-content-navbar">
  <div class="layout-container">

    {{-- Sidebar --}}
    @include('layouts.header')

    <div class="layout-page">

      {{-- Navbar --}}
      @include('layouts.navbar')

      <div class="content-wrapper">
        {{-- Content --}}
        <div class="container-xxl flex-grow-1 container-p-y">
          @yield('content')
        </div>

        {{-- Footer --}}
        @include('layouts.footer')
      </div>
    </div>
  </div>
</div>

{{-- Scripts --}}
<!-- Core JS -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('assets/js/main.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<script>
    $(document).ready(function () {
        const notyf = new Notyf({
            duration: 5000,
            position: { x: 'right', y: 'bottom' },
        });

        // Jika ada pesan sukses tunggal
        @if (session('successMessage'))
            notyf.success("{{ session('successMessage') }}");
        @endif

        // Jika ada pesan error tunggal
        @if (session('errorMessage'))
            notyf.error("{{ session('errorMessage') }}");
        @endif

        // Jika ada beberapa pesan sukses (multiple success messages)
        @if (session('successMessages'))
            @foreach(session('successMessages') as $message)
                notyf.success("{{ $message }}");
            @endforeach
        @endif

        // Jika ada beberapa pesan error (multiple error messages)
        @if (session('errorMessages'))
            @foreach(session('errorMessages') as $message)
                notyf.error("{{ $message }}");
            @endforeach
        @endif
    });
</script>

@stack('scripts')
</body>
</html>
