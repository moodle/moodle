<?php

interface com_wiris_util_telemetry_TelemetryLogger {
	function logWithParams($text, $parameters, $level);
	function log($text, $level);
}
