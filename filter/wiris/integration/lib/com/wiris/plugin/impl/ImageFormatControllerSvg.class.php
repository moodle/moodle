<?php

class com_wiris_plugin_impl_ImageFormatControllerSvg implements com_wiris_plugin_api_ImageFormatController{
	public function __construct() { 
	}
	public function scalateMetrics($dpi, $metrics) {
	}
	public function getMetrics($bytes, &$output) {
		$svg = $bytes->toString();
		$svgRoot = _hx_substr($svg, 0, _hx_index_of($svg, ">", null));
		$firstIndex = _hx_index_of($svgRoot, "height=", null) + 8;
		$endIndex = _hx_index_of($svgRoot, "\"", $firstIndex);
		$height = _hx_substr($svgRoot, $firstIndex, $endIndex - $firstIndex);
		$firstIndex = _hx_index_of($svgRoot, "width=", null) + 7;
		$endIndex = _hx_index_of($svgRoot, "\"", $firstIndex);
		$width = _hx_substr($svgRoot, $firstIndex, $endIndex - $firstIndex);
		$firstIndex = _hx_index_of($svgRoot, "wrs:baseline=", null) + 14;
		$endIndex = _hx_index_of($svgRoot, "\"", $firstIndex);
		$baseline = _hx_substr($svgRoot, $firstIndex, $endIndex - $firstIndex);
		$output = $output;
		$r = null;
		if($output !== null) {
			$output["width"] = "" . $width;
			$output["height"] = "" . $height;
			$output["baseline"] = "" . $baseline;
			$r = "";
		} else {
			$r = "&cw=" . $width . "&ch=" . $height . "&cb=" . $baseline;
		}
		return $r;
	}
	public function getContentType() {
		return "image/svg+xml";
	}
	function __toString() { return 'com.wiris.plugin.impl.ImageFormatControllerSvg'; }
}
