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
                                View Saved Contents
                            </h1>
                        </div>
                        <div class="col-12 col-xl-auto mb-3">
                            <a class="btn btn-sm btn-light text-primary" href="">
                                <i class="me-1" data-feather="arrow-left"></i>
                                Create Content
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="container-xl px-4 mt-4">
            <div class="card">
                <div class="card-body">

                    <table id="usersTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Content Type</th>
                                <th>Prompt</th>
                                <th>Generated Text</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($contents as $content)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $content->content_type }}</td>
                                    <td>{{ $content->prompt }}</td>
                                    <td>{{ Str::limit($content->generated_text, 50, '...') }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-primary view-content-btn"
                                            data-content="{{ htmlspecialchars($content->generated_text, ENT_QUOTES) }}"
                                            title="View Content">
                                            <i data-feather="eye"></i>
                                        </button>
                                        <button class="btn btn-xs btn-danger delete-content-btn"
                                            data-id="{{ $content->id }}" title="Delete">
                                            <i data-feather="trash-2"></i>
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

            $('#usersTable').DataTable();

            $(document).on('click', '.view-content-btn', function() {
                let content = $(this).data('content');

                Swal.fire({
                    title: '<i data-feather="file-text" style="width:28px;height:28px;"></i>',
                    html: `
                    <div style="text-align:left; max-height:300px; overflow-y:auto;">
                        <p id="swal-content-text">${content}</p>
                    </div>
                `,
                    width: '800px',
                    showCloseButton: true,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Close',
                    confirmButtonText: 'Copy',
                    showDenyButton: true,
                    denyButtonText: 'Copy',
                    didOpen: () => {
                        feather.replace();
                        document.querySelector('.swal2-deny').addEventListener('click',
                            function() {
                                navigator.clipboard.writeText(content).then(() => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Copied!',
                                        text: 'Content copied to clipboard',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                });
                            });
                    }
                });
            });
        });

        $(document).on('click', '.delete-content-btn', function() {

            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');

            Swal.fire({
                title: 'Delete Content',
                text: "Are you sure you want to delete this content?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, delete it!',
                didOpen: () => {
                    feather.replace();
                }
            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: `/contents/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {

                            if (response.success) {

                                // Remove row from DataTable
                                $('#usersTable').DataTable().row(row).remove().draw();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong!'
                            });
                        }
                    });

                }

            });

        });
    </script>
@endpush
