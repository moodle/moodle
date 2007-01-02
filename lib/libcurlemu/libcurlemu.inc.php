<?php
/* CURL Extension Emulation Library
 * Version 1.0.3
 * Copyright 2004-2005, Steve Blinch
 * http://code.blitzaffe.com
 * ============================================================================
 *
 * DESCRIPTION
 *
 * Provides a pure-PHP implementation of the PHP CURL extension, for use on
 * systems which do not already have the CURL extension installed.  It emulates
 * all of the curl_* functions normally provided by the CURL extension itself.
 *
 * This will automatically detect and use the best CURL implementation available
 * on your server.  It will attempt the following, in order:
 *
 * 1) Check for the existence of the "real" CURL PHP Extension.  If it is
 *    loaded, the library will do nothing (and it will not interfere with the
 *    "real" extension).
 * 2) Check for the existence of the CURL console binary (usually located in
 *    /usr/bin/curl).  If found, the library will emulate the CURL PHP
 *    extension (including all curl_* functions) and use the console binary
 *    to execute all requests.
 * 3) If neither the "real" CURL PHP Extension nor the CURL console binary
 *    are available, the library will emulate the CURL PHP extension (including
 *    all curl_* functions) using a native, pure-PHP HTTP client implementation.
 *    This implementation is somewhat limited, but it provides support for most
 *    of the common CURL options.  HTTPS (SSL) support is available in this
 *    mode under PHP 4.3.0 if the OpenSSL Extension is loaded.
 *
 * Thus, by including this library in your project, you can rely on having some
 * level of CURL support regardless of the configuration of the server on which
 * it is being used.
 *
 *
 * USAGE
 *
 * Simply copy all of the libcurlemu files into your project directory, then:
 *
 * require_once("libcurlemu.inc.php");
 *
 * After this, you can use all of the curl_* functions documented in the PHP
 * Manual.
 *
 *
 * EXAMPLE
 *
 * // CURL Extension Emulation Library Example
 * //
 * // Usage should be straightforward; you simply use this script exactly as you
 * // would normally use the PHP CURL extension functions.
 *
 * // first, include libcurlemu.inc.php
 * require_once('libcurlemu.inc.php');
 *
 * // at this point, libcurlemu has detected the best available CURL solution
 * // (either the CURL extension, if available, or the CURL commandline binary,
 * // if available, or as a last resort, HTTPRetriever, our native-PHP HTTP
 * // client implementation) and has implemented the curl_* functions if
 * // necessary, so you can use CURL normally and safely assume that all CURL
 * // functions are available.
 *
 * // the rest of this example code is copied straight from the PHP manual's
 * // reference for the curl_init() function, and will work fine with libcurlemu
 *
 * // create a new CURL resource
 * $ch = curl_init();
 * 
 * // set URL and other appropriate options
 * curl_setopt($ch, CURLOPT_URL, "http://www.example.com/");
 * curl_setopt($ch, CURLOPT_HEADER, false);
 * 
 * // grab URL and pass it to the browser
 * curl_exec($ch);
 * 
 * // close CURL resource, and free up system resources
 * curl_close($ch);
 *
 *
 * LICENSE
 *
 * This script is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *	
 * You should have received a copy of the GNU General Public License along
 * with this script; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
if (!extension_loaded('curl') && !function_exists('curl_init')) {
	define('CURLEXT_MISSING_ABORT',true);
	require_once(dirname(__FILE__)."/libcurlexternal.inc.php");
	
	if (!function_exists('curl_init')) {
		require_once(dirname(__FILE__)."/class_HTTPRetriever.php");
		require_once(dirname(__FILE__)."/libcurlnative.inc.php");
	}
}
?>