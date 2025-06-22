protected $middleware = [
    // ...
    \App\Http\Middleware\HandleCors::class,
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
];
