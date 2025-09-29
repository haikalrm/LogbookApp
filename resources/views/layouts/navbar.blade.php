<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
     id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="ri-menu-fill ri-22px"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    {{-- Search --}}
    <div class="navbar-nav align-items-center">
      <div class="nav-item navbar-search-wrapper mb-0">
        <a class="nav-item nav-link search-toggler fw-normal px-0" href="javascript:void(0);">
          <i class="ri-search-line ri-22px scaleX-n1-rtl me-3"></i>
          <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
        </a>
      </div>
    </div>
	<ul class="navbar-nav flex-row align-items-center ms-auto">
	<!-- Style Switcher -->
	<li class="nav-item dropdown-style-switcher dropdown me-1 me-xl-0">
	  <a
		class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
		href="javascript:void(0);"
		data-bs-toggle="dropdown">
		<i class="ri-22px"></i>
	  </a>
	  <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
		<li>
		  <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
			<span class="align-middle"><i class="ri-sun-line ri-22px me-3"></i>Light</span>
		  </a>
		</li>
		<li>
		  <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
			<span class="align-middle"><i class="ri-moon-clear-line ri-22px me-3"></i>Dark</span>
		  </a>
		</li>
		<li>
		  <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
			<span class="align-middle"><i class="ri-computer-line ri-22px me-3"></i>System</span>
		  </a>
		</li>
	  </ul>
	</li>
    <ul class="navbar-nav flex-row align-items-center ms-auto">
      {{-- Notification --}}
      <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-4 me-xl-1">
        @include('layouts.notification')
      </li>

      {{-- User Dropdown --}}
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <img src="{{ asset('assets/img/profile/' . Auth::user()->profile_picture) }}"
                 alt="profile" class="rounded-circle"/>
          </div>
        </a>
        @include('layouts.menu-dropdown')
      </li>
    </ul>
  </div>
  <div class="navbar-search-wrapper search-input-wrapper d-none">
  <input
	type="text"
	class="form-control search-input container-xxl border-0"
	placeholder="Search..."
	aria-label="Search..." />
  <i class="ri-close-fill search-toggler cursor-pointer"></i>
</div>
</nav>
