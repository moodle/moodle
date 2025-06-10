<?php

class com_wiris_plugin_api_ConfigurationKeys {
	public function __construct(){}
	static $DEBUG = "wirisdebug";
	static $FORMULA_FOLDER = "wirisformuladirectory";
	static $CACHE_FOLDER = "wiriscachedirectory";
	static $INTEGRATION_PATH = "wirisintegrationpath";
	static $EDITOR_PARAMETERS_LIST = "wiriseditorparameterslist";
	static $STORAGE_CLASS = "wirisstorageclass";
	static $CONFIGURATION_CLASS = "wirisconfigurationclass";
	static $CONFIGURATION_PATH = "wirisconfigurationpath";
	static $ACCESSPROVIDER_CLASS = "wirisaccessproviderclass";
	static $CONTEXT_PATH = "wiriscontextpath";
	static $SERVICE_PROTOCOL = "wirisimageserviceprotocol";
	static $SERVICE_PORT = "wirisimageserviceport";
	static $SERVICE_HOST = "wirisimageservicehost";
	static $SERVICE_PATH = "wirisimageservicepath";
	static $CAS_LANGUAGES = "wiriscaslanguages";
	static $CAS_CODEBASE = "wiriscascodebase";
	static $CAS_ARCHIVE = "wiriscasarchive";
	static $CAS_CLASS = "wiriscasclass";
	static $CAS_WIDTH = "wiriscaswidth";
	static $CAS_HEIGHT = "wiriscasheight";
	static $SHOWIMAGE_PATH = "wirishowimagepath";
	static $SHOWCASIMAGE_PATH = "wirishowcasimagepath";
	static $CLEAN_CACHE_PATH = "wiriscleancachepath";
	static $RESOURCE_PATH = "wirisresourcespath";
	static $LATEX_TO_MATHML_URL = "wirislatextomathmlurl";
	static $SAVE_MODE = "wiriseditorsavemode";
	static $EDITOR_TOOLBAR = "wiriseditortoolbar";
	static $HOST_PLATFORM = "wirishostplatform";
	static $VERSION_PLATFORM = "wirisversionplatform";
	static $WIRIS_DPI = "wirisimagedpi";
	static $FONT_FAMILY = "wirisfontfamily";
	static $FILTER_OUTPUT_MATHML = "wirisfilteroutputmathml";
	static $SAVE_MATHML_SEMANTICS = "wirissavehandtraces";
	static $EDITOR_MATHML_ATTRIBUTE = "wiriseditormathmlattribute";
	static $EDITOR_PARAMS = "wiriseditorparameters";
	static $EDITOR_PARAMETERS_DEFAULT_LIST = "mml,color,centerbaseline,zoom,dpi,fontSize,fontFamily,defaultStretchy,backgroundColor,format,saveLatex";
	static $EDITOR_PARAMETERS_NOTRENDER_LIST = "toolbar, toolbarHidden, reservedWords, autoformat, mml, language, rtlLanguages, ltrLanguages, arabicIndicLanguages, easternArabicIndicLanguages, europeanLanguages";
	static $HTTPPROXY = "wirisproxy";
	static $HTTPPROXY_HOST = "wirisproxy_host";
	static $HTTPPROXY_PORT = "wirisproxy_port";
	static $HTTPPROXY_USER = "wirisproxy_user";
	static $HTTPPROXY_PASS = "wirisproxy_password";
	static $REFERER = "wirisreferer";
	static $IMAGE_FORMAT = "wirisimageformat";
	static $EXTERNAL_PLUGIN = "wirisexternalplugin";
	static $EXTERNAL_REFERER = "wirisexternalreferer";
	static $IMPROVE_PERFORMANCE = "wirispluginperformance";
	static $EDITOR_KEY = "wiriseditorkey";
	static $CLEAN_CACHE_TOKEN = "wiriscleancachetoken";
	static $CUSTOM_HEADER_KEY = "wiriscustomheaders";
	static $CLEAN_CACHE_GUI = "wiriscleancachegui";
	static $imageConfigProperties;
	static $imageConfigPropertiesInv;
	static $SERVICES_PARAMETERS_LIST = "mml,lang,service,latex,mode,ignoreStyles";
	static function computeInverse($dict) {
		$keys = $dict->keys();
		$outDict = new Hash();
		while($keys->hasNext()) {
			$key = $keys->next();
			$outDict->set($dict->get($key), $key);
			unset($key);
		}
		return $outDict;
	}
	function __toString() { return 'com.wiris.plugin.api.ConfigurationKeys'; }
}
{
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties = new Hash();
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("backgroundColor", "wirisimagebackgroundcolor");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("transparency", "wiristransparency");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("fontSize", "wirisimagefontsize");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("version", "wirisimageserviceversion");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("color", "wirisimagecolor");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("dpi", "wirisimagedpi");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("fontFamily", com_wiris_plugin_api_ConfigurationKeys::$FONT_FAMILY);
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("rtlLanguages", "wirisrtllanguages");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("ltrLanguages", "wirisltrlanguages");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("arabicIndicLanguages", "wirisarabicindiclanguages");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("easternArabicIndicLanguages", "wiriseasternarabicindiclanguages");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("europeanLanguages", "wiriseuropeanlanguages");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("defaultStretchy", "wirisimagedefaultstretchy");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties->set("parseMemoryLimit", "wirisparsememorylimit");
	com_wiris_plugin_api_ConfigurationKeys::$imageConfigPropertiesInv = com_wiris_plugin_api_ConfigurationKeys::computeInverse(com_wiris_plugin_api_ConfigurationKeys::$imageConfigProperties);
}
