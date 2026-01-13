<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Diary Export</title>
    <style>
        @page {
            margin: 2cm;
            font-family: 'DejaVu Sans', sans-serif;
            /* Good for UTF-8 */
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #333;
        }

        .front-page {
            text-align: center;
            padding-top: 5cm;
            page-break-after: always;
        }

        .front-title {
            font-size: 36pt;
            color: #af8c3e;
            /* Gold-ish */
            font-weight: bold;
            margin-bottom: 1cm;
        }

        .front-dates {
            font-size: 18pt;
            color: #666;
            margin-bottom: 0.5cm;
        }

        .front-author {
            font-size: 14pt;
            font-style: italic;
        }

        .entry {
            margin-bottom: 2cm;
            page-break-inside: avoid;
        }

        .entry-header {
            border-bottom: 1px solid #af8c3e;
            padding-bottom: 0.2cm;
            margin-bottom: 0.5cm;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        .entry-date {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            /* Navy */
        }

        .entry-mood {
            font-size: 10pt;
            color: #666;
            margin-left: 10px;
        }

        .entry-content {
            margin-bottom: 0.5cm;
            white-space: pre-wrap;
            /* Preserve line breaks */
            text-align: justify;
        }

        .meta-section {
            font-size: 10pt;
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 0.5cm;
        }

        .meta-title {
            font-weight: bold;
            color: #af8c3e;
            font-size: 9pt;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .photos-grid {
            width: 100%;
            margin-top: 0.5cm;
        }

        .photo-container {
            display: inline-block;
            width: 48%;
            /* 2 per row roughly */
            margin-bottom: 10px;
            margin-right: 2%;
            vertical-align: top;
        }

        .photo-container img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .caption {
            font-size: 9pt;
            color: #666;
            font-style: italic;
            margin-top: 4px;
        }

        .children-status {
            margin-bottom: 5px;
        }

        .children-status span {
            font-weight: bold;
        }
    </style>
</head>

<body>
    @if(!empty($options['front_page']) && $options['front_page'])
        <div class="front-page">
            <div class="front-title">{{ $options['title'] ?? 'Diary Export' }}</div>
            <div class="front-dates">
                {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
            </div>
            <div class="front-author">
                {{ $user->name }}
            </div>
        </div>
    @endif

    @foreach($entries as $entry)
        <div class="entry">
            <div class="entry-header">
                <span class="entry-date">{{ \Carbon\Carbon::parse($entry->date)->format('l, d F Y') }}</span>
                @if($entry->mood)
                    <span class="entry-mood">({{ $entry->mood->name }})</span>
                @endif
            </div>

            <div class="entry-content">
                {{ $entry->content }}
            </div>

            @if($entry->childrenLogs->count() > 0)
                <div class="meta-section">
                    <div class="meta-title">Daily Status</div>
                    @foreach($entry->childrenLogs as $log)
                        <div class="children-status">
                            <span>{{ $log->person->name }}:</span> {{ $log->status }}
                            @if($log->notes)
                                <br><small><i>{{ $log->notes }}</i></small>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if($entry->photos->count() > 0)
                <div class="photos-grid">
                    @foreach($entry->photos as $photo)
                        @if($photo->src)
                            <div class="photo-container">
                                <img src="{{ $photo->src }}">
                                @if($photo->caption)
                                    <div class="caption">{{ $photo->caption }}</div>
                                @endif
                            </div>
                        @else
                            <div class="photo-container"
                                style="background: #eee; height: 100px; line-height: 100px; text-align: center; color: #999;">
                                Image failed to load
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</body>

</html>