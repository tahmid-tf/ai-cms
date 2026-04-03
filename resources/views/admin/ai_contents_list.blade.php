@extends('layouts.admin')

@section('content')
    @php
        $user = auth()->user();
        $canEditRecords = $user?->hasAnyRole(['admin', 'editor']);
        $canDeleteRecords = $user?->hasRole('admin');
    @endphp
    <main>
        <div class="lp-topbar">
            <div class="lp-topbar-left">
                <div class="lp-icon-wrap">
                    <i data-feather="file-text"></i>
                </div>
                <div>
                    <div class="lp-title">View Saved Contents</div>
                    <div class="lp-sub">Manage generated content with quick view, edit, and delete actions</div>
                </div>
            </div>
            <div class="lp-badges">
                <span class="lp-badge">Content Library</span>
            </div>
        </div>

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
                                <th>{{ $canEditRecords || $canDeleteRecords ? 'Action' : 'View' }}</th>
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
                                        @if ($canDeleteRecords)
                                            <button class="btn btn-xs btn-danger delete-content-btn"
                                                data-id="{{ $content->id }}" title="Delete">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        @endif
                                        @if ($canEditRecords)
                                            <button class="btn btn-xs btn-warning edit-content-btn"
                                                data-id="{{ $content->id }}" data-type="{{ $content->content_type }}"
                                                data-prompt="{{ htmlspecialchars($content->prompt, ENT_QUOTES) }}"
                                                data-text="{{ htmlspecialchars($content->generated_text, ENT_QUOTES) }}"
                                                title="Edit">
                                                <i data-feather="edit"></i>
                                            </button>
                                        @endif
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
                @if ($canDeleteRecords)
                    <button class="btn btn-xs btn-danger delete-content-btn"
                        data-id="${content.id}" title="Delete">
                        <i data-feather="trash-2"></i>
                    </button>
                @endif
                @if ($canEditRecords)
                    <button class="btn btn-xs btn-warning edit-content-btn"
                        data-id="${content.id}"
                        data-type="${escapeHtml(content.content_type)}"
                        data-prompt="${escapeHtml(content.prompt)}"
                        data-text="${escapeHtml(content.generated_text)}"
                        title="Edit">
                        <i data-feather="edit"></i>
                    </button>
                @endif
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
                <style>
                    .swal-form {
                        text-align: left;
                        font-family: system-ui, -apple-system, sans-serif;
                    }

                    .swal-group {
                        margin-bottom: 15px;
                    }

                    .swal-label {
                        display: block;
                        font-size: 13px;
                        font-weight: 600;
                        margin-bottom: 5px;
                        color: #444;
                    }

                    .swal-input,
                    .swal-textarea {
                        width: 100%;
                        border-radius: 8px;
                        border: 1px solid #ddd;
                        padding: 10px 12px;
                        font-size: 14px;
                        transition: all 0.2s ease;
                    }

                    .swal-input:focus,
                    .swal-textarea:focus {
                        border-color: #6366f1;
                        outline: none;
                        box-shadow: 0 0 0 2px rgba(99,102,241,0.15);
                    }

                    .swal-textarea {
                        min-height: 90px;
                        resize: vertical;
                    }

                    .swal-textarea.large {
                        min-height: 150px;
                    }

                    .swal-card {
                        background: #f9fafb;
                        padding: 15px;
                        border-radius: 10px;
                        border: 1px solid #eee;
                    }
                </style>

                <div class="swal-form">
                    <div class="swal-card">

                        <div class="swal-group">
                            <label class="swal-label">Content Type</label>
                            <input id="swal-type" class="swal-input" value="${contentType}">
                        </div>

                        <div class="swal-group">
                            <label class="swal-label">Prompt</label>
                            <textarea id="swal-prompt" class="swal-textarea">${prompt}</textarea>
                        </div>

                        <div class="swal-group">
                            <label class="swal-label">Generated Text</label>
                            <textarea id="swal-text" class="swal-textarea large">${generatedText}</textarea>
                        </div>

                    </div>
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

    <style>
        .lp-topbar {
            background: linear-gradient(90deg, #ffffff 0%, #f1fbfa 100%);
            border-bottom: 1px solid #d8e4e4;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .lp-topbar-left {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .lp-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(180deg, #14b8a6 0%, #0f766e 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(15, 118, 110, 0.22);
        }

        .lp-icon-wrap svg {
            width: 18px;
            height: 18px;
        }

        .lp-title {
            font-size: 1rem;
            font-weight: 700;
            color: #102a2a;
        }

        .lp-sub {
            font-size: 0.76rem;
            color: #789090;
        }

        .lp-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.32rem 0.8rem;
            border-radius: 999px;
            background: #e6f6f4;
            color: #0f766e;
            font-size: 0.74rem;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .lp-topbar {
                padding-left: 1rem;
                padding-right: 1rem;
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush
