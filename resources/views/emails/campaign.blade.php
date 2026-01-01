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
            background-color: #f4f4f4;
        }
        .email-container {
            background: #ffffff;
            border-radius: 8px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            text-align: center;
        }
        .company-name {
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .email-body {
            padding: 30px;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1 class="company-name">{{ $fromName ?? 'Şirket Adı' }}</h1>
        </div>

        <!-- Body -->
        <div class="email-body">
            @if($recipientName)
                <p style="font-size: 16px; color: #333;">Sayın {{ $recipientName }},</p>
            @endif

            <div style="margin-top: 20px; white-space: pre-line;">
                {!! $processedBody !!}
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p style="margin: 0;">© {{ date('Y') }} {{ $fromName ?? 'Şirket Adı' }}. Tüm hakları saklıdır.</p>
        </div>
    </div>

    <!-- Tracking Pixel -->
    @if(isset($trackingPixelUrl))
        <img src="{{ $trackingPixelUrl }}" alt="" style="display:none;" width="1" height="1">
    @endif
</body>
</html>
