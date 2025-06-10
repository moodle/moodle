<?php

interface com_wiris_plugin_asyncapi_AsyncRender {
	function getMathml($digest, $call);
	function showImage($digest, $mml, $param, $call);
	function createImage($mml, $param, &$output, $call);
}
