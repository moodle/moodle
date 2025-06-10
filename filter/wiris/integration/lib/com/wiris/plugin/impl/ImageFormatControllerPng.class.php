<?php

class com_wiris_plugin_impl_ImageFormatControllerPng implements com_wiris_plugin_api_ImageFormatController{
	public function __construct() { 
	}
	public function scalateMetrics($dpi, $metrics) {
		$f = 96 / $dpi;
		$metrics->set("width", intval($f * $metrics->get("width")));
		$metrics->set("height", intval($f * $metrics->get("height")));
		$metrics->set("baseline", intval($f * $metrics->get("baseline")));
	}
	public function getMetrics($bytes, &$output) {
		$output = $output;
		$width = 0;
		$height = 0;
		$dpi = 0;
		$baseline = 0;
		$bi = new haxe_io_BytesInput($bytes, null, null);
		$n = $bytes->length;
		$alloc = 10;
		$b = haxe_io_Bytes::alloc($alloc);
		$bi->readBytes($b, 0, 8);
		$n -= 8;
		while($n > 0) {
			$len = com_wiris_system_InputEx::readInt32_($bi);
			$typ = com_wiris_system_InputEx::readInt32_($bi);
			if($typ === 1229472850) {
				$width = com_wiris_system_InputEx::readInt32_($bi);
				$height = com_wiris_system_InputEx::readInt32_($bi);
				com_wiris_system_InputEx::readInt32_($bi);
				$bi->readByte();
			} else {
				if($typ === 1650545477) {
					$baseline = com_wiris_system_InputEx::readInt32_($bi);
				} else {
					if($typ === 1883789683) {
						$dpi = com_wiris_system_InputEx::readInt32_($bi);
						$dpi = Math::round($dpi / 39.37);
						com_wiris_system_InputEx::readInt32_($bi);
						$bi->readByte();
					} else {
						if($len > $alloc) {
							$alloc = $len;
							$b = haxe_io_Bytes::alloc($alloc);
						}
						$bi->readBytes($b, 0, $len);
					}
				}
			}
			com_wiris_system_InputEx::readInt32_($bi);
			$n -= $len + 12;
			unset($typ,$len);
		}
		$r = null;
		if($output !== null) {
			$output["width"] = "" . _hx_string_rec($width, "");
			$output["height"] = "" . _hx_string_rec($height, "");
			$output["baseline"] = "" . _hx_string_rec($baseline, "");
			if($dpi !== 96) {
				$output["dpi"] = "" . _hx_string_rec($dpi, "");
			}
			$r = "";
		} else {
			$r = "&cw=" . _hx_string_rec($width, "") . "&ch=" . _hx_string_rec($height, "") . "&cb=" . _hx_string_rec($baseline, "");
			if($dpi !== 96) {
				$r = $r . "&dpi=" . _hx_string_rec($dpi, "");
			}
		}
		return $r;
	}
	public function getContentType() {
		return "image/png";
	}
	function __toString() { return 'com.wiris.plugin.impl.ImageFormatControllerPng'; }
}
