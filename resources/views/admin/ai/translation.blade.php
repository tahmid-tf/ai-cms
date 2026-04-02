@extends('layouts.admin')

@section('content')
    <div class="tr-topbar">
        <div class="tr-topbar-left">
            <div class="tr-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="M5 8l6 6" />
                    <path d="M4 14l6-6 2-3" />
                    <path d="M2 5h12" />
                    <path d="M7 2h1" />
                    <path d="M22 22l-5-10-5 10" />
                    <path d="M14 18h6" />
                </svg>
            </div>
            <div>
                <div class="tr-title">AI Content Translation</div>
                <div class="tr-sub">Translate content with polished, readable output</div>
            </div>
        </div>
        <div class="tr-badges">
            <span class="tr-badge">AI Powered</span>
        </div>
    </div>

    <div class="tr-page">
        <div class="tr-sidebar">
            <div class="tr-card">
                <div class="tr-card-header">
                    <div class="tr-card-header-icon">
                        <i data-feather="globe"></i>
                    </div>
                    <span class="tr-card-title">Language Settings</span>
                </div>
                <div class="tr-card-body">
                    <div class="mb-3">
                        <label class="tr-field-label">Translate To</label>
                        <select id="target-language" class="tr-select">
                            <option value="bangla">Bangla</option>
                            <option value="english">English</option>
                            <option value="hindi">Hindi</option>
                            <option value="arabic">Arabic</option>
                            <option value="spanish">Spanish</option>
                        </select>
                    </div>

                    <div class="mb-1">
                        <label class="tr-field-label">Source Language</label>
                        <input type="text" id="source-language" class="tr-input"
                            placeholder="Optional, e.g. English or Bangla">
                    </div>
                </div>
            </div>

            <div class="tr-card">
                <div class="tr-card-header">
                    <div class="tr-card-header-icon">
                        <i data-feather="bar-chart-2"></i>
                    </div>
                    <span class="tr-card-title">Stats</span>
                </div>
                <div class="tr-card-body">
                    <div class="tr-stat-row"><span class="tr-stat-label">Words (in)</span><span class="tr-stat-val"
                            id="tr-stat-words-in">0</span></div>
                    <div class="tr-stat-row"><span class="tr-stat-label">Chars (in)</span><span class="tr-stat-val"
                            id="tr-stat-chars-in">0</span></div>
                    <div class="tr-stat-row"><span class="tr-stat-label">Words (out)</span><span class="tr-stat-val"
                            id="tr-stat-words-out">-</span></div>
                    <div class="tr-stat-row"><span class="tr-stat-label">Chars (out)</span><span class="tr-stat-val"
                            id="tr-stat-chars-out">-</span></div>
                </div>
            </div>
        </div>

        <div class="tr-main">
            <div class="tr-editor-grid">
                <div class="tr-pane">
                    <div class="tr-pane-label">
                        Original Content
                        <button type="button" class="tr-pane-action" id="translation-clear-btn">
                            <i data-feather="trash-2"></i>
                            Clear
                        </button>
                    </div>
                    <textarea class="tr-textarea" id="translation-input-content"
                        placeholder="Paste or type the content you want to translate here."></textarea>
                </div>

                <div class="tr-pane">
                    <div class="tr-pane-label">
                        Translated Output
                        <button type="button" class="tr-pane-action tr-copy-btn" id="translation-copy-btn" disabled>
                            <i data-feather="copy"></i>
                            Copy
                        </button>
                    </div>
                    <textarea class="tr-textarea" id="translation-output-content" readonly
                        placeholder="Your translated content will appear here."></textarea>
                </div>
            </div>

            <div class="tr-action-bar">
                <div class="tr-action-left">
                    <div class="tr-selected-target">
                        Target: <span id="translation-active-target-label">Bangla</span>
                    </div>
                    <div class="tr-tip">
                        <i data-feather="info"></i>
                        Review translated text before publishing.
                    </div>
                </div>
                <div class="tr-action-right">
                    <button type="button" class="tr-btn-translate" id="translation-btn">
                        <i data-feather="languages"></i>
                        Translate Content
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function updateTranslationStats() {
                const inputText = $('#translation-input-content').val().trim();
                const outputText = $('#translation-output-content').val().trim();
                const inputWords = inputText ? inputText.split(/\s+/).filter(Boolean).length : 0;
                const outputWords = outputText ? outputText.split(/\s+/).filter(Boolean).length : 0;

                $('#tr-stat-words-in').text(inputWords);
                $('#tr-stat-chars-in').text(inputText.length);
                $('#tr-stat-words-out').text(outputText ? outputWords : '-');
                $('#tr-stat-chars-out').text(outputText ? outputText.length : '-');
            }

            function resetCopyButton() {
                $('#translation-copy-btn')
                    .prop('disabled', true)
                    .removeClass('copied')
                    .html('<i data-feather="copy"></i> Copy');
                feather.replace();
            }

            $('#translation-input-content').on('input', updateTranslationStats);

            $('#target-language').on('change', function() {
                const label = $(this).find('option:selected').text();
                $('#translation-active-target-label').text(label);
            });

            $('#translation-clear-btn').on('click', function() {
                $('#translation-input-content').val('');
                $('#translation-output-content').val('').removeClass('has-output loading');
                updateTranslationStats();
                resetCopyButton();
            });

            $('#translation-copy-btn').on('click', function() {
                const text = $('#translation-output-content').val();
                if (!text) {
                    return;
                }

                navigator.clipboard.writeText(text).then(() => {
                    const button = $(this);
                    button.addClass('copied').html('<i data-feather="check"></i> Copied!');
                    feather.replace();

                    setTimeout(() => {
                        button.removeClass('copied').html('<i data-feather="copy"></i> Copy');
                        feather.replace();
                    }, 2000);
                });
            });

            $('#translation-btn').on('click', function() {
                const button = $(this);
                const content = $('#translation-input-content').val();
                const targetLanguage = $('#target-language').val();
                const sourceLanguage = $('#source-language').val();

                if (!content.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Empty content',
                        text: 'Please enter some content to translate.',
                        confirmButtonColor: '#0f766e'
                    });
                    return;
                }

                button.prop('disabled', true).html('<i data-feather="loader"></i> Translating...');
                feather.replace();

                $('#translation-output-content').addClass('loading').removeClass('has-output').val('');
                resetCopyButton();

                $.ajax({
                    url: '{{ route('ai_translation.process') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        content: content,
                        target_language: targetLanguage,
                        source_language: sourceLanguage
                    },
                    success: function(response) {
                        const result = response.translated_content || '';

                        $('#translation-output-content')
                            .removeClass('loading')
                            .addClass('has-output')
                            .val(result);

                        updateTranslationStats();

                        $('#translation-copy-btn')
                            .prop('disabled', !result)
                            .html('<i data-feather="copy"></i> Copy');

                        feather.replace();
                    },
                    error: function(xhr) {
                        $('#translation-output-content').removeClass('loading');

                        let message = 'Something went wrong. Please try again.';
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Translation Failed',
                            html: message,
                            confirmButtonColor: '#0f766e'
                        });
                    },
                    complete: function() {
                        button.prop('disabled', false).html('<i data-feather="languages"></i> Translate Content');
                        feather.replace();
                    }
                });
            });

            feather.replace();
        });
    </script>

    <style>
        :root {
            --tr-bg: #f3f7f7;
            --tr-surface: #ffffff;
            --tr-surface-soft: #f8fbfb;
            --tr-border: #d8e4e4;
            --tr-border-strong: #b8d3d3;
            --tr-text: #102a2a;
            --tr-text-soft: #4b6363;
            --tr-muted: #789090;
            --tr-accent: #0f766e;
            --tr-accent-soft: #e6f6f4;
            --tr-accent-hover: #0b5d57;
            --tr-shadow: 0 10px 30px rgba(15, 118, 110, 0.08);
            --tr-radius: 16px;
        }

        .tr-topbar {
            background: linear-gradient(90deg, #ffffff 0%, #f1fbfa 100%);
            border-bottom: 1px solid var(--tr-border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .tr-topbar-left {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .tr-icon-wrap {
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

        .tr-icon-wrap svg {
            width: 18px;
            height: 18px;
        }

        .tr-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--tr-text);
        }

        .tr-sub {
            font-size: 0.76rem;
            color: var(--tr-muted);
        }

        .tr-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.32rem 0.8rem;
            border-radius: 999px;
            background: var(--tr-accent-soft);
            color: var(--tr-accent);
            font-size: 0.74rem;
            font-weight: 700;
        }

        .tr-page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 2rem 1.5rem 3rem;
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 1.25rem;
        }

        .tr-sidebar,
        .tr-main {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .tr-card,
        .tr-action-bar,
        .tr-textarea {
            box-shadow: var(--tr-shadow);
        }

        .tr-card {
            background: var(--tr-surface);
            border: 1px solid var(--tr-border);
            border-radius: var(--tr-radius);
            overflow: hidden;
        }

        .tr-card-header {
            padding: 0.95rem 1.1rem;
            border-bottom: 1px solid var(--tr-border);
            display: flex;
            align-items: center;
            gap: 0.55rem;
        }

        .tr-card-header-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--tr-accent-soft);
            color: var(--tr-accent);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tr-card-header-icon svg {
            width: 14px;
            height: 14px;
        }

        .tr-card-title {
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--tr-text-soft);
        }

        .tr-card-body {
            padding: 1rem 1.1rem;
        }

        .tr-field-label {
            display: block;
            margin-bottom: 0.45rem;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--tr-text-soft);
        }

        .tr-select,
        .tr-input,
        .tr-textarea {
            width: 100%;
            border: 1.5px solid var(--tr-border);
            border-radius: 12px;
            background: var(--tr-surface-soft);
            color: var(--tr-text);
            font-size: 0.9rem;
            transition: border-color .15s, box-shadow .15s;
        }

        .tr-select,
        .tr-input {
            height: 46px;
            padding: 0 0.9rem;
        }

        .tr-input::placeholder,
        .tr-textarea::placeholder {
            color: var(--tr-muted);
        }

        .tr-select:focus,
        .tr-input:focus,
        .tr-textarea:focus {
            outline: none;
            border-color: var(--tr-accent);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
        }

        .tr-stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.52rem 0;
            border-bottom: 1px solid var(--tr-border);
        }

        .tr-stat-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .tr-stat-label {
            font-size: 0.78rem;
            color: var(--tr-muted);
        }

        .tr-stat-val {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--tr-text);
        }

        .tr-editor-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .tr-pane {
            display: flex;
            flex-direction: column;
        }

        .tr-pane-label {
            margin-bottom: 0.55rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--tr-muted);
        }

        .tr-pane-action {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border: none;
            background: transparent;
            color: var(--tr-muted);
            font-size: 0.78rem;
            font-weight: 600;
        }

        .tr-pane-action svg {
            width: 13px;
            height: 13px;
        }

        .tr-pane-action:hover {
            color: var(--tr-accent);
        }

        .tr-textarea {
            min-height: 300px;
            padding: 1rem 1.1rem;
            resize: vertical;
            line-height: 1.7;
            background: var(--tr-surface);
        }

        .tr-textarea[readonly] {
            background: #fbfefe;
        }

        .tr-textarea.has-output {
            border-color: var(--tr-border-strong);
            background: #ffffff;
        }

        .tr-textarea.loading {
            background: linear-gradient(90deg, #eef7f7 25%, #e2f1ef 50%, #eef7f7 75%);
            background-size: 400px 100%;
            animation: trShimmer 1.4s ease infinite;
            color: transparent;
            pointer-events: none;
        }

        .tr-action-bar {
            background: var(--tr-surface);
            border: 1px solid var(--tr-border);
            border-radius: var(--tr-radius);
            padding: 1rem 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .tr-action-left {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .tr-selected-target {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--tr-text-soft);
        }

        .tr-selected-target span {
            margin-left: 0.3rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: var(--tr-accent-soft);
            color: var(--tr-accent);
        }

        .tr-tip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.76rem;
            color: var(--tr-muted);
        }

        .tr-tip svg {
            width: 13px;
            height: 13px;
        }

        .tr-btn-translate,
        .tr-copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border-radius: 10px;
            font-weight: 700;
            transition: all .15s;
        }

        .tr-btn-translate {
            padding: 0.72rem 1.35rem;
            border: none;
            background: var(--tr-accent);
            color: #fff;
        }

        .tr-btn-translate:hover:not(:disabled) {
            background: var(--tr-accent-hover);
        }

        .tr-copy-btn.copied {
            color: var(--tr-accent);
        }

        .tr-btn-translate:disabled,
        .tr-copy-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        @keyframes trShimmer {
            0% {
                background-position: -400px 0;
            }

            100% {
                background-position: 400px 0;
            }
        }

        @media (max-width: 992px) {
            .tr-page {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .tr-topbar,
            .tr-action-bar {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .tr-editor-grid {
                grid-template-columns: 1fr;
            }

            .tr-action-bar {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
@endpush
