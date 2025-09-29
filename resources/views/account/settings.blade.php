@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-12">
            
            {{-- Navigation Pills --}}
            <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-md-row mb-6 gap-2 gap-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('account.settings') }}">
                            <i class="ri-group-line me-2"></i>Account
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('account.security') }}">
                            <i class="ri-lock-line me-2"></i>Security
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Main Content Card --}}
            <div class="card mb-6">
                <form id="formAccountSettings" method="POST" action="{{ route('account.update.details') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="card-body">
                        {{-- Profile Picture Upload --}}
                        <div class="d-flex align-items-start align-items-sm-center gap-6">
                            <img src="{{ Auth::user()->profile_picture ? asset('assets/img/profile/' . Auth::user()->profile_picture) : asset('assets/img/avatars/1.png') }}"
                                 alt="user-avatar"
                                 class="d-block w-px-100 h-px-100 rounded-4"
                                 id="uploadedAvatar" />
                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-3 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new photo</span>
                                    <i class="ri-upload-2-line d-block d-sm-none"></i>
                                    <input type="file" id="upload" name="profile_picture" class="account-file-input" hidden accept="image/png, image/jpeg" />
                                </label>
                                <div>Allowed JPG, GIF or PNG. Max size of 800K</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-0">

                    <div class="card-body pt-4">
                        {{-- Form Fields --}}
                        <div class="row mt-1 g-5">
                            {{-- Note: 'name' is used instead of 'firstName' and 'lastName' to match the database --}}
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" autofocus />
                                    <label for="name">Full Name</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="phoneNumber" name="phone_number" class="form-control" value="{{ old('phone_number', Auth::user()->phone_number) }}" />
                                        <label for="phoneNumber">Phone Number</label>
                                    </div>
                                    <span class="input-group-text">ID (+62)</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address', Auth::user()->address) }}" />
                                    <label for="address">Address</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input class="form-control" type="text" id="state" name="state" value="{{ old('state', Auth::user()->state) }}" />
                                    <label for="state">State</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="city" name="city" value="{{ old('city', Auth::user()->city) }}" />
                                    <label for="city">City</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="zipCode" name="zip_code" value="{{ old('zip_code', Auth::user()->zip_code) }}" maxlength="6" />
                                    <label for="zipCode">Zip Code</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="country" name="country" class="select2 form-select">
                                        {{-- The selected option is now determined by the user's data --}}
                                        <option value="Indonesia" @selected(old('country', Auth::user()->country) == 'Indonesia')>Indonesia</option>
                                        <option value="United States" @selected(old('country', Auth::user()->country) == 'United States')>United States</option>
                                        {{-- Add other countries as needed --}}
                                    </select>
                                    <label for="country">Country</label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="btn btn-primary me-3">Save changes</button>
                        </div>
                    </div>
                </form>
                </div>
        </div>
    </div>
</div>
@endsection