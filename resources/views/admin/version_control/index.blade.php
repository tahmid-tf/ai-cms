@extends('layouts.admin')

@section('content')
    <div class="vc-topbar">
        <div class="vc-topbar-left">
            <div class="vc-icon-wrap">
                <i data-feather="git-branch"></i>
            </div>
            <div>
                <div class="vc-title">Version Control & Drafts</div>
                <div class="vc-sub">Create draft or published content with automatic version tracking</div>
            </div>
        </div>
        <div class="vc-badge">Planning Ready</div>
    </div>

    <div class="vc-page">
        <div class="vc-side">
            <div class="vc-card">
                <div class="vc-card-head">
                    <i data-feather="layers"></i>
                    <span>Status</span>
                </div>
                <div class="vc-card-body">
                    <label class="vc-label">Publish Status</label>
                    <select id="content-status" class="vc-select">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>

                    <div class="vc-note">
                        <strong>Draft</strong> keeps the content editable and unpublished.<br>
                        <strong>Published</strong> marks it as the current final version.
                    </div>
                </div>
            </div>

            <div class="vc-card">
                <div class="vc-card-head">
                    <i data-feather="bar-chart-2"></i>
                    <span>Stats</span>
                </div>
                <div class="vc-card-body">
                    <div class="vc-stat"><span>Words</span><strong id="vc-words">0</strong></div>
                    <div class="vc-stat"><span>Characters</span><strong id="vc-chars">0</strong></div>
                    <div class="vc-stat"><span>Status</span><strong id="vc-status-label">Draft</strong></div>
                </div>
            </div>
        </div>

        <div class="vc-main">
            <div class="vc-form-card">
                <div class="vc-field">
                    <label class="vc-label">Title</label>
                    <input type="text" id="content-title" class="vc-input" placeholder="Enter a content title">
                </div>

                <div class="vc-field">
                    <label class="vc-label">Content</label>
                    <textarea id="content-body" class="vc-textarea"
                        placeholder="Write your content here. Each save will create a new version snapshot."></textarea>
                </div>

                <div class="vc-actions">
                    <button type="button" id="clear-content-btn" class="vc-btn vc-btn-light">
                        <i data-feather="trash-2"></i>
                        Clear
                    </button>
                    <button type="button" id="save-content-btn" class="vc-btn vc-btn-primary">
                        <i data-feather="save"></i>
                        Save Content
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function updateStats() {
                const content = $('#content-body').val().trim();
                const words = content ? content.split(/\s+/).filter(Boolean).length : 0;

                $('#vc-words').text(words);
                $('#vc-chars').text(content.length);
                $('#vc-status-label').text($('#content-status').val() === 'published' ? 'Published' : 'Draft');
            }

            $('#content-body, #content-title').on('input', updateStats);
            $('#content-status').on('change', updateStats);

            $('#clear-content-btn').on('click', function() {
                $('#content-title').val('');
                $('#content-body').val('');
                $('#content-status').val('draft');
                updateStats();
            });

            $('#save-content-btn').on('click', function() {
                const button = $(this);

                $.ajax({
                    url: '{{ route('version_control.store') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        title: $('#content-title').val(),
                        content: $('#content-body').val(),
                        status: $('#content-status').val()
                    },
                    beforeSend: function() {
                        button.prop('disabled', true).html('<i data-feather="loader"></i> Saving...');
                        feather.replace();
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved',
                            text: response.message,
                            confirmButtonColor: '#1d4ed8'
                        }).then(() => {
                            $('#content-title').val('');
                            $('#content-body').val('');
                            $('#content-status').val('draft');
                            updateStats();
                        });
                    },
                    error: function(xhr) {
                        let message = 'Something went wrong.';

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Save Failed',
                            html: message,
                            confirmButtonColor: '#1d4ed8'
                        });
                    },
                    complete: function() {
                        button.prop('disabled', false).html('<i data-feather="save"></i> Save Content');
                        feather.replace();
                    }
                });
            });

            updateStats();
            feather.replace();
        });
    </script>

    <style>
        :root {
            --vc-bg: #f4f7fb;
            --vc-surface: #ffffff;
            --vc-border: #dfe7f1;
            --vc-text: #102033;
            --vc-muted: #6b7b93;
            --vc-accent: #1d4ed8;
            --vc-accent-soft: #e7efff;
            --vc-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
        }

        .vc-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 2rem;
            background: linear-gradient(90deg, #ffffff 0%, #eef4ff 100%);
            border-bottom: 1px solid var(--vc-border);
        }

        .vc-topbar-left {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .vc-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--vc-accent);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(29, 78, 216, 0.22);
        }

        .vc-icon-wrap svg {
            width: 18px;
            height: 18px;
        }

        .vc-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--vc-text);
        }

        .vc-sub {
            font-size: 0.76rem;
            color: var(--vc-muted);
        }

        .vc-badge {
            padding: 0.35rem 0.8rem;
            border-radius: 999px;
            background: var(--vc-accent-soft);
            color: var(--vc-accent);
            font-weight: 700;
            font-size: 0.75rem;
        }

        .vc-page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 2rem 1.5rem 3rem;
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 1.25rem;
        }

        .vc-side,
        .vc-main {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .vc-card,
        .vc-form-card {
            background: var(--vc-surface);
            border: 1px solid var(--vc-border);
            border-radius: 18px;
            box-shadow: var(--vc-shadow);
        }

        .vc-card-head {
            padding: 1rem 1.1rem;
            border-bottom: 1px solid var(--vc-border);
            display: flex;
            align-items: center;
            gap: 0.55rem;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #3b4b63;
        }

        .vc-card-head svg {
            width: 15px;
            height: 15px;
            color: var(--vc-accent);
        }

        .vc-card-body,
        .vc-form-card {
            padding: 1.1rem;
        }

        .vc-label {
            display: block;
            margin-bottom: 0.45rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
        }

        .vc-input,
        .vc-select,
        .vc-textarea {
            width: 100%;
            border: 1.5px solid var(--vc-border);
            border-radius: 12px;
            background: #fbfdff;
            color: var(--vc-text);
            transition: border-color .15s, box-shadow .15s;
        }

        .vc-input,
        .vc-select {
            height: 46px;
            padding: 0 0.9rem;
        }

        .vc-textarea {
            min-height: 420px;
            padding: 1rem 1.1rem;
            resize: vertical;
            line-height: 1.7;
        }

        .vc-input:focus,
        .vc-select:focus,
        .vc-textarea:focus {
            outline: none;
            border-color: var(--vc-accent);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        .vc-note {
            margin-top: 0.9rem;
            padding: 0.9rem;
            border-radius: 12px;
            background: #f8fbff;
            color: var(--vc-muted);
            font-size: 0.78rem;
            line-height: 1.6;
        }

        .vc-stat {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.55rem 0;
            border-bottom: 1px solid var(--vc-border);
            color: var(--vc-muted);
            font-size: 0.8rem;
        }

        .vc-stat:last-child {
            border-bottom: none;
        }

        .vc-stat strong {
            color: var(--vc-text);
        }

        .vc-field + .vc-field {
            margin-top: 1rem;
        }

        .vc-actions {
            margin-top: 1rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.8rem;
        }

        .vc-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border-radius: 10px;
            padding: 0.72rem 1.2rem;
            font-weight: 700;
            border: none;
        }

        .vc-btn svg {
            width: 15px;
            height: 15px;
        }

        .vc-btn-light {
            background: #eef2f7;
            color: #475569;
        }

        .vc-btn-primary {
            background: var(--vc-accent);
            color: #fff;
        }

        @media (max-width: 992px) {
            .vc-page {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .vc-topbar {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .vc-actions {
                flex-direction: column;
            }
        }
    </style>
@endpush
