<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
->withRouting(
 web: __DIR__.'/../routes/web.php',
commands: __DIR__.'/../routes/console.php',
 health: '/up',
)
 ->withMiddleware(function (Middleware $middleware): void {
        
        // Configuration des alias de middlewares pour les routes
 $middleware->alias([

            
            'admin' => \App\Http\Middleware\AdminMiddleware::class, 
 ]);

        // Vous pouvez aussi définir des middlewares par groupe si nécessaire
        // $middleware->web(append: [
        //     \App\Http\Middleware\HandleInertiaRequests::class,
        // ]);

})
->withExceptions(function (Exceptions $exceptions): void {

})->create();