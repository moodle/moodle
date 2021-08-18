<?php

interface com_wiris_plugin_api_ImageFormatController {
	function scalateMetrics($dpi, $metrics);
	function getMetrics($bytes, &$output);
	function getContentType();
}
