<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Authenticate as CustomAuthenticate;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'realtor'      => \App\Http\Middleware\RealtorMiddleware::class,
            'admin'        => \App\Http\Middleware\AdminMiddleware::class,
            'agent'        => \App\Http\Middleware\AgentMiddleware::class,
            'agency'       => \App\Http\Middleware\AgencyMiddleware::class,
            'whatsapp.key' => \App\Http\Middleware\VerifyWhatsappApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        

    })->create();
