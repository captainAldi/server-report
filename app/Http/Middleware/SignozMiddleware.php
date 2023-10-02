<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Facades\Support\Log;

use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;


class SignozMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $transport = (new OtlpHttpTransportFactory())->create($otlpHttpEndpoint, 'application/x-protobuf');
        $exporter = new SpanExporter($transport);

        echo 'Starting OTLP example';

        $tracerProvider =  new TracerProvider(
            new SimpleSpanProcessor(
                $exporter
            )
        );
        $tracer = $tracerProvider->getTracer('io.signoz.examples.php');

        $root = $span = $tracer->spanBuilder('root')->startSpan();
        $scope = $span->activate();

        try {
            // Execute the next middleware in the pipeline and the controller
            $response = $next($request);
            
            // You can add attributes and events here as needed
            
            return $response;
        } catch (\Exception $e) {
            // Handle exceptions if necessary
            $span->recordException($e);
            Log::error($e->getMessage());
            throw $e;
        } finally {
            // Always end the span to avoid leaks
            $span->end();

            $root->end();
            $scope->detach();

            $tracerProvider->shutdown();
        }
    }
}
