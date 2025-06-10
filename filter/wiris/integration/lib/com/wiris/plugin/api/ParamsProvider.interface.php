<?php

interface com_wiris_plugin_api_ParamsProvider {
	function getServiceParameters();
	function getRenderParameters($configuration);
	function getParameters();
	function getRequiredParameter($param);
	function getParameter($param, $dflt);
}
