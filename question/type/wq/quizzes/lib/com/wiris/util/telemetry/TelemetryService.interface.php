<?php

interface com_wiris_util_telemetry_TelemetryService {
	function setTestMode($testMode);
	function setLazyMode($lazyMode);
	function sendMessage($message);
	function getBatch();
}
