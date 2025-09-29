@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card mb-6">
                <div class="user-profile-header-banner">
                    {{-- Use the asset() helper for static image paths --}}
                    <img src="{{ asset('assets/img/pages/default-banner.png') }}" alt="Banner image" class="rounded-top" style="width: 100%; height: 250px;">
                </div>
                <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-5">
                    <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        {{-- Display data from the $user object passed by the controller --}}
                        <img src="{{ asset('assets/img/profile/' . $user->profile_picture) }}" alt="user image" class="d-block h-auto ms-0 ms-sm-5 rounded-4 user-profile-img" width="120" height="125">
                    </div>
                    <div class="flex-grow-1 mt-4 mt-sm-12">
                        <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-5 flex-md-row flex-column gap-6">
                            <div class="user-profile-info">
                                <h4 class="mb-2">{{ $user->name }}</h4>
                                <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-4">
                                    <li class="list-inline-item">
                                        <i class="ri-user-2-line me-2 ri-24px"></i>
                                        <span class="fw-medium">{{ $user->position }}</span>
                                    </li>
                                    <li class="list-inline-item">
                                        <i class="ri-map-pin-line me-2 ri-24px"></i>
                                        <span class="fw-medium">{{ $user->city }}</span>
                                    </li>
                                    <li class="list-inline-item">
                                        <i class="ri-calendar-line me-2 ri-24px"></i>
                                        {{-- Use Carbon, Laravel's built-in date library, for easy formatting --}}
                                        <span class="fw-medium">Joined {{ $user->joined->format('F Y') }}</span>
                                    </li>
                                </ul>
                            </div>
                            <a href="javascript:void(0)" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#modal_barcode">
                                <i class="ri-barcode-fill ri-16px me-2"></i>Show QR Code
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-sm-row mb-6 row-gap-2">
                    {{-- Use the route() helper to generate dynamic and maintainable URLs --}}
                    <li class="nav-item"><a class="nav-link waves-effect waves-light" href="{{ route('profile.show', $user->name) }}"><i class="ri-user-3-line me-2"></i>Profile</a></li>
                    <li class="nav-item"><a class="nav-link active waves-effect waves-light" href="javascript:void(0);"><i class="ri-notification-line me-2"></i>Notifications</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Notifications</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table id="notifications_table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>MESSAGE</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Use @forelse to loop through data and handle empty cases --}}
                        @forelse ($notifications as $notification)
                        <tr>
                            <td>
                                <li class="list-group-item list-group-item-action dropdown-notifications-item waves-effect waves-light" data-id="{{ $notification->id }}">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar">
                                                {{-- Access the author's profile picture via the relationship for consistency --}}
                                                <img src="{{ asset('assets/img/profile/' . $notification->author->profile_picture) }}" alt="" class="rounded-circle">
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="small mb-1">{{ $notification->title }}</h6>
                                            <small class="mb-1 d-block text-body">{{ $notification->body }}</small>
                                            {{-- Use Carbon's diffForHumans() for user-friendly relative time --}}
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </li>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center">No notifications found for this user.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_barcode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalCenterTitle">QR Code</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ route('profile.qr', $user->name) }}" alt="QR Code" style="width: 80%; height: auto;" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
	var dt_user_table = $('#logbook_list').DataTable({
		responsive: true,
		language: {
			emptyTable: 'Notifications of this user will appear here',
			sLengthMenu: 'Show _MENU_',
			search: '',
			searchPlaceholder: 'Search Notifications'
		},
		columnDefs: [
			{ targets: 0, orderable: false, searchable: true } // pastikan searchable: true
		],
		dom:
			'<"row"' +
			'<"col-md-2 d-flex align-items-center justify-content-md-start justify-content-center"<"dt-action-buttons mt-5 mt-md-0"B>>' +
			'<"col-md-10"<"d-flex align-items-center justify-content-md-end justify-content-center"<"me-4"f><"add-new">>>' +
			'>t' +
			'<"row"' +
			'<"col-sm-12 col-md-6"i>' +
			'<"col-sm-12 col-md-6"p>' +
			'>',
		buttons: [
			{
				extend: 'collection',
				className: 'btn btn-outline-secondary dropdown-toggle waves-effect waves-light',
				text: '<span class="d-flex align-items-center"><i class="ri-upload-2-line ri-16px me-2"></i> <span class="d-none d-sm-inline-block">Export</span></span> ',
				buttons: [
					{
						extend: 'print',
						text: '<i class="ri-printer-line me-1"></i>Print',
						className: 'dropdown-item',
						exportOptions: {
							columns: ':visible'
						},
						customize: function (win) {
							$(win.document.body)
								.css('color', '#000')
								.css('border-color', '#aaa')
								.css('background-color', '#fff');
							$(win.document.body).find('table')
								.addClass('compact')
								.css('color', 'inherit')
								.css('border-color', 'inherit')
								.css('background-color', 'inherit');
						}
					},
					{
						extend: 'csv',
						text: '<i class="ri-file-text-line me-1"></i>Csv',
						className: 'dropdown-item',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: 'excel',
						text: '<i class="ri-file-excel-line me-1"></i>Excel',
						className: 'dropdown-item',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: 'pdf',
						text: '<i class="ri-file-pdf-line me-1"></i>Pdf',
						className: 'dropdown-item',
						exportOptions: {
							columns: ':visible'
						}
					},
					{
						extend: 'copy',
						text: '<i class="ri-file-copy-line me-1"></i>Copy',
						className: 'dropdown-item',
						exportOptions: {
							columns: ':visible'
						}
					}
				]
			}
		]
	});

	// Pindahkan elemen pencarian ke dalam div "search_list"
	$('#logbook_list_filter').appendTo('#search_list');
});
</script>
@endpush
