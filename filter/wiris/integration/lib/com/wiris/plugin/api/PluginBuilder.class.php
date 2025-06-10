<?php

class com_wiris_plugin_api_PluginBuilder {
	public function __construct() { 
	}
	public function getAccessProvider() {
		return null;
	}
	public function setAccessProvider($provider) {
	}
	public function getCustomParamsProvider() {
		return null;
	}
	public function setCustomParamsProvider($provider) {
	}
	public function newGenericParamsProvider($properties) {
		return null;
	}
	public function getImageFormatController() {
		return null;
	}
	public function isEditorLicensed() {
		return false;
	}
	public function newResourceLoader() {
		return null;
	}
	public function newCleanCache() {
		return null;
	}
	public function addCorsHeaders($response, $origin) {
	}
	public function newEditor() {
		return null;
	}
	public function newCas() {
		return null;
	}
	public function newTest() {
		return null;
	}
	public function setStorageAndCacheCacheFormulaObject($cache) {
	}
	public function setStorageAndCacheCacheObject($cache) {
	}
	public function setStorageAndCacheInitObject($obj) {
	}
	public function getStorageAndCache() {
		return null;
	}
	public function getConfiguration() {
		return null;
	}
	public function newAsyncTextService() {
		return null;
	}
	public function newTextService() {
		return null;
	}
	public function newAsyncRender() {
		return null;
	}
	public function newRender() {
		return null;
	}
	public function setStorageAndCache($store) {
	}
	public function addConfigurationUpdater($conf) {
	}
	static $pb = null;
	static function getInstance() {
		if(com_wiris_plugin_api_PluginBuilder::$pb === null) {
			com_wiris_plugin_api_PluginBuilder::$pb = new com_wiris_plugin_impl_PluginBuilderImpl();
		}
		return com_wiris_plugin_api_PluginBuilder::$pb;
	}
	static function newInstance() {
		return new com_wiris_plugin_impl_PluginBuilderImpl();
	}
	function __toString() { return 'com.wiris.plugin.api.PluginBuilder'; }
}
