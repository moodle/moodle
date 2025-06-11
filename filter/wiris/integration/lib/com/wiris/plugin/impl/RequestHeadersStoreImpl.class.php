<?php

class com_wiris_plugin_impl_RequestHeadersStoreImpl implements com_wiris_plugin_api_RequestHeadersStore{
	public function __construct(){}
	public function store($headers) {
		com_wiris_plugin_impl_RequestHeadersStoreImpl::$headers = $headers;
	}
	static $headers;
	static function dumpInto($h) {
		if(com_wiris_plugin_impl_RequestHeadersStoreImpl::$headers !== null) {
			$headerNames = com_wiris_plugin_impl_RequestHeadersStoreImpl::$headers->keys();
			while($headerNames->hasNext()) {
				$name = $headerNames->next();
				$h->setHeader($name, com_wiris_plugin_impl_RequestHeadersStoreImpl::$headers->get($name));
				unset($name);
			}
		}
	}
	function __toString() { return 'com.wiris.plugin.impl.RequestHeadersStoreImpl'; }
}
