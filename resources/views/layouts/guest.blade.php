<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="theme-default"
      data-assets-path="{{ asset('/assets') }}/"
      data-template="vertical-menu-template"
      data-style="light">
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <meta name="csrf-token" content="{{ csrf_token() }}">
      <title>{{ config('app.name', 'Logbook') }}</title>

      <!-- Icon -->
      <link rel="icon" type="image/png" href="{{ asset('assets/img/branding/logo-small.png') }}">

      <!-- Fonts -->
      <link rel="preconnect" href="https://fonts.googleapis.com" />
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

      <!-- Vendor CSS -->
      <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/remixicon/remixicon.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/flag-icons.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/vendor/libs/node-waves/node-waves.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/vendor/libs/typeahead-js/typeahead.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/vendor/libs/toastr/toastr.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}">
      <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">

      <!-- Scripts dari vendor lama -->
      <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
      <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
      <script src="{{ asset('assets/js/config.js') }}"></script>

      <!-- HCAPTCHA -->
      <script src="https://js.hcaptcha.com/1/api.js" async defer></script>
  </head>
  <body>
      @yield('content')

      <!-- Vendor JS -->
      <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
      <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
      <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
      <script src="{{ asset('assets/vendor/libs/node-waves/node-waves.js') }}"></script>
      <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
      <script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
      <script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
      <script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
      <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
      <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
      <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
      <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
      <script src="{{ asset('assets/js/main.js') }}"></script>
      <script src="{{ asset('assets/js/pages-auth.js') }}"></script>

      <!-- Notyf -->
      <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
      <script src="{{ asset('assets/js/notyf.js') }}"></script>
	  <script>
		document.addEventListener("DOMContentLoaded", function () {
			var notyf = new Notyf({
				duration: 4000,
				position: { x: 'right', y: 'bottom' }
			});

			@if ($errors->any())
				@foreach ($errors->all() as $error)
					notyf.error("{{ $error }}");
				@endforeach
			@endif

			@if (session('status'))
				notyf.success("{{ session('status') }}");
			@endif
		});
	</script>
  </body>
</html>
