<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
/**
 * Contains the reknown class "t3lib_div" with general purpose functions
 *
 * $Id$
 * Revised for TYPO3 3.6 July/2003 by Kasper Skaarhoj
 * XHTML compliant
 * Usage counts are based on search 22/2 2003 through whole source including tslib/
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *  211: class t3lib_div
 *
 *              SECTION: GET/POST Variables
 *  241:     function _GP($var)
 *  259:     function _GET($var=NULL)
 *  276:     function _POST($var=NULL)
 *  292:     function _GETset($inputGet,$key='')
 *  315:     function GPvar($var,$strip=0)
 *  332:     function GParrayMerged($var)
 *
 *              SECTION: IMAGE FUNCTIONS
 *  376:     function gif_compress($theFile, $type)
 *  402:     function png_to_gif_by_imagemagick($theFile)
 *  426:     function read_png_gif($theFile,$output_png=0)
 *
 *              SECTION: STRING FUNCTIONS
 *  475:     function fixed_lgd($string,$origChars,$preStr='...')
 *  500:     function fixed_lgd_pre($string,$chars)
 *  514:     function fixed_lgd_cs($string,$chars)
 *  531:     function breakTextForEmail($str,$implChar="\n",$charWidth=76)
 *  550:     function breakLinesForEmail($str,$implChar="\n",$charWidth=76)
 *  585:     function cmpIP($baseIP, $list)
 *  627:     function cmpFQDN($baseIP, $list)
 *  657:     function inList($list,$item)
 *  669:     function rmFromList($element,$list)
 *  687:     function intInRange($theInt,$min,$max=2000000000,$zeroValue=0)
 *  703:     function intval_positive($theInt)
 *  716:     function int_from_ver($verNumberStr)
 *  728:     function md5int($str)
 *  741:     function shortMD5($input, $len=10)
 *  754:     function uniqueList($in_list, $secondParameter=NULL)
 *  768:     function split_fileref($fileref)
 *  805:     function dirname($path)
 *  821:     function modifyHTMLColor($color,$R,$G,$B)
 *  841:     function modifyHTMLColorAll($color,$all)
 *  852:     function rm_endcomma($string)
 *  865:     function danish_strtoupper($string)
 *  880:     function convUmlauts($str)
 *  893:     function testInt($var)
 *  905:     function isFirstPartOfStr($str,$partStr)
 *  921:     function formatSize($sizeInBytes,$labels='')
 *  956:     function convertMicrotime($microtime)
 *  970:     function splitCalc($string,$operators)
 *  992:     function calcPriority($string)
 * 1032:     function calcParenthesis($string)
 * 1058:     function htmlspecialchars_decode($value)
 * 1073:     function deHSCentities($str)
 * 1086:     function slashJS($string,$extended=0,$char="'")
 * 1099:     function rawUrlEncodeJS($str)
 * 1111:     function rawUrlEncodeFP($str)
 * 1122:     function validEmail($email)
 * 1137:     function formatForTextarea($content)
 *
 *              SECTION: ARRAY FUNCTIONS
 * 1168:     function inArray($in_array,$item)
 * 1185:     function intExplode($delim, $string)
 * 1204:     function revExplode($delim, $string, $count=0)
 * 1224:     function trimExplode($delim, $string, $onlyNonEmptyValues=0)
 * 1246:     function uniqueArray($valueArray)
 * 1258:     function removeArrayEntryByValue($array,$cmpValue)
 * 1286:     function implodeArrayForUrl($name,$theArray,$str='',$skipBlank=0,$rawurlencodeParamName=0)
 * 1313:     function compileSelectedGetVarsFromArray($varList,$getArray,$GPvarAlt=1)
 * 1336:     function addSlashesOnArray(&$theArray)
 * 1360:     function stripSlashesOnArray(&$theArray)
 * 1382:     function slashArray($arr,$cmd)
 * 1399:     function array_merge_recursive_overrule($arr0,$arr1,$notAddKeys=0,$includeEmtpyValues=true)
 * 1432:     function array_merge($arr1,$arr2)
 * 1445:     function csvValues($row,$delim=',',$quote='"')
 *
 *              SECTION: HTML/XML PROCESSING
 * 1487:     function get_tag_attributes($tag)
 * 1524:     function split_tag_attributes($tag)
 * 1558:     function implodeAttributes($arr,$xhtmlSafe=FALSE,$dontOmitBlankAttribs=FALSE)
 * 1585:     function implodeParams($arr,$xhtmlSafe=FALSE,$dontOmitBlankAttribs=FALSE)
 * 1600:     function wrapJS($string, $linebreak=TRUE)
 * 1630:     function xml2tree($string,$depth=999)
 * 1716:     function array2xml($array,$NSprefix='',$level=0,$docTag='phparray',$spaceInd=0, $options=array(),$stackData=array())
 * 1830:     function xml2array($string,$NSprefix='',$reportDocTag=FALSE)
 * 1940:     function xmlRecompileFromStructValArray($vals)
 * 1984:     function xmlGetHeaderAttribs($xmlData)
 *
 *              SECTION: FILES FUNCTIONS
 * 2016:     function getURL($url)
 * 2058:     function writeFile($file,$content)
 * 2075:     function fixPermissions($file)
 * 2092:     function writeFileToTypo3tempDir($filepath,$content)
 * 2135:     function mkdir($theNewFolder)
 * 2155:     function get_dirs($path)
 * 2180:     function getFilesInDir($path,$extensionList='',$prependPath=0,$order='')
 * 2234:     function getAllFilesAndFoldersInPath($fileArr,$path,$extList='',$regDirs=0,$recursivityLevels=99)
 * 2257:     function removePrefixPathFromList($fileArr,$prefixToRemove)
 * 2273:     function fixWindowsFilePath($theFile)
 * 2285:     function resolveBackPath($pathStr)
 * 2312:     function locationHeaderUrl($path)
 *
 *              SECTION: DEBUG helper FUNCTIONS
 * 2352:     function debug_ordvalue($string,$characters=100)
 * 2369:     function view_array($array_in)
 * 2397:     function print_array($array_in)
 * 2412:     function debug($var="",$brOrHeader=0)
 *
 *              SECTION: SYSTEM INFORMATION
 * 2481:     function getThisUrl()
 * 2497:     function linkThisScript($getParams=array())
 * 2521:     function linkThisUrl($url,$getParams=array())
 * 2546:     function getIndpEnv($getEnvName)
 * 2735:     function milliseconds()
 * 2747:     function clientInfo($useragent='')
 *
 *              SECTION: TYPO3 SPECIFIC FUNCTIONS
 * 2834:     function getFileAbsFileName($filename,$onlyRelative=1,$relToTYPO3_mainDir=0)
 * 2870:     function validPathStr($theFile)
 * 2881:     function isAbsPath($path)
 * 2892:     function isAllowedAbsPath($path)
 * 2909:     function verifyFilenameAgainstDenyPattern($filename)
 * 2927:     function upload_copy_move($source,$destination)
 * 2953:     function upload_to_tempfile($uploadedFileName)
 * 2971:     function unlink_tempfile($uploadedTempFileName)
 * 2987:     function tempnam($filePrefix)
 * 3000:     function stdAuthCode($uid_or_record,$fields='')
 * 3030:     function cHashParams($addQueryParams)
 * 3059:     function loadTCA($table)
 * 3079:     function resolveSheetDefInDS($dataStructArray,$sheet='sDEF')
 * 3109:     function resolveAllSheetsInDS($dataStructArray)
 * 3138:     function callUserFunction($funcName,&$params,&$ref,$checkPrefix='user_',$silent=0)
 * 3237:     function &getUserObj($classRef,$checkPrefix='user_',$silent=0)
 * 3295:     function &makeInstance($className)
 * 3307:     function makeInstanceClassName($className)
 * 3321:     function &makeInstanceService($serviceType, $serviceSubType='', $excludeServiceKeys=array())
 * 3382:     function plainMailEncoded($email,$subject,$message,$headers='',$enc='',$charset='ISO-8859-1',$dontEncodeSubject=0)
 * 3427:     function quoted_printable($string,$maxlen=76)
 * 3471:     function substUrlsInPlainText($message,$urlmode='76',$index_script_url='')
 * 3505:     function makeRedirectUrl($inUrl,$l=0,$index_script_url='')
 * 3532:     function freetypeDpiComp($font_size)
 * 3551:     function devLog($msg, $extKey, $severity=0, $dataVar=FALSE)
 * 3572:     function arrayToLogString($arr, $valueList=array(), $valueLength=20)
 *
 * TOTAL FUNCTIONS: 117
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */












/**
 * The legendary "t3lib_div" class - Miscellaneous functions for general purpose.
 * Most of the functions does not relate specifically to TYPO3
 * However a section of functions requires certain TYPO3 features available
 * See comments in the source.
 * You are encouraged to use this library in your own scripts!
 *
 * USE:
 * The class is intended to be used without creating an instance of it.
 * So: Don't instantiate - call functions with "t3lib_div::" prefixed the function name.
 * So use t3lib_div::[method-name] to refer to the functions, eg. 't3lib_div::milliseconds()'
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @package TYPO3
 * @subpackage t3lib
 */
class t3lib_div {





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
	 * This function substitutes t3lib_div::GPvar()
	 * To enhancement security in your scripts, please consider using t3lib_div::_GET or t3lib_div::_POST if you already know by which method your data is arriving to the scripts!
	 * Usage: 537
	 *
	 * @param	string		GET/POST var to return
	 * @return	mixed		POST var named $var and if not set, the GET var of the same name.
	 * @see GPvar()
	 */
	function _GP($var)	{
		if(empty($var)) return;
		$value = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];
		if (isset($value))	{
			if (is_array($value))	{ t3lib_div::stripSlashesOnArray($value); } else { $value = stripslashes($value); }
		}
		return $value;
	}

	/**
	 * Returns the global GET array (or value from) normalized to contain un-escaped values.
	 * ALWAYS use this API function to acquire the GET variables!
	 * Usage: 27
	 *
	 * @param	string		Optional pointer to value in GET array (basically name of GET var)
	 * @return	mixed		If $var is set it returns the value of $_GET[$var]. If $var is NULL (default), returns $_GET itself. In any case *slashes are stipped from the output!*
	 * @see _POST(), _GP(), _GETset()
	 */
	function _GET($var=NULL)	{
		$value = ($var === NULL) ? $_GET : (empty($var) ? NULL : $_GET[$var]);
		if (isset($value))	{	// Removes slashes since TYPO3 has added them regardless of magic_quotes setting.
			if (is_array($value))	{ t3lib_div::stripSlashesOnArray($value); } else { $value = stripslashes($value); }
		}
		return $value;
	}

	/**
	 * Returns the global POST array (or value from) normalized to contain un-escaped values.
	 * ALWAYS use this API function to acquire the POST variables!
	 * Usage: 41
	 *
	 * @param	string		Optional pointer to value in POST array (basically name of POST var)
	 * @return	mixed		If $var is set it returns the value of $_POST[$var]. If $var is NULL (default), returns $_POST itself. In any case *slashes are stipped from the output!*
	 * @see _GET(), _GP()
	 */
	function _POST($var=NULL)	{
		$value = ($var === NULL) ? $_POST : (empty($var) ? NULL : $_POST[$var]);
		if (isset($value))	{	// Removes slashes since TYPO3 has added them regardless of magic_quotes setting.
			if (is_array($value))	{ t3lib_div::stripSlashesOnArray($value); } else { $value = stripslashes($value); }
		}
		return $value;
	}

	/**
	 * Writes input value to $_GET
	 * Usage: 2
	 *
	 * @param	array		Array to write to $_GET. Values should NOT be escaped at input time (but will be escaped before writing according to TYPO3 standards).
	 * @param	string		Alternative key; If set, this will not set the WHOLE GET array, but only the key in it specified by this value!
	 * @return	void
	 */
	function _GETset($inputGet,$key='')	{
			// ADDS slashes since TYPO3 standard currently is that slashes MUST be applied (regardless of magic_quotes setting).
		if (strcmp($key,''))	{
			if (is_array($inputGet))	{ t3lib_div::addSlashesOnArray($inputGet); } else { $inputGet = addslashes($inputGet); }
			$GLOBALS['HTTP_GET_VARS'][$key] = $_GET[$key] = $inputGet;
		} elseif (is_array($inputGet)){
			t3lib_div::addSlashesOnArray($inputGet);
			$GLOBALS['HTTP_GET_VARS'] = $_GET = $inputGet;
		}
	}

	/**
	 * GET/POST variable
	 * Returns the 'GLOBAL' value of incoming data from POST or GET, with priority to POST (that is equalent to 'GP' order)
	 * Strips slashes of string-outputs, but not arrays UNLESS $strip is set. If $strip is set all output will have escaped characters unescaped.
	 * Usage: 2
	 *
	 * @param	string		GET/POST var to return
	 * @param	boolean		If set, values are stripped of return values that are *arrays!* - string/integer values returned are always strip-slashed()
	 * @return	mixed		POST var named $var and if not set, the GET var of the same name.
	 * @deprecated		Use t3lib_div::_GP instead (ALWAYS delivers a value with un-escaped values!)
	 * @see _GP()
	 */
	function GPvar($var,$strip=0)	{
		if(empty($var)) return;
		$value = isset($_POST[$var]) ? $_POST[$var] : $_GET[$var];
		if (isset($value) && is_string($value))	{ $value = stripslashes($value); }	// Originally check '&& get_magic_quotes_gpc() ' but the values of $_GET are always slashed regardless of get_magic_quotes_gpc() because HTTP_POST/GET_VARS are run through addSlashesOnArray in the very beginning of index_ts.php eg.
		if ($strip && isset($value) && is_array($value)) { t3lib_div::stripSlashesOnArray($value); }
		return $value;
	}

	/**
	 * Returns the GET/POST global arrays merged with POST taking precedence.
	 * Usage: 1
	 *
	 * @param	string		Key (variable name) from GET or POST vars
	 * @return	array		Returns the GET vars merged recursively onto the POST vars.
	 * @ignore
	 * @deprecated
	 */
	function GParrayMerged($var)	{
		$postA = is_array($_POST[$var]) ? $_POST[$var] : array();
		$getA = is_array($_GET[$var]) ? $_GET[$var] : array();
		$mergedA = t3lib_div::array_merge_recursive_overrule($getA,$postA);
		t3lib_div::stripSlashesOnArray($mergedA);
		return $mergedA;
	}










	/*************************
	 *
	 * IMAGE FUNCTIONS
	 *
	 *************************/


	/**
	 * Compressing a GIF file if not already LZW compressed
	 * This function is a workaround for the fact that ImageMagick and/or GD does not compress GIF-files to their minimun size (that is RLE or no compression used)
	 *
	 * 		The function takes a file-reference, $theFile, and saves it again through GD or ImageMagick in order to compress the file
	 * 		GIF:
	 * 		If $type is not set, the compression is done with ImageMagick (provided that $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_path_lzw'] is pointing to the path of a lzw-enabled version of 'convert') else with GD (should be RLE-enabled!)
	 * 		If $type is set to either 'IM' or 'GD' the compression is done with ImageMagick and GD respectively
	 * 		PNG:
	 * 		No changes.
	 *
	 * 		$theFile is expected to be a valid GIF-file!
	 * 		The function returns a code for the operation.
	 * Usage: 9
	 *
	 * @param	string		Filepath
	 * @param	string		See description of function
	 * @return	string		Returns "GD" if GD was used, otherwise "IM" if ImageMagick was used. If nothing done at all, it returns empty string.
	 * @internal
	 */
	function gif_compress($theFile, $type)	{
		$gfxConf = $GLOBALS['TYPO3_CONF_VARS']['GFX'];
		$returnCode='';
		if ($gfxConf['gif_compress'] && strtolower(substr($theFile,-4,4))=='.gif')	{	// GIF...
			if (($type=='IM' || !$type) && $gfxConf['im'] && $gfxConf['im_path_lzw'])	{	// IM
				$cmd = t3lib_div::imageMagickCommand('convert', '"'.$theFile.'" "'.$theFile.'"', $gfxConf['im_path_lzw']);
				exec($cmd);

				$returnCode='IM';
			} elseif (($type=='GD' || !$type) && $gfxConf['gdlib'] && !$gfxConf['gdlib_png'])	{	// GD
				$tempImage = imageCreateFromGif($theFile);
				imageGif($tempImage, $theFile);
				imageDestroy($tempImage);
				$returnCode='GD';
			}
		}
		return $returnCode;
	}

	/**
	 * Converts a png file to gif
	 * This converts a png file to gif IF the FLAG $GLOBALS['TYPO3_CONF_VARS']['FE']['png_to_gif'] is set true.
	 * Usage: 5
	 *
	 * @param	string		$theFile	the filename with path
	 * @return	string		new filename
	 * @internal
	 */
	function png_to_gif_by_imagemagick($theFile)	{
		if ($GLOBALS['TYPO3_CONF_VARS']['FE']['png_to_gif']
			&& $GLOBALS['TYPO3_CONF_VARS']['GFX']['im']
			&& $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_path_lzw']
			&& strtolower(substr($theFile,-4,4))=='.png'
			&& @is_file($theFile))	{	// IM
				$newFile = substr($theFile,0,-4).'.gif';
				$cmd = t3lib_div::imageMagickCommand('convert', '"'.$theFile.'" "'.$newFile.'"', $gfxConf['im_path_lzw']);
				exec($cmd);
				$theFile = $newFile;
					// unlink old file?? May be bad idea bacause TYPO3 would then recreate the file every time as TYPO3 thinks the file is not generated because it's missing!! So do not unlink $theFile here!!
		}
		return $theFile;
	}

	/**
	 * Returns filename of the png/gif version of the input file (which can be png or gif).
	 * If input file type does not match the wanted output type a conversion is made and temp-filename returned.
	 * Usage: 2
	 *
	 * @param	string		Filepath of image file
	 * @param	boolean		If set, then input file is converted to PNG, otherwise to GIF
	 * @return	string		If the new image file exists, it's filepath is returned
	 * @internal
	 */
	function read_png_gif($theFile,$output_png=0)	{
		if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['im'] && @is_file($theFile))	{
			$ext = strtolower(substr($theFile,-4,4));
			if (
					((string)$ext=='.png' && $output_png)	||
					((string)$ext=='.gif' && !$output_png)
				)	{
				return $theFile;
			} else {
				$newFile = PATH_site.'typo3temp/readPG_'.md5($theFile.'|'.filemtime($theFile)).($output_png?'.png':'.gif');
				exec($GLOBALS['TYPO3_CONF_VARS']['GFX']['im_path'].'convert "'.$theFile.'" "'.$newFile.'"');
				if (@is_file($newFile))	return $newFile;
			}
		}
	}















	/*************************
	 *
	 * STRING FUNCTIONS
	 *
	 *************************/

	/**
	 * Truncate string
	 * Returns a new string of max. $chars length.
	 * If the string is longer, it will be truncated and appended with '...'.
	 * DEPRECIATED. Works ONLY for single-byte charsets! USE t3lib_div::fixed_lgd_cs() instead
	 * Usage: 39
	 *
	 * @param	string		$string 	string to truncate
	 * @param	integer		$chars 	must be an integer with an absolute value of at least 4. if negative the string is cropped from the right end.
	 * @param	string		String to append to the output if it is truncated, default is '...'
	 * @return	string		new string
	 * @see fixed_lgd_pre()
	 */
	function fixed_lgd($string,$origChars,$preStr='...')	{
		$chars = abs($origChars);
		if ($chars >= 4)	{
			if(strlen($string)>$chars)  {
				return $origChars < 0 ?
					$preStr.trim(substr($string, -($chars-3))) :
					trim(substr($string, 0, $chars-3)).$preStr;
			}
		}
		return $string;
	}

	/**
	 * Truncate string
	 * Returns a new string of max. $chars length.
	 * If the string is longer, it will be truncated and prepended with '...'.
	 * This works like fixed_lgd, but is truncated in the start of the string instead of the end
	 * DEPRECIATED. Use either fixed_lgd() or fixed_lgd_cs() (with negative input value for $chars)
	 * Usage: 6
	 *
	 * @param	string		$string 	string to truncate
	 * @param	integer		$chars 	must be an integer of at least 4
	 * @return	string		new string
	 * @see fixed_lgd()
	 */
	function fixed_lgd_pre($string,$chars)	{
		return strrev(t3lib_div::fixed_lgd(strrev($string),$chars));
	}

	/**
	 * Truncates a string with appended/prepended "..." and takes backend character set into consideration
	 * Use only from backend!
	 * Usage: 75
	 *
	 * @param	string		$string 	string to truncate
	 * @param	integer		$chars 	must be an integer with an absolute value of at least 4. if negative the string is cropped from the right end.
	 * @return	string		New string
	 * @see fixed_lgd()
	 */
	function fixed_lgd_cs($string,$chars)	{
		if (is_object($GLOBALS['LANG']))	{
			return $GLOBALS['LANG']->csConvObj->crop($GLOBALS['LANG']->charSet,$string,$chars,'...');
		} else {
			return t3lib_div::fixed_lgd($string, $chars);
		}
	}

	/**
	 * Breaks up the text for emails
	 * Usage: 1
	 *
	 * @param	string		The string to break up
	 * @param	string		The string to implode the broken lines with (default/typically \n)
	 * @param	integer		The line length
	 * @return	string
	 */
	function breakTextForEmail($str,$implChar="\n",$charWidth=76)	{
		$lines = explode(chr(10),$str);
		$outArr=array();
		while(list(,$lStr)=each($lines))	{
			$outArr = array_merge($outArr,t3lib_div::breakLinesForEmail($lStr,$implChar,$charWidth));
		}
		return implode(chr(10),$outArr);
	}

	/**
	 * Breaks up a single line of text for emails
	 * Usage: 5
	 *
	 * @param	string		The string to break up
	 * @param	string		The string to implode the broken lines with (default/typically \n)
	 * @param	integer		The line length
	 * @return	string
	 * @see breakTextForEmail()
	 */
	function breakLinesForEmail($str,$implChar="\n",$charWidth=76)	{
		$lines=array();
		$l=$charWidth;
		$p=0;
		while(strlen($str)>$p)	{
			$substr=substr($str,$p,$l);
			if (strlen($substr)==$l)	{
				$count = count(explode(' ',trim(strrev($substr))));
				if ($count>1)	{	// OK...
					$parts = explode(' ',strrev($substr),2);
					$theLine = strrev($parts[1]);
				} else {
					$afterParts = explode(' ',substr($str,$l+$p),2);
					$theLine = $substr.$afterParts[0];
				}
				if (!strlen($theLine))	{break; }	// Error, because this would keep us in an endless loop.
			} else {
				$theLine=$substr;
			}

			$lines[]=trim($theLine);
			$p+=strlen($theLine);
			if (!trim(substr($str,$p,$l)))	break;	// added...
		}
		return implode($implChar,$lines);
	}

	/**
	 * Match IP number with list of numbers with wildcard
	 * Usage: 10
	 *
	 * @param	string		$baseIP is the current remote IP address for instance, typ. REMOTE_ADDR
	 * @param	string		$list is a comma-list of IP-addresses to match with. *-wildcard allowed instead of number, plus leaving out parts in the IP number is accepted as wildcard (eg. 192.168.*.* equals 192.168)
	 * @return	boolean		True if an IP-mask from $list matches $baseIP
	 */
	function cmpIP($baseIP, $list)	{
		$IPpartsReq = explode('.',$baseIP);
		if (count($IPpartsReq)==4)	{
			$values = t3lib_div::trimExplode(',',$list,1);

			foreach($values as $test)	{
				list($test,$mask) = explode('/',$test);

				if(intval($mask)) {
						// "192.168.3.0/24"
					$lnet = ip2long($test);
					$lip = ip2long($baseIP);
					$binnet = str_pad( decbin($lnet),32,'0','STR_PAD_LEFT');
					$firstpart = substr($binnet,0,$mask);
					$binip = str_pad( decbin($lip),32,'0','STR_PAD_LEFT');
					$firstip = substr($binip,0,$mask);
					$yes = (strcmp($firstpart,$firstip)==0);
				} else {
						// "192.168.*.*"
					$IPparts = explode('.',$test);
					$yes = 1;
					reset($IPparts);
					while(list($index,$val)=each($IPparts))	{
						$val = trim($val);
						if (strcmp($val,'*') && strcmp($IPpartsReq[$index],$val))	{
							$yes=0;
						}
					}
				}
				if ($yes) return true;
			}
		}
		return false;
	}

	/**
	 * Match fully qualified domain name with list of strings with wildcard
	 *
	 * @param	string		$baseIP is the current remote IP address for instance, typ. REMOTE_ADDR
	 * @param	string		$list is a comma-list of domain names to match with. *-wildcard allowed but cannot be part of a string, so it must match the full host name (eg. myhost.*.com => correct, myhost.*domain.com => wrong)
	 * @return	boolean		True if a domain name mask from $list matches $baseIP
	 */
	function cmpFQDN($baseIP, $list)        {
		if (count(explode('.',$baseIP))==4)     {
			$resolvedHostName = explode('.', gethostbyaddr($baseIP));
			$values = t3lib_div::trimExplode(',',$list,1);

			foreach($values as $test)	{
				$hostNameParts = explode('.',$test);
				$yes = 1;

				foreach($hostNameParts as $index => $val)	{
					$val = trim($val);
					if (strcmp($val,'*') && strcmp($resolvedHostName[$index],$val)) {
						$yes=0;
					}
				}
				if ($yes) return true;
			}
		}
		return false;
	}

	/**
	 * Check for item in list
	 * Check if an item exists in a comma-separated list of items.
	 * Usage: 163
	 *
	 * @param	string		comma-separated list of items (string)
	 * @param	string		item to check for
	 * @return	boolean		true if $item is in $list
	 */
	function inList($list,$item)	{
		return strstr(','.$list.',', ','.$item.',') ? true : false;
	}

	/**
	 * Removes an item from a comma-separated list of items.
	 * Usage: 1
	 *
	 * @param	string		$element  	element to remove
	 * @param	string		$list 	comma-separated list of items (string)
	 * @return	string		new comma-separated list of items
	 */
	function rmFromList($element,$list)	{
		$items = explode(',',$list);
		while(list($k,$v)=each($items))	{
			if ($v==$element)	{unset($items[$k]);}
		}
		return implode(',',$items);
	}

	/**
	 * Expand a comma-separated list of integers with ranges (eg 1,3-5,7 becomes 1,3,4,5,7).
	 * Ranges are limited to 1000 values per range.
	 *
	 * @param	string		$list 	comma-separated list of integers with ranges (string)
	 * @return	string		new comma-separated list of items
	 * @author	Martin Kutschker <martin.kutschker@activesolution.at>
	 */
	function expandList($list)      {
		$items = explode(',',$list);
		$list = array();
		while(list(,$item)=each($items))	{
			$range = explode('-',$item);
			if (isset($range[1]))	{
				$runAwayBrake = 1000;
				for ($n=$range[0]; $n<=$range[1]; $n++)	{
					$list[] = $n;

					$runAwayBrake--;
					if ($runAwayBrake<=0)	break;
				}
			} else {
				$list[] = $item;
			}
		}

		return implode(',',$list);
	}

	/**
	 * Forces the integer $theInt into the boundaries of $min and $max. If the $theInt is 'false' then the $zeroValue is applied.
	 * Usage: 224
	 *
	 * @param	integer		Input value
	 * @param	integer		Lower limit
	 * @param	integer		Higher limit
	 * @param	integer		Default value if input is false.
	 * @return	integer		The input value forced into the boundaries of $min and $max
	 */
	function intInRange($theInt,$min,$max=2000000000,$zeroValue=0)	{
		// Returns $theInt as an integer in the integerspace from $min to $max
		$theInt = intval($theInt);
		if ($zeroValue && !$theInt)	{$theInt=$zeroValue;}	// If the input value is zero after being converted to integer, zeroValue may set another default value for it.
		if ($theInt<$min){$theInt=$min;}
		if ($theInt>$max){$theInt=$max;}
		return $theInt;
	}

	/**
	 * Returns the $integer if greater than zero, otherwise returns zero.
	 * Usage: 1
	 *
	 * @param	integer		Integer string to process
	 * @return	integer
	 */
	function intval_positive($theInt)	{
		$theInt = intval($theInt);
		if ($theInt<0){$theInt=0;}
		return $theInt;
	}

	/**
	 * Returns an integer from a three part version number, eg '4.12.3' -> 4012003
	 * Usage: 2
	 *
	 * @param	string		Version number on format x.x.x
	 * @return	integer		Integer version of version number (where each part can count to 999)
	 */
	function int_from_ver($verNumberStr)	{
		$verParts = explode('.',$verNumberStr);
		return intval((int)$verParts[0].str_pad((int)$verParts[1],3,'0',STR_PAD_LEFT).str_pad((int)$verParts[2],3,'0',STR_PAD_LEFT));
	}

	/**
	 * Makes a positive integer hash out of the first 7 chars from the md5 hash of the input
	 * Usage: 5
	 *
	 * @param	string		String to md5-hash
	 * @return	integer		Returns 28bit integer-hash
	 */
	function md5int($str)	{
		return hexdec(substr(md5($str),0,7));
	}

	/**
	 * Returns the first 10 positions of the MD5-hash		(changed from 6 to 10 recently)
	 *
	 * Usage: 37
	 *
	 * @param	string		Input string to be md5-hashed
	 * @param	integer		The string-length of the output
	 * @return	string		Substring of the resulting md5-hash, being $len chars long (from beginning)
	 */
	function shortMD5($input, $len=10)	{
		return substr(md5($input),0,$len);
	}

	/**
	 * Takes comma-separated lists and arrays and removes all duplicates
	 * If a value in the list is trim(empty), the value is ignored.
	 * Usage: 16
	 *
	 * @param	string		Accept multiple parameters wich can be comma-separated lists of values and arrays.
	 * @param	mixed		$secondParameter: Dummy field, which is set will show a warning!
	 * @return	string		Returns the list without any duplicates of values, space around values are trimmed
	 */
	function uniqueList($in_list, $secondParameter=NULL)	{
		if (is_array($in_list))	die('t3lib_div::uniqueList() does NOT support array arguments anymore! Only string comma lists!');
		if (isset($secondParameter))	die('t3lib_div::uniqueList() does NOT support more than a single argument value anymore. You have specified more than one.');

		return implode(',',array_unique(t3lib_div::trimExplode(',',$in_list,1)));
	}

	/**
	 * Splits a reference to a file in 5 parts
	 * Usage: 43
	 *
	 * @param	string		Filename/filepath to be analysed
	 * @return	array		Contains keys [path], [file], [filebody], [fileext], [realFileext]
	 */
	function split_fileref($fileref)	{
		if (	ereg('(.*/)(.*)$',$fileref,$reg)	)	{
			$info['path'] = $reg[1];
			$info['file'] = $reg[2];
		} else {
			$info['path'] = '';
			$info['file'] = $fileref;
		}
		$reg='';
		if (	ereg('(.*)\.([^\.]*$)',$info['file'],$reg)	)	{
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
	 * Usage: 5
	 *
	 * @param	string		Directory name / path
	 * @return	string		Processed input value. See function description.
	 */
	function dirname($path)	{
		$p=t3lib_div::revExplode('/',$path,2);
		return count($p)==2?$p[0]:'';
	}

	/**
	 * Modifies a HTML Hex color by adding/subtracting $R,$G and $B integers
	 * Usage: 11
	 *
	 * @param	string		A hexadecimal color code, #xxxxxx
	 * @param	integer		Offset value 0-255
	 * @param	integer		Offset value 0-255
	 * @param	integer		Offset value 0-255
	 * @return	string		A hexadecimal color code, #xxxxxx, modified according to input vars
	 * @see modifyHTMLColorAll()
	 */
	function modifyHTMLColor($color,$R,$G,$B)	{
		// This takes a hex-color (# included!) and adds $R, $G and $B to the HTML-color (format: #xxxxxx) and returns the new color
		$nR = t3lib_div::intInRange(hexdec(substr($color,1,2))+$R,0,255);
		$nG = t3lib_div::intInRange(hexdec(substr($color,3,2))+$G,0,255);
		$nB = t3lib_div::intInRange(hexdec(substr($color,5,2))+$B,0,255);
		return '#'.
			substr('0'.dechex($nR),-2).
			substr('0'.dechex($nG),-2).
			substr('0'.dechex($nB),-2);
	}

	/**
	 * Modifies a HTML Hex color by adding/subtracting $all integer from all R/G/B channels
	 * Usage: 6
	 *
	 * @param	string		A hexadecimal color code, #xxxxxx
	 * @param	integer		Offset value 0-255 for all three channels.
	 * @return	string		A hexadecimal color code, #xxxxxx, modified according to input vars
	 * @see modifyHTMLColor()
	 */
	function modifyHTMLColorAll($color,$all)	{
		return t3lib_div::modifyHTMLColor($color,$all,$all,$all);
	}

	/**
	 * Removes comma (if present) in the end of string
	 * Usage: 2
	 *
	 * @param	string		String from which the comma in the end (if any) will be removed.
	 * @return	string
	 */
	function rm_endcomma($string)	{
		return ereg_replace(',$','',$string);
	}

	/**
	 * strtoupper which converts danish (and other characters) characters as well
	 * (DEPRECIATED, use t3lib_cs::conv_case() instead or for HTML output, wrap your content in <span class="uppercase">...</span>)
	 * Usage: 0
	 *
	 * @param	string		String to process
	 * @return	string
	 * @ignore
	 */
	function danish_strtoupper($string)	{
		$value = strtoupper($string);
		return strtr($value, 'áéúíâêûôîæøåäöü', 'ÁÉÚÍÄËÜÖÏÆØÅÄÖÜ');
	}

	/**
	 * Change umlaut characters to plain ASCII with normally two character target
	 * Only known characters will be converted, so don't expect a result for any character.
	 * (DEPRECIATED: Works only for western europe single-byte charsets! Use t3lib_cs::specCharsToASCII() instead!)
	 *
	 * ä => ae, Ö => Oe
	 *
	 * @param	string		String to convert.
	 * @return	string
	 */
	function convUmlauts($str)	{
		$pat  = array (	'/ä/',	'/Ä/',	'/ö/',	'/Ö/',	'/ü/',	'/Ü/',	'/ß/',	'/å/',	'/Å/',	'/ø/',	'/Ø/',	'/æ/',	'/Æ/'	);
		$repl = array (	'ae',	'Ae',	'oe',	'Oe',	'ue',	'Ue',	'ss',	'aa',	'AA',	'oe',	'OE',	'ae',	'AE'	);
		return preg_replace($pat,$repl,$str);
	}

	/**
	 * Tests if the input is an integer.
	 * Usage: 77
	 *
	 * @param	mixed		Any input variable to test.
	 * @return	boolean		Returns true if string is an integer
	 */
	function testInt($var)	{
		return !strcmp($var,intval($var));
	}

	/**
	 * Returns true if the first part of $str matches the string $partStr
	 * Usage: 59
	 *
	 * @param	string		Full string to check
	 * @param	string		Reference string which must be found as the "first part" of the full string
	 * @return	boolean		True if $partStr was found to be equal to the first part of $str
	 */
	function isFirstPartOfStr($str,$partStr)	{
		// Returns true, if the first part of a $str equals $partStr and $partStr is not ''
		$psLen = strlen($partStr);
		if ($psLen)	{
			return substr($str,0,$psLen)==(string)$partStr;
		} else return false;
	}

	/**
	 * Formats the input integer $sizeInBytes as bytes/kilobytes/megabytes (-/K/M)
	 * Usage: 53
	 *
	 * @param	integer		Number of bytes to format.
	 * @param	string		Labels for bytes, kilo, mega and giga separated by vertical bar (|) and possibly encapsulated in "". Eg: " | K| M| G" (which is the default value)
	 * @return	string		Formatted representation of the byte number, for output.
	 */
	function formatSize($sizeInBytes,$labels='')	{

			// Set labels:
		if (strlen($labels) == 0) {
		    $labels = ' | K| M| G';
		} else {
		    $labels = str_replace('"','',$labels);
		}
		$labelArr = explode('|',$labels);

			// Find size:
		if ($sizeInBytes>900)	{
			if ($sizeInBytes>900000000)	{	// GB
				$val = $sizeInBytes/(1024*1024*1024);
				return number_format($val, (($val<20)?1:0), '.', '').$labelArr[3];
			}
			elseif ($sizeInBytes>900000)	{	// MB
				$val = $sizeInBytes/(1024*1024);
				return number_format($val, (($val<20)?1:0), '.', '').$labelArr[2];
			} else {	// KB
				$val = $sizeInBytes/(1024);
				return number_format($val, (($val<20)?1:0), '.', '').$labelArr[1];
			}
		} else {	// Bytes
			return $sizeInBytes.$labelArr[0];
		}
	}

	/**
	 * Returns microtime input to milliseconds
	 * Usage: 2
	 *
	 * @param	string		Microtime
	 * @return	integer		Microtime input string converted to an integer (milliseconds)
	 */
	function convertMicrotime($microtime)	{
		$parts = explode(' ',$microtime);
		return round(($parts[0]+$parts[1])*1000);
	}

	/**
	 * This splits a string by the chars in $operators (typical /+-*) and returns an array with them in
	 * Usage: 2
	 *
	 * @param	string		Input string, eg "123 + 456 / 789 - 4"
	 * @param	string		Operators to split by, typically "/+-*"
	 * @return	array		Array with operators and operands separated.
	 * @see tslib_cObj::calc(), tslib_gifBuilder::calcOffset()
	 */
	function splitCalc($string,$operators)	{
		$res = Array();
		$sign='+';
		while($string)	{
			$valueLen=strcspn($string,$operators);
			$value=substr($string,0,$valueLen);
			$res[] = Array($sign,trim($value));
			$sign=substr($string,$valueLen,1);
			$string=substr($string,$valueLen+1);
		}
		reset($res);
		return $res;
	}

	/**
	 * Calculates the input by +,-,*,/,%,^ with priority to + and -
	 * Usage: 1
	 *
	 * @param	string		Input string, eg "123 + 456 / 789 - 4"
	 * @return	integer		Calculated value. Or error string.
	 * @see calcParenthesis()
	 */
	function calcPriority($string)	{
		$string=ereg_replace('[[:space:]]*','',$string);	// removing all whitespace
		$string='+'.$string;	// Ensuring an operator for the first entrance
		$qm='\*\/\+-^%';
		$regex = '(['.$qm.'])(['.$qm.']?[0-9\.]*)';
			// split the expression here:
		preg_match_all('/'.$regex.'/',$string,$reg);

		reset($reg[2]);
		$number=0;
		$Msign='+';
		$err='';
		$buffer=doubleval(current($reg[2]));
		next($reg[2]);	// Advance pointer
		while(list($k,$v)=each($reg[2]))	{
			$v=doubleval($v);
			$sign = $reg[1][$k];
			if ($sign=='+' || $sign=='-')	{
				$number = $Msign=='-' ? $number-=$buffer : $number+=$buffer;
				$Msign = $sign;
				$buffer=$v;
			} else {
				if ($sign=='/')	{if ($v) $buffer/=$v; else $err='dividing by zero';}
				if ($sign=='%')	{if ($v) $buffer%=$v; else $err='dividing by zero';}
				if ($sign=='*')	{$buffer*=$v;}
				if ($sign=='^')	{$buffer=pow($buffer,$v);}
			}
		}
		$number = $Msign=='-' ? $number-=$buffer : $number+=$buffer;
		return $err ? 'ERROR: '.$err : $number;
	}

	/**
	 * Calculates the input with parenthesis levels
	 * Usage: 2
	 *
	 * @param	string		Input string, eg "(123 + 456) / 789 - 4"
	 * @return	integer		Calculated value. Or error string.
	 * @see calcPriority(), tslib_cObj::stdWrap()
	 */
	function calcParenthesis($string)	{
		$securC=100;
		do {
			$valueLenO=strcspn($string,'(');
			$valueLenC=strcspn($string,')');
			if ($valueLenC==strlen($string) || $valueLenC < $valueLenO)	{
				$value = t3lib_div::calcPriority(substr($string,0,$valueLenC));
				$string = $value.substr($string,$valueLenC+1);
				return $string;
			} else {
				$string = substr($string,0,$valueLenO).t3lib_div::calcParenthesis(substr($string,$valueLenO+1));
			}
				// Security:
			$securC--;
			if ($securC<=0)	break;
		} while($valueLenO<strlen($string));
		return $string;
	}

	/**
	 * Inverse version of htmlspecialchars()
	 * Usage: 4
	 *
	 * @param	string		Value where &gt;, &lt;, &quot; and &amp; should be converted to regular chars.
	 * @return	string		Converted result.
	 */
	function htmlspecialchars_decode($value)	{
		$value = str_replace('&gt;','>',$value);
		$value = str_replace('&lt;','<',$value);
		$value = str_replace('&quot;','"',$value);
		$value = str_replace('&amp;','&',$value);
		return $value;
	}

	/**
	 * Re-converts HTML entities if they have been converted by htmlspecialchars()
	 * Usage: 10
	 *
	 * @param	string		String which contains eg. "&amp;amp;" which should stay "&amp;". Or "&amp;#1234;" to "&#1234;". Or "&amp;#x1b;" to "&#x1b;"
	 * @return	string		Converted result.
	 */
	function deHSCentities($str)	{
		return ereg_replace('&amp;([#[:alnum:]]*;)','&\1',$str);
	}

	/**
	 * This function is used to escape any ' -characters when transferring text to JavaScript!
	 * Usage: 3
	 *
	 * @param	string		String to escape
	 * @param	boolean		If set, also backslashes are escaped.
	 * @param	string		The character to escape, default is ' (single-quote)
	 * @return	string		Processed input string
	 */
	function slashJS($string,$extended=0,$char="'")	{
		if ($extended)	{$string = str_replace ("\\", "\\\\", $string);}
		return str_replace ($char, "\\".$char, $string);
	}

	/**
	 * Version of rawurlencode() where all spaces (%20) are re-converted to space-characters.
	 * Usefull when passing text to JavaScript where you simply url-encode it to get around problems with syntax-errors, linebreaks etc.
	 * Usage: 4
	 *
	 * @param	string		String to raw-url-encode with spaces preserved
	 * @return	string		Rawurlencoded result of input string, but with all %20 (space chars) converted to real spaces.
	 */
	function rawUrlEncodeJS($str)	{
		return str_replace('%20',' ',rawurlencode($str));
	}

	/**
	 * rawurlencode which preserves "/" chars
	 * Usefull when filepaths should keep the "/" chars, but have all other special chars encoded.
	 * Usage: 5
	 *
	 * @param	string		Input string
	 * @return	string		Output string
	 */
	function rawUrlEncodeFP($str)	{
		return str_replace('%2F','/',rawurlencode($str));
	}

	/**
	 * Checking syntax of input email address
	 * Usage: 5
	 *
	 * @param	string		Input string to evaluate
	 * @return	boolean		Returns true if the $email address (input string) is valid; Has a "@", domain name with at least one period and only allowed a-z characters.
	 */
	function validEmail($email)	{
		$email = trim ($email);
		if (strstr($email,' '))	 return FALSE;
		return ereg('^[A-Za-z0-9\._-]+[@][A-Za-z0-9\._-]+[\.].[A-Za-z0-9]+$',$email) ? TRUE : FALSE;
	}

	/**
	 * Formats a string for output between <textarea>-tags
	 * All content outputted in a textarea form should be passed through this function
	 * Not only is the content htmlspecialchar'ed on output but there is also a single newline added in the top. The newline is necessary because browsers will ignore the first newline after <textarea> if that is the first character. Therefore better set it!
	 * Usage: 23
	 *
	 * @param	string		Input string to be formatted.
	 * @return	string		Formatted for <textarea>-tags
	 */
	function formatForTextarea($content)	{
		return chr(10).htmlspecialchars($content);
	}












	/*************************
	 *
	 * ARRAY FUNCTIONS
	 *
	 *************************/

	/**
	 * Check if an item exists in an array
	 * Please note that the order of parameters is reverse compared to the php4-function in_array()!!!
	 * Usage: 3
	 *
	 * @param	array		$in_array		one-dimensional array of items
	 * @param	string		$item 	item to check for
	 * @return	boolean		true if $item is in the one-dimensional array $in_array
	 * @internal
	 */
	function inArray($in_array,$item)	{
		if (is_array($in_array))	{
			while (list(,$val)=each($in_array))	{
				if (!is_array($val) && !strcmp($val,$item)) return true;
			}
		}
	}

	/**
	 * Explodes a $string delimited by $delim and passes each item in the array through intval().
	 * Corresponds to explode(), but with conversion to integers for all values.
	 * Usage: 76
	 *
	 * @param	string		Delimiter string to explode with
	 * @param	string		The string to explode
	 * @return	array		Exploded values, all converted to integers
	 */
	function intExplode($delim, $string)	{
		$temp = explode($delim,$string);
		while(list($key,$val)=each($temp))	{
			$temp[$key]=intval($val);
		}
		reset($temp);
		return $temp;
	}

	/**
	 * Reverse explode which explodes the string counting from behind.
	 * Thus t3lib_div::revExplode(':','my:words:here',2) will return array('my:words','here')
	 * Usage: 8
	 *
	 * @param	string		Delimiter string to explode with
	 * @param	string		The string to explode
	 * @param	integer		Number of array entries
	 * @return	array		Exploded values
	 */
	function revExplode($delim, $string, $count=0)	{
		$temp = explode($delim,strrev($string),$count);
		while(list($key,$val)=each($temp))	{
			$temp[$key]=strrev($val);
		}
		$temp=array_reverse($temp);
		reset($temp);
		return $temp;
	}

	/**
	 * Explodes a string and trims all values for whitespace in the ends.
	 * If $onlyNonEmptyValues is set, then all blank ('') values are removed.
	 * Usage: 256
	 *
	 * @param	string		Delimiter string to explode with
	 * @param	string		The string to explode
	 * @param	boolean		If set, all empty values (='') will NOT be set in output
	 * @return	array		Exploded values
	 */
	function trimExplode($delim, $string, $onlyNonEmptyValues=0)	{
		$temp = explode($delim,$string);
		$newtemp=array();
		while(list($key,$val)=each($temp))	{
			if (!$onlyNonEmptyValues || strcmp('',trim($val)))	{
				$newtemp[]=trim($val);
			}
		}
		reset($newtemp);
		return $newtemp;
	}

	/**
	 * Remove duplicate values from an array
	 * This function is deprecated, use the PHP function array_unique instead
	 * Usage: 0
	 *
	 * @param	array		Array of values to make unique
	 * @return	array
	 * @ignore
	 * @deprecated
	 */
	function uniqueArray($valueArray)	{
		return array_unique($valueArray);
	}

	/**
	 * Removes the value $cmpValue from the $array if found there. Returns the modified array
	 * Usage: 3
	 *
	 * @param	array		Array containing the values
	 * @param	string		Value to search for and if found remove array entry where found.
	 * @return	array		Output array with entries removed if search string is found
	 */
	function removeArrayEntryByValue($array,$cmpValue)	{
		if (is_array($array))	{
			reset($array);
			while(list($k,$v)=each($array))	{
				if (is_array($v))	{
					$array[$k] = t3lib_div::removeArrayEntryByValue($v,$cmpValue);
				} else {
					if (!strcmp($v,$cmpValue))	{
						unset($array[$k]);
					}
				}
			}
		}
		reset($array);
		return $array;
	}

	/**
	 * Implodes a multidim-array into GET-parameters (eg. &param[key][key2]=value2&param[key][key3]=value3)
	 * Usage: 24
	 *
	 * @param	string		Name prefix for entries. Set to blank if you wish none.
	 * @param	array		The (multidim) array to implode
	 * @param	string		(keep blank)
	 * @param	boolean		If set, parameters which were blank strings would be removed.
	 * @param	boolean		If set, the param name itself (for example "param[key][key2]") would be rawurlencoded as well.
	 * @return	string		Imploded result, fx. &param[key][key2]=value2&param[key][key3]=value3
	 * @see explodeUrl2Array()
	 */
	function implodeArrayForUrl($name,$theArray,$str='',$skipBlank=0,$rawurlencodeParamName=0)	{
		if (is_array($theArray))	{
			foreach($theArray as $Akey => $AVal)	{
				$thisKeyName = $name ? $name.'['.$Akey.']' : $Akey;
				if (is_array($AVal))	{
					$str = t3lib_div::implodeArrayForUrl($thisKeyName,$AVal,$str,$skipBlank,$rawurlencodeParamName);
				} else {
					if (!$skipBlank || strcmp($AVal,''))	{
						$str.='&'.($rawurlencodeParamName ? rawurlencode($thisKeyName) : $thisKeyName).
							'='.rawurlencode($AVal);
					}
				}
			}
		}
		return $str;
	}

	/**
	 * Explodes a string with GETvars (eg. "&id=1&type=2&ext[mykey]=3") into an array
	 *
	 * @param	string		GETvars string
	 * @param	boolean		If set, the string will be parsed into a multidimensional array if square brackets are used in variable names (using PHP function parse_str())
	 * @return	array		Array of values. All values AND keys are rawurldecoded() as they properly should be. But this means that any implosion of the array again must rawurlencode it!
	 * @see implodeArrayForUrl()
	 */
	function explodeUrl2Array($string,$multidim=FALSE)	{
		if ($multidim)	{
			parse_str($string,$tempGetVars);
			return $tempGetVars;
		} else {
			$output = array();
			$p = explode('&',$string);
			foreach($p as $v)	{
				if (strlen($v))	{
					list($pK,$pV) = explode('=',$v,2);
					$output[rawurldecode($pK)] = rawurldecode($pV);
				}
			}
			return $output;
		}
	}

	/**
	 * Returns an array with selected keys from incoming data.
	 * (Better read source code if you want to find out...)
	 * Usage: 3
	 *
	 * @param	string		List of variable/key names
	 * @param	array		Array from where to get values based on the keys in $varList
	 * @param	boolean		If set, then t3lib_div::_GP() is used to fetch the value if not found (isset) in the $getArray
	 * @return	array		Output array with selected variables.
	 */
	function compileSelectedGetVarsFromArray($varList,$getArray,$GPvarAlt=1)	{
		$keys = t3lib_div::trimExplode(',',$varList,1);
		$outArr=array();
		foreach($keys as $v)	{
			if (isset($getArray[$v]))	{
				$outArr[$v]=$getArray[$v];
			} elseif ($GPvarAlt) {
				$outArr[$v]=t3lib_div::_GP($v);
			}
		}
		return $outArr;
	}

	/**
	 * AddSlash array
	 * This function traverses a multidimentional array and adds slashes to the values.
	 * NOTE that the input array is and argument by reference.!!
	 * Twin-function to stripSlashesOnArray
	 * Usage: 8
	 *
	 * @param	array		Multidimensional input array, (REFERENCE!)
	 * @return	array
	 */
	function addSlashesOnArray(&$theArray)	{
		if (is_array($theArray))	{
			reset($theArray);
			while(list($Akey,$AVal)=each($theArray))	{
				if (is_array($AVal))	{
					t3lib_div::addSlashesOnArray($theArray[$Akey]);
				} else {
					$theArray[$Akey] = addslashes($AVal);
				}
			}
			reset($theArray);
		}
	}

	/**
	 * StripSlash array
	 * This function traverses a multidimentional array and strips slashes to the values.
	 * NOTE that the input array is and argument by reference.!!
	 * Twin-function to addSlashesOnArray
	 * Usage: 10
	 *
	 * @param	array		Multidimensional input array, (REFERENCE!)
	 * @return	array
	 */
	function stripSlashesOnArray(&$theArray)	{
		if (is_array($theArray))	{
			reset($theArray);
			while(list($Akey,$AVal)=each($theArray))	{
				if (is_array($AVal))	{
					t3lib_div::stripSlashesOnArray($theArray[$Akey]);
				} else {
					$theArray[$Akey] = stripslashes($AVal);
				}
			}
			reset($theArray);
		}
	}

	/**
	 * Either slashes ($cmd=add) or strips ($cmd=strip) array $arr depending on $cmd
	 * Usage: 0
	 *
	 * @param	array		Multidimensional input array
	 * @param	string		"add" or "strip", depending on usage you wish.
	 * @return	array
	 */
	function slashArray($arr,$cmd)	{
		if ($cmd=='strip')	t3lib_div::stripSlashesOnArray($arr);
		if ($cmd=='add')	t3lib_div::addSlashesOnArray($arr);
		return $arr;
	}

	/**
	 * Merges two arrays recursively and "binary safe" (integer keys are overridden as well), overruling similar values in the first array ($arr0) with the values of the second array ($arr1)
	 * In case of identical keys, ie. keeping the values of the second.
	 * Usage: 0
	 *
	 * @param	array		First array
	 * @param	array		Second array, overruling the first array
	 * @param	boolean		If set, keys that are NOT found in $arr0 (first array) will not be set. Thus only existing value can/will be overruled from second array.
	 * @param	boolean		If set, values from $arr1 will overrule if they are empty. Default: true
	 * @return	array		Resulting array where $arr1 values has overruled $arr0 values
	 */
	function array_merge_recursive_overrule($arr0,$arr1,$notAddKeys=0,$includeEmtpyValues=true) {
		reset($arr1);
		while(list($key,$val) = each($arr1)) {
			if(is_array($arr0[$key])) {
				if (is_array($arr1[$key]))	{
					$arr0[$key] = t3lib_div::array_merge_recursive_overrule($arr0[$key],$arr1[$key],$notAddKeys);
				}
			} else {
				if ($notAddKeys) {
					if (isset($arr0[$key])) {
						if ($includeEmtpyValues OR $val) {
							$arr0[$key] = $val;
						}
					}
				} else {
					if ($includeEmtpyValues OR $val) {
						$arr0[$key] = $val;
					}
				}
			}
		}
		reset($arr0);
		return $arr0;
	}

	/**
	 * An array_merge function where the keys are NOT renumbered as they happen to be with the real php-array_merge function. It is "binary safe" in the sense that integer keys are overridden as well.
	 * Usage: 16
	 *
	 * @param	array		First array
	 * @param	array		Second array
	 * @return	array		Merged result.
	 */
	function array_merge($arr1,$arr2)	{
		return $arr2+$arr1;
	}

	/**
	 * Takes a row and returns a CSV string of the values with $delim (default is ,) and $quote (default is ") as separator chars.
	 * Usage: 5
	 *
	 * @param	array		Input array of values
	 * @param	string		Delimited, default is comman
	 * @param	string		Quote-character to wrap around the values.
	 * @return	string		A single line of CSV
	 */
	function csvValues($row,$delim=',',$quote='"')	{
		reset($row);
		$out=array();
		while(list(,$value)=each($row))	{
			list($valPart) = explode(chr(10),$value);
			$valPart = trim($valPart);
			$out[]=str_replace($quote,$quote.$quote,$valPart);
		}
		$str = $quote.implode($quote.$delim.$quote,$out).$quote;
		return $str;
	}
















	/*************************
	 *
	 * HTML/XML PROCESSING
	 *
	 *************************/

	/**
	 * Returns an array with all attributes of the input HTML tag as key/value pairs. Attributes are only lowercase a-z
	 * $tag is either a whole tag (eg '<TAG OPTION ATTRIB=VALUE>') or the parameterlist (ex ' OPTION ATTRIB=VALUE>')
	 * If a attribute is empty (I call it 'an option'), then the value for the key is empty. You can check if it existed with isset()
	 * Usage: 8
	 *
	 * @param	string		HTML-tag string (or attributes only)
	 * @return	array		Array with the attribute values.
	 */
	function get_tag_attributes($tag)	{
		$components = t3lib_div::split_tag_attributes($tag);
		$name = '';	 // attribute name is stored here
		$valuemode = '';
		if (is_array($components))	{
			while (list($key,$val) = each ($components))	{
				if ($val != '=')	{	// Only if $name is set (if there is an attribute, that waits for a value), that valuemode is enabled. This ensures that the attribute is assigned it's value
					if ($valuemode)	{
						if ($name)	{
							$attributes[$name] = $val;
							$name = '';
						}
					} else {
						if ($key = strtolower(ereg_replace('[^a-zA-Z0-9]','',$val)))	{
							$attributes[$key] = '';
							$name = $key;
						}
					}
					$valuemode = '';
				} else {
					$valuemode = 'on';
				}
			}
			if (is_array($attributes))	reset($attributes);
			return $attributes;
		}
	}

	/**
	 * Returns an array with the 'components' from an attribute list from an HTML tag. The result is normally analyzed by get_tag_attributes
	 * Removes tag-name if found
	 * Usage: 2
	 *
	 * @param	string		HTML-tag string (or attributes only)
	 * @return	array		Array with the attribute values.
	 * @internal
	 */
	function split_tag_attributes($tag)	{
		$tag_tmp = trim(eregi_replace ('^<[^[:space:]]*','',trim($tag)));
			// Removes any > in the end of the string
		$tag_tmp = trim(eregi_replace ('>$','',$tag_tmp));

		while (strcmp($tag_tmp,''))	{	// Compared with empty string instead , 030102
			$firstChar=substr($tag_tmp,0,1);
			if (!strcmp($firstChar,'"') || !strcmp($firstChar,"'"))	{
				$reg=explode($firstChar,$tag_tmp,3);
				$value[]=$reg[1];
				$tag_tmp=trim($reg[2]);
			} elseif (!strcmp($firstChar,'=')) {
				$value[] = '=';
				$tag_tmp = trim(substr($tag_tmp,1));		// Removes = chars.
			} else {
					// There are '' around the value. We look for the next ' ' or '>'
				$reg = split('[[:space:]=]',$tag_tmp,2);
				$value[] = trim($reg[0]);
				$tag_tmp = trim(substr($tag_tmp,strlen($reg[0]),1).$reg[1]);
			}
		}
		if (is_array($value))	reset($value);
		return $value;
	}

	/**
	 * Implodes attributes in the array $arr for an attribute list in eg. and HTML tag (with quotes)
	 * Usage: 14
	 *
	 * @param	array		Array with attribute key/value pairs, eg. "bgcolor"=>"red", "border"=>0
	 * @param	boolean		If set the resulting attribute list will have a) all attributes in lowercase (and duplicates weeded out, first entry taking precedence) and b) all values htmlspecialchar()'ed. It is recommended to use this switch!
	 * @param	boolean		If true, don't check if values are blank. Default is to omit attributes with blank values.
	 * @return	string		Imploded attributes, eg. 'bgcolor="red" border="0"'
	 */
	function implodeAttributes($arr,$xhtmlSafe=FALSE,$dontOmitBlankAttribs=FALSE)	{
		if (is_array($arr))	{
			if ($xhtmlSafe)	{
				$newArr=array();
				foreach($arr as $p => $v)	{
					if (!isset($newArr[strtolower($p)])) $newArr[strtolower($p)] = htmlspecialchars($v);
				}
				$arr = $newArr;
			}
			$list = array();
			foreach($arr as $p => $v)	{
				if (strcmp($v,'') || $dontOmitBlankAttribs)	{$list[]=$p.'="'.$v.'"';}
			}
			return implode(' ',$list);
		}
	}

	/**
	 * Implodes attributes in the array $arr for an attribute list in eg. and HTML tag (with quotes)
	 *
	 * @param	array		See implodeAttributes()
	 * @param	boolean		See implodeAttributes()
	 * @param	boolean		See implodeAttributes()
	 * @return	string		See implodeAttributes()
	 * @deprecated	Name was changed into implodeAttributes
	 * @see implodeAttributes()
	 */
	function implodeParams($arr,$xhtmlSafe=FALSE,$dontOmitBlankAttribs=FALSE)	{
		return t3lib_div::implodeAttributes($arr,$xhtmlSafe,$dontOmitBlankAttribs);
	}

	/**
	 * Wraps JavaScript code XHTML ready with <script>-tags
	 * Automatic re-identing of the JS code is done by using the first line as ident reference.
	 * This is nice for identing JS code with PHP code on the same level.
	 *
	 * @param	string		JavaScript code
	 * @param	boolean		Wrap script element in linebreaks? Default is TRUE.
	 * @return	string		The wrapped JS code, ready to put into a XHTML page
	 * @author	Ingmar Schlecht <ingmars@web.de>
	 * @author	René Fritz <r.fritz@colorcube.de>
	 */
	function wrapJS($string, $linebreak=TRUE) {
		if(trim($string)) {
				// <script wrapped in nl?
			$cr = $linebreak? "\n" : '';

				// remove nl from the beginning
			$string = preg_replace ('/^\n+/', '', $string);
				// re-ident to one tab using the first line as reference
			if(preg_match('/^(\t+)/',$string,$match)) {
				$string = str_replace($match[1],"\t", $string);
			}
			$string = $cr.'<script type="text/javascript">
/*<![CDATA[*/
'.$string.'
/*]]>*/
</script>'.$cr;
		}
		return trim($string);
	}


	/**
	 * Parses XML input into a PHP array with associative keys
	 * Usage: 0
	 *
	 * @param	string		XML data input
	 * @param	integer		Number of element levels to resolve the XML into an array. Any further structure will be set as XML.
	 * @return	mixed		The array with the parsed structure unless the XML parser returns with an error in which case the error message string is returned.
	 * @author bisqwit at iki dot fi dot not dot for dot ads dot invalid / http://dk.php.net/xml_parse_into_struct + kasperYYYY@typo3.com
	 */
	function xml2tree($string,$depth=999) {
		$parser = xml_parser_create();
		$vals = array();
		$index = array();

		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);
		xml_parse_into_struct($parser, $string, $vals, $index);

		if (xml_get_error_code($parser))	return 'Line '.xml_get_current_line_number($parser).': '.xml_error_string(xml_get_error_code($parser));
		xml_parser_free($parser);

		$stack = array( array() );
		$stacktop = 0;
		$startPoint=0;

		unset($tagi);
		foreach($vals as $key => $val) {
			$type = $val['type'];

				// open tag:
			if ($type=='open' || $type=='complete') {
				$stack[$stacktop++] = $tagi;

				if ($depth==$stacktop)	{
					$startPoint=$key;
				}

				$tagi = array('tag' => $val['tag']);

				if(isset($val['attributes']))  $tagi['attrs'] = $val['attributes'];
				if(isset($val['value']))	$tagi['values'][] = $val['value'];
			}
				// finish tag:
			if ($type=='complete' || $type=='close')	{
				$oldtagi = $tagi;
				$tagi = $stack[--$stacktop];
				$oldtag = $oldtagi['tag'];
				unset($oldtagi['tag']);

				if ($depth==($stacktop+1))	{
					if ($key-$startPoint > 0)	{
						$partArray = array_slice(
							$vals,
							$startPoint+1,
							$key-$startPoint-1
						);
						#$oldtagi=array('XMLvalue'=>t3lib_div::xmlRecompileFromStructValArray($partArray));
						$oldtagi['XMLvalue']=t3lib_div::xmlRecompileFromStructValArray($partArray);
					} else {
						$oldtagi['XMLvalue']=$oldtagi['values'][0];
					}
				}

				$tagi['ch'][$oldtag][] = $oldtagi;
				unset($oldtagi);
			}
				// cdata
			if($type=='cdata') {
				$tagi['values'][] = $val['value'];
			}
		}
		return $tagi['ch'];
	}

	/**
	 * Converts a PHP array into an XML string.
	 * The XML output is optimized for readability since associative keys are used as tagnames.
	 * This also means that only alphanumeric characters are allowed in the tag names AND only keys NOT starting with numbers (so watch your usage of keys!). However there are options you can set to avoid this problem.
	 * Numeric keys are stored with the default tagname "numIndex" but can be overridden to other formats)
	 * The function handles input values from the PHP array in a binary-safe way; All characters below 32 (except 9,10,13) will trigger the content to be converted to a base64-string
	 * The PHP variable type of the data IS preserved as long as the types are strings, arrays, integers and booleans. Strings are the default type unless the "type" attribute is set.
	 * The output XML has been tested with the PHP XML-parser and parses OK under all tested circumstances.
	 * However using MSIE to read the XML output didn't always go well: One reason could be that the character encoding is not observed in the PHP data. The other reason may be if the tag-names are invalid in the eyes of MSIE. Also using the namespace feature will make MSIE break parsing. There might be more reasons...
	 * Usage: 5
	 *
	 * @param	array		The input PHP array with any kind of data; text, binary, integers. Not objects though.
	 * @param	string		tag-prefix, eg. a namespace prefix like "T3:"
	 * @param	integer		Current recursion level. Don't change, stay at zero!
	 * @param	string		Alternative document tag. Default is "phparray".
	 * @param	integer		If set, the number of spaces corresponding to this number is used for indenting, otherwise a single chr(9) (TAB) is used
	 * @param	array		Options for the compilation. Key "useNindex" => 0/1 (boolean: whether to use "n0, n1, n2" for num. indexes); Key "useIndexTagForNum" => "[tag for numerical indexes]"; Key "useIndexTagForAssoc" => "[tag for associative indexes"; Key "parentTagMap" => array('parentTag' => 'thisLevelTag')
	 * @param	string		Stack data. Don't touch.
	 * @return	string		An XML string made from the input content in the array.
	 * @see xml2array()
	 */
	function array2xml($array,$NSprefix='',$level=0,$docTag='phparray',$spaceInd=0, $options=array(),$stackData=array())	{
			// The list of byte values which will trigger binary-safe storage. If any value has one of these char values in it, it will be encoded in base64
		$binaryChars = chr(0).chr(1).chr(2).chr(3).chr(4).chr(5).chr(6).chr(7).chr(8).
						chr(11).chr(12).chr(14).chr(15).chr(16).chr(17).chr(18).chr(19).
						chr(20).chr(21).chr(22).chr(23).chr(24).chr(25).chr(26).chr(27).chr(28).chr(29).
						chr(30).chr(31);
			// Set indenting mode:
		$indentChar = $spaceInd ? ' ' : chr(9);
		$indentN = $spaceInd>0 ? $spaceInd : 1;

			// Init output variable:
		$output='';

			// Traverse the input array
		if (is_array($array))	{
			foreach($array as $k=>$v)	{
				$attr = '';
				$tagName = $k;

					// Construct the tag name.
				if(isset($options['grandParentTagMap'][$stackData['grandParentTagName'].'/'.$stackData['parentTagName']])) {		// Use tag based on grand-parent + parent tag name
					$attr.=' index="'.htmlspecialchars($tagName).'"';
					$tagName = (string)$options['grandParentTagMap'][$stackData['grandParentTagName'].'/'.$stackData['parentTagName']];
				}elseif(isset($options['parentTagMap'][$stackData['parentTagName'].':'.$tagName])) {		// Use tag based on parent tag name + current tag
					$attr.=' index="'.htmlspecialchars($tagName).'"';
					$tagName = (string)$options['parentTagMap'][$stackData['parentTagName'].':'.$tagName];
				} elseif(isset($options['parentTagMap'][$stackData['parentTagName']])) {		// Use tag based on parent tag name:
					$attr.=' index="'.htmlspecialchars($tagName).'"';
					$tagName = (string)$options['parentTagMap'][$stackData['parentTagName']];
				} elseif (!strcmp(intval($tagName),$tagName))	{	// If integer...;
					if ($options['useNindex']) {	// If numeric key, prefix "n"
						$tagName = 'n'.$tagName;
					} else {	// Use special tag for num. keys:
						$attr.=' index="'.$tagName.'"';
						$tagName = $options['useIndexTagForNum'] ? $options['useIndexTagForNum'] : 'numIndex';
					}
				} elseif($options['useIndexTagForAssoc']) {		// Use tag for all associative keys:
					$attr.=' index="'.htmlspecialchars($tagName).'"';
					$tagName = $options['useIndexTagForAssoc'];
				}

					// The tag name is cleaned up so only alphanumeric chars (plus - and _) are in there and not longer than 100 chars either.
				$tagName = substr(ereg_replace('[^[:alnum:]_-]','',$tagName),0,100);

					// If the value is an array then we will call this function recursively:
				if (is_array($v))	{

						// Sub elements:
					if ($options['alt_options'][$stackData['path'].'/'.$tagName])	{
						$subOptions = $options['alt_options'][$stackData['path'].'/'.$tagName];
						$clearStackPath = $subOptions['clearStackPath'];
					} else {
						$subOptions = $options;
						$clearStackPath = FALSE;
					}

					$content = chr(10).
								t3lib_div::array2xml(
									$v,
									$NSprefix,
									$level+1,
									'',
									$spaceInd,
									$subOptions,
									array(
										'parentTagName' => $tagName,
										'grandParentTagName' => $stackData['parentTagName'],
										'path' => $clearStackPath ? '' : $stackData['path'].'/'.$tagName,
									)
								).
								str_pad('',($level+1)*$indentN,$indentChar);
					$attr.=' type="array"';
				} else {	// Just a value:

						// Look for binary chars:
					if (strcspn($v,$binaryChars) != strlen($v))	{	// Go for base64 encoding if the initial segment NOT matching any binary char has the same length as the whole string!
							// If the value contained binary chars then we base64-encode it an set an attribute to notify this situation:
						$content = chr(10).chunk_split(base64_encode($v));
						$attr.=' base64="1"';
					} else {
							// Otherwise, just htmlspecialchar the stuff:
						$content = htmlspecialchars($v);
						$dType = gettype($v);
						if ($dType!='string' && !$options['disableTypeAttrib'])	{ $attr.=' type="'.$dType.'"'; }
					}
				}

					// Add the element to the output string:
				$output.=str_pad('',($level+1)*$indentN,$indentChar).'<'.$NSprefix.$tagName.$attr.'>'.$content.'</'.$NSprefix.$tagName.'>'.chr(10);
			}
		}

			// If we are at the outer-most level, then we finally wrap it all in the document tags and return that as the value:
		if (!$level)	{
			$output =
				'<'.$docTag.'>'.chr(10).
				$output.
				'</'.$docTag.'>';
		}

		return $output;
	}

	/**
	 * Converts an XML string to a PHP array.
	 * This is the reverse function of array2xml()
	 * Usage: 17
	 *
	 * @param	string		XML content to convert into an array
	 * @param	string		The tag-prefix resolve, eg. a namespace like "T3:"
	 * @param	boolean		If set, the document tag will be set in the key "_DOCUMENT_TAG" of the output array
	 * @return	mixed		If the parsing had errors, a string with the error message is returned. Otherwise an array with the content.
	 * @see array2xml()
	 */
	function xml2array($string,$NSprefix='',$reportDocTag=FALSE) {
		global $TYPO3_CONF_VARS;

			// Create parser:
		$parser = xml_parser_create();
		$vals = array();
		$index = array();

		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 0);

			// PHP5 fix of charset awareness:
			// Problem is: PHP5 apparently detects the charset of the XML file (or defaults to utf-8) and will AUTOMATICALLY convert the content to either utf-8, iso-8859-1 or us-ascii. PHP4 just passed the content through without taking action regarding the charset.
			// In TYPO3 we expect that the charset of XML content is NOT handled in the parser but internally in TYPO3 instead. Therefore it would be very nice if PHP5 could be configured to NOT process the charset of the files. But this is not possible for now.
			// What we do here fixes the problem but ONLY if the charset is utf-8, iso-8859-1 or us-ascii. That should work for most TYPO3 installations, in particular if people use utf-8 which we highly recommend.
		if ((double)phpversion()>=5)	{
			unset($ereg_result);
			ereg('^[[:space:]]*<\?xml[^>]*encoding[[:space:]]*=[[:space:]]*"([^"]*)"',substr($string,0,200),$ereg_result);
			$theCharset = $ereg_result[1] ? $ereg_result[1] : ($TYPO3_CONF_VARS['BE']['forceCharset'] ? $TYPO3_CONF_VARS['BE']['forceCharset'] : 'iso-8859-1');
			xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $theCharset);  // us-ascii / utf-8 / iso-8859-1
		}

			// Parse content:
		xml_parse_into_struct($parser, $string, $vals, $index);

			// If error, return error message:
		if (xml_get_error_code($parser))	{
			return 'Line '.xml_get_current_line_number($parser).': '.xml_error_string(xml_get_error_code($parser));
		}
		xml_parser_free($parser);

			// Init vars:
		$stack = array(array());
		$stacktop = 0;
		$current=array();
		$tagName = '';
		$documentTag = '';

			// Traverse the parsed XML structure:
		foreach($vals as $key => $val) {

				// First, process the tag-name (which is used in both cases, whether "complete" or "close")
			$tagName = $val['tag'];
			if (!$documentTag)	$documentTag = $tagName;

				// Test for name space:
			$tagName = ($NSprefix && substr($tagName,0,strlen($NSprefix))==$NSprefix) ? substr($tagName,strlen($NSprefix)) : $tagName;

				// Test for numeric tag, encoded on the form "nXXX":
			$testNtag = substr($tagName,1);	// Closing tag.
			$tagName = (substr($tagName,0,1)=='n' && !strcmp(intval($testNtag),$testNtag)) ? intval($testNtag) : $tagName;

				// Test for alternative index value:
			if (strlen($val['attributes']['index']))	{ $tagName = $val['attributes']['index']; }

				// Setting tag-values, manage stack:
			switch($val['type'])	{
				case 'open':		// If open tag it means there is an array stored in sub-elements. Therefore increase the stackpointer and reset the accumulation array:
					$current[$tagName] = array();	// Setting blank place holder
					$stack[$stacktop++] = $current;
					$current = array();
				break;
				case 'close':	// If the tag is "close" then it is an array which is closing and we decrease the stack pointer.
					$oldCurrent = $current;
					$current = $stack[--$stacktop];
					end($current);	// Going to the end of array to get placeholder key, key($current), and fill in array next:
					$current[key($current)] = $oldCurrent;
					unset($oldCurrent);
				break;
				case 'complete':	// If "complete", then it's a value. If the attribute "base64" is set, then decode the value, otherwise just set it.
					if ($val['attributes']['base64'])	{
						$current[$tagName] = base64_decode($val['value']);
					} else {
						$current[$tagName] = (string)$val['value']; // Had to cast it as a string - otherwise it would be evaluate false if tested with isset()!!

							// Cast type:
						switch((string)$val['attributes']['type'])	{
							case 'integer':
								$current[$tagName] = (integer)$current[$tagName];
							break;
							case 'double':
								$current[$tagName] = (double)$current[$tagName];
							break;
							case 'boolean':
								$current[$tagName] = (bool)$current[$tagName];
							break;
							case 'array':
								$current[$tagName] = array();	// MUST be an empty array since it is processed as a value; Empty arrays would end up here because they would have no tags inside...
							break;
						}
					}
				break;
			}
		}

		if ($reportDocTag)	{
			$current[$tagName]['_DOCUMENT_TAG'] = $documentTag;
		}

			// Finally return the content of the document tag.
		return $current[$tagName];
	}

	/**
	 * This implodes an array of XML parts (made with xml_parse_into_struct()) into XML again.
	 * Usage: 2
	 *
	 * @param	array		A array of XML parts, see xml2tree
	 * @return	string		Re-compiled XML data.
	 */
	function xmlRecompileFromStructValArray($vals)	{
		$XMLcontent='';

		foreach($vals as $val) {
			$type = $val['type'];

				// open tag:
			if ($type=='open' || $type=='complete') {
				$XMLcontent.='<'.$val['tag'];
				if(isset($val['attributes']))  {
					foreach($val['attributes'] as $k => $v)	{
						$XMLcontent.=' '.$k.'="'.htmlspecialchars($v).'"';
					}
				}
				if ($type=='complete')	{
					if(isset($val['value']))	{
						$XMLcontent.='>'.htmlspecialchars($val['value']).'</'.$val['tag'].'>';
					} else $XMLcontent.='/>';
				} else $XMLcontent.='>';

				if ($type=='open' && isset($val['value']))	{
					$XMLcontent.=htmlspecialchars($val['value']);
				}
			}
				// finish tag:
			if ($type=='close')	{
				$XMLcontent.='</'.$val['tag'].'>';
			}
				// cdata
			if($type=='cdata') {
				$XMLcontent.=htmlspecialchars($val['value']);
			}
		}

		return $XMLcontent;
	}

	/**
	 * Extract the encoding scheme as found in the first line of an XML document (typically)
	 * Usage: 1
	 *
	 * @param	string		XML data
	 * @return	string		Encoding scheme (lowercase), if found.
	 */
	function xmlGetHeaderAttribs($xmlData)	{
		$xmlHeader = substr(trim($xmlData),0,200);
		$reg=array();
		if (eregi('^<\?xml([^>]*)\?\>',$xmlHeader,$reg))	{
			return t3lib_div::get_tag_attributes($reg[1]);
		}
	}











	/*************************
	 *
	 * FILES FUNCTIONS
	 *
	 *************************/

	/**
	 * Reads the file or url $url and returns the content
	 * If you are having trouble with proxys when reading URLs you can configure your way out of that with settings like $TYPO3_CONF_VARS['SYS']['curlUse'] etc.
	 * Usage: 83
	 *
	 * @param	string		Filepath/URL to read
	 * @return	string		The content from the resource given as input.
	 */
	function getURL($url)	{
		$content = '';

			// (Proxy support implemented by Arco <arco@appeltaart.mine.nu>)
		if((substr($url,0,7)=='http://') && ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse']=='1'))	{
			//external URL without error checking.
			$ch = curl_init();
			curl_setopt ($ch,CURLOPT_URL, $url);
			curl_setopt ($ch,CURLOPT_HEADER, 0);
			curl_setopt ($ch,CURLOPT_RETURNTRANSFER, 1);

			if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyServer']) {
				curl_setopt ($ch, CURLOPT_PROXY, $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyServer']);

				// I don't know if it will be needed
				if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyTunnel']) {
					curl_setopt ($ch, CURLOPT_HTTPPROXYTUNNEL, $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyTunnel'] );
				}
				if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyUserPass']) {
					curl_setopt ($ch, CURLOPT_PROXYUSERPWD, $GLOBALS['TYPO3_CONF_VARS']['SYS']['curlProxyUserPass'] );
				}
			}
			$content=curl_exec ($ch);
			curl_close ($ch);
			return $content;
		} elseif($fd = fopen($url,'rb'))    {
			while (!feof($fd))	{
				$content.=fread($fd, 5000);
			}
			fclose($fd);
			return $content;
		}
	}

	/**
	 * Writes $content to the file $file
	 * Usage: 30
	 *
	 * @param	string		Filepath to write to
	 * @param	string		Content to write
	 * @return	boolean		True if the file was successfully opened and written to.
	 */
	function writeFile($file,$content)	{
		if($fd = fopen($file,'wb'))	{
			fwrite( $fd, $content);
			fclose( $fd );

			t3lib_div::fixPermissions($file);	// Change the permissions of the file

			return true;
		}
	}

	/**
	 * Setting file system mode & group ownership of file
	 *
	 * @param	string		Filepath of newly created file
	 * @return	void
	 */
	function fixPermissions($file)	{
		if (@is_file($file) && TYPO3_OS!='WIN')	{
			@chmod($file, octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['fileCreateMask']));		// "@" is there because file is not necessarily OWNED by the user
			if($GLOBALS['TYPO3_CONF_VARS']['BE']['createGroup'])	{	// skip this if createGroup is empty
				@chgrp($file, $GLOBALS['TYPO3_CONF_VARS']['BE']['createGroup']);		// "@" is there because file is not necessarily OWNED by the user
			}
		}
	}

	/**
	 * Writes $content to a filename in the typo3temp/ folder (and possibly a subfolder...)
	 * Accepts an additional subdirectory in the file path!
	 *
	 * @param	string		Absolute filepath to write to inside "typo3temp/". First part of this string must match PATH_site."typo3temp/"
	 * @param	string		Content string to write
	 * @return	string		Returns false on success, otherwise an error string telling about the problem.
	 */
	function writeFileToTypo3tempDir($filepath,$content)	{

			// Parse filepath into directory and basename:
		$fI = pathinfo($filepath);
		$fI['dirname'].= '/';

			// Check parts:
		if (t3lib_div::validPathStr($filepath) && $fI['basename'] && strlen($fI['basename'])<60)	{
			if (defined('PATH_site'))	{
				$dirName = PATH_site.'typo3temp/';	// Setting main temporary directory name (standard)
				if (@is_dir($dirName))	{
					if (t3lib_div::isFirstPartOfStr($fI['dirname'],$dirName))	{

							// Checking if the "subdir" is found:
						$subdir = substr($fI['dirname'],strlen($dirName));
						if ($subdir)	{
							if (ereg('^[[:alnum:]_]+\/$',$subdir))	{
								$dirName.= $subdir;
								if (!@is_dir($dirName))	{
									t3lib_div::mkdir($dirName);
								}
							} else return 'Subdir, "'.$subdir.'", was NOT on the form "[a-z]/"';
						}
							// Checking dir-name again (sub-dir might have been created):
						if (@is_dir($dirName))	{
							if ($filepath == $dirName.$fI['basename'])	{
								t3lib_div::writeFile($filepath, $content);
								if (!@is_file($filepath))	return 'File not written to disk! Write permission error in filesystem?';
							} else return 'Calculated filelocation didn\'t match input $filepath!';
						} else return '"'.$dirName.'" is not a directory!';
					} else return '"'.$fI['dirname'].'" was not within directory PATH_site + "typo3temp/"';
				} else return 'PATH_site + "typo3temp/" was not a directory!';
			} else return 'PATH_site constant was NOT defined!';
		} else return 'Input filepath "'.$filepath.'" was generally invalid!';
	}

	/**
	 * Wrapper function for mkdir, setting folder permissions according to $GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask'] and group ownership according to $GLOBALS['TYPO3_CONF_VARS']['BE']['createGroup']
	 * Usage: 6
	 *
	 * @param	string		Absolute path to folder, see PHP mkdir() function. Removes trailing slash internally.
	 * @return	boolean		TRUE if @mkdir went well!
	 */
	function mkdir($theNewFolder)	{
		$theNewFolder = ereg_replace('\/$','',$theNewFolder);
		if (mkdir($theNewFolder, octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask']))){
			chmod($theNewFolder, octdec($GLOBALS['TYPO3_CONF_VARS']['BE']['folderCreateMask'])); //added this line, because the mode at 'mkdir' has a strange behaviour sometimes

			if($GLOBALS['TYPO3_CONF_VARS']['BE']['createGroup'])	{	// skip this if createGroup is empty
				chgrp($theNewFolder, $GLOBALS['TYPO3_CONF_VARS']['BE']['createGroup']);
			}
			return TRUE;
		}
	}

	/**
	 * Returns an array with the names of folders in a specific path
	 * Will return 'error' (string) if there were an error with reading directory content.
	 * Usage: 11
	 *
	 * @param	string		Path to list directories from
	 * @return	array		Returns an array with the directory entries as values. If no path, the return value is nothing.
	 */
	function get_dirs($path)	{
		if ($path)	{
			$d = @dir($path);
			if (is_object($d))	{
				while($entry=$d->read()) {
					if (@is_dir($path.'/'.$entry) && $entry!= '..' && $entry!= '.')	{
					    $filearray[]=$entry;
					}
				}
				$d->close();
			} else return 'error';
			return $filearray;
		}
	}

	/**
	 * Returns an array with the names of files in a specific path
	 * Usage: 18
	 *
	 * @param	string		$path: Is the path to the file
	 * @param	string		$extensionList is the comma list of extensions to read only (blank = all)
	 * @param	boolean		If set, then the path is prepended the filenames. Otherwise only the filenames are returned in the array
	 * @param	string		$order is sorting: 1= sort alphabetically, 'mtime' = sort by modification time.
	 * @return	array		Array of the files found
	 */
	function getFilesInDir($path,$extensionList='',$prependPath=0,$order='')	{

			// Initialize variabels:
		$filearray = array();
		$sortarray = array();
		$path = ereg_replace('\/$','',$path);

			// Find files+directories:
		if (@is_dir($path))	{
			$extensionList = strtolower($extensionList);
			$d = dir($path);
			if (is_object($d))	{
				while($entry=$d->read()) {
					if (@is_file($path.'/'.$entry))	{
						$fI = pathinfo($entry);
						$key = md5($path.'/'.$entry);	// Don't change this ever - extensions may depend on the fact that the hash is an md5 of the path! (import/export extension)
						if (!$extensionList || t3lib_div::inList($extensionList,strtolower($fI['extension'])))	{
						    $filearray[$key]=($prependPath?$path.'/':'').$entry;
							if ($order=='mtime') {$sortarray[$key]=filemtime($path.'/'.$entry);}
								elseif ($order)	{$sortarray[$key]=$entry;}
						}
					}
				}
				$d->close();
			} else return 'error opening path: "'.$path.'"';
		}

			// Sort them:
		if ($order) {
			asort($sortarray);
			reset($sortarray);
			$newArr=array();
			while(list($k,$v)=each($sortarray))	{
				$newArr[$k]=$filearray[$k];
			}
			$filearray=$newArr;
		}

			// Return result
		reset($filearray);
		return $filearray;
	}

	/**
	 * Recursively gather all files and folders of a path.
	 * Usage: 5
	 *
	 * @param	array		$fileArr: Empty input array (will have files added to it)
	 * @param	string		$path: The path to read recursively from (absolute) (include trailing slash!)
	 * @param	string		$extList: Comma list of file extensions: Only files with extensions in this list (if applicable) will be selected.
	 * @param	boolean		$regDirs: If set, directories are also included in output.
	 * @param	integer		$recursivityLevels: The number of levels to dig down...
	 * @return	array		An array with the found files/directories.
	 */
	function getAllFilesAndFoldersInPath($fileArr,$path,$extList='',$regDirs=0,$recursivityLevels=99)	{
		if ($regDirs)	$fileArr[] = $path;
		$fileArr = array_merge($fileArr, t3lib_div::getFilesInDir($path,$extList,1,1));

		$dirs = t3lib_div::get_dirs($path);
		if (is_array($dirs) && $recursivityLevels>0)	{
			foreach ($dirs as $subdirs)	{
				if ((string)$subdirs!='')	{
					$fileArr = t3lib_div::getAllFilesAndFoldersInPath($fileArr,$path.$subdirs.'/',$extList,$regDirs,$recursivityLevels-1);
				}
			}
		}
		return $fileArr;
	}

	/**
	 * Removes the absolute part of all files/folders in fileArr
	 * Usage: 2
	 *
	 * @param	array		$fileArr: The file array to remove the prefix from
	 * @param	string		$prefixToRemove: The prefix path to remove (if found as first part of string!)
	 * @return	array		The input $fileArr processed.
	 */
	function removePrefixPathFromList($fileArr,$prefixToRemove)	{
		foreach($fileArr as $k => $absFileRef)	{
			if(t3lib_div::isFirstPartOfStr($absFileRef,$prefixToRemove))	{
				$fileArr[$k] = substr($absFileRef,strlen($prefixToRemove));
			} else return 'ERROR: One or more of the files was NOT prefixed with the prefix-path!';
		}
		return $fileArr;
	}

	/**
	 * Fixes a path for windows-backslashes and reduces double-slashes to single slashes
	 * Usage: 2
	 *
	 * @param	string		File path to process
	 * @return	string
	 */
	function fixWindowsFilePath($theFile)	{
		return str_replace('//','/', str_replace('\\','/', $theFile));
	}

	/**
	 * Resolves "../" sections in the input path string.
	 * For example "fileadmin/directory/../other_directory/" will be resolved to "fileadmin/other_directory/"
	 * Usage: 2
	 *
	 * @param	string		File path in which "/../" is resolved
	 * @return	string
	 */
	function resolveBackPath($pathStr)	{
		$parts = explode('/',$pathStr);
		$output=array();
		foreach($parts as $pV)	{
			if ($pV=='..')	{
				if ($c)	{
					array_pop($output);
					$c--;
				} else $output[]=$pV;
			} else {
				$c++;
				$output[]=$pV;
			}
		}
		return implode('/',$output);
	}

	/**
	 * Prefixes a URL used with 'header-location' with 'http://...' depending on whether it has it already.
	 * - If already having a scheme, nothing is prepended
	 * - If having REQUEST_URI slash '/', then prefixing 'http://[host]' (relative to host)
	 * - Otherwise prefixed with TYPO3_REQUEST_DIR (relative to current dir / TYPO3_REQUEST_DIR)
	 * Usage: 30
	 *
	 * @param	string		URL / path to prepend full URL addressing to.
	 * @return	string
	 */
	function locationHeaderUrl($path)	{
		$uI = parse_url($path);
		if (substr($path,0,1)=='/')	{ // relative to HOST
			$path = t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST').$path;
		} elseif (!$uI['scheme'])	{ // No scheme either
			$path = t3lib_div::getIndpEnv('TYPO3_REQUEST_DIR').$path;
		}
		return $path;
	}
















	/*************************
	 *
	 * DEBUG helper FUNCTIONS
	 *
	 *************************/

	/**
	 * Returns a string with a list of ascii-values for the first $characters characters in $string
	 * Usage: 0
	 *
	 * @param	string		String to show ASCII value for
	 * @param	integer		Number of characters to show
	 * @return	string		The string with ASCII values in separated by a space char.
	 * @internal
	 */
	function debug_ordvalue($string,$characters=100)	{
		if(strlen($string) < $characters)	$characters = strlen($string);
		for ($i=0; $i<$characters; $i++)	{
			$valuestring.=' '.ord(substr($string,$i,1));
		}
		return trim($valuestring);
	}

	/**
	 * Returns HTML-code, which is a visual representation of a multidimensional array
	 * use t3lib_div::print_array() in order to print an array
	 * Returns false if $array_in is not an array
	 * Usage: 31
	 *
	 * @param	array		Array to view
	 * @return	string		HTML output
	 */
	function view_array($array_in)	{
		if (is_array($array_in))	{
			$result='<table border="1" cellpadding="1" cellspacing="0" bgcolor="white">';
			if (!count($array_in))	{$result.= '<tr><td><font face="Verdana,Arial" size="1"><b>'.htmlspecialchars("EMPTY!").'</b></font></td></tr>';}
			while (list($key,$val)=each($array_in))	{
				$result.= '<tr><td><font face="Verdana,Arial" size="1">'.htmlspecialchars((string)$key).'</font></td><td>';
				if (is_array($array_in[$key]))	{
					$result.=t3lib_div::view_array($array_in[$key]);
				} else
					$result.= '<font face="Verdana,Arial" size="1" color="red">'.nl2br(htmlspecialchars((string)$val)).'<br /></font>';
				$result.= '</td></tr>';
			}
			$result.= '</table>';
		} else	{
			$result  = false;
		}
		return $result;
	}

	/**
	 * Prints an array
	 * Usage: 6
	 *
	 * @param	array		Array to print visually (in a table).
	 * @return	void
	 * @internal
	 * @see view_array()
	 */
	function print_array($array_in)	{
		echo t3lib_div::view_array($array_in);
	}

	/**
	 * Makes debug output
	 * Prints $var in bold between two vertical lines
	 * If not $var the word 'debug' is printed
	 * If $var is an array, the array is printed by t3lib_div::print_array()
	 * Usage: 8
	 *
	 * @param	mixed		Variable to print
	 * @param	mixed		If the parameter is a string it will be used as header. Otherwise number of break tags to apply after (positive integer) or before (negative integer) the output.
	 * @return	void
	 */
	function debug($var="",$brOrHeader=0)	{
		if ($brOrHeader && !t3lib_div::testInt($brOrHeader)) {
			echo '<table border="0" cellpadding="0" cellspacing="0" bgcolor="white" style="border:0px; margin-top:3px; margin-bottom:3px;"><tr><td style="background-color:#bbbbbb; font-family: verdana,arial; font-weight: bold; font-size: 10px;">'.htmlspecialchars((string)$brOrHeader).'</td></tr><td>';
		} elseif ($brOrHeader<0) {
			for($a=0;$a<abs(intval($brOrHeader));$a++){echo '<br />';}
		}

		if (is_array($var))	{
			t3lib_div::print_array($var);
		} elseif (is_object($var))	{
			echo '<b>|Object:<pre>';
			print_r($var);
			echo '</pre>|</b>';
		} elseif ((string)$var!='') {
			echo '<b>|'.htmlspecialchars((string)$var).'|</b>';
		} else {
			echo '<b>| debug |</b>';
		}

		if ($brOrHeader && !t3lib_div::testInt($brOrHeader)) {
			echo '</td></tr></table>';
		} elseif ($brOrHeader>0) {
			for($a=0;$a<intval($brOrHeader);$a++){echo '<br />';}
		}
	}
































	/*************************
	 *
	 * SYSTEM INFORMATION
	 *
	 *************************/

	/**
	 * Returns the HOST+DIR-PATH of the current script (The URL, but without 'http://' and without script-filename)
	 * Usage: 1
	 *
	 * @return	string
	 */
	function getThisUrl()	{
		$p=parse_url(t3lib_div::getIndpEnv('TYPO3_REQUEST_SCRIPT'));		// Url of this script
		$dir=t3lib_div::dirname($p['path']).'/';	// Strip file
		$url = str_replace('//','/',$p['host'].($p['port']?':'.$p['port']:'').$dir);
		return $url;
	}

	/**
	 * Returns the link-url to the current script.
	 * In $getParams you can set associative keys corresponding to the GET-vars you wish to add to the URL. If you set them empty, they will remove existing GET-vars from the current URL.
	 * REMEMBER to always use htmlspecialchars() for content in href-properties to get ampersands converted to entities (XHTML requirement and XSS precaution)
	 * Usage: 52
	 *
	 * @param	array		Array of GET parameters to include
	 * @return	string
	 */
	function linkThisScript($getParams=array())	{
		$parts = t3lib_div::getIndpEnv('SCRIPT_NAME');
		$params = t3lib_div::_GET();

		foreach($getParams as $k => $v)	{
			if (strcmp($v,''))	{
				$params[$k]=$v;
			} else unset($params[$k]);
		}

		$pString = t3lib_div::implodeArrayForUrl('',$params);

		return $pString ? $parts.'?'.ereg_replace('^&','',$pString) : $parts;
	}

	/**
	 * Takes a full URL, $url, possibly with a querystring and overlays the $getParams arrays values onto the quirystring, packs it all together and returns the URL again.
	 * So basically it adds the parameters in $getParams to an existing URL, $url
	 * Usage: 2
	 *
	 * @param	string		URL string
	 * @param	array		Array of key/value pairs for get parameters to add/overrule with. Can be multidimensional.
	 * @return	string		Output URL with added getParams.
	 */
	function linkThisUrl($url,$getParams=array())	{
		$parts = parse_url($url);
		if ($parts['query'])	{
			parse_str($parts['query'],$getP);
		} else {
			$getP = array();
		}

		$getP = t3lib_div::array_merge_recursive_overrule($getP,$getParams);
		$uP = explode('?',$url);

		$params = t3lib_div::implodeArrayForUrl('',$getP);
		$outurl = $uP[0].($params ? '?'.substr($params, 1) : '');

		return $outurl;
	}

	/**
	 * Abstraction method which returns System Environment Variables regardless of server OS, CGI/MODULE version etc. Basically this is SERVER variables for most of them.
	 * This should be used instead of getEnv() and $_SERVER/ENV_VARS to get reliable values for all situations.
	 * Usage: 221
	 *
	 * @param	string		Name of the "environment variable"/"server variable" you wish to use. Valid values are SCRIPT_NAME, SCRIPT_FILENAME, REQUEST_URI, PATH_INFO, REMOTE_ADDR, REMOTE_HOST, HTTP_REFERER, HTTP_HOST, HTTP_USER_AGENT, HTTP_ACCEPT_LANGUAGE, QUERY_STRING, TYPO3_DOCUMENT_ROOT, TYPO3_HOST_ONLY, TYPO3_HOST_ONLY, TYPO3_REQUEST_HOST, TYPO3_REQUEST_URL, TYPO3_REQUEST_SCRIPT, TYPO3_REQUEST_DIR, TYPO3_SITE_URL, _ARRAY
	 * @return	string		Value based on the input key, independent of server/os environment.
	 */
	function getIndpEnv($getEnvName)	{
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
				TYPO3_HOST_ONLY	=		[host]			= 192.168.1.4
				TYPO3_PORT		=		[port]			= 8080 (blank if 80, taken from host value)
				TYPO3_REQUEST_HOST = 	[scheme]://[host][:[port]]
				TYPO3_REQUEST_URL =		[scheme]://[host][:[port]][path]?[query]	(sheme will by default be 'http' until we can detect if it's https -
				TYPO3_REQUEST_SCRIPT =  [scheme]://[host][:[port]][path_script]
				TYPO3_REQUEST_DIR =		[scheme]://[host][:[port]][path_dir]
				TYPO3_SITE_URL = 		[scheme]://[host][:[port]][path_dir] of the TYPO3 website frontend
				TYPO3_SITE_SCRIPT = 	[script / Speaking URL] of the TYPO3 website
				TYPO3_DOCUMENT_ROOT	=	Absolute path of root of documents:	TYPO3_DOCUMENT_ROOT.SCRIPT_NAME = SCRIPT_FILENAME (typically)

			Notice: [fragment] is apparently NEVER available to the script!


			Testing suggestions:
			- Output all the values.
			- In the script, make a link to the script it self, maybe add some parameters and click the link a few times so HTTP_REFERER is seen
			- ALSO TRY the script from the ROOT of a site (like 'http://www.mytest.com/' and not 'http://www.mytest.com/test/' !!)

		*/

#		if ($getEnvName=='HTTP_REFERER')	return '';
		switch((string)$getEnvName)	{
			case 'SCRIPT_NAME':
				return (php_sapi_name()=='cgi'||php_sapi_name()=='cgi-fcgi')&&($_SERVER['ORIG_PATH_INFO']?$_SERVER['ORIG_PATH_INFO']:$_SERVER['PATH_INFO']) ? ($_SERVER['ORIG_PATH_INFO']?$_SERVER['ORIG_PATH_INFO']:$_SERVER['PATH_INFO']) : ($_SERVER['ORIG_SCRIPT_NAME']?$_SERVER['ORIG_SCRIPT_NAME']:$_SERVER['SCRIPT_NAME']);
			break;
			case 'SCRIPT_FILENAME':
				return str_replace('//','/', str_replace('\\','/', (php_sapi_name()=='cgi'||php_sapi_name()=='isapi' ||php_sapi_name()=='cgi-fcgi')&&($_SERVER['ORIG_PATH_TRANSLATED']?$_SERVER['ORIG_PATH_TRANSLATED']:$_SERVER['PATH_TRANSLATED'])? ($_SERVER['ORIG_PATH_TRANSLATED']?$_SERVER['ORIG_PATH_TRANSLATED']:$_SERVER['PATH_TRANSLATED']):($_SERVER['ORIG_SCRIPT_FILENAME']?$_SERVER['ORIG_SCRIPT_FILENAME']:$_SERVER['SCRIPT_FILENAME'])));
			break;
			case 'REQUEST_URI':
					// Typical application of REQUEST_URI is return urls, forms submitting to itself etc. Example: returnUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'))
				if (!$_SERVER['REQUEST_URI'])	{	// This is for ISS/CGI which does not have the REQUEST_URI available.
					return '/'.ereg_replace('^/','',t3lib_div::getIndpEnv('SCRIPT_NAME')).
						($_SERVER['QUERY_STRING']?'?'.$_SERVER['QUERY_STRING']:'');
				} else return $_SERVER['REQUEST_URI'];
			break;
			case 'PATH_INFO':
					// $_SERVER['PATH_INFO']!=$_SERVER['SCRIPT_NAME'] is necessary because some servers (Windows/CGI) are seen to set PATH_INFO equal to script_name
					// Further, there must be at least one '/' in the path - else the PATH_INFO value does not make sense.
					// IF 'PATH_INFO' never works for our purpose in TYPO3 with CGI-servers, then 'php_sapi_name()=='cgi'' might be a better check. Right now strcmp($_SERVER['PATH_INFO'],t3lib_div::getIndpEnv('SCRIPT_NAME')) will always return false for CGI-versions, but that is only as long as SCRIPT_NAME is set equal to PATH_INFO because of php_sapi_name()=='cgi' (see above)
//				if (strcmp($_SERVER['PATH_INFO'],t3lib_div::getIndpEnv('SCRIPT_NAME')) && count(explode('/',$_SERVER['PATH_INFO']))>1)	{
				if (php_sapi_name()!='cgi'&&php_sapi_name()!='cgi-fcgi')	{
					return $_SERVER['PATH_INFO'];
				} else return '';
			break;
				// These are let through without modification
			case 'REMOTE_ADDR':
			case 'REMOTE_HOST':
			case 'HTTP_REFERER':
			case 'HTTP_HOST':
			case 'HTTP_USER_AGENT':
			case 'HTTP_ACCEPT_LANGUAGE':
			case 'QUERY_STRING':
				return $_SERVER[$getEnvName];
			break;
			case 'TYPO3_DOCUMENT_ROOT':
				// Some CGI-versions (LA13CGI) and mod-rewrite rules on MODULE versions will deliver a 'wrong' DOCUMENT_ROOT (according to our description). Further various aliases/mod_rewrite rules can disturb this as well.
				// Therefore the DOCUMENT_ROOT is now always calculated as the SCRIPT_FILENAME minus the end part shared with SCRIPT_NAME.
				$SFN = t3lib_div::getIndpEnv('SCRIPT_FILENAME');
				$SN_A = explode('/',strrev(t3lib_div::getIndpEnv('SCRIPT_NAME')));
				$SFN_A = explode('/',strrev($SFN));
				$acc = array();
				while(list($kk,$vv)=each($SN_A))	{
					if (!strcmp($SFN_A[$kk],$vv))	{
						$acc[] = $vv;
					} else break;
				}
				$commonEnd=strrev(implode('/',$acc));
				if (strcmp($commonEnd,''))	{ $DR = substr($SFN,0,-(strlen($commonEnd)+1)); }
				return $DR;
			break;
			case 'TYPO3_HOST_ONLY':
				$p = explode(':',$_SERVER['HTTP_HOST']);
				return $p[0];
			break;
			case 'TYPO3_PORT':
				$p = explode(':',$_SERVER['HTTP_HOST']);
				return $p[1];
			break;
			case 'TYPO3_REQUEST_HOST':
				return (t3lib_div::getIndpEnv('TYPO3_SSL') ? 'https://' : 'http://').
					$_SERVER['HTTP_HOST'];
			break;
			case 'TYPO3_REQUEST_URL':
				return t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST').t3lib_div::getIndpEnv('REQUEST_URI');
			break;
			case 'TYPO3_REQUEST_SCRIPT':
				return t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST').t3lib_div::getIndpEnv('SCRIPT_NAME');
			break;
			case 'TYPO3_REQUEST_DIR':
				return t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST').t3lib_div::dirname(t3lib_div::getIndpEnv('SCRIPT_NAME')).'/';
			break;
			case 'TYPO3_SITE_URL':
				if (defined('PATH_thisScript') && defined('PATH_site'))	{
					$lPath = substr(dirname(PATH_thisScript),strlen(PATH_site)).'/';
					$url = t3lib_div::getIndpEnv('TYPO3_REQUEST_DIR');
					$siteUrl = substr($url,0,-strlen($lPath));
					if (substr($siteUrl,-1)!='/')	$siteUrl.='/';
					return $siteUrl;
				} else return '';
			break;
			case 'TYPO3_SITE_SCRIPT':
				return substr(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'),strlen(t3lib_div::getIndpEnv('TYPO3_SITE_URL')));
			break;
			case 'TYPO3_SSL':
				return $_SERVER['SSL_SESSION_ID'] || !strcmp($_SERVER['HTTPS'],'on') ? TRUE : FALSE;
			break;
			case '_ARRAY':
				$out = array();
					// Here, list ALL possible keys to this function for debug display.
				$envTestVars = t3lib_div::trimExplode(',','
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
					SCRIPT_NAME,
					TYPO3_DOCUMENT_ROOT,
					SCRIPT_FILENAME,
					REMOTE_ADDR,
					REMOTE_HOST,
					HTTP_USER_AGENT,
					HTTP_ACCEPT_LANGUAGE',1);
				reset($envTestVars);
				while(list(,$v)=each($envTestVars))	{
					$out[$v]=t3lib_div::getIndpEnv($v);
				}
				reset($out);
				return $out;
			break;
		}
	}

	/**
	 * milliseconds
	 * microtime recalculated to t3lib_div::milliseconds(1/1000 sec)
	 * Usage: 20
	 *
	 * @return	integer
	 */
	function milliseconds()	{
		$p=explode(' ',microtime());
		return round(($p[0]+$p[1])*1000);
	}

	/**
	 * Client Browser Information
	 * Usage: 4
	 *
	 * @param	string		Alternative User Agent string (if empty, t3lib_div::getIndpEnv('HTTP_USER_AGENT') is used)
	 * @return	array		Parsed information about the HTTP_USER_AGENT in categories BROWSER, VERSION, SYSTEM and FORMSTYLE
	 */
	function clientInfo($useragent='')	{
		if (!$useragent) $useragent=t3lib_div::getIndpEnv('HTTP_USER_AGENT');

		$bInfo=array();
			// Which browser?
		if (strstr($useragent,'Konqueror'))	{
			$bInfo['BROWSER']= 'konqu';
		} elseif (strstr($useragent,'Opera'))	{
			$bInfo['BROWSER']= 'opera';
		} elseif (strstr($useragent,'MSIE 4') || strstr($useragent,'MSIE 5') || strstr($useragent,'MSIE 6'))	{
			$bInfo['BROWSER']= 'msie';
		} elseif (strstr($useragent,'Mozilla/4') || strstr($useragent,'Mozilla/5'))	{
			$bInfo['BROWSER']='net';
		}
		if ($bInfo['BROWSER'])	{
				// Browser version
			switch($bInfo['BROWSER'])	{
				case 'net':
					$bInfo['VERSION']= doubleval(substr($useragent,8));
					if (strstr($useragent,'Netscape6/')) {$bInfo['VERSION']=doubleval(substr(strstr($useragent,'Netscape6/'),10));}
					if (strstr($useragent,'Netscape/7')) {$bInfo['VERSION']=doubleval(substr(strstr($useragent,'Netscape/7'),9));}
				break;
				case 'msie':
					$tmp = strstr($useragent,'MSIE');
					$bInfo['VERSION'] = doubleval(ereg_replace('^[^0-9]*','',substr($tmp,4)));
				break;
				case 'opera':
					$tmp = strstr($useragent,'Opera');
					$bInfo['VERSION'] = doubleval(ereg_replace('^[^0-9]*','',substr($tmp,5)));
				break;
				case 'konqu':
					$tmp = strstr($useragent,'Konqueror/');
					$bInfo['VERSION'] = doubleval(substr($tmp,10));
				break;
			}
				// Client system
			if (strstr($useragent,'Win'))	{
				$bInfo['SYSTEM'] = 'win';
			} elseif (strstr($useragent,'Mac'))	{
				$bInfo['SYSTEM'] = 'mac';
			} elseif (strstr($useragent,'Linux') || strstr($useragent,'X11') || strstr($useragent,'SGI') || strstr($useragent,' SunOS ') || strstr($useragent,' HP-UX '))	{
				$bInfo['SYSTEM'] = 'unix';
			}
		}
			// Is true if the browser supports css to format forms, especially the width
		$bInfo['FORMSTYLE']=($bInfo['BROWSER']=='msie' || ($bInfo['BROWSER']=='net'&&$bInfo['VERSION']>=5) || $bInfo['BROWSER']=='opera' || $bInfo['BROWSER']=='konqu');

		return $bInfo;
	}























	/*************************
	 *
	 * TYPO3 SPECIFIC FUNCTIONS
	 *
	 *************************/

	/**
	 * Returns the absolute filename of a relative reference, resolves the "EXT:" prefix (way of referring to files inside extensions) and checks that the file is inside the PATH_site of the TYPO3 installation and implies a check with t3lib_div::validPathStr(). Returns false if checks failed. Does not check if the file exists.
	 * Usage: 24
	 *
	 * @param	string		The input filename/filepath to evaluate
	 * @param	boolean		If $onlyRelative is set (which it is by default), then only return values relative to the current PATH_site is accepted.
	 * @param	boolean		If $relToTYPO3_mainDir is set, then relative paths are relative to PATH_typo3 constant - otherwise (default) they are relative to PATH_site
	 * @return	string		Returns the absolute filename of $filename IF valid, otherwise blank string.
	 */
	function getFileAbsFileName($filename,$onlyRelative=1,$relToTYPO3_mainDir=0)	{
		if (!strcmp($filename,''))		return '';

		if ($relToTYPO3_mainDir)	{
			if (!defined('PATH_typo3'))	return '';
			$relPathPrefix = PATH_typo3;
		} else {
			$relPathPrefix = PATH_site;
		}
		if (substr($filename,0,4)=='EXT:')	{	// extension
			list($extKey,$local) = explode('/',substr($filename,4),2);
			$filename='';
			if (strcmp($extKey,'') && t3lib_extMgm::isLoaded($extKey) && strcmp($local,''))	{
				$filename = t3lib_extMgm::extPath($extKey).$local;
			}
		} elseif (!t3lib_div::isAbsPath($filename))	{	// relative. Prepended with $relPathPrefix
			$filename=$relPathPrefix.$filename;
		} elseif ($onlyRelative && !t3lib_div::isFirstPartOfStr($filename,$relPathPrefix)) {	// absolute, but set to blank if not allowed
			$filename='';
		}
		if (strcmp($filename,'') && t3lib_div::validPathStr($filename))	{	// checks backpath.
			return $filename;
		}
	}

	/**
	 * Checks for malicious file paths.
	 * Returns true if no '//', '..' or '\' is in the $theFile
	 * This should make sure that the path is not pointing 'backwards' and further doesn't contain double/back slashes.
	 * So it's compatible with the UNIX style path strings valid for TYPO3 internally.
	 * Usage: 14
	 *
	 * @param	string		Filepath to evaluate
	 * @return	boolean		True, if no '//', '..' or '\' is in the $theFile
	 * @todo	Possible improvement: Should it rawurldecode the string first to check if any of these characters is encoded ?
	 */
	function validPathStr($theFile)	{
		if (!strstr($theFile,'//') && !strstr($theFile,'..') && !strstr($theFile,'\\'))	return true;
	}

	/**
	 * Checks if the $path is absolute or relative (detecting either '/' or 'x:/' as first part of string) and returns true if so.
	 * Usage: 8
	 *
	 * @param	string		Filepath to evaluate
	 * @return	boolean
	 */
	function isAbsPath($path)	{
		return TYPO3_OS=='WIN' ? substr($path,1,2)==':/' :  substr($path,0,1)=='/';
	}

	/**
	 * Returns true if the path is absolute, without backpath '..' and within the PATH_site OR within the lockRootPath
	 * Usage: 5
	 *
	 * @param	string		Filepath to evaluate
	 * @return	boolean
	 */
	function isAllowedAbsPath($path)	{
		if (t3lib_div::isAbsPath($path) &&
			t3lib_div::validPathStr($path) &&
				(	t3lib_div::isFirstPartOfStr($path,PATH_site)
					||
					($GLOBALS['TYPO3_CONF_VARS']['BE']['lockRootPath'] && t3lib_div::isFirstPartOfStr($path,$GLOBALS['TYPO3_CONF_VARS']['BE']['lockRootPath']))
				)
			)	return true;
	}

	/**
	 * Verifies the input filename againts the 'fileDenyPattern'. Returns true if OK.
	 * Usage: 2
	 *
	 * @param	string		Filepath to evaluate
	 * @return	boolean
	 */
	function verifyFilenameAgainstDenyPattern($filename)	{
		if (strcmp($filename,'') && strcmp($GLOBALS['TYPO3_CONF_VARS']['BE']['fileDenyPattern'],''))	{
			$result = eregi($GLOBALS['TYPO3_CONF_VARS']['BE']['fileDenyPattern'],$filename);
			if ($result)	return false;	// so if a matching filename is found, return false;
		}
		return true;
	}

	/**
	 * Moves $source file to $destination if uploaded, otherwise try to make a copy
	 * Usage: 4
	 *
	 * @param	string		Source file, absolute path
	 * @param	string		Destination file, absolute path
	 * @return	boolean		Returns true if the file was moved.
	 * @coauthor	Dennis Petersen <fessor@software.dk>
	 * @see upload_to_tempfile()
	 */
	function upload_copy_move($source,$destination)	{
		if (is_uploaded_file($source))	{
			$uploaded = TRUE;
			// Return the value of move_uploaded_file, and if false the temporary $source is still around so the user can use unlink to delete it:
			$uploadedResult = move_uploaded_file($source, $destination);
		} else {
			$uploaded = FALSE;
			@copy($source,$destination);
		}

		t3lib_div::fixPermissions($destination);	// Change the permissions of the file

			// If here the file is copied and the temporary $source is still around, so when returning false the user can try unlink to delete the $source
		return $uploaded ? $uploadedResult : FALSE;
	}

	/**
	 * Will move an uploaded file (normally in "/tmp/xxxxx") to a temporary filename in PATH_site."typo3temp/" from where TYPO3 can use it under safe_mode.
	 * Use this function to move uploaded files to where you can work on them.
	 * REMEMBER to use t3lib_div::unlink_tempfile() afterwards - otherwise temp-files will build up! They are NOT automatically deleted in PATH_site."typo3temp/"!
	 * Usage: 6
	 *
	 * @param	string		The temporary uploaded filename, eg. $_FILES['[upload field name here]']['tmp_name']
	 * @return	string		If a new file was successfully created, return its filename, otherwise blank string.
	 * @see unlink_tempfile(), upload_copy_move()
	 */
	function upload_to_tempfile($uploadedFileName)	{
		if (is_uploaded_file($uploadedFileName))	{
			$tempFile = t3lib_div::tempnam('upload_temp_');
			move_uploaded_file($uploadedFileName, $tempFile);
			return @is_file($tempFile) ? $tempFile : '';
		}
	}

	/**
	 * Deletes (unlink) a temporary filename in 'PATH_site."typo3temp/"' given as input.
	 * The function will check that the file exists, is in PATH_site."typo3temp/" and does not contain back-spaces ("../") so it should be pretty safe.
	 * Use this after upload_to_tempfile() or tempnam() from this class!
	 * Usage: 9
	 *
	 * @param	string		Filepath for a file in PATH_site."typo3temp/". Must be absolute.
	 * @return	boolean		Returns true if the file was unlink()'ed
	 * @see upload_to_tempfile(), tempnam()
	 */
	function unlink_tempfile($uploadedTempFileName)	{
		if ($uploadedTempFileName && t3lib_div::validPathStr($uploadedTempFileName) && t3lib_div::isFirstPartOfStr($uploadedTempFileName,PATH_site.'typo3temp/') && @is_file($uploadedTempFileName))	{
			if (unlink($uploadedTempFileName))	return TRUE;
		}
	}

	/**
	 * Create temporary filename (Create file with unique file name)
	 * This function should be used for getting temporary filenames - will make your applications safe for open_basedir = on
	 * REMEMBER to delete the temporary files after use! This is done by t3lib_div::unlink_tempfile()
	 * Usage: 7
	 *
	 * @param	string		Prefix to temp file (which will have no extension btw)
	 * @return	string		result from PHP function tempnam() with PATH_site.'typo3temp/' set for temp path.
	 * @see unlink_tempfile(), upload_to_tempfile()
	 */
	function tempnam($filePrefix)	{
		return tempnam(PATH_site.'typo3temp/',$filePrefix);
	}

	/**
	 * standard authentication code - can't remember what it's used for.
	 * Usage: 2
	 *
	 * @param	mixed		Uid (integer) or record (array)
	 * @param	string		List of fields from the record if that is given.
	 * @return	string		MD5 hash of 8 chars.
	 * @internal
	 */
	function stdAuthCode($uid_or_record,$fields='')	{
		if (is_array($uid_or_record))	{
			$recCopy_temp=array();
			if ($fields)	{
				$fieldArr = t3lib_div::trimExplode(',',$fields,1);
				reset($fieldArr);
				while(list($k,$v)=each($fieldArr))	{
					$recCopy_temp[$k]=$recCopy[$v];
				}
			} else {
				$recCopy_temp=$recCopy;
			}
			$preKey = implode('|',$recCopy_temp);
		} else {
			$preKey = $uid_or_record;
		}

		$authCode = $preKey.'||'.$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];
		$authCode = substr(md5($authCode),0,8);
		return $authCode;
	}

	/**
	 * Splits the input query-parameters into an array with certain parameters filtered out.
	 * Used to create the cHash value
	 *
	 * @param	string		Query-parameters: "&xxx=yyy&zzz=uuu"
	 * @return	array		Array with key/value pairs of query-parameters WITHOUT a certain list of variable names (like id, type, no_cache etc) and WITH a variable, encryptionKey, specific for this server/installation
	 * @see tslib_fe::makeCacheHash(), tslib_cObj::typoLink()
	 */
	function cHashParams($addQueryParams) {
		$params = explode('&',substr($addQueryParams,1));	// Splitting parameters up

			// Make array:
		$pA = array();
		foreach($params as $theP)	{
			$pKV = explode('=', $theP);	// Splitting single param by '=' sign
			if (!t3lib_div::inList('id,type,no_cache,cHash,MP,ftu',$pKV[0]))	{
				$pA[$pKV[0]] = (string)rawurldecode($pKV[1]);
			}
		}
		$pA['encryptionKey'] = $GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey'];
		ksort($pA);

		return $pA;
	}

	/**
	 * Responds on input localization setting value whether the page it comes from should be hidden if no translation exists or not.
	 *
	 * @param	integer		Value from "l18n_cfg" field of a page record
	 * @return	boolean		True if the page should be hidden
	 */
	function hideIfNotTranslated($l18n_cfg_fieldValue)	{
		if ($GLOBALS['TYPO3_CONF_VARS']['FE']['hidePagesIfNotTranslatedByDefault'])	{
			return $l18n_cfg_fieldValue&2 ? FALSE : TRUE;
		} else {
			return $l18n_cfg_fieldValue&2 ? TRUE : FALSE;
		}
	}

	/**
	 * Includes a locallang file and returns the $LOCAL_LANG array found inside.
	 *
	 * @param	string		Input is a file-reference (see t3lib_div::getFileAbsFileName) which, if exists, is included. That file is expected to be a 'local_lang' file containing a $LOCAL_LANG array.
	 * @param	string		Language key
	 * @return	array		Value of $LOCAL_LANG found in the included file. If that array is found it's returned. Otherwise an empty array
	 */
	function readLLfile($fileRef,$langKey)	{
		$file = t3lib_div::getFileAbsFileName($fileRef);
		if ($file)	{
			$baseFile = ereg_replace('\.(php|xml)$', '', $file);

			if (@is_file($baseFile.'.xml'))	{
				$LOCAL_LANG = t3lib_div::readLLXMLfile($baseFile.'.xml', $langKey);
			} elseif (@is_file($baseFile.'.php'))	{
				include($baseFile.'.php');
			} else die('Filereference, "'.$file.'", not found!');
		}

		return is_array($LOCAL_LANG)?$LOCAL_LANG:array();
	}

	/**
	 * Includes a locallang-xml file and returns the $LOCAL_LANG array
	 * Works only when the frontend or backend has been initialized with a charset conversion object. See first code lines.
	 *
	 * @param	string		Absolute reference to locallang-XML file
	 * @param	string		TYPO3 language key, eg. "dk" or "de" or "default"
	 * @return	array		LOCAL_LANG array in return.
	 */
	function readLLXMLfile($fileRef,$langKey)	{

		if (is_object($GLOBALS['LANG']))	{
			$csConvObj = &$GLOBALS['LANG']->csConvObj;
		} elseif (is_object($GLOBALS['TSFE']))	{
			$csConvObj = &$GLOBALS['TSFE']->csConvObj;
		} else $csConvObj = NULL;

		if (@is_file($fileRef) && $langKey && is_object($csConvObj))	{

				// Set charset:
			$origCharset = $csConvObj->parse_charset($csConvObj->charSetArray[$langKey] ? $csConvObj->charSetArray[$langKey] : 'iso-8859-1');

				// Cache file name:
			$hashSource = substr($fileRef,strlen(PATH_site)).'|'.date('d-m-Y H:i:s',filemtime($fileRef));
			$cacheFileName = PATH_site.'typo3temp/llxml/'.
							#str_replace('_','',ereg_replace('^.*\/','',dirname($fileRef))).
							#'_'.basename($fileRef).
							substr(basename($fileRef),10,15).
							'_'.t3lib_div::shortMD5($hashSource).'.'.$langKey.'.'.$origCharset.'.cache';

				// Check if cache file exists...
			if (!@is_file($cacheFileName))	{	// ... if it doesn't, create content and write it:

					// Read XML, parse it.
				$xmlString = t3lib_div::getUrl($fileRef);
				$xmlContent = t3lib_div::xml2array($xmlString);

					// Set default LOCAL_LANG array content:
				$LOCAL_LANG = array();
				$LOCAL_LANG['default'] = $xmlContent['data']['default'];

					// Specific language, convert from utf-8 to backend language charset:
					// NOTICE: Converting from utf-8 back to "native" language may be a temporary solution until we can totally discard "locallang.php" files altogether (and use utf-8 for everything). But doing this conversion is the quickest way to migrate now and the source is in utf-8 anyway which is the main point.
				if ($langKey!='default')	{
					$LOCAL_LANG[$langKey] = $xmlContent['data'][$langKey];

						// Checking if charset should be converted.
					if (is_array($LOCAL_LANG[$langKey]) && $origCharset!='utf-8')	{
						foreach($LOCAL_LANG[$langKey] as $labelKey => $labelValue)	{
							$LOCAL_LANG[$langKey][$labelKey] = $csConvObj->utf8_decode($labelValue,$origCharset);
						}
					}
				}

					// Cache the content now:
				$serContent = array('origFile'=>$hashSource, 'LOCAL_LANG'=>$LOCAL_LANG);
				$res = t3lib_div::writeFileToTypo3tempDir($cacheFileName, serialize($serContent));
				if ($res)	die('ERROR: '.$res);
			} else {
					// Get content from cache:
				$serContent = unserialize(t3lib_div::getUrl($cacheFileName));
				$LOCAL_LANG = $serContent['LOCAL_LANG'];
			}

				// Checking for EXTERNAL file for non-default language:
			if ($langKey!='default' && is_string($LOCAL_LANG[$langKey]) && strlen($LOCAL_LANG[$langKey]))	{

					// Look for localized file:
				$localized_file = t3lib_div::getFileAbsFileName($LOCAL_LANG[$langKey]);
				if ($localized_file && @is_file($localized_file))	{

						// Cache file name:
					$hashSource = substr($localized_file,strlen(PATH_site)).'|'.date('d-m-Y H:i:s',filemtime($localized_file));
					$cacheFileName = PATH_site.'typo3temp/llxml/ext_'.
									substr(basename($localized_file),10,15).
									'_'.t3lib_div::shortMD5($hashSource).'.'.$langKey.'.'.$origCharset.'.cache';

						// Check if cache file exists...
					if (!@is_file($cacheFileName))	{	// ... if it doesn't, create content and write it:

							// Read and parse XML content:
						$local_xmlString = t3lib_div::getUrl($localized_file);
						$local_xmlContent = t3lib_div::xml2array($local_xmlString);
						$LOCAL_LANG[$langKey] = is_array($local_xmlContent['data'][$langKey]) ? $local_xmlContent['data'][$langKey] : array();

							// Checking if charset should be converted.
						if (is_array($LOCAL_LANG[$langKey]) && $origCharset!='utf-8')	{
							foreach($LOCAL_LANG[$langKey] as $labelKey => $labelValue)	{
								$LOCAL_LANG[$langKey][$labelKey] = $csConvObj->utf8_decode($labelValue,$origCharset);
							}
						}

							// Cache the content now:
						$serContent = array('extlang'=>$langKey, 'origFile'=>$LOCAL_LANG[$langKey], 'EXT_DATA'=>$LOCAL_LANG[$langKey]);
						$res = t3lib_div::writeFileToTypo3tempDir($cacheFileName, serialize($serContent));
						if ($res)	die('ERROR: '.$res);
					} else {
							// Get content from cache:
						$serContent = unserialize(t3lib_div::getUrl($cacheFileName));
						$LOCAL_LANG[$langKey] = $serContent['EXT_DATA'];
					}
				} else {
					$LOCAL_LANG[$langKey] = array();
				}
			}

			return $LOCAL_LANG;
		}
	}

	/**
	 * Loads the $TCA (Table Configuration Array) for the $table
	 *
	 * Requirements:
	 * 1) must be configured table (the ctrl-section configured),
	 * 2) columns must not be an array (which it is always if whole table loaded), and
	 * 3) there is a value for dynamicConfigFile (filename in typo3conf)
	 * Usage: 84
	 *
	 * @param	string		Table name for which to load the full TCA array part into the global $TCA
	 * @return	void
	 */
	function loadTCA($table)	{
		global $TCA,$LANG_GENERAL_LABELS;
		if (isset($TCA[$table]) && !is_array($TCA[$table]['columns']) && $TCA[$table]['ctrl']['dynamicConfigFile'])	{
			if (!strcmp(substr($TCA[$table]['ctrl']['dynamicConfigFile'],0,6),'T3LIB:'))	{
				include(PATH_t3lib.'stddb/'.substr($TCA[$table]['ctrl']['dynamicConfigFile'],6));
			} elseif (t3lib_div::isAbsPath($TCA[$table]['ctrl']['dynamicConfigFile']) && @is_file($TCA[$table]['ctrl']['dynamicConfigFile']))	{	// Absolute path...
				include($TCA[$table]['ctrl']['dynamicConfigFile']);
			} else include(PATH_typo3conf.$TCA[$table]['ctrl']['dynamicConfigFile']);
		}
	}

	/**
	 * Looks for a sheet-definition in the input data structure array. If found it will return the data structure for the sheet given as $sheet (if found).
	 * If the sheet definition is in an external file that file is parsed and the data structure inside of that is returned.
	 * Usage: 5
	 *
	 * @param	array		Input data structure, possibly with a sheet-definition and references to external data source files.
	 * @param	string		The sheet to return, preferably.
	 * @return	array		An array with two num. keys: key0: The data structure is returned in this key (array) UNLESS an error happend in which case an error string is returned (string). key1: The used sheet key value!
	 */
	function resolveSheetDefInDS($dataStructArray,$sheet='sDEF')	{
		if (is_array($dataStructArray['sheets']))	{
			$singleSheet = FALSE;
			if (!isset($dataStructArray['sheets'][$sheet]))	{
				$sheet='sDEF';
			}
			$dataStruct =  $dataStructArray['sheets'][$sheet];

				// If not an array, but still set, then regard it as a relative reference to a file:
			if ($dataStruct && !is_array($dataStruct))	{
				$file = t3lib_div::getFileAbsFileName($dataStruct);
				if ($file && @is_file($file))	{
					$dataStruct = t3lib_div::xml2array(t3lib_div::getUrl($file));
				}
			}
		} else {
			$singleSheet = TRUE;
			$dataStruct = $dataStructArray;
			unset($dataStruct['meta']);	// Meta data should not appear there.
			$sheet = 'sDEF';	// Default sheet
		}
		return array($dataStruct,$sheet,$singleSheet);
	}

	/**
	 * Resolves ALL sheet definitions in dataStructArray
	 * If no sheet is found, then the default "sDEF" will be created with the dataStructure inside.
	 *
	 * @param	array		Input data structure, possibly with a sheet-definition and references to external data source files.
	 * @return	array		Output data structure with all sheets resolved as arrays.
	 */
	function resolveAllSheetsInDS($dataStructArray)	{
		if (is_array($dataStructArray['sheets']))	{
			$out=array('sheets'=>array());
			foreach($dataStructArray['sheets'] as $sheetId => $sDat)	{
				list($ds,$aS) = t3lib_div::resolveSheetDefInDS($dataStructArray,$sheetId);
				if ($sheetId==$aS)	{
					$out['sheets'][$aS]=$ds;
				}
			}
		} else {
			list($ds) = t3lib_div::resolveSheetDefInDS($dataStructArray);
			$out = array('sheets' => array('sDEF' => $ds));
		}
		return $out;
	}

	/**
	 * Calls a userdefined function/method in class
	 * Such a function/method should look like this: "function proc(&$params, &$ref)	{...}"
	 * Usage: 17
	 *
	 * @param	string		Function/Method reference, '[file-reference":"]["&"]class/function["->"method-name]'. You can prefix this reference with "[file-reference]:" and t3lib_div::getFileAbsFileName() will then be used to resolve the filename and subsequently include it by "require_once()" which means you don't have to worry about including the class file either! Example: "EXT:realurl/class.tx_realurl.php:&tx_realurl->encodeSpURL". Finally; you can prefix the class name with "&" if you want to reuse a former instance of the same object call ("singleton").
	 * @param	mixed		Parameters to be pass along (typically an array) (REFERENCE!)
	 * @param	mixed		Reference to be passed along (typically "$this" - being a reference to the calling object) (REFERENCE!)
	 * @param	string		Required prefix of class or function name
	 * @param	boolean		If set, no debug() error message is shown if class/function is not present.
	 * @return	mixed		Content from method/function call
	 * @see getUserObj()
	 */
	function callUserFunction($funcName,&$params,&$ref,$checkPrefix='user_',$silent=0)	{
		global $TYPO3_CONF_VARS;

			// Check persistent object and if found, call directly and exit.
		if (is_array($GLOBALS['T3_VAR']['callUserFunction'][$funcName]))	{
			return call_user_method(
						$GLOBALS['T3_VAR']['callUserFunction'][$funcName]['method'],
						$GLOBALS['T3_VAR']['callUserFunction'][$funcName]['obj'],
						$params,
						$ref
					);
		}

			// Check file-reference prefix; if found, require_once() the file (should be library of code)
		if (strstr($funcName,':'))	{
			list($file,$funcRef) = t3lib_div::revExplode(':',$funcName,2);
			$requireFile = t3lib_div::getFileAbsFileName($file);
			if ($requireFile) require_once($requireFile);
		} else {
			$funcRef = $funcName;
		}

			// Check for persistent object token, "&"
		if (substr($funcRef,0,1)=='&')	{
			$funcRef = substr($funcRef,1);
			$storePersistentObject = TRUE;
		} else {
			$storePersistentObject = FALSE;
		}

			// Check prefix is valid:
		if ($checkPrefix &&
			!t3lib_div::isFirstPartOfStr(trim($funcRef),$checkPrefix) &&
			!t3lib_div::isFirstPartOfStr(trim($funcRef),'tx_')
			)	{
			if (!$silent)	debug("Function/Class '".$funcRef."' was not prepended with '".$checkPrefix."'",1);
			return FALSE;
		}

			// Call function or method:
		$parts = explode('->',$funcRef);
		if (count($parts)==2)	{	// Class

				// Check if class/method exists:
			if (class_exists($parts[0]))	{

					// Get/Create object of class:
				if ($storePersistentObject)	{	// Get reference to current instance of class:
					if (!is_object($GLOBALS['T3_VAR']['callUserFunction_classPool'][$parts[0]]))	{
						$GLOBALS['T3_VAR']['callUserFunction_classPool'][$parts[0]] = &t3lib_div::makeInstance($parts[0]);
					}
					$classObj = &$GLOBALS['T3_VAR']['callUserFunction_classPool'][$parts[0]];
				} else {	// Create new object:
					$classObj = &t3lib_div::makeInstance($parts[0]);
				}

				if (method_exists($classObj, $parts[1]))	{

						// If persistent object should be created, set reference:
					if ($storePersistentObject)	{
						$GLOBALS['T3_VAR']['callUserFunction'][$funcName] = array (
							'method' => $parts[1],
							'obj' => &$classObj
						);
					}
						// Call method:
					$content = call_user_method(
						$parts[1],
						$classObj,
						$params,
						$ref
					);
				} else {
					if (!$silent)	debug("<strong>ERROR:</strong> No method name '".$parts[1]."' in class ".$parts[0],1);
				}
			} else {
				if (!$silent)	debug("<strong>ERROR:</strong> No class named: ".$parts[0],1);
			}
		} else {	// Function
			if (function_exists($funcRef))	{
			 	$content = call_user_func($funcRef, $params, $ref);
			} else {
				if (!$silent)	debug("<strong>ERROR:</strong> No function named: ".$funcRef,1);
			}
		}
		return $content;
	}

	/**
	 * Creates and returns reference to a user defined object.
	 * This function can return an object reference if you like. Just prefix the function call with "&": "$objRef = &t3lib_div::getUserObj('EXT:myext/class.tx_myext_myclass.php:&tx_myext_myclass');". This will work ONLY if you prefix the class name with "&" as well. See description of function arguments.
	 * Usage: 5
	 *
	 * @param	string		Class reference, '[file-reference":"]["&"]class-name'. You can prefix the class name with "[file-reference]:" and t3lib_div::getFileAbsFileName() will then be used to resolve the filename and subsequently include it by "require_once()" which means you don't have to worry about including the class file either! Example: "EXT:realurl/class.tx_realurl.php:&tx_realurl". Finally; for the class name you can prefix it with "&" and you will reuse the previous instance of the object identified by the full reference string (meaning; if you ask for the same $classRef later in another place in the code you will get a reference to the first created one!).
	 * @param	string		Required prefix of class name. By default "tx_" is allowed.
	 * @param	boolean		If set, no debug() error message is shown if class/function is not present.
	 * @return	object		The instance of the class asked for. Instance is created with t3lib_div::makeInstance
	 * @see callUserFunction()
	 */
	function &getUserObj($classRef,$checkPrefix='user_',$silent=0)	{
		global $TYPO3_CONF_VARS;
			// Check persistent object and if found, call directly and exit.
		if (is_object($GLOBALS['T3_VAR']['getUserObj'][$classRef]))	{
			return $GLOBALS['T3_VAR']['getUserObj'][$classRef];
		} else {

				// Check file-reference prefix; if found, require_once() the file (should be library of code)
			if (strstr($classRef,':'))	{
				list($file,$class) = t3lib_div::revExplode(':',$classRef,2);
				$requireFile = t3lib_div::getFileAbsFileName($file);
				if ($requireFile)	require_once($requireFile);
			} else {
				$class = $classRef;
			}

				// Check for persistent object token, "&"
			if (substr($class,0,1)=='&')	{
				$class = substr($class,1);
				$storePersistentObject = TRUE;
			} else {
				$storePersistentObject = FALSE;
			}

				// Check prefix is valid:
			if ($checkPrefix &&
				!t3lib_div::isFirstPartOfStr(trim($class),$checkPrefix) &&
				!t3lib_div::isFirstPartOfStr(trim($class),'tx_')
				)	{
				if (!$silent)	debug("Class '".$class."' was not prepended with '".$checkPrefix."'",1);
				return FALSE;
			}

				// Check if class exists:
			if (class_exists($class))	{
				$classObj = &t3lib_div::makeInstance($class);

					// If persistent object should be created, set reference:
				if ($storePersistentObject)	{
					$GLOBALS['T3_VAR']['getUserObj'][$classRef] = &$classObj;
				}

				return $classObj;
			} else {
				if (!$silent)	debug("<strong>ERROR:</strong> No class named: ".$class,1);
			}
		}
	}

	/**
	 * Make instance of class
	 * Takes the class-extensions API of TYPO3 into account
	 * Please USE THIS instead of the PHP "new" keyword. Eg. "$obj = new myclass;" should be "$obj = t3lib_div::makeInstance("myclass")" instead!
	 * Usage: 447
	 *
	 * @param	string		Class name to instantiate
	 * @return	object		The object
	 */
	function &makeInstance($className)	{
		return class_exists('ux_'.$className) ? t3lib_div::makeInstance('ux_'.$className) : new $className;
	}

	/**
	 * Return classname for new instance
	 * Takes the class-extensions API of TYPO3 into account
	 * Usage: 17
	 *
	 * @param	string		Base Class name to evaluate
	 * @return	string		Final class name to instantiate with "new [classname]"
	 */
	function makeInstanceClassName($className)	{
		return class_exists('ux_'.$className) ? t3lib_div::makeInstanceClassName('ux_'.$className) : $className;
	}

	/**
	 * Find the best service and check if it works.
	 * Returns object of the service class.
	 *
	 * @param	string		Type of service (service key).
	 * @param	string		Sub type like file extensions or similar. Defined by the service.
	 * @param	mixed		List of service keys which should be exluded in the search for a service. Array or comma list.
	 * @return	object		The service object or an array with error info's.
	 * @author	René Fritz <r.fritz@colorcube.de>
	 */
	function &makeInstanceService($serviceType, $serviceSubType='', $excludeServiceKeys=array())	{
		global $T3_SERVICES, $T3_VAR, $TYPO3_CONF_VARS;

		$error = FALSE;

		if (!is_array($excludeServiceKeys) ) {
			$excludeServiceKeys = t3lib_div::trimExplode(',', $excludeServiceKeys, 1);
		}
		while ($info = t3lib_extMgm::findService($serviceType, $serviceSubType, $excludeServiceKeys))	{

				// Check persistent object and if found, call directly and exit.
			if (is_object($GLOBALS['T3_VAR']['makeInstanceService'][$info['className']]))	{
					// reset service and return object
				$T3_VAR['makeInstanceService'][$info['className']]->reset();
				return $GLOBALS['T3_VAR']['makeInstanceService'][$info['className']];

				// include file and create object
			} else {
				$requireFile = t3lib_div::getFileAbsFileName($info['classFile']);
				if (@is_file($requireFile)) {
					require_once ($requireFile);
					$obj = t3lib_div::makeInstance($info['className']);
					if (is_object($obj)) {
						if(!@is_callable(array($obj,'init')))	{
								// use silent logging??? I don't think so.
							die ('Broken service:'.t3lib_div::view_array($info));
						}
						$obj->info = $info;
						if ($obj->init()) { // service available?

								// create persistent object
							$T3_VAR['makeInstanceService'][$info['className']] = &$obj;

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
			t3lib_extMgm::deactivateService($info['serviceType'],$info['serviceKey']);
		}
		return $error;
	}

	/**
	 * Simple substitute for the PHP function mail() which allows you to specify encoding and character set
	 * The fifth parameter, $enc, will allow you to specify 'base64' encryption for the output (set $enc=base64)
	 * Further the output has the charset set to ISO-8859-1 by default.
	 * Usage: 4
	 *
	 * @param	string		Email address to send to. (see PHP function mail())
	 * @param	string		Subject line, non-encoded. (see PHP function mail())
	 * @param	string		Message content, non-encoded. (see PHP function mail())
	 * @param	string		Headers, separated by chr(10)
	 * @param	string		Encoding type: "base64", "quoted-printable", "8bit". If blank, no encoding will be used, no encoding headers set.
	 * @param	string		Charset used in encoding-headers (only if $enc is set to a valid value which produces such a header)
	 * @param	boolean		If set, the content of $subject will not be encoded.
	 * @return	void
	 */
	function plainMailEncoded($email,$subject,$message,$headers='',$enc='',$charset='ISO-8859-1',$dontEncodeSubject=0)	{
		switch((string)$enc)	{
			case 'base64':
				$headers=trim($headers).chr(10).
				'Mime-Version: 1.0'.chr(10).
				'Content-Type: text/plain; charset="'.$charset.'"'.chr(10).
				'Content-Transfer-Encoding: base64';

				$message=trim(chunk_split(base64_encode($message.chr(10)))).chr(10);	// Adding chr(10) because I think MS outlook 2002 wants it... may be removed later again.

				if (!$dontEncodeSubject)	$subject='=?'.$charset.'?B?'.base64_encode($subject).'?=';
			break;
			case 'quoted-printable':
				$headers=trim($headers).chr(10).
				'Mime-Version: 1.0'.chr(10).
				'Content-Type: text/plain; charset="'.$charset.'"'.chr(10).
				'Content-Transfer-Encoding: quoted-printable';

				$message=t3lib_div::quoted_printable($message);

				if (!$dontEncodeSubject)	$subject='=?'.$charset.'?Q?'.trim(t3lib_div::quoted_printable(ereg_replace('[[:space:]]','_',$subject),1000)).'?=';
			break;
			case '8bit':
				$headers=trim($headers).chr(10).
				'Mime-Version: 1.0'.chr(10).
				'Content-Type: text/plain; charset="'.$charset.'"'.chr(10).
				'Content-Transfer-Encoding: 8bit';
			break;
		}
		$headers=trim(implode(chr(10),t3lib_div::trimExplode(chr(10),$headers,1)));	// make sure no empty lines are there.

		mail($email,$subject,$message,$headers);
	}

	/**
	 * Implementation of quoted-printable encode.
	 * This functions is buggy. It seems that in the part where the lines are breaked every 76th character, that it fails if the break happens right in a quoted_printable encode character!
	 * Important: For the ease of use, this function internally uses Unix linebreaks ("\n") for breaking lines, but the output must use "\r\n" instead!
	 * See RFC 1521, section 5.1 Quoted-Printable Content-Transfer-Encoding
	 * Usage: 2
	 *
	 * @param	string		Content to encode
	 * @param	integer		Length of the lines, default is 76
	 * @return	string		The QP encoded string
	 */
	function quoted_printable($string,$maxlen=76)	{
			// Make sure the string contains only Unix linebreaks
		$string = str_replace(chr(13).chr(10), chr(10), $string);	// Replace Windows breaks (\r\n)
		$string = str_replace(chr(13), chr(10), $string);		// Replace Mac breaks (\r)

		$newString = '';
		$theLines = explode(chr(10),$string);	// Split lines
		foreach($theLines as $val)	{
			$newVal = '';
			$theValLen = strlen($val);
			$len = 0;
			for ($index=0; $index < $theValLen; $index++)	{	// Walk through each character of this line
				$char = substr($val,$index,1);
				$ordVal = ord($char);
				if ($len>($maxlen-4) || ($len>(($maxlen-10)-4)&&$ordVal==32))	{
					$newVal.='='.chr(13).chr(10);	// Add a line break
					$len=0;			// Reset the length counter
				}
				if (($ordVal>=33 && $ordVal<=60) || ($ordVal>=62 && $ordVal<=126) || $ordVal==9 || $ordVal==32)	{
					$newVal.=$char;		// This character is ok, add it to the message
					$len++;
				} else {
					$newVal.=sprintf('=%02X',$ordVal);	// Special character, needs to be encoded
					$len+=3;
				}
			}
			$newVal = ereg_replace(chr(32).'$','=20',$newVal);		// Replaces a possible SPACE-character at the end of a line
			$newVal = ereg_replace(chr(9).'$','=09',$newVal);		// Replaces a possible TAB-character at the end of a line
			$newString.=$newVal.chr(13).chr(10);
		}
		return ereg_replace(chr(13).chr(10).'$','',$newString);
	}

	/**
	 * Takes a clear-text message body for a plain text email, finds all 'http://' links and if they are longer than 76 chars they are converted to a shorter URL with a hash parameter. The real parameter is stored in the database and the hash-parameter/URL will be redirected to the real parameter when the link is clicked.
	 * This function is about preserving long links in messages.
	 * Usage: 3
	 *
	 * @param	string		Message content
	 * @param	string		URL mode; "76" or "all"
	 * @param	string		URL of index script (see makeRedirectUrl())
	 * @return	string		Processed message content
	 * @see makeRedirectUrl()
	 */
	function substUrlsInPlainText($message,$urlmode='76',$index_script_url='')	{
			// Substitute URLs with shorter links:
		$urlSplit=explode('http://',$message);
		reset($urlSplit);
		while(list($c,$v)=each($urlSplit))	{
			if ($c)	{
				$newParts = split('[[:space:]]|\)|\(',$v,2);
				$newURL='http://'.$newParts[0];
					switch((string)$urlmode)	{
						case 'all':
							$newURL=t3lib_div::makeRedirectUrl($newURL,0,$index_script_url);
						break;
						case '76':
							$newURL=t3lib_div::makeRedirectUrl($newURL,76,$index_script_url);
						break;
					}
				$urlSplit[$c]=$newURL.substr($v,strlen($newParts[0]));
			}
		}

		$message=implode('',$urlSplit);
		return $message;
	}

	/**
	 * Subfunction for substUrlsInPlainText() above.
	 * Usage: 2
	 *
	 * @param	string		Input URL
	 * @param	integer		URL string length limit
	 * @param	string		URL of "index script" - the prefix of the "?RDCT=..." parameter. If not supplyed it will default to t3lib_div::getIndpEnv('TYPO3_REQUEST_DIR')
	 * @return	string		Processed URL
	 * @internal
	 */
	function makeRedirectUrl($inUrl,$l=0,$index_script_url='')	{
		if (strlen($inUrl)>$l)	{
			$md5 = substr(md5($inUrl),0,20);
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('md5hash', 'cache_md5params', 'md5hash='.$GLOBALS['TYPO3_DB']->fullQuoteStr($md5, 'cache_md5params'));
			if (!$GLOBALS['TYPO3_DB']->sql_num_rows($res))	{
				$insertFields = array(
					'md5hash' => $md5,
					'tstamp' => time(),
					'type' => 2,
					'params' => $inUrl
				);

				$GLOBALS['TYPO3_DB']->exec_INSERTquery('cache_md5params', $insertFields);
			}
			$inUrl=($index_script_url ? $index_script_url : t3lib_div::getIndpEnv('TYPO3_REQUEST_DIR')).
				'?RDCT='.$md5;
		}
		return $inUrl;
	}

	/**
	 * Function to compensate for FreeType2 96 dpi
	 * Usage: 21
	 *
	 * @param	integer		Fontsize for freetype function call
	 * @return	integer		Compensated fontsize based on $GLOBALS['TYPO3_CONF_VARS']['GFX']['TTFdpi']
	 */
	function freetypeDpiComp($font_size)	{
		$dpi = intval($GLOBALS['TYPO3_CONF_VARS']['GFX']['TTFdpi']);
		if ($dpi!=72)	$font_size = $font_size/$dpi*72;
		return $font_size;
	}

	/**
	 * Developer log; This should be implemented around the source code, both frontend and backend, logging everything from the flow through an application, messages, results from comparisons to fatal errors.
	 * The result is meant to make sense to developers during development or debugging of a site.
	 * The idea is that this function is only a wrapper for external extensions which can set a hook which will be allowed to handle the logging of the information to any format they might wish and with any kind of filter they would like.
	 * If you want to implement the devLog in your applications, simply add lines like:
	 * 		if (TYPO3_DLOG)	t3lib_div::devLog('[write message in english here]', 'extension key');
	 *
	 * @param	string		Message (in english).
	 * @param	string		Extension key (from which extension you are calling the log)
	 * @param	integer		Severity: 0 is info, 1 is notice, 2 is warning, 3 is fatal error, -1 is "OK" message
	 * @param	array		Additional data you want to pass to the logger.
	 * @return	void
	 */
	function devLog($msg, $extKey, $severity=0, $dataVar=FALSE)	{
		global $TYPO3_CONF_VARS;

		if (is_array($TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog']))	{
			$params = array('msg'=>$msg, 'extKey'=>$extKey, 'severity'=>$severity, 'dataVar'=>$dataVar);
			$fakeThis = FALSE;
			foreach($TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'] as $hookMethod)	{
				t3lib_div::callUserFunction($hookMethod,$params,$fakeThis);
			}
		}
	}

	/**
	 * Converts a one dimensional array to a one line string which can be used for logging or debugging output
	 * Example: "loginType: FE; refInfo: Array; HTTP_HOST: www.example.org; REMOTE_ADDR: 192.168.1.5; REMOTE_HOST:; security_level:; showHiddenRecords: 0;"
	 *
	 * @param	array		Data array which should be outputted
	 * @param	mixed		List of keys which should be listed in the output string. Pass a comma list or an array. An empty list outputs the whole array.
	 * @param	integer		Long string values are shortened to this length. Default: 20
	 * @return	string		Output string with key names and their value as string
	 */
	function arrayToLogString($arr, $valueList=array(), $valueLength=20) {
		$str = '';
		if(is_array($arr)) {
			if (!is_array($valueList)) {
				$valueList = explode(',', $valueList);
			}
			foreach($arr as $key => $value) {
				if (!count($valueList) OR (count($valueList) AND in_array($key, $valueList))) {
					$str .= (string)$key.trim(': '.t3lib_div::fixed_lgd(str_replace("\n",'|',(string)$value), $valueLength)).'; ';
				}
			}
		}
		return $str;
	}

	/**
	 * Compile the command for running ImageMagick/GraphicsMagick.
	 *
	 * @param	string		Command to be run
	 * @param	string		The parameters string
	 * @param	string		Override the default path
	 * @return	string		Compiled command that deals with IM6 & GraphicsMagick
	 */
	function imageMagickCommand($command, $parameters, $path='')	{
		$gfxConf = $GLOBALS['TYPO3_CONF_VARS']['GFX'];
		$isExt = (TYPO3_OS=='WIN' ? '.exe' : '');
		$switchCompositeParameters=false;

		if(!$path)	{ $path = $gfxConf['im_path']; }

		$im_version = strtolower($gfxConf['im_version_5']);
		$combineScript = $gfxConf['im_combine_filename'] ? trim($gfxConf['im_combine_filename']) : 'combine';

		if($command==='combine')	{	// This is only used internally, has no effect outside
			$command = 'composite';
}

			// Compile the path & command
		if($im_version==='gm')	{
			$switchCompositeParameters=true;
			$path .= 'gm'.$isExt.' '.$command;
		} else	{
			if($im_version==='im6')	{ $switchCompositeParameters=true; }
			$path .= (($command=='composite') ? $combineScript : $command).$isExt;
		}

		$cmdLine = $path.' '.$parameters;

		if($command=='composite' && $switchCompositeParameters)	{	// Because of some weird incompatibilities between ImageMagick 4 and 6 (plus GraphicsMagick), it is needed to change the parameters order under some preconditions
			$paramsArr = t3lib_div::unQuoteFilenames($parameters);

			if(count($paramsArr)==6)	{	// The mask image has been specified => swap the parameters
				$tmp = $paramsArr[3];
				$paramsArr[3] = $paramsArr[2];
				$paramsArr[2] = $tmp;
			}

			$cmdLine = $path.' '.implode(' ', $paramsArr);
		}

		return $cmdLine;
	}

	/**
	 * Explode a string (normally a list of filenames) with whitespaces by considering quotes in that string. This is mostly needed by the imageMagickCommand function above.
	 *
	 * @param	string		The whole parameters string
	 * @return	array		Exploded parameters
	 */
	function unQuoteFilenames($parameters)	{
		$paramsArr = explode(' ', trim($parameters));

		$quoteActive = -1;	// Whenever a quote character (") is found, $quoteActive is set to the element number inside of $params. A value of -1 means that there are not open quotes at the current position.
		foreach($paramsArr as $k=>$v)	{
			if($quoteActive > -1)	{
				$paramsArr[$quoteActive] .= ' '.$v;
				unset($paramsArr[$k]);
				if(ereg('"$', $v))	{ $quoteActive = -1; }

			} elseif(!trim($v))	{
				unset($paramsArr[$k]);	// Remove empty elements

			} elseif(ereg('^"', $v))	{
				$quoteActive = $k;
			}
		}

		return $paramsArr;
	}
}

?>