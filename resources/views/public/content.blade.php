<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content->title }}</title>
    <style>
        body {
            margin: 0;
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
            font-family: Georgia, "Times New Roman", serif;
            color: #1e293b;
        }

        .page {
            max-width: 860px;
            margin: 0 auto;
            padding: 56px 24px 72px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #dbe7ff;
            border-radius: 20px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.07);
            overflow: hidden;
        }

        .hero {
            padding: 36px 36px 24px;
            background: linear-gradient(180deg, #f8fbff 0%, #f1f6ff 100%);
            border-bottom: 1px solid #dbe7ff;
        }

        .badge {
            display: inline-block;
            margin-bottom: 14px;
            padding: 6px 12px;
            border-radius: 999px;
            background: {{ $content->status === 'published' ? '#dcfce7' : '#fef3c7' }};
            color: #0f172a;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 38px;
            line-height: 1.2;
            color: #0f172a;
        }

        .meta {
            font-size: 14px;
            color: #64748b;
        }

        .content {
            padding: 32px 36px 40px;
            white-space: pre-wrap;
            line-height: 1.9;
            font-size: 18px;
        }
    </style>
</head>

<body>
    <div class="page">
        <article class="card">
            <div class="hero">
                <div class="badge">{{ ucfirst($content->status) }}</div>
                <h1>{{ $content->title }}</h1>
                <div class="meta">Updated {{ $content->updated_at->format('d M Y, h:i A') }}</div>
            </div>
            <div class="content">{{ $content->content }}</div>
        </article>
    </div>
</body>

</html>
