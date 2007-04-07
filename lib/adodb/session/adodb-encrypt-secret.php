<?php

/*
V4.94 23 Jan 2007  (c) 2000-2007 John Lim (jlim#natsoft.com.my). All rights reserved.
         Contributed by Ross Smith (adodb@netebb.com). 
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
	  Set tabs to 4 for best viewing.

*/

@define('HORDE_BASE', dirname(dirname(dirname(__FILE__))) . '/horde');

if (!is_dir(HORDE_BASE)) {
	trigger_error(sprintf('Directory not found: \'%s\'', HORDE_BASE), E_USER_ERROR);
	return 0;
}

include_once HORDE_BASE . '/lib/Horde.php';
include_once HORDE_BASE . '/lib/Secret.php';

/**

NOTE: On Windows 2000 SP4 with PHP 4.3.1, MCrypt 2.4.x, and Apache 1.3.28,
the session didn't work properly.

This may be resolved with 4.3.3.

 */
class ADODB_Encrypt_Secret {
	/**
	 */
	function write($data, $key) {
		return Secret::write($key, $data);
	}

	/**
	 */
	function read($data, $key) {
		return Secret::read($key, $data);
	}

}

return 1;

?>
