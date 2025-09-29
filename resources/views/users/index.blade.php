@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
	<style>
        .signature-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
        }
        #signature-pad {
            border: 2px solid #000;
            background-color: #fff;
        }
    </style>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Manage Users</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table id="users_list" class="datatables-users table">
                    <thead>
                        <tr>
							<th></th>
                            <th class="control sorting_disabled" rowspan="1" colspan="1" style="width: 0px;"></th>
                            <th class="sorting sorting_desc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 30px;" aria-sort="descending">
                                ID
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 136px;">
                                Username
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 136px;">
                                Full Name
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 90px;">
                                Email
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 150px;">
                                Address
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 95px;">
                                Role
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 86px;">
                                Position
                            </th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 86px;">
                                Signature
                            </th>
                            <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 147px;">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td></td>
								<td></td>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->fullname }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->address }}</td>
                                <td>
                                    @if($user->access_level == 2)
                                        <span class="badge rounded-pill bg-label-success">Admin</span>
                                    @elseif($user->access_level == 1)
                                        <span class="badge rounded-pill bg-label-warning">Support</span>
                                    @else
                                        <span class="badge rounded-pill bg-label-secondary">User</span>
                                    @endif
                                </td>
                                <td>{{ $user->position }}</td>
                                <td>
                                    <img src="{{ asset('storage/signatures/' . $user->signature) }}" alt="Signature" style="width: 80%; height: 80%;" />
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-50">
                                        <a href="/profile/{{ $user->name }}" class="btn btn-sm btn-icon btn-text-secondary rounded-pill waves-effect">
                                            <i class="ri-eye-line ri-20px"></i>
                                        </a>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
												<a class="dropdown-item waves-effect edit-record" href="javascript:;" data-id="{{ $user->id }}">
                                                    <i class="ri-edit-box-line me-2"></i><span>Edit</span>
                                                </a>
                                                <a class="dropdown-item waves-effect delete-record" href="javascript:;" data-id="{{ $user->id }}">
                                                    <i class="ri-delete-bin-7-line me-2"></i><span>Delete</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal to add new user -->
	<div class="modal fade" id="addNewUser" tabindex="-1" aria-hidden="true">
	 <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
	  <div class="modal-content">
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		<div class="modal-body p-0">
		  <div class="text-center mb-6">
			<h4 class="address-title mb-2">Add New User</h4>
			<p class="address-subtitle">Add new user for new account</p>
		  </div>
		  <form id="addNewUserForm" class="row g-5">
			@csrf
			<div class="col-12">
			  <div class="row g-5">
				  <div class="col-md mb-md-0 mb-5">
					<div class="form-check custom-option custom-option-icon checked">
					  <label class="form-check-label custom-option-content" for="customRadioIcon1">
						<span class="custom-option-body">
						  <i class="ri-user-line"></i>
						  <span class="custom-option-title mb-2">User</span>
						  <small> This role can create and edit its own logbook. </small>
						</span>
						<input name="customRadioIcon-01" class="form-check-input" type="radio" value="0" id="customRadioIcon1" checked="">
					  </label>
					</div>
				  </div>
				  <div class="col-md mb-md-0 mb-5">
					<div class="form-check custom-option custom-option-icon">
					  <label class="form-check-label custom-option-content" for="customRadioIcon2">
						<span class="custom-option-body">
						  <i class="ri-customer-service-2-line"></i>
						  <span class="custom-option-title mb-2"> Support </span>
						  <small> This role can create, edit, and approve logbooks. </small>
						</span>
						<input name="customRadioIcon-01" class="form-check-input" type="radio" value="1" id="customRadioIcon2">
					  </label>
					</div>
				  </div>
				  <div class="col-md">
					<div class="form-check custom-option custom-option-icon">
					  <label class="form-check-label custom-option-content" for="customRadioIcon3">
						<span class="custom-option-body">
						  <i class="ri-admin-line"></i>
						  <span class="custom-option-title mb-2"> Admin </span>
						  <small> This role can manage logbooks, tools, users, etc. </small>
						</span>
						<input name="customRadioIcon-01" class="form-check-input" type="radio" value="2" id="customRadioIcon3">
					  </label>
					</div>
				  </div>
				</div>
			</div>
			<div class="col-12 col-md-6">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalAddressFirstName"
				  name="modalAddressFirstName"
				  class="form-control"
				  placeholder="John" required />
				<label for="modalAddressFirstName">First Name</label>
			  </div>
			</div>
			<div class="col-12 col-md-6">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalAddressLastName"
				  name="modalAddressLastName"
				  class="form-control"
				  placeholder="Doe" required />
				<label for="modalAddressLastName">Last Name</label>
			  </div>
			</div>
			<div class="col-12 col-md-6">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalUsername"
				  name="modalUsername"
				  class="form-control"
				  placeholder="LunarEcho" required />
				<label for="modalUsername">Username</label>
			  </div>
			</div>
			<div class="col-12 col-md-6">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalGelar"
				  name="modalGelar"
				  class="form-control"
				  placeholder="S.Kom." />
				<label for="modalGelar">Title</label>
			  </div>
			</div>
			<div class="col-12">
			  <div class="form-floating form-floating-outline">
				<select
				  id="modalAddressCountry"
				  name="modalAddressCountry"
				  class="select2 form-select"
				  data-allow-clear="true">
				  <option value="Australia">Australia</option>
				  <option value="Bangladesh">Bangladesh</option>
				  <option value="Belarus">Belarus</option>
				  <option value="Brazil">Brazil</option>
				  <option value="Canada">Canada</option>
				  <option value="China">China</option>
				  <option value="France">France</option>
				  <option value="Germany">Germany</option>
				  <option value="India">India</option>
				  <option value="Indonesia" selected>Indonesia</option>
				  <option value="Italy">Italy</option>
				  <option value="Japan">Japan</option>
				  <option value="Korea">Korea, Republic of</option>
				  <option value="Mexico">Mexico</option>
				  <option value="Philippines">Philippines</option>
				  <option value="Russia">Russian Federation</option>
				  <option value="South Africa">South Africa</option>
				  <option value="Thailand">Thailand</option>
				  <option value="Turkey">Turkey</option>
				  <option value="Ukraine">Ukraine</option>
				  <option value="United Arab Emirates">United Arab Emirates</option>
				  <option value="United Kingdom">United Kingdom</option>
				  <option value="United States">United States</option>
				</select>
				<label for="modalAddressCountry">Country</label>
			  </div>
			</div>
			<div class="col-12 col-md-6">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalAddressEmail"
				  name="modalAddressEmail"
				  class="form-control"
				  placeholder="user@haikal.engineer" required />
				<label for="modalAddressEmail">Email</label>
			  </div>
			</div>
			<div class="col-12 col-md-6">
			  <div class="form-floating form-floating-outline">
				<select class="form-select" id="inputGroupSelect01" name="inputGroupSelect01">
					@foreach ($positions as $position)
						<option value="{{ $position->no }}">{{ $position->name }}</option>
					@endforeach
				</select>
				<label class="input-group-text" for="inputGroupSelect01">Positions</label>
			  </div>
			</div>
			<div class="col-12">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalAddressAddress1"
				  name="modalAddressAddress1"
				  class="form-control"
				  placeholder="Merdeka Square" required />
				<label for="modalAddressAddress1">Address Line 1</label>
			  </div>
			</div>
			<div class="col-12">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalAddressAddress2"
				  name="modalAddressAddress2"
				  class="form-control"
				  placeholder="Monas Street Square" />
				<label for="modalAddressAddress2">Address Line 2</label>
			  </div>
			</div>
			<div class="col-12 col-md-6">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalPhoneNumber"
				  name="modalPhoneNumber"
				  class="form-control"
				  placeholder="+62" required />
				<label for="modalPhoneNumber">Phone Number</label>
			  </div>
			</div>
			<div class="col-12 col-md-6">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalAddressCity"
				  name="modalAddressCity"
				  class="form-control"
				  placeholder="Jakarta" required />
				<label for="modalAddressCity">City</label>
			  </div>
			</div>
			<div class="col-12 col-md-6">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalAddressState"
				  name="modalAddressState"
				  class="form-control"
				  placeholder="Central Jakarta City" required />
				<label for="modalAddressLandmark">State</label>
			  </div>
			</div>
			<div class="col-12 col-md-6">
			  <div class="form-floating form-floating-outline">
				<input
				  type="text"
				  id="modalAddressZipCode"
				  name="modalAddressZipCode"
				  class="form-control"
				  placeholder="10110" required />
				<label for="modalAddressZipCode">Zip Code</label>
			  </div>
			</div>
			<div class="col-12 mt-6">
			  <div class="form-check form-switch">
				<input type="checkbox" class="form-check-input" id="technician" name="technician" />
				<label for="technician">Is this user a technician?</label>
			  </div>
			</div>
			<div class="col-12">
				<small class="text-light fw-medium">Signature</small>
				<div class="signature-container">
					<canvas id="signature-pad"></canvas>
					<br>
					<button id="clear-button" class="btn btn-text-warning waves-effect waves-light">Reset</button>
				</div>
			</div>
			<input type="hidden" id="signature" name="signature">
			<div class="col-12 mt-6 d-flex flex-wrap justify-content-center gap-4 row-gap-4">
			  <button type="submit" class="btn btn-primary">Submit</button>
			  <button
				type="reset"
				class="btn btn-outline-secondary"
				data-bs-dismiss="modal"
				aria-label="Close">
				Cancel
			  </button>
			</div>
		  </form>
		</div>
	  </div>
	</div>
   </div>
   <!-- Modal to edit user -->
	<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-simple modal-add-new-address">
			<div class="modal-content">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				<div class="modal-body p-0">
					<div class="text-center mb-6">
						<h4 class="address-title mb-2">Edit User</h4>
						<p class="address-subtitle">Edit user details</p>
					</div>
					<form id="editUserForm" class="row g-5">
						@method('PUT')
						@csrf
						<div class="col-12">
							<div class="row g-5">
								<div class="col-md mb-md-0 mb-5">
									<div class="form-check custom-option custom-option-icon">
										<label class="form-check-label custom-option-content" for="editRadioIcon1">
											<span class="custom-option-body">
												<i class="ri-user-line"></i>
												<span class="custom-option-title mb-2">User</span>
												<small> This role can create and edit its own logbook. </small>
											</span>
											<input name="editRadioIcon-01" class="form-check-input" type="radio" value="0" id="editRadioIcon1">
										</label>
									</div>
								</div>
								<div class="col-md mb-md-0 mb-5">
									<div class="form-check custom-option custom-option-icon">
										<label class="form-check-label custom-option-content" for="editRadioIcon2">
											<span class="custom-option-body">
												<i class="ri-customer-service-2-line"></i>
												<span class="custom-option-title mb-2"> Support </span>
												<small> This role can create, edit, and approve logbooks. </small>
											</span>
											<input name="editRadioIcon-01" class="form-check-input" type="radio" value="1" id="editRadioIcon2">
										</label>
									</div>
								</div>
								<div class="col-md">
									<div class="form-check custom-option custom-option-icon">
										<label class="form-check-label custom-option-content" for="editRadioIcon3">
											<span class="custom-option-body">
												<i class="ri-admin-line"></i>
												<span class="custom-option-title mb-2"> Admin </span>
												<small> This role can manage logbooks, tools, users, etc. </small>
											</span>
											<input name="editRadioIcon-01" class="form-check-input" type="radio" value="2" id="editRadioIcon3">
										</label>
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" id="editUserId" name="user_id">
						<div class="col-12 col-md-6">
							<div class="form-floating form-floating-outline">
								<input type="text" id="editFirstName" name="modalAddressFirstName" class="form-control" placeholder="John" required />
								<label for="editFirstName">First Name</label>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-floating form-floating-outline">
								<input type="text" id="editLastName" name="modalAddressLastName" class="form-control" placeholder="Doe" required />
								<label for="editLastName">Last Name</label>
							</div>
						</div>
						<div class="col-12 col-md-6">
						  <div class="form-floating form-floating-outline">
							<input
							  type="text"
							  id="editUsername"
							  name="modalUsername"
							  class="form-control"
							  placeholder="LunarEcho" required />
							<label for="editUsername">Username</label>
						  </div>
						</div>
						<div class="col-12 col-md-6">
						  <div class="form-floating form-floating-outline">
							<input
							  type="text"
							  id="editGelar"
							  name="modalGelar"
							  class="form-control"
							  placeholder="S.Kom." />
							<label for="editGelar">Title</label>
						  </div>
						</div>
						<div class="col-12">
							<div class="form-floating form-floating-outline">
								<select id="editCountry" name="modalAddressCountry" class="select2 form-select" data-allow-clear="true">
									<option value="Australia">Australia</option>
									<option value="Bangladesh">Bangladesh</option>
									<option value="Belarus">Belarus</option>
									<option value="Brazil">Brazil</option>
									<option value="Canada">Canada</option>
									<option value="China">China</option>
									<option value="France">France</option>
									<option value="Germany">Germany</option>
									<option value="India">India</option>
									<option value="Indonesia" selected>Indonesia</option>
									<option value="Italy">Italy</option>
									<option value="Japan">Japan</option>
									<option value="Korea">Korea, Republic of</option>
									<option value="Mexico">Mexico</option>
									<option value="Philippines">Philippines</option>
									<option value="Russia">Russian Federation</option>
									<option value="South Africa">South Africa</option>
									<option value="Thailand">Thailand</option>
									<option value="Turkey">Turkey</option>
									<option value="Ukraine">Ukraine</option>
									<option value="United Arab Emirates">United Arab Emirates</option>
									<option value="United Kingdom">United Kingdom</option>
									<option value="United States">United States</option>
								</select>
								<label for="editCountry">Country</label>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-floating form-floating-outline">
								<input type="text" id="editEmail" name="modalAddressEmail" class="form-control" placeholder="user@haikal.engineer" required />
								<label for="editEmail">Email</label>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-floating form-floating-outline">
								<select class="form-select" id="editPosition" name="inputGroupSelect01">
									@foreach ($positions as $position)
										<option value="{{ $position->no }}">{{ $position->name }}</option>
									@endforeach
								</select>
								<label class="input-group-text" for="editPosition">Positions</label>
							</div>
						</div>
						<div class="col-12">
							<div class="form-floating form-floating-outline">
								<input type="text" id="editAddress1" name="modalAddressAddress1" class="form-control" placeholder="Merdeka Square" required />
								<label for="editAddress1">Address Line 1</label>
							</div>
						</div>
						<div class="col-12">
							<div class="form-floating form-floating-outline">
								<input type="text" id="editAddress2" name="modalAddressAddress2" class="form-control" placeholder="Monas Street Square" />
								<label for="editAddress2">Address Line 2</label>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-floating form-floating-outline">
								<input type="text" id="editPhoneNumber" name="modalPhoneNumber" class="form-control" placeholder="+62" required />
								<label for="editPhoneNumber">Phone Number</label>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-floating form-floating-outline">
								<input type="text" id="editCity" name="modalAddressCity" class="form-control" placeholder="Jakarta" required />
								<label for="editCity">City</label>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-floating form-floating-outline">
								<input type="text" id="editState" name="modalAddressState" class="form-control" placeholder="Central Jakarta City" required />
								<label for="editState">State</label>
							</div>
						</div>
						<div class="col-12 col-md-6">
							<div class="form-floating form-floating-outline">
								<input type="text" id="editZipCode" name="modalAddressZipCode" class="form-control" placeholder="10110" required />
								<label for="editZipCode">Zip Code</label>
							</div>
						</div>
						<div class="col-12 mt-6">
							<div class="form-check form-switch">
								<input type="checkbox" class="form-check-input" id="editTechnician" name="technician" />
								<label for="editTechnician">Is this user a technician?</label>
							</div>
						</div>
						<div class="col-12">
							<small class="text-light fw-medium">Signature</small>
							<div class="signature-container">
								<canvas id="edit-signature-pad"></canvas>
								<br>
								<button id="edit-clear-button" class="btn btn-text-warning waves-effect waves-light">Reset</button>
							</div>
						</div>
						<input type="hidden" id="editSignature" name="signature">
						<div class="col-12 mt-6 d-flex flex-wrap justify-content-center gap-4 row-gap-4">
							<button type="submit" class="btn btn-primary">Submit</button>
							<button type="reset" class="btn btn-outline-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
  
	
<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="../../assets/vendor/libs/jquery/jquery.js"></script>

<script>
// Get the canvas element and buttons
const canvas = document.getElementById("signature-pad");
const clearButton = document.getElementById("clear-button");
const ctx = canvas.getContext("2d");

let drawing = false;
let prevX = 0;
let prevY = 0;

// Event listeners for drawing
canvas.addEventListener("mousedown", (e) => {
	drawing = true;
	prevX = e.clientX - canvas.getBoundingClientRect().left;
	prevY = e.clientY - canvas.getBoundingClientRect().top;
});

canvas.addEventListener("mousemove", (e) => {
	if (!drawing) return;
	draw(e.clientX - canvas.getBoundingClientRect().left, e.clientY - canvas.getBoundingClientRect().top);
});

canvas.addEventListener("mouseup", () => {
	drawing = false;
});

canvas.addEventListener("mouseleave", () => {
	drawing = false;
});

// Function to draw on the canvas
function draw(x, y) {
	ctx.beginPath();
	ctx.strokeStyle = "#000";
	ctx.lineWidth = 2;
	ctx.lineJoin = "round";
	ctx.moveTo(prevX, prevY);
	ctx.lineTo(x, y);
	ctx.closePath();
	ctx.stroke();
	prevX = x;
	prevY = y;
}

// Event listener for the clear button
clearButton.addEventListener("click", (event) => {
	event.preventDefault(); // Prevent form submission
	ctx.clearRect(0, 0, canvas.width, canvas.height);
});
</script>
<script>
$(document).ready(function() {
	var dt_user_table = $('#users_list').DataTable({
		responsive: true,
		language: {
			sLengthMenu: 'Show _MENU_',
			search: '',
			searchPlaceholder: 'Search Users'
		},
		'columnDefs': [
			{ targets: 0, orderable: false, searchable: true },
			{
				checkboxes: {
					'className': 'dt-checkboxes form-check-input'
				}
			}
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

	// Tambahkan tombol "Add New" ke dalam div "add-new"
	$('.add-new').html(
		"<button class='btn btn-primary waves-effect waves-light' data-bs-toggle='modal' data-bs-target='#addNewUser'><i class='ri-add-line me-0 me-sm-1 d-inline-block d-sm-none'></i><span class= 'd-none d-sm-inline-block'> Add New User </span ></button>"
	);
	$('input[type="checkbox"][autocomplete="off"]').removeClass().addClass('dt-checkboxes form-check-input');
	
	$('.select-all').on('click', function() {
		// Check/uncheck all checkboxes in the table
		var rows = table.rows({ 'search': 'applied' }).nodes();
		$('input[type="checkbox"]', rows).prop('checked', this.checked);
	});

	// Handle click on checkbox to set state of "Select all" control
	$('.datatables-users tbody').on('change', 'input[type="checkbox"]', function() {
		// If checkbox is not checked
		if (!this.checked) {
			var el = $('.select-all').get(0);
			// If "Select all" control is checked and has 'indeterminate' property
			if (el && el.checked && ('indeterminate' in el)) {
				// Set visual state of "Select all" control 
				// as 'indeterminate'
				el.indeterminate = true;
			}
		}
	});
	
	function saveCurrentPage() {
		//var currentPage = dt_user_table.page();
		//sessionStorage.setItem('currentPage', currentPage);
		var info = dt_user_table.page.info();
		var currentPage = info.page;
		var lastPage = info.pages - 1;
	
		if (currentPage === lastPage) {
			sessionStorage.setItem('currentPage', currentPage);
		} else {
			sessionStorage.setItem('currentPage', lastPage);
		}
	}
	// Mengatur DataTable ke halaman yang disimpan
	function setCurrentPage() {
		var currentPage = sessionStorage.getItem('currentPage');
		if (currentPage) {
			dt_user_table.page(parseInt(currentPage)).draw(false);
			sessionStorage.removeItem('currentPage'); // Hapus data dari storage setelah digunakan
		}
	}
	function isCanvasBlank(canvas) {
		const blank = document.createElement('canvas');
		blank.width = canvas.width;
		blank.height = canvas.height;
		return canvas.toDataURL() === blank.toDataURL();
	}
	setCurrentPage();
	var isSubmitting = false;
	$('#addNewUserForm').on('submit', function (e) {
		e.preventDefault();
		if (isSubmitting) {
			return; // prevent multiple submissions
		}
		isSubmitting = true;
		var canvas = document.getElementById('signature-pad');
		if (isCanvasBlank(canvas)) {
			alert('Please provide a signature.');
			return false;
		}
		var signatureData = canvas.toDataURL('image/png');
		document.getElementById('signature').value = signatureData;
		var formData = $(this).serialize();
		$.ajax({
			url: 'users/add', // Ensure the URL is correct
			type: 'POST',
			data: formData, // Directly pass the serialized form data
			dataType: 'json',
			success: function(data) {
				console.log(data); // Debug: Check the response data
				if (data.success) {
					saveCurrentPage(); // Save the current page
					window.location.replace("?successMessage=" + encodeURIComponent(data.message));
				} else {
					window.location.replace("?errorMessage=" + encodeURIComponent(data.message));
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error('AJAX Error:', textStatus, errorThrown);
				console.error('Response Text:', jqXHR.responseText);
				alert('An error occurred. Please try again.');
			},
			complete: function() {
				isSubmitting = false; // Reset isSubmitting flag
			}
		});
	});
	
	$('#users_list').on('click', '.edit-record', function () {
		var userId = $(this).data('id');
		$.ajax({
			url: 'users/get_user.php',
			type: 'GET',
			data: { user_id: userId },
			dataType: 'json',
			success: function(data) {
				if (data.success) {
					var user = data.user;
					$('#editUserId').val(user.id);
					$('#editFirstName').val(user.first_name);
					$('#editLastName').val(user.last_name);
					$('#editGelar').val(user.gelar);
					$('#editUsername').val(user.username);
					$('#editEmail').val(user.email);
					$('#editCountry').val(user.country).trigger('change');
					$('#editPosition').val(user.position).trigger('change');
					$('#editAddress1').val(user.address1);
					$('#editAddress2').val(user.address2);
					$('#editPhoneNumber').val(user.phone_number);
					$('#editCity').val(user.city);
					$('#editState').val(user.state);
					$('#editZipCode').val(user.zip_code);
					$('#editTechnician').prop('checked', user.technician == 1);
					
					// Clear the signature pad and redraw the signature
					var editCanvas = document.getElementById('edit-signature-pad');
					var ctx = editCanvas.getContext('2d');
					ctx.clearRect(0, 0, editCanvas.width, editCanvas.height);
					if (user.signature) {
						var signatureImage = new Image();
						signatureImage.src = user.signature;
						signatureImage.onload = function() {
							ctx.drawImage(signatureImage, 0, 0);
						};
					}
					
					$('.form-check.custom-option').removeClass('checked');
					// Set the access level radio button
					if (user.access_level == 2) {
						$('#editRadioIcon3').prop('checked', true).closest('.form-check.custom-option').addClass('checked');
					}
					else if (user.access_level == 1) {
						$('#editRadioIcon2').prop('checked', true).closest('.form-check.custom-option').addClass('checked');
					}
					else {
						 $('#editRadioIcon1').prop('checked', true).closest('.form-check.custom-option').addClass('checked');
					}
					
					// Set up the signature pad for editing
					let drawing = false;
					let prevX = 0;
					let prevY = 0;
	
					// Event listeners for drawing
					editCanvas.addEventListener("mousedown", (e) => {
						drawing = true;
						prevX = e.clientX - editCanvas.getBoundingClientRect().left;
						prevY = e.clientY - editCanvas.getBoundingClientRect().top;
					});
	
					editCanvas.addEventListener("mousemove", (e) => {
						if (!drawing) return;
						draw(e.clientX - editCanvas.getBoundingClientRect().left, e.clientY - editCanvas.getBoundingClientRect().top);
					});
	
					editCanvas.addEventListener("mouseup", () => {
						drawing = false;
					});
	
					editCanvas.addEventListener("mouseleave", () => {
						drawing = false;
					});
	
					// Function to draw on the canvas
					function draw(x, y) {
						ctx.beginPath();
						ctx.strokeStyle = "#000";
						ctx.lineWidth = 2;
						ctx.lineJoin = "round";
						ctx.moveTo(prevX, prevY);
						ctx.lineTo(x, y);
						ctx.closePath();
						ctx.stroke();
						prevX = x;
						prevY = y;
					}
	
					$('#editUserModal').modal('show');
				} else {
					alert(data.message);
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				alert('Error retrieving user details. Please try again.');
			}
		});
	});

	// Edit User Form Submission
	$('#editUserForm').on('submit', function (e) {
		e.preventDefault();
		var canvas = document.getElementById('edit-signature-pad');
		if (isCanvasBlank(canvas)) {
			window.location.replace("?errorMessage=Please provide a signature.");
			return false;
		}
		var signatureData = canvas.toDataURL('image/png');
		document.getElementById('editSignature').value = signatureData;
		var formData = $(this).serialize();
		$.ajax({
			url: 'users/update',
			type: 'POST',
			data: formData,
			dataType: 'json',
			success: function(data) {
				if (data.success) {
					saveCurrentPage();
					window.location.replace("?successMessage=" + encodeURIComponent(data.message));
				} else {
					window.location.replace("?errorMessage=" + encodeURIComponent(data.message));
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.error('AJAX Error:', textStatus, errorThrown);
				console.error('Response Text:', jqXHR.responseText);
				alert('An error occurred. Please try again.');
			}
		});
	});
	
	$('#users_list').on('click', '.delete-record', function () {
			var $row = $(this).closest('tr');
			var id = $(this).data('id');
			var table = $('#users_list').DataTable();
			var currentPage = table.page();
			var recordsOnPage = table.rows({ page: 'current' }).count(); // Menggunakan .count()
	
			if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
				$.ajax({
					url: 'users/delete', // Pastikan URL benar
					type: 'POST',
					data: { user_id: id },
					dataType: 'json',
					success: function(data) {
						if (data.success) {
							table.row($row).remove().draw(false);
							// Perbarui nomor urut
							table.rows().every(function (rowIdx, tableLoop, rowLoop) {
								this.cell(rowIdx, 0).data(rowIdx + 1); // Update nomor urut
							}).draw(false);
	
							// Pindah ke halaman sebelumnya jika halaman saat ini kosong setelah penghapusan
							if (recordsOnPage === 1 && currentPage > 0) {
								table.page(currentPage - 1).draw('page');
							}
						} else {
							alert(data.message);
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert('Error sending request. Please try again.');
					}
				});
			}
	});
});
const editClearButton = document.getElementById("edit-clear-button");
	editClearButton.addEventListener("click", (event) => {
		event.preventDefault(); // Prevent form submission
		var editCanvas = document.getElementById('edit-signature-pad');
		var ctx = editCanvas.getContext("2d");
		ctx.clearRect(0, 0, editCanvas.width, editCanvas.height);
});
</script>


@endsection

