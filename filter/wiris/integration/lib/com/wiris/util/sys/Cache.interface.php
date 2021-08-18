<?php

interface com_wiris_util_sys_Cache {
	function delete($key);
	function deleteAll();
	function get($key);
	function set($key, $value);
}
