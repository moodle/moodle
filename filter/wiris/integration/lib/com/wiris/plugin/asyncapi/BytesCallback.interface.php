<?php

interface com_wiris_plugin_asyncapi_BytesCallback {
	function error($msg);
	function returnBytes($bs);
}
