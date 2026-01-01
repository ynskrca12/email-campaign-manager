<?php

namespace App\Http\Controllers;

use App\Services\EmailTrackingService;
use Illuminate\Http\Request;

class EmailTrackingController extends Controller
{
    public function __construct(
        private EmailTrackingService $trackingService
    ) {}

    /**
     * Tracking pixel - Email açılma
     */
    public function pixel(string $hash, Request $request)
    {
        $this->trackingService->trackOpen($hash, $request);

        // 1x1 transparent pixel döndür
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($pixel)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Link tıklama
     */
    public function click(string $hash, Request $request)
    {
        $originalUrl = $this->trackingService->trackClick($hash, $request);

        if (!$originalUrl) {
            abort(404);
        }

        return redirect($originalUrl);
    }
}
