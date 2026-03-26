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
                                        <button class="btn btn-xs btn-warning edit-content-btn"
                                            data-id="{{ $content->id }}" data-type="{{ $content->content_type }}"
                                            data-prompt="{{ htmlspecialchars($content->prompt, ENT_QUOTES) }}"
                                            data-text="{{ htmlspecialchars($content->generated_text, ENT_QUOTES) }}"
                                            title="Edit">
                                            <i data-feather="edit"></i>
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
        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function getPreviewText(value, limit = 50) {
            return value.length > limit ? value.substring(0, limit) + '...' : value;
        }

        function buildActionButtons(content) {
            return `
                <button class="btn btn-xs btn-primary view-content-btn"
                    data-content="${escapeHtml(content.generated_text)}"
                    title="View Content">
                    <i data-feather="eye"></i>
                </button>
                <button class="btn btn-xs btn-danger delete-content-btn"
                    data-id="${content.id}" title="Delete">
                    <i data-feather="trash-2"></i>
                </button>
                <button class="btn btn-xs btn-warning edit-content-btn"
                    data-id="${content.id}"
                    data-type="${escapeHtml(content.content_type)}"
                    data-prompt="${escapeHtml(content.prompt)}"
                    data-text="${escapeHtml(content.generated_text)}"
                    title="Edit">
                    <i data-feather="edit"></i>
                </button>
            `;
        }

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

    <script>
        // update data
        $(document).on('click', '.edit-content-btn', function() {

            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');

            let contentType = button.data('type');
            let prompt = button.data('prompt');
            let generatedText = button.data('text');

            Swal.fire({
                title: '<i data-feather="edit" style="width:28px;height:28px;"></i>',
                width: '800px',
                html: `
            <div class="text-start">
                <label class="mb-1">Content Type</label>
                <input id="swal-type" class="swal2-input" value="${contentType}">

                <label class="mb-1 mt-2">Prompt</label>
                <textarea id="swal-prompt" class="swal2-textarea">${prompt}</textarea>

                <label class="mb-1 mt-2">Generated Text</label>
                <textarea id="swal-text" class="swal2-textarea" style="height:150px;">${generatedText}</textarea>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                didOpen: () => {
                    feather.replace();
                },
                preConfirm: () => {

                    let updatedType = $('#swal-type').val();
                    let updatedPrompt = $('#swal-prompt').val();
                    let updatedText = $('#swal-text').val();

                    if (!updatedType || !updatedPrompt || !updatedText) {
                        Swal.showValidationMessage('All fields are required');
                        return false;
                    }

                    return {
                        content_type: updatedType,
                        prompt: updatedPrompt,
                        generated_text: updatedText
                    };
                }

            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({
                        url: `/contents/${id}`,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            content_type: result.value.content_type,
                            prompt: result.value.prompt,
                            generated_text: result.value.generated_text
                        },
                        success: function(response) {

                            if (response.success) {

                                // 🔥 Update row UI instantly
                                let updated = response.data;

                                let table = $('#usersTable').DataTable();

                                table.row(row).data([
                                    row.find('td:eq(0)').text(), // keep index
                                    updated.content_type,
                                    updated.prompt,
                                    getPreviewText(updated.generated_text),
                                    buildActionButtons(updated)
                                ]).draw();

                                feather.replace();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
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
                                text: 'Update failed!'
                            });
                        }
                    });

                }

            });

        });
    </script>
@endpush
