<?php

interface com_wiris_util_sys_HttpConnectionListener {
	function onHTTPStatus($status, $service);
	function onHTTPError($error, $service);
	function onHTTPData($res, $service);
}
