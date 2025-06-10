<?php

interface com_wiris_plugin_api_ServiceResourceLoader {
	function getContentType($name);
	function getContent($resource);
}
