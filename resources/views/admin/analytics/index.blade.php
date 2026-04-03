@extends('layouts.admin')

@section('content')
    <div class="an-topbar">
        <div class="an-topbar-left">
            <div class="an-icon-wrap"><i data-feather="bar-chart-2"></i></div>
            <div>
                <div class="an-title">Analytics & Insights</div>
                <div class="an-sub">Track engagement and generate AI recommendations for better performance</div>
            </div>
        </div>
        <div class="an-badge">Chart.js Dashboard</div>
    </div>

    <div class="an-page">
        <div class="an-cards">
            <div class="an-card">
                <div class="an-card-label">Total Contents</div>
                <div class="an-card-value">{{ $totalContents }}</div>
            </div>
            <div class="an-card">
                <div class="an-card-label">Total Views</div>
                <div class="an-card-value">{{ $totalViews }}</div>
            </div>
            <div class="an-card">
                <div class="an-card-label">Total Likes</div>
                <div class="an-card-value">{{ $totalLikes }}</div>
            </div>
            <div class="an-card">
                <div class="an-card-label">Engagement Rate</div>
                <div class="an-card-value">{{ $engagementRate }}%</div>
            </div>
        </div>

        <div class="an-summary-grid">
            <div class="an-summary-card">
                <div class="an-summary-head">Top Performing Content</div>
                <div class="an-summary-title">{{ $topPerformingContent['title'] ?? 'N/A' }}</div>
                <div class="an-summary-meta">
                    Views: {{ $topPerformingContent['views'] ?? 0 }} |
                    Likes: {{ $topPerformingContent['likes'] ?? 0 }} |
                    Shares: {{ $topPerformingContent['shares'] ?? 0 }}
                </div>
            </div>
            <div class="an-summary-card">
                <div class="an-summary-head">Lowest Performing Content</div>
                <div class="an-summary-title">{{ $lowestPerformingContent['title'] ?? 'N/A' }}</div>
                <div class="an-summary-meta">
                    Views: {{ $lowestPerformingContent['views'] ?? 0 }} |
                    Likes: {{ $lowestPerformingContent['likes'] ?? 0 }} |
                    Shares: {{ $lowestPerformingContent['shares'] ?? 0 }}
                </div>
            </div>
        </div>

        <div class="an-chart-grid">
            <div class="an-chart-card">
                <div class="an-chart-title">Views Over Content</div>
                <canvas id="viewsChart"></canvas>
            </div>
            <div class="an-chart-card">
                <div class="an-chart-title">Likes vs Shares</div>
                <canvas id="engagementChart"></canvas>
            </div>
            <div class="an-chart-card an-chart-card-wide">
                <div class="an-chart-title">Top 5 Content by Engagement</div>
                <canvas id="topContentChart"></canvas>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body">
                <table id="analyticsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>Likes</th>
                            <th>Shares</th>
                            <th>Engagement Rate</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contents as $content)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $content['title'] }}</td>
                                <td>
                                    <span class="badge {{ $content['status'] === 'published' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($content['status']) }}
                                    </span>
                                </td>
                                <td class="analytics-views-cell">{{ $content['views'] }}</td>
                                <td class="analytics-likes-cell">{{ $content['likes'] }}</td>
                                <td class="analytics-shares-cell">{{ $content['shares'] }}</td>
                                <td class="analytics-rate-cell">{{ $content['engagement_rate'] }}%</td>
                                <td>
                                    <button class="btn btn-xs btn-outline-primary track-analytics-btn"
                                        data-id="{{ $content['id'] }}" data-event="view">View</button>
                                    <button class="btn btn-xs btn-outline-success track-analytics-btn"
                                        data-id="{{ $content['id'] }}" data-event="like">Like</button>
                                    <button class="btn btn-xs btn-outline-info track-analytics-btn"
                                        data-id="{{ $content['id'] }}" data-event="share">Share</button>
                                    <button class="btn btn-xs btn-warning generate-insight-btn"
                                        data-id="{{ $content['id'] }}"
                                        data-title="{{ htmlspecialchars($content['title'], ENT_QUOTES) }}">
                                        AI Insight
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            initResponsiveDataTable('#analyticsTable');

            const labels = @json($chartLabels);
            const viewsData = @json($viewsChartData);
            const likesData = @json($likesChartData);
            const sharesData = @json($sharesChartData);
            const topLabels = @json($topFiveContents->pluck('title')->map(fn($title) => \Illuminate\Support\Str::limit($title, 18))->values());
            const topScores = @json($topFiveContents->pluck('engagement_score')->values());

            new Chart(document.getElementById('viewsChart'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Views',
                        data: viewsData,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.12)',
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            new Chart(document.getElementById('engagementChart'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Likes',
                        data: likesData,
                        backgroundColor: '#16a34a'
                    }, {
                        label: 'Shares',
                        data: sharesData,
                        backgroundColor: '#0ea5e9'
                    }]
                },
                options: {
                    responsive: true
                }
            });

            new Chart(document.getElementById('topContentChart'), {
                type: 'bar',
                data: {
                    labels: topLabels,
                    datasets: [{
                        label: 'Engagement Score',
                        data: topScores,
                        backgroundColor: '#7c3aed'
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            $(document).on('click', '.track-analytics-btn', function() {
                let button = $(this);
                let row = button.closest('tr');
                let contentId = button.data('id');
                let eventType = button.data('event');

                $.ajax({
                    url: `/analytics/${contentId}/track`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        event_type: eventType
                    },
                    success: function(response) {
                        if (response.success) {
                            row.find('.analytics-views-cell').text(response.data.views);
                            row.find('.analytics-likes-cell').text(response.data.likes);
                            row.find('.analytics-shares-cell').text(response.data.shares);
                            row.find('.analytics-rate-cell').text(response.data.engagement_rate + '%');

                            Swal.fire({
                                icon: 'success',
                                title: 'Tracked',
                                text: response.message,
                                timer: 1000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Tracking failed.'
                        });
                    }
                });
            });

            $(document).on('click', '.generate-insight-btn', function() {
                let button = $(this);
                let contentId = button.data('id');
                let contentTitle = button.data('title');

                button.prop('disabled', true).text('Generating...');

                $.ajax({
                    url: `/analytics/${contentId}/generate-insight`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: contentTitle,
                                html: `
                                    <div style="text-align:left;">
                                        <div style="font-size:12px;color:#64748b;margin-bottom:10px;">AI Insight</div>
                                        <div style="padding:14px 16px;border:1px solid #dbe7ff;border-radius:14px;background:#f8fbff;color:#0f172a;line-height:1.7;">
                                            ${response.data.insight_text}
                                        </div>
                                    </div>
                                `,
                                width: '760px',
                                confirmButtonText: 'Close'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Insight generation failed.'
                        });
                    },
                    complete: function() {
                        button.prop('disabled', false).text('AI Insight');
                    }
                });
            });
        });
    </script>

    <style>
        .an-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 2rem;
            background: linear-gradient(90deg, #ffffff 0%, #f4f7ff 100%);
            border-bottom: 1px solid #dbe4f0;
        }

        .an-topbar-left {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .an-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.22);
        }

        .an-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .an-sub {
            font-size: 0.76rem;
            color: #64748b;
        }

        .an-badge {
            padding: 0.35rem 0.8rem;
            border-radius: 999px;
            background: #eef4ff;
            color: #2563eb;
            font-weight: 700;
            font-size: 0.75rem;
        }

        .an-page {
            padding: 1.5rem;
        }

        .an-cards,
        .an-summary-grid,
        .an-chart-grid {
            display: grid;
            gap: 1rem;
        }

        .an-cards {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            margin-bottom: 1rem;
        }

        .an-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-bottom: 1rem;
        }

        .an-chart-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .an-chart-card-wide {
            grid-column: 1 / -1;
        }

        .an-card,
        .an-summary-card,
        .an-chart-card {
            background: #fff;
            border: 1px solid #dbe4f0;
            border-radius: 18px;
            padding: 1.1rem 1.2rem;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
        }

        .an-card-label,
        .an-summary-head,
        .an-chart-title {
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.55rem;
        }

        .an-card-value {
            color: #0f172a;
            font-size: 1.8rem;
            font-weight: 800;
        }

        .an-summary-title {
            color: #0f172a;
            font-size: 1rem;
            font-weight: 700;
        }

        .an-summary-meta {
            color: #64748b;
            font-size: 0.84rem;
            margin-top: 0.3rem;
        }

        @media (max-width: 992px) {
            .an-cards,
            .an-summary-grid,
            .an-chart-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
