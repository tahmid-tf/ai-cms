@extends('layouts.admin')

@section('content')
    <div class="ai-page">
        <div id="alert-container"></div>

        @if ($errors->any())
            <div class="alert-pro error">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="ai-header">
            <div class="ai-header-eyebrow">
                <span class="ai-header-eyebrow-dot"></span>
                AI Studio
            </div>
            <h1>Generate <em>great</em> content,<br>instantly.</h1>
            <p class="ai-header-desc">
                Choose a format, describe what you need, and let the model do the heavy lifting.
                Works for blogs, products, and social — all in seconds.
            </p>
        </div>

        <div class="ai-card">
            <form id="ai-generate-form">
                @csrf

                <div class="ai-card-body">
                    <div class="field-group">
                        <label class="field-label">Content Type</label>
                        <div class="type-selector">
                            <div class="type-option">
                                <input type="radio" name="content_type" id="type_blog" value="blog post" checked>
                                <label for="type_blog">
                                    <div class="type-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                    </div>
                                    <span class="type-label-text">Blog Post</span>
                                </label>
                            </div>

                            <div class="type-option">
                                <input type="radio" name="content_type" id="type_product" value="product description">
                                <label for="type_product">
                                    <div class="type-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                                            <line x1="3" y1="6" x2="21" y2="6" />
                                            <path d="M16 10a4 4 0 0 1-8 0" />
                                        </svg>
                                    </div>
                                    <span class="type-label-text">Product Description</span>
                                </label>
                            </div>

                            <div class="type-option">
                                <input type="radio" name="content_type" id="type_social" value="social media caption">
                                <label for="type_social">
                                    <div class="type-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                        </svg>
                                    </div>
                                    <span class="type-label-text">Social Caption</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="field-group" style="margin-bottom:0;">
                        <label for="prompt" class="field-label">Keywords &amp; Prompt</label>
                        <span class="field-hint">Describe your topic, tone, or any specific details you want
                            included.</span>
                        <div class="prompt-wrap">
                            <svg class="prompt-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.35-4.35" />
                            </svg>
                            <input type="text" name="prompt" id="prompt"
                                placeholder="e.g. noise-cancelling headphones for remote workers, professional tone…"
                                required autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="card-divider"></div>

                <div class="ai-card-footer">
                    <div class="footer-note">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                        Results are AI-generated — always review before publishing.
                    </div>

                    <button type="submit" class="btn-generate" id="generate-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                        <span id="generate-btn-text">Generate</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="result-panel" id="result-panel" style="display:none;">
            <div class="result-header">
                <div class="result-label">
                    Output
                    <span class="result-label-tag" id="result-content-type"></span>
                </div>

                <div class="result-actions">
                    <button class="copy-btn" id="copy-btn" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                        </svg>
                        <span>Copy</span>
                    </button>

                    <button class="copy-btn save-btn" id="save-btn" type="button" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                            <polyline points="17 21 17 13 7 13 7 21" />
                            <polyline points="7 3 7 8 15 8" />
                        </svg>
                        <span id="save-btn-text">Save</span>
                    </button>
                </div>
            </div>

            <div class="result-box">
                <div class="result-box-inner" id="result-text"></div>
                <div class="result-box-footer">
                    <span class="word-count" id="word-count"></span>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('ai-generate-form');
        const generateBtn = document.getElementById('generate-btn');
        const generateBtnText = document.getElementById('generate-btn-text');
        const resultPanel = document.getElementById('result-panel');
        const resultText = document.getElementById('result-text');
        const resultContentType = document.getElementById('result-content-type');
        const wordCount = document.getElementById('word-count');
        const alertContainer = document.getElementById('alert-container');
        const copyBtn = document.getElementById('copy-btn');
        const saveBtn = document.getElementById('save-btn');
        const saveBtnText = document.getElementById('save-btn-text');

        let latestGenerated = {
            content_type: '',
            prompt: '',
            generated_text: ''
        };

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearAlert();

            const formData = new FormData(form);

            generateBtn.disabled = true;
            generateBtnText.textContent = 'Generating...';
            saveBtn.disabled = true;
            saveBtnText.textContent = 'Save';

            try {
                const response = await fetch("{{ route('ai.content.generate') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    if (data.errors) {
                        const messages = Object.values(data.errors).flat().join('<br>');
                        showAlert(messages, 'error');
                    } else {
                        showAlert(data.message || 'Something went wrong while generating content.', 'error');
                    }
                    return;
                }

                latestGenerated = {
                    content_type: data.content_type,
                    prompt: formData.get('prompt'),
                    generated_text: data.generated_text
                };

                resultText.textContent = data.generated_text;
                resultContentType.textContent = data.content_type;
                updateWordCount(data.generated_text);
                resultPanel.style.display = 'block';
                saveBtn.disabled = false;

                showAlert(data.message || 'Content generated successfully.', 'success');
            } catch (error) {
                showAlert('Unable to connect to the server. Please try again.', 'error');
            } finally {
                generateBtn.disabled = false;
                generateBtnText.textContent = 'Generate';
            }
        });

        copyBtn.addEventListener('click', async function() {
            const text = resultText.innerText.trim();

            if (!text) {
                showAlert('Nothing to copy yet.', 'error');
                return;
            }

            try {
                await navigator.clipboard.writeText(text);
                const label = copyBtn.querySelector('span');
                copyBtn.classList.add('copied');
                label.textContent = 'Copied!';

                setTimeout(() => {
                    copyBtn.classList.remove('copied');
                    label.textContent = 'Copy';
                }, 2000);
            } catch (error) {
                showAlert('Copy failed. Please copy manually.', 'error');
            }
        });

        saveBtn.addEventListener('click', async function() {
            clearAlert();

            if (!latestGenerated.generated_text) {
                showAlert('Generate content first before saving.', 'error');
                return;
            }

            saveBtn.disabled = true;
            saveBtnText.textContent = 'Saving...';

            try {
                const response = await fetch("{{ route('ai.content.save') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(latestGenerated)
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    if (data.errors) {
                        const messages = Object.values(data.errors).flat().join('<br>');
                        showAlert(messages, 'error');
                    } else {
                        showAlert(data.message || 'Failed to save content.', 'error');
                    }

                    saveBtn.disabled = false;
                    saveBtnText.textContent = 'Save';
                    return;
                }

                showAlert(data.message || 'Content saved successfully!', 'success');
                saveBtnText.textContent = 'Saved!';

                setTimeout(() => {
                    saveBtn.disabled = false;
                    saveBtnText.textContent = 'Save';
                }, 1500);
            } catch (error) {
                showAlert('Unable to save content right now. Please try again.', 'error');
                saveBtn.disabled = false;
                saveBtnText.textContent = 'Save';
            }
        });

        function updateWordCount(text) {
            const words = text.trim().split(/\s+/).filter(Boolean).length;
            wordCount.textContent = words + ' words · ' + text.length + ' characters';
        }

        function showAlert(message, type = 'error') {
            alertContainer.innerHTML = `
                <div class="alert-pro ${type}">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <span>${message}</span>
                </div>
            `;
        }

        function clearAlert() {
            alertContainer.innerHTML = '';
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Geist:wght@300;400;500;600&display=swap');

        :root {
            --page-bg: #f7f6f3;
            --surface: #ffffff;
            --border: #e4e2dc;
            --border-focus: #1a1a1a;
            --text: #1a1a1a;
            --text2: #4a4845;
            --muted: #9b9590;
            --accent: #1a1a1a;
            --accent-hover: #333;
            --tag-bg: #f0efe9;
            --success-bg: #f0faf4;
            --success-border: #b4dfc4;
            --success-text: #166534;
            --error-bg: #fef5f5;
            --error-border: #f5c6c6;
            --error-text: #c0392b;
        }

        .ai-page {
            min-height: 100vh;
            background: var(--page-bg);
            font-family: 'Geist', sans-serif;
            padding: 3rem 1.5rem 5rem;
        }

        .ai-header {
            max-width: 680px;
            margin: 0 auto 2.5rem;
        }

        .ai-header-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.85rem;
        }

        .ai-header-eyebrow-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--muted);
        }

        .ai-header h1 {
            font-family: 'Instrument Serif', Georgia, serif;
            font-size: clamp(2rem, 4vw, 2.8rem);
            font-weight: 400;
            color: var(--text);
            line-height: 1.15;
            letter-spacing: -0.02em;
            margin-bottom: 0.6rem;
        }

        .ai-header h1 em {
            font-style: italic;
            color: var(--muted);
        }

        .ai-header-desc {
            font-size: 0.9rem;
            color: var(--text2);
            line-height: 1.65;
            font-weight: 300;
        }

        .alert-pro {
            max-width: 680px;
            margin: 0 auto 1.25rem;
            padding: 0.85rem 1.1rem;
            border-radius: 10px;
            font-size: 0.83rem;
            line-height: 1.55;
            border: 1px solid;
            display: flex;
            gap: 0.7rem;
            align-items: flex-start;
        }

        .alert-pro.error {
            background: var(--error-bg);
            border-color: var(--error-border);
            color: var(--error-text);
        }

        .alert-pro.success {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success-text);
        }

        .alert-pro svg {
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-pro ul {
            margin: 0;
            padding-left: 1.1rem;
        }

        .ai-card {
            max-width: 680px;
            margin: 0 auto;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04), 0 12px 40px rgba(0, 0, 0, 0.06);
        }

        .ai-card-body {
            padding: 2rem 2rem 1.75rem;
        }

        .field-group {
            margin-bottom: 1.5rem;
        }

        .field-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--text2);
            margin-bottom: 0.55rem;
        }

        .field-hint {
            font-size: 0.78rem;
            color: var(--muted);
            font-weight: 300;
            margin-bottom: 0.55rem;
            display: block;
        }

        .type-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.6rem;
        }

        .type-option {
            position: relative;
        }

        .type-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .type-option label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 0.5rem;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            background: var(--page-bg);
            transition: all .15s;
            text-align: center;
        }

        .type-option label:hover {
            border-color: var(--border-focus);
            background: #f3f2ee;
        }

        .type-option input:checked+label {
            border-color: var(--border-focus);
            background: var(--surface);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .type-icon {
            width: 32px;
            height: 32px;
            background: var(--tag-bg);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .type-icon svg {
            width: 15px;
            height: 15px;
            stroke: var(--text2);
        }

        .type-option input:checked+label .type-icon {
            background: var(--text);
        }

        .type-option input:checked+label .type-icon svg {
            stroke: #fff;
        }

        .type-label-text {
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--text2);
            line-height: 1.3;
        }

        .type-option input:checked+label .type-label-text {
            color: var(--text);
            font-weight: 600;
        }

        .prompt-wrap {
            position: relative;
        }

        .prompt-wrap svg.prompt-icon {
            position: absolute;
            left: 14px;
            top: 14px;
            width: 15px;
            height: 15px;
            stroke: var(--muted);
            pointer-events: none;
        }

        #prompt {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            font-size: 0.9rem;
            font-family: 'Geist', sans-serif;
            font-weight: 400;
            color: var(--text);
            background: var(--page-bg);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }

        #prompt::placeholder {
            color: var(--muted);
        }

        #prompt:focus {
            border-color: var(--border-focus);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(26, 26, 26, 0.06);
        }

        .card-divider {
            height: 1px;
            background: var(--border);
            margin: 0;
        }

        .ai-card-footer {
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background: #fafaf8;
        }

        .footer-note {
            font-size: 0.76rem;
            color: var(--muted);
            font-weight: 300;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .footer-note svg {
            width: 13px;
            height: 13px;
            stroke: var(--muted);
            flex-shrink: 0;
        }

        .btn-generate {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.7rem 1.4rem;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 0.85rem;
            font-weight: 600;
            font-family: 'Geist', sans-serif;
            cursor: pointer;
            transition: background .15s, transform .1s, box-shadow .15s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
            white-space: nowrap;
        }

        .btn-generate:disabled,
        .copy-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-generate svg {
            width: 15px;
            height: 15px;
            stroke: currentColor;
        }

        .btn-generate:hover {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.22);
        }

        .btn-generate:active {
            transform: translateY(0);
        }

        .result-panel {
            max-width: 680px;
            margin: 1.5rem auto 0;
        }

        .result-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            gap: 1rem;
        }

        .result-actions {
            display: flex;
            gap: 0.5rem;
        }

        .result-label {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .result-label-tag {
            background: var(--tag-bg);
            color: var(--text2);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.15rem 0.5rem;
            border-radius: 5px;
            text-transform: capitalize;
            letter-spacing: 0;
        }

        .copy-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.78rem;
            font-weight: 500;
            color: var(--text2);
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 0.3rem 0.7rem;
            cursor: pointer;
            font-family: 'Geist', sans-serif;
            transition: all .15s;
        }

        .copy-btn svg {
            width: 12px;
            height: 12px;
            stroke: currentColor;
        }

        .copy-btn:hover {
            border-color: var(--border-focus);
            color: var(--text);
            background: #f8f7f4;
        }

        .copy-btn.copied {
            color: #16a34a;
            border-color: #b4dfc4;
            background: var(--success-bg);
        }

        .save-btn {
            color: #0f5132;
        }

        .result-box {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04), 0 8px 28px rgba(0, 0, 0, 0.05);
            animation: slideUp 0.35s cubic-bezier(.22, .68, 0, 1.2) both;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-box-inner {
            padding: 1.5rem 1.75rem;
            font-size: 0.9rem;
            color: var(--text);
            line-height: 1.75;
            font-weight: 300;
            white-space: pre-wrap;
            word-break: break-word;
            min-height: 120px;
            max-height: 400px;
            overflow-y: auto;
        }

        .result-box-inner::-webkit-scrollbar {
            width: 4px;
        }

        .result-box-inner::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }

        .result-box-footer {
            border-top: 1px solid var(--border);
            padding: 0.65rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafaf8;
        }

        .word-count {
            font-size: 0.73rem;
            color: var(--muted);
            font-weight: 400;
        }

        @media (max-width: 640px) {
            .type-selector {
                grid-template-columns: 1fr;
            }

            .ai-card-footer,
            .result-header {
                flex-direction: column;
                align-items: stretch;
            }

            .result-actions {
                justify-content: flex-end;
            }
        }
    </style>
@endsection
