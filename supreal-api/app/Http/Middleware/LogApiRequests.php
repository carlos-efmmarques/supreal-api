<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        
        $logData = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'duration_ms' => $duration,
            'status_code' => $response->getStatusCode(),
            'request_body' => $this->sanitizeData($request->all()),
            'response_size' => strlen($response->getContent())
        ];

        if ($request->has('api_token')) {
            $logData['token_id'] = $request->api_token->id ?? null;
            $logData['token_name'] = $request->api_token->name ?? null;
        }

        Log::channel('daily')->info('API Request', $logData);

        return $response;
    }

    private function sanitizeData(array $data): array
    {
        $sensitiveKeys = ['password', 'token', 'secret', 'api_key', 'authorization'];
        
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveKeys)) {
                $data[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeData($value);
            }
        }
        
        return $data;
    }
}