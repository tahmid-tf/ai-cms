<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $content->title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1e293b;
            margin: 32px;
            line-height: 1.7;
        }

        .header {
            border-bottom: 2px solid #dbe7ff;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .title {
            font-size: 26px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .meta {
            font-size: 12px;
            color: #64748b;
        }

        .content {
            white-space: pre-wrap;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">{{ $content->title }}</div>
        <div class="meta">Author: {{ $author }}</div>
        <div class="meta">Date: {{ $exportedAt->format('d M Y, h:i A') }}</div>
        <div class="meta">Status: {{ ucfirst($content->status) }}</div>
    </div>

    <div class="content">{{ $content->content }}</div>
</body>

</html>
