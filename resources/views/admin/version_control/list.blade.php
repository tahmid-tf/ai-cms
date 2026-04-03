@extends('layouts.admin')

@section('content')
    <main>
        <div class="lp-topbar">
            <div class="lp-topbar-left">
                <div class="lp-icon-wrap">
                    <i data-feather="git-branch"></i>
                </div>
                <div>
                    <div class="lp-title">Version Control List</div>
                    <div class="lp-sub">Monitor drafts, published content, and restore-ready version history</div>
                </div>
            </div>
            <div class="lp-badges">
                <span class="lp-badge">Versioned Content</span>
            </div>
        </div>

        <div class="container-xl px-4 mt-4">
            <div class="card">
                <div class="card-body">
                    <table id="versionControlTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Current Version</th>
                                <th>Updated At</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($contents as $content)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $content->title }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $content->status === 'published' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($content->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $content->current_version_id ?: 'N/A' }}</td>
                                    <td>{{ $content->updated_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-primary view-version-content-btn"
                                            data-title="{{ htmlspecialchars($content->title, ENT_QUOTES) }}"
                                            data-status="{{ $content->status }}"
                                            data-content="{{ htmlspecialchars($content->content, ENT_QUOTES) }}"
                                            data-version="{{ $content->current_version_id ?: 'N/A' }}" title="View">
                                            <i data-feather="eye"></i>
                                        </button>
                                        <button class="btn btn-xs btn-info history-version-content-btn"
                                            data-id="{{ $content->id }}"
                                            data-title="{{ htmlspecialchars($content->title, ENT_QUOTES) }}"
                                            title="History">
                                            <i data-feather="clock"></i>
                                        </button>
                                        <button class="btn btn-xs btn-warning edit-version-content-btn"
                                            data-id="{{ $content->id }}"
                                            data-title="{{ htmlspecialchars($content->title, ENT_QUOTES) }}"
                                            data-status="{{ $content->status }}"
                                            data-content="{{ htmlspecialchars($content->content, ENT_QUOTES) }}"
                                            data-version="{{ $content->current_version_id ?: '' }}" title="Edit">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <button class="btn btn-xs btn-danger delete-version-content-btn"
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
        function escapeHtml(value) {
            return $('<div>').text(value ?? '').html();
        }

        function getPreviewText(value, limit = 50) {
            return value.length > limit ? value.substring(0, limit) + '...' : value;
        }

        function formatStatusBadge(status) {
            let badgeClass = status === 'published' ? 'bg-success' : 'bg-warning text-dark';
            return `<span class="badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
        }

        function buildVersionActionButtons(content) {
            const currentVersion = content.current_version_id || 'N/A';

            return `
                <button class="btn btn-xs btn-primary view-version-content-btn"
                    data-title="${escapeHtml(content.title)}"
                    data-status="${escapeHtml(content.status)}"
                    data-content="${escapeHtml(content.content)}"
                    data-version="${escapeHtml(currentVersion)}" title="View">
                    <i data-feather="eye"></i>
                </button>
                <button class="btn btn-xs btn-info history-version-content-btn"
                    data-id="${content.id}"
                    data-title="${escapeHtml(content.title)}" title="History">
                    <i data-feather="clock"></i>
                </button>
                <button class="btn btn-xs btn-warning edit-version-content-btn"
                    data-id="${content.id}"
                    data-title="${escapeHtml(content.title)}"
                    data-status="${escapeHtml(content.status)}"
                    data-content="${escapeHtml(content.content)}"
                    data-version="${escapeHtml(currentVersion)}" title="Edit">
                    <i data-feather="edit"></i>
                </button>
                <button class="btn btn-xs btn-danger delete-version-content-btn"
                    data-id="${content.id}" title="Delete">
                    <i data-feather="trash-2"></i>
                </button>
            `;
        }

        function renderHistoryRows(contentId, versions) {
            if (!versions.length) {
                return `<tr><td colspan="5" class="text-center text-muted py-4">No version history found.</td></tr>`;
            }

            return versions.map((version) => `
                <tr>
                    <td class="vc-history-version-cell">${version.version_number}</td>
                    <td class="vc-history-type-cell">${version.is_auto_save === 'yes' ? 'Auto Save' : 'Manual Save'}</td>
                    <td class="vc-history-date-cell">${new Date(version.updated_at).toLocaleString()}</td>
                    <td class="vc-history-action-cell">
                        <button class="btn btn-sm btn-outline-primary vc-history-action-btn view-history-version-btn"
                            data-title="${escapeHtml(version.title)}"
                            data-content="${escapeHtml(version.content)}"
                            data-version="${escapeHtml(version.version_number)}">
                            View
                        </button>
                    </td>
                    <td class="vc-history-action-cell">
                        <button class="btn btn-sm btn-success vc-history-action-btn restore-history-version-btn"
                            data-content-id="${contentId}" data-version-id="${version.id}">
                            Restore
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function normalizePreviewContent(value) {
            return String(value ?? '').replace(/^\s+|\s+$/g, '');
        }

        $(document).ready(function() {
            $('#versionControlTable').DataTable();

            $(document).on('click', '.view-version-content-btn', function() {
                let title = $(this).data('title');
                let status = $(this).data('status');
                let content = $(this).data('content');
                let version = $(this).data('version');

                Swal.fire({
                    title: `<span>${title}</span>`,
                    html: `
                        <div style="text-align:left;max-height:420px;overflow-y:auto;">
                            <div class="mb-3">
                                <span class="badge ${status === 'published' ? 'bg-success' : 'bg-warning text-dark'} me-2">${status}</span>
                                <span class="badge bg-info text-dark">Current Version: ${version}</span>
                            </div>
                            <div class="border rounded p-3 bg-light" style="white-space:pre-wrap;">${content}</div>
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
        $(document).on('click', '.delete-version-content-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');

            Swal.fire({
                title: 'Delete Content',
                text: 'Are you sure you want to delete this content and all versions?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/version-control/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#versionControlTable').DataTable().row(row).remove().draw();

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
        $(document).on('click', '.edit-version-content-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');

            let title = button.data('title');
            let status = button.data('status');
            let content = button.data('content');

            Swal.fire({
                title: `
                    <div style="display:flex;align-items:center;gap:12px;text-align:left;">
                        <div style="width:42px;height:42px;border-radius:12px;background:#edf4ff;display:flex;align-items:center;justify-content:center;color:#1d4ed8;">
                            <i data-feather="edit-3" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div style="font-size:18px;font-weight:700;color:#0f172a;">Edit Content</div>
                            <div style="font-size:12px;color:#64748b;font-weight:500;">Saving will create a new version automatically</div>
                        </div>
                    </div>
                `,
                width: '980px',
                customClass: {
                    popup: 'swal-vc-popup',
                    title: 'swal-vc-title',
                    htmlContainer: 'swal-vc-html'
                },
                html: `
                    <div style="text-align:left;">
                        <div style="display:grid;grid-template-columns:2fr 1fr;gap:14px;margin-bottom:18px;">
                            <div style="padding:14px 16px;border:1px solid #dbe7ff;border-radius:14px;background:linear-gradient(180deg,#f8fbff 0%,#f1f6ff 100%);">
                                <label style="display:block;margin-bottom:8px;font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#475569;">Title</label>
                                <input id="swal-vc-title-input" class="swal2-input" value="${title}" style="margin:0;width:100%;height:46px;border:1px solid #cbd5e1;border-radius:12px;background:#fff;padding:0 14px;">
                            </div>
                            <div style="padding:14px 16px;border:1px solid #dbe7ff;border-radius:14px;background:linear-gradient(180deg,#f8fbff 0%,#f1f6ff 100%);">
                                <label style="display:block;margin-bottom:8px;font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#475569;">Status</label>
                                <select id="swal-vc-status" class="swal2-select" style="margin:0;width:100%;height:46px;border:1px solid #cbd5e1;border-radius:12px;background:#fff;padding:0 14px;">
                                    <option value="draft" ${status === 'draft' ? 'selected' : ''}>Draft</option>
                                    <option value="published" ${status === 'published' ? 'selected' : ''}>Published</option>
                                </select>
                            </div>
                        </div>

                        <div style="border:1px solid #dbe3ee;border-radius:16px;background:#fff;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.05);">
                            <div style="padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                                <div style="font-size:13px;font-weight:700;color:#0f172a;">Content Body</div>
                                <div style="font-size:12px;color:#64748b;">The current content will be snapshotted as a new version on save.</div>
                            </div>
                            <div style="padding:14px;">
                                <textarea id="swal-vc-content" class="swal2-textarea" style="margin:0;width:100%;height:260px;border:1px solid #dbe3ee;border-radius:12px;background:#fbfdff;color:#1e293b;font-size:14px;line-height:1.7;padding:14px 16px;">${content}</textarea>
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
                    let updatedTitle = $('#swal-vc-title-input').val();
                    let updatedStatus = $('#swal-vc-status').val();
                    let updatedContent = $('#swal-vc-content').val();

                    if (!updatedTitle || !updatedStatus || !updatedContent) {
                        Swal.showValidationMessage('All fields are required');
                        return false;
                    }

                    return {
                        title: updatedTitle,
                        status: updatedStatus,
                        content: updatedContent
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/version-control/${id}`,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            title: result.value.title,
                            status: result.value.status,
                            content: result.value.content
                        },
                        success: function(response) {
                            if (response.success) {
                                let updated = response.data;
                                let table = $('#versionControlTable').DataTable();

                                table.row(row).data([
                                    row.find('td:eq(0)').text(),
                                    updated.title,
                                    formatStatusBadge(updated.status),
                                    updated.current_version_id || 'N/A',
                                    new Date(updated.updated_at).toLocaleString(),
                                    buildVersionActionButtons(updated)
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
                                message = Object.values(xhr.responseJSON.errors).flat().join(
                                    '<br>');
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

    <script>
        $(document).on('click', '.history-version-content-btn', function() {
            let contentId = $(this).data('id');
            let title = $(this).data('title');

            $.ajax({
                url: `/version-control/${contentId}/history`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const previewVersion = response.versions[0] || null;

                        Swal.fire({
                            title: `
                                <div style="display:flex;align-items:center;gap:12px;text-align:left;">
                                    <div style="width:42px;height:42px;border-radius:12px;background:#ecfeff;display:flex;align-items:center;justify-content:center;color:#0f766e;">
                                        <i data-feather="clock" style="width:20px;height:20px;"></i>
                                    </div>
                                    <div>
                                        <div style="font-size:18px;font-weight:700;color:#0f172a;">Version History</div>
                                        <div style="font-size:12px;color:#64748b;font-weight:500;">${title}</div>
                                    </div>
                                </div>
                            `,
                            width: '980px',
                            customClass: {
                                popup: 'swal-vc-popup',
                                title: 'swal-vc-title',
                                htmlContainer: 'swal-vc-html'
                            },
                            html: `
                                <div style="text-align:left;">
                                    <div class="vc-history-layout">
                                        <div class="vc-history-table-wrap">
                                            <table class="table table-bordered mb-0 vc-history-table">
                                                <thead>
                                                    <tr>
                                                        <th>Version</th>
                                                        <th>Type</th>
                                                        <th>Edited Date</th>
                                                        <th>View</th>
                                                        <th>Restore</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${renderHistoryRows(contentId, response.versions)}
                                                </tbody>
                                            </table>
                                        </div>

                                        <div id="history-version-preview" class="vc-history-preview">
                                            <div class="vc-history-preview-head">
                                                <div id="history-preview-version" class="vc-history-preview-version">
                                                    ${previewVersion ? `Version ${previewVersion.version_number}` : 'No Preview'}
                                                </div>
                                                <div id="history-preview-title" class="vc-history-preview-title">
                                                    ${previewVersion ? escapeHtml(normalizePreviewContent(previewVersion.title)) : 'Select a version to preview it here.'}
                                                </div>
                                            </div>
                                            <div id="history-preview-content" class="vc-history-preview-content">
                                                ${previewVersion ? escapeHtml(normalizePreviewContent(previewVersion.content)) : 'Select a version to preview it here.'}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `,
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: 'Close',
                            didOpen: () => {
                                feather.replace();
                            }
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Could not load version history.'
                    });
                }
            });
        });

        $(document).on('click', '.view-history-version-btn', function() {
            let title = $(this).data('title');
            let content = $(this).data('content');
            let version = $(this).data('version');

            $('#history-preview-version').text(`Version ${version}`);
            $('#history-preview-title').text(normalizePreviewContent(title));
            $('#history-preview-content').text(normalizePreviewContent(content));
        });

        $(document).on('click', '.restore-history-version-btn', function() {
            let contentId = $(this).data('content-id');
            let versionId = $(this).data('version-id');

            Swal.fire({
                title: 'Restore This Version?',
                text: 'The current content will be preserved as a new history version before restore.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/version-control/${contentId}/restore/${versionId}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Restored!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message || 'Restore failed!'
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

        .swal2-popup.swal-vc-popup {
            padding: 1.1rem 1.25rem 1.25rem;
        }

        .swal2-title.swal-vc-title {
            margin: 0;
            padding: 0;
        }

        .swal2-html-container.swal-vc-html {
            margin-top: 0.65rem;
            padding-top: 0;
        }

        .vc-history-layout {
            display: grid;
            grid-template-columns: minmax(520px, 1.1fr) minmax(360px, 0.9fr);
            gap: 18px;
            align-items: start;
        }

        .vc-history-table-wrap {
            overflow-x: auto;
            border: 1px solid #dbe3ee;
            border-radius: 16px;
            background: #fff;
        }

        .vc-history-table {
            table-layout: fixed;
            margin-bottom: 0;
        }

        .vc-history-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            vertical-align: middle;
            white-space: nowrap;
        }

        .vc-history-table td {
            vertical-align: middle;
            font-size: 14px;
            color: #334155;
        }

        .vc-history-version-cell {
            width: 72px;
            font-weight: 700;
            text-align: center;
        }

        .vc-history-type-cell {
            width: 110px;
            font-weight: 600;
            line-height: 1.3;
        }

        .vc-history-date-cell {
            width: 170px;
            line-height: 1.35;
        }

        .vc-history-action-cell {
            width: 92px;
            text-align: center;
        }

        .vc-history-action-btn {
            min-width: 74px;
            padding: 0.42rem 0.75rem;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .vc-history-preview {
            border: 1px solid #dbe3ee;
            border-radius: 16px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        }

        .vc-history-preview-head {
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
            background: linear-gradient(180deg, #f8fbff 0%, #f8fafc 100%);
        }

        .vc-history-preview-version {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }

        .vc-history-preview-title {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .vc-history-preview-content {
            padding: 16px;
            /* white-space: pre-wrap; */
            line-height: 1.7;
            color: #1e293b;
            font-size: 14px;
            min-height: 220px;
            max-height: 360px;
            overflow-y: auto;
            display: block;
            text-align: left;
        }

        @media (max-width: 900px) {
            .vc-history-layout {
                grid-template-columns: 1fr;
            }
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
