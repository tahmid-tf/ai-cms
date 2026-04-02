@extends('layouts.admin')

@section('content')
    <main>
        <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
            <div class="container-xl px-4">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="user-edit"></i></div>
                                Edit User
                            </h1>
                        </div>
                        <div class="col-12 col-xl-auto mb-3">
                            <a class="btn btn-sm btn-light text-primary" href="{{ route('admin.users.index') }}">
                                <i class="me-1" data-feather="arrow-left"></i>
                                Back to Users List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="container-xl px-4 mt-4">
            <form id="editUserForm" method="POST" action="{{ route('admin.users.update', $user->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-xl-4">
                        <div class="card mb-4 mb-xl-0">
                            <div class="card-header">Profile Picture</div>
                            <div class="card-body text-center">

                                <img id="preview-image" class="img-account-profile rounded-circle mb-2"
                                    src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('assets/img/demo/user-placeholder.svg') }}"
                                    style="width:150px;height:150px;object-fit:cover;" />

                                <div class="small font-italic text-muted mb-4">
                                    JPG or PNG no larger than 5 MB
                                </div>

                                <input type="file" name="image" id="imageInput" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="card mb-4">
                            <div class="card-header">Account Details</div>
                            <div class="card-body">

                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1">First name</label>
                                        <input class="form-control" name="first_name" type="text"
                                            value="{{ explode(' ', $user->name)[0] ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1">Last name</label>
                                        <input class="form-control" name="last_name" type="text"
                                            value="{{ explode(' ', $user->name)[1] ?? '' }}" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="small mb-1">Email</label>
                                            <input class="form-control" name="email" type="email"
                                                value="{{ $user->email }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1">Password <small>(leave blank to keep
                                                    current)</small></label>
                                            <input class="form-control" name="password" type="password">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="small mb-1">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option disabled>Select a role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}"
                                                {{ $user->hasRole($role) ? 'selected' : '' }}>
                                                {{ ucfirst($role) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button class="btn btn-primary" type="submit">
                                    Update User
                                </button>

                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            let form = $('#editUserForm');

            $('#imageInput').on('change', function(e) {
                let file = e.target.files[0];

                if (file) {
                    let reader = new FileReader();

                    reader.onload = function(e) {
                        $('#preview-image').attr('src', e.target.result);
                    };

                    reader.readAsDataURL(file);
                }
            });

            form.on('submit', function(e) {
                e.preventDefault();

                let submitButton = form.find('button[type="submit"]');
                let originalText = submitButton.text();
                let formData = new FormData(this);

                submitButton.prop('disabled', true).text('Updating...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 1800,
                            showConfirmButton: false
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'User update failed.';

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: errorMessage
                        });
                    },
                    complete: function() {
                        submitButton.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
    </script>
@endpush
