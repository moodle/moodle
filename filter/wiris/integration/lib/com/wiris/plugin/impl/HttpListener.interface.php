<?php

interface com_wiris_plugin_impl_HttpListener {
	function onError($msg);
	function onData($data);
}
