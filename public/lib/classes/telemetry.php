<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace core;

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\LocalRootSpan;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\Context\Context;

/**
 * OpenTelemetry Telemetry manager class for Moodle.
 *
 * This class acts as a central point for managing telemetry in Moodle,
 * providing methods to initialize the telemetry system, get the current page ID, and record exceptions.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class telemetry {
    /**
     * Get the page ID for the current request.
     *
     * If OpenTelemetry is not configured, or there is no current root span, this will return null.
     *
     * @return string|null
     */
    public static function get_page_id(): ?string {
        if (!static::is_available()) {
            return null;
        }

        // Fetch the current root span, and return the trace ID as the page ID.
        $rootspan = LocalRootSpan::current();
        $rootcontext = $rootspan->getContext();
        if ($rootcontext->isValid() === false) {
            return null;
        }

        return $rootcontext->getTraceId();
    }

    /**
     * A helper method to get the trace id for the current request.
     *
     * @return string|null Null if OpenTelemetry is not configured
     */
    public static function get_trace_parent_id(): ?string {
        if (!static::is_available()) {
            return null;
        }

        $headers = [];
        Globals::propagator()->inject($headers);

        return $headers[TraceContextPropagator::TRACEPARENT] ?? null;
    }

    /**
     * Whether Telemetry is available and configured.
     *
     * @return bool
     */
    public static function is_available(): bool {
        if (!class_exists(Globals::class)) {
            return false;
        }

        if (extension_loaded('opentelemetry') === false) {
            return false;
        }

        return true;
    }

    /**
     * Record an error, or exception to the current span.
     *
     * @param \Throwable $ex The exception to record.
     */
    public static function record_throwable(\Throwable $ex): void {
        if (!static::is_available()) {
            return;
        }

        $span = Span::fromContext(Context::getCurrent());
        $span->recordException($ex);
    }
}
