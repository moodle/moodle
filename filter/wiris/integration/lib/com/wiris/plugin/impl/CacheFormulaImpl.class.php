<?php

class com_wiris_plugin_impl_CacheFormulaImpl extends com_wiris_plugin_impl_CacheImpl {
	public function __construct($conf) { if(!php_Boot::$skip_constructor) {
		parent::__construct($conf);
		$this->cacheFolder = $this->getAndCheckFolder(com_wiris_plugin_api_ConfigurationKeys::$FORMULA_FOLDER);
	}}
	function __toString() { return 'com.wiris.plugin.impl.CacheFormulaImpl'; }
}
