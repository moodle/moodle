<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 1999-2011 Kasper Skårhøj (kasperYYYY@typo3.com)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

	// a tabulator
define('TAB', chr(9));
	// a linefeed
define('LF', chr(10));
	// a carriage return
define('CR', chr(13));
	// a CR-LF combination
define('CRLF', CR . LF);

/**
 * The legendary "t3lib_div" class - Miscellaneous functions for general purpose.
 * Most of the functions do not relate specifically to TYPO3
 * However a section of functions requires certain TYPO3 features available
 * See comments in the source.
 * You are encouraged to use this library in your own scripts!
 *
 * USE:
 * The class is intended to be used without creating an instance of it.
 * So: Don't instantiate - call functions with "t3lib_div::" prefixed the function name.
 * So use t3lib_div::[method-name] to refer to the functions, eg. 't3lib_div::milliseconds()'
 *
 * @author Kasper Skårhøj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage t3lib
 */
final class t3lib_div {

		// Severity constants used by t3lib_div::sysLog()
	const SYSLOG_SEVERITY_INFO = 0;
	const SYSLOG_SEVERITY_NOTICE = 1;
	const SYSLOG_SEVERITY_WARNING = 2;
	const SYSLOG_SEVERITY_ERROR = 3;
	const SYSLOG_SEVERITY_FATAL = 4;

	const ENV_TRUSTED_HOSTS_PATTERN_ALLOW_ALL = '.*';
	const ENV_TRUSTED_HOSTS_PATTERN_SERVER_NAME = 'SERVER_NAME';

	/**
	 * State of host header value security check
	 * in order to avoid unnecessary multiple checks during one request
	 *
	 * @var bool
	 */
	static protected $allowHostHeaderValue = FALSE;

	/**
	 * Singleton instances returned by makeInstance, using the class names as
	 * array keys
	 *
	 * @var array<t3lib_Singleton>
	 */
	protected static $singletonInstances = array();

	/**
	 * Instances returned by makeInstance, using the class names as array keys
	 *
	 * @var array<array><object>
	 */
	protected static $nonSingletonInstances = array();

	/**
	 * Register for makeInstance with given class name and final class names to reduce number of class_exists() calls
	 *
	 * @var array Given class name => final class name
	 */
	protected static $finalClassNameRegister = array();

	/*************************
	 *
	 * GET/POST Variables
	 *
	 * Background:
	 * Input GET/POST variables in PHP may have their quotes escaped with "\" or not depending on configuration.
	 * TYPO3 has always converted quotes to BE escaped if the configuration told that they would not be so.
	 * But the clean solution is that quotes are never escaped and that is what the functions below offers.
	 * Eventually TYPO3 should provide this in the global space as well.
	 * In the transitional phase (or forever..?) we need to encourage EVERY to read and write GET/POST vars through the API functions below.
	 *
	 *************************/

	/**
	 * Returns the 'GLOBAL' value of incoming data from POST or GET, with priority to POST (that is equalent to 'GP' order)
	 * Strips slashes from all output, both strings and arrays.
	 * To enhancement security in your scripts, please consider using t3lib_div::_GET or t3lib_div::_POST if you already
	 * know by which method your data is arriving to the scripts!
	 *
	 * @param string $var GET/POST var to return
	 * @return mixed POST var named $var and if not set, the GET var of the same name.
	 */
	public static function _GP($var) {
		if (empty($var)) {
			return;
		}
		$value = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];
		if (isset($value)) {
			if (is_array($value)) {
				self::stripSlashesOnArray($value);
			} else {
				$value = stripslashes($value);
			}
		}
		return $value;
	}

	/**
	 * Returns the global arrays $_GET and $_POST merged with $_POST taking precedence.
	 *
	 * @param string $parameter Key (variable name) from GET or POST vars
	 * @return array Returns the GET vars merged recursively onto the POST vars.
	 */
	public static function _GPmerged($parameter) {
		$postParameter = (isset($_POST[$parameter]) && is_array($_POST[$parameter])) ? $_POST[$parameter] : array();
		$getParameter = (isset($_GET[$parameter]) && is_array($_GET[$parameter])) ? $_GET[$parameter] : array();

		$mergedParameters = self::array_merge_recursive_overrule($getParameter, $postParameter);
		self::stripSlashesOnArray($mergedParameters);

		return $mergedParameters;
	}

	/**
	 * Returns the global $_GET array (or value from) normalized to contain un-escaped values.
	 * ALWAYS use this API function to acquire the GET variables!
	 *
	 * @param string $var Optional pointer to value in GET array (basically name of GET var)
	 * @return mixed If $var is set it returns the value of $_GET[$var]. If $var is NULL (default), returns $_GET itself. In any case *slashes are stipped from the output!*
	 * @see _POST(), _GP(), _GETset()
	 */
	public static function _GET($var = NULL) {
		$value = ($var === NULL) ? $_GET : (empty($var) ? NULL : $_GET[$var]);
		if (isset($value)) { // Removes slashes since TYPO3 has added them regardless of magic_quotes setting.
			if (is_array($value)) {
				self::stripSlashesOnArray($value);
			} else {
				$value = stripslashes($value);
			}
		}
		return $value;
	}

	/**
	 * Returns the global $_POST array (or value from) normalized to contain un-escaped values.
	 * ALWAYS use this API function to acquire the $_POST variables!
	 *
	 * @param string $var Optional pointer to value in POST array (basically name of POST var)
	 * @return mixed If $var is set it returns the value of $_POST[$var]. If $var is NULL (default), returns $_POST itself. In any case *slashes are stipped from the output!*
	 * @see _GET(), _GP()
	 */
	public static function _POST($var = NULL) {
		$value = ($var === NULL) ? $_POST : (empty($var) ? NULL : $_POST[$var]);
		if (isset($value)) { // Removes slashes since TYPO3 has added them regardless of magic_quotes setting.
			if (is_array($value)) {
				self::stripSlashesOnArray($value);
			} else {
				$value = stripslashes($value);
			}
		}
		return $value;
	}

	/**
	 * Writes input value to $_GET.
	 *
	 * @param mixed $inputGet
	 *		array or single value to write to $_GET. Values should NOT be
	 *		escaped at input time (but will be escaped before writing
	 *		according to TYPO3 standards).
	 * @param string $key
	 *		alternative key; If set, this will not set the WHOLE GET array,
	 *		but only the key in it specified by this value!
	 *		You can specify to replace keys on deeper array levels by
	 *		separating the keys with a pipe.
	 *		Example: 'parentKey|childKey' will result in
	 *		array('parentKey' => array('childKey' => $inputGet))
	 *
	 * @return void
	 */
	public static function _GETset($inputGet, $key = '') {
			// adds slashes since TYPO3 standard currently is that slashes
			// must be applied (regardless of magic_quotes setting)
		if (is_array($inputGet)) {
			self::addSlashesOnArray($inputGet);
		} else {
			$inputGet = addslashes($inputGet);
		}

		if ($key != '') {
			if (strpos($key, '|') !== FALSE) {
				$pieces = explode('|', $key);
				$newGet = array();
				$pointer =& $newGet;
				foreach ($pieces as $piece) {
					$pointer =& $pointer[$piece];
				}
				$pointer = $inputGet;
				$mergedGet = self::array_merge_recursive_overrule(
					$_GET, $newGet
				);

				$_GET = $mergedGet;
				$GLOBALS['HTTP_GET_VARS'] = $mergedGet;
			} else {
				$_GET[$key] = $inputGet;
				$GLOBALS['HTTP_GET_VARS'][$key] = $inputGet;
			}
		} elseif (is_array($inputGet)) {
			$_GET = $inputGet;
			$GLOBALS['HTTP_GET_VARS'] = $inputGet;
		}
	}

	/**
	 * Wrapper for the RemoveXSS function.
	 * Removes potential XSS code from an input string.
	 *
	 * Using an external class by Travis Puderbaugh <kallahar@quickwired.com>
	 *
	 * @param string $string Input string
	 * @return string Input string with potential XSS code removed
	 */
	public static function removeXSS($string) {
		require_once(PATH_typo3 . 'contrib/RemoveXSS/RemoveXSS.php');
		$string = RemoveXSS::process($string);
		return $string;
	}


	/*************************
	 *
	 * IMAGE FUNCTIONS
	 *
	 *************************/


	/**
	 * Compressing a GIF file if not already LZW compressed.
	 * This function is a workaround for the fact that ImageMagick and/or GD does not compress GIF-files to their minimun size (that is RLE or no compression used)
	 *
	 *		 The function takes a file-reference, $theFile, and saves it again through GD or ImageMagick in order to compress the file
	 *		 GIF:
	 *		 If $type is not set, the compression is done with ImageMagick (provided that $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_path_lzw'] is pointing to the path of a lzw-enabled version of 'convert') else with GD (should be RLE-enabled!)
	 *		 If $type is set to either 'IM' or 'GD' the compression is done with ImageMagick and GD respectively
	 *		 PNG:
	 *		 No changes.
	 *
	 *		 $theFile is expected to be a valid GIF-file!
	 *		 The function returns a code for the operation.
	 *
	 * @param string $theFile Filepath
	 * @param string $type See description of function
	 * @return string Returns "GD" if GD was used, otherwise "IM" if ImageMagick was used. If nothing done at all, it returns empty string.
	 */
	public static function gif_compress($theFile, $type) {
		$gfxConf = $GLOBALS['TYPO3_CONF_VARS']['GFX'];
		$returnCode = '';
		if ($gfxConf['gif_compress'] && strtolower(substr($theFile, -4, 4)) == '.gif') { // GIF...
			if (($type == 'IM' || !$type) && $gfxConf['im'] && $gfxConf['im_path_lzw']) { // IM
					// use temporary file to prevent problems with read and write lock on same file on network file systems
				$temporaryName  =  dirname($theFile) . '/' . md5(uniqid()) . '.gif';
					// rename could fail, if a simultaneous thread is currently working on the same thing
				if (@rename($theFile, $temporaryName)) {
					$cmd = self::imageMagickCommand('convert', '"' . $temporaryName . '" "' . $theFile . '"', $gfxConf['im_path_lzw']);
					t3lib_utility_Command::exec($cmd);
					unlink($temporaryName);
				}

				$returnCode = 'IM';
				if (@is_file($theFile)) {
					self::fixPermissions($theFile);
				}
			} elseif (($type == 'GD' || !$type) && $gfxConf['gdlib'] && !$gfxConf['gdlib_png']) { // GD
				$tempImage = imageCreateFromGif($theFile);
				imageGif($tempImage, $theFile);
				imageDestroy($tempImage);
				$returnCode = 'GD';
				if (@is_file($theFile)) {
					self::fixPermissions($theFile);
				}
			}
		}
		return $returnCode;
	}

	/**
	 * Converts a png file to gif.
	 * This converts a png file to gif IF the FLAG $GLOBALS['TYPO3_CONF_VARS']['FE']['png_to_gif'] is set TRUE.
	 *
	 * @param string $theFile the filename with path
	 * @return string new filename
	 */
	public static function png_to_gif_by_imagemagick($theFile) {
		if ($GLOBALS['TYPO3_CONF_VARS']['FE']['png_to_gif']
				&& $GLOBALS['TYPO3_CONF_VARS']['GFX']['im']
				&& $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_path_lzw']
				&& strtolower(substr($theFile, -4, 4)) == '.png'
				&& @is_file($theFile)) { // IM
			$newFile = substr($theFile, 0, -4) . '.gif';
			$cmd = self::imageMagickCommand('convert', '"' . $theFile . '" "' . $newFile . '"', $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_path_lzw']);
			t3lib_utility_Command::exec($cmd);
			$theFile = $newFile;
			if (@is_file($newFile)) {
				self::fixPermissions($newFile);
			}
				// unlink old file?? May be bad idea because TYPO3 would then recreate the file every time as
				// TYPO3 thinks the file is not generated because it's missing!! So do not unlink $theFile here!!
		}
		return $theFile;
	}

	/**
	 * Returns filename of the png/gif version of the input file (which can be png or gif).
	 * If input file type does not match the wanted output type a conversion is made and temp-filename returned.
	 *
	 * @param string $theFile Filepath of image file
	 * @param boolean $output_png If set, then input file is converted to PNG, otherwise to GIF
	 * @return string If the new image file exists, its filepath is returned
	 */
	public static function read_png_gif($theFile, $output_png = FALSE) {
		if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['im'] && @is_file($theFile)) {
			$ext = strtolower(substr($theFile, -4, 4));
			if (
				((string) $ext == '.png' && $output_png) ||
				((string) $ext == '.gif' && !$output_png)
			) {
				return $theFile;
			} else {
				$newFile = PATH_site . 'typo3temp/readPG_' . md5($theFile . '|' . filemtime($theFile)) . ($output_png ? '.png' : '.gif');
				$cmd = self::imageMagickCommand('convert', '"' . $theFile . '" "' . $newFile . '"', $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_path']);
				t3lib_utility_Command::exec($cmd);
				if (@is_file($newFile)) {
					self::fixPermissions($newFile);
					return $newFile;
				}
			}
		}
	}


	/*************************
	 *
	 * STRING FUNCTIONS
	 *
	 *************************/

	/**
	 * Truncates a string with appended/prepended "..." and takes current character set into consideration.
	 *
	 * @param string $string string to truncate
	 * @param integer $chars must be an integer with an absolute value of at least 4. if negative the string is cropped from the right end.
	 * @param string $appendString appendix to the truncated string
	 * @return string cropped string
	 */
	public static function fixed_lgd_cs($string, $chars, $appendString = '...') {
		if (is_object($GLOBALS['LANG'])) {
			return $GLOBALS['LANG']->csConvObj->crop($GLOBALS['LANG']->charSet, $string, $chars, $appendString);
		} elseif (is_object($GLOBALS['TSFE'])) {
			$charSet = ($GLOBALS['TSFE']->renderCharset != '' ? $GLOBALS['TSFE']->renderCharset : $GLOBALS['TSFE']->defaultCharSet);
			return $GLOBALS['TSFE']->csConvObj->crop($charSet, $string, $chars, $appendString);
		} else {
				// this case should not happen
			$csConvObj = self::makeInstance('t3lib_cs');
			return $csConvObj->crop('utf-8', $string, $chars, $appendString);
		}
	}

	/**
	 * Breaks up a single line of text for emails
	 *
	 * @param string $str The string to break up
	 * @param string $newlineChar The string to implode the broken lines with (default/typically \n)
	 * @param integer $lineWidth The line width
	 * @return string reformatted text
	 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0 - Use t3lib_utility_Mail::breakLinesForEmail()
	 */
	public static function breakLinesForEmail($str, $newlineChar = LF, $lineWidth = 76) {
		self::logDeprecatedFunction();
		return t3lib_utility_Mail::breakLinesForEmail($str, $newlineChar, $lineWidth);
	}

	/**
	 * Match IP number with list of numbers with wildcard
	 * Dispatcher method for switching into specialised IPv4 and IPv6 methods.
	 *
	 * @param string $baseIP is the current remote IP address for instance, typ. REMOTE_ADDR
	 * @param string $list is a comma-list of IP-addresses to match with. *-wildcard allowed instead of number, plus leaving out parts in the IP number is accepted as wildcard (eg. 192.168.*.* equals 192.168). If list is "*" no check is done and the function returns TRUE immediately. An empty list always returns FALSE.
	 * @return boolean TRUE if an IP-mask from $list matches $baseIP
	 */
	public static function cmpIP($baseIP, $list) {
		$list = trim($list);
		if ($list === '') {
			return FALSE;
		} elseif ($list === '*') {
			return TRUE;
		}
		if (strpos($baseIP, ':') !== FALSE && self::validIPv6($baseIP)) {
			return self::cmpIPv6($baseIP, $list);
		} else {
			return self::cmpIPv4($baseIP, $list);
		}
	}

	/**
	 * Match IPv4 number with list of numbers with wildcard
	 *
	 * @param	string		$baseIP is the current remote IP address for instance, typ. REMOTE_ADDR
	 * @param	string		$list is a comma-list of IP-addresses to match with. *-wildcard allowed instead of number, plus leaving out parts in the IP number is accepted as wildcard (eg. 192.168.*.* equals 192.168), could also contain IPv6 addresses
	 * @return	boolean		TRUE if an IP-mask from $list matches $baseIP
	 */
	public static function cmpIPv4($baseIP, $list) {
		$IPpartsReq = explode('.', $baseIP);
		if (count($IPpartsReq) == 4) {
			$values = self::trimExplode(',', $list, 1);

			foreach ($values as $test) {
				$testList = explode('/', $test);
				if (count($testList) == 2) {
					list($test, $mask) = $testList;
				} else {
					$mask = FALSE;
				}

				if (intval($mask)) {
						// "192.168.3.0/24"
					$lnet = ip2long($test);
					$lip = ip2long($baseIP);
					$binnet = str_pad(decbin($lnet), 32, '0', STR_PAD_LEFT);
					$firstpart = substr($binnet, 0, $mask);
					$binip = str_pad(decbin($lip), 32, '0', STR_PAD_LEFT);
					$firstip = substr($binip, 0, $mask);
					$yes = (strcmp($firstpart, $firstip) == 0);
				} else {
						// "192.168.*.*"
					$IPparts = explode('.', $test);
					$yes = 1;
					foreach ($IPparts as $index => $val) {
						$val = trim($val);
						if (($val !== '*') && ($IPpartsReq[$index] !== $val)) {
							$yes = 0;
						}
					}
				}
				if ($yes) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}

	/**
	 * Match IPv6 address with a list of IPv6 prefixes
	 *
	 * @param string $baseIP is the current remote IP address for instance
	 * @param string $list is a comma-list of IPv6 prefixes, could also contain IPv4 addresses
	 * @return boolean TRUE if an baseIP matches any prefix
	 */
	public static function cmpIPv6($baseIP, $list) {
		$success = FALSE; // Policy default: Deny connection
		$baseIP = self::normalizeIPv6($baseIP);

		$values = self::trimExplode(',', $list, 1);
		foreach ($values as $test) {
			$testList = explode('/', $test);
			if (count($testList) == 2) {
				list($test, $mask) = $testList;
			} else {
				$mask = FALSE;
			}

			if (self::validIPv6($test)) {
				$test = self::normalizeIPv6($test);
				$maskInt = intval($mask) ? intval($mask) : 128;
				if ($mask === '0') { // special case; /0 is an allowed mask - equals a wildcard
					$success = TRUE;
				} elseif ($maskInt == 128) {
					$success = ($test === $baseIP);
				} else {
					$testBin = self::IPv6Hex2Bin($test);
					$baseIPBin = self::IPv6Hex2Bin($baseIP);
					$success = TRUE;

					// modulo is 0 if this is a 8-bit-boundary
					$maskIntModulo = $maskInt % 8;
					$numFullCharactersUntilBoundary = intval($maskInt / 8);

					if (substr($testBin, 0, $numFullCharactersUntilBoundary) !== substr($baseIPBin, 0, $numFullCharactersUntilBoundary)) {
						$success = FALSE;
					} elseif ($maskIntModulo > 0) {
						// if not an 8-bit-boundary, check bits of last character
						$testLastBits = str_pad(decbin(ord(substr($testBin, $numFullCharactersUntilBoundary, 1))), 8, '0', STR_PAD_LEFT);
						$baseIPLastBits = str_pad(decbin(ord(substr($baseIPBin, $numFullCharactersUntilBoundary, 1))), 8, '0', STR_PAD_LEFT);
						if (strncmp($testLastBits, $baseIPLastBits, $maskIntModulo) != 0) {
							$success = FALSE;
						}
					}
				}
			}
			if ($success) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Transform a regular IPv6 address from hex-representation into binary
	 *
	 * @param string $hex IPv6 address in hex-presentation
	 * @return string Binary representation (16 characters, 128 characters)
	 * @see IPv6Bin2Hex()
	 */
	public static function IPv6Hex2Bin($hex) {
			// use PHP-function if PHP was compiled with IPv6-support
		if (defined('AF_INET6')) {
			$bin = inet_pton($hex);
		} else {
			$hex = self::normalizeIPv6($hex);
			$hex = str_replace(':', '', $hex); // Replace colon to nothing
			$bin = pack("H*" , $hex);
		}
		return $bin;
	}

	/**
	 * Transform an IPv6 address from binary to hex-representation
	 *
	 * @param string $bin IPv6 address in hex-presentation
	 * @return string Binary representation (16 characters, 128 characters)
	 * @see IPv6Hex2Bin()
	 */
	public static function IPv6Bin2Hex($bin) {
			// use PHP-function if PHP was compiled with IPv6-support
		if (defined('AF_INET6')) {
			$hex = inet_ntop($bin);
		} else {
			$hex = unpack("H*" , $bin);
			$hex = chunk_split($hex[1], 4, ':');
				// strip last colon (from chunk_split)
			$hex = substr($hex, 0, -1);
				// IPv6 is now in normalized form
				// compress it for easier handling and to match result from inet_ntop()
			$hex = self::compressIPv6($hex);
		}
		return $hex;

	}

	/**
	 * Normalize an IPv6 address to full length
	 *
	 * @param string $address Given IPv6 address
	 * @return string Normalized address
	 * @see compressIPv6()
	 */
	public static function normalizeIPv6($address) {
		$normalizedAddress = '';
		$stageOneAddress = '';

			// according to RFC lowercase-representation is recommended
		$address = strtolower($address);

			// normalized representation has 39 characters (0000:0000:0000:0000:0000:0000:0000:0000)
		if (strlen($address) == 39) {
				// already in full expanded form
			return $address;
		}

		$chunks = explode('::', $address); // Count 2 if if address has hidden zero blocks
		if (count($chunks) == 2) {
			$chunksLeft = explode(':', $chunks[0]);
			$chunksRight = explode(':', $chunks[1]);
			$left = count($chunksLeft);
			$right = count($chunksRight);

				// Special case: leading zero-only blocks count to 1, should be 0
			if ($left == 1 && strlen($chunksLeft[0]) == 0) {
				$left = 0;
			}

			$hiddenBlocks = 8 - ($left + $right);
			$hiddenPart = '';
			$h = 0;
			while ($h < $hiddenBlocks) {
				$hiddenPart .= '0000:';
				$h++;
			}

			if ($left == 0) {
				$stageOneAddress = $hiddenPart . $chunks[1];
			} else {
				$stageOneAddress = $chunks[0] . ':' . $hiddenPart . $chunks[1];
			}
		} else {
			$stageOneAddress = $address;
		}

			// normalize the blocks:
		$blocks = explode(':', $stageOneAddress);
		$divCounter = 0;
		foreach ($blocks as $block) {
			$tmpBlock = '';
			$i = 0;
			$hiddenZeros = 4 - strlen($block);
			while ($i < $hiddenZeros) {
				$tmpBlock .= '0';
				$i++;
			}
			$normalizedAddress .= $tmpBlock . $block;
			if ($divCounter < 7) {
				$normalizedAddress .= ':';
				$divCounter++;
			}
		}
		return $normalizedAddress;
	}


	/**
	 * Compress an IPv6 address to the shortest notation
	 *
	 * @param string $address Given IPv6 address
	 * @return string Compressed address
	 * @see normalizeIPv6()
	 */
	public static function compressIPv6($address) {
			// use PHP-function if PHP was compiled with IPv6-support
		if (defined('AF_INET6')) {
			$bin = inet_pton($address);
			$address = inet_ntop($bin);
		} else {
			$address = self::normalizeIPv6($address);

				// append one colon for easier handling
				// will be removed later
			$address .= ':';

				// according to IPv6-notation the longest match
				// of a package of '0000:' may be replaced with ':'
				// (resulting in something like '1234::abcd')
			for ($counter = 8; $counter > 1; $counter--) {
				$search = str_repeat('0000:', $counter);
				if (($pos = strpos($address, $search)) !== FALSE) {
					$address = substr($address, 0, $pos) . ':' . substr($address, $pos + ($counter*5));
					break;
				}
			}

				// up to 3 zeros in the first part may be removed
			$address = preg_replace('/^0{1,3}/', '', $address);
				// up to 3 zeros at the beginning of other parts may be removed
			$address = preg_replace('/:0{1,3}/', ':', $address);

				// strip last colon (from chunk_split)
			$address = substr($address, 0, -1);
		}
		return $address;
	}

	/**
	 * Validate a given IP address.
	 *
	 * Possible format are IPv4 and IPv6.
	 *
	 * @param string $ip IP address to be tested
	 * @return boolean TRUE if $ip is either of IPv4 or IPv6 format.
	 */
	public static function validIP($ip) {
		return (filter_var($ip, FILTER_VALIDATE_IP) !== FALSE);
	}

	/**
	 * Validate a given IP address to the IPv4 address format.
	 *
	 * Example for possible format: 10.0.45.99
	 *
	 * @param string $ip IP address to be tested
	 * @return boolean TRUE if $ip is of IPv4 format.
	 */
	public static function validIPv4($ip) {
		return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== FALSE);
	}

	/**
	 * Validate a given IP address to the IPv6 address format.
	 *
	 * Example for possible format: 43FB::BB3F:A0A0:0 | ::1
	 *
	 * @param string $ip IP address to be tested
	 * @return boolean TRUE if $ip is of IPv6 format.
	 */
	public static function validIPv6($ip) {
		return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== FALSE);
	}

	/**
	 * Match fully qualified domain name with list of strings with wildcard
	 *
	 * @param string $baseHost A hostname or an IPv4/IPv6-address (will by reverse-resolved; typically REMOTE_ADDR)
	 * @param string $list A comma-list of domain names to match with. *-wildcard allowed but cannot be part of a string, so it must match the full host name (eg. myhost.*.com => correct, myhost.*domain.com => wrong)
	 * @return boolean TRUE if a domain name mask from $list matches $baseIP
	 */
	public static function cmpFQDN($baseHost, $list) {
		$baseHost = trim($baseHost);
		if (empty($baseHost)) {
			return FALSE;
		}
		if (self::validIPv4($baseHost) || self::validIPv6($baseHost)) {
				// resolve hostname
				// note: this is reverse-lookup and can be randomly set as soon as somebody is able to set
				// the reverse-DNS for his IP (security when for example used with REMOTE_ADDR)
			$baseHostName = gethostbyaddr($baseHost);
			if ($baseHostName === $baseHost) {
					// unable to resolve hostname
				return FALSE;
			}
		} else {
			$baseHostName = $baseHost;
		}
		$baseHostNameParts = explode('.', $baseHostName);

		$values = self::trimExplode(',', $list, 1);

		foreach ($values as $test) {
			$hostNameParts = explode('.', $test);

				// to match hostNameParts can only be shorter (in case of wildcards) or equal
			if (count($hostNameParts) > count($baseHostNameParts)) {
				continue;
			}

			$yes = TRUE;
			foreach ($hostNameParts as $index => $val) {
				$val = trim($val);
				if ($val === '*') {
						// wildcard valid for one or more hostname-parts

					$wildcardStart = $index + 1;
						// wildcard as last/only part always matches, otherwise perform recursive checks
					if ($wildcardStart < count($hostNameParts)) {
						$wildcardMatched = FALSE;
						$tempHostName = implode('.', array_slice($hostNameParts, $index + 1));
						while (($wildcardStart < count($baseHostNameParts)) && (!$wildcardMatched)) {
							$tempBaseHostName = implode('.', array_slice($baseHostNameParts, $wildcardStart));
							$wildcardMatched = self::cmpFQDN($tempBaseHostName, $tempHostName);
							$wildcardStart++;
						}
						if ($wildcardMatched) {
								// match found by recursive compare
							return TRUE;
						} else {
							$yes = FALSE;
						}
					}
				} elseif ($baseHostNameParts[$index] !== $val) {
						// in case of no match
					$yes = FALSE;
				}
			}
			if ($yes) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Checks if a given URL matches the host that currently handles this HTTP request.
	 * Scheme, hostname and (optional) port of the given URL are compared.
	 *
	 * @param string $url: URL to compare with the TYPO3 request host
	 * @return boolean Whether the URL matches the TYPO3 request host
	 */
	public static function isOnCurrentHost($url) {
		return (stripos($url . '/', self::getIndpEnv('TYPO3_REQUEST_HOST') . '/') === 0);
	}

	/**
	 * Check for item in list
	 * Check if an item exists in a comma-separated list of items.
	 *
	 * @param string $list comma-separated list of items (string)
	 * @param string $item item to check for
	 * @return boolean TRUE if $item is in $list
	 */
	public static function inList($list, $item) {
		return (strpos(',' . $list . ',', ',' . $item . ',') !== FALSE ? TRUE : FALSE);
	}

	/**
	 * Removes an item from a comma-separated list of items.
	 *
	 * @param string $element element to remove
	 * @param string $list comma-separated list of items (string)
	 * @return string new comma-separated list of items
	 */
	public static function rmFromList($element, $list) {
		$items = explode(',', $list);
		foreach ($items as $k => $v) {
			if ($v == $element) {
				unset($items[$k]);
			}
		}
		return implode(',', $items);
	}

	/**
	 * Expand a comma-separated list of integers with ranges (eg 1,3-5,7 becomes 1,3,4,5,7).
	 * Ranges are limited to 1000 values per range.
	 *
	 * @param string $list comma-separated list of integers with ranges (string)
	 * @return string new comma-separated list of items
	 */
	public static function expandList($list) {
		$items = explode(',', $list);
		$list = array();
		foreach ($items as $item) {
			$range = explode('-', $item);
			if (isset($range[1])) {
				$runAwayBrake = 1000;
				for ($n = $range[0]; $n <= $range[1]; $n++) {
					$list[] = $n;

					$runAwayBrake--;
					if ($runAwayBrake <= 0) {
						break;
					}
				}
			} else {
				$list[] = $item;
			}
		}
		return implode(',', $list);
	}

	/**
	 * Forces the integer $theInt into the boundaries of $min and $max. If the $theInt is 'FALSE' then the $zeroValue is applied.
	 *
	 * @param integer $theInt Input value
	 * @param integer $min Lower limit
	 * @param integer $max Higher limit
	 * @param integer $zeroValue Default value if input is FALSE.
	 * @return integer The input value forced into the boundaries of $min and $max
	 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0 - Use t3lib_utility_Math::forceIntegerInRange() instead
	 */
	public static function intInRange($theInt, $min, $max = 2000000000, $zeroValue = 0) {
		self::logDeprecatedFunction();
		return t3lib_utility_Math::forceIntegerInRange($theInt, $min, $max, $zeroValue);
	}

	/**
	 * Returns the $integer if greater than zero, otherwise returns zero.
	 *
	 * @param integer $theInt Integer string to process
	 * @return integer
	 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0 - Use t3lib_utility_Math::convertToPositiveInteger() instead
	 */
	public static function intval_positive($theInt) {
		self::logDeprecatedFunction();
		return t3lib_utility_Math::convertToPositiveInteger($theInt);
	}

	/**
	 * Returns an integer from a three part version number, eg '4.12.3' -> 4012003
	 *
	 * @param string $verNumberStr Version number on format x.x.x
	 * @return integer Integer version of version number (where each part can count to 999)
	 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.1 - Use t3lib_utility_VersionNumber::convertVersionNumberToInteger() instead
	 */
	public static function int_from_ver($verNumberStr) {
			// Deprecation log is activated only for TYPO3 4.7 and above
		if (t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) >= 4007000) {
			self::logDeprecatedFunction();
		}
		return t3lib_utility_VersionNumber::convertVersionNumberToInteger($verNumberStr);
	}

	/**
	 * Returns TRUE if the current TYPO3 version (or compatibility version) is compatible to the input version
	 * Notice that this function compares branches, not versions (4.0.1 would be > 4.0.0 although they use the same compat_version)
	 *
	 * @param string $verNumberStr	Minimum branch number required (format x.y / e.g. "4.0" NOT "4.0.0"!)
	 * @return boolean Returns TRUE if this setup is compatible with the provided version number
	 * @todo Still needs a function to convert versions to branches
	 */
	public static function compat_version($verNumberStr) {
		$currVersionStr = $GLOBALS['TYPO3_CONF_VARS']['SYS']['compat_version'] ? $GLOBALS['TYPO3_CONF_VARS']['SYS']['compat_version'] : TYPO3_branch;

		if (t3lib_utility_VersionNumber::convertVersionNumberToInteger($currVersionStr) < t3lib_utility_VersionNumber::convertVersionNumberToInteger($verNumberStr)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * Makes a positive integer hash out of the first 7 chars from the md5 hash of the input
	 *
	 * @param string $str String to md5-hash
	 * @return integer Returns 28bit integer-hash
	 */
	public static function md5int($str) {
		return hexdec(substr(md5($str), 0, 7));
	}

	/**
	 * Returns the first 10 positions of the MD5-hash		(changed from 6 to 10 recently)
	 *
	 * @param string $input Input string to be md5-hashed
	 * @param integer $len The string-length of the output
	 * @return string Substring of the resulting md5-hash, being $len chars long (from beginning)
	 */
	public static function shortMD5($input, $len = 10) {
		return substr(md5($input), 0, $len);
	}

	/**
	 * Returns a proper HMAC on a given input string and secret TYPO3 encryption key.
	 *
	 * @param string $input Input string to create HMAC from
	 * @param string $additionalSecret additionalSecret to prevent hmac beeing used in a different context
	 * @return string resulting (hexadecimal) HMAC currently with a length of 40 (HMAC-SHA-1)
	 */
	public static function hmac($input, $additionalSecret = '') {
		$hashAlgorithm = 'sha1';
		$hashBlocksize = 64;
		$hmac = '';
		$secret = $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] . $additionalSecret;
		if (extension_loaded('hash') && function_exists('hash_hmac') && function_exists('hash_algos') && in_array($hashAlgorithm, hash_algos())) {
			$hmac = hash_hmac($hashAlgorithm, $input, $secret);
		} else {
				// outer padding
			$opad = str_repeat(chr(0x5C), $hashBlocksize);
				// inner padding
			$ipad = str_repeat(chr(0x36), $hashBlocksize);
			if (strlen($secret) > $hashBlocksize) {
					// keys longer than block size are shorten
				$key = str_pad(pack('H*', call_user_func($hashAlgorithm, $secret)), $hashBlocksize, chr(0));
			} else {
					// keys shorter than block size are zero-padded
				$key = str_pad($secret, $hashBlocksize, chr(0));
			}
			$hmac = call_user_func($hashAlgorithm, ($key ^ $opad) . pack('H*', call_user_func($hashAlgorithm, ($key ^ $ipad) . $input)));
		}
		return $hmac;
	}

	/**
	 * Takes comma-separated lists and arrays and removes all duplicates
	 * If a value in the list is trim(empty), the value is ignored.
	 *
	 * @param string $in_list Accept multiple parameters which can be comma-separated lists of values and arrays.
	 * @param mixed $secondParameter: Dummy field, which if set will show a warning!
	 * @return string Returns the list without any duplicates of values, space around values are trimmed
	 */
	public static function uniqueList($in_list, $secondParameter = NULL) {
		if (is_array($in_list)) {
			throw new InvalidArgumentException(
				'TYPO3 Fatal Error: t3lib_div::uniqueList() does NOT support array arguments anymore! Only string comma lists!',
				1270853885
			);
		}
		if (isset($secondParameter)) {
			throw new InvalidArgumentException(
				'TYPO3 Fatal Error: t3lib_div::uniqueList() does NOT support more than a single argument value anymore. You have specified more than one!',
				1270853886
			);
		}

		return implode(',', array_unique(self::trimExplode(',', $in_list, 1)));
	}

	/**
	 * Splits a reference to a file in 5 parts
	 *
	 * @param string $fileref Filename/filepath to be analysed
	 * @return array Contains keys [path], [file], [filebody], [fileext], [realFileext]
	 */
	public static function split_fileref($fileref) {
		$reg = array();
		if (preg_match('/(.*\/)(.*)$/', $fileref, $reg)) {
			$info['path'] = $reg[1];
			$info['file'] = $reg[2];
		} else {
			$info['path'] = '';
			$info['file'] = $fileref;
		}

		$reg = '';
		if (!is_dir($fileref) && preg_match('/(.*)\.([^\.]*$)/', $info['file'], $reg)) {
			$info['filebody'] = $reg[1];
			$info['fileext'] = strtolower($reg[2]);
			$info['realFileext'] = $reg[2];
		} else {
			$info['filebody'] = $info['file'];
			$info['fileext'] = '';
		}
		reset($info);
		return $info;
	}

	/**
	 * Returns the directory part of a path without trailing slash
	 * If there is no dir-part, then an empty string is returned.
	 * Behaviour:
	 *
	 * '/dir1/dir2/script.php' => '/dir1/dir2'
	 * '/dir1/' => '/dir1'
	 * 'dir1/script.php' => 'dir1'
	 * 'd/script.php' => 'd'
	 * '/script.php' => ''
	 * '' => ''
	 *
	 * @param string $path Directory name / path
	 * @return string Processed input value. See function description.
	 */
	public static function dirname($path) {
		$p = self::revExplode('/', $path, 2);
		return count($p) == 2 ? $p[0] : '';
	}

	/**
	 * Modifies a HTML Hex color by adding/subtracting $R,$G and $B integers
	 *
	 * @param string $color A hexadecimal color code, #xxxxxx
	 * @param integer $R Offset value 0-255
	 * @param integer $G Offset value 0-255
	 * @param integer $B Offset value 0-255
	 * @return string A hexadecimal color code, #xxxxxx, modified according to input vars
	 * @see modifyHTMLColorAll()
	 */
	public static function modifyHTMLColor($color, $R, $G, $B) {
			// This takes a hex-color (# included!) and adds $R, $G and $B to the HTML-color (format: #xxxxxx) and returns the new color
		$nR = t3lib_utility_Math::forceIntegerInRange(hexdec(substr($color, 1, 2)) + $R, 0, 255);
		$nG = t3lib_utility_Math::forceIntegerInRange(hexdec(substr($color, 3, 2)) + $G, 0, 255);
		$nB = t3lib_utility_Math::forceIntegerInRange(hexdec(substr($color, 5, 2)) + $B, 0, 255);
		return '#' .
				substr('0' . dechex($nR), -2) .
				substr('0' . dechex($nG), -2) .
				substr('0' . dechex($nB), -2);
	}

	/**
	 * Modifies a HTML Hex color by adding/subtracting $all integer from all R/G/B channels
	 *
	 * @param string $color A hexadecimal color code, #xxxxxx
	 * @param integer $all Offset value 0-255 for all three channels.
	 * @return string A hexadecimal color code, #xxxxxx, modified according to input vars
	 * @see modifyHTMLColor()
	 */
	public static function modifyHTMLColorAll($color, $all) {
		return self::modifyHTMLColor($color, $all, $all, $all);
	}

	/**
	 * Tests if the input can be interpreted as integer.
	 *
	 * @param mixed $var Any input variable to test
	 * @return boolean Returns TRUE if string is an integer
	 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0 - Use t3lib_utility_Math::canBeInterpretedAsInteger() instead
	 */
	public static function testInt($var) {
		self::logDeprecatedFunction();

		return t3lib_utility_Math::canBeInterpretedAsInteger($var);
	}

	/**
	 * Returns TRUE if the first part of $str matches the string $partStr
	 *
	 * @param string $str Full string to check
	 * @param string $partStr Reference string which must be found as the "first part" of the full string
	 * @return boolean TRUE if $partStr was found to be equal to the first part of $str
	 */
	public static function isFirstPartOfStr($str, $partStr) {
		return $partStr != '' && strpos((string) $str, (string) $partStr, 0) === 0;
	}

	/**
	 * Formats the input integer $sizeInBytes as bytes/kilobytes/megabytes (-/K/M)
	 *
	 * @param integer $sizeInBytes Number of bytes to format.
	 * @param string $labels Labels for bytes, kilo, mega and giga separated by vertical bar (|) and possibly encapsulated in "". Eg: " | K| M| G" (which is the default value)
	 * @return string Formatted representation of the byte number, for output.
	 */
	public static function formatSize($sizeInBytes, $labels = '') {

			// Set labels:
		if (strlen($labels) == 0) {
			$labels = ' | K| M| G';
		} else {
			$labels = str_replace('"', '', $labels);
		}
		$labelArr = explode('|', $labels);

			// Find size:
		if ($sizeInBytes > 900) {
			if ($sizeInBytes > 900000000) { // GB
				$val = $sizeInBytes / (1024 * 1024 * 1024);
				return number_format($val, (($val < 20) ? 1 : 0), '.', '') . $labelArr[3];
			}
			elseif ($sizeInBytes > 900000) { // MB
				$val = $sizeInBytes / (1024 * 1024);
				return number_format($val, (($val < 20) ? 1 : 0), '.', '') . $labelArr[2];
			} else { // KB
				$val = $sizeInBytes / (1024);
				return number_format($val, (($val < 20) ? 1 : 0), '.', '') . $labelArr[1];
			}
		} else { // Bytes
			return $sizeInBytes . $labelArr[0];
		}
	}

	/**
	 * Returns microtime input to milliseconds
	 *
	 * @param string $microtime Microtime
	 * @return integer Microtime input string converted to an integer (milliseconds)
	 */
	public static function convertMicrotime($microtime) {
		$parts = explode(' ', $microtime);
		return round(($parts[0] + $parts[1]) * 1000);
	}

	/**
	 * This splits a string by the chars in $operators (typical /+-*) and returns an array with them in
	 *
	 * @param string $string Input string, eg "123 + 456 / 789 - 4"
	 * @param string $operators Operators to split by, typically "/+-*"
	 * @return array Array with operators and operands separated.
	 * @see tslib_cObj::calc(), tslib_gifBuilder::calcOffset()
	 */
	public static function splitCalc($string, $operators) {
		$res = Array();
		$sign = '+';
		while ($string) {
			$valueLen = strcspn($string, $operators);
			$value = substr($string, 0, $valueLen);
			$res[] = Array($sign, trim($value));
			$sign = substr($string, $valueLen, 1);
			$string = substr($string, $valueLen + 1);
		}
		reset($res);
		return $res;
	}

	/**
	 * Calculates the input by +,-,*,/,%,^ with priority to + and -
	 *
	 * @param string $string Input string, eg "123 + 456 / 789 - 4"
	 * @return integer Calculated value. Or error string.
	 * @see calcParenthesis()
	 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0 - Use t3lib_utility_Math::calculateWithPriorityToAdditionAndSubtraction() instead
	 */
	public static function calcPriority($string) {
		self::logDeprecatedFunction();

		return t3lib_utility_Math::calculateWithPriorityToAdditionAndSubtraction($string);
	}

	/**
	 * Calculates the input with parenthesis levels
	 *
	 * @param string $string Input string, eg "(123 + 456) / 789 - 4"
	 * @return integer Calculated value. Or error string.
	 * @see calcPriority(), tslib_cObj::stdWrap()
	 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0 - Use t3lib_utility_Math::calculateWithParentheses() instead
	 */
	public static function calcParenthesis($string) {
		self::logDeprecatedFunction();

		return t3lib_utility_Math::calculateWithParentheses($string);
	}

	/**
	 * Inverse version of htmlspecialchars()
	 *
	 * @param string $value Value where &gt;, &lt;, &quot; and &amp; should be converted to regular chars.
	 * @return string Converted result.
	 */
	public static function htmlspecialchars_decode($value) {
		$value = str_replace('&gt;', '>', $value);
		$value = str_replace('&lt;', '<', $value);
		$value = str_replace('&quot;', '"', $value);
		$value = str_replace('&amp;', '&', $value);
		return $value;
	}

	/**
	 * Re-converts HTML entities if they have been converted by htmlspecialchars()
	 *
	 * @param string $str String which contains eg. "&amp;amp;" which should stay "&amp;". Or "&amp;#1234;" to "&#1234;". Or "&amp;#x1b;" to "&#x1b;"
	 * @return string Converted result.
	 */
	public static function deHSCentities($str) {
		return preg_replace('/&amp;([#[:alnum:]]*;)/', '&\1', $str);
	}

	/**
	 * This function is used to escape any ' -characters when transferring text to JavaScript!
	 *
	 * @param string $string String to escape
	 * @param boolean $extended If set, also backslashes are escaped.
	 * @param string $char The character to escape, default is ' (single-quote)
	 * @return string Processed input string
	 */
	public static function slashJS($string, $extended = FALSE, $char = "'") {
		if ($extended) {
			$string = str_replace("\\", "\\\\", $string);
		}
		return str_replace($char, "\\" . $char, $string);
	}

	/**
	 * Version of rawurlencode() where all spaces (%20) are re-converted to space-characters.
	 * Useful when passing text to JavaScript where you simply url-encode it to get around problems with syntax-errors, linebreaks etc.
	 *
	 * @param string $str String to raw-url-encode with spaces preserved
	 * @return string Rawurlencoded result of input string, but with all %20 (space chars) converted to real spaces.
	 */
	public static function rawUrlEncodeJS($str) {
		return str_replace('%20', ' ', rawurlencode($str));
	}

	/**
	 * rawurlencode which preserves "/" chars
	 * Useful when file paths should keep the "/" chars, but have all other special chars encoded.
	 *
	 * @param string $str Input string
	 * @return string Output string
	 */
	public static function rawUrlEncodeFP($str) {
		return str_replace('%2F', '/', rawurlencode($str));
	}

	/**
	 * Checking syntax of input email address
	 *
	 * @param string $email Input string to evaluate
	 * @return boolean Returns TRUE if the $email address (input string) is valid
	 */
	public static function validEmail($email) {
			// enforce maximum length to prevent libpcre recursion crash bug #52929 in PHP
			// fixed in PHP 5.3.4; length restriction per SMTP RFC 2821
		if (strlen($email) > 320) {
			return FALSE;
		}
		require_once(PATH_typo3 . 'contrib/idna/idna_convert.class.php');
		$IDN = new idna_convert(array('idn_version' => 2008));

		return (filter_var($IDN->encode($email), FILTER_VALIDATE_EMAIL) !== FALSE);
	}

	/**
	 * Checks if current e-mail sending method does not accept recipient/sender name
	 * in a call to PHP mail() function. Windows version of mail() and mini_sendmail
	 * program are known not to process such input correctly and they cause SMTP
	 * errors. This function will return TRUE if current mail sending method has
	 * problem with recipient name in recipient/sender argument for mail().
	 *
	 * TODO: 4.3 should have additional configuration variable, which is combined
	 * by || with the rest in this function.
	 *
	 * @return boolean TRUE if mail() does not accept recipient name
	 */
	public static function isBrokenEmailEnvironment() {
		return TYPO3_OS == 'WIN' || (FALSE !== strpos(ini_get('sendmail_path'), 'mini_sendmail'));
	}

	/**
	 * Changes from/to arguments for mail() function to work in any environment.
	 *
	 * @param string $address Address to adjust
	 * @return string Adjusted address
	 * @see	t3lib_::isBrokenEmailEnvironment()
	 */
	public static function normalizeMailAddress($address) {
		if (self::isBrokenEmailEnvironment() && FALSE !== ($pos1 = strrpos($address, '<'))) {
			$pos2 = strpos($address, '>', $pos1);
			$address = substr($address, $pos1 + 1, ($pos2 ? $pos2 : strlen($address)) - $pos1 - 1);
		}
		return $address;
	}

	/**
	 * Formats a string for output between <textarea>-tags
	 * All content outputted in a textarea form should be passed through this function
	 * Not only is the content htmlspecialchar'ed on output but there is also a single newline added in the top. The newline is necessary because browsers will ignore the first newline after <textarea> if that is the first character. Therefore better set it!
	 *
	 * @param string $content Input string to be formatted.
	 * @return string Formatted for <textarea>-tags
	 */
	public static function formatForTextarea($content) {
		return LF . htmlspecialchars($content);
	}

	/**
	 * Converts string to uppercase
	 * The function converts all Latin characters (a-z, but no accents, etc) to
	 * uppercase. It is safe for all supported character sets (incl. utf-8).
	 * Unlike strtoupper() it does not honour the locale.
	 *
	 * @param string $str Input string
	 * @return string Uppercase String
	 */
	public static function strtoupper($str) {
		return strtr((string) $str, 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
	}

	/**
	 * Converts string to lowercase
	 * The function converts all Latin characters (A-Z, but no accents, etc) to
	 * lowercase. It is safe for all supported character sets (incl. utf-8).
	 * Unlike strtolower() it does not honour the locale.
	 *
	 * @param string $str Input string
	 * @return string Lowercase String
	 */
	public static function strtolower($str) {
		return strtr((string) $str, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
	}

	/**
	 * Returns a string of highly randomized bytes (over the full 8-bit range).
	 *
	 * Note: Returned values are not guaranteed to be crypto-safe,
	 * most likely they are not, depending on the used retrieval method.
	 *
	 * @param integer $bytesToReturn Number of characters (bytes) to return
	 * @return string Random Bytes
	 * @see http://bugs.php.net/bug.php?id=52523
	 * @see http://www.php-security.org/2010/05/09/mops-submission-04-generating-unpredictable-session-ids-and-hashes/index.html
	 */
	public static function generateRandomBytes($bytesToReturn) {
			// Cache 4k of the generated bytestream.
		static $bytes = '';
		$bytesToGenerate = max(4096, $bytesToReturn);

			// if we have not enough random bytes cached, we generate new ones
		if (!isset($bytes{$bytesToReturn - 1})) {
			if (TYPO3_OS === 'WIN') {
					// Openssl seems to be deadly slow on Windows, so try to use mcrypt
					// Windows PHP versions have a bug when using urandom source (see #24410)
				$bytes .= self::generateRandomBytesMcrypt($bytesToGenerate, MCRYPT_RAND);
			} else {
					// Try to use native PHP functions first, precedence has openssl
				$bytes .= self::generateRandomBytesOpenSsl($bytesToGenerate);

				if (!isset($bytes{$bytesToReturn - 1})) {
					$bytes .= self::generateRandomBytesMcrypt($bytesToGenerate, MCRYPT_DEV_URANDOM);
				}

					// If openssl and mcrypt failed, try /dev/urandom
				if (!isset($bytes{$bytesToReturn - 1})) {
					$bytes .= self::generateRandomBytesUrandom($bytesToGenerate);
				}
			}

				// Fall back if other random byte generation failed until now
			if (!isset($bytes{$bytesToReturn - 1})) {
				$bytes .= self::generateRandomBytesFallback($bytesToReturn);
			}
		}

			// get first $bytesToReturn and remove it from the byte cache
		$output = substr($bytes, 0, $bytesToReturn);
		$bytes = substr($bytes, $bytesToReturn);

		return $output;
	}

	/**
	 * Generate random bytes using openssl if available
	 *
	 * @param string $bytesToGenerate
	 * @return string
	 */
	protected static function generateRandomBytesOpenSsl($bytesToGenerate) {
		if (!function_exists('openssl_random_pseudo_bytes')) {
			return '';
		}
		$isStrong = NULL;
		return (string) openssl_random_pseudo_bytes($bytesToGenerate, $isStrong);
	}

	/**
	 * Generate random bytes using mcrypt if available
	 *
	 * @param $bytesToGenerate
	 * @param $randomSource
	 * @return string
	 */
	protected static function generateRandomBytesMcrypt($bytesToGenerate, $randomSource) {
		if (!function_exists('mcrypt_create_iv')) {
			return '';
		}
		return (string) @mcrypt_create_iv($bytesToGenerate, $randomSource);
	}

	/**
	 * Read random bytes from /dev/urandom if it is accessible
	 *
	 * @param $bytesToGenerate
	 * @return string
	 */
	protected static function generateRandomBytesUrandom($bytesToGenerate) {
		$bytes = '';
		$fh = @fopen('/dev/urandom', 'rb');
		if ($fh) {
				// PHP only performs buffered reads, so in reality it will always read
				// at least 4096 bytes. Thus, it costs nothing extra to read and store
				// that much so as to speed any additional invocations.
			$bytes = fread($fh, $bytesToGenerate);
			fclose($fh);
		}

		return $bytes;
	}

	/**
	 * Generate pseudo random bytes as last resort
	 *
	 * @param $bytesToReturn
	 * @return string
	 */
	protected static function generateRandomBytesFallback($bytesToReturn) {
		$bytes = '';
			// We initialize with somewhat random.
		$randomState = $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'] . base_convert(memory_get_usage() % pow(10, 6), 10, 2) . microtime() . uniqid('') . getmypid();
		while (!isset($bytes{$bytesToReturn - 1})) {
			$randomState = sha1(microtime() . mt_rand() . $randomState);
			$bytes .= sha1(mt_rand() . $randomState, TRUE);
		}
		return $bytes;
	}

	/**
	 * Returns a hex representation of a random byte string.
	 *
	 * @param integer $count Number of hex characters to return
	 * @return string Random Bytes
	 */
	public static function getRandomHexString($count) {
		return substr(bin2hex(self::generateRandomBytes(intval(($count + 1) / 2))), 0, $count);
	}

	/**
	 * Returns a given string with underscores as UpperCamelCase.
	 * Example: Converts blog_example to BlogExample
	 *
	 * @param string $string: String to be converted to camel case
	 * @return string UpperCamelCasedWord
	 */
	public static function underscoredToUpperCamelCase($string) {
		$upperCamelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', self::strtolower($string))));
		return $upperCamelCase;
	}

	/**
	 * Returns a given string with underscores as lowerCamelCase.
	 * Example: Converts minimal_value to minimalValue
	 *
	 * @param string $string: String to be converted to camel case
	 * @return string lowerCamelCasedWord
	 */
	public static function underscoredToLowerCamelCase($string) {
		$upperCamelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', self::strtolower($string))));
		$lowerCamelCase = self::lcfirst($upperCamelCase);
		return $lowerCamelCase;
	}

	/**
	 * Returns a given CamelCasedString as an lowercase string with underscores.
	 * Example: Converts BlogExample to blog_example, and minimalValue to minimal_value
	 *
	 * @param string $string String to be converted to lowercase underscore
	 * @return string lowercase_and_underscored_string
	 */
	public static function camelCaseToLowerCaseUnderscored($string) {
		return self::strtolower(preg_replace('/(?<=\w)([A-Z])/', '_\\1', $string));
	}

	/**
	 * Converts the first char of a string to lowercase if it is a latin character (A-Z).
	 * Example: Converts "Hello World" to "hello World"
	 *
	 * @param string $string The string to be used to lowercase the first character
	 * @return string The string with the first character as lowercase
	 */
	public static function lcfirst($string) {
		return self::strtolower(substr($string, 0, 1)) . substr($string, 1);
	}

	/**
	 * Checks if a given string is a Uniform Resource Locator (URL).
	 *
	 * @param string $url The URL to be validated
	 * @return boolean Whether the given URL is valid
	 */
	public static function isValidUrl($url) {
		require_once(PATH_typo3 . 'contrib/idna/idna_convert.class.php');
		$IDN = new idna_convert(array('idn_version' => 2008));

		return (filter_var($IDN->encode($url), FILTER_VALIDATE_URL) !== FALSE);
	}


	/*************************
	 *
	 * ARRAY FUNCTIONS
	 *
	 *************************/

	/**
	 * Check if an string item exists in an array.
	 * Please note that the order of function parameters is reverse compared to the PHP function in_array()!!!
	 *
	 * Comparison to PHP in_array():
	 * -> $array = array(0, 1, 2, 3);
	 * -> variant_a := t3lib_div::inArray($array, $needle)
	 * -> variant_b := in_array($needle, $array)
	 * -> variant_c := in_array($needle, $array, TRUE)
	 * +---------+-----------+-----------+-----------+
	 * | $needle | variant_a | variant_b | variant_c |
	 * +---------+-----------+-----------+-----------+
	 * | '1a'	| FALSE	 | TRUE	  | FALSE	 |
	 * | ''	  | FALSE	 | TRUE	  | FALSE	 |
	 * | '0'	 | TRUE	  | TRUE	  | FALSE	 |
	 * | 0	   | TRUE	  | TRUE	  | TRUE	  |
	 * +---------+-----------+-----------+-----------+
	 *
	 * @param array $in_array one-dimensional array of items
	 * @param string $item item to check for
	 * @return boolean TRUE if $item is in the one-dimensional array $in_array
	 */
	public static function inArray(array $in_array, $item) {
		foreach ($in_array as $val) {
			if (!is_array($val) && !strcmp($val, $item)) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Explodes a $string delimited by $delim and passes each item in the array through intval().
	 * Corresponds to t3lib_div::trimExplode(), but with conversion to integers for all values.
	 *
	 * @param string $delimiter Delimiter string to explode with
	 * @param string $string The string to explode
	 * @param boolean $onlyNonEmptyValues If set, all empty values (='') will NOT be set in output
	 * @param integer $limit If positive, the result will contain a maximum of limit elements,
	 *						 if negative, all components except the last -limit are returned,
	 *						 if zero (default), the result is not limited at all
	 * @return array Exploded values, all converted to integers
	 */
	public static function intExplode($delimiter, $string, $onlyNonEmptyValues = FALSE, $limit = 0) {
		$explodedValues = self::trimExplode($delimiter, $string, $onlyNonEmptyValues, $limit);
		return array_map('intval', $explodedValues);
	}

	/**
	 * Reverse explode which explodes the string counting from behind.
	 * Thus t3lib_div::revExplode(':','my:words:here',2) will return array('my:words','here')
	 *
	 * @param string $delimiter Delimiter string to explode with
	 * @param string $string The string to explode
	 * @param integer $count Number of array entries
	 * @return array Exploded values
	 */
	public static function revExplode($delimiter, $string, $count = 0) {
		$explodedValues = explode($delimiter, strrev($string), $count);
		$explodedValues = array_map('strrev', $explodedValues);
		return array_reverse($explodedValues);
	}

	/**
	 * Explodes a string and trims all values for whitespace in the ends.
	 * If $onlyNonEmptyValues is set, then all blank ('') values are removed.
	 *
	 * @param string $delim Delimiter string to explode with
	 * @param string $string The string to explode
	 * @param boolean $removeEmptyValues If set, all empty values will be removed in output
	 * @param integer $limit If positive, the result will contain a maximum of
	 *						 $limit elements, if negative, all components except
	 *						 the last -$limit are returned, if zero (default),
	 *						 the result is not limited at all. Attention though
	 *						 that the use of this parameter can slow down this
	 *						 function.
	 * @return array Exploded values
	 */
	public static function trimExplode($delim, $string, $removeEmptyValues = FALSE, $limit = 0) {
		$explodedValues = explode($delim, $string);

		$result = array_map('trim', $explodedValues);

		if ($removeEmptyValues) {
			$temp = array();
			foreach ($result as $value) {
				if ($value !== '') {
					$temp[] = $value;
				}
			}
			$result = $temp;
		}

		if ($limit != 0) {
			if ($limit < 0) {
				$result = array_slice($result, 0, $limit);
			} elseif (count($result) > $limit) {
				$lastElements = array_slice($result, $limit - 1);
				$result = array_slice($result, 0, $limit - 1);
				$result[] = implode($delim, $lastElements);
			}
		}

		return $result;
	}

	/**
	 * Removes the value $cmpValue from the $array if found there. Returns the modified array
	 *
	 * @param array $array Array containing the values
	 * @param string $cmpValue Value to search for and if found remove array entry where found.
	 * @return array Output array with entries removed if search string is found
	 */
	public static function removeArrayEntryByValue(array $array, $cmpValue) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$array[$k] = self::removeArrayEntryByValue($v, $cmpValue);
			} elseif (!strcmp($v, $cmpValue)) {
				unset($array[$k]);
			}
		}
		return $array;
	}

	/**
	 * Filters an array to reduce its elements to match the condition.
	 * The values in $keepItems can be optionally evaluated by a custom callback function.
	 *
	 * Example (arguments used to call this function):
	 * $array = array(
	 *		 array('aa' => array('first', 'second'),
	 *		 array('bb' => array('third', 'fourth'),
	 *		 array('cc' => array('fifth', 'sixth'),
	 * );
	 * $keepItems = array('third');
	 * $getValueFunc = create_function('$value', 'return $value[0];');
	 *
	 * Returns:
	 * array(
	 *		 array('bb' => array('third', 'fourth'),
	 * )
	 *
	 * @param array $array: The initial array to be filtered/reduced
	 * @param mixed $keepItems: The items which are allowed/kept in the array - accepts array or csv string
	 * @param string $getValueFunc: (optional) Unique function name set by create_function() used to get the value to keep
	 * @return array The filtered/reduced array with the kept items
	 */
	public static function keepItemsInArray(array $array, $keepItems, $getValueFunc = NULL) {
		if ($array) {
				// Convert strings to arrays:
			if (is_string($keepItems)) {
				$keepItems = self::trimExplode(',', $keepItems);
			}
				// create_function() returns a string:
			if (!is_string($getValueFunc)) {
				$getValueFunc = NULL;
			}
				// Do the filtering:
			if (is_array($keepItems) && count($keepItems)) {
				foreach ($array as $key => $value) {
						// Get the value to compare by using the callback function:
					$keepValue = (isset($getValueFunc) ? $getValueFunc($value) : $value);
					if (!in_array($keepValue, $keepItems)) {
						unset($array[$key]);
					}
				}
			}
		}
		return $array;
	}

	/**
	 * Implodes a multidim-array into GET-parameters (eg. &param[key][key2]=value2&param[key][key3]=value3)
	 *
	 * @param string $name Name prefix for entries. Set to blank if you wish none.
	 * @param array $theArray The (multidimensional) array to implode
	 * @param string $str (keep blank)
	 * @param boolean $skipBlank If set, parameters which were blank strings would be removed.
	 * @param boolean $rawurlencodeParamName If set, the param name itself (for example "param[key][key2]") would be rawurlencoded as well.
	 * @return string Imploded result, fx. &param[key][key2]=value2&param[key][key3]=value3
	 * @see explodeUrl2Array()
	 */
	public static function implodeArrayForUrl($name, array $theArray, $str = '', $skipBlank = FALSE, $rawurlencodeParamName = FALSE) {
		foreach ($theArray as $Akey => $AVal) {
			$thisKeyName = $name ? $name . '[' . $Akey . ']' : $Akey;
			if (is_array($AVal)) {
				$str = self::implodeArrayForUrl($thisKeyName, $AVal, $str, $skipBlank, $rawurlencodeParamName);
			} else {
				if (!$skipBlank || strcmp($AVal, '')) {
					$str .= '&' . ($rawurlencodeParamName ? rawurlencode($thisKeyName) : $thisKeyName) .
							'=' . rawurlencode($AVal);
				}
			}
		}
		return $str;
	}

	/**
	 * Explodes a string with GETvars (eg. "&id=1&type=2&ext[mykey]=3") into an array
	 *
	 * @param string $string GETvars string
	 * @param boolean $multidim If set, the string will be parsed into a multidimensional array if square brackets are used in variable names (using PHP function parse_str())
	 * @return array Array of values. All values AND keys are rawurldecoded() as they properly should be. But this means that any implosion of the array again must rawurlencode it!
	 * @see implodeArrayForUrl()
	 */
	public static function explodeUrl2Array($string, $multidim = FALSE) {
		$output = array();
		if ($multidim) {
			parse_str($string, $output);
		} else {
			$p = explode('&', $string);
			foreach ($p as $v) {
				if (strlen($v)) {
					list($pK, $pV) = explode('=', $v, 2);
					$output[rawurldecode($pK)] = rawurldecode($pV);
				}
			}
		}
		return $output;
	}

	/**
	 * Returns an array with selected keys from incoming data.
	 * (Better read source code if you want to find out...)
	 *
	 * @param string $varList List of variable/key names
	 * @param array $getArray Array from where to get values based on the keys in $varList
	 * @param boolean $GPvarAlt If set, then t3lib_div::_GP() is used to fetch the value if not found (isset) in the $getArray
	 * @return array Output array with selected variables.
	 */
	public static function compileSelectedGetVarsFromArray($varList, array $getArray, $GPvarAlt = TRUE) {
		$keys = self::trimExplode(',', $varList, 1);
		$outArr = array();
		foreach ($keys as $v) {
			if (isset($getArray[$v])) {
				$outArr[$v] = $getArray[$v];
			} elseif ($GPvarAlt) {
				$outArr[$v] = self::_GP($v);
			}
		}
		return $outArr;
	}

	/**
	 * AddSlash array
	 * This function traverses a multidimensional array and adds slashes to the values.
	 * NOTE that the input array is and argument by reference.!!
	 * Twin-function to stripSlashesOnArray
	 *
	 * @param array $theArray Multidimensional input array, (REFERENCE!)
	 * @return array
	 */
	public static function addSlashesOnArray(array &$theArray) {
		foreach ($theArray as &$value) {
			if (is_array($value)) {
				self::addSlashesOnArray($value);
			} else {
				$value = addslashes($value);
			}
		}
		unset($value);
		reset($theArray);
	}

	/**
	 * StripSlash array
	 * This function traverses a multidimensional array and strips slashes to the values.
	 * NOTE that the input array is and argument by reference.!!
	 * Twin-function to addSlashesOnArray
	 *
	 * @param array $theArray Multidimensional input array, (REFERENCE!)
	 * @return array
	 */
	public static function stripSlashesOnArray(array &$theArray) {
		foreach ($theArray as &$value) {
			if (is_array($value)) {
				self::stripSlashesOnArray($value);
			} else {
				$value = stripslashes($value);
			}
		}
		unset($value);
		reset($theArray);
	}

	/**
	 * Either slashes ($cmd=add) or strips ($cmd=strip) array $arr depending on $cmd
	 *
	 * @param array $arr Multidimensional input array
	 * @param string $cmd "add" or "strip", depending on usage you wish.
	 * @return array
	 */
	public static function slashArray(array $arr, $cmd) {
		if ($cmd == 'strip') {
			self::stripSlashesOnArray($arr);
		}
		if ($cmd == 'add') {
			self::addSlashesOnArray($arr);
		}
		return $arr;
	}

	/**
	 * Rename Array keys with a given mapping table
	 *
	 * @param array	$array Array by reference which should be remapped
	 * @param array	$mappingTable Array with remap information, array/$oldKey => $newKey)
	 */
	public static function remapArrayKeys(&$array, $mappingTable) {
		if (is_array($mappingTable)) {
			foreach ($mappingTable as $old => $new) {
				if ($new && isset($array[$old])) {
					$array[$new] = $array[$old];
					unset ($array[$old]);
				}
			}
		}
	}


	/**
	 * Merges two arrays recursively and "binary safe" (integer keys are
	 * overridden as well), overruling similar values in the first array
	 * ($arr0) with the values of the second array ($arr1)
	 * In case of identical keys, ie. keeping the values of the second.
	 *
	 * @param array $arr0 First array
	 * @param array $arr1 Second array, overruling the first array
	 * @param boolean $notAddKeys If set, keys that are NOT found in $arr0 (first array) will not be set. Thus only existing value can/will be overruled from second array.
	 * @param boolean $includeEmptyValues If set, values from $arr1 will overrule if they are empty or zero. Default: TRUE
	 * @param boolean $enableUnsetFeature If set, special values "__UNSET" can be used in the second array in order to unset array keys in the resulting array.
	 * @return array Resulting array where $arr1 values has overruled $arr0 values
	 */
	public static function array_merge_recursive_overrule(array $arr0, array $arr1, $notAddKeys = FALSE, $includeEmptyValues = TRUE, $enableUnsetFeature = TRUE) {
		foreach ($arr1 as $key => $val) {
			if ($enableUnsetFeature && $val === '__UNSET') {
				unset($arr0[$key]);
				continue;
			}
			if (is_array($arr0[$key])) {
				if (is_array($arr1[$key])) {
					$arr0[$key] = self::array_merge_recursive_overrule(
						$arr0[$key],
						$arr1[$key],
						$notAddKeys,
						$includeEmptyValues,
						$enableUnsetFeature
					);
				}
			} elseif (
				(!$notAddKeys || isset($arr0[$key])) &&
				($includeEmptyValues || $val)
			) {
				$arr0[$key] = $val;
			}
		}

		reset($arr0);
		return $arr0;
	}

	/**
	 * An array_merge function where the keys are NOT renumbered as they happen to be with the real php-array_merge function. It is "binary safe" in the sense that integer keys are overridden as well.
	 *
	 * @param array $arr1 First array
	 * @param array $arr2 Second array
	 * @return array Merged result.
	 */
	public static function array_merge(array $arr1, array $arr2) {
		return $arr2 + $arr1;
	}

	/**
	 * Filters keys off from first array that also exist in second array. Comparison is done by keys.
	 * This method is a recursive version of php array_diff_assoc()
	 *
	 * @param array $array1 Source array
	 * @param array $array2 Reduce source array by this array
	 * @return array Source array reduced by keys also present in second array
	 */
	public static function arrayDiffAssocRecursive(array $array1, array $array2) {
		$differenceArray = array();
		foreach ($array1 as $key => $value) {
			if (!array_key_exists($key, $array2)) {
				$differenceArray[$key] = $value;
			} elseif (is_array($value)) {
				if (is_array($array2[$key])) {
					$differenceArray[$key] = self::arrayDiffAssocRecursive($value, $array2[$key]);
				}
			}
		}

		return $differenceArray;
	}

	/**
	 * Takes a row and returns a CSV string of the values with $delim (default is ,) and $quote (default is ") as separator chars.
	 *
	 * @param array $row Input array of values
	 * @param string $delim Delimited, default is comma
	 * @param string $quote Quote-character to wrap around the values.
	 * @return string A single line of CSV
	 */
	public static function csvValues(array $row, $delim = ',', $quote = '"') {
		$out = array();
		foreach ($row as $value) {
			$out[] = str_replace($quote, $quote . $quote, $value);
		}
		$str = $quote . implode($quote . $delim . $quote, $out) . $quote;
		return $str;
	}

	/**
	 * Removes dots "." from end of a key identifier of TypoScript styled array.
	 * array('key.' => array('property.' => 'value')) --> array('key' => array('property' => 'value'))
	 *
	 * @param array $ts: TypoScript configuration array
	 * @return array TypoScript configuration array without dots at the end of all keys
	 */
	public static function removeDotsFromTS(array $ts) {
		$out = array();
		foreach ($ts as $key => $value) {
			if (is_array($value)) {
				$key = rtrim($key, '.');
				$out[$key] = self::removeDotsFromTS($value);
			} else {
				$out[$key] = $value;
			}
		}
		return $out;
	}

	/**
	 * Sorts an array by key recursive - uses natural sort order (aAbB-zZ)
	 *
	 * @param array $array array to be sorted recursively, passed by reference
	 * @return boolean TRUE if param is an array
	 */
	public static function naturalKeySortRecursive(&$array) {
		if (!is_array($array)) {
			return FALSE;
		}
		uksort($array, 'strnatcasecmp');
		foreach ($array as $key => $value) {
			self::naturalKeySortRecursive($array[$key]);
		}
		return TRUE;
	}


	/*************************
	 *
	 * HTML/XML PROCESSING
	 *
	 *************************/

	/**
	 * Returns an array with all attributes of the input HTML tag as key/value pairs. Attributes are only lowercase a-z
	 * $tag is either a whole tag (eg '<TAG OPTION ATTRIB=VALUE>') or the parameter list (ex ' OPTION ATTRIB=VALUE>')
	 * If an attribute is empty, then the value for the key is empty. You can check if it existed with isset()
	 *
	 * @param string $tag HTML-tag string (or attributes only)
	 * @return array Array with the attribute values.
	 */
	public static function get_tag_attributes($tag) {
		$components = self::split_tag_attributes($tag);
		$name = ''; // attribute name is stored here
		$valuemode = FALSE;
		$attributes = array();
		foreach ($components as $key => $val) {
			if ($val != '=') { // Only if $name is set (if there is an attribute, that waits for a value), that valuemode is enabled. This ensures that the attribute is assigned it's value
				if ($valuemode) {
					if ($name) {
						$attributes[$name] = $val;
						$name = '';
					}
				} else {
					if ($key = strtolower(preg_replace('/[^[:alnum:]_\:\-]/', '', $val))) {
						$attributes[$key] = '';
						$name = $key;
					}
				}
				$valuemode = FALSE;
			} else {
				$valuemode = TRUE;
			}
		}
		return $attributes;
	}

	/**
	 * Returns an array with the 'components' from an attribute list from an HTML tag. The result is normally analyzed by get_tag_attributes
	 * Removes tag-name if found
	 *
	 * @param string $tag HTML-tag string (or attributes only)
	 * @return array Array with the attribute values.
	 */
	public static function split_tag_attributes($tag) {
		$tag_tmp = trim(preg_replace('/^<[^[:space:]]*/', '', trim($tag)));
			// Removes any > in the end of the string
		$tag_tmp = trim(rtrim($tag_tmp, '>'));

		$value = array();
		while (strcmp($tag_tmp, '')) { // Compared with empty string instead , 030102
			$firstChar = substr($tag_tmp, 0, 1);
			if (!strcmp($firstChar, '"') || !strcmp($firstChar, "'")) {
				$reg = explode($firstChar, $tag_tmp, 3);
				$value[] = $reg[1];
				$tag_tmp = trim($reg[2]);
			} elseif (!strcmp($firstChar, '=')) {
				$value[] = '=';
				$tag_tmp = trim(substr($tag_tmp, 1)); // Removes = chars.
			} else {
					// There are '' around the value. We look for the next ' ' or '>'
				$reg = preg_split('/[[:space:]=]/', $tag_tmp, 2);
				$value[] = trim($reg[0]);
				$tag_tmp = trim(substr($tag_tmp, strlen($reg[0]), 1) . $reg[1]);
			}
		}
		reset($value);
		return $value;
	}

	/**
	 * Implodes attributes in the array $arr for an attribute list in eg. and HTML tag (with quotes)
	 *
	 * @param array $arr Array with attribute key/value pairs, eg. "bgcolor"=>"red", "border"=>0
	 * @param boolean $xhtmlSafe If set the resulting attribute list will have a) all attributes in lowercase (and duplicates weeded out, first entry taking precedence) and b) all values htmlspecialchar()'ed. It is recommended to use this switch!
	 * @param boolean $dontOmitBlankAttribs If TRUE, don't check if values are blank. Default is to omit attributes with blank values.
	 * @return string Imploded attributes, eg. 'bgcolor="red" border="0"'
	 */
	public static function implodeAttributes(array $arr, $xhtmlSafe = FALSE, $dontOmitBlankAttribs = FALSE) {
		if ($xhtmlSafe) {
			$newArr = array();
			foreach ($arr as $p => $v) {
				if (!isset($newArr[strtolower($p)])) {
					$newArr[strtolower($p)] = htmlspecialchars($v);
				}
			}
			$arr = $newArr;
		}
		$list = array();
		foreach ($arr as $p => $v) {
			if (strcmp($v, '') || $dontOmitBlankAttribs) {
				$list[] = $p . '="' . $v . '"';
			}
		}
		return implode(' ', $list);
	}

	/**
	 * Wraps JavaScript code XHTML ready with <script>-tags
	 * Automatic re-indenting of the JS code is done by using the first line as indent reference.
	 * This is nice for indenting JS code with PHP code on the same level.
	 *
	 * @param string $string JavaScript code
	 * @param boolean $linebreak Wrap script element in line breaks? Default is TRUE.
	 * @return string The wrapped JS code, ready to put into a XHTML page
	 */
	public static function wrapJS($string, $linebreak = TRUE) {
		if (trim($string)) {
				// <script wrapped in nl?
			$cr = $linebreak ? LF : '';

				// remove nl from the beginning
			$string = preg_replace('/^\n+/', '', $string);
				// re-ident to one tab using the first line as reference
			$match = array();
			if (preg_match('/^(\t+)/', $string, $match)) {
				$string = str_replace($match[1], TAB, $string);
			}
			$string = $cr . '<script type="text/javascript">
/*<![CDATA[*/
' . $string . '
/*]]>*/
</script>' . $cr;
		}
		return trim($string);
	}


	/**
	 * Parses XML input into a PHP array with associative keys
	 *
	 * @param string $string XML data input
	 * @param integer $depth Number of element levels to resolve the XML into an array. Any further structure will be set as XML.
	 * @return mixed The array with the parsed structure unless the XML parser returns with an error in which case the error message string is returned.
	 * @author bisqwit at iki dot fi dot not dot for dot ads dot invalid / http://dk.php.net/xml_parse_into_struct + kasperYYYY@typo3.com
	 */
	public static function xml2tree($string, $depth = 999) {
		$parser = xml_parser_create();
		$vals = array();
		$index = array();

		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
		xml_parse_into_struct($parser, $string, $vals, $index);

		if (xml_get_error_code($parser)) {
			return 'Line ' . xml_get_current_line_number($parser) . ': ' . xml_error_string(xml_get_error_code($parser));
		}
		xml_parser_free($parser);

		$stack = array(array());
		$stacktop = 0;
		$startPoint = 0;

		$tagi = array();
		foreach ($vals as $key => $val) {
			$type = $val['type'];

				// open tag:
			if ($type == 'open' || $type == 'complete') {
				$stack[$stacktop++] = $tagi;

				if ($depth == $stacktop) {
					$startPoint = $key;
				}

				$tagi = array('tag' => $val['tag']);

				if (isset($val['attributes'])) {
					$tagi['attrs'] = $val['attributes'];
				}
				if (isset($val['value'])) {
					$tagi['values'][] = $val['value'];
				}
			}
				// finish tag:
			if ($type == 'complete' || $type == 'close') {
				$oldtagi = $tagi;
				$tagi = $stack[--$stacktop];
				$oldtag = $oldtagi['tag'];
				unset($oldtagi['tag']);

				if ($depth == ($stacktop + 1)) {
					if ($key - $startPoint > 0) {
						$partArray = array_slice(
							$vals,
								$startPoint + 1,
								$key - $startPoint - 1
						);
						$oldtagi['XMLvalue'] = self::xmlRecompileFromStructValArray($partArray);
					} else {
						$oldtagi['XMLvalue'] = $oldtagi['values'][0];
					}
				}

				$tagi['ch'][$oldtag][] = $oldtagi;
				unset($oldtagi);
			}
				// cdata
			if ($type == 'cdata') {
				$tagi['values'][] = $val['value'];
			}
		}
		return $tagi['ch'];
	}

	/**
	 * Turns PHP array into XML. See array2xml()
	 *
	 * @param array $array The input PHP array with any kind of data; text, binary, integers. Not objects though.
	 * @param string $docTag Alternative document tag. Default is "phparray".
	 * @param array $options Options for the compilation. See array2xml() for description.
	 * @param string $charset Forced charset to prologue
	 * @return string An XML string made from the input content in the array.
	 * @see xml2array(),array2xml()
	 */
	public static function array2xml_cs(array $array, $docTag = 'phparray', array $options = array(), $charset = '') {

			// Set default charset unless explicitly specified
		$charset = $charset ? $charset : 'utf-8';

			// Return XML:
		return '<?xml version="1.0" encoding="' . htmlspecialchars($charset) . '" standalone="yes" ?>' . LF .
				self::array2xml($array, '', 0, $docTag, 0, $options);
	}

	/**
	 * Deprecated to call directly (unless you are aware of using XML prologues)! Use "array2xml_cs" instead (which adds an XML-prologue)
	 *
	 * Converts a PHP array into an XML string.
	 * The XML output is optimized for readability since associative keys are used as tag names.
	 * This also means that only alphanumeric characters are allowed in the tag names AND only keys NOT starting with numbers (so watch your usage of keys!). However there are options you can set to avoid this problem.
	 * Numeric keys are stored with the default tag name "numIndex" but can be overridden to other formats)
	 * The function handles input values from the PHP array in a binary-safe way; All characters below 32 (except 9,10,13) will trigger the content to be converted to a base64-string
	 * The PHP variable type of the data IS preserved as long as the types are strings, arrays, integers and booleans. Strings are the default type unless the "type" attribute is set.
	 * The output XML has been tested with the PHP XML-parser and parses OK under all tested circumstances with 4.x versions. However, with PHP5 there seems to be the need to add an XML prologue a la <?xml version="1.0" encoding="[charset]" standalone="yes" ?> - otherwise UTF-8 is assumed! Unfortunately, many times the output from this function is used without adding that prologue meaning that non-ASCII characters will break the parsing!! This suchs of course! Effectively it means that the prologue should always be prepended setting the right characterset, alternatively the system should always run as utf-8!
	 * However using MSIE to read the XML output didn't always go well: One reason could be that the character encoding is not observed in the PHP data. The other reason may be if the tag-names are invalid in the eyes of MSIE. Also using the namespace feature will make MSIE break parsing. There might be more reasons...
	 *
	 * @param array $array The input PHP array with any kind of data; text, binary, integers. Not objects though.
	 * @param string $NSprefix tag-prefix, eg. a namespace prefix like "T3:"
	 * @param integer $level Current recursion level. Don't change, stay at zero!
	 * @param string $docTag Alternative document tag. Default is "phparray".
	 * @param integer $spaceInd If greater than zero, then the number of spaces corresponding to this number is used for indenting, if less than zero - no indentation, if zero - a single TAB is used
	 * @param array $options Options for the compilation. Key "useNindex" => 0/1 (boolean: whether to use "n0, n1, n2" for num. indexes); Key "useIndexTagForNum" => "[tag for numerical indexes]"; Key "useIndexTagForAssoc" => "[tag for associative indexes"; Key "parentTagMap" => array('parentTag' => 'thisLevelTag')
	 * @param array $stackData Stack data. Don't touch.
	 * @return string An XML string made from the input content in the array.
	 * @see xml2array()
	 */
	public static function array2xml(array $array, $NSprefix = '', $level = 0, $docTag = 'phparray', $spaceInd = 0, array $options = array(), array $stackData = array()) {
			// The list of byte values which will trigger binary-safe storage. If any value has one of these char values in it, it will be encoded in base64
		$binaryChars = chr(0) . chr(1) . chr(2) . chr(3) . chr(4) . chr(5) . chr(6) . chr(7) . chr(8) .
				chr(11) . chr(12) . chr(14) . chr(15) . chr(16) . chr(17) . chr(18) . chr(19) .
				chr(20) . chr(21) . chr(22) . chr(23) . chr(24) . chr(25) . chr(26) . chr(27) . chr(28) . chr(29) .
				chr(30) . chr(31);
			// Set indenting mode:
		$indentChar = $spaceInd ? ' ' : TAB;
		$indentN = $spaceInd > 0 ? $spaceInd : 1;
		$nl = ($spaceInd >= 0 ? LF : '');

			// Init output variable:
		$output = '';

			// Traverse the input array
		foreach ($array as $k => $v) {
			$attr = '';
			$tagName = $k;

				// Construct the tag name.
			if (isset($options['grandParentTagMap'][$stackData['grandParentTagName'] . '/' . $stackData['parentTagName']])) { // Use tag based on grand-parent + parent tag name
				$attr .= ' index="' . htmlspecialchars($tagName) . '"';
				$tagName = (string) $options['grandParentTagMap'][$stackData['grandParentTagName'] . '/' . $stackData['parentTagName']];
			} elseif (isset($options['parentTagMap'][$stackData['parentTagName'] . ':_IS_NUM']) && t3lib_utility_Math::canBeInterpretedAsInteger($tagName)) { // Use tag based on parent tag name + if current tag is numeric
				$attr .= ' index="' . htmlspecialchars($tagName) . '"';
				$tagName = (string) $options['parentTagMap'][$stackData['parentTagName'] . ':_IS_NUM'];
			} elseif (isset($options['parentTagMap'][$stackData['parentTagName'] . ':' . $tagName])) { // Use tag based on parent tag name + current tag
				$attr .= ' index="' . htmlspecialchars($tagName) . '"';
				$tagName = (string) $options['parentTagMap'][$stackData['parentTagName'] . ':' . $tagName];
			} elseif (isset($options['parentTagMap'][$stackData['parentTagName']])) { // Use tag based on parent tag name:
				$attr .= ' index="' . htmlspecialchars($tagName) . '"';
				$tagName = (string) $options['parentTagMap'][$stackData['parentTagName']];
			} elseif (!strcmp(intval($tagName), $tagName)) { // If integer...;
				if ($options['useNindex']) { // If numeric key, prefix "n"
					$tagName = 'n' . $tagName;
				} else { // Use special tag for num. keys:
					$attr .= ' index="' . $tagName . '"';
					$tagName = $options['useIndexTagForNum'] ? $options['useIndexTagForNum'] : 'numIndex';
				}
			} elseif ($options['useIndexTagForAssoc']) { // Use tag for all associative keys:
				$attr .= ' index="' . htmlspecialchars($tagName) . '"';
				$tagName = $options['useIndexTagForAssoc'];
			}

				// The tag name is cleaned up so only alphanumeric chars (plus - and _) are in there and not longer than 100 chars either.
			$tagName = substr(preg_replace('/[^[:alnum:]_-]/', '', $tagName), 0, 100);

				// If the value is an array then we will call this function recursively:
			if (is_array($v)) {

					// Sub elements:
				if ($options['alt_options'][$stackData['path'] . '/' . $tagName]) {
					$subOptions = $options['alt_options'][$stackData['path'] . '/' . $tagName];
					$clearStackPath = $subOptions['clearStackPath'];
				} else {
					$subOptions = $options;
					$clearStackPath = FALSE;
				}

				$content = $nl .
						self::array2xml(
							$v,
							$NSprefix,
								$level + 1,
							'',
							$spaceInd,
							$subOptions,
							array(
								'parentTagName' => $tagName,
								'grandParentTagName' => $stackData['parentTagName'],
								'path' => $clearStackPath ? '' : $stackData['path'] . '/' . $tagName,
							)
						) .
						($spaceInd >= 0 ? str_pad('', ($level + 1) * $indentN, $indentChar) : '');
				if ((int) $options['disableTypeAttrib'] != 2) { // Do not set "type = array". Makes prettier XML but means that empty arrays are not restored with xml2array
					$attr .= ' type="array"';
				}
			} else { // Just a value:

					// Look for binary chars:
				$vLen = strlen($v); // check for length, because PHP 5.2.0 may crash when first argument of strcspn is empty
				if ($vLen && strcspn($v, $binaryChars) != $vLen) { // Go for base64 encoding if the initial segment NOT matching any binary char has the same length as the whole string!
						// If the value contained binary chars then we base64-encode it an set an attribute to notify this situation:
					$content = $nl . chunk_split(base64_encode($v));
					$attr .= ' base64="1"';
				} else {
						// Otherwise, just htmlspecialchar the stuff:
					$content = htmlspecialchars($v);
					$dType = gettype($v);
					if ($dType == 'string') {
						if ($options['useCDATA'] && $content != $v) {
							$content = '<![CDATA[' . $v . ']]>';
						}
					} elseif (!$options['disableTypeAttrib']) {
						$attr .= ' type="' . $dType . '"';
					}
				}
			}

				// Add the element to the output string:
			$output .= ($spaceInd >= 0 ? str_pad('', ($level + 1) * $indentN, $indentChar) : '') . '<' . $NSprefix . $tagName . $attr . '>' . $content . '</' . $NSprefix . $tagName . '>' . $nl;
		}

			// If we are at the outer-most level, then we finally wrap it all in the document tags and return that as the value:
		if (!$level) {
			$output =
					'<' . $docTag . '>' . $nl .
							$output .
							'</' . $docTag . '>';
		}

		return $output;
	}

	/**
	 * Converts an XML string to a PHP array.
	 * This is the reverse function of array2xml()
	 * This is a wrapper for xml2arrayProcess that adds a two-level cache
	 *
	 * @param string $string XML content to convert into an array
	 * @param string $NSprefix The tag-prefix resolve, eg. a namespace like "T3:"
	 * @param boolean $reportDocTag If set, the document tag will be set in the key "_DOCUMENT_TAG" of the output array
	 * @return mixed If the parsing had errors, a string with the error message is returned. Otherwise an array with the content.
	 * @see array2xml(),xml2arrayProcess()
	 */
	public static function xml2array($string, $NSprefix = '', $reportDocTag = FALSE) {
		static $firstLevelCache = array();

		$identifier = md5($string . $NSprefix . ($reportDocTag ? '1' : '0'));

			// look up in first level cache
		if (!empty($firstLevelCache[$identifier])) {
			$array = $firstLevelCache[$identifier];
		} else {
				// look up in second level cache
			$cacheContent = t3lib_pageSelect::getHash($identifier, 0);
			$array = unserialize($cacheContent);

			if ($array === FALSE) {
				$array = self::xml2arrayProcess($string, $NSprefix, $reportDocTag);
				t3lib_pageSelect::storeHash($identifier, serialize($array), 'ident_xml2array');
			}
				// store content in first level cache
			$firstLevelCache[$identifier] = $array;
		}
		return $array;
	}

	/**
	 * Converts an XML string to a PHP array.
	 * This is the reverse function of array2xml()
	 *
	 * @param string $string XML content to convert into an array
	 * @param string $NSprefix The tag-prefix resolve, eg. a namespace like "T3:"
	 * @param boolean $reportDocTag If set, the document tag will be set in the key "_DOCUMENT_TAG" of the output array
	 * @return mixed If the parsing had errors, a string with the error message is returned. Otherwise an array with the content.
	 * @see array2xml()
	 */
	protected static function xml2arrayProcess($string, $NSprefix = '', $reportDocTag = FALSE) {
			// Create parser:
		$parser = xml_parser_create();
		$vals = array();
		$index = array();

		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);

			// default output charset is UTF-8, only ASCII, ISO-8859-1 and UTF-8 are supported!!!
		$match = array();
		preg_match('/^[[:space:]]*<\?xml[^>]*encoding[[:space:]]*=[[:space:]]*"([^"]*)"/', substr($string, 0, 200), $match);
		$theCharset = $match[1] ? $match[1] : 'utf-8';
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $theCharset); // us-ascii / utf-8 / iso-8859-1

			// Parse content:
		xml_parse_into_struct($parser, $string, $vals, $index);

			// If error, return error message:
		if (xml_get_error_code($parser)) {
			return 'Line ' . xml_get_current_line_number($parser) . ': ' . xml_error_string(xml_get_error_code($parser));
		}
		xml_parser_free($parser);

			// Init vars:
		$stack = array(array());
		$stacktop = 0;
		$current = array();
		$tagName = '';
		$documentTag = '';

			// Traverse the parsed XML structure:
		foreach ($vals as $key => $val) {

				// First, process the tag-name (which is used in both cases, whether "complete" or "close")
			$tagName = $val['tag'];
			if (!$documentTag) {
				$documentTag = $tagName;
			}

				// Test for name space:
			$tagName = ($NSprefix && substr($tagName, 0, strlen($NSprefix)) == $NSprefix) ? substr($tagName, strlen($NSprefix)) : $tagName;

				// Test for numeric tag, encoded on the form "nXXX":
			$testNtag = substr($tagName, 1); // Closing tag.
			$tagName = (substr($tagName, 0, 1) == 'n' && !strcmp(intval($testNtag), $testNtag)) ? intval($testNtag) : $tagName;

				// Test for alternative index value:
			if (strlen($val['attributes']['index'])) {
				$tagName = $val['attributes']['index'];
			}

				// Setting tag-values, manage stack:
			switch ($val['type']) {
				case 'open': // If open tag it means there is an array stored in sub-elements. Therefore increase the stackpointer and reset the accumulation array:
					$current[$tagName] = array(); // Setting blank place holder
					$stack[$stacktop++] = $current;
					$current = array();
					break;
				case 'close': // If the tag is "close" then it is an array which is closing and we decrease the stack pointer.
					$oldCurrent = $current;
					$current = $stack[--$stacktop];
					end($current); // Going to the end of array to get placeholder key, key($current), and fill in array next:
					$current[key($current)] = $oldCurrent;
					unset($oldCurrent);
					break;
				case 'complete': // If "complete", then it's a value. If the attribute "base64" is set, then decode the value, otherwise just set it.
					if ($val['attributes']['base64']) {
						$current[$tagName] = base64_decode($val['value']);
					} else {
						$current[$tagName] = (string) $val['value']; // Had to cast it as a string - otherwise it would be evaluate FALSE if tested with isset()!!

							// Cast type:
						switch ((string) $val['attributes']['type']) {
							case 'integer':
								$current[$tagName] = (integer) $current[$tagName];
								break;
							case 'double':
								$current[$tagName] = (double) $current[$tagName];
								break;
							case 'boolean':
								$current[$tagName] = (bool) $current[$tagName];
								break;
							case 'array':
								$current[$tagName] = array(); // MUST be an empty array since it is processed as a value; Empty arrays would end up here because they would have no tags inside...
								break;
						}
					}
					break;
			}
		}

		if ($reportDocTag) {
			$current[$tagName]['_DOCUMENT_TAG'] = $documentTag;
		}

			// Finally return the content of the document tag.
		return $current[$tagName];
	}

	/**
	 * This implodes an array of XML parts (made with xml_parse_into_struct()) into XML again.
	 *
	 * @param array $vals An array of XML parts, see xml2tree
	 * @return string Re-compiled XML data.
	 */
	public static function xmlRecompileFromStructValArray(array $vals) {
		$XMLcontent = '';

		foreach ($vals as $val) {
			$type = $val['type'];

				// open tag:
			if ($type == 'open' || $type == 'complete') {
				$XMLcontent .= '<' . $val['tag'];
				if (isset($val['attributes'])) {
					foreach ($val['attributes'] as $k => $v) {
						$XMLcontent .= ' ' . $k . '="' . htmlspecialchars($v) . '"';
					}
				}
				if ($type == 'complete') {
					if (isset($val['value'])) {
						$XMLcontent .= '>' . htmlspecialchars($val['value']) . '</' . $val['tag'] . '>';
					} else {
						$XMLcontent .= '/>';
					}
				} else {
					$XMLcontent .= '>';
				}

				if ($type == 'open' && isset($val['value'])) {
					$XMLcontent .= htmlspecialchars($val['value']);
				}
			}
				// finish tag:
			if ($type == 'close') {
				$XMLcontent .= '</' . $val['tag'] . '>';
			}
				// cdata
			if ($type == 'cdata') {
				$XMLcontent .= htmlspecialchars($val['value']);
			}
		}

		return $XMLcontent;
	}

	/**
	 * Extracts the attributes (typically encoding and version) of an XML prologue (header).
	 *
	 * @param string $xmlData XML data
	 * @return array Attributes of the xml prologue (header)
	 */
	public static function xmlGetHeaderAttribs($xmlData) {
		$match = array();
		if (preg_match('/^\s*<\?xml([^>]*)\?\>/', $xmlData, $match)) {
			return self::get_tag_attributes($match[1]);
		}
	}

	/**
	 * Minifies JavaScript
	 *
	 * @param string $script Script to minify
	 * @param string $error Error message (if any)
	 * @return string Minified script or source string if error happened
	 */
	public static function minifyJavaScript($script, &$error = '') {
		require_once(PATH_typo3 . 'contrib/jsmin/jsmin.php');
		try {
			$error = '';
			$script = trim(JSMin::minify(str_replace(CR, '', $script)));
		}
		catch (JSMinException $e) {
			$error = 'Error while minifying JavaScript: ' . $e->getMessage();
			self::devLog($error, 't3lib_div', 2,
				array('JavaScript' => $script, 'Stack trace' => $e->getTrace()));
		}
		return $script;
	}


	/*************************
	 *
	 * FILES FUNCTIONS
	 *
	 *************************/

	/**
	 * Reads the file or url $url and returns the content
	 * If you are having trouble with proxys when reading URLs you can configure your way out of that with settings like $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] etc.
	 *
	 * @param string $url File/URL to read
	 * @param integer $includeHeader Whether the HTTP header should be fetched or not. 0=disable, 1=fetch header+content, 2=fetch header only
	 * @param array $requestHeaders HTTP headers to be used in the request
	 * @param array $report Error code/message and, if $includeHeader is 1, response meta data (HTTP status and content type)
	 * @return mixed The content from the resource given as input. FALSE if an error has occured.
	 */
	public static function getUrl($url, $includeHeader = 0, $requestHeaders = FALSE, &$report = NULL) {
		$content = FALSE;

		if (isset($report)) {
			$report['error'] = 0;
			$report['message'] = '';
		}

			// use cURL for: http, https, ftp, ftps, sftp and scp
		if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] == '1' && preg_match('/^(?:http|ftp)s?|s(?:ftp|cp):/', $url)) {
			if (isset($report)) {
				$report['lib'] = 'cURL';
			}

				// External URL without error checking.
			if (!function_exists('curl_init') || !($ch = curl_init())) {
				if (isset($report)) {
					$report['error'] = -1;
					$report['message'] = 'Couldn\'t initialize cURL.';
				}
				return FALSE;
			}

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, $includeHeader ? 1 : 0);
			curl_setopt($ch, CURLOPT_NOBODY, $includeHeader == 2 ? 1 : 0);
			curl_setopt($ch, CURLOPT_HTTPGET, $includeHeader == 2 ? 'HEAD' : 'GET');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, max(0, intval($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlTimeout'])));

			$followLocation = @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

			if (is_array($requestHeaders)) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
			}

				// (Proxy support implemented by Arco <arco@appeltaart.mine.nu>)
			if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyServer']) {
				curl_setopt($ch, CURLOPT_PROXY, $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyServer']);

				if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyTunnel']) {
					curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyTunnel']);
				}
				if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyUserPass']) {
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyUserPass']);
				}
			}
			$content = curl_exec($ch);
			if (isset($report)) {
				if ($content === FALSE) {
					$report['error'] = curl_errno($ch);
					$report['message'] = curl_error($ch);
				} else {
					$curlInfo = curl_getinfo($ch);
						// We hit a redirection but we couldn't follow it
					if (!$followLocation && $curlInfo['status'] >= 300 && $curlInfo['status'] < 400) {
						$report['error'] = -1;
						$report['message'] = 'Couldn\'t follow location redirect (PHP configuration option open_basedir is in effect).';
					} elseif ($includeHeader) {
							// Set only for $includeHeader to work exactly like PHP variant
						$report['http_code'] = $curlInfo['http_code'];
						$report['content_type'] = $curlInfo['content_type'];
					}
				}
			}
			curl_close($ch);

		} elseif ($includeHeader) {
			if (isset($report)) {
				$report['lib'] = 'socket';
			}
			$parsedURL = parse_url($url);
			if (!preg_match('/^https?/', $parsedURL['scheme'])) {
				if (isset($report)) {
					$report['error'] = -1;
					$report['message'] = 'Reading headers is not allowed for this protocol.';
				}
				return FALSE;
			}
			$port = intval($parsedURL['port']);
			if ($port < 1) {
				if ($parsedURL['scheme'] == 'http') {
					$port = ($port > 0 ? $port : 80);
					$scheme = '';
				} else {
					$port = ($port > 0 ? $port : 443);
					$scheme = 'ssl://';
				}
			}
			$errno = 0;
				// $errstr = '';
			$fp = @fsockopen($scheme . $parsedURL['host'], $port, $errno, $errstr, 2.0);
			if (!$fp || $errno > 0) {
				if (isset($report)) {
					$report['error'] = $errno ? $errno : -1;
					$report['message'] = $errno ? ($errstr ? $errstr : 'Socket error.') : 'Socket initialization error.';
				}
				return FALSE;
			}
			$method = ($includeHeader == 2) ? 'HEAD' : 'GET';
			$msg = $method . ' ' . (isset($parsedURL['path']) ? $parsedURL['path'] : '/') .
					($parsedURL['query'] ? '?' . $parsedURL['query'] : '') .
					' HTTP/1.0' . CRLF . 'Host: ' .
					$parsedURL['host'] . "\r\nConnection: close\r\n";
			if (is_array($requestHeaders)) {
				$msg .= implode(CRLF, $requestHeaders) . CRLF;
			}
			$msg .= CRLF;

			fputs($fp, $msg);
			while (!feof($fp)) {
				$line = fgets($fp, 2048);
				if (isset($report)) {
					if (preg_match('|^HTTP/\d\.\d +(\d+)|', $line, $status)) {
						$report['http_code'] = $status[1];
					}
					elseif (preg_match('/^Content-Type: *(.*)/i', $line, $type)) {
						$report['content_type'] = $type[1];
					}
				}
				$content .= $line;
				if (!strlen(trim($line))) {
					break; // Stop at the first empty line (= end of header)
				}
			}
			if ($includeHeader != 2) {
				$content .= stream_get_contents($fp);
			}
			fclose($fp);

		} elseif (is_array($requestHeaders)) {
			if (isset($report)) {
				$report['lib'] = 'file/context';
			}
			$parsedURL = parse_url($url);
			if (!preg_match('/^https?/', $parsedURL['scheme'])) {
				if (isset($report)) {
					$report['error'] = -1;
					$report['message'] = 'Sending request headers is not allowed for this protocol.';
				}
				return FALSE;
			}
			$ctx = stream_context_create(array(
				'http' => array(
					'header' => implode(CRLF, $requestHeaders)
				)
			)
			);

			$content = @file_get_contents($url, FALSE, $ctx);

			if ($content === FALSE && isset($report)) {
				$report['error'] = -1;
				$report['message'] = 'Couldn\'t get URL: ' . implode(LF, $http_response_header);
			}
		} else {
			if (isset($report)) {
				$report['lib'] = 'file';
			}

			$content = @file_get_contents($url);

			if ($content === FALSE && isset($report)) {
				$report['error'] = -1;
				$report['message'] = 'Couldn\'t get URL: ' . implode(LF, $http_response_header);
			}
		}

		return $content;
	}

	/**
	 * Writes $content to the file $file
	 *
	 * @param string $file Filepath to write to
	 * @param string $content Content to write
	 * @return boolean TRUE if the file was successfully opened and written to.
	 */
	public static function writeFile($file, $content) {
		if (!@is_file($file)) {
			$changePermissions = TRUE;
		}

		if ($fd = fopen($file, 'wb')) {
			$res = fwrite($fd, $content);
			fclose($fd);

			if ($res === FALSE) {
				return FALSE;
			}

			if ($changePermissions) { // Change the permissions only if the file has just been created
				self::fixPermissions($file);
			}

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Sets the file system mode and group ownership of a file or a folder.
	 *
	 * @param string $path Path of file or folder, must not be escaped. Path can be absolute or relative
	 * @param boolean $recursive If set, also fixes permissions of files and folders in the folder (if $path is a folder)
	 * @return mixed TRUE on success, FALSE on error, always TRUE on Windows OS
	 */
	public static function fixPermissions($path, $recursive = FALSE) {
		if (TYPO3_OS != 'WIN') {
			$result = FALSE;

				// Make path absolute
			if (!self::isAbsPath($path)) {
				$path = self::getFileAbsFileName($path, FALSE);
			}

			if (self::isAllowedAbsPath($path)) {
				if (@is_file($path)) {
						// "@" is there because file is not necessarily OWNED by the user
					$result = @chmod($path, octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['fileCreateMask']));
				} elseif (@is_dir($path)) {
						// "@" is there because file is not necessarily OWNED by the user
					$result = @chmod($path, octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask']));
				}

					// Set createGroup if not empty
				if ($GLOBALS['TYPO3_CONF_VARS']['BE']['createGroup']) {
						// "@" is there because file is not necessarily OWNED by the user
					$changeGroupResult = @chgrp($path, $GLOBALS['TYPO3_CONF_VARS']['BE']['createGroup']);
					$result = $changeGroupResult ? $result : FALSE;
				}

					// Call recursive if recursive flag if set and $path is directory
				if ($recursive && @is_dir($path)) {
					$handle = opendir($path);
					while (($file = readdir($handle)) !== FALSE) {
						$recursionResult = NULL;
						if ($file !== '.' && $file !== '..') {
							if (@is_file($path . '/' . $file)) {
								$recursionResult = self::fixPermissions($path . '/' . $file);
							} elseif (@is_dir($path . '/' . $file)) {
								$recursionResult = self::fixPermissions($path . '/' . $file, TRUE);
							}
							if (isset($recursionResult) && !$recursionResult) {
								$result = FALSE;
							}
						}
					}
					closedir($handle);
				}
			}
		} else {
			$result = TRUE;
		}
		return $result;
	}

	/**
	 * Writes $content to a filename in the typo3temp/ folder (and possibly one or two subfolders...)
	 * Accepts an additional subdirectory in the file path!
	 *
	 * @param string $filepath Absolute file path to write to inside "typo3temp/". First part of this string must match PATH_site."typo3temp/"
	 * @param string $content Content string to write
	 * @return string Returns NULL on success, otherwise an error string telling about the problem.
	 */
	public static function writeFileToTypo3tempDir($filepath, $content) {

			// Parse filepath into directory and basename:
		$fI = pathinfo($filepath);
		$fI['dirname'] .= '/';

			// Check parts:
		if (self::validPathStr($filepath) && $fI['basename'] && strlen($fI['basename']) < 60) {
			if (defined('PATH_site')) {
				$dirName = PATH_site . 'typo3temp/'; // Setting main temporary directory name (standard)
				if (@is_dir($dirName)) {
					if (self::isFirstPartOfStr($fI['dirname'], $dirName)) {

							// Checking if the "subdir" is found:
						$subdir = substr($fI['dirname'], strlen($dirName));
						if ($subdir) {
							if (preg_match('/^[[:alnum:]_]+\/$/', $subdir) || preg_match('/^[[:alnum:]_]+\/[[:alnum:]_]+\/$/', $subdir)) {
								$dirName .= $subdir;
								if (!@is_dir($dirName)) {
									self::mkdir_deep(PATH_site . 'typo3temp/', $subdir);
								}
							} else {
								return 'Subdir, "' . $subdir . '", was NOT on the form "[[:alnum:]_]/" or  "[[:alnum:]_]/[[:alnum:]_]/"';
							}
						}
							// Checking dir-name again (sub-dir might have been created):
						if (@is_dir($dirName)) {
							if ($filepath == $dirName . $fI['basename']) {
								self::writeFile($filepath, $content);
								if (!@is_file($filepath)) {
									return 'The file was not written to the disk. Please, check that you have write permissions to the typo3temp/ directory.';
								}
							} else {
								return 'Calculated filelocation didn\'t match input $filepath!';
							}
						} else {
							return '"' . $dirName . '" is not a directory!';
						}
					} else {
						return '"' . $fI['dirname'] . '" was not within directory PATH_site + "typo3temp/"';
					}
				} else {
					return 'PATH_site + "typo3temp/" was not a directory!';
				}
			} else {
				return 'PATH_site constant was NOT defined!';
			}
		} else {
			return 'Input filepath "' . $filepath . '" was generally invalid!';
		}
	}

	/**
	 * Wrapper function for mkdir.
	 * Sets folder permissions according to $GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask']
	 * and group ownership according to $GLOBALS['TYPO3_CONF_VARS']['BE']['createGroup']
	 *
	 * @param string $newFolder Absolute path to folder, see PHP mkdir() function. Removes trailing slash internally.
	 * @return boolean TRUE if @mkdir went well!
	 */
	public static function mkdir($newFolder) {
		$result = @mkdir($newFolder, octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask']));
		if ($result) {
			self::fixPermissions($newFolder);
		}
		return $result;
	}

	/**
	 * Creates a directory - including parent directories if necessary and
	 * sets permissions on newly created directories.
	 *
	 * @param string $directory Target directory to create. Must a have trailing slash
	 * 		if second parameter is given!
	 * 		Example: "/root/typo3site/typo3temp/foo/"
	 * @param string $deepDirectory Directory to create. This second parameter
	 * 		is kept for backwards compatibility since 4.6 where this method
	 * 		was split into a base directory and a deep directory to be created.
	 * 		Example: "xx/yy/" which creates "/root/typo3site/xx/yy/" if $directory is "/root/typo3site/"
	 * @return void
	 * @throws \InvalidArgumentException If $directory or $deepDirectory are not strings
	 * @throws \RuntimeException If directory could not be created
	 */
	public static function mkdir_deep($directory, $deepDirectory = '') {
		if (!is_string($directory)) {
			throw new \InvalidArgumentException(
				'The specified directory is of type "' . gettype($directory) . '" but a string is expected.',
				1303662955
			);
		}
		if (!is_string($deepDirectory)) {
			throw new \InvalidArgumentException(
				'The specified directory is of type "' . gettype($deepDirectory) . '" but a string is expected.',
				1303662956
			);
		}

		$fullPath = $directory . $deepDirectory;
		if (!is_dir($fullPath) && strlen($fullPath) > 0) {
			$firstCreatedPath = self::createDirectoryPath($fullPath);
			if ($firstCreatedPath !== '') {
				self::fixPermissions($firstCreatedPath, TRUE);
			}
		}
	}

	/**
	 * Creates directories for the specified paths if they do not exist. This
	 * functions sets proper permission mask but does not set proper user and
	 * group.
	 *
	 * @static
	 * @param string $fullDirectoryPath
	 * @return string Path to the the first created directory in the hierarchy
	 * @see t3lib_div::mkdir_deep
	 * @throws \RuntimeException If directory could not be created
	 */
	protected static function createDirectoryPath($fullDirectoryPath) {
		$currentPath = $fullDirectoryPath;
		$firstCreatedPath = '';
		$permissionMask = octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask']);
		if (!@is_dir($currentPath)) {
			do {
				$firstCreatedPath = $currentPath;
				$separatorPosition = strrpos($currentPath, DIRECTORY_SEPARATOR);
				$currentPath = substr($currentPath, 0, $separatorPosition);
			} while (!is_dir($currentPath) && $separatorPosition !== FALSE);

			$result = @mkdir($fullDirectoryPath, $permissionMask, TRUE);
			if (!$result) {
				throw new \RuntimeException('Could not create directory "' . $fullDirectoryPath . '"!', 1170251400);
			}
		}
		return $firstCreatedPath;
	}

	/**
	 * Wrapper function for rmdir, allowing recursive deletion of folders and files
	 *
	 * @param string $path Absolute path to folder, see PHP rmdir() function. Removes trailing slash internally.
	 * @param boolean $removeNonEmpty Allow deletion of non-empty directories
	 * @return boolean TRUE if @rmdir went well!
	 */
	public static function rmdir($path, $removeNonEmpty = FALSE) {
		$OK = FALSE;
		$path = preg_replace('|/$|', '', $path); // Remove trailing slash

		if (file_exists($path)) {
			$OK = TRUE;

			if (is_dir($path)) {
				if ($removeNonEmpty == TRUE && $handle = opendir($path)) {
					while ($OK && FALSE !== ($file = readdir($handle))) {
						if ($file == '.' || $file == '..') {
							continue;
						}
						$OK = self::rmdir($path . '/' . $file, $removeNonEmpty);
					}
					closedir($handle);
				}
				if ($OK) {
					$OK = rmdir($path);
				}

			} else { // If $dirname is a file, simply remove it
				$OK = unlink($path);
			}

			clearstatcache();
		}

		return $OK;
	}

	/**
	 * Returns an array with the names of folders in a specific path
	 * Will return 'error' (string) if there were an error with reading directory content.
	 *
	 * @param string $path Path to list directories from
	 * @return array Returns an array with the directory entries as values. If no path, the return value is nothing.
	 */
	public static function get_dirs($path) {
		if ($path) {
			if (is_dir($path)) {
				$dir = scandir($path);
				$dirs = array();
				foreach ($dir as $entry) {
					if (is_dir($path . '/' . $entry) && $entry != '..' && $entry != '.') {
						$dirs[] = $entry;
					}
				}
			} else {
				$dirs = 'error';
			}
		}
		return $dirs;
	}

	/**
	 * Returns an array with the names of files in a specific path
	 *
	 * @param string $path Is the path to the file
	 * @param string $extensionList is the comma list of extensions to read only (blank = all)
	 * @param boolean $prependPath If set, then the path is prepended the file names. Otherwise only the file names are returned in the array
	 * @param string $order is sorting: 1= sort alphabetically, 'mtime' = sort by modification time.
	 *
	 * @param string $excludePattern A comma separated list of file names to exclude, no wildcards
	 * @return array Array of the files found
	 */
	public static function getFilesInDir($path, $extensionList = '', $prependPath = FALSE, $order = '', $excludePattern = '') {

			// Initialize variables:
		$filearray = array();
		$sortarray = array();
		$path = rtrim($path, '/');

			// Find files+directories:
		if (@is_dir($path)) {
			$extensionList = strtolower($extensionList);
			$d = dir($path);
			if (is_object($d)) {
				while ($entry = $d->read()) {
					if (@is_file($path . '/' . $entry)) {
						$fI = pathinfo($entry);
						$key = md5($path . '/' . $entry); // Don't change this ever - extensions may depend on the fact that the hash is an md5 of the path! (import/export extension)
						if ((!strlen($extensionList) || self::inList($extensionList, strtolower($fI['extension']))) && (!strlen($excludePattern) || !preg_match('/^' . $excludePattern . '$/', $entry))) {
							$filearray[$key] = ($prependPath ? $path . '/' : '') . $entry;
							if ($order == 'mtime') {
								$sortarray[$key] = filemtime($path . '/' . $entry);
							}
							elseif ($order) {
								$sortarray[$key] = strtolower($entry);
							}
						}
					}
				}
				$d->close();
			} else {
				return 'error opening path: "' . $path . '"';
			}
		}

			// Sort them:
		if ($order) {
			asort($sortarray);
			$newArr = array();
			foreach ($sortarray as $k => $v) {
				$newArr[$k] = $filearray[$k];
			}
			$filearray = $newArr;
		}

			// Return result
		reset($filearray);
		return $filearray;
	}

	/**
	 * Recursively gather all files and folders of a path.
	 *
	 * @param array $fileArr Empty input array (will have files added to it)
	 * @param string $path The path to read recursively from (absolute) (include trailing slash!)
	 * @param string $extList Comma list of file extensions: Only files with extensions in this list (if applicable) will be selected.
	 * @param boolean $regDirs If set, directories are also included in output.
	 * @param integer $recursivityLevels The number of levels to dig down...
	 * @param string $excludePattern regex pattern of files/directories to exclude
	 * @return array An array with the found files/directories.
	 */
	public static function getAllFilesAndFoldersInPath(array $fileArr, $path, $extList = '', $regDirs = FALSE, $recursivityLevels = 99, $excludePattern = '') {
		if ($regDirs) {
			$fileArr[] = $path;
		}
		$fileArr = array_merge($fileArr, self::getFilesInDir($path, $extList, 1, 1, $excludePattern));

		$dirs = self::get_dirs($path);
		if (is_array($dirs) && $recursivityLevels > 0) {
			foreach ($dirs as $subdirs) {
				if ((string) $subdirs != '' && (!strlen($excludePattern) || !preg_match('/^' . $excludePattern . '$/', $subdirs))) {
					$fileArr = self::getAllFilesAndFoldersInPath($fileArr, $path . $subdirs . '/', $extList, $regDirs, $recursivityLevels - 1, $excludePattern);
				}
			}
		}
		return $fileArr;
	}

	/**
	 * Removes the absolute part of all files/folders in fileArr
	 *
	 * @param array $fileArr: The file array to remove the prefix from
	 * @param string $prefixToRemove: The prefix path to remove (if found as first part of string!)
	 * @return array The input $fileArr processed.
	 */
	public static function removePrefixPathFromList(array $fileArr, $prefixToRemove) {
		foreach ($fileArr as $k => &$absFileRef) {
			if (self::isFirstPartOfStr($absFileRef, $prefixToRemove)) {
				$absFileRef = substr($absFileRef, strlen($prefixToRemove));
			} else {
				return 'ERROR: One or more of the files was NOT prefixed with the prefix-path!';
			}
		}
		unset($absFileRef);
		return $fileArr;
	}

	/**
	 * Fixes a path for windows-backslashes and reduces double-slashes to single slashes
	 *
	 * @param string $theFile File path to process
	 * @return string
	 */
	public static function fixWindowsFilePath($theFile) {
		return str_replace('//', '/', str_replace('\\', '/', $theFile));
	}

	/**
	 * Resolves "../" sections in the input path string.
	 * For example "fileadmin/directory/../other_directory/" will be resolved to "fileadmin/other_directory/"
	 *
	 * @param string $pathStr File path in which "/../" is resolved
	 * @return string
	 */
	public static function resolveBackPath($pathStr) {
		$parts = explode('/', $pathStr);
		$output = array();
		$c = 0;
		foreach ($parts as $pV) {
			if ($pV == '..') {
				if ($c) {
					array_pop($output);
					$c--;
				} else {
					$output[] = $pV;
				}
			} else {
				$c++;
				$output[] = $pV;
			}
		}
		return implode('/', $output);
	}

	/**
	 * Prefixes a URL used with 'header-location' with 'http://...' depending on whether it has it already.
	 * - If already having a scheme, nothing is prepended
	 * - If having REQUEST_URI slash '/', then prefixing 'http://[host]' (relative to host)
	 * - Otherwise prefixed with TYPO3_REQUEST_DIR (relative to current dir / TYPO3_REQUEST_DIR)
	 *
	 * @param string $path URL / path to prepend full URL addressing to.
	 * @return string
	 */
	public static function locationHeaderUrl($path) {
		$uI = parse_url($path);
		if (substr($path, 0, 1) == '/') { // relative to HOST
			$path = self::getIndpEnv('TYPO3_REQUEST_HOST') . $path;
		} elseif (!$uI['scheme']) { // No scheme either
			$path = self::getIndpEnv('TYPO3_REQUEST_DIR') . $path;
		}
		return $path;
	}

	/**
	 * Returns the maximum upload size for a file that is allowed. Measured in KB.
	 * This might be handy to find out the real upload limit that is possible for this
	 * TYPO3 installation. The first parameter can be used to set something that overrides
	 * the maxFileSize, usually for the TCA values.
	 *
	 * @param integer $localLimit: the number of Kilobytes (!) that should be used as
	 *						the initial Limit, otherwise $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'] will be used
	 * @return integer the maximum size of uploads that are allowed (measured in kilobytes)
	 */
	public static function getMaxUploadFileSize($localLimit = 0) {
			// don't allow more than the global max file size at all
		$t3Limit = (intval($localLimit > 0 ? $localLimit : $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize']));
			// as TYPO3 is handling the file size in KB, multiply by 1024 to get bytes
		$t3Limit = $t3Limit * 1024;

			// check for PHP restrictions of the maximum size of one of the $_FILES
		$phpUploadLimit = self::getBytesFromSizeMeasurement(ini_get('upload_max_filesize'));
			// check for PHP restrictions of the maximum $_POST size
		$phpPostLimit = self::getBytesFromSizeMeasurement(ini_get('post_max_size'));
			// if the total amount of post data is smaller (!) than the upload_max_filesize directive,
			// then this is the real limit in PHP
		$phpUploadLimit = ($phpPostLimit < $phpUploadLimit ? $phpPostLimit : $phpUploadLimit);

			// is the allowed PHP limit (upload_max_filesize) lower than the TYPO3 limit?, also: revert back to KB
		return floor($phpUploadLimit < $t3Limit ? $phpUploadLimit : $t3Limit) / 1024;
	}

	/**
	 * Gets the bytes value from a measurement string like "100k".
	 *
	 * @param string $measurement: The measurement (e.g. "100k")
	 * @return integer The bytes value (e.g. 102400)
	 */
	public static function getBytesFromSizeMeasurement($measurement) {
		$bytes = doubleval($measurement);
		if (stripos($measurement, 'G')) {
			$bytes *= 1024 * 1024 * 1024;
		} elseif (stripos($measurement, 'M')) {
			$bytes *= 1024 * 1024;
		} elseif (stripos($measurement, 'K')) {
			$bytes *= 1024;
		}
		return $bytes;
	}

	/**
	 * Retrieves the maximum path length that is valid in the current environment.
	 *
	 * @return integer The maximum available path length
	 */
	public static function getMaximumPathLength() {
		return PHP_MAXPATHLEN;
	}


	/**
	 * Function for static version numbers on files, based on the filemtime
	 *
	 * This will make the filename automatically change when a file is
	 * changed, and by that re-cached by the browser. If the file does not
	 * exist physically the original file passed to the function is
	 * returned without the timestamp.
	 *
	 * Behaviour is influenced by the setting
	 * TYPO3_CONF_VARS[TYPO3_MODE][versionNumberInFilename]
	 * = TRUE (BE) / "embed" (FE) : modify filename
	 * = FALSE (BE) / "querystring" (FE) : add timestamp as parameter
	 *
	 * @param string $file Relative path to file including all potential query parameters (not htmlspecialchared yet)
	 * @param boolean $forceQueryString If settings would suggest to embed in filename, this parameter allows us to force the versioning to occur in the query string. This is needed for scriptaculous.js which cannot have a different filename in order to load its modules (?load=...)
	 * @return Relative path with version filename including the timestamp
	 */
	public static function createVersionNumberedFilename($file, $forceQueryString = FALSE) {
		$lookupFile = explode('?', $file);
		$path = self::resolveBackPath(self::dirname(PATH_thisScript) . '/' . $lookupFile[0]);

		if (TYPO3_MODE == 'FE') {
			$mode = strtolower($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['versionNumberInFilename']);
			if ($mode === 'embed') {
				$mode = TRUE;
			} else {
				if ($mode === 'querystring') {
					$mode = FALSE;
				} else {
					$doNothing = TRUE;
				}
			}
		} else {
			$mode = $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['versionNumberInFilename'];
		}

		if (!file_exists($path) || $doNothing) {
				// File not found, return filename unaltered
			$fullName = $file;

		} else {
			if (!$mode || $forceQueryString) {
					// If use of .htaccess rule is not configured,
					// we use the default query-string method
				if ($lookupFile[1]) {
					$separator = '&';
				} else {
					$separator = '?';
				}
				$fullName = $file . $separator . filemtime($path);

			} else {
					// Change the filename
				$name = explode('.', $lookupFile[0]);
				$extension = array_pop($name);

				array_push($name, filemtime($path), $extension);
				$fullName = implode('.', $name);
					// append potential query string
				$fullName .= $lookupFile[1] ? '?' . $lookupFile[1] : '';
			}
		}

		return $fullName;
	}

	/*************************
	 *
	 * SYSTEM INFORMATION
	 *
	 *************************/

	/**
	 * Returns the HOST+DIR-PATH of the current script (The URL, but without 'http://' and without script-filename)
	 *
	 * @return string
	 */
	public static function getThisUrl() {
		$p = parse_url(self::getIndpEnv('TYPO3_REQUEST_SCRIPT')); // Url of this script
		$dir = self::dirname($p['path']) . '/'; // Strip file
		$url = str_replace('//', '/', $p['host'] . ($p['port'] ? ':' . $p['port'] : '') . $dir);
		return $url;
	}

	/**
	 * Returns the link-url to the current script.
	 * In $getParams you can set associative keys corresponding to the GET-vars you wish to add to the URL. If you set them empty, they will remove existing GET-vars from the current URL.
	 * REMEMBER to always use htmlspecialchars() for content in href-properties to get ampersands converted to entities (XHTML requirement and XSS precaution)
	 *
	 * @param array $getParams Array of GET parameters to include
	 * @return string
	 */
	public static function linkThisScript(array $getParams = array()) {
		$parts = self::getIndpEnv('SCRIPT_NAME');
		$params = self::_GET();

		foreach ($getParams as $key => $value) {
			if ($value !== '') {
				$params[$key] = $value;
			} else {
				unset($params[$key]);
			}
		}

		$pString = self::implodeArrayForUrl('', $params);

		return $pString ? $parts . '?' . preg_replace('/^&/', '', $pString) : $parts;
	}

	/**
	 * Takes a full URL, $url, possibly with a querystring and overlays the $getParams arrays values onto the quirystring, packs it all together and returns the URL again.
	 * So basically it adds the parameters in $getParams to an existing URL, $url
	 *
	 * @param string $url URL string
	 * @param array $getParams Array of key/value pairs for get parameters to add/overrule with. Can be multidimensional.
	 * @return string Output URL with added getParams.
	 */
	public static function linkThisUrl($url, array $getParams = array()) {
		$parts = parse_url($url);
		$getP = array();
		if ($parts['query']) {
			parse_str($parts['query'], $getP);
		}
		$getP = self::array_merge_recursive_overrule($getP, $getParams);
		$uP = explode('?', $url);

		$params = self::implodeArrayForUrl('', $getP);
		$outurl = $uP[0] . ($params ? '?' . substr($params, 1) : '');

		return $outurl;
	}

	/**
	 * Abstraction method which returns System Environment Variables regardless of server OS, CGI/MODULE version etc. Basically this is SERVER variables for most of them.
	 * This should be used instead of getEnv() and $_SERVER/ENV_VARS to get reliable values for all situations.
	 *
	 * @param string $getEnvName Name of the "environment variable"/"server variable" you wish to use. Valid values are SCRIPT_NAME, SCRIPT_FILENAME, REQUEST_URI, PATH_INFO, REMOTE_ADDR, REMOTE_HOST, HTTP_REFERER, HTTP_HOST, HTTP_USER_AGENT, HTTP_ACCEPT_LANGUAGE, QUERY_STRING, TYPO3_DOCUMENT_ROOT, TYPO3_HOST_ONLY, TYPO3_HOST_ONLY, TYPO3_REQUEST_HOST, TYPO3_REQUEST_URL, TYPO3_REQUEST_SCRIPT, TYPO3_REQUEST_DIR, TYPO3_SITE_URL, _ARRAY
	 * @return string Value based on the input key, independent of server/os environment.
	 */
	public static function getIndpEnv($getEnvName) {
		/*
			Conventions:
			output from parse_url():
			URL:	http://username:password@192.168.1.4:8080/typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/?arg1,arg2,arg3&p1=parameter1&p2[key]=value#link1
				[scheme] => 'http'
				[user] => 'username'
				[pass] => 'password'
				[host] => '192.168.1.4'
				[port] => '8080'
				[path] => '/typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/'
				[query] => 'arg1,arg2,arg3&p1=parameter1&p2[key]=value'
				[fragment] => 'link1'

				Further definition: [path_script] = '/typo3/32/temp/phpcheck/index.php'
									[path_dir] = '/typo3/32/temp/phpcheck/'
									[path_info] = '/arg1/arg2/arg3/'
									[path] = [path_script/path_dir][path_info]

			Keys supported:

			URI______:
				REQUEST_URI		=	[path]?[query]		= /typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/?arg1,arg2,arg3&p1=parameter1&p2[key]=value
				HTTP_HOST		=	[host][:[port]]		= 192.168.1.4:8080
				SCRIPT_NAME		=	[path_script]++		= /typo3/32/temp/phpcheck/index.php		// NOTICE THAT SCRIPT_NAME will return the php-script name ALSO. [path_script] may not do that (eg. '/somedir/' may result in SCRIPT_NAME '/somedir/index.php')!
				PATH_INFO		=	[path_info]			= /arg1/arg2/arg3/
				QUERY_STRING	=	[query]				= arg1,arg2,arg3&p1=parameter1&p2[key]=value
				HTTP_REFERER	=	[scheme]://[host][:[port]][path]	= http://192.168.1.4:8080/typo3/32/temp/phpcheck/index.php/arg1/arg2/arg3/?arg1,arg2,arg3&p1=parameter1&p2[key]=value
										(Notice: NO username/password + NO fragment)

			CLIENT____:
				REMOTE_ADDR		=	(client IP)
				REMOTE_HOST		=	(client host)
				HTTP_USER_AGENT	=	(client user agent)
				HTTP_ACCEPT_LANGUAGE	= (client accept language)

			SERVER____:
				SCRIPT_FILENAME	=	Absolute filename of script		(Differs between windows/unix). On windows 'C:\\blabla\\blabl\\' will be converted to 'C:/blabla/blabl/'

			Special extras:
				TYPO3_HOST_ONLY =		[host] = 192.168.1.4
				TYPO3_PORT =			[port] = 8080 (blank if 80, taken from host value)
				TYPO3_REQUEST_HOST = 		[scheme]://[host][:[port]]
				TYPO3_REQUEST_URL =		[scheme]://[host][:[port]][path]?[query] (scheme will by default be "http" until we can detect something different)
				TYPO3_REQUEST_SCRIPT =  	[scheme]://[host][:[port]][path_script]
				TYPO3_REQUEST_DIR =		[scheme]://[host][:[port]][path_dir]
				TYPO3_SITE_URL = 		[scheme]://[host][:[port]][path_dir] of the TYPO3 website frontend
				TYPO3_SITE_PATH = 		[path_dir] of the TYPO3 website frontend
				TYPO3_SITE_SCRIPT = 		[script / Speaking URL] of the TYPO3 website
				TYPO3_DOCUMENT_ROOT =		Absolute path of root of documents: TYPO3_DOCUMENT_ROOT.SCRIPT_NAME = SCRIPT_FILENAME (typically)
				TYPO3_SSL = 			Returns TRUE if this session uses SSL/TLS (https)
				TYPO3_PROXY = 			Returns TRUE if this session runs over a well known proxy

			Notice: [fragment] is apparently NEVER available to the script!

			Testing suggestions:
			- Output all the values.
			- In the script, make a link to the script it self, maybe add some parameters and click the link a few times so HTTP_REFERER is seen
			- ALSO TRY the script from the ROOT of a site (like 'http://www.mytest.com/' and not 'http://www.mytest.com/test/' !!)
		*/

		$retVal = '';

		switch ((string) $getEnvName) {
			case 'SCRIPT_NAME':
				$retVal = (PHP_SAPI == 'fpm-fcgi' || PHP_SAPI == 'cgi' || PHP_SAPI == 'cgi-fcgi') &&
						($_SERVER['ORIG_PATH_INFO'] ? $_SERVER['ORIG_PATH_INFO'] : $_SERVER['PATH_INFO']) ?
						($_SERVER['ORIG_PATH_INFO'] ? $_SERVER['ORIG_PATH_INFO'] : $_SERVER['PATH_INFO']) :
						($_SERVER['ORIG_SCRIPT_NAME'] ? $_SERVER['ORIG_SCRIPT_NAME'] : $_SERVER['SCRIPT_NAME']);
					// add a prefix if TYPO3 is behind a proxy: ext-domain.com => int-server.com/prefix
				if (self::cmpIP($_SERVER['REMOTE_ADDR'], $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP'])) {
					if (self::getIndpEnv('TYPO3_SSL') && $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyPrefixSSL']) {
						$retVal = $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyPrefixSSL'] . $retVal;
					} elseif ($GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyPrefix']) {
						$retVal = $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyPrefix'] . $retVal;
					}
				}
				break;
			case 'SCRIPT_FILENAME':
				$retVal = str_replace('//', '/', str_replace('\\', '/',
					(PHP_SAPI == 'fpm-fcgi' || PHP_SAPI == 'cgi' || PHP_SAPI == 'isapi' || PHP_SAPI == 'cgi-fcgi') &&
							($_SERVER['ORIG_PATH_TRANSLATED'] ? $_SERVER['ORIG_PATH_TRANSLATED'] : $_SERVER['PATH_TRANSLATED']) ?
							($_SERVER['ORIG_PATH_TRANSLATED'] ? $_SERVER['ORIG_PATH_TRANSLATED'] : $_SERVER['PATH_TRANSLATED']) :
							($_SERVER['ORIG_SCRIPT_FILENAME'] ? $_SERVER['ORIG_SCRIPT_FILENAME'] : $_SERVER['SCRIPT_FILENAME'])));

				break;
			case 'REQUEST_URI':
					// Typical application of REQUEST_URI is return urls, forms submitting to itself etc. Example: returnUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'))
				if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['requestURIvar']) { // This is for URL rewriters that store the original URI in a server variable (eg ISAPI_Rewriter for IIS: HTTP_X_REWRITE_URL)
					list($v, $n) = explode('|', $GLOBALS['TYPO3_CONF_VARS']['SYS']['requestURIvar']);
					$retVal = $GLOBALS[$v][$n];
				} elseif (!$_SERVER['REQUEST_URI']) { // This is for ISS/CGI which does not have the REQUEST_URI available.
					$retVal = '/' . ltrim(self::getIndpEnv('SCRIPT_NAME'), '/') .
							($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
				} else {
					$retVal = $_SERVER['REQUEST_URI'];
				}
					// add a prefix if TYPO3 is behind a proxy: ext-domain.com => int-server.com/prefix
				if (self::cmpIP($_SERVER['REMOTE_ADDR'], $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP'])) {
					if (self::getIndpEnv('TYPO3_SSL') && $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyPrefixSSL']) {
						$retVal = $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyPrefixSSL'] . $retVal;
					} elseif ($GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyPrefix']) {
						$retVal = $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyPrefix'] . $retVal;
					}
				}
				break;
			case 'PATH_INFO':
					// $_SERVER['PATH_INFO']!=$_SERVER['SCRIPT_NAME'] is necessary because some servers (Windows/CGI) are seen to set PATH_INFO equal to script_name
					// Further, there must be at least one '/' in the path - else the PATH_INFO value does not make sense.
					// IF 'PATH_INFO' never works for our purpose in TYPO3 with CGI-servers, then 'PHP_SAPI=='cgi'' might be a better check. Right now strcmp($_SERVER['PATH_INFO'],t3lib_div::getIndpEnv('SCRIPT_NAME')) will always return FALSE for CGI-versions, but that is only as long as SCRIPT_NAME is set equal to PATH_INFO because of PHP_SAPI=='cgi' (see above)
					//				if (strcmp($_SERVER['PATH_INFO'],self::getIndpEnv('SCRIPT_NAME')) && count(explode('/',$_SERVER['PATH_INFO']))>1)	{
				if (PHP_SAPI != 'cgi' && PHP_SAPI != 'cgi-fcgi' && PHP_SAPI != 'fpm-fcgi') {
					$retVal = $_SERVER['PATH_INFO'];
				}
				break;
			case 'TYPO3_REV_PROXY':
				$retVal = self::cmpIP($_SERVER['REMOTE_ADDR'], $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP']);
				break;
			case 'REMOTE_ADDR':
				$retVal = $_SERVER['REMOTE_ADDR'];
				if (self::cmpIP($_SERVER['REMOTE_ADDR'], $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP'])) {
					$ip = self::trimExplode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
						// choose which IP in list to use
					if (count($ip)) {
						switch ($GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyHeaderMultiValue']) {
							case 'last':
								$ip = array_pop($ip);
								break;
							case 'first':
								$ip = array_shift($ip);
								break;
							case 'none':
							default:
								$ip = '';
								break;
						}
					}
					if (self::validIP($ip)) {
						$retVal = $ip;
					}
				}
				break;
			case 'HTTP_HOST':
				$retVal = $_SERVER['HTTP_HOST'];
				if (self::cmpIP($_SERVER['REMOTE_ADDR'], $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP'])) {
					$host = self::trimExplode(',', $_SERVER['HTTP_X_FORWARDED_HOST']);
						// choose which host in list to use
					if (count($host)) {
						switch ($GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyHeaderMultiValue']) {
							case 'last':
								$host = array_pop($host);
								break;
							case 'first':
								$host = array_shift($host);
								break;
							case 'none':
							default:
								$host = '';
								break;
						}
					}
					if ($host) {
						$retVal = $host;
					}
				}
				if (!self::isAllowedHostHeaderValue($retVal)) {
					throw new UnexpectedValueException(
						'The current host header value does not match the configured trusted hosts pattern! Check the pattern defined in $GLOBALS[\'TYPO3_CONF_VARS\'][\'SYS\'][\'trustedHostsPattern\'] and adapt it, if you want to allow the current host header \'' . $retVal . '\' for your installation.',
						1396795884
					);
				}
				break;
				// These are let through without modification
			case 'HTTP_REFERER':
			case 'HTTP_USER_AGENT':
			case 'HTTP_ACCEPT_ENCODING':
			case 'HTTP_ACCEPT_LANGUAGE':
			case 'REMOTE_HOST':
			case 'QUERY_STRING':
				$retVal = $_SERVER[$getEnvName];
				break;
			case 'TYPO3_DOCUMENT_ROOT':
					// Get the web root (it is not the root of the TYPO3 installation)
					// The absolute path of the script can be calculated with TYPO3_DOCUMENT_ROOT + SCRIPT_FILENAME
					// Some CGI-versions (LA13CGI) and mod-rewrite rules on MODULE versions will deliver a 'wrong' DOCUMENT_ROOT (according to our description). Further various aliases/mod_rewrite rules can disturb this as well.
					// Therefore the DOCUMENT_ROOT is now always calculated as the SCRIPT_FILENAME minus the end part shared with SCRIPT_NAME.
				$SFN = self::getIndpEnv('SCRIPT_FILENAME');
				$SN_A = explode('/', strrev(self::getIndpEnv('SCRIPT_NAME')));
				$SFN_A = explode('/', strrev($SFN));
				$acc = array();
				foreach ($SN_A as $kk => $vv) {
					if (!strcmp($SFN_A[$kk], $vv)) {
						$acc[] = $vv;
					} else {
						break;
					}
				}
				$commonEnd = strrev(implode('/', $acc));
				if (strcmp($commonEnd, '')) {
					$DR = substr($SFN, 0, -(strlen($commonEnd) + 1));
				}
				$retVal = $DR;
				break;
			case 'TYPO3_HOST_ONLY':
				$httpHost = self::getIndpEnv('HTTP_HOST');
				$httpHostBracketPosition = strpos($httpHost, ']');
				$httpHostParts = explode(':', $httpHost);
				$retVal = ($httpHostBracketPosition !== FALSE) ? substr($httpHost, 0, ($httpHostBracketPosition + 1)) : array_shift($httpHostParts);
				break;
			case 'TYPO3_PORT':
				$httpHost = self::getIndpEnv('HTTP_HOST');
				$httpHostOnly = self::getIndpEnv('TYPO3_HOST_ONLY');
				$retVal = (strlen($httpHost) > strlen($httpHostOnly)) ? substr($httpHost, strlen($httpHostOnly) + 1) : '';
				break;
			case 'TYPO3_REQUEST_HOST':
				$retVal = (self::getIndpEnv('TYPO3_SSL') ? 'https://' : 'http://') .
						self::getIndpEnv('HTTP_HOST');
				break;
			case 'TYPO3_REQUEST_URL':
				$retVal = self::getIndpEnv('TYPO3_REQUEST_HOST') . self::getIndpEnv('REQUEST_URI');
				break;
			case 'TYPO3_REQUEST_SCRIPT':
				$retVal = self::getIndpEnv('TYPO3_REQUEST_HOST') . self::getIndpEnv('SCRIPT_NAME');
				break;
			case 'TYPO3_REQUEST_DIR':
				$retVal = self::getIndpEnv('TYPO3_REQUEST_HOST') . self::dirname(self::getIndpEnv('SCRIPT_NAME')) . '/';
				break;
			case 'TYPO3_SITE_URL':
				if (defined('PATH_thisScript') && defined('PATH_site')) {
					$lPath = substr(dirname(PATH_thisScript), strlen(PATH_site)) . '/';
					$url = self::getIndpEnv('TYPO3_REQUEST_DIR');
					$siteUrl = substr($url, 0, -strlen($lPath));
					if (substr($siteUrl, -1) != '/') {
						$siteUrl .= '/';
					}
					$retVal = $siteUrl;
				}
				break;
			case 'TYPO3_SITE_PATH':
				$retVal = substr(self::getIndpEnv('TYPO3_SITE_URL'), strlen(self::getIndpEnv('TYPO3_REQUEST_HOST')));
				break;
			case 'TYPO3_SITE_SCRIPT':
				$retVal = substr(self::getIndpEnv('TYPO3_REQUEST_URL'), strlen(self::getIndpEnv('TYPO3_SITE_URL')));
				break;
			case 'TYPO3_SSL':
				$proxySSL = trim($GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxySSL']);
				if ($proxySSL == '*') {
					$proxySSL = $GLOBALS['TYPO3_CONF_VARS']['SYS']['reverseProxyIP'];
				}
				if (self::cmpIP(self::getIndpEnv('REMOTE_ADDR'), $proxySSL)) {
					$retVal = TRUE;
				} else {
					$retVal = $_SERVER['SSL_SESSION_ID'] || !strcasecmp($_SERVER['HTTPS'], 'on') || !strcmp($_SERVER['HTTPS'], '1') ? TRUE : FALSE; // see http://bugs.typo3.org/view.php?id=3909
				}
				break;
			case '_ARRAY':
				$out = array();
					// Here, list ALL possible keys to this function for debug display.
				$envTestVars = self::trimExplode(',', '
					HTTP_HOST,
					TYPO3_HOST_ONLY,
					TYPO3_PORT,
					PATH_INFO,
					QUERY_STRING,
					REQUEST_URI,
					HTTP_REFERER,
					TYPO3_REQUEST_HOST,
					TYPO3_REQUEST_URL,
					TYPO3_REQUEST_SCRIPT,
					TYPO3_REQUEST_DIR,
					TYPO3_SITE_URL,
					TYPO3_SITE_SCRIPT,
					TYPO3_SSL,
					TYPO3_REV_PROXY,
					SCRIPT_NAME,
					TYPO3_DOCUMENT_ROOT,
					SCRIPT_FILENAME,
					REMOTE_ADDR,
					REMOTE_HOST,
					HTTP_USER_AGENT,
					HTTP_ACCEPT_LANGUAGE', 1);
				foreach ($envTestVars as $v) {
					$out[$v] = self::getIndpEnv($v);
				}
				reset($out);
				$retVal = $out;
				break;
		}
		return $retVal;
	}

	/**
	 * Checks if the provided host header value matches the trusted hosts pattern.
	 * If the pattern is not defined (which only can happen early in the bootstrap), deny any value.
	 *
	 * @param string $hostHeaderValue HTTP_HOST header value as sent during the request (may include port)
	 * @return bool
	 */
	static public function isAllowedHostHeaderValue($hostHeaderValue) {
		if (self::$allowHostHeaderValue === TRUE) {
			return TRUE;
		}

		// Allow all install tool requests
		// We accept this risk to have the install tool always available
		// Also CLI needs to be allowed as unfortunately AbstractUserAuthentication::getAuthInfoArray() accesses HTTP_HOST without reason on CLI
		if (defined('TYPO3_REQUESTTYPE') && (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL) || (TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_CLI)) {
			return self::$allowHostHeaderValue = TRUE;
		}

		// Deny the value if trusted host patterns is empty, which means we are early in the bootstrap
		if (empty($GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'])) {
			return FALSE;
		}

		if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'] === self::ENV_TRUSTED_HOSTS_PATTERN_ALLOW_ALL) {
			self::$allowHostHeaderValue = TRUE;
		} elseif ($GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'] === self::ENV_TRUSTED_HOSTS_PATTERN_SERVER_NAME) {
			// Allow values that equal the server name
			// Note that this is only secure if name base virtual host are configured correctly in the webserver
			$defaultPort = self::getIndpEnv('TYPO3_SSL') ? '443' : '80';
			$parsedHostValue = parse_url('http://' . $hostHeaderValue);
			if (isset($parsedHostValue['port'])) {
				self::$allowHostHeaderValue = ($parsedHostValue['host'] === $_SERVER['SERVER_NAME'] && (string)$parsedHostValue['port'] === $_SERVER['SERVER_PORT']);
			} else {
				self::$allowHostHeaderValue = ($hostHeaderValue === $_SERVER['SERVER_NAME'] && $defaultPort === $_SERVER['SERVER_PORT']);
			}
		} else {
			// In case name based virtual hosts are not possible, we allow setting a trusted host pattern
			// See https://typo3.org/teams/security/security-bulletins/typo3-core/typo3-core-sa-2014-001/ for further details
			self::$allowHostHeaderValue = (bool)preg_match('/^' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['trustedHostsPattern'] . '$/', $hostHeaderValue);
		}

		return self::$allowHostHeaderValue;
	}

	/**
	 * Gets the unixtime as milliseconds.
	 *
	 * @return integer The unixtime as milliseconds
	 */
	public static function milliseconds() {
		return round(microtime(TRUE) * 1000);
	}

	/**
	 * Client Browser Information
	 *
	 * @param string $useragent Alternative User Agent string (if empty, t3lib_div::getIndpEnv('HTTP_USER_AGENT') is used)
	 * @return array Parsed information about the HTTP_USER_AGENT in categories BROWSER, VERSION, SYSTEM and FORMSTYLE
	 */
	public static function clientInfo($useragent = '') {
		if (!$useragent) {
			$useragent = self::getIndpEnv('HTTP_USER_AGENT');
		}

		$bInfo = array();
			// Which browser?
		if (strpos($useragent, 'Konqueror') !== FALSE) {
			$bInfo['BROWSER'] = 'konqu';
		} elseif (strpos($useragent, 'Opera') !== FALSE) {
			$bInfo['BROWSER'] = 'opera';
		} elseif (strpos($useragent, 'MSIE') !== FALSE) {
			$bInfo['BROWSER'] = 'msie';
		} elseif (strpos($useragent, 'Mozilla') !== FALSE) {
			$bInfo['BROWSER'] = 'net';
		} elseif (strpos($useragent, 'Flash') !== FALSE) {
			$bInfo['BROWSER'] = 'flash';
		}
		if ($bInfo['BROWSER']) {
				// Browser version
			switch ($bInfo['BROWSER']) {
				case 'net':
					$bInfo['VERSION'] = doubleval(substr($useragent, 8));
					if (strpos($useragent, 'Netscape6/') !== FALSE) {
						$bInfo['VERSION'] = doubleval(substr(strstr($useragent, 'Netscape6/'), 10));
					} // Will we ever know if this was a typo or intention...?! :-(
					if (strpos($useragent, 'Netscape/6') !== FALSE) {
						$bInfo['VERSION'] = doubleval(substr(strstr($useragent, 'Netscape/6'), 10));
					}
					if (strpos($useragent, 'Netscape/7') !== FALSE) {
						$bInfo['VERSION'] = doubleval(substr(strstr($useragent, 'Netscape/7'), 9));
					}
					break;
				case 'msie':
					$tmp = strstr($useragent, 'MSIE');
					$bInfo['VERSION'] = doubleval(preg_replace('/^[^0-9]*/', '', substr($tmp, 4)));
					break;
				case 'opera':
					$tmp = strstr($useragent, 'Opera');
					$bInfo['VERSION'] = doubleval(preg_replace('/^[^0-9]*/', '', substr($tmp, 5)));
					break;
				case 'konqu':
					$tmp = strstr($useragent, 'Konqueror/');
					$bInfo['VERSION'] = doubleval(substr($tmp, 10));
					break;
			}
			// Client system
			if (strpos($useragent, 'Win') !== FALSE) {
				$bInfo['SYSTEM'] = 'win';
			} elseif (strpos($useragent, 'Mac') !== FALSE) {
				$bInfo['SYSTEM'] = 'mac';
			} elseif (strpos($useragent, 'Linux') !== FALSE || strpos($useragent, 'X11') !== FALSE || strpos($useragent, 'SGI') !== FALSE || strpos($useragent, ' SunOS ') !== FALSE || strpos($useragent, ' HP-UX ') !== FALSE) {
				$bInfo['SYSTEM'] = 'unix';
			}
		}
		// Is TRUE if the browser supports css to format forms, especially the width
		$bInfo['FORMSTYLE'] = ($bInfo['BROWSER'] == 'msie' || ($bInfo['BROWSER'] == 'net' && $bInfo['VERSION'] >= 5) || $bInfo['BROWSER'] == 'opera' || $bInfo['BROWSER'] == 'konqu');

		return $bInfo;
	}

	/**
	 * Get the fully-qualified domain name of the host.
	 *
	 * @param boolean $requestHost Use request host (when not in CLI mode).
	 * @return string The fully-qualified host name.
	 */
	public static function getHostname($requestHost = TRUE) {
		$host = '';
			// If not called from the command-line, resolve on getIndpEnv()
			// Note that TYPO3_REQUESTTYPE is not used here as it may not yet be defined
		if ($requestHost && (!defined('TYPO3_cliMode') || !TYPO3_cliMode)) {
			$host = self::getIndpEnv('HTTP_HOST');
		}
		if (!$host) {
				// will fail for PHP 4.1 and 4.2
			$host = @php_uname('n');
				// 'n' is ignored in broken installations
			if (strpos($host, ' ')) {
				$host = '';
			}
		}
			// we have not found a FQDN yet
		if ($host && strpos($host, '.') === FALSE) {
			$ip = gethostbyname($host);
				// we got an IP address
			if ($ip != $host) {
				$fqdn = gethostbyaddr($ip);
				if ($ip != $fqdn) {
					$host = $fqdn;
				}
			}
		}
		if (!$host) {
			$host = 'localhost.localdomain';
		}

		return $host;
	}


	/*************************
	 *
	 * TYPO3 SPECIFIC FUNCTIONS
	 *
	 *************************/

	/**
	 * Returns the absolute filename of a relative reference, resolves the "EXT:" prefix (way of referring to files inside extensions) and checks that the file is inside the PATH_site of the TYPO3 installation and implies a check with t3lib_div::validPathStr(). Returns FALSE if checks failed. Does not check if the file exists.
	 *
	 * @param string $filename The input filename/filepath to evaluate
	 * @param boolean $onlyRelative If $onlyRelative is set (which it is by default), then only return values relative to the current PATH_site is accepted.
	 * @param boolean $relToTYPO3_mainDir If $relToTYPO3_mainDir is set, then relative paths are relative to PATH_typo3 constant - otherwise (default) they are relative to PATH_site
	 * @return string Returns the absolute filename of $filename IF valid, otherwise blank string.
	 */
	public static function getFileAbsFileName($filename, $onlyRelative = TRUE, $relToTYPO3_mainDir = FALSE) {
		if (!strcmp($filename, '')) {
			return '';
		}

		if ($relToTYPO3_mainDir) {
			if (!defined('PATH_typo3')) {
				return '';
			}
			$relPathPrefix = PATH_typo3;
		} else {
			$relPathPrefix = PATH_site;
		}
		if (substr($filename, 0, 4) == 'EXT:') { // extension
			list($extKey, $local) = explode('/', substr($filename, 4), 2);
			$filename = '';
			if (strcmp($extKey, '') && t3lib_extMgm::isLoaded($extKey) && strcmp($local, '')) {
				$filename = t3lib_extMgm::extPath($extKey) . $local;
			}
		} elseif (!self::isAbsPath($filename)) { // relative. Prepended with $relPathPrefix
			$filename = $relPathPrefix . $filename;
		} elseif ($onlyRelative && !self::isFirstPartOfStr($filename, $relPathPrefix)) { // absolute, but set to blank if not allowed
			$filename = '';
		}
		if (strcmp($filename, '') && self::validPathStr($filename)) { // checks backpath.
			return $filename;
		}
	}

	/**
	 * Checks for malicious file paths.
	 *
	 * Returns TRUE if no '//', '..', '\' or control characters are found in the $theFile.
	 * This should make sure that the path is not pointing 'backwards' and further doesn't contain double/back slashes.
	 * So it's compatible with the UNIX style path strings valid for TYPO3 internally.
	 *
	 * @param string $theFile File path to evaluate
	 * @return boolean TRUE, $theFile is allowed path string, FALSE otherwise
	 * @see http://php.net/manual/en/security.filesystem.nullbytes.php
	 * @todo Possible improvement: Should it rawurldecode the string first to check if any of these characters is encoded?
	 */
	public static function validPathStr($theFile) {
		if (strpos($theFile, '//') === FALSE && strpos($theFile, '\\') === FALSE && !preg_match('#(?:^\.\.|/\.\./|[[:cntrl:]])#u', $theFile)) {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Checks if the $path is absolute or relative (detecting either '/' or 'x:/' as first part of string) and returns TRUE if so.
	 *
	 * @param string $path File path to evaluate
	 * @return boolean
	 */
	public static function isAbsPath($path) {
			// on Windows also a path starting with a drive letter is absolute: X:/
		if (TYPO3_OS === 'WIN' && substr($path, 1, 2) === ':/') {
			return TRUE;
		}

			// path starting with a / is always absolute, on every system
		return (substr($path, 0, 1) === '/');
	}

	/**
	 * Returns TRUE if the path is absolute, without backpath '..' and within the PATH_site OR within the lockRootPath
	 *
	 * @param string $path File path to evaluate
	 * @return boolean
	 */
	public static function isAllowedAbsPath($path) {
		if (self::isAbsPath($path) &&
				self::validPathStr($path) &&
				(self::isFirstPartOfStr($path, PATH_site)
						||
						($GLOBALS['TYPO3_CONF_VARS']['BE']['lockRootPath'] && self::isFirstPartOfStr($path, $GLOBALS['TYPO3_CONF_VARS']['BE']['lockRootPath']))
				)
		) {
			return TRUE;
		}
	}

	/**
	 * Verifies the input filename against the 'fileDenyPattern'. Returns TRUE if OK.
	 *
	 * @param string $filename File path to evaluate
	 * @return boolean
	 */
	public static function verifyFilenameAgainstDenyPattern($filename) {
			// Filenames are not allowed to contain control characters
		if (preg_match('/[[:cntrl:]]/', $filename)) {
			return FALSE;
		}

		if (strcmp($filename, '') && strcmp($GLOBALS['TYPO3_CONF_VARS']['BE']['fileDenyPattern'], '')) {
			$result = preg_match('/' . $GLOBALS['TYPO3_CONF_VARS']['BE']['fileDenyPattern'] . '/i', $filename);
			if ($result) {
				return FALSE;
			} // so if a matching filename is found, return FALSE;
		}
		return TRUE;
	}

	/**
	 * Checks if a given string is a valid frame URL to be loaded in the
	 * backend.
	 *
	 * @param string $url potential URL to check
	 * @return string either $url if $url is considered to be harmless, or an
	 *				empty string otherwise
	 */
	public static function sanitizeLocalUrl($url = '') {
		$sanitizedUrl = '';
		$decodedUrl = rawurldecode($url);

		if (!empty($url) && self::removeXSS($decodedUrl) === $decodedUrl) {
			$testAbsoluteUrl = self::resolveBackPath($decodedUrl);
			$testRelativeUrl = self::resolveBackPath(
				self::dirname(self::getIndpEnv('SCRIPT_NAME')) . '/' . $decodedUrl
			);

				// Pass if URL is on the current host:
			if (self::isValidUrl($decodedUrl)) {
				if (self::isOnCurrentHost($decodedUrl) && strpos($decodedUrl, self::getIndpEnv('TYPO3_SITE_URL')) === 0) {
					$sanitizedUrl = $url;
				}
				// Pass if URL is an absolute file path:
			} elseif (self::isAbsPath($decodedUrl) && self::isAllowedAbsPath($decodedUrl)) {
				$sanitizedUrl = $url;
				// Pass if URL is absolute and below TYPO3 base directory:
			} elseif (strpos($testAbsoluteUrl, self::getIndpEnv('TYPO3_SITE_PATH')) === 0 && substr($decodedUrl, 0, 1) === '/') {
				$sanitizedUrl = $url;
				// Pass if URL is relative and below TYPO3 base directory:
			} elseif (strpos($testRelativeUrl, self::getIndpEnv('TYPO3_SITE_PATH')) === 0 && substr($decodedUrl, 0, 1) !== '/') {
				$sanitizedUrl = $url;
			}
		}

		if (!empty($url) && empty($sanitizedUrl)) {
			self::sysLog('The URL "' . $url . '" is not considered to be local and was denied.', 'Core', self::SYSLOG_SEVERITY_NOTICE);
		}

		return $sanitizedUrl;
	}

	/**
	 * Moves $source file to $destination if uploaded, otherwise try to make a copy
	 *
	 * @param string $source Source file, absolute path
	 * @param string $destination Destination file, absolute path
	 * @return boolean Returns TRUE if the file was moved.
	 * @coauthor Dennis Petersen <fessor@software.dk>
	 * @see upload_to_tempfile()
	 */
	public static function upload_copy_move($source, $destination) {
		if (is_uploaded_file($source)) {
			$uploaded = TRUE;
				// Return the value of move_uploaded_file, and if FALSE the temporary $source is still around so the user can use unlink to delete it:
			$uploadedResult = move_uploaded_file($source, $destination);
		} else {
			$uploaded = FALSE;
			@copy($source, $destination);
		}

		self::fixPermissions($destination); // Change the permissions of the file

			// If here the file is copied and the temporary $source is still around, so when returning FALSE the user can try unlink to delete the $source
		return $uploaded ? $uploadedResult : FALSE;
	}

	/**
	 * Will move an uploaded file (normally in "/tmp/xxxxx") to a temporary filename in PATH_site."typo3temp/" from where TYPO3 can use it.
	 * Use this function to move uploaded files to where you can work on them.
	 * REMEMBER to use t3lib_div::unlink_tempfile() afterwards - otherwise temp-files will build up! They are NOT automatically deleted in PATH_site."typo3temp/"!
	 *
	 * @param string $uploadedFileName The temporary uploaded filename, eg. $_FILES['[upload field name here]']['tmp_name']
	 * @return string If a new file was successfully created, return its filename, otherwise blank string.
	 * @see unlink_tempfile(), upload_copy_move()
	 */
	public static function upload_to_tempfile($uploadedFileName) {
		if (is_uploaded_file($uploadedFileName)) {
			$tempFile = self::tempnam('upload_temp_');
			move_uploaded_file($uploadedFileName, $tempFile);
			return @is_file($tempFile) ? $tempFile : '';
		}
	}

	/**
	 * Deletes (unlink) a temporary filename in 'PATH_site."typo3temp/"' given as input.
	 * The function will check that the file exists, is in PATH_site."typo3temp/" and does not contain back-spaces ("../") so it should be pretty safe.
	 * Use this after upload_to_tempfile() or tempnam() from this class!
	 *
	 * @param string $uploadedTempFileName Filepath for a file in PATH_site."typo3temp/". Must be absolute.
	 * @return boolean Returns TRUE if the file was unlink()'ed
	 * @see upload_to_tempfile(), tempnam()
	 */
	public static function unlink_tempfile($uploadedTempFileName) {
		if ($uploadedTempFileName) {
			$uploadedTempFileName = self::fixWindowsFilePath($uploadedTempFileName);
			if (self::validPathStr($uploadedTempFileName) && self::isFirstPartOfStr($uploadedTempFileName, PATH_site . 'typo3temp/') && @is_file($uploadedTempFileName)) {
				if (unlink($uploadedTempFileName)) {
					return TRUE;
				}
			}
		}
	}

	/**
	 * Create temporary filename (Create file with unique file name)
	 * This function should be used for getting temporary file names - will make your applications safe for open_basedir = on
	 * REMEMBER to delete the temporary files after use! This is done by t3lib_div::unlink_tempfile()
	 *
	 * @param string $filePrefix Prefix to temp file (which will have no extension btw)
	 * @return string result from PHP function tempnam() with PATH_site . 'typo3temp/' set for temp path.
	 * @see unlink_tempfile(), upload_to_tempfile()
	 */
	public static function tempnam($filePrefix) {
		return tempnam(PATH_site . 'typo3temp/', $filePrefix);
	}

	/**
	 * Standard authentication code (used in Direct Mail, checkJumpUrl and setfixed links computations)
	 *
	 * @param mixed $uid_or_record Uid (integer) or record (array)
	 * @param string $fields List of fields from the record if that is given.
	 * @param integer $codeLength Length of returned authentication code.
	 * @return string MD5 hash of 8 chars.
	 */
	public static function stdAuthCode($uid_or_record, $fields = '', $codeLength = 8) {

		if (is_array($uid_or_record)) {
			$recCopy_temp = array();
			if ($fields) {
				$fieldArr = self::trimExplode(',', $fields, 1);
				foreach ($fieldArr as $k => $v) {
					$recCopy_temp[$k] = $uid_or_record[$v];
				}
			} else {
				$recCopy_temp = $uid_or_record;
			}
			$preKey = implode('|', $recCopy_temp);
		} else {
			$preKey = $uid_or_record;
		}

		$authCode = $preKey . '||' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];
		$authCode = substr(md5($authCode), 0, $codeLength);
		return $authCode;
	}

	/**
	 * Splits the input query-parameters into an array with certain parameters filtered out.
	 * Used to create the cHash value
	 *
	 * @param string $addQueryParams Query-parameters: "&xxx=yyy&zzz=uuu"
	 * @return array Array with key/value pairs of query-parameters WITHOUT a certain list of variable names (like id, type, no_cache etc.) and WITH a variable, encryptionKey, specific for this server/installation
	 * @see tslib_fe::makeCacheHash(), tslib_cObj::typoLink(), t3lib_div::calculateCHash()
	 * @deprecated since TYPO3 4.7 - will be removed in TYPO3 6.1 - use t3lib_cacheHash instead
	 */
	public static function cHashParams($addQueryParams) {
		t3lib_div::logDeprecatedFunction();
		$params = explode('&', substr($addQueryParams, 1)); // Splitting parameters up
		/* @var $cacheHash t3lib_cacheHash */
		$cacheHash = t3lib_div::makeInstance('t3lib_cacheHash');
		$pA = $cacheHash->getRelevantParameters($addQueryParams);

			// Hook: Allows to manipulate the parameters which are taken to build the chash:
		if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['cHashParamsHook'])) {
			$cHashParamsHook =& $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['cHashParamsHook'];
			if (is_array($cHashParamsHook)) {
				$hookParameters = array(
					'addQueryParams' => &$addQueryParams,
					'params' => &$params,
					'pA' => &$pA,
				);
				$hookReference = NULL;
				foreach ($cHashParamsHook as $hookFunction) {
					self::callUserFunction($hookFunction, $hookParameters, $hookReference);
				}
			}
		}

		return $pA;
	}

	/**
	 * Returns the cHash based on provided query parameters and added values from internal call
	 *
	 * @param string $addQueryParams Query-parameters: "&xxx=yyy&zzz=uuu"
	 * @return string Hash of all the values
	 * @see t3lib_div::cHashParams(), t3lib_div::calculateCHash()
	 * @deprecated since TYPO3 4.7 - will be removed in TYPO3 6.1 - use t3lib_cacheHash instead
	 */
	public static function generateCHash($addQueryParams) {
		t3lib_div::logDeprecatedFunction();
		/* @var $cacheHash t3lib_cacheHash */
		$cacheHash = t3lib_div::makeInstance('t3lib_cacheHash');
		return $cacheHash->generateForParameters($addQueryParams);
	}

	/**
	 * Calculates the cHash based on the provided parameters
	 *
	 * @param array $params Array of key-value pairs
	 * @return string Hash of all the values
	 * @deprecated since TYPO3 4.7 - will be removed in TYPO3 6.1 - use t3lib_cacheHash instead
	 */
	public static function calculateCHash($params) {
		t3lib_div::logDeprecatedFunction();
		/* @var $cacheHash t3lib_cacheHash */
		$cacheHash = t3lib_div::makeInstance('t3lib_cacheHash');
		return $cacheHash->calculateCacheHash($params);
	}

	/**
	 * Responds on input localization setting value whether the page it comes from should be hidden if no translation exists or not.
	 *
	 * @param integer $l18n_cfg_fieldValue Value from "l18n_cfg" field of a page record
	 * @return boolean TRUE if the page should be hidden
	 */
	public static function hideIfNotTranslated($l18n_cfg_fieldValue) {
		if ($GLOBALS['TYPO3_CONF_VARS']['FE']['hidePagesIfNotTranslatedByDefault']) {
			return $l18n_cfg_fieldValue & 2 ? FALSE : TRUE;
		} else {
			return $l18n_cfg_fieldValue & 2 ? TRUE : FALSE;
		}
	}

	/**
	 * Returns true if the "l18n_cfg" field value is not set to hide
	 * pages in the default language
	 *
	 * @param int $localizationConfiguration
	 * @return boolean
	 */
	public static function hideIfDefaultLanguage($localizationConfiguration) {
		return ($localizationConfiguration & 1);
	}

	/**
	 * Includes a locallang file and returns the $LOCAL_LANG array found inside.
	 *
	 * @param string $fileRef Input is a file-reference (see t3lib_div::getFileAbsFileName). That file is expected to be a 'locallang.php' file containing a $LOCAL_LANG array (will be included!) or a 'locallang.xml' file conataining a valid XML TYPO3 language structure.
	 * @param string $langKey Language key
	 * @param string $charset Character set (option); if not set, determined by the language key
	 * @param integer $errorMode Error mode (when file could not be found): 0 - syslog entry, 1 - do nothing, 2 - throw an exception
	 * @return array Value of $LOCAL_LANG found in the included file. If that array is found it will returned.
	 *						 Otherwise an empty array and it is FALSE in error case.
	 */
	public static function readLLfile($fileRef, $langKey, $charset = '', $errorMode = 0) {
		/** @var $languageFactory t3lib_l10n_Factory */
		$languageFactory = t3lib_div::makeInstance('t3lib_l10n_Factory');
		return $languageFactory->getParsedData($fileRef, $langKey, $charset, $errorMode);
	}

	/**
	 * Includes a locallang-php file and returns the $LOCAL_LANG array
	 * Works only when the frontend or backend has been initialized with a charset conversion object. See first code lines.
	 *
	 * @param string $fileRef Absolute reference to locallang-PHP file
	 * @param string $langKey TYPO3 language key, eg. "dk" or "de" or "default"
	 * @param string $charset Character set (optional)
	 * @return array LOCAL_LANG array in return.
	 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0 - use t3lib_l10n_parser_Llphp::getParsedData() from now on
	 */
	public static function readLLPHPfile($fileRef, $langKey, $charset = '') {
		t3lib_div::logDeprecatedFunction();

		if (is_object($GLOBALS['LANG'])) {
			$csConvObj = $GLOBALS['LANG']->csConvObj;
		} elseif (is_object($GLOBALS['TSFE'])) {
			$csConvObj = $GLOBALS['TSFE']->csConvObj;
		} else {
			$csConvObj = self::makeInstance('t3lib_cs');
		}

		if (@is_file($fileRef) && $langKey) {

				// Set charsets:
			$sourceCharset = $csConvObj->parse_charset($csConvObj->charSetArray[$langKey] ? $csConvObj->charSetArray[$langKey] : 'utf-8');
			if ($charset) {
				$targetCharset = $csConvObj->parse_charset($charset);
			} else {
				$targetCharset = 'utf-8';
			}

				// Cache file name:
			$hashSource = substr($fileRef, strlen(PATH_site)) . '|' . date('d-m-Y H:i:s', filemtime($fileRef)) . '|version=2.3';
			$cacheFileName = PATH_site . 'typo3temp/llxml/' .
					substr(basename($fileRef), 10, 15) .
					'_' . self::shortMD5($hashSource) . '.' . $langKey . '.' . $targetCharset . '.cache';
				// Check if cache file exists...
			if (!@is_file($cacheFileName)) { // ... if it doesn't, create content and write it:
				$LOCAL_LANG = NULL;
					// Get PHP data
				include($fileRef);
				if (!is_array($LOCAL_LANG)) {
					$fileName = substr($fileRef, strlen(PATH_site));
					throw new RuntimeException(
						'TYPO3 Fatal Error: "' . $fileName . '" is no TYPO3 language file!',
						1270853900
					);
				}

					// converting the default language (English)
					// this needs to be done for a few accented loan words and extension names
				if (is_array($LOCAL_LANG['default']) && $targetCharset != 'utf-8') {
					foreach ($LOCAL_LANG['default'] as &$labelValue) {
						$labelValue = $csConvObj->conv($labelValue, 'utf-8', $targetCharset);
					}
					unset($labelValue);
				}

				if ($langKey != 'default' && is_array($LOCAL_LANG[$langKey]) && $sourceCharset != $targetCharset) {
					foreach ($LOCAL_LANG[$langKey] as &$labelValue) {
						$labelValue = $csConvObj->conv($labelValue, $sourceCharset, $targetCharset);
					}
					unset($labelValue);
				}

					// Cache the content now:
				$serContent = array('origFile' => $hashSource, 'LOCAL_LANG' => array('default' => $LOCAL_LANG['default'], $langKey => $LOCAL_LANG[$langKey]));
				$res = self::writeFileToTypo3tempDir($cacheFileName, serialize($serContent));
				if ($res) {
					throw new RuntimeException(
						'TYPO3 Fatal Error: "' . $res,
						1270853901
					);
				}
			} else {
					// Get content from cache:
				$serContent = unserialize(self::getUrl($cacheFileName));
				$LOCAL_LANG = $serContent['LOCAL_LANG'];
			}

			return $LOCAL_LANG;
		}
	}

	/**
	 * Includes a locallang-xml file and returns the $LOCAL_LANG array
	 * Works only when the frontend or backend has been initialized with a charset conversion object. See first code lines.
	 *
	 * @param string $fileRef Absolute reference to locallang-XML file
	 * @param string $langKey TYPO3 language key, eg. "dk" or "de" or "default"
	 * @param string $charset Character set (optional)
	 * @return array LOCAL_LANG array in return.
	 * @deprecated since TYPO3 4.6, will be removed in TYPO3 6.0 - use t3lib_l10n_parser_Llxml::getParsedData() from now on
	 */
	public static function readLLXMLfile($fileRef, $langKey, $charset = '') {
		t3lib_div::logDeprecatedFunction();

		if (is_object($GLOBALS['LANG'])) {
			$csConvObj = $GLOBALS['LANG']->csConvObj;
		} elseif (is_object($GLOBALS['TSFE'])) {
			$csConvObj = $GLOBALS['TSFE']->csConvObj;
		} else {
			$csConvObj = self::makeInstance('t3lib_cs');
		}

		$LOCAL_LANG = NULL;
		if (@is_file($fileRef) && $langKey) {

				// Set charset:
			if ($charset) {
				$targetCharset = $csConvObj->parse_charset($charset);
			} else {
				$targetCharset = 'utf-8';
			}

				// Cache file name:
			$hashSource = substr($fileRef, strlen(PATH_site)) . '|' . date('d-m-Y H:i:s', filemtime($fileRef)) . '|version=2.3';
			$cacheFileName = PATH_site . 'typo3temp/llxml/' .
					substr(basename($fileRef), 10, 15) .
					'_' . self::shortMD5($hashSource) . '.' . $langKey . '.' . $targetCharset . '.cache';

				// Check if cache file exists...
			if (!@is_file($cacheFileName)) { // ... if it doesn't, create content and write it:

					// Read XML, parse it.
				$xmlString = self::getUrl($fileRef);
				$xmlContent = self::xml2array($xmlString);
				if (!is_array($xmlContent)) {
					$fileName = substr($fileRef, strlen(PATH_site));
					throw new RuntimeException(
						'TYPO3 Fatal Error: The file "' . $fileName . '" is no TYPO3 language file!',
						1270853902
					);
				}

					// Set default LOCAL_LANG array content:
				$LOCAL_LANG = array();
				$LOCAL_LANG['default'] = $xmlContent['data']['default'];

					// converting the default language (English)
					// this needs to be done for a few accented loan words and extension names
					// NOTE: no conversion is done when in UTF-8 mode!
				if (is_array($LOCAL_LANG['default']) && $targetCharset != 'utf-8') {
					foreach ($LOCAL_LANG['default'] as &$labelValue) {
						$labelValue = $csConvObj->utf8_decode($labelValue, $targetCharset);
					}
					unset($labelValue);
				}

					// converting other languages to their "native" charsets
					// NOTE: no conversion is done when in UTF-8 mode!
				if ($langKey != 'default') {

						// If no entry is found for the language key, then force a value depending on meta-data setting. By default an automated filename will be used:
					$LOCAL_LANG[$langKey] = self::llXmlAutoFileName($fileRef, $langKey);
					$localized_file = self::getFileAbsFileName($LOCAL_LANG[$langKey]);
					if (!@is_file($localized_file) && isset($xmlContent['data'][$langKey])) {
						$LOCAL_LANG[$langKey] = $xmlContent['data'][$langKey];
					}

						// Checking if charset should be converted.
					if (is_array($LOCAL_LANG[$langKey]) && $targetCharset != 'utf-8') {
						foreach ($LOCAL_LANG[$langKey] as &$labelValue) {
							$labelValue = $csConvObj->utf8_decode($labelValue, $targetCharset);
						}
						unset($labelValue);
					}
				}

					// Cache the content now:
				$serContent = array('origFile' => $hashSource, 'LOCAL_LANG' => array('default' => $LOCAL_LANG['default'], $langKey => $LOCAL_LANG[$langKey]));
				$res = self::writeFileToTypo3tempDir($cacheFileName, serialize($serContent));
				if ($res) {
					throw new RuntimeException(
						'TYPO3 Fatal Error: ' . $res,
						1270853903
					);
				}
			} else {
					// Get content from cache:
				$serContent = unserialize(self::getUrl($cacheFileName));
				$LOCAL_LANG = $serContent['LOCAL_LANG'];
			}

				// Checking for EXTERNAL file for non-default language:
			if ($langKey != 'default' && is_string($LOCAL_LANG[$langKey]) && strlen($LOCAL_LANG[$langKey])) {

					// Look for localized file:
				$localized_file = self::getFileAbsFileName($LOCAL_LANG[$langKey]);
				if ($localized_file && @is_file($localized_file)) {

						// Cache file name:
					$hashSource = substr($localized_file, strlen(PATH_site)) . '|' . date('d-m-Y H:i:s', filemtime($localized_file)) . '|version=2.3';
					$cacheFileName = PATH_site . 'typo3temp/llxml/EXT_' .
							substr(basename($localized_file), 10, 15) .
							'_' . self::shortMD5($hashSource) . '.' . $langKey . '.' . $targetCharset . '.cache';

						// Check if cache file exists...
					if (!@is_file($cacheFileName)) { // ... if it doesn't, create content and write it:

							// Read and parse XML content:
						$local_xmlString = self::getUrl($localized_file);
						$local_xmlContent = self::xml2array($local_xmlString);
						if (!is_array($local_xmlContent)) {
							$fileName = substr($localized_file, strlen(PATH_site));
							throw new RuntimeException(
								'TYPO3 Fatal Error: The file "' . $fileName . '" is no TYPO3 language file!',
								1270853904
							);
						}
						$LOCAL_LANG[$langKey] = is_array($local_xmlContent['data'][$langKey]) ? $local_xmlContent['data'][$langKey] : array();

							// Checking if charset should be converted.
						if (is_array($LOCAL_LANG[$langKey]) && $targetCharset != 'utf-8') {
							foreach ($LOCAL_LANG[$langKey] as &$labelValue) {
								$labelValue = $csConvObj->utf8_decode($labelValue, $targetCharset);
							}
							unset($labelValue);
						}

							// Cache the content now:
						$serContent = array('extlang' => $langKey, 'origFile' => $hashSource, 'EXT_DATA' => $LOCAL_LANG[$langKey]);
						$res = self::writeFileToTypo3tempDir($cacheFileName, serialize($serContent));
						if ($res) {
							throw new RuntimeException(
								'TYPO3 Fatal Error: ' . $res,
								1270853905
							);
						}
					} else {
							// Get content from cache:
						$serContent = unserialize(self::getUrl($cacheFileName));
						$LOCAL_LANG[$langKey] = $serContent['EXT_DATA'];
					}
				} else {
					$LOCAL_LANG[$langKey] = array();
				}
			}

				// Convert the $LOCAL_LANG array to XLIFF structure
			foreach ($LOCAL_LANG as &$keysLabels) {
				foreach ($keysLabels as &$label) {
					$label = array(0 => array(
						'target' => $label,
					));
				}
				unset($label);
			}
			unset($keysLabels);

			return $LOCAL_LANG;
		}
	}

	/**
	 * Returns auto-filename for locallang-XML localizations.
	 *
	 * @param string $fileRef Absolute file reference to locallang-XML file. Must be inside system/global/local extension
	 * @param string $language Language key
	 * @param boolean $sameLocation if TRUE, then locallang-XML localization file name will be returned with same directory as $fileRef
	 * @return string Returns the filename reference for the language unless error occurred (or local mode is used) in which case it will be NULL
	 */
	public static function llXmlAutoFileName($fileRef, $language, $sameLocation = FALSE) {
		if ($sameLocation) {
			$location = 'EXT:';
		} else {
			$location = 'typo3conf/l10n/' . $language . '/'; // Default location of translations
		}

			// Analyse file reference:
		if (self::isFirstPartOfStr($fileRef, PATH_typo3 . 'sysext/')) { // Is system:
			$validatedPrefix = PATH_typo3 . 'sysext/';
			#$location = 'EXT:csh_'.$language.'/';	// For system extensions translations are found in "csh_*" extensions (language packs)
		} elseif (self::isFirstPartOfStr($fileRef, PATH_typo3 . 'ext/')) { // Is global:
			$validatedPrefix = PATH_typo3 . 'ext/';
		} elseif (self::isFirstPartOfStr($fileRef, PATH_typo3conf . 'ext/')) { // Is local:
			$validatedPrefix = PATH_typo3conf . 'ext/';
		} elseif (self::isFirstPartOfStr($fileRef, PATH_site . 'typo3_src/tests/')) { // Is test:
			$validatedPrefix = PATH_site . 'typo3_src/tests/';
			$location = $validatedPrefix;
		} else {
			$validatedPrefix = '';
		}

		if ($validatedPrefix) {

				// Divide file reference into extension key, directory (if any) and base name:
			list($file_extKey, $file_extPath) = explode('/', substr($fileRef, strlen($validatedPrefix)), 2);
			$temp = self::revExplode('/', $file_extPath, 2);
			if (count($temp) == 1) {
				array_unshift($temp, '');
			} // Add empty first-entry if not there.
			list($file_extPath, $file_fileName) = $temp;

				// If $fileRef is already prefix with "[language key]" then we should return it as this
			if (substr($file_fileName, 0, strlen($language) + 1) === $language . '.') {
				return $fileRef;
			}

				// The filename is prefixed with "[language key]." because it prevents the llxmltranslate tool from detecting it.
			return $location .
					$file_extKey . '/' .
					($file_extPath ? $file_extPath . '/' : '') .
					$language . '.' . $file_fileName;
		} else {
			return NULL;
		}
	}


	/**
	 * Loads the $GLOBALS['TCA'] (Table Configuration Array) for the $table
	 *
	 * Requirements:
	 * 1) must be configured table (the ctrl-section configured),
	 * 2) columns must not be an array (which it is always if whole table loaded), and
	 * 3) there is a value for dynamicConfigFile (filename in typo3conf)
	 *
	 * Note: For the frontend this loads only 'ctrl' and 'feInterface' parts.
	 * For complete TCA use $GLOBALS['TSFE']->includeTCA() instead.
	 *
	 * @param string $table Table name for which to load the full TCA array part into $GLOBALS['TCA']
	 * @return void
	 */
	public static function loadTCA($table) {
			//needed for inclusion of the dynamic config files.
		global $TCA;
		if (isset($TCA[$table])) {
			$tca = &$TCA[$table];
			if (!$tca['columns']) {
				$dcf = $tca['ctrl']['dynamicConfigFile'];
				if ($dcf) {
					if (!strcmp(substr($dcf, 0, 6), 'T3LIB:')) {
						include(PATH_t3lib . 'stddb/' . substr($dcf, 6));
					} elseif (self::isAbsPath($dcf) && @is_file($dcf)) { // Absolute path...
						include($dcf);
					} else {
						include(PATH_typo3conf . $dcf);
					}
				}
			}
		}
	}

	/**
	 * Looks for a sheet-definition in the input data structure array. If found it will return the data structure for the sheet given as $sheet (if found).
	 * If the sheet definition is in an external file that file is parsed and the data structure inside of that is returned.
	 *
	 * @param array $dataStructArray Input data structure, possibly with a sheet-definition and references to external data source files.
	 * @param string $sheet The sheet to return, preferably.
	 * @return array An array with two num. keys: key0: The data structure is returned in this key (array) UNLESS an error occurred in which case an error string is returned (string). key1: The used sheet key value!
	 */
	public static function resolveSheetDefInDS($dataStructArray, $sheet = 'sDEF') {
		if (!is_array($dataStructArray)) {
			return 'Data structure must be an array';
		}

		if (is_array($dataStructArray['sheets'])) {
			$singleSheet = FALSE;
			if (!isset($dataStructArray['sheets'][$sheet])) {
				$sheet = 'sDEF';
			}
			$dataStruct = $dataStructArray['sheets'][$sheet];

				// If not an array, but still set, then regard it as a relative reference to a file:
			if ($dataStruct && !is_array($dataStruct)) {
				$file = self::getFileAbsFileName($dataStruct);
				if ($file && @is_file($file)) {
					$dataStruct = self::xml2array(self::getUrl($file));
				}
			}
		} else {
			$singleSheet = TRUE;
			$dataStruct = $dataStructArray;
			if (isset($dataStruct['meta'])) {
				unset($dataStruct['meta']);
			} // Meta data should not appear there.
			$sheet = 'sDEF'; // Default sheet
		}
		return array($dataStruct, $sheet, $singleSheet);
	}

	/**
	 * Resolves ALL sheet definitions in dataStructArray
	 * If no sheet is found, then the default "sDEF" will be created with the dataStructure inside.
	 *
	 * @param array $dataStructArray Input data structure, possibly with a sheet-definition and references to external data source files.
	 * @return array Output data structure with all sheets resolved as arrays.
	 */
	public static function resolveAllSheetsInDS(array $dataStructArray) {
		if (is_array($dataStructArray['sheets'])) {
			$out = array('sheets' => array());
			foreach ($dataStructArray['sheets'] as $sheetId => $sDat) {
				list($ds, $aS) = self::resolveSheetDefInDS($dataStructArray, $sheetId);
				if ($sheetId == $aS) {
					$out['sheets'][$aS] = $ds;
				}
			}
		} else {
			list($ds) = self::resolveSheetDefInDS($dataStructArray);
			$out = array('sheets' => array('sDEF' => $ds));
		}
		return $out;
	}

	/**
	 * Calls a user-defined function/method in class
	 * Such a function/method should look like this: "function proc(&$params, &$ref)	{...}"
	 *
	 * @param string $funcName Function/Method reference, '[file-reference":"]["&"]class/function["->"method-name]'. You can prefix this reference with "[file-reference]:" and t3lib_div::getFileAbsFileName() will then be used to resolve the filename and subsequently include it by "require_once()" which means you don't have to worry about including the class file either! Example: "EXT:realurl/class.tx_realurl.php:&tx_realurl->encodeSpURL". Finally; you can prefix the class name with "&" if you want to reuse a former instance of the same object call ("singleton").
	 * @param mixed $params Parameters to be pass along (typically an array) (REFERENCE!)
	 * @param mixed $ref Reference to be passed along (typically "$this" - being a reference to the calling object) (REFERENCE!)
	 * @param string $checkPrefix Alternative allowed prefix of class or function name
	 * @param integer $errorMode Error mode (when class/function could not be found): 0 - call debug(), 1 - do nothing, 2 - raise an exception (allows to call a user function that may return FALSE)
	 * @return mixed Content from method/function call or FALSE if the class/method/function was not found
	 * @see getUserObj()
	 */
	public static function callUserFunction($funcName, &$params, &$ref, $checkPrefix = 'user_', $errorMode = 0) {
		$content = FALSE;

			// Check persistent object and if found, call directly and exit.
		if (is_array($GLOBALS['T3_VAR']['callUserFunction'][$funcName])) {
			return call_user_func_array(
				array(&$GLOBALS['T3_VAR']['callUserFunction'][$funcName]['obj'],
					$GLOBALS['T3_VAR']['callUserFunction'][$funcName]['method']),
				array(&$params, &$ref)
			);
		}

			// Check file-reference prefix; if found, require_once() the file (should be library of code)
		if (strpos($funcName, ':') !== FALSE) {
			list($file, $funcRef) = self::revExplode(':', $funcName, 2);
			$requireFile = self::getFileAbsFileName($file);
			if ($requireFile) {
				self::requireOnce($requireFile);
			}
		} else {
			$funcRef = $funcName;
		}

			// Check for persistent object token, "&"
		if (substr($funcRef, 0, 1) == '&') {
			$funcRef = substr($funcRef, 1);
			$storePersistentObject = TRUE;
		} else {
			$storePersistentObject = FALSE;
		}

			// Check prefix is valid:
		if ($checkPrefix && !self::hasValidClassPrefix($funcRef, array($checkPrefix))) {
			$errorMsg = "Function/class '$funcRef' was not prepended with '$checkPrefix'";
			if ($errorMode == 2) {
				throw new InvalidArgumentException($errorMsg, 1294585864);
			} elseif (!$errorMode) {
				debug($errorMsg, 't3lib_div::callUserFunction');
			}
			return FALSE;
		}

			// Call function or method:
		$parts = explode('->', $funcRef);
		if (count($parts) == 2) { // Class

				// Check if class/method exists:
			if (class_exists($parts[0])) {

					// Get/Create object of class:
				if ($storePersistentObject) { // Get reference to current instance of class:
					if (!is_object($GLOBALS['T3_VAR']['callUserFunction_classPool'][$parts[0]])) {
						$GLOBALS['T3_VAR']['callUserFunction_classPool'][$parts[0]] = self::makeInstance($parts[0]);
					}
					$classObj = $GLOBALS['T3_VAR']['callUserFunction_classPool'][$parts[0]];
				} else { // Create new object:
					$classObj = self::makeInstance($parts[0]);
				}

				if (method_exists($classObj, $parts[1])) {

						// If persistent object should be created, set reference:
					if ($storePersistentObject) {
						$GLOBALS['T3_VAR']['callUserFunction'][$funcName] = array(
							'method' => $parts[1],
							'obj' => &$classObj
						);
					}
						// Call method:
					$content = call_user_func_array(
						array(&$classObj, $parts[1]),
						array(&$params, &$ref)
					);
				} else {
					$errorMsg = "No method name '" . $parts[1] . "' in class " . $parts[0];
					if ($errorMode == 2) {
						throw new InvalidArgumentException($errorMsg, 1294585865);
					} elseif (!$errorMode) {
						debug($errorMsg, 't3lib_div::callUserFunction');
					}
				}
			} else {
				$errorMsg = 'No class named ' . $parts[0];
				if ($errorMode == 2) {
					throw new InvalidArgumentException($errorMsg, 1294585866);
				} elseif (!$errorMode) {
					debug($errorMsg, 't3lib_div::callUserFunction');
				}
			}
		} else { // Function
			if (function_exists($funcRef)) {
				$content = call_user_func_array($funcRef, array(&$params, &$ref));
			} else {
				$errorMsg = 'No function named: ' . $funcRef;
				if ($errorMode == 2) {
					throw new InvalidArgumentException($errorMsg, 1294585867);
				} elseif (!$errorMode) {
					debug($errorMsg, 't3lib_div::callUserFunction');
				}
			}
		}
		return $content;
	}

	/**
	 * Creates and returns reference to a user defined object.
	 * This function can return an object reference if you like. Just prefix the function call with "&": "$objRef = &t3lib_div::getUserObj('EXT:myext/class.tx_myext_myclass.php:&tx_myext_myclass');". This will work ONLY if you prefix the class name with "&" as well. See description of function arguments.
	 *
	 * @param string $classRef Class reference, '[file-reference":"]["&"]class-name'. You can prefix the class name with "[file-reference]:" and t3lib_div::getFileAbsFileName() will then be used to resolve the filename and subsequently include it by "require_once()" which means you don't have to worry about including the class file either! Example: "EXT:realurl/class.tx_realurl.php:&tx_realurl". Finally; for the class name you can prefix it with "&" and you will reuse the previous instance of the object identified by the full reference string (meaning; if you ask for the same $classRef later in another place in the code you will get a reference to the first created one!).
	 * @param string $checkPrefix Required prefix of class name. By default "tx_" and "Tx_" are allowed.
	 * @param boolean $silent If set, no debug() error message is shown if class/function is not present.
	 * @return object The instance of the class asked for. Instance is created with t3lib_div::makeInstance
	 * @see callUserFunction()
	 */
	public static function getUserObj($classRef, $checkPrefix = 'user_', $silent = FALSE) {
			// Check persistent object and if found, call directly and exit.
		if (is_object($GLOBALS['T3_VAR']['getUserObj'][$classRef])) {
			return $GLOBALS['T3_VAR']['getUserObj'][$classRef];
		} else {

				// Check file-reference prefix; if found, require_once() the file (should be library of code)
			if (strpos($classRef, ':') !== FALSE) {
				list($file, $class) = self::revExplode(':', $classRef, 2);
				$requireFile = self::getFileAbsFileName($file);
				if ($requireFile) {
					self::requireOnce($requireFile);
				}
			} else {
				$class = $classRef;
			}

				// Check for persistent object token, "&"
			if (substr($class, 0, 1) == '&') {
				$class = substr($class, 1);
				$storePersistentObject = TRUE;
			} else {
				$storePersistentObject = FALSE;
			}

				// Check prefix is valid:
			if ($checkPrefix && !self::hasValidClassPrefix($class, array($checkPrefix))) {
				if (!$silent) {
					debug("Class '" . $class . "' was not prepended with '" . $checkPrefix . "'", 't3lib_div::getUserObj');
				}
				return FALSE;
			}

				// Check if class exists:
			if (class_exists($class)) {
				$classObj = self::makeInstance($class);

					// If persistent object should be created, set reference:
				if ($storePersistentObject) {
					$GLOBALS['T3_VAR']['getUserObj'][$classRef] = $classObj;
				}

				return $classObj;
			} else {
				if (!$silent) {
					debug("<strong>ERROR:</strong> No class named: " . $class, 't3lib_div::getUserObj');
				}
			}
		}
	}

	/**
	 * Checks if a class or function has a valid prefix: tx_, Tx_ or custom, e.g. user_
	 *
	 * @param string $classRef The class or function to check
	 * @param array $additionalPrefixes Additional allowed prefixes, mostly this will be user_
	 * @return bool TRUE if name is allowed
	 */
	public static function hasValidClassPrefix($classRef, array $additionalPrefixes = array()) {
		if (empty($classRef)) {
			return FALSE;
		}
		if (!is_string($classRef)) {
			throw new InvalidArgumentException('$classRef has to be of type string', 1313917992);
		}
		$hasValidPrefix = FALSE;
		$validPrefixes = self::getValidClassPrefixes();
		$classRef = trim($classRef);

		if (count($additionalPrefixes)) {
			$validPrefixes = array_merge($validPrefixes, $additionalPrefixes);
		}
		foreach ($validPrefixes as $prefixToCheck) {
			if (self::isFirstPartOfStr($classRef, $prefixToCheck) || $prefixToCheck === '') {
				$hasValidPrefix = TRUE;
				break;
			}
		}

		return $hasValidPrefix;
	}

	/**
	 * Returns all valid class prefixes.
	 *
	 * @return array Array of valid prefixed of class names
	 */
	public static function getValidClassPrefixes() {
		$validPrefixes = array('tx_', 'Tx_', 'user_', 'User_');
		if (
			isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['additionalAllowedClassPrefixes'])
			&& is_string($GLOBALS['TYPO3_CONF_VARS']['SYS']['additionalAllowedClassPrefixes'])
		) {
			$validPrefixes = array_merge(
				$validPrefixes,
				t3lib_div::trimExplode(',', $GLOBALS['TYPO3_CONF_VARS']['SYS']['additionalAllowedClassPrefixes'])
			);
		}
		return $validPrefixes;
	}

	/**
	 * Creates an instance of a class taking into account the class-extensions
	 * API of TYPO3. USE THIS method instead of the PHP "new" keyword.
	 * Eg. "$obj = new myclass;" should be "$obj = t3lib_div::makeInstance("myclass")" instead!
	 *
	 * You can also pass arguments for a constructor:
	 * 		t3lib_div::makeInstance('myClass', $arg1, $arg2, ..., $argN)
	 *
	 * @throws InvalidArgumentException if classname is an empty string
	 * @param string $className
	 * 			name of the class to instantiate, must not be empty
	 * @return object the created instance
	 */
	public static function makeInstance($className) {
		if (!is_string($className) || empty($className)) {
			throw new InvalidArgumentException('$className must be a non empty string.', 1288965219);
		}

			// Determine final class name which must be instantiated, this takes XCLASS handling
			// into account. Cache in a local array to save some cycles for consecutive calls.
		if (!isset(self::$finalClassNameRegister[$className])) {
			self::$finalClassNameRegister[$className] = self::getClassName($className);
		}
		$finalClassName = self::$finalClassNameRegister[$className];

			// Return singleton instance if it is already registered
		if (isset(self::$singletonInstances[$finalClassName])) {
			return self::$singletonInstances[$finalClassName];
		}

			// Return instance if it has been injected by addInstance()
		if (isset(self::$nonSingletonInstances[$finalClassName])
			&& !empty(self::$nonSingletonInstances[$finalClassName])
		) {
			return array_shift(self::$nonSingletonInstances[$finalClassName]);
		}

			// Create new instance and call constructor with parameters
		$instance = static::instantiateClass($finalClassName, func_get_args());

			// Register new singleton instance
		if ($instance instanceof t3lib_Singleton) {
			self::$singletonInstances[$finalClassName] = $instance;
		}

		return $instance;
	}

	/**
	 * Speed optimized alternative to ReflectionClass::newInstanceArgs()
	 *
	 * @param string $className Name of the class to instantiate
	 * @param array $arguments Arguments passed to self::makeInstance() thus the first one with index 0 holds the requested class name
	 * @return mixed
	 */
	protected static function instantiateClass($className, $arguments) {
		switch (count($arguments)) {
			case 1:
				$instance = new $className();
				break;
			case 2:
				$instance = new $className($arguments[1]);
				break;
			case 3:
				$instance = new $className($arguments[1], $arguments[2]);
				break;
			case 4:
				$instance = new $className($arguments[1], $arguments[2], $arguments[3]);
				break;
			case 5:
				$instance = new $className($arguments[1], $arguments[2], $arguments[3], $arguments[4]);
				break;
			case 6:
				$instance = new $className($arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5]);
				break;
			case 7:
				$instance = new $className($arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6]);
				break;
			case 8:
				$instance = new $className($arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7]);
				break;
			case 9:
				$instance = new $className($arguments[1], $arguments[2], $arguments[3], $arguments[4], $arguments[5], $arguments[6], $arguments[7], $arguments[8]);
				break;
			default:
				// The default case for classes with constructors that have more than 8 arguments.
				// This will fail when one of the arguments shall be passed by reference.
				// In case we really need to support this edge case, we can implement the solution from here: https://review.typo3.org/26344
				$class = new ReflectionClass($className);
				array_shift($arguments);
				$instance = $class->newInstanceArgs($arguments);
				return $instance;
		}
		return $instance;
	}

	/**
	 * Returns the class name for a new instance, taking into account the
	 * class-extension API.
	 *
	 * @param string $className Base class name to evaluate
	 * @return string Final class name to instantiate with "new [classname]"
	 */
	protected static function getClassName($className) {
		if (class_exists($className)) {
			while (class_exists('ux_' . $className, FALSE)) {
				$className = 'ux_' . $className;
			}
		}

		return $className;
	}

	/**
	 * Sets the instance of a singleton class to be returned by makeInstance.
	 *
	 * If this function is called multiple times for the same $className,
	 * makeInstance will return the last set instance.
	 *
	 * Warning: This is a helper method for unit tests. Do not call this directly in production code!
	 *
	 * @see makeInstance
	 * @param string $className
	 *        the name of the class to set, must not be empty
	 * @param t3lib_Singleton $instance
	 *        the instance to set, must be an instance of $className
	 * @return void
	 */
	public static function setSingletonInstance($className, t3lib_Singleton $instance) {
		self::checkInstanceClassName($className, $instance);
		self::$singletonInstances[$className] = $instance;
	}

	/**
	 * Sets the instance of a non-singleton class to be returned by makeInstance.
	 *
	 * If this function is called multiple times for the same $className,
	 * makeInstance will return the instances in the order in which they have
	 * been added (FIFO).
	 *
	 * Warning: This is a helper method for unit tests. Do not call this directly in production code!
	 *
	 * @see makeInstance
	 * @throws InvalidArgumentException if class extends t3lib_Singleton
	 * @param string $className
	 *        the name of the class to set, must not be empty
	 * @param object $instance
	 *        the instance to set, must be an instance of $className
	 * @return void
	 */
	public static function addInstance($className, $instance) {
		self::checkInstanceClassName($className, $instance);

		if ($instance instanceof t3lib_Singleton) {
			throw new InvalidArgumentException(
				'$instance must not be an instance of t3lib_Singleton. ' .
					'For setting singletons, please use setSingletonInstance.',
				1288969325
			);
		}

		if (!isset(self::$nonSingletonInstances[$className])) {
			self::$nonSingletonInstances[$className] = array();
		}
		self::$nonSingletonInstances[$className][] = $instance;
	}

	/**
	 * Checks that $className is non-empty and that $instance is an instance of
	 * $className.
	 *
	 * @throws InvalidArgumentException if $className is empty or if $instance is no instance of $className
	 * @param string $className a class name
	 * @param object $instance an object
	 * @return void
	 */
	protected static function checkInstanceClassName($className, $instance) {
		if ($className === '') {
			throw new InvalidArgumentException('$className must not be empty.', 1288967479);
		}
		if (!($instance instanceof $className)) {
			throw new InvalidArgumentException(
				'$instance must be an instance of ' . $className . ', but actually is an instance of ' . get_class($instance) . '.',
				1288967686
			);
		}
	}

	/**
	 * Purge all instances returned by makeInstance.
	 *
	 * This function is most useful when called from tearDown in a test case
	 * to drop any instances that have been created by the tests.
	 *
	 * Warning: This is a helper method for unit tests. Do not call this directly in production code!
	 *
	 * @see makeInstance
	 * @return void
	 */
	public static function purgeInstances() {
		self::$singletonInstances = array();
		self::$nonSingletonInstances = array();
	}

	/**
	 * Find the best service and check if it works.
	 * Returns object of the service class.
	 *
	 * @param string $serviceType Type of service (service key).
	 * @param string $serviceSubType Sub type like file extensions or similar. Defined by the service.
	 * @param mixed $excludeServiceKeys List of service keys which should be excluded in the search for a service. Array or comma list.
	 * @return object The service object or an array with error info's.
	 */
	public static function makeInstanceService($serviceType, $serviceSubType = '', $excludeServiceKeys = array()) {
		$error = FALSE;

		if (!is_array($excludeServiceKeys)) {
			$excludeServiceKeys = self::trimExplode(',', $excludeServiceKeys, 1);
		}

		$requestInfo = array(
			'requestedServiceType' => $serviceType,
			'requestedServiceSubType' => $serviceSubType,
			'requestedExcludeServiceKeys' => $excludeServiceKeys,
		);

		while ($info = t3lib_extMgm::findService($serviceType, $serviceSubType, $excludeServiceKeys)) {

				// provide information about requested service to service object
			$info = array_merge($info, $requestInfo);

				// Check persistent object and if found, call directly and exit.
			if (is_object($GLOBALS['T3_VAR']['makeInstanceService'][$info['className']])) {

					// update request info in persistent object
				$GLOBALS['T3_VAR']['makeInstanceService'][$info['className']]->info = $info;

					// reset service and return object
				$GLOBALS['T3_VAR']['makeInstanceService'][$info['className']]->reset();
				return $GLOBALS['T3_VAR']['makeInstanceService'][$info['className']];

				// include file and create object
			} else {
				$requireFile = self::getFileAbsFileName($info['classFile']);
				if (@is_file($requireFile)) {
					self::requireOnce($requireFile);
					$obj = self::makeInstance($info['className']);
					if (is_object($obj)) {
						if (!@is_callable(array($obj, 'init'))) {
								// use silent logging??? I don't think so.
							die ('Broken service:' . t3lib_utility_Debug::viewArray($info));
						}
						$obj->info = $info;
						if ($obj->init()) { // service available?

								// create persistent object
							$GLOBALS['T3_VAR']['makeInstanceService'][$info['className']] = $obj;

								// needed to delete temp files
							register_shutdown_function(array(&$obj, '__destruct'));

							return $obj; // object is passed as reference by function definition
						}
						$error = $obj->getLastErrorArray();
						unset($obj);
					}
				}
			}
				// deactivate the service
			t3lib_extMgm::deactivateService($info['serviceType'], $info['serviceKey']);
		}
		return $error;
	}

	/**
	 * Require a class for TYPO3
	 * Useful to require classes from inside other classes (not global scope). A limited set of global variables are available (see function)
	 *
	 * @param string $requireFile: Path of the file to be included
	 * @return void
	 */
	public static function requireOnce($requireFile) {
			// Needed for require_once
		global $T3_SERVICES, $T3_VAR, $TYPO3_CONF_VARS;

		require_once ($requireFile);
	}

	/**
	 * Requires a class for TYPO3
	 * Useful to require classes from inside other classes (not global scope).
	 * A limited set of global variables are available (see function)
	 *
	 * @param string $requireFile: Path of the file to be included
	 * @return void
	 */
	public static function requireFile($requireFile) {
			// Needed for require
		global $T3_SERVICES, $T3_VAR, $TYPO3_CONF_VARS;
		require $requireFile;
	}

	/**
	 * Simple substitute for the PHP function mail() which allows you to specify encoding and character set
	 * The fifth parameter ($encoding) will allow you to specify 'base64' encryption for the output (set $encoding=base64)
	 * Further the output has the charset set to UTF-8 by default.
	 *
	 * @param string $email Email address to send to. (see PHP function mail())
	 * @param string $subject Subject line, non-encoded. (see PHP function mail())
	 * @param string $message Message content, non-encoded. (see PHP function mail())
	 * @param string $headers Headers, separated by LF
	 * @param string $encoding Encoding type: "base64", "quoted-printable", "8bit". Default value is "quoted-printable".
	 * @param string $charset Charset used in encoding-headers (only if $encoding is set to a valid value which produces such a header)
	 * @param boolean $dontEncodeHeader If set, the header content will not be encoded.
	 * @return boolean TRUE if mail was accepted for delivery, FALSE otherwise
	 */
	public static function plainMailEncoded($email, $subject, $message, $headers = '', $encoding = 'quoted-printable', $charset = '', $dontEncodeHeader = FALSE) {
		if (!$charset) {
			$charset = 'utf-8';
		}

		$email = self::normalizeMailAddress($email);
		if (!$dontEncodeHeader) {
				// Mail headers must be ASCII, therefore we convert the whole header to either base64 or quoted_printable
			$newHeaders = array();
			foreach (explode(LF, $headers) as $line) { // Split the header in lines and convert each line separately
				$parts = explode(': ', $line, 2); // Field tags must not be encoded
				if (count($parts) == 2) {
					if (0 == strcasecmp($parts[0], 'from')) {
						$parts[1] = self::normalizeMailAddress($parts[1]);
					}
					$parts[1] = self::encodeHeader($parts[1], $encoding, $charset);
					$newHeaders[] = implode(': ', $parts);
				} else {
					$newHeaders[] = $line; // Should never happen - is such a mail header valid? Anyway, just add the unchanged line...
				}
			}
			$headers = implode(LF, $newHeaders);
			unset($newHeaders);

			$email = self::encodeHeader($email, $encoding, $charset); // Email address must not be encoded, but it could be appended by a name which should be so (e.g. "Kasper Skårhøj <kasperYYYY@typo3.com>")
			$subject = self::encodeHeader($subject, $encoding, $charset);
		}

		switch ((string) $encoding) {
			case 'base64':
				$headers = trim($headers) . LF .
						'Mime-Version: 1.0' . LF .
						'Content-Type: text/plain; charset="' . $charset . '"' . LF .
						'Content-Transfer-Encoding: base64';

				$message = trim(chunk_split(base64_encode($message . LF))) . LF; // Adding LF because I think MS outlook 2002 wants it... may be removed later again.
				break;
			case '8bit':
				$headers = trim($headers) . LF .
						'Mime-Version: 1.0' . LF .
						'Content-Type: text/plain; charset=' . $charset . LF .
						'Content-Transfer-Encoding: 8bit';
				break;
			case 'quoted-printable':
			default:
				$headers = trim($headers) . LF .
						'Mime-Version: 1.0' . LF .
						'Content-Type: text/plain; charset=' . $charset . LF .
						'Content-Transfer-Encoding: quoted-printable';

				$message = self::quoted_printable($message);
				break;
		}

			// Headers must be separated by CRLF according to RFC 2822, not just LF.
			// But many servers (Gmail, for example) behave incorrectly and want only LF.
			// So we stick to LF in all cases.
		$headers = trim(implode(LF, self::trimExplode(LF, $headers, TRUE))); // Make sure no empty lines are there.

		return t3lib_utility_Mail::mail($email, $subject, $message, $headers);
	}

	/**
	 * Implementation of quoted-printable encode.
	 * See RFC 1521, section 5.1 Quoted-Printable Content-Transfer-Encoding
	 *
	 * @param string $string Content to encode
	 * @param integer $maxlen Length of the lines, default is 76
	 * @return string The QP encoded string
	 */
	public static function quoted_printable($string, $maxlen = 76) {
			// Make sure the string contains only Unix line breaks
		$string = str_replace(CRLF, LF, $string); // Replace Windows breaks (\r\n)
		$string = str_replace(CR, LF, $string); // Replace Mac breaks (\r)

		$linebreak = LF; // Default line break for Unix systems.
		if (TYPO3_OS == 'WIN') {
			$linebreak = CRLF; // Line break for Windows. This is needed because PHP on Windows systems send mails via SMTP instead of using sendmail, and thus the line break needs to be \r\n.
		}

		$newString = '';
		$theLines = explode(LF, $string); // Split lines
		foreach ($theLines as $val) {
			$newVal = '';
			$theValLen = strlen($val);
			$len = 0;
			for ($index = 0; $index < $theValLen; $index++) { // Walk through each character of this line
				$char = substr($val, $index, 1);
				$ordVal = ord($char);
				if ($len > ($maxlen - 4) || ($len > ($maxlen - 14) && $ordVal == 32)) {
					$newVal .= '=' . $linebreak; // Add a line break
					$len = 0; // Reset the length counter
				}
				if (($ordVal >= 33 && $ordVal <= 60) || ($ordVal >= 62 && $ordVal <= 126) || $ordVal == 9 || $ordVal == 32) {
					$newVal .= $char; // This character is ok, add it to the message
					$len++;
				} else {
					$newVal .= sprintf('=%02X', $ordVal); // Special character, needs to be encoded
					$len += 3;
				}
			}
			$newVal = preg_replace('/' . chr(32) . '$/', '=20', $newVal); // Replaces a possible SPACE-character at the end of a line
			$newVal = preg_replace('/' . TAB . '$/', '=09', $newVal); // Replaces a possible TAB-character at the end of a line
			$newString .= $newVal . $linebreak;
		}
		return preg_replace('/' . $linebreak . '$/', '', $newString); // Remove last newline
	}

	/**
	 * Encode header lines
	 * Email headers must be ASCII, therefore they will be encoded to quoted_printable (default) or base64.
	 *
	 * @param string $line Content to encode
	 * @param string $enc Encoding type: "base64" or "quoted-printable". Default value is "quoted-printable".
	 * @param string $charset Charset used for encoding
	 * @return string The encoded string
	 */
	public static function encodeHeader($line, $enc = 'quoted-printable', $charset = 'utf-8') {
			// Avoid problems if "###" is found in $line (would conflict with the placeholder which is used below)
		if (strpos($line, '###') !== FALSE) {
			return $line;
		}
			// Check if any non-ASCII characters are found - otherwise encoding is not needed
		if (!preg_match('/[^' . chr(32) . '-' . chr(127) . ']/', $line)) {
			return $line;
		}
			// Wrap email addresses in a special marker
		$line = preg_replace('/([^ ]+@[^ ]+)/', '###$1###', $line);

		$matches = preg_split('/(.?###.+###.?|\(|\))/', $line, -1, PREG_SPLIT_NO_EMPTY);
		foreach ($matches as $part) {
			$oldPart = $part;
			$partWasQuoted = ($part{0} == '"');
			$part = trim($part, '"');
			switch ((string) $enc) {
				case 'base64':
					$part = '=?' . $charset . '?B?' . base64_encode($part) . '?=';
					break;
				case 'quoted-printable':
				default:
					$qpValue = self::quoted_printable($part, 1000);
					if ($part != $qpValue) {
							// Encoded words in the header should not contain non-encoded:
							// * spaces. "_" is a shortcut for "=20". See RFC 2047 for details.
							// * question mark. See RFC 1342 (http://tools.ietf.org/html/rfc1342)
						$search = array(' ', '?');
						$replace = array('_', '=3F');
						$qpValue = str_replace($search, $replace, $qpValue);
						$part = '=?' . $charset . '?Q?' . $qpValue . '?=';
					}
					break;
			}
			if ($partWasQuoted) {
				$part = '"' . $part . '"';
			}
			$line = str_replace($oldPart, $part, $line);
		}
		$line = preg_replace('/###(.+?)###/', '$1', $line); // Remove the wrappers

		return $line;
	}

	/**
	 * Takes a clear-text message body for a plain text email, finds all 'http://' links and if they are longer than 76 chars they are converted to a shorter URL with a hash parameter. The real parameter is stored in the database and the hash-parameter/URL will be redirected to the real parameter when the link is clicked.
	 * This function is about preserving long links in messages.
	 *
	 * @param string $message Message content
	 * @param string $urlmode URL mode; "76" or "all"
	 * @param string $index_script_url URL of index script (see makeRedirectUrl())
	 * @return string Processed message content
	 * @see makeRedirectUrl()
	 */
	public static function substUrlsInPlainText($message, $urlmode = '76', $index_script_url = '') {
		$lengthLimit = FALSE;

		switch ((string) $urlmode) {
			case '':
				$lengthLimit = FALSE;
				break;
			case 'all':
				$lengthLimit = 0;
				break;
			case '76':
			default:
				$lengthLimit = (int) $urlmode;
		}

		if ($lengthLimit === FALSE) {
				// no processing
			$messageSubstituted = $message;
		} else {
			$messageSubstituted = preg_replace(
				'/(http|https):\/\/.+(?=[\]\.\?]*([\! \'"()<>]+|$))/eiU',
				'self::makeRedirectUrl("\\0",' . $lengthLimit . ',"' . $index_script_url . '")',
				$message
			);
		}

		return $messageSubstituted;
	}

	/**
	 * Sub-function for substUrlsInPlainText() above.
	 *
	 * @param string $inUrl Input URL
	 * @param integer $l URL string length limit
	 * @param string $index_script_url URL of "index script" - the prefix of the "?RDCT=..." parameter. If not supplied it will default to t3lib_div::getIndpEnv('TYPO3_REQUEST_DIR').'index.php'
	 * @return string Processed URL
	 */
	public static function makeRedirectUrl($inUrl, $l = 0, $index_script_url = '') {
		if (strlen($inUrl) > $l) {
			$md5 = substr(md5($inUrl), 0, 20);
			$count = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows(
				'*',
				'cache_md5params',
					'md5hash=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($md5, 'cache_md5params')
			);
			if (!$count) {
				$insertFields = array(
					'md5hash' => $md5,
					'tstamp' => $GLOBALS['EXEC_TIME'],
					'type' => 2,
					'params' => $inUrl
				);

				$GLOBALS['TYPO3_DB']->exec_INSERTquery('cache_md5params', $insertFields);
			}
			$inUrl = ($index_script_url ? $index_script_url : self::getIndpEnv('TYPO3_REQUEST_DIR') . 'index.php') .
					'?RDCT=' . $md5;
		}

		return $inUrl;
	}

	/**
	 * Function to compensate for FreeType2 96 dpi
	 *
	 * @param integer $font_size Fontsize for freetype function call
	 * @return integer Compensated fontsize based on $GLOBALS['TYPO3_CONF_VARS']['GFX']['TTFdpi']
	 */
	public static function freetypeDpiComp($font_size) {
		$dpi = intval($GLOBALS['TYPO3_CONF_VARS']['GFX']['TTFdpi']);
		if ($dpi != 72) {
			$font_size = $font_size / $dpi * 72;
		}
		return $font_size;
	}

	/**
	 * Initialize the system log.
	 *
	 * @return void
	 * @see sysLog()
	 */
	public static function initSysLog() {
			// for CLI logging name is <fqdn-hostname>:<TYPO3-path>
			// Note that TYPO3_REQUESTTYPE is not used here as it may not yet be defined
		if (defined('TYPO3_cliMode') && TYPO3_cliMode) {
			$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLogHost'] = self::getHostname($requestHost = FALSE) . ':' . PATH_site;
		}
			// for Web logging name is <protocol>://<request-hostame>/<site-path>
		else {
			$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLogHost'] = self::getIndpEnv('TYPO3_SITE_URL');
		}

			// init custom logging
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLog'])) {
			$params = array('initLog' => TRUE);
			$fakeThis = FALSE;
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLog'] as $hookMethod) {
				self::callUserFunction($hookMethod, $params, $fakeThis);
			}
		}

			// init TYPO3 logging
		foreach (explode(';', $GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLog'], 2) as $log) {
			list($type, $destination) = explode(',', $log, 3);

			if ($type == 'syslog') {
				if (TYPO3_OS == 'WIN') {
					$facility = LOG_USER;
				} else {
					$facility = constant('LOG_' . strtoupper($destination));
				}
				openlog($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLogHost'], LOG_ODELAY, $facility);
			}
		}

		$GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLogLevel'] = t3lib_utility_Math::forceIntegerInRange($GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLogLevel'], 0, 4);
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLogInit'] = TRUE;
	}

	/**
	 * Logs message to the system log.
	 * This should be implemented around the source code, including the Core and both frontend and backend, logging serious errors.
	 * If you want to implement the sysLog in your applications, simply add lines like:
	 *		 t3lib_div::sysLog('[write message in English here]', 'extension_key', 'severity');
	 *
	 * @param string $msg Message (in English).
	 * @param string $extKey Extension key (from which extension you are calling the log) or "Core"
	 * @param integer $severity Severity: 0 is info, 1 is notice, 2 is warning, 3 is error, 4 is fatal error
	 * @return void
	 */
	public static function sysLog($msg, $extKey, $severity = 0) {
		$severity = t3lib_utility_Math::forceIntegerInRange($severity, 0, 4);

			// is message worth logging?
		if (intval($GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLogLevel']) > $severity) {
			return;
		}

			// initialize logging
		if (!$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLogInit']) {
			self::initSysLog();
		}

			// do custom logging
		if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLog']) &&
				is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLog'])) {
			$params = array('msg' => $msg, 'extKey' => $extKey, 'backTrace' => debug_backtrace(), 'severity' => $severity);
			$fakeThis = FALSE;
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLog'] as $hookMethod) {
				self::callUserFunction($hookMethod, $params, $fakeThis);
			}
		}

			// TYPO3 logging enabled?
		if (!$GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLog']) {
			return;
		}

		$dateFormat = $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'];
		$timeFormat = $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'];

			// use all configured logging options
		foreach (explode(';', $GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLog'], 2) as $log) {
			list($type, $destination, $level) = explode(',', $log, 4);

				// is message worth logging for this log type?
			if (intval($level) > $severity) {
				continue;
			}

			$msgLine = ' - ' . $extKey . ': ' . $msg;

				// write message to a file
			if ($type == 'file') {
				$lockObject = t3lib_div::makeInstance('t3lib_lock', $destination, $GLOBALS['TYPO3_CONF_VARS']['SYS']['lockingMode']);
				/** @var t3lib_lock $lockObject */
				$lockObject->setEnableLogging(FALSE);
				$lockObject->acquire();
				$file = fopen($destination, 'a');
				if ($file) {
					fwrite($file, date($dateFormat . ' ' . $timeFormat) . $msgLine . LF);
					fclose($file);
					self::fixPermissions($destination);
				}
				$lockObject->release();
			}
				// send message per mail
			elseif ($type == 'mail') {
				list($to, $from) = explode('/', $destination);
				if (!t3lib_div::validEmail($from)) {
					$from = t3lib_utility_Mail::getSystemFrom();
				}
				/** @var $mail t3lib_mail_Message */
				$mail = t3lib_div::makeInstance('t3lib_mail_Message');
				$mail->setTo($to)
						->setFrom($from)
						->setSubject('Warning - error in TYPO3 installation')
						->setBody('Host: ' . $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLogHost'] . LF .
								'Extension: ' . $extKey . LF .
								'Severity: ' . $severity . LF .
								LF . $msg
				);
				$mail->send();
			}
				// use the PHP error log
			elseif ($type == 'error_log') {
				error_log($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['systemLogHost'] . $msgLine, 0);
			}
				// use the system log
			elseif ($type == 'syslog') {
				$priority = array(LOG_INFO, LOG_NOTICE, LOG_WARNING, LOG_ERR, LOG_CRIT);
				syslog($priority[(int) $severity], $msgLine);
			}
		}
	}

	/**
	 * Logs message to the development log.
	 * This should be implemented around the source code, both frontend and backend, logging everything from the flow through an application, messages, results from comparisons to fatal errors.
	 * The result is meant to make sense to developers during development or debugging of a site.
	 * The idea is that this function is only a wrapper for external extensions which can set a hook which will be allowed to handle the logging of the information to any format they might wish and with any kind of filter they would like.
	 * If you want to implement the devLog in your applications, simply add lines like:
	 *		 if (TYPO3_DLOG)	t3lib_div::devLog('[write message in english here]', 'extension key');
	 *
	 * @param string $msg Message (in english).
	 * @param string $extKey Extension key (from which extension you are calling the log)
	 * @param integer $severity Severity: 0 is info, 1 is notice, 2 is warning, 3 is fatal error, -1 is "OK" message
	 * @param mixed $dataVar Additional data you want to pass to the logger.
	 * @return void
	 */
	public static function devLog($msg, $extKey, $severity = 0, $dataVar = FALSE) {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'])) {
			$params = array('msg' => $msg, 'extKey' => $extKey, 'severity' => $severity, 'dataVar' => $dataVar);
			$fakeThis = FALSE;
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'] as $hookMethod) {
				self::callUserFunction($hookMethod, $params, $fakeThis);
			}
		}
	}

	/**
	 * Writes a message to the deprecation log.
	 *
	 * @param string $msg Message (in English).
	 * @return void
	 */
	public static function deprecationLog($msg) {
		if (!$GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog']) {
			return;
		}

		$log = $GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog'];
		$date = date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] . ' ' . $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm'] . ': ');

			// legacy values (no strict comparison, $log can be boolean, string or int)
		if ($log === TRUE || $log == '1') {
			$log = 'file';
		}

		if (stripos($log, 'file') !== FALSE) {
				// In case lock is acquired before autoloader was defined:
			if (class_exists('t3lib_lock') === FALSE) {
				require_once PATH_t3lib . 'class.t3lib_lock.php';
			}
				// write a longer message to the deprecation log
			$destination = self::getDeprecationLogFileName();
			$lockObject = t3lib_div::makeInstance('t3lib_lock', $destination, $GLOBALS['TYPO3_CONF_VARS']['SYS']['lockingMode']);
			/** @var t3lib_lock $lockObject */
			$lockObject->setEnableLogging(FALSE);
			$lockObject->acquire();
			$file = @fopen($destination, 'a');
			if ($file) {
				@fwrite($file, $date . $msg . LF);
				@fclose($file);
				self::fixPermissions($destination);
			}
			$lockObject->release();
		}

		if (stripos($log, 'devlog') !== FALSE) {
				// copy message also to the developer log
			self::devLog($msg, 'Core', self::SYSLOG_SEVERITY_WARNING);
		}

			// do not use console in login screen
		if (stripos($log, 'console') !== FALSE && isset($GLOBALS['BE_USER']->user['uid'])) {
			t3lib_utility_Debug::debug($msg, $date, 'Deprecation Log');
		}
	}

	/**
	 * Gets the absolute path to the deprecation log file.
	 *
	 * @return string absolute path to the deprecation log file
	 */
	public static function getDeprecationLogFileName() {
		return PATH_typo3conf .
				'deprecation_' .
				self::shortMD5(
					PATH_site . $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']
				) .
				'.log';
	}

	/**
	 * Logs a call to a deprecated function.
	 * The log message will be taken from the annotation.
	 * @return void
	 */
	public static function logDeprecatedFunction() {
		if (!$GLOBALS['TYPO3_CONF_VARS']['SYS']['enableDeprecationLog']) {
			return;
		}

			// This require_once is needed for deprecation calls
			// thrown early during bootstrap, if the autoloader is
			// not instantiated yet. This can happen for example if
			// ext_localconf triggers a deprecation.
		require_once __DIR__.'/class.t3lib_utility_debug.php';

		$trail = debug_backtrace();

		if ($trail[1]['type']) {
			$function = new ReflectionMethod($trail[1]['class'], $trail[1]['function']);
		} else {
			$function = new ReflectionFunction($trail[1]['function']);
		}

		$msg = '';
		if (preg_match('/@deprecated\s+(.*)/', $function->getDocComment(), $match)) {
			$msg = $match[1];
		}

			// trigger PHP error with a short message: <function> is deprecated (called from <source>, defined in <source>)
		$errorMsg = 'Function ' . $trail[1]['function'];
		if ($trail[1]['class']) {
			$errorMsg .= ' of class ' . $trail[1]['class'];
		}
		$errorMsg .= ' is deprecated (called from ' . $trail[1]['file'] . '#' . $trail[1]['line'] . ', defined in ' . $function->getFileName() . '#' . $function->getStartLine() . ')';

			// write a longer message to the deprecation log: <function> <annotion> - <trace> (<source>)
		$logMsg = $trail[1]['class'] . $trail[1]['type'] . $trail[1]['function'];
		$logMsg .= '() - ' . $msg.' - ' . t3lib_utility_Debug::debugTrail();
		$logMsg .= ' (' . substr($function->getFileName(), strlen(PATH_site)) . '#' . $function->getStartLine() . ')';
		self::deprecationLog($logMsg);
	}

	/**
	 * Converts a one dimensional array to a one line string which can be used for logging or debugging output
	 * Example: "loginType: FE; refInfo: Array; HTTP_HOST: www.example.org; REMOTE_ADDR: 192.168.1.5; REMOTE_HOST:; security_level:; showHiddenRecords: 0;"
	 *
	 * @param array $arr Data array which should be outputted
	 * @param mixed $valueList List of keys which should be listed in the output string. Pass a comma list or an array. An empty list outputs the whole array.
	 * @param integer $valueLength Long string values are shortened to this length. Default: 20
	 * @return string Output string with key names and their value as string
	 */
	public static function arrayToLogString(array $arr, $valueList = array(), $valueLength = 20) {
		$str = '';
		if (!is_array($valueList)) {
			$valueList = self::trimExplode(',', $valueList, 1);
		}
		$valListCnt = count($valueList);
		foreach ($arr as $key => $value) {
			if (!$valListCnt || in_array($key, $valueList)) {
				$str .= (string) $key . trim(': ' . self::fixed_lgd_cs(str_replace(LF, '|', (string) $value), $valueLength)) . '; ';
			}
		}
		return $str;
	}

	/**
	 * Compile the command for running ImageMagick/GraphicsMagick.
	 *
	 * @param string $command Command to be run: identify, convert or combine/composite
	 * @param string $parameters The parameters string
	 * @param string $path Override the default path (e.g. used by the install tool)
	 * @return string Compiled command that deals with IM6 & GraphicsMagick
	 */
	public static function imageMagickCommand($command, $parameters, $path = '') {
		return t3lib_utility_Command::imageMagickCommand($command, $parameters, $path);
	}

	/**
	 * Explode a string (normally a list of filenames) with whitespaces by considering quotes in that string. This is mostly needed by the imageMagickCommand function above.
	 *
	 * @param string $parameters The whole parameters string
	 * @param boolean $unQuote If set, the elements of the resulting array are unquoted.
	 * @return array Exploded parameters
	 */
	public static function unQuoteFilenames($parameters, $unQuote = FALSE) {
		$paramsArr = explode(' ', trim($parameters));

		$quoteActive = -1; // Whenever a quote character (") is found, $quoteActive is set to the element number inside of $params. A value of -1 means that there are not open quotes at the current position.
		foreach ($paramsArr as $k => $v) {
			if ($quoteActive > -1) {
				$paramsArr[$quoteActive] .= ' ' . $v;
				unset($paramsArr[$k]);
				if (substr($v, -1) === $paramsArr[$quoteActive][0]) {
					$quoteActive = -1;
				}
			} elseif (!trim($v)) {
				unset($paramsArr[$k]); // Remove empty elements

			} elseif (preg_match('/^(["\'])/', $v) && substr($v, -1) !== $v[0]) {
				$quoteActive = $k;
			}
		}

		if ($unQuote) {
			foreach ($paramsArr as $key => &$val) {
				$val = preg_replace('/(^"|"$)/', '', $val);
				$val = preg_replace('/(^\'|\'$)/', '', $val);

			}
			unset($val);
		}
			// return reindexed array
		return array_values($paramsArr);
	}


	/**
	 * Quotes a string for usage as JS parameter.
	 *
	 * @param string $value the string to encode, may be empty
	 *
	 * @return string the encoded value already quoted (with single quotes),
	 *				will not be empty
	 */
	public static function quoteJSvalue($value) {
		$escapedValue = t3lib_div::makeInstance('t3lib_codec_JavaScriptEncoder')->encode($value);
		return '\'' . $escapedValue . '\'';
	}


	/**
	 * Ends and cleans all output buffers
	 *
	 * @return void
	 */
	public static function cleanOutputBuffers() {
		while (ob_end_clean()) {
			;
		}
		header('Content-Encoding: None', TRUE);
	}


	/**
	 * Ends and flushes all output buffers
	 *
	 * @return void
	 */
	public static function flushOutputBuffers() {
		$obContent = '';

		while ($content = ob_get_clean()) {
			$obContent .= $content;
		}

			// if previously a "Content-Encoding: whatever" has been set, we have to unset it
		if (!headers_sent()) {
			$headersList = headers_list();
			foreach ($headersList as $header) {
					// split it up at the :
				list($key, $value) = self::trimExplode(':', $header, TRUE);
					// check if we have a Content-Encoding other than 'None'
				if (strtolower($key) === 'content-encoding' && strtolower($value) !== 'none') {
					header('Content-Encoding: None');
					break;
				}
			}
		}
		echo $obContent;
	}
}

?>
