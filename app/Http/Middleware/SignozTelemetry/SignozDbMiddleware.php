<?php

namespace App\Http\Middleware\SignozTelemetry;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use OpenTelemetry\SDK\Trace\Tracer;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;

class SignozDbMiddleware
{

    protected $tracer;

    public function __construct(Tracer $tracer)
    {
        $this->tracer = $tracer;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $dbSpan = $tracer->spanBuilder('db_query')->startSpan();

        $dbSpan->setAttribute('request_method', $request->method());
        $dbSpan->setAttribute('request_path', $request->path());

        try {
            // Register an event listener for database queries
            DB::listen(function ($query) use ($dbSpan) {
                // Add attributes to the database span
                $dbSpan->setAttribute('query', $query->sql);
                $dbSpan->setAttribute('bindings', json_encode($query->bindings));
            });

            // Continue processing the request
            return $next($request);
        } catch (\Exception $e) {
            // Handle exceptions if necessary
            $dbSpan->recordException($e);

            Log::error($e->getMessage());
            throw $e;
        } finally {
            // End the database span when the request is complete
            $dbSpan->end();
        }

            
    }

}
