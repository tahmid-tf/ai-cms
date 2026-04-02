@extends('layouts.admin')

@section('content')
    {{-- Topbar --}}
    <div class="ae-topbar">
        <div class="ae-topbar-left">
            <div class="ae-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                </svg>
            </div>
            <div>
                <div class="ae-title">AI Content Editor</div>
                <div class="ae-sub">Rewrite, refine, and optimise</div>
            </div>
        </div>
        <div class="ae-badges">
            <span class="ae-badge">✦ AI Powered</span>
        </div>
    </div>

    {{-- Page --}}
    <div class="ae-page">

        {{-- Sidebar --}}
        <div class="ae-sidebar">

            {{-- Edit mode --}}
            <div class="ae-card">
                <div class="ae-card-header">
                    <div class="ae-card-header-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14" />
                        </svg>
                    </div>
                    <span class="ae-card-title">Edit Mode</span>
                </div>
                <div class="ae-card-body">
                    <div class="edit-type-list">

                        <div class="edit-type-option">
                            <input type="radio" name="edit_type" id="type_grammar" value="grammar" checked>
                            <label for="type_grammar">
                                <div class="et-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" />
                                    </svg>
                                </div>
                                <div class="et-text">
                                    <span class="et-label">Fix Grammar</span>
                                    <span class="et-desc">Correct errors &amp; typos</span>
                                </div>
                            </label>
                        </div>

                        <div class="edit-type-option">
                            <input type="radio" name="edit_type" id="type_tone" value="tone">
                            <label for="type_tone">
                                <div class="et-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                                        <path d="M8 21h8M12 17v4" />
                                    </svg>
                                </div>
                                <div class="et-text">
                                    <span class="et-label">Professional Tone</span>
                                    <span class="et-desc">Formal &amp; polished voice</span>
                                </div>
                            </label>
                        </div>

                        <div class="edit-type-option">
                            <input type="radio" name="edit_type" id="type_seo" value="seo">
                            <label for="type_seo">
                                <div class="et-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8" />
                                        <path d="m21 21-4.35-4.35" />
                                    </svg>
                                </div>
                                <div class="et-text">
                                    <span class="et-label">SEO Optimize</span>
                                    <span class="et-desc">Boost search ranking</span>
                                </div>
                            </label>
                        </div>

                        <div class="edit-type-option">
                            <input type="radio" name="edit_type" id="type_rewrite" value="rewrite">
                            <label for="type_rewrite">
                                <div class="et-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="1 4 1 10 7 10" />
                                        <path d="M3.51 15a9 9 0 1 0 .49-3.51" />
                                    </svg>
                                </div>
                                <div class="et-text">
                                    <span class="et-label">Rewrite</span>
                                    <span class="et-desc">Fresh take, same idea</span>
                                </div>
                            </label>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Stats card --}}
            <div class="ae-card">
                <div class="ae-card-header">
                    <div class="ae-card-header-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10" />
                            <line x1="12" y1="20" x2="12" y2="4" />
                            <line x1="6" y1="20" x2="6" y2="14" />
                        </svg>
                    </div>
                    <span class="ae-card-title">Stats</span>
                </div>
                <div class="ae-card-body">
                    <div class="stat-row"><span class="stat-label">Words (in)</span><span class="stat-val"
                            id="stat-words-in">0</span></div>
                    <div class="stat-row"><span class="stat-label">Chars (in)</span><span class="stat-val"
                            id="stat-chars-in">0</span></div>
                    <div class="stat-row"><span class="stat-label">Words (out)</span><span class="stat-val"
                            id="stat-words-out">—</span></div>
                    <div class="stat-row"><span class="stat-label">Chars (out)</span><span class="stat-val"
                            id="stat-chars-out">—</span></div>
                </div>
            </div>

        </div>

        {{-- Main editor --}}
        <div class="ae-main">
            <div class="ae-editor-grid">
                <div class="editor-pane">
                    <div class="pane-label">
                        Your Content
                        <button type="button" class="pane-action" id="clear-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6" />
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                <path d="M10 11v6M14 11v6" />
                            </svg>
                            Clear
                        </button>
                    </div>
                    <textarea class="ae-textarea" id="input-content"
                        placeholder="Paste or type your content here. The AI will analyse and improve it based on the selected mode…"></textarea>
                </div>

                <div class="editor-pane">
                    <div class="pane-label">
                        Improved Output
                        <button type="button" class="pane-action btn-copy" id="copy-btn" disabled>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="9" y="9" width="13" height="13" rx="2" />
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
                            </svg>
                            Copy
                        </button>
                    </div>
                    <textarea class="ae-textarea" id="output-content" readonly placeholder="Your improved content will appear here…"></textarea>
                </div>
            </div>

            {{-- Action bar --}}
            <div class="ae-action-bar">
                <div class="ae-action-left">
                    <div class="ae-selected-type">
                        Mode: <span id="active-mode-label">Fix Grammar</span>
                    </div>
                    <div class="ae-tip">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        Always review AI output before publishing.
                    </div>
                </div>
                <div class="ae-action-right">
                    <button type="button" class="btn-improve" id="edit-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                        </svg>
                        Improve Content
                    </button>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // ── Live stats
            function updateStats() {
                const inp = $('#input-content').val().trim();
                const out = $('#output-content').val().trim();
                const wIn = inp ? inp.split(/\s+/).filter(Boolean).length : 0;
                const wOut = out ? out.split(/\s+/).filter(Boolean).length : 0;
                $('#stat-words-in').text(wIn);
                $('#stat-chars-in').text(inp.length);
                $('#stat-words-out').text(out ? wOut : '—');
                $('#stat-chars-out').text(out ? out.length : '—');
            }

            $('#input-content').on('input', updateStats);

            // ── Active mode label
            $('input[name="edit_type"]').on('change', function() {
                $('#active-mode-label').text($(this).closest('.edit-type-option').find('.et-label').text());
            });

            // ── Clear
            $('#clear-btn').on('click', function() {
                $('#input-content').val('');
                $('#output-content').val('').removeClass('has-output');
                $('#copy-btn').prop('disabled', true).removeClass('copied');
                updateStats();
            });

            // ── Copy
            $('#copy-btn').on('click', function() {
                const text = $('#output-content').val();
                if (!text) return;
                navigator.clipboard.writeText(text).then(() => {
                    const btn = $(this);
                    btn.addClass('copied').html(`
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                Copied!`);
                    setTimeout(() => {
                        btn.removeClass('copied').html(`
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    Copy`);
                    }, 2000);
                });
            });

            // ── Improve
            $(document).on('click', '#edit-btn', function() {
                const button = $(this);
                const content = $('#input-content').val();
                const type = $('input[name="edit_type"]:checked').val();

                if (!content.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Empty content',
                        text: 'Please enter some content to improve.',
                        confirmButtonColor: '#3b6ff5'
                    });
                    return;
                }

                // Loading state
                button.prop('disabled', true).html(`
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite;"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.49-4.49"/></svg>
            Processing…`);

                $('#output-content').addClass('loading').val('');
                $('#copy-btn').prop('disabled', true);

                $.ajax({
                    url: '/ai/editor',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        content,
                        type
                    },
                    success: function(res) {
                        const result = res.edited_content || '';
                        $('#output-content').removeClass('loading').val(result).addClass(
                            'has-output');
                        updateStats();
                        $('#copy-btn').prop('disabled', false);
                    },
                    error: function(xhr) {
                        $('#output-content').removeClass('loading');
                        let message = 'Something went wrong. Please try again.';
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            message = Object.values(xhr.responseJSON.errors).flat().join(
                                '<br>');
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: message,
                            confirmButtonColor: '#3b6ff5'
                        });
                    },
                    complete: function() {
                        button.prop('disabled', false).html(`
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    Improve Content`);
                    }
                });
            });

        });
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

        :root {
            --bg: #f4f6fa;
            --surface: #ffffff;
            --surface2: #f8f9fc;
            --border: #e8ecf2;
            --border-focus: #3b6ff5;
            --text: #111827;
            --text2: #374151;
            --muted: #9ca3af;
            --accent: #3b6ff5;
            --accent-light: #eef2fe;
            --accent-hover: #2d5de0;
            --green: #16a34a;
            --green-light: #dcfce7;
            --radius: 14px;
            --shadow: 0 1px 4px rgba(0, 0, 0, 0.06), 0 6px 24px rgba(0, 0, 0, 0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text);
        }

        /* ── Topbar ── */
        .ae-topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .ae-topbar-left {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .ae-icon-wrap {
            width: 36px;
            height: 36px;
            background: var(--accent);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(59, 111, 245, 0.3);
            flex-shrink: 0;
        }

        .ae-icon-wrap svg {
            width: 17px;
            height: 17px;
            stroke: #fff;
        }

        .ae-title {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .ae-sub {
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 1px;
        }

        .ae-badges {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ae-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            background: var(--accent-light);
            color: var(--accent);
            border: 1px solid #d4e0fd;
        }

        /* ── Page ── */
        .ae-page {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.5rem 4rem;
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 1.5rem;
            align-items: start;
        }

        /* ── Sidebar ── */
        .ae-sidebar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .ae-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .ae-card-header {
            padding: 0.9rem 1.2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .ae-card-header-icon {
            width: 26px;
            height: 26px;
            background: var(--accent-light);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ae-card-header-icon svg {
            width: 13px;
            height: 13px;
            stroke: var(--accent);
        }

        .ae-card-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text2);
        }

        .ae-card-body {
            padding: 1rem 1.2rem;
        }

        /* Edit type options */
        .edit-type-list {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .edit-type-option {
            position: relative;
        }

        .edit-type-option input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .edit-type-option label {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 0.8rem;
            border: 1.5px solid var(--border);
            border-radius: 9px;
            cursor: pointer;
            background: var(--surface2);
            transition: all .15s;
        }

        .edit-type-option label:hover {
            border-color: var(--accent);
            background: var(--accent-light);
        }

        .edit-type-option input:checked+label {
            border-color: var(--accent);
            background: var(--accent-light);
            box-shadow: 0 0 0 3px rgba(59, 111, 245, 0.09);
        }

        .et-icon {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            background: #e8ecf2;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
        }

        .et-icon svg {
            width: 13px;
            height: 13px;
            stroke: var(--muted);
            transition: stroke .15s;
        }

        .edit-type-option input:checked+label .et-icon {
            background: var(--accent);
        }

        .edit-type-option input:checked+label .et-icon svg {
            stroke: #fff;
        }

        .et-text {
            line-height: 1.3;
        }

        .et-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text2);
            display: block;
            transition: color .15s;
        }

        .et-desc {
            font-size: 0.68rem;
            color: var(--muted);
            font-weight: 400;
        }

        .edit-type-option input:checked+label .et-label {
            color: var(--accent);
        }

        /* Stats card */
        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border);
            font-size: 0.78rem;
        }

        .stat-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .stat-row:first-child {
            padding-top: 0;
        }

        .stat-label {
            color: var(--muted);
            font-weight: 400;
        }

        .stat-val {
            font-weight: 700;
            color: var(--text);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
        }

        /* ── Main editor ── */
        .ae-main {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .ae-editor-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .editor-pane {
            display: flex;
            flex-direction: column;
        }

        .pane-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pane-action {
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--muted);
            background: none;
            border: none;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            align-items: center;
            gap: 0.3rem;
            transition: color .15s;
        }

        .pane-action svg {
            width: 11px;
            height: 11px;
            stroke: currentColor;
        }

        .pane-action:hover {
            color: var(--accent);
        }

        .ae-textarea {
            width: 100%;
            flex: 1;
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 11px;
            padding: 1rem 1.1rem;
            font-size: 0.88rem;
            font-weight: 400;
            line-height: 1.7;
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            resize: vertical;
            min-height: 280px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            box-shadow: var(--shadow);
        }

        .ae-textarea::placeholder {
            color: var(--muted);
        }

        .ae-textarea:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(59, 111, 245, 0.1);
        }

        .ae-textarea[readonly] {
            background: #fafbfd;
            color: var(--text2);
            cursor: default;
        }

        .ae-textarea[readonly]:focus {
            border-color: var(--border);
            box-shadow: var(--shadow);
        }

        /* output state */
        .ae-textarea.has-output {
            border-color: #b4dfc4;
            background: #fafff9;
        }

        /* ── Action bar ── */
        .ae-action-bar {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1rem 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .ae-action-left {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .ae-selected-type {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text2);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .ae-selected-type span {
            background: var(--accent-light);
            color: var(--accent);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.15rem 0.55rem;
            border-radius: 20px;
        }

        .ae-tip {
            font-size: 0.75rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .ae-tip svg {
            width: 13px;
            height: 13px;
            stroke: var(--muted);
            flex-shrink: 0;
        }

        .ae-action-right {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .btn-copy {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.6rem 1rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text2);
            background: var(--surface2);
            border: 1.5px solid var(--border);
            border-radius: 9px;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all .15s;
        }

        .btn-copy svg {
            width: 13px;
            height: 13px;
            stroke: currentColor;
        }

        .btn-copy:hover {
            color: var(--accent);
            border-color: var(--accent);
            background: var(--accent-light);
        }

        .btn-copy.copied {
            color: var(--green);
            border-color: #b4dfc4;
            background: var(--green-light);
        }

        .btn-copy:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .btn-improve {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.68rem 1.4rem;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 0.88rem;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(59, 111, 245, 0.28);
            transition: background .15s, transform .1s, box-shadow .15s;
        }

        .btn-improve svg {
            width: 15px;
            height: 15px;
            stroke: currentColor;
        }

        .btn-improve:hover:not(:disabled) {
            background: var(--accent-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(59, 111, 245, 0.35);
        }

        .btn-improve:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-improve:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Processing shimmer on output */
        @keyframes shimmer {
            0% {
                background-position: -400px 0;
            }

            100% {
                background-position: 400px 0;
            }
        }

        .ae-textarea.loading {
            background: linear-gradient(90deg, #f0f2f8 25%, #e8ecf4 50%, #f0f2f8 75%);
            background-size: 400px 100%;
            animation: shimmer 1.4s ease infinite;
            color: transparent;
            border-color: var(--border);
            pointer-events: none;
        }
    </style>

    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
