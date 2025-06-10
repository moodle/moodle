<?php

interface com_wiris_util_sys_AccessProvider {
	function isEnabled();
	function requireAccess();
}
