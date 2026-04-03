@extends('layouts.admin')

@section('content')
    <div class="gc-topbar">
        <div class="gc-topbar-left">
            <div class="gc-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                </svg>
            </div>
            <div>
                <div class="gc-title">AI Content Generation</div>
                <div class="gc-sub">Create polished content from a prompt in seconds</div>
            </div>
        </div>
        <div class="gc-badges">
            <span class="gc-badge">AI Powered</span>
        </div>
    </div>

    <div class="gc-page">
        <div class="gc-sidebar">
            <div class="gc-card">
                <div class="gc-card-header">
                    <div class="gc-card-header-icon">
                        <i data-feather="layout"></i>
                    </div>
                    <span class="gc-card-title">Content Type</span>
                </div>
                <div class="gc-card-body">
                    <div class="gc-type-list">
                        <div class="gc-type-option">
                            <input type="radio" name="content_type" id="type_blog" value="blog post" checked>
                            <label for="type_blog">
                                <div class="gc-type-icon"><i data-feather="file-text"></i></div>
                                <div class="gc-type-text">
                                    <span class="gc-type-label">Blog Post</span>
                                    <span class="gc-type-desc">Long-form editorial content</span>
                                </div>
                            </label>
                        </div>

                        <div class="gc-type-option">
                            <input type="radio" name="content_type" id="type_product" value="product description">
                            <label for="type_product">
                                <div class="gc-type-icon"><i data-feather="shopping-bag"></i></div>
                                <div class="gc-type-text">
                                    <span class="gc-type-label">Product Description</span>
                                    <span class="gc-type-desc">Feature-focused selling copy</span>
                                </div>
                            </label>
                        </div>

                        <div class="gc-type-option">
                            <input type="radio" name="content_type" id="type_social" value="social media caption">
                            <label for="type_social">
                                <div class="gc-type-icon"><i data-feather="message-square"></i></div>
                                <div class="gc-type-text">
                                    <span class="gc-type-label">Social Caption</span>
                                    <span class="gc-type-desc">Short, punchy social content</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="gc-card">
                <div class="gc-card-header">
                    <div class="gc-card-header-icon">
                        <i data-feather="bar-chart-2"></i>
                    </div>
                    <span class="gc-card-title">Stats</span>
                </div>
                <div class="gc-card-body">
                    <div class="gc-stat-row"><span class="gc-stat-label">Words (prompt)</span><span class="gc-stat-val"
                            id="gc-stat-words-in">0</span></div>
                    <div class="gc-stat-row"><span class="gc-stat-label">Chars (prompt)</span><span class="gc-stat-val"
                            id="gc-stat-chars-in">0</span></div>
                    <div class="gc-stat-row"><span class="gc-stat-label">Words (output)</span><span class="gc-stat-val"
                            id="gc-stat-words-out">-</span></div>
                    <div class="gc-stat-row"><span class="gc-stat-label">Chars (output)</span><span class="gc-stat-val"
                            id="gc-stat-chars-out">-</span></div>
                </div>
            </div>
        </div>

        <div class="gc-main">
            <div id="gc-alert-container"></div>

            <div class="gc-editor-grid">
                <div class="gc-pane">
                    <div class="gc-pane-label">
                        Prompt & Keywords
                        <button type="button" class="gc-pane-action" id="gc-clear-btn">
                            <i data-feather="trash-2"></i>
                            Clear
                        </button>
                    </div>
                    <textarea class="gc-textarea" id="gc-prompt"
                        placeholder="Describe what you want to generate. Include topic, audience, tone, structure, keywords, or any specific requirements."></textarea>
                </div>

                <div class="gc-pane">
                    <div class="gc-pane-label">
                        Generated Output
                        <div class="gc-pane-actions">
                            <button type="button" class="gc-pane-action gc-copy-btn" id="gc-copy-btn" disabled>
                                <i data-feather="copy"></i>
                                Copy
                            </button>
                            <button type="button" class="gc-pane-action gc-save-btn" id="gc-save-btn" disabled>
                                <i data-feather="save"></i>
                                Save
                            </button>
                        </div>
                    </div>
                    <textarea class="gc-textarea" id="gc-output" readonly
                        placeholder="Generated content will appear here after you run the prompt."></textarea>
                </div>
            </div>

            <div class="gc-action-bar">
                <div class="gc-action-left">
                    <div class="gc-selected-type">
                        Type: <span id="gc-active-type-label">Blog Post</span>
                    </div>
                    <div class="gc-tip">
                        <i data-feather="info"></i>
                        Review AI output before publishing or saving.
                    </div>
                </div>
                <div class="gc-action-right">
                    <button type="button" class="gc-btn-generate" id="gc-generate-btn">
                        <i data-feather="zap"></i>
                        <span id="gc-generate-btn-text">Generate Content</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const alertContainer = $('#gc-alert-container');
            const promptInput = $('#gc-prompt');
            const outputInput = $('#gc-output');
            const generateBtn = $('#gc-generate-btn');
            const generateBtnText = $('#gc-generate-btn-text');
            const copyBtn = $('#gc-copy-btn');
            const saveBtn = $('#gc-save-btn');
            const activeTypeLabel = $('#gc-active-type-label');

            let latestGenerated = {
                content_type: 'blog post',
                prompt: '',
                generated_text: ''
            };

            function updateStats() {
                const inputText = promptInput.val().trim();
                const outputText = outputInput.val().trim();
                const inputWords = inputText ? inputText.split(/\s+/).filter(Boolean).length : 0;
                const outputWords = outputText ? outputText.split(/\s+/).filter(Boolean).length : 0;

                $('#gc-stat-words-in').text(inputWords);
                $('#gc-stat-chars-in').text(inputText.length);
                $('#gc-stat-words-out').text(outputText ? outputWords : '-');
                $('#gc-stat-chars-out').text(outputText ? outputText.length : '-');
            }

            function showAlert(message, type = 'error') {
                alertContainer.html(`
                    <div class="gc-alert ${type}">
                        <i data-feather="${type === 'success' ? 'check-circle' : 'alert-circle'}"></i>
                        <span>${message}</span>
                    </div>
                `);
                feather.replace();
            }

            function clearAlert() {
                alertContainer.html('');
            }

            function resetCopyButton() {
                copyBtn.prop('disabled', true).removeClass('copied').html('<i data-feather="copy"></i> Copy');
                feather.replace();
            }

            function resetSaveButton(label = 'Save') {
                saveBtn.prop('disabled', true).html(`<i data-feather="save"></i> ${label}`);
                feather.replace();
            }

            function enableSaveButton(label = 'Save') {
                saveBtn.prop('disabled', false).html(`<i data-feather="save"></i> ${label}`);
                feather.replace();
            }

            promptInput.on('input', updateStats);

            $('input[name="content_type"]').on('change', function() {
                activeTypeLabel.text($(this).closest('.gc-type-option').find('.gc-type-label').text());
            });

            $('#gc-clear-btn').on('click', function() {
                promptInput.val('');
                outputInput.val('').removeClass('has-output loading');
                latestGenerated = {
                    content_type: $('input[name="content_type"]:checked').val(),
                    prompt: '',
                    generated_text: ''
                };
                resetCopyButton();
                resetSaveButton();
                clearAlert();
                updateStats();
            });

            copyBtn.on('click', function() {
                const text = outputInput.val();
                if (!text) {
                    return;
                }

                navigator.clipboard.writeText(text).then(() => {
                    copyBtn.addClass('copied').html('<i data-feather="check"></i> Copied!');
                    feather.replace();

                    setTimeout(() => {
                        copyBtn.removeClass('copied').html('<i data-feather="copy"></i> Copy');
                        feather.replace();
                    }, 2000);
                }).catch(() => {
                    showAlert('Copy failed. Please copy manually.', 'error');
                });
            });

            generateBtn.on('click', function() {
                clearAlert();

                const prompt = promptInput.val().trim();
                const contentType = $('input[name="content_type"]:checked').val();

                if (!prompt) {
                    showAlert('Please enter a prompt before generating content.', 'error');
                    return;
                }

                generateBtn.prop('disabled', true);
                generateBtnText.text('Generating...');
                outputInput.addClass('loading').removeClass('has-output').val('');
                resetCopyButton();
                resetSaveButton();
                updateStats();

                $.ajax({
                    url: '{{ route('ai.content.generate') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        content_type: contentType,
                        prompt: prompt
                    },
                    success: function(response) {
                        latestGenerated = {
                            content_type: response.content_type,
                            prompt: prompt,
                            generated_text: response.generated_text
                        };

                        outputInput
                            .removeClass('loading')
                            .addClass('has-output')
                            .val(response.generated_text);

                        copyBtn.prop('disabled', false).html('<i data-feather="copy"></i> Copy');
                        enableSaveButton();
                        updateStats();
                        showAlert(response.message || 'Content generated successfully.', 'success');
                        feather.replace();
                    },
                    error: function(xhr) {
                        outputInput.removeClass('loading');

                        let message = 'Something went wrong while generating content.';
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        showAlert(message, 'error');
                    },
                    complete: function() {
                        generateBtn.prop('disabled', false);
                        generateBtnText.text('Generate Content');
                        feather.replace();
                    }
                });
            });

            saveBtn.on('click', function() {
                clearAlert();

                if (!latestGenerated.generated_text) {
                    showAlert('Generate content first before saving.', 'error');
                    return;
                }

                saveBtn.prop('disabled', true).html('<i data-feather="loader"></i> Saving...');
                feather.replace();

                $.ajax({
                    url: '{{ route('ai.content.save') }}',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(latestGenerated),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        showAlert(response.message || 'Content saved successfully.', 'success');
                        saveBtn.html('<i data-feather="check"></i> Saved!');
                        feather.replace();

                        setTimeout(() => {
                            enableSaveButton();
                        }, 1400);
                    },
                    error: function(xhr) {
                        let message = 'Failed to save content.';
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        showAlert(message, 'error');
                        enableSaveButton();
                    }
                });
            });

            updateStats();
            feather.replace();
        });
    </script>

    <style>
        :root {
            --gc-bg: #f3f7f7;
            --gc-surface: #ffffff;
            --gc-surface-soft: #f8fbfb;
            --gc-border: #d8e4e4;
            --gc-border-strong: #b8d3d3;
            --gc-text: #102a2a;
            --gc-text-soft: #4b6363;
            --gc-muted: #789090;
            --gc-accent: #0f766e;
            --gc-accent-soft: #e6f6f4;
            --gc-accent-hover: #0b5d57;
            --gc-shadow: 0 10px 30px rgba(15, 118, 110, 0.08);
            --gc-radius: 16px;
            --gc-success-bg: #ecfdf5;
            --gc-success-border: #bbf7d0;
            --gc-success-text: #166534;
            --gc-error-bg: #fef2f2;
            --gc-error-border: #fecaca;
            --gc-error-text: #b91c1c;
        }

        .gc-topbar {
            background: linear-gradient(90deg, #ffffff 0%, #f1fbfa 100%);
            border-bottom: 1px solid var(--gc-border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .gc-topbar-left {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .gc-icon-wrap {
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

        .gc-icon-wrap svg {
            width: 18px;
            height: 18px;
        }

        .gc-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gc-text);
        }

        .gc-sub {
            font-size: 0.76rem;
            color: var(--gc-muted);
        }

        .gc-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.32rem 0.8rem;
            border-radius: 999px;
            background: var(--gc-accent-soft);
            color: var(--gc-accent);
            font-size: 0.74rem;
            font-weight: 700;
        }

        .gc-page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 2rem 1.5rem 3rem;
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 1.25rem;
        }

        .gc-sidebar,
        .gc-main {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .gc-card,
        .gc-action-bar,
        .gc-textarea {
            box-shadow: var(--gc-shadow);
        }

        .gc-card {
            background: var(--gc-surface);
            border: 1px solid var(--gc-border);
            border-radius: var(--gc-radius);
            overflow: hidden;
        }

        .gc-card-header {
            padding: 0.95rem 1.1rem;
            border-bottom: 1px solid var(--gc-border);
            display: flex;
            align-items: center;
            gap: 0.55rem;
        }

        .gc-card-header-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--gc-accent-soft);
            color: var(--gc-accent);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gc-card-header-icon svg {
            width: 14px;
            height: 14px;
        }

        .gc-card-title {
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--gc-text-soft);
        }

        .gc-card-body {
            padding: 1rem 1.1rem;
        }

        .gc-type-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .gc-type-option {
            position: relative;
        }

        .gc-type-option input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .gc-type-option label {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.75rem 0.85rem;
            border: 1.5px solid var(--gc-border);
            border-radius: 10px;
            cursor: pointer;
            background: var(--gc-surface-soft);
            transition: all .15s;
        }

        .gc-type-option label:hover {
            border-color: var(--gc-accent);
            background: var(--gc-accent-soft);
        }

        .gc-type-option input:checked+label {
            border-color: var(--gc-accent);
            background: var(--gc-accent-soft);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.09);
        }

        .gc-type-icon {
            width: 32px;
            height: 32px;
            flex-shrink: 0;
            background: #e8ecf2;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gc-muted);
        }

        .gc-type-icon svg {
            width: 15px;
            height: 15px;
        }

        .gc-type-option input:checked+label .gc-type-icon {
            background: var(--gc-accent);
            color: #fff;
        }

        .gc-type-text {
            line-height: 1.35;
        }

        .gc-type-label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--gc-text-soft);
        }

        .gc-type-desc {
            display: block;
            font-size: 0.69rem;
            color: var(--gc-muted);
        }

        .gc-type-option input:checked+label .gc-type-label {
            color: var(--gc-accent);
        }

        .gc-stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.52rem 0;
            border-bottom: 1px solid var(--gc-border);
        }

        .gc-stat-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .gc-stat-label {
            font-size: 0.78rem;
            color: var(--gc-muted);
        }

        .gc-stat-val {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--gc-text);
        }

        .gc-editor-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .gc-pane {
            display: flex;
            flex-direction: column;
        }

        .gc-pane-label {
            margin-bottom: 0.55rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--gc-muted);
        }

        .gc-pane-actions {
            display: flex;
            gap: 0.45rem;
        }

        .gc-pane-action {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            border: none;
            background: transparent;
            color: var(--gc-muted);
            font-size: 0.78rem;
            font-weight: 600;
        }

        .gc-pane-action svg {
            width: 13px;
            height: 13px;
        }

        .gc-pane-action:hover:not(:disabled) {
            color: var(--gc-accent);
        }

        .gc-textarea {
            width: 100%;
            min-height: 300px;
            padding: 1rem 1.1rem;
            border: 1.5px solid var(--gc-border);
            border-radius: 12px;
            background: var(--gc-surface);
            color: var(--gc-text);
            font-size: 0.9rem;
            line-height: 1.7;
            resize: vertical;
            transition: border-color .15s, box-shadow .15s;
        }

        .gc-textarea:focus {
            outline: none;
            border-color: var(--gc-accent);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
        }

        .gc-textarea::placeholder {
            color: var(--gc-muted);
        }

        .gc-textarea[readonly] {
            background: #fbfefe;
        }

        .gc-textarea.has-output {
            border-color: var(--gc-border-strong);
            background: #ffffff;
        }

        .gc-textarea.loading {
            background: linear-gradient(90deg, #eef7f7 25%, #e2f1ef 50%, #eef7f7 75%);
            background-size: 400px 100%;
            animation: gcShimmer 1.4s ease infinite;
            color: transparent;
            pointer-events: none;
        }

        .gc-action-bar {
            background: var(--gc-surface);
            border: 1px solid var(--gc-border);
            border-radius: var(--gc-radius);
            padding: 1rem 1.2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .gc-action-left {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .gc-selected-type {
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--gc-text-soft);
        }

        .gc-selected-type span {
            margin-left: 0.3rem;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: var(--gc-accent-soft);
            color: var(--gc-accent);
        }

        .gc-tip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.76rem;
            color: var(--gc-muted);
        }

        .gc-tip svg {
            width: 13px;
            height: 13px;
        }

        .gc-btn-generate {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.72rem 1.35rem;
            border: none;
            border-radius: 10px;
            background: var(--gc-accent);
            color: #fff;
            font-weight: 700;
            transition: all .15s;
        }

        .gc-btn-generate:hover:not(:disabled) {
            background: var(--gc-accent-hover);
        }

        .gc-btn-generate svg {
            width: 15px;
            height: 15px;
        }

        .gc-copy-btn.copied {
            color: var(--gc-accent);
        }

        .gc-btn-generate:disabled,
        .gc-pane-action:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .gc-alert {
            margin-bottom: 1rem;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            border: 1px solid;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            font-size: 0.84rem;
            line-height: 1.55;
        }

        .gc-alert svg {
            width: 16px;
            height: 16px;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .gc-alert.success {
            background: var(--gc-success-bg);
            border-color: var(--gc-success-border);
            color: var(--gc-success-text);
        }

        .gc-alert.error {
            background: var(--gc-error-bg);
            border-color: var(--gc-error-border);
            color: var(--gc-error-text);
        }

        @keyframes gcShimmer {
            0% {
                background-position: -400px 0;
            }

            100% {
                background-position: 400px 0;
            }
        }

        @media (max-width: 992px) {
            .gc-page {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .gc-topbar,
            .gc-action-bar {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .gc-editor-grid {
                grid-template-columns: 1fr;
            }

            .gc-action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .gc-pane-label {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .gc-pane-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
@endpush
