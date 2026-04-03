@extends('layouts.admin')

@section('content')
    @php
        $user = auth()->user();
        $isAdmin = $user?->hasRole('admin');
        $canEditRecords = $user?->hasAnyRole(['admin', 'editor']);
        $canViewLists = $user?->hasAnyRole(['admin', 'editor', 'viewer']);
    @endphp
    <main>
        <div class="db-shell">
            <section class="db-hero">
                <div class="db-hero-copy">
                    <div class="db-kicker">{{ $isAdmin ? 'Admin Overview' : ($canEditRecords ? 'Editor Overview' : 'Viewer Overview') }}</div>
                    <h1>AI CMS command center for content, performance, and publishing.</h1>
                    <p>
                        Monitor AI workflows, editorial activity, audience engagement, and publishing momentum from a
                        single dashboard built around your real project data.
                    </p>
                    <div class="db-hero-actions">
                        @if ($isAdmin)
                            <a href="{{ route('ai.content') }}" class="db-btn db-btn-primary">Generate Content</a>
                            <a href="{{ route('analytics.index') }}" class="db-btn db-btn-light">Open Analytics</a>
                        @elseif ($canEditRecords)
                            <a href="{{ route('version_control.list') }}" class="db-btn db-btn-primary">Open Content List</a>
                            <a href="{{ route('analytics.insights_list') }}" class="db-btn db-btn-light">Open Insights</a>
                        @else
                            <a href="{{ route('ai.content.list') }}" class="db-btn db-btn-primary">Browse Content</a>
                            <a href="{{ route('version_control.list') }}" class="db-btn db-btn-light">View Versions</a>
                        @endif
                    </div>
                </div>
                <div class="db-hero-panel">
                    <div class="db-panel-card db-panel-primary">
                        <div class="db-panel-label">Engagement Rate</div>
                        <div class="db-panel-value">{{ $engagementRate }}%</div>
                        <div class="db-panel-meta">Based on likes and shares against total views</div>
                    </div>
                    <div class="db-panel-grid">
                        <div class="db-panel-card">
                            <div class="db-panel-label">Drafts</div>
                            <div class="db-panel-value">{{ $draftCount }}</div>
                        </div>
                        <div class="db-panel-card">
                            <div class="db-panel-label">Published</div>
                            <div class="db-panel-value">{{ $publishedCount }}</div>
                        </div>
                        <div class="db-panel-card">
                            <div class="db-panel-label">Total Views</div>
                            <div class="db-panel-value">{{ $totalViews }}</div>
                        </div>
                        <div class="db-panel-card">
                            <div class="db-panel-label">Insights</div>
                            <div class="db-panel-value">{{ $totalInsights }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="db-metrics">
                <article class="db-metric-card">
                    <div class="db-metric-head">
                        <span>Total Users</span>
                        <i data-feather="users"></i>
                    </div>
                    <div class="db-metric-value">{{ $totalUsers }}</div>
                    <div class="db-metric-foot">Registered admin-side user accounts</div>
                </article>
                <article class="db-metric-card">
                    <div class="db-metric-head">
                        <span>Generated Content</span>
                        <i data-feather="zap"></i>
                    </div>
                    <div class="db-metric-value">{{ $totalGeneratedContents }}</div>
                    <div class="db-metric-foot">AI-assisted content generation records</div>
                </article>
                <article class="db-metric-card">
                    <div class="db-metric-head">
                        <span>Edited Content</span>
                        <i data-feather="edit-3"></i>
                    </div>
                    <div class="db-metric-value">{{ $totalEditedContents }}</div>
                    <div class="db-metric-foot">Saved editing sessions and refinements</div>
                </article>
                <article class="db-metric-card">
                    <div class="db-metric-head">
                        <span>Translations</span>
                        <i data-feather="globe"></i>
                    </div>
                    <div class="db-metric-value">{{ $totalTranslations }}</div>
                    <div class="db-metric-foot">Language conversion records across supported locales</div>
                </article>
            </section>

            <section class="db-grid">
                <article class="db-card db-card-wide">
                    <div class="db-card-head">
                        <div>
                            <div class="db-card-kicker">Workflow Activity</div>
                            <h3>Module distribution</h3>
                        </div>
                    </div>
                    <div class="db-module-stack">
                        @php
                            $maxModuleValue = collect($moduleStats)->max('value') ?: 1;
                        @endphp
                        @foreach ($moduleStats as $module)
                            <div class="db-module-row">
                                <div class="db-module-meta">
                                    <span>{{ $module['label'] }}</span>
                                    <strong>{{ $module['value'] }}</strong>
                                </div>
                                <div class="db-module-bar">
                                    <span style="width: {{ ($module['value'] / $maxModuleValue) * 100 }}%; background: {{ $module['color'] }};"></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article class="db-card">
                    <div class="db-card-head">
                        <div>
                            <div class="db-card-kicker">Engagement Totals</div>
                            <h3>Audience interaction</h3>
                        </div>
                    </div>
                    <div class="db-stat-list">
                        <div class="db-stat-item">
                            <span>Views</span>
                            <strong>{{ $totalViews }}</strong>
                        </div>
                        <div class="db-stat-item">
                            <span>Likes</span>
                            <strong>{{ $totalLikes }}</strong>
                        </div>
                        <div class="db-stat-item">
                            <span>Shares</span>
                            <strong>{{ $totalShares }}</strong>
                        </div>
                    </div>
                </article>
            </section>

            <section class="db-grid">
                <article class="db-card">
                    <div class="db-card-head">
                        <div>
                            <div class="db-card-kicker">Publishing</div>
                            <h3>Recent version-controlled content</h3>
                        </div>
                        @if ($canViewLists)
                            <a href="{{ route('version_control.list') }}" class="db-inline-link">View all</a>
                        @endif
                    </div>
                    <div class="db-list">
                        @forelse ($recentContents as $content)
                            <div class="db-list-item">
                                <div>
                                    <div class="db-list-title">{{ $content->title }}</div>
                                    <div class="db-list-meta">{{ $content->updated_at->format('d M Y, h:i A') }}</div>
                                </div>
                                <span class="db-badge {{ $content->status === 'published' ? 'is-success' : 'is-warning' }}">
                                    {{ ucfirst($content->status) }}
                                </span>
                            </div>
                        @empty
                            <div class="db-empty">No content records yet.</div>
                        @endforelse
                    </div>
                </article>

                <article class="db-card">
                    <div class="db-card-head">
                        <div>
                            <div class="db-card-kicker">AI Insights</div>
                            <h3>Latest recommendations</h3>
                        </div>
                        @if ($canViewLists)
                            <a href="{{ route('analytics.insights_list') }}" class="db-inline-link">{{ $canEditRecords ? 'Manage' : 'View list' }}</a>
                        @endif
                    </div>
                    <div class="db-list">
                        @forelse ($recentInsights as $insight)
                            <div class="db-insight-item">
                                <div class="db-insight-dot"></div>
                                <div>{{ \Illuminate\Support\Str::limit($insight->insight_text, 120) }}</div>
                            </div>
                        @empty
                            <div class="db-empty">No insights generated yet.</div>
                        @endforelse
                    </div>
                </article>

                <article class="db-card">
                    <div class="db-card-head">
                        <div>
                            <div class="db-card-kicker">Localization</div>
                            <h3>Recent translations</h3>
                        </div>
                        @if ($canViewLists)
                            <a href="{{ route('ai_translation.list') }}" class="db-inline-link">Open list</a>
                        @endif
                    </div>
                    <div class="db-list">
                        @forelse ($recentTranslations as $translation)
                            <div class="db-list-item db-list-item-stack">
                                <div class="db-list-title">{{ ucfirst($translation->target_language) }}</div>
                                <div class="db-list-meta">
                                    {{ \Illuminate\Support\Str::limit($translation->translated_content, 90) }}
                                </div>
                            </div>
                        @empty
                            <div class="db-empty">No translations available yet.</div>
                        @endforelse
                    </div>
                </article>
            </section>

            <section class="db-grid">
                <article class="db-card db-card-wide">
                    <div class="db-card-head">
                        <div>
                            <div class="db-card-kicker">Quick Access</div>
                            <h3>Jump into key workflows</h3>
                        </div>
                    </div>
                    <div class="db-quick-grid">
                        @if ($isAdmin)
                            <a href="{{ route('ai.content') }}" class="db-quick-card">
                                <i data-feather="file-text"></i>
                                <span>Content Generation</span>
                            </a>
                            <a href="{{ route('ai_editor.editor') }}" class="db-quick-card">
                                <i data-feather="edit"></i>
                                <span>Content Editing</span>
                            </a>
                            <a href="{{ route('ai_translation.index') }}" class="db-quick-card">
                                <i data-feather="languages"></i>
                                <span>Translation</span>
                            </a>
                        @endif
                        <a href="{{ route('version_control.list') }}" class="db-quick-card">
                            <i data-feather="git-branch"></i>
                            <span>Version Control</span>
                        </a>
                        @if ($isAdmin)
                            <a href="{{ route('analytics.index') }}" class="db-quick-card">
                                <i data-feather="bar-chart-2"></i>
                                <span>Analytics</span>
                            </a>
                        @else
                            <a href="{{ route('analytics.insights_list') }}" class="db-quick-card">
                                <i data-feather="message-square"></i>
                                <span>Insights List</span>
                            </a>
                        @endif
                        <a href="{{ route('export_sharing.index') }}" class="db-quick-card">
                            <i data-feather="share-2"></i>
                            <span>Export & Sharing</span>
                        </a>
                    </div>
                </article>
            </section>
        </div>
    </main>
@endsection

@push('scripts')
    <style>
        :root {
            --db-bg: #f5f7fb;
            --db-surface: #ffffff;
            --db-border: #dde5f0;
            --db-text: #102033;
            --db-muted: #6b7a90;
            --db-primary: #1d4ed8;
            --db-primary-soft: #eef4ff;
            --db-green: #0f766e;
            --db-orange: #ea580c;
            --db-purple: #7c3aed;
            --db-shadow: 0 18px 45px rgba(15, 23, 42, 0.07);
        }

        .db-shell {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .db-hero {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 1rem;
            padding: 1.5rem;
            border: 1px solid var(--db-border);
            border-radius: 26px;
            background:
                radial-gradient(circle at top right, rgba(29, 78, 216, 0.12), transparent 32%),
                linear-gradient(135deg, #ffffff 0%, #f7faff 100%);
            box-shadow: var(--db-shadow);
        }

        .db-hero-copy {
            padding-top: 0.85rem;
        }

        .db-kicker,
        .db-card-kicker {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--db-primary);
        }

        .db-hero h1 {
            margin: 0.75rem 0 0.8rem;
            font-size: clamp(2rem, 4vw, 3.25rem);
            line-height: 1.02;
            letter-spacing: -0.04em;
            color: var(--db-text);
        }

        .db-hero p {
            margin: 0;
            max-width: 58ch;
            color: var(--db-muted);
            font-size: 0.98rem;
            line-height: 1.8;
        }

        .db-hero-actions {
            margin-top: 1.25rem;
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .db-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 1.15rem;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
        }

        .db-btn-primary {
            background: var(--db-primary);
            color: #fff;
        }

        .db-btn-light {
            background: #edf2f9;
            color: var(--db-text);
        }

        .db-hero-panel,
        .db-panel-grid {
            display: grid;
            gap: 0.85rem;
        }

        .db-panel-card {
            padding: 1rem 1.1rem;
            border: 1px solid var(--db-border);
            border-radius: 18px;
            background: var(--db-surface);
        }

        .db-panel-primary {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            border-color: transparent;
            color: #fff;
        }

        .db-panel-label {
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            opacity: 0.9;
        }

        .db-panel-value {
            margin-top: 0.35rem;
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .db-panel-meta {
            margin-top: 0.25rem;
            font-size: 0.82rem;
            line-height: 1.5;
            opacity: 0.9;
        }

        .db-panel-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .db-metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .db-metric-card,
        .db-card {
            background: var(--db-surface);
            border: 1px solid var(--db-border);
            border-radius: 22px;
            box-shadow: var(--db-shadow);
        }

        .db-metric-card {
            padding: 1.1rem 1.15rem;
        }

        .db-metric-head {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            color: var(--db-muted);
            font-size: 0.8rem;
            font-weight: 700;
        }

        .db-metric-head svg {
            width: 18px;
            height: 18px;
            color: var(--db-primary);
        }

        .db-metric-value {
            margin-top: 0.65rem;
            font-size: 2rem;
            font-weight: 800;
            color: var(--db-text);
            letter-spacing: -0.04em;
        }

        .db-metric-foot {
            margin-top: 0.3rem;
            color: var(--db-muted);
            font-size: 0.8rem;
            line-height: 1.55;
        }

        .db-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }

        .db-card {
            padding: 1.15rem;
        }

        .db-card-wide {
            grid-column: span 2;
        }

        .db-card-head {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .db-card-head h3 {
            margin: 0.25rem 0 0;
            font-size: 1rem;
            font-weight: 800;
            color: var(--db-text);
        }

        .db-inline-link {
            color: var(--db-primary);
            text-decoration: none;
            font-size: 0.84rem;
            font-weight: 700;
        }

        .db-module-stack,
        .db-list {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .db-module-row {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .db-module-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.87rem;
            color: var(--db-text);
            font-weight: 700;
        }

        .db-module-bar {
            width: 100%;
            height: 12px;
            border-radius: 999px;
            background: #edf2f8;
            overflow: hidden;
        }

        .db-module-bar span {
            display: block;
            height: 100%;
            border-radius: inherit;
        }

        .db-stat-list {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .db-stat-item,
        .db-list-item {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            padding: 0.95rem 1rem;
            border-radius: 16px;
            background: #f8fbff;
            border: 1px solid #e6edf7;
        }

        .db-list-item-stack {
            align-items: flex-start;
            flex-direction: column;
        }

        .db-stat-item span,
        .db-list-meta {
            color: var(--db-muted);
            font-size: 0.84rem;
        }

        .db-stat-item strong {
            color: var(--db-text);
            font-size: 1.05rem;
        }

        .db-list-title {
            color: var(--db-text);
            font-weight: 700;
            font-size: 0.92rem;
        }

        .db-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.32rem 0.72rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .db-badge.is-success {
            background: #dcfce7;
            color: #166534;
        }

        .db-badge.is-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .db-insight-item {
            display: grid;
            grid-template-columns: 10px 1fr;
            gap: 0.8rem;
            padding: 0.95rem 1rem;
            border-radius: 16px;
            background: #fff8f1;
            border: 1px solid #fde6d3;
            color: #7c2d12;
            line-height: 1.65;
            font-size: 0.88rem;
        }

        .db-insight-dot {
            width: 10px;
            height: 10px;
            margin-top: 0.42rem;
            border-radius: 999px;
            background: var(--db-orange);
        }

        .db-quick-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.9rem;
        }

        .db-quick-card {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 1rem;
            text-decoration: none;
            color: var(--db-text);
            border: 1px solid #e5ebf4;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            font-weight: 700;
        }

        .db-quick-card svg {
            width: 18px;
            height: 18px;
            color: var(--db-primary);
        }

        .db-empty {
            padding: 1rem;
            border-radius: 16px;
            background: #f8fbff;
            color: var(--db-muted);
            font-size: 0.86rem;
        }

        @media (max-width: 1200px) {
            .db-metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .db-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .db-quick-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 992px) {
            .db-hero,
            .db-grid,
            .db-card-wide {
                grid-template-columns: 1fr;
            }

            .db-card-wide {
                grid-column: auto;
            }
        }

        @media (max-width: 768px) {
            .db-shell {
                padding: 1rem;
            }

            .db-metrics,
            .db-panel-grid,
            .db-quick-grid {
                grid-template-columns: 1fr;
            }

            .db-hero {
                padding: 1.15rem;
            }
        }
    </style>
@endpush
