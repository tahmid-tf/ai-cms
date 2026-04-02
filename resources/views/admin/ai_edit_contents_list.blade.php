@extends('layouts.admin')

@section('content')
    <main>

        <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
            <div class="container-xl px-4">
                <div class="page-header-content">
                    <div class="row align-items-center justify-content-between pt-3">
                        <div class="col-auto mb-3">
                            <h1 class="page-header-title">
                                <div class="page-header-icon"><i data-feather="edit-3"></i></div>
                                View Edited Contents
                            </h1>
                        </div>
                        <div class="col-12 col-xl-auto mb-3">
                            <a class="btn btn-sm btn-light text-primary" href="{{ route('ai_editor.editor') }}">
                                <i class="me-1" data-feather="arrow-left"></i>
                                Open Editor
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="container-xl px-4 mt-4">
            <div class="card">
                <div class="card-body">

                    <table id="editContentsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Edit Type</th>
                                <th>Original Content</th>
                                <th>Edited Content</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($contentEdits as $contentEdit)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucfirst($contentEdit->edit_type) }}</td>
                                    <td>{{ Str::limit($contentEdit->original_content, 50, '...') }}</td>
                                    <td>{{ Str::limit($contentEdit->edited_content, 50, '...') }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-primary view-content-edit-btn"
                                            data-original="{{ htmlspecialchars($contentEdit->original_content, ENT_QUOTES) }}"
                                            data-edited="{{ htmlspecialchars($contentEdit->edited_content, ENT_QUOTES) }}"
                                            data-type="{{ $contentEdit->edit_type }}" title="View">
                                            <i data-feather="eye"></i>
                                        </button>
                                        <button class="btn btn-xs btn-danger delete-content-edit-btn"
                                            data-id="{{ $contentEdit->id }}" title="Delete">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                        <button class="btn btn-xs btn-warning edit-content-edit-btn"
                                            data-id="{{ $contentEdit->id }}"
                                            data-type="{{ $contentEdit->edit_type }}"
                                            data-original="{{ htmlspecialchars($contentEdit->original_content, ENT_QUOTES) }}"
                                            data-edited="{{ htmlspecialchars($contentEdit->edited_content, ENT_QUOTES) }}"
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

        function formatEditTypeLabel(type) {
            if (!type) {
                return '';
            }

            return type.charAt(0).toUpperCase() + type.slice(1);
        }

        function buildEditActionButtons(contentEdit) {
            return `
                <button class="btn btn-xs btn-primary view-content-edit-btn"
                    data-original="${escapeHtml(contentEdit.original_content)}"
                    data-edited="${escapeHtml(contentEdit.edited_content)}"
                    data-type="${escapeHtml(contentEdit.edit_type)}"
                    title="View">
                    <i data-feather="eye"></i>
                </button>
                <button class="btn btn-xs btn-danger delete-content-edit-btn"
                    data-id="${contentEdit.id}" title="Delete">
                    <i data-feather="trash-2"></i>
                </button>
                <button class="btn btn-xs btn-warning edit-content-edit-btn"
                    data-id="${contentEdit.id}"
                    data-type="${escapeHtml(contentEdit.edit_type)}"
                    data-original="${escapeHtml(contentEdit.original_content)}"
                    data-edited="${escapeHtml(contentEdit.edited_content)}"
                    title="Edit">
                    <i data-feather="edit"></i>
                </button>
            `;
        }

        $(document).ready(function() {
            $('#editContentsTable').DataTable();

            $(document).on('click', '.view-content-edit-btn', function() {
                let originalContent = $(this).data('original');
                let editedContent = $(this).data('edited');
                let editType = $(this).data('type');

                Swal.fire({
                    title: `<span>${formatEditTypeLabel(editType)} Content</span>`,
                    html: `
                        <div style="text-align:left; max-height:420px; overflow-y:auto;">
                            <label class="fw-bold mb-2 d-block">Original Content</label>
                            <div class="border rounded p-3 mb-3 bg-light">${originalContent}</div>
                            <label class="fw-bold mb-2 d-block">Edited Content</label>
                            <div class="border rounded p-3 bg-light">${editedContent}</div>
                        </div>
                    `,
                    width: '900px',
                    showCloseButton: true,
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Close'
                });
            });
        });
    </script>

    <script>
        $(document).on('click', '.delete-content-edit-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');

            Swal.fire({
                title: 'Delete Edited Content',
                text: 'Are you sure you want to delete this record?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/content-edits/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#editContentsTable').DataTable().row(row).remove().draw();

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
        $(document).on('click', '.edit-content-edit-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');

            let editType = button.data('type');
            let originalContent = button.data('original');
            let editedContent = button.data('edited');

            Swal.fire({
                title: `
                    <div style="display:flex;align-items:center;gap:12px;text-align:left;">
                        <div style="width:42px;height:42px;border-radius:12px;background:#eef4ff;display:flex;align-items:center;justify-content:center;color:#2563eb;">
                            <i data-feather="edit-3" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div style="font-size:18px;font-weight:700;color:#0f172a;">Edit Content Record</div>
                            <div style="font-size:12px;color:#64748b;font-weight:500;">Refine the saved original and edited copy</div>
                        </div>
                    </div>
                `,
                width: '980px',
                customClass: {
                    popup: 'swal-edit-record-popup',
                    title: 'swal-edit-record-title',
                    htmlContainer: 'swal-edit-record-html'
                },
                html: `
                    <div style="text-align:left;">
                        <div style="margin-bottom:18px;padding:14px 16px;border:1px solid #dbe7ff;border-radius:14px;background:linear-gradient(180deg,#f8fbff 0%,#f1f6ff 100%);">
                            <label style="display:block;margin-bottom:8px;font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#475569;">Edit Type</label>
                            <select id="swal-edit-type" class="swal2-select" style="margin:0;width:100%;height:46px;border:1px solid #cbd5e1;border-radius:12px;background:#fff;color:#0f172a;font-size:14px;font-weight:600;padding:0 14px;">
                                <option value="grammar" ${editType === 'grammar' ? 'selected' : ''}>Fix Grammar</option>
                                <option value="tone" ${editType === 'tone' ? 'selected' : ''}>Professional Tone</option>
                                <option value="seo" ${editType === 'seo' ? 'selected' : ''}>SEO Optimize</option>
                                <option value="rewrite" ${editType === 'rewrite' ? 'selected' : ''}>Rewrite</option>
                            </select>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div style="border:1px solid #e2e8f0;border-radius:16px;background:#ffffff;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.05);">
                                <div style="padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                                    <div style="font-size:13px;font-weight:700;color:#0f172a;">Original Content</div>
                                    <div style="font-size:12px;color:#64748b;">Source text before editing</div>
                                </div>
                                <div style="padding:14px;">
                                    <textarea id="swal-original-content" class="swal2-textarea" style="margin:0;width:100%;height:220px;border:1px solid #dbe3ee;border-radius:12px;background:#fbfdff;color:#1e293b;font-size:14px;line-height:1.65;padding:14px 16px;">${originalContent}</textarea>
                                </div>
                            </div>

                            <div style="border:1px solid #dbe7ff;border-radius:16px;background:#ffffff;overflow:hidden;box-shadow:0 10px 30px rgba(37,99,235,.08);">
                                <div style="padding:12px 16px;border-bottom:1px solid #dbe7ff;background:linear-gradient(180deg,#f8fbff 0%,#eef4ff 100%);">
                                    <div style="font-size:13px;font-weight:700;color:#0f172a;">Edited Content</div>
                                    <div style="font-size:12px;color:#64748b;">Final version shown to users</div>
                                </div>
                                <div style="padding:14px;">
                                    <textarea id="swal-edited-content" class="swal2-textarea" style="margin:0;width:100%;height:220px;border:1px solid #c7d7fe;border-radius:12px;background:#ffffff;color:#0f172a;font-size:14px;line-height:1.65;padding:14px 16px;">${editedContent}</textarea>
                                </div>
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
                    let updatedType = $('#swal-edit-type').val();
                    let updatedOriginalContent = $('#swal-original-content').val();
                    let updatedEditedContent = $('#swal-edited-content').val();

                    if (!updatedType || !updatedOriginalContent || !updatedEditedContent) {
                        Swal.showValidationMessage('All fields are required');
                        return false;
                    }

                    return {
                        edit_type: updatedType,
                        original_content: updatedOriginalContent,
                        edited_content: updatedEditedContent
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/content-edits/${id}`,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            edit_type: result.value.edit_type,
                            original_content: result.value.original_content,
                            edited_content: result.value.edited_content
                        },
                        success: function(response) {
                            if (response.success) {
                                let updated = response.data;
                                let table = $('#editContentsTable').DataTable();

                                table.row(row).data([
                                    row.find('td:eq(0)').text(),
                                    formatEditTypeLabel(updated.edit_type),
                                    getPreviewText(updated.original_content),
                                    getPreviewText(updated.edited_content),
                                    buildEditActionButtons(updated)
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
                        error: function(xhr) {
                            let message = 'Update failed!';

                            if (xhr.status === 422 && xhr.responseJSON?.errors) {
                                message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            } else if (xhr.responseJSON?.message) {
                                message = xhr.responseJSON.message;
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                html: message
                            });
                        }
                    });
                }
            });
        });
    </script>

    <style>
        .swal2-popup.swal-edit-record-popup {
            padding: 1.1rem 1.25rem 1.25rem;
        }

        .swal2-title.swal-edit-record-title {
            margin: 0;
            padding: 0;
        }

        .swal2-html-container.swal-edit-record-html {
            margin-top: 0.65rem;
            padding-top: 0;
        }
    </style>
@endpush
