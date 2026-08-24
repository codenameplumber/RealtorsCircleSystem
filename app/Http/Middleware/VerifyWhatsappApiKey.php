<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyWhatsappApiKey
{
    /**
     * Gate the /api/wa/v1/* surface behind a single shared secret — n8n is the
     * only caller, so there's no per-user auth here, just proof the request
     * really came from n8n and not the open internet.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expected = config('services.whatsapp_api.key');
        $provided = $request->header('X-API-Key');

        if (! $expected || ! $provided || ! hash_equals($expected, $provided)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or missing API key.'], 401);
        }

        return $next($request);
    }
}
