<?php

// $CVSHeader$

/*
V4.01 23 Oct 2003  (c) 2000-2004 John Lim (jlim@natsoft.com.my). All rights reserved.
         Contributed by Ross Smith (adodb@netebb.com). 
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
	  Set tabs to 4 for best viewing.

*/

include_once ADODB_SESSION . '/crypt.inc.php';

/**
 */
class ADODB_Encrypt_MD5 {
	/**
	 */
	function write($data, $key) {
		$md5crypt =& new MD5Crypt();
		return $md5crypt->encrypt($data, $key);
	}

	/**
	 */
	function read($data, $key) {
		$md5crypt =& new MD5Crypt();
		return $md5crypt->decrypt($data, $key);
	}

}

return 1;

?>