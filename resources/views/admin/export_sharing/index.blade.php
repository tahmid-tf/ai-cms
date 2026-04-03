@extends('layouts.admin')

@section('content')
    @php
        $user = auth()->user();
        $canEditRecords = $user?->hasAnyRole(['admin', 'editor']);
        $canManageExports = $user?->hasRole('admin');
    @endphp
    <main>
        <div class="es-topbar">
            <div class="es-topbar-left">
                <div class="es-icon-wrap">
                    <i data-feather="share-2"></i>
                </div>
                <div>
                    <div class="es-title">Export & Sharing</div>
                    <div class="es-sub">Export content as PDF or Word and share published links externally</div>
                </div>
            </div>
            <div class="es-badges">
                <span class="es-badge">Share Ready</span>
            </div>
        </div>

        <div class="container-xl px-4 mt-4">
            <div class="card">
                <div class="card-body">
                    <table id="exportSharingTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Updated</th>
                                <th>{{ $canEditRecords || $canManageExports ? 'Action' : 'View' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contents as $content)
                                @php
                                    $slug = \Illuminate\Support\Str::slug($content->title) . '-' . $content->id;
                                    $publicUrl = route('content.public', ['slug' => $slug]);
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $content->title }}</td>
                                    <td>
                                        <span class="badge {{ $content->status === 'published' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            {{ ucfirst($content->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $content->updated_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-primary view-export-content-btn"
                                            data-title="{{ htmlspecialchars($content->title, ENT_QUOTES) }}"
                                            data-status="{{ $content->status }}"
                                            data-content="{{ htmlspecialchars($content->content, ENT_QUOTES) }}"
                                            title="View">
                                            <i data-feather="eye"></i>
                                        </button>
                                        @if ($canEditRecords)
                                            <button class="btn btn-xs btn-warning edit-export-content-btn"
                                                data-id="{{ $content->id }}"
                                                data-title="{{ htmlspecialchars($content->title, ENT_QUOTES) }}"
                                                data-status="{{ $content->status }}"
                                                data-content="{{ htmlspecialchars($content->content, ENT_QUOTES) }}"
                                                title="Edit">
                                                <i data-feather="edit"></i>
                                            </button>
                                        @endif
                                        @if ($canManageExports)
                                            <button class="btn btn-xs btn-danger delete-export-content-btn"
                                                data-id="{{ $content->id }}" title="Delete">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                            <a class="btn btn-xs btn-outline-danger"
                                                href="{{ route('export_sharing.pdf', $content->id) }}" title="Export PDF">
                                                PDF
                                            </a>
                                            <a class="btn btn-xs btn-outline-primary"
                                                href="{{ route('export_sharing.word', $content->id) }}" title="Export Word">
                                                Word
                                            </a>
                                            <button class="btn btn-xs btn-outline-success share-export-content-btn"
                                                data-title="{{ htmlspecialchars($content->title, ENT_QUOTES) }}"
                                                data-status="{{ $content->status }}"
                                                data-url="{{ $publicUrl }}"
                                                title="Share">
                                                Share
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

        function formatStatusBadge(status) {
            let badgeClass = status === 'published' ? 'bg-success' : 'bg-warning text-dark';
            return `<span class="badge ${badgeClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
        }

        function buildExportSharingActions(content, publicUrl) {
            return `
                <button class="btn btn-xs btn-primary view-export-content-btn"
                    data-title="${escapeHtml(content.title)}"
                    data-status="${escapeHtml(content.status)}"
                    data-content="${escapeHtml(content.content)}"
                    title="View">
                    <i data-feather="eye"></i>
                </button>
                @if ($canEditRecords)
                    <button class="btn btn-xs btn-warning edit-export-content-btn"
                        data-id="${content.id}"
                        data-title="${escapeHtml(content.title)}"
                        data-status="${escapeHtml(content.status)}"
                        data-content="${escapeHtml(content.content)}"
                        title="Edit">
                        <i data-feather="edit"></i>
                    </button>
                @endif
                @if ($canManageExports)
                    <button class="btn btn-xs btn-danger delete-export-content-btn"
                        data-id="${content.id}" title="Delete">
                        <i data-feather="trash-2"></i>
                    </button>
                    <a class="btn btn-xs btn-outline-danger" href="/export-sharing/${content.id}/pdf" title="Export PDF">PDF</a>
                    <a class="btn btn-xs btn-outline-primary" href="/export-sharing/${content.id}/word" title="Export Word">Word</a>
                    <button class="btn btn-xs btn-outline-success share-export-content-btn"
                        data-title="${escapeHtml(content.title)}"
                        data-status="${escapeHtml(content.status)}"
                        data-url="${escapeHtml(publicUrl)}"
                        title="Share">
                        Share
                    </button>
                @endif
            `;
        }

        $(document).ready(function() {
            $('#exportSharingTable').DataTable();

            $(document).on('click', '.view-export-content-btn', function() {
                let title = $(this).data('title');
                let status = $(this).data('status');
                let content = $(this).data('content');

                Swal.fire({
                    title: title,
                    html: `
                        <div style="text-align:left;max-height:420px;overflow-y:auto;">
                            <div class="mb-3">
                                <span class="badge ${status === 'published' ? 'bg-success' : 'bg-warning text-dark'}">${status}</span>
                            </div>
                            <div class="border rounded p-3 bg-light" style="white-space:pre-wrap;">${content}</div>
                        </div>
                    `,
                    width: '900px',
                    showCancelButton: true,
                    showConfirmButton: false,
                    cancelButtonText: 'Close'
                });
            });

            $(document).on('click', '.share-export-content-btn', function() {
                let title = $(this).data('title');
                let status = $(this).data('status');
                let url = $(this).data('url');

                if (status !== 'published') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Draft Content',
                        text: 'Only published content is available for public sharing.',
                        confirmButtonColor: '#16a34a'
                    });
                    return;
                }

                let encodedUrl = encodeURIComponent(url);
                let encodedText = encodeURIComponent(title);

                Swal.fire({
                    title: 'Share Content',
                    width: '780px',
                    html: `
                        <div style="text-align:left;">
                            <div style="padding:14px 16px;border:1px solid #dbe7ff;border-radius:14px;background:#f8fbff;margin-bottom:16px;">
                                <div style="font-size:12px;color:#64748b;margin-bottom:6px;">Public Share Link</div>
                                <div style="font-size:14px;color:#0f172a;word-break:break-all;">${url}</div>
                            </div>
                            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                                <button class="btn btn-primary" id="copy-share-link-btn" data-url="${url}">Copy Link</button>
                                <a class="btn btn-outline-primary" href="https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}" target="_blank">Facebook</a>
                                <a class="btn btn-outline-info" href="https://twitter.com/intent/tweet?text=${encodedText}&url=${encodedUrl}" target="_blank">Twitter (X)</a>
                                <a class="btn btn-outline-secondary" href="https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}" target="_blank">LinkedIn</a>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    showConfirmButton: false,
                    cancelButtonText: 'Close'
                });
            });

            $(document).on('click', '#copy-share-link-btn', function() {
                let url = $(this).data('url');

                navigator.clipboard.writeText(url).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Copied!',
                        text: 'Share link copied to clipboard.',
                        timer: 1200,
                        showConfirmButton: false
                    });
                });
            });
        });
    </script>

    <script>
        $(document).on('click', '.delete-export-content-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');

            Swal.fire({
                title: 'Delete Content',
                text: 'Are you sure you want to delete this content?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/export-sharing/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#exportSharingTable').DataTable().row(row).remove().draw();
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
        $(document).on('click', '.edit-export-content-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');
            let title = button.data('title');
            let status = button.data('status');
            let content = button.data('content');

            Swal.fire({
                title: `
                    <div style="display:flex;align-items:center;gap:12px;text-align:left;">
                        <div style="width:42px;height:42px;border-radius:12px;background:#eef4ff;display:flex;align-items:center;justify-content:center;color:#2563eb;">
                            <i data-feather="edit-3" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div style="font-size:18px;font-weight:700;color:#0f172a;">Edit Exportable Content</div>
                            <div style="font-size:12px;color:#64748b;font-weight:500;">Changes stay versioned and share-ready</div>
                        </div>
                    </div>
                `,
                width: '980px',
                html: `
                    <div style="text-align:left;">
                        <div style="display:grid;grid-template-columns:2fr 1fr;gap:14px;margin-bottom:18px;">
                            <div style="padding:14px 16px;border:1px solid #dbe7ff;border-radius:14px;background:linear-gradient(180deg,#f8fbff 0%,#f1f6ff 100%);">
                                <label style="display:block;margin-bottom:8px;font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#475569;">Title</label>
                                <input id="swal-export-title" class="swal2-input" value="${title}" style="margin:0;width:100%;height:46px;border:1px solid #cbd5e1;border-radius:12px;background:#fff;padding:0 14px;">
                            </div>
                            <div style="padding:14px 16px;border:1px solid #dbe7ff;border-radius:14px;background:linear-gradient(180deg,#f8fbff 0%,#f1f6ff 100%);">
                                <label style="display:block;margin-bottom:8px;font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#475569;">Status</label>
                                <select id="swal-export-status" class="swal2-select" style="margin:0;width:100%;height:46px;border:1px solid #cbd5e1;border-radius:12px;background:#fff;padding:0 14px;">
                                    <option value="draft" ${status === 'draft' ? 'selected' : ''}>Draft</option>
                                    <option value="published" ${status === 'published' ? 'selected' : ''}>Published</option>
                                </select>
                            </div>
                        </div>
                        <textarea id="swal-export-content" class="swal2-textarea" style="margin:0;width:100%;height:260px;border:1px solid #dbe3ee;border-radius:12px;background:#fbfdff;color:#1e293b;font-size:14px;line-height:1.7;padding:14px 16px;">${content}</textarea>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                didOpen: () => {
                    feather.replace();
                },
                preConfirm: () => {
                    let updatedTitle = $('#swal-export-title').val();
                    let updatedStatus = $('#swal-export-status').val();
                    let updatedContent = $('#swal-export-content').val();

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
                        url: `/export-sharing/${id}`,
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
                                let slug = `${updated.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '')}-${updated.id}`;
                                let publicUrl = new URL(`/content/${slug}`, window.location.origin).href;
                                let table = $('#exportSharingTable').DataTable();

                                table.row(row).data([
                                    row.find('td:eq(0)').text(),
                                    updated.title,
                                    formatStatusBadge(updated.status),
                                    new Date(updated.updated_at).toLocaleString(),
                                    buildExportSharingActions(updated, publicUrl)
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
        .es-topbar {
            background: linear-gradient(90deg, #ffffff 0%, #f1fbfa 100%);
            border-bottom: 1px solid #d8e4e4;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .es-topbar-left {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .es-icon-wrap {
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

        .es-icon-wrap svg {
            width: 18px;
            height: 18px;
        }

        .es-title {
            font-size: 1rem;
            font-weight: 700;
            color: #102a2a;
        }

        .es-sub {
            font-size: 0.76rem;
            color: #789090;
        }

        .es-badge {
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
            .es-topbar {
                padding-left: 1rem;
                padding-right: 1rem;
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush
