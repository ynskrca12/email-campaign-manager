<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f4f4f4;">

    <table cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px;">

        <!-- Header -->
        <tr>
            <td style="background: #ffffff; padding: 20px 30px; text-align: center; border-bottom: 2px solid #f0f0f0;">
                <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                    <tr>
                        <td style="vertical-align: middle; padding-right: 8px;">
                            <img src="https://sigortayonetimsistemi.com/logosysnew.png"
                                 alt="Logo"
                                 width="32"
                                 height="32"
                                 style="display: block; width: 32px; height: 32px;">
                        </td>
                        <td style="vertical-align: middle;">
                            <span style="color: #333333; font-size: 20px; font-weight: bold;">{{ $fromName ?? 'Şirket Adı' }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Body -->
        <tr>
            <td style="padding: 15px; font-family: Arial, sans-serif; font-size: 15px; line-height: 1.6; color: #333;">
                {!! $body !!}
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e9ecef;">
                <p style="margin: 0; font-size: 12px; color: #6c757d;">
                    © {{ date('Y') }} {{ $fromName ?? 'Şirket Adı' }}. Tüm hakları saklıdır.
                </p>
            </td>
        </tr>

    </table>

    <!-- Tracking Pixel -->
    @if(isset($trackingPixelUrl))
        <img src="{{ $trackingPixelUrl }}" alt="" style="display:none;" width="1" height="1">
    @endif

</body>
</html>
