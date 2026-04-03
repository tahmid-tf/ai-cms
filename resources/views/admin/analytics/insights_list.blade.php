@extends('layouts.admin')

@section('content')
    <main>
        <div class="lp-topbar">
            <div class="lp-topbar-left">
                <div class="lp-icon-wrap">
                    <i data-feather="message-square"></i>
                </div>
                <div>
                    <div class="lp-title">Insights List</div>
                    <div class="lp-sub">Review and refine stored AI recommendations from analytics activity</div>
                </div>
            </div>
            <div class="lp-badges">
                <span class="lp-badge">Insight Archive</span>
            </div>
        </div>

        <div class="container-xl px-4 mt-4">
            <div class="card">
                <div class="card-body">
                    <table id="insightsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Content</th>
                                <th>Insight</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($insights as $insight)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $insight->content_title }}</td>
                                    <td>{{ Str::limit($insight->insight_text, 70, '...') }}</td>
                                    <td>{{ $insight->created_at->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-primary view-insight-btn"
                                            data-content-title="{{ htmlspecialchars($insight->content_title, ENT_QUOTES) }}"
                                            data-insight="{{ htmlspecialchars($insight->insight_text, ENT_QUOTES) }}"
                                            title="View">
                                            <i data-feather="eye"></i>
                                        </button>
                                        <button class="btn btn-xs btn-warning edit-insight-btn"
                                            data-id="{{ $insight->id }}"
                                            data-content-title="{{ htmlspecialchars($insight->content_title, ENT_QUOTES) }}"
                                            data-insight="{{ htmlspecialchars($insight->insight_text, ENT_QUOTES) }}"
                                            title="Edit">
                                            <i data-feather="edit"></i>
                                        </button>
                                        <button class="btn btn-xs btn-danger delete-insight-btn"
                                            data-id="{{ $insight->id }}" title="Delete">
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

        function getPreviewText(value, limit = 70) {
            return value.length > limit ? value.substring(0, limit) + '...' : value;
        }

        function buildInsightActionButtons(insight) {
            return `
                <button class="btn btn-xs btn-primary view-insight-btn"
                    data-content-title="${escapeHtml(insight.content_title)}"
                    data-insight="${escapeHtml(insight.insight_text)}"
                    title="View">
                    <i data-feather="eye"></i>
                </button>
                <button class="btn btn-xs btn-warning edit-insight-btn"
                    data-id="${insight.id}"
                    data-content-title="${escapeHtml(insight.content_title)}"
                    data-insight="${escapeHtml(insight.insight_text)}"
                    title="Edit">
                    <i data-feather="edit"></i>
                </button>
                <button class="btn btn-xs btn-danger delete-insight-btn"
                    data-id="${insight.id}" title="Delete">
                    <i data-feather="trash-2"></i>
                </button>
            `;
        }

        $(document).ready(function() {
            $('#insightsTable').DataTable();

            $(document).on('click', '.view-insight-btn', function() {
                let contentTitle = $(this).data('content-title');
                let insight = $(this).data('insight');

                Swal.fire({
                    title: contentTitle,
                    html: `
                        <div style="text-align:left;">
                            <div style="font-size:12px;color:#64748b;margin-bottom:10px;">Stored Insight</div>
                            <div style="padding:14px 16px;border:1px solid #dbe7ff;border-radius:14px;background:#f8fbff;color:#0f172a;line-height:1.7;">
                                ${insight}
                            </div>
                        </div>
                    `,
                    width: '760px',
                    confirmButtonText: 'Close'
                });
            });
        });
    </script>

    <script>
        $(document).on('click', '.delete-insight-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');

            Swal.fire({
                title: 'Delete Insight',
                text: 'Are you sure you want to delete this insight?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/analytics-insights/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#insightsTable').DataTable().row(row).remove().draw();

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
        $(document).on('click', '.edit-insight-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');
            let contentTitle = button.data('content-title');
            let insight = button.data('insight');

            Swal.fire({
                title: `
                    <div style="display:flex;align-items:center;gap:12px;text-align:left;">
                        <div style="width:42px;height:42px;border-radius:12px;background:#eef4ff;display:flex;align-items:center;justify-content:center;color:#2563eb;">
                            <i data-feather="message-square" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div style="font-size:18px;font-weight:700;color:#0f172a;">Edit Insight</div>
                            <div style="font-size:12px;color:#64748b;font-weight:500;">${contentTitle}</div>
                        </div>
                    </div>
                `,
                width: '820px',
                html: `
                    <div style="text-align:left;">
                        <label style="display:block;margin-bottom:8px;font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#475569;">Insight Text</label>
                        <textarea id="swal-insight-text" class="swal2-textarea" style="margin:0;width:100%;height:220px;border:1px solid #dbe3ee;border-radius:12px;background:#fbfdff;color:#1e293b;font-size:14px;line-height:1.7;padding:14px 16px;">${insight}</textarea>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                didOpen: () => {
                    feather.replace();
                },
                preConfirm: () => {
                    let updatedInsight = $('#swal-insight-text').val();

                    if (!updatedInsight) {
                        Swal.showValidationMessage('Insight text is required');
                        return false;
                    }

                    return {
                        insight_text: updatedInsight
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/analytics-insights/${id}`,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            insight_text: result.value.insight_text
                        },
                        success: function(response) {
                            if (response.success) {
                                let updated = response.data;
                                let table = $('#insightsTable').DataTable();

                                table.row(row).data([
                                    row.find('td:eq(0)').text(),
                                    updated.content_title,
                                    getPreviewText(updated.insight_text),
                                    row.find('td:eq(3)').text(),
                                    buildInsightActionButtons(updated)
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
