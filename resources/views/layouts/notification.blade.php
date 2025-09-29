<a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
   href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
  <i class="ri-notification-2-line ri-22px"></i>
  @if(!empty($unreadCount) && $unreadCount > 0)
    <span class="badge bg-danger">{{ $unreadCount }}</span>
  @endif
</a>

<ul class="dropdown-menu dropdown-menu-end py-0">
  <li class="dropdown-menu-header border-bottom py-50">
    <div class="dropdown-header d-flex align-items-center py-2">
      <h6 class="mb-0 me-auto">Notification</h6>
      @if($unreadCount > 0)
        <div class="d-flex align-items-center">
          <span class="badge rounded-pill bg-label-primary fs-xsmall me-2">{{ $unreadCount }} New</span>
          <a href="{{ url('/notifications/mark-all') }}" class="btn btn-text-secondary rounded-pill btn-icon"
             title="Mark all as read">
            <i class="ri-mail-open-line text-heading ri-20px"></i>
          </a>
        </div>
      @endif
    </div>
  </li>

  <li class="dropdown-notifications-list scrollable-container">
    <ul class="list-group list-group-flush">
      @forelse ($notifications as $row)
        <li class="list-group-item list-group-item-action dropdown-notifications-item">
          <div class="d-flex">
            <div class="flex-shrink-0 me-3">
              <div class="avatar">
                <img src="{{ asset('assets/img/profile/'.($row->profile ?? 'default.png')) }}"
                     alt="profile"
                     class="rounded-circle" width="36" height="36"/>
              </div>
            </div>
            <div class="flex-grow-1">
              <h6 class="small mb-1">{{ $row->title }}</h6>
              <small class="mb-1 d-block text-body">{{ $row->body }}</small>
              {{-- Laravel lebih gampang pakai diffForHumans() --}}
              <small class="text-muted">{{ $row->date->diffForHumans() }}</small>
            </div>

            @if(optional($row->pivot)->status == 0)
              <div class="flex-shrink-0">
                <a href="{{ url('/notification/'.$row->id) }}">
                  <span class="badge badge-dot"></span>
                </a>
              </div>
            @endif
          </div>
        </li>
      @empty
        <li class="list-group-item text-center text-muted">No notifications available</li>
      @endforelse
    </ul>
  </li>

  <li class="border-top">
    <div class="d-grid p-4">
      <a class="btn btn-primary btn-sm d-flex" href="{{ url('/account/notifications') }}">
        <small class="align-middle">View all notifications</small>
      </a>
    </div>
  </li>
</ul>
