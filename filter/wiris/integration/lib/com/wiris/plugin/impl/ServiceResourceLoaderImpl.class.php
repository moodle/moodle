<?php

class com_wiris_plugin_impl_ServiceResourceLoaderImpl implements com_wiris_plugin_api_ServiceResourceLoader{
	public function __construct() { 
	}
	public function getContentType($name) {
		$ext = _hx_substr($name, _hx_last_index_of($name, ".", null) + 1, null);
		if($ext === "png") {
			return "image/png";
		} else {
			if($ext === "gif") {
				return "image/gif";
			} else {
				if($ext === "jpg" || $ext === "jpeg") {
					return "image/jpeg";
				} else {
					if($ext === "html" || $ext === "htm") {
						return "text/html";
					} else {
						if($ext === "css") {
							return "text/css";
						} else {
							if($ext === "js") {
								return "application/javascript";
							} else {
								if($ext === "txt") {
									return "text/plain";
								} else {
									if($ext === "ini") {
										return "text/plain";
									} else {
										return "application/octet-stream";
									}
								}
							}
						}
					}
				}
			}
		}
	}
	public function getContent($resource) {
		return com_wiris_system_Storage::newResourceStorage($resource)->read();
	}
	function __toString() { return 'com.wiris.plugin.impl.ServiceResourceLoaderImpl'; }
}
