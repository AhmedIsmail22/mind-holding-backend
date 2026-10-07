<?php

namespace App\Services\Redirects;

use App\Models\Redirect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Tells the frontend a redirect rule changed, so it can refresh whatever it
 * caches without waiting for a redeploy. A no-op when FRONTEND_REVALIDATE_URL
 * isn't set (nothing is configured yet), and a failure is logged rather than
 * surfaced - the redirect itself is already saved by the time this runs, same
 * reasoning as LeadNotificationService for mail.
 */
class FrontendRevalidationNotifier
{
    public function created(Redirect $redirect): void
    {
        $this->notify('created', $redirect->old_path, $redirect->new_path);
    }

    public function updated(Redirect $redirect, string $previousOldPath, string $previousNewPath): void
    {
        $this->notify('updated', $redirect->old_path, $redirect->new_path, $previousOldPath, $previousNewPath);
    }

    public function deleted(string $oldPath, string $newPath): void
    {
        $this->notify('deleted', $oldPath, $newPath);
    }

    private function notify(string $event, string $oldPath, string $newPath, ?string $previousOldPath = null, ?string $previousNewPath = null): void
    {
        $url = config('services.frontend.revalidate_url');

        if (empty($url)) {
            return;
        }

        $payload = [
            'event' => $event,
            'redirect' => ['old_path' => $oldPath, 'new_path' => $newPath],
            'previous' => $previousOldPath !== null
                ? ['old_path' => $previousOldPath, 'new_path' => $previousNewPath]
                : null,
        ];

        try {
            $response = Http::timeout(3)
                ->withHeader('X-Revalidate-Secret', (string) config('services.frontend.revalidate_secret'))
                ->post($url, $payload);

            if (! $response->successful()) {
                Log::warning('Frontend revalidation call failed.', ['payload' => $payload, 'status' => $response->status()]);
            }
        } catch (Throwable $e) {
            Log::warning('Frontend revalidation call failed.', ['payload' => $payload, 'error' => $e->getMessage()]);
        }
    }
}
