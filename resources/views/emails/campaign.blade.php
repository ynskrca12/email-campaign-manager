<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-container {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
        }
        .email-body {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        @if($recipientName)
            <p>Sayın {{ $recipientName }},</p>
        @endif

        <div class="email-body">
            {!! nl2br(e($body)) !!}
        </div>
    </div>
</body>
</html>
