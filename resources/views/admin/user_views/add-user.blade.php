@extends('layouts.admin')

@section('content')
    <main>
        <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
            <div class="container-xl px-4">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="user-plus"></i></div>
                                Add User
                            </h1>
                        </div>
                        <div class="col-12 col-xl-auto mb-3">
                            {{-- <a class="btn btn-sm btn-light text-primary" href="{{ route('admin.users.index') }}"> --}}
                            <i class="me-1" data-feather="arrow-left"></i>
                            Back to Users List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="container-xl px-4 mt-4">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <!-- Profile Picture -->
                    <div class="col-xl-4">
                        <div class="card mb-4 mb-xl-0">
                            <div class="card-header">Profile Picture</div>
                            <div class="card-body text-center">

                                <img id="preview-image" class="img-account-profile rounded-circle mb-2"
                                    src="{{ asset('assets/img/demo/user-placeholder.svg') }}"
                                    style="width:150px;height:150px;object-fit:cover;" />

                                <div class="small font-italic text-muted mb-4">
                                    JPG or PNG no larger than 5 MB
                                </div>

                                <input type="file" name="image" id="imageInput" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Account Details -->
                    <div class="col-xl-8">
                        <div class="card mb-4">
                            <div class="card-header">Account Details</div>
                            <div class="card-body">

                                <!-- Name -->
                                <div class="row gx-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="small mb-1">First name</label>
                                        <input class="form-control" name="first_name" type="text"
                                            value="{{ old('first_name') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small mb-1">Last name</label>
                                        <input class="form-control" name="last_name" type="text"
                                            value="{{ old('last_name') }}" required>
                                    </div>
                                </div>

                                <!-- Email & Password -->
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="small mb-1">Email</label>
                                            <input class="form-control" name="email" type="email"
                                                value="{{ old('email') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="small mb-1">Password</label>
                                            <input class="form-control" name="password" type="password" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Role -->
                                <div class="mb-3">
                                    <label class="small mb-1">Role</label>
                                    <select name="role" class="form-select" required>
                                        <option disabled selected>Select a role</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor
                                        </option>
                                        <option value="viewer" {{ old('role') == 'viewer' ? 'selected' : '' }}>Viewer
                                        </option>
                                    </select>
                                </div>

                                <!-- Submit -->
                                <button class="btn btn-primary" type="submit">
                                    Add User
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {

            // Image Preview
            $('#imageInput').on('change', function(e) {
                let file = e.target.files[0];

                if (file) {
                    let reader = new FileReader();

                    reader.onload = function(e) {
                        $('#preview-image').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(file);
                }
            });

        });
    </script>

    {{-- Success Alert --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        </script>
    @endif

    {{-- Error Alert --}}
    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: `{!! implode('<br>', $errors->all()) !!}`
            });
        </script>
    @endif
@endpush
