<?php

declare(strict_types=1);

namespace Effulgence\Laravel\Middleware;

use Closure;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function Constructo\Cast\stringify;

class CorsMiddleware
{
    public function __construct(private readonly ConfigRepository $config)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $origin = stringify($this->config->get('effulgence.cors.allow_origin', '*'));

        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set(
            'Access-Control-Allow-Headers',
            'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization'
        );
        $response->headers->set('Access-Control-Allow-Methods', '*');

        return $response;
    }
}
