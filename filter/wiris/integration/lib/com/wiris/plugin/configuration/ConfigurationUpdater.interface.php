<?php

interface com_wiris_plugin_configuration_ConfigurationUpdater {
	function updateConfiguration(&$configuration);
	function init($obj);
}
