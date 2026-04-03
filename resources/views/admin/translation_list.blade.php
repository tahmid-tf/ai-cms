@extends('layouts.admin')

@section('content')
    <main>
        <div class="lp-topbar">
            <div class="lp-topbar-left">
                <div class="lp-icon-wrap">
                    <i data-feather="languages"></i>
                </div>
                <div>
                    <div class="lp-title">View Translations</div>
                    <div class="lp-sub">Manage translated content records across supported languages</div>
                </div>
            </div>
            <div class="lp-badges">
                <span class="lp-badge">Translation Records</span>
            </div>
        </div>

        <div class="container-xl px-4 mt-4">
            <div class="card">
                <div class="card-body">
                    <table id="translationsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Target Language</th>
                                <th>Source Language</th>
                                <th>Original Content</th>
                                <th>Translated Content</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($translations as $translation)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucfirst($translation->target_language) }}</td>
                                    <td>{{ $translation->source_language ?: 'Auto Detect' }}</td>
                                    <td>{{ Str::limit($translation->original_content, 50, '...') }}</td>
                                    <td>{{ Str::limit($translation->translated_content, 50, '...') }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-primary view-translation-btn"
                                            data-original="{{ htmlspecialchars($translation->original_content, ENT_QUOTES) }}"
                                            data-translated="{{ htmlspecialchars($translation->translated_content, ENT_QUOTES) }}"
                                            data-source="{{ htmlspecialchars($translation->source_language ?? 'Auto Detect', ENT_QUOTES) }}"
                                            data-target="{{ $translation->target_language }}" title="View">
                                            <i data-feather="eye"></i>
                                        </button>
                                        <button class="btn btn-xs btn-danger delete-translation-btn"
                                            data-id="{{ $translation->id }}" title="Delete">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                        <button class="btn btn-xs btn-warning edit-translation-btn"
                                            data-id="{{ $translation->id }}"
                                            data-original="{{ htmlspecialchars($translation->original_content, ENT_QUOTES) }}"
                                            data-translated="{{ htmlspecialchars($translation->translated_content, ENT_QUOTES) }}"
                                            data-source="{{ htmlspecialchars($translation->source_language ?? '', ENT_QUOTES) }}"
                                            data-target="{{ $translation->target_language }}" title="Edit">
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

        function formatLanguageLabel(language) {
            if (!language) {
                return 'Auto Detect';
            }

            return language.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        }

        function buildTranslationActionButtons(translation) {
            return `
                <button class="btn btn-xs btn-primary view-translation-btn"
                    data-original="${escapeHtml(translation.original_content)}"
                    data-translated="${escapeHtml(translation.translated_content)}"
                    data-source="${escapeHtml(translation.source_language || 'Auto Detect')}"
                    data-target="${escapeHtml(translation.target_language)}"
                    title="View">
                    <i data-feather="eye"></i>
                </button>
                <button class="btn btn-xs btn-danger delete-translation-btn"
                    data-id="${translation.id}" title="Delete">
                    <i data-feather="trash-2"></i>
                </button>
                <button class="btn btn-xs btn-warning edit-translation-btn"
                    data-id="${translation.id}"
                    data-original="${escapeHtml(translation.original_content)}"
                    data-translated="${escapeHtml(translation.translated_content)}"
                    data-source="${escapeHtml(translation.source_language || '')}"
                    data-target="${escapeHtml(translation.target_language)}"
                    title="Edit">
                    <i data-feather="edit"></i>
                </button>
            `;
        }

        $(document).ready(function() {
            $('#translationsTable').DataTable();

            $(document).on('click', '.view-translation-btn', function() {
                let originalContent = $(this).data('original');
                let translatedContent = $(this).data('translated');
                let sourceLanguage = $(this).data('source');
                let targetLanguage = $(this).data('target');

                Swal.fire({
                    title: `<span>${formatLanguageLabel(targetLanguage)} Translation</span>`,
                    html: `
                        <div style="text-align:left; max-height:440px; overflow-y:auto;">
                            <div class="mb-3">
                                <span class="badge bg-light text-dark me-2">Source: ${sourceLanguage}</span>
                                <span class="badge bg-info text-dark">Target: ${formatLanguageLabel(targetLanguage)}</span>
                            </div>
                            <label class="fw-bold mb-2 d-block">Original Content</label>
                            <div class="border rounded p-3 mb-3 bg-light">${originalContent}</div>
                            <label class="fw-bold mb-2 d-block">Translated Content</label>
                            <div class="border rounded p-3 bg-light">${translatedContent}</div>
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
        $(document).on('click', '.delete-translation-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');

            Swal.fire({
                title: 'Delete Translation',
                text: 'Are you sure you want to delete this translation?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/translations/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#translationsTable').DataTable().row(row).remove().draw();

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
        $(document).on('click', '.edit-translation-btn', function() {
            let button = $(this);
            let id = button.data('id');
            let row = button.closest('tr');

            let originalContent = button.data('original');
            let translatedContent = button.data('translated');
            let sourceLanguage = button.data('source');
            let targetLanguage = button.data('target');

            Swal.fire({
                title: `
                    <div style="display:flex;align-items:center;gap:12px;text-align:left;">
                        <div style="width:42px;height:42px;border-radius:12px;background:#ecfeff;display:flex;align-items:center;justify-content:center;color:#0f766e;">
                            <i data-feather="languages" style="width:20px;height:20px;"></i>
                        </div>
                        <div>
                            <div style="font-size:18px;font-weight:700;color:#0f172a;">Edit Translation Record</div>
                            <div style="font-size:12px;color:#64748b;font-weight:500;">Refine the saved source and translated copy</div>
                        </div>
                    </div>
                `,
                width: '980px',
                customClass: {
                    popup: 'swal-translation-record-popup',
                    title: 'swal-translation-record-title',
                    htmlContainer: 'swal-translation-record-html'
                },
                html: `
                    <div style="text-align:left;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
                            <div style="padding:14px 16px;border:1px solid #d2f1ee;border-radius:14px;background:linear-gradient(180deg,#f6fffe 0%,#eefcfb 100%);">
                                <label style="display:block;margin-bottom:8px;font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#475569;">Source Language</label>
                                <input id="swal-source-language" class="swal2-input" placeholder="Optional, e.g. English" value="${sourceLanguage}" style="margin:0;width:100%;height:46px;border:1px solid #cbd5e1;border-radius:12px;background:#fff;padding:0 14px;">
                            </div>
                            <div style="padding:14px 16px;border:1px solid #d2f1ee;border-radius:14px;background:linear-gradient(180deg,#f6fffe 0%,#eefcfb 100%);">
                                <label style="display:block;margin-bottom:8px;font-size:12px;font-weight:700;letter-spacing:.04em;text-transform:uppercase;color:#475569;">Target Language</label>
                                <select id="swal-target-language" class="swal2-select" style="margin:0;width:100%;height:46px;border:1px solid #cbd5e1;border-radius:12px;background:#fff;color:#0f172a;font-size:14px;font-weight:600;padding:0 14px;">
                                    <option value="bangla" ${targetLanguage === 'bangla' ? 'selected' : ''}>Bangla</option>
                                    <option value="english" ${targetLanguage === 'english' ? 'selected' : ''}>English</option>
                                    <option value="hindi" ${targetLanguage === 'hindi' ? 'selected' : ''}>Hindi</option>
                                    <option value="arabic" ${targetLanguage === 'arabic' ? 'selected' : ''}>Arabic</option>
                                    <option value="spanish" ${targetLanguage === 'spanish' ? 'selected' : ''}>Spanish</option>
                                </select>
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                            <div style="border:1px solid #e2e8f0;border-radius:16px;background:#ffffff;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.05);">
                                <div style="padding:12px 16px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
                                    <div style="font-size:13px;font-weight:700;color:#0f172a;">Original Content</div>
                                    <div style="font-size:12px;color:#64748b;">Source text before translation</div>
                                </div>
                                <div style="padding:14px;">
                                    <textarea id="swal-original-translation-content" class="swal2-textarea" style="margin:0;width:100%;height:220px;border:1px solid #dbe3ee;border-radius:12px;background:#fbfdff;color:#1e293b;font-size:14px;line-height:1.65;padding:14px 16px;">${originalContent}</textarea>
                                </div>
                            </div>

                            <div style="border:1px solid #d2f1ee;border-radius:16px;background:#ffffff;overflow:hidden;box-shadow:0 10px 30px rgba(15,118,110,.08);">
                                <div style="padding:12px 16px;border-bottom:1px solid #d2f1ee;background:linear-gradient(180deg,#f6fffe 0%,#eefcfb 100%);">
                                    <div style="font-size:13px;font-weight:700;color:#0f172a;">Translated Content</div>
                                    <div style="font-size:12px;color:#64748b;">Final translated version</div>
                                </div>
                                <div style="padding:14px;">
                                    <textarea id="swal-translated-content" class="swal2-textarea" style="margin:0;width:100%;height:220px;border:1px solid #bde7e3;border-radius:12px;background:#ffffff;color:#0f172a;font-size:14px;line-height:1.65;padding:14px 16px;">${translatedContent}</textarea>
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
                    let updatedSourceLanguage = $('#swal-source-language').val();
                    let updatedTargetLanguage = $('#swal-target-language').val();
                    let updatedOriginalContent = $('#swal-original-translation-content').val();
                    let updatedTranslatedContent = $('#swal-translated-content').val();

                    if (!updatedTargetLanguage || !updatedOriginalContent || !updatedTranslatedContent) {
                        Swal.showValidationMessage('Target language, original content, and translated content are required');
                        return false;
                    }

                    return {
                        source_language: updatedSourceLanguage,
                        target_language: updatedTargetLanguage,
                        original_content: updatedOriginalContent,
                        translated_content: updatedTranslatedContent
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/translations/${id}`,
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}',
                            source_language: result.value.source_language,
                            target_language: result.value.target_language,
                            original_content: result.value.original_content,
                            translated_content: result.value.translated_content
                        },
                        success: function(response) {
                            if (response.success) {
                                let updated = response.data;
                                let table = $('#translationsTable').DataTable();

                                table.row(row).data([
                                    row.find('td:eq(0)').text(),
                                    formatLanguageLabel(updated.target_language),
                                    updated.source_language || 'Auto Detect',
                                    getPreviewText(updated.original_content),
                                    getPreviewText(updated.translated_content),
                                    buildTranslationActionButtons(updated)
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

        .swal2-popup.swal-translation-record-popup {
            padding: 1.1rem 1.25rem 1.25rem;
        }

        .swal2-title.swal-translation-record-title {
            margin: 0;
            padding: 0;
        }

        .swal2-html-container.swal-translation-record-html {
            margin-top: 0.65rem;
            padding-top: 0;
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
