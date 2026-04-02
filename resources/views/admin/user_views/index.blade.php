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
                                View Users
                            </h1>
                        </div>
                        <div class="col-12 col-xl-auto mb-3">
                            <a class="btn btn-sm btn-light text-primary" href="{{ route('admin.users.create') }}">
                                <i class="me-1" data-feather="arrow-left"></i>
                                Add User
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>



        <div class="container-xl px-4 mt-4">

            <div class="card">
                <div class="card-body">

                    <table id="usersTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>

                                    <td>
                                        <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('assets/img/demo/user-placeholder.svg') }}"
                                            width="50" height="50" style="object-fit:cover;border-radius:50%;">
                                    </td>

                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>

                                    <td>
                                        @foreach ($user->roles as $role)
                                            <span class="badge bg-success">{{ $role->name }}</span>
                                        @endforeach
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                            class="btn btn-sm btn-warning">Edit</a>

                                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $user->id }}">
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>
            </div>

        </div>
    </main>
@endsection


@push('scripts')
    <script>
        $(document).ready(function() {

            // DataTable Init
            let table = $('#usersTable').DataTable();

            // Delete with Swal
            $(document).on('click', '.delete-btn', function() {
                let button = $(this);
                let userId = button.data('id');
                let row = button.closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This user will be deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonColor: '#858796'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: '/admin/users/' + userId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    table.row(row).remove().draw(false);

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'User deleted successfully.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to delete user.'
                                });
                            }
                        });

                    }
                });
            });

        });
    </script>
@endpush
