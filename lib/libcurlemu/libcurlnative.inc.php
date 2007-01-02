<?php
/* CURL Extension Emulation Library (Native PHP)
 * Copyright 2004-2005, Steve Blinch
 * http://code.blitzaffe.com
 * ============================================================================
 *
 * DESCRIPTION
 *
 * Provides a pure-PHP implementation of the PHP CURL extension, for use on
 * systems which do not already have the CURL extension installed.  It emulates
 * all of the curl_* functions normally provided by the CURL extension itself,
 * and uses an internal, native-PHP HTTP library to make requests.
 *
 * This library will automatically detect whether or not the "real" CURL
 * extension is installed, and if so, it will not interfere.  Thus, it can be
 * used to ensure that, one way or another, the CURL functions are available
 * for use.
 *
 * Note that this is only a *rough* emulation of CURL; it is not exact, and
 * many of CURL's options are not implemented.  For a more precise emulation of
 * CURL, you may want to try our other libcurlexternal library which is based on
 * the CURL console binary (and is virtually identical to the CURL extension).
 *
 *
 * USAGE
 *
 * Please see the PHP documentation under the "CURL, Client URL Library 
 * Functions" section for information about using this library.  Almost all of
 * the documentation and examples in the PHP manual should work with this
 * library.
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
 
// if the real CURL PHP extension is installed, exit without doing anything;
// if libcurlemu is installed and providing a wrapper for the CURL binary,
// exit without doing anything
if (!extension_loaded("curl") && !function_exists("curl_init")) {


// if the CURL binary was not found, do one of the following:
//   - if CURLNAT_MISSING_ABORT was defined, then exit without
//     implementing the CURL functions
//   - otherwise, raise a fatal error and halt the script
if (!class_exists("HTTPRetriever")) {
	if (is_readable(dirname(__FILE__)."/class_HTTPRetriever.php")) {
		define("HTTPR_NO_REDECLARE_CURL",true);
		require_once(dirname(__FILE__)."/class_HTTPRetriever.php");
	} else {
	 	if (defined("CURLNAT_MISSING_ABORT") && CURLNAT_MISSING_ABORT) {
	 		return;
	 	} else {
			trigger_error("CURL extension is not loaded, libcurlemu is not loaded, and the HTTPRetriever class is unavailable",E_USER_ERROR);
		}
	}
}

define("CURLNAT_VERSION","1.0.0");

define('CURLOPT_NOTHING',0);
define('CURLOPT_FILE',10001);
define('CURLOPT_URL',10002);
define('CURLOPT_PORT',3);
define('CURLOPT_PROXY',10004);
define('CURLOPT_USERPWD',10005);
define('CURLOPT_PROXYUSERPWD',10006);
define('CURLOPT_RANGE',10007);
define('CURLOPT_INFILE',10009);
define('CURLOPT_ERRORBUFFER',10010);
define('CURLOPT_WRITEFUNCTION',20011);
define('CURLOPT_READFUNCTION',20012);
define('CURLOPT_TIMEOUT',13);
define('CURLOPT_INFILESIZE',14);
define('CURLOPT_POSTFIELDS',10015);
define('CURLOPT_REFERER',10016);
define('CURLOPT_FTPPORT',10017);
define('CURLOPT_USERAGENT',10018);
define('CURLOPT_LOW_SPEED_LIMIT',19);
define('CURLOPT_LOW_SPEED_TIME',20);
define('CURLOPT_RESUME_FROM',21);
define('CURLOPT_COOKIE',10022);
define('CURLOPT_HTTPHEADER',10023);
define('CURLOPT_HTTPPOST',10024);
define('CURLOPT_SSLCERT',10025);
define('CURLOPT_SSLCERTPASSWD',10026);
define('CURLOPT_SSLKEYPASSWD',10026);
define('CURLOPT_CRLF',27);
define('CURLOPT_QUOTE',10028);
define('CURLOPT_WRITEHEADER',10029);
define('CURLOPT_COOKIEFILE',10031);
define('CURLOPT_SSLVERSION',32);
define('CURLOPT_TIMECONDITION',33);
define('CURLOPT_TIMEVALUE',34);
define('CURLOPT_HTTPREQUEST',10035);
define('CURLOPT_CUSTOMREQUEST',10036);
define('CURLOPT_STDERR',10037);
define('CURLOPT_POSTQUOTE',10039);
define('CURLOPT_WRITEINFO',10040);
define('CURLOPT_VERBOSE',41);
define('CURLOPT_HEADER',42);
define('CURLOPT_NOPROGRESS',43);
define('CURLOPT_NOBODY',44);
define('CURLOPT_FAILONERROR',45);
define('CURLOPT_UPLOAD',46);
define('CURLOPT_POST',47);
define('CURLOPT_FTPLISTONLY',48);
define('CURLOPT_FTPAPPEND',50);
define('CURLOPT_NETRC',51);
define('CURLOPT_FOLLOWLOCATION',52);
define('CURLOPT_FTPASCII',53);
define('CURLOPT_TRANSFERTEXT',53);
define('CURLOPT_PUT',54);
define('CURLOPT_MUTE',55);
define('CURLOPT_PROGRESSFUNCTION',20056);
define('CURLOPT_PROGRESSDATA',10057);
define('CURLOPT_AUTOREFERER',58);
define('CURLOPT_PROXYPORT',59);
define('CURLOPT_POSTFIELDSIZE',60);
define('CURLOPT_HTTPPROXYTUNNEL',61);
define('CURLOPT_INTERFACE',10062);
define('CURLOPT_KRB4LEVEL',10063);
define('CURLOPT_SSL_VERIFYPEER',64);
define('CURLOPT_CAINFO',10065);
define('CURLOPT_PASSWDFUNCTION',20066);
define('CURLOPT_PASSWDDATA',10067);
define('CURLOPT_MAXREDIRS',68);
define('CURLOPT_FILETIME',10069);
define('CURLOPT_TELNETOPTIONS',10070);
define('CURLOPT_MAXCONNECTS',71);
define('CURLOPT_CLOSEPOLICY',72);
define('CURLOPT_CLOSEFUNCTION',20073);
define('CURLOPT_FRESH_CONNECT',74);
define('CURLOPT_FORBID_REUSE',75);
define('CURLOPT_RANDOM_FILE',10076);
define('CURLOPT_EGDSOCKET',10077);
define('CURLOPT_CONNECTTIMEOUT',78);
define('CURLOPT_HEADERFUNCTION',20079);
define('CURLOPT_HTTPGET',80);
define('CURLOPT_SSL_VERIFYHOST',81);
define('CURLOPT_COOKIEJAR',10082);
define('CURLOPT_SSL_CIPHER_LIST',10083);
define('CURLOPT_HTTP_VERSION',84);
define('CURLOPT_FTP_USE_EPSV',85);
define('CURLOPT_SSLCERTTYPE',10086);
define('CURLOPT_SSLKEY',10087);
define('CURLOPT_SSLKEYTYPE',10088);
define('CURLOPT_SSLENGINE',10089);
define('CURLOPT_SSLENGINE_DEFAULT',90);
define('CURLOPT_DNS_USE_GLOBAL_CACHE',91);
define('CURLOPT_DNS_CACHE_TIMEOUT',92);
define('CURLOPT_PREQUOTE',10093); 
define('CURLOPT_RETURNTRANSFER', 19913);//moodlefix

define('CURLINFO_EFFECTIVE_URL',1);
define('CURLINFO_HTTP_CODE',2);
define('CURLINFO_FILETIME',14);
define('CURLINFO_TOTAL_TIME',3);
define('CURLINFO_NAMELOOKUP_TIME',4);
define('CURLINFO_CONNECT_TIME',5);
define('CURLINFO_PRETRANSFER_TIME',6);
define('CURLINFO_STARTTRANSFER_TIME',17);
define('CURLINFO_REDIRECT_TIME',19);
define('CURLINFO_REDIRECT_COUNT',20);
define('CURLINFO_SIZE_UPLOAD',7);
define('CURLINFO_SIZE_DOWNLOAD',8);
define('CURLINFO_SPEED_DOWNLOAD',9);
define('CURLINFO_SPEED_UPLOAD',10);
define('CURLINFO_HEADER_SIZE',11);
define('CURLINFO_REQUEST_SIZE',12);
define('CURLINFO_SSL_VERIFYRESULT',13);
define('CURLINFO_CONTENT_LENGTH_DOWNLOAD',15);
define('CURLINFO_CONTENT_LENGTH_UPLOAD',16);
define('CURLINFO_CONTENT_TYPE',18);


define("TIMECOND_ISUNMODSINCE",1);
define("TIMECOND_IFMODSINCE",2);


function _curlopt_name($curlopt) {
	foreach (get_defined_constants() as $k=>$v) {
		if ( (substr($k,0,8)=="CURLOPT_") && ($v==$curlopt)) return $k;
	}
	return false;
}

// Initialize a CURL emulation session
function curl_init($url=false) {
    if(!isset($GLOBALS["_CURLNAT_OPT"])) {//moodlefix
        $GLOBALS["_CURLNAT_OPT"] = array();//moodlefix
        $GLOBALS["_CURLNAT_OPT"]["index"] = 0;//moodlefix
    }//moodlefix
	$i = $GLOBALS["_CURLNAT_OPT"]["index"]++;
    $GLOBALS["_CURLNAT_OPT"][$i] = array("url"=>$url, "fail_on_error"=>false);//moodlefix
	$GLOBALS["_CURLNAT_OPT"][$i]["http"] = &new HTTPRetriever(); 
	$GLOBALS["_CURLNAT_OPT"][$i]["include_body"] = true;
    $GLOBALS["_CURLNAT_OPT"][$i]["args"] = array();//moodlefix
    $GLOBALS["_CURLNAT_OPT"][$i]["settings"] = array();//moodlefix
	return $i;
}

// Set an option for a CURL emulation transfer 
function curl_setopt($ch,$option,$value) {
	
	$opt = &$GLOBALS["_CURLNAT_OPT"][$ch];
	$args = &$opt["args"];
	$settings = &$opt["settings"];
	$http = &$opt["http"];
	
	switch($option) {
		case CURLOPT_URL:
			$opt["url"] = $value;
			break;
		case CURLOPT_CUSTOMREQUEST:
			$opt["method"] = $value;
			break;
		case CURLOPT_REFERER:
			$http->headers["Referer"] = $value;
			break;
		case CURLOPT_NOBODY:
			$opt["include_body"] = $value==0;
			break;
		case CURLOPT_FAILONERROR:
			$opt["fail_on_error"] = $value>0;
			break;
		case CURLOPT_USERAGENT:
			$http->headers["User-Agent"] = $value;
			break;
		case CURLOPT_HEADER:
			$opt["include_headers"] = $value>0;
			break;
		case CURLOPT_RETURNTRANSFER:
			$opt["return_transfer"] = $value>0;
			break;
		case CURLOPT_TIMEOUT:
			$opt["max-time"] = (int) $value;
			break;
		case CURLOPT_HTTPHEADER:
			reset($value);
			foreach ($value as $k=>$header) {
				list($headername,$headervalue) = explode(":",$header);
				$http->headers[$headername] = ltrim($headervalue);
			}
			break;
		case CURLOPT_POST:
			$opt["post"] = $value>0;
			break;
		case CURLOPT_POSTFIELDS:
			$opt["postdata"] = $value;
			break;
		case CURLOPT_MUTE:
			// we're already mute, no?
			break;
		case CURLOPT_FILE:
			if (is_resource($value)) {
				$opt["output_handle"] = $value;
			} else {
				trigger_error("CURLOPT_FILE must specify a valid file resource",E_USER_WARNING);
			}
			break;
		case CURLOPT_WRITEHEADER:
			if (is_resource($value)) {
				$opt["header_handle"] = $value;
			} else {
				trigger_error("CURLOPT_WRITEHEADER must specify a valid file resource",E_USER_WARNING);
			}
			break;
		case CURLOPT_STDERR:
			// not implemented for now - not really relevant
			break;

		case CURLOPT_SSL_VERIFYPEER:
		case CURLOPT_SSL_VERIFYHOST:
			// these are automatically disabled using ssl:// anyway
			break;
			
		case CURLOPT_USERPWD:
			list($curl_user,$curl_pass) = explode(':',$value,2);
			$http->auth_username = $curl_user;
			$http->auth_password = $curl_pass;
			break;

		// Important stuff not implemented (as it's not yet supported by HTTPRetriever)
		case CURLOPT_PUT:
		case CURLOPT_INFILE:
		case CURLOPT_FOLLOWLOCATION:
		case CURLOPT_PROXYUSERPWD:
		case CURLOPT_COOKIE:
		case CURLOPT_COOKIEFILE:
		case CURLOPT_PROXY:
		case CURLOPT_RANGE:
		case CURLOPT_RESUME_FROM:

		// Things that cannot (reasonably) be implemented here
		case CURLOPT_LOW_SPEED_LIMIT:
		case CURLOPT_LOW_SPEED_TIME:
		case CURLOPT_KRB4LEVEL:
		case CURLOPT_SSLCERT:
		case CURLOPT_SSLCERTPASSWD:
		case CURLOPT_SSLVERSION:
		case CURLOPT_INTERFACE:
		case CURLOPT_CAINFO:
		case CURLOPT_TIMECONDITION:
		case CURLOPT_TIMEVALUE:
	
		// FTP stuff not implemented
		case CURLOPT_QUOTE:
		case CURLOPT_POSTQUOTE:
		case CURLOPT_UPLOAD:
		case CURLOPT_FTPLISTONLY:
		case CURLOPT_FTPAPPEND:
		case CURLOPT_FTPPORT:
		
		// Other stuff not implemented
		case CURLOPT_VERBOSE:
		case CURLOPT_NETRC:
		default:
			trigger_error("CURL emulation does not implement CURL option "._curlopt_name($option),E_USER_WARNING);
			break;
	}
}

// Perform a CURL emulation session
function curl_exec($ch) {
	$opt = &$GLOBALS["_CURLNAT_OPT"][$ch];
	$url = $opt["url"];

	$http = &$opt["http"];
	$http->disable_curl = true; // avoid problems with recursion, since we *ARE* CURL
    $http->error = false;//moodlefix

	// set time limits if requested
	if (!empty($opt["max-time"])) {//moodlefix
		$http->connect_timeout = $opt["max-time"];
		$http->max_time = $opt["max-time"];
	}
	
	if (!empty($opt["post"])) {//moodlefix
		$res = $http->post($url,$opt["postdata"]);
	} elseif (!empty($opt["method"])) {
		$res = $http->custom($opt["method"],$url,$opt["postdata"]);
	} else {
		$res = $http->get($url);
	}
		
	// check for errors
	$opt["errno"] = (!$res && $http->error) ? 1 : 0;
	if ($opt["errno"]) $opt["error"] = $http->error;
	
	// die if CURLOPT_FAILONERROR is set and the HTTP result code is greater than 300
	if ($opt["fail_on_error"]) {
		if ($http->result_code>300) die;
	}
    
    if ($res === false) {//moodlefix
        return false;//moodlefix
    }//moodlefix
	
	$opt["stats"] = $http->stats;


	$headers = "";
	foreach ($http->response_headers as $k=>$v) {
		$headers .= "$k: $v\r\n";
	}

	// if a file handle was provided for header output, extract the headers
	// and write them to the handle
	if (isset($opt["header_handle"])) {
		fwrite($opt["header_handle"],$headers);
	}

	$output = ($opt["include_headers"] ? $headers."\r\n" : "") . ($opt["include_body"] ? $http->response : "");
	
	// if a file handle was provided for output, write the output to it
	if (isset($opt["output_handle"])) {
		fwrite($opt["output_handle"],$output);
		
	// if the caller requested that the response be returned, return it
	} elseif ($opt["return_transfer"]) {
		return $output;
		
	// otherwise, just echo the output to stdout
	} else {
		echo $output;
	}
	return true;
}

function curl_close($ch) {
	$opt = &$GLOBALS["_CURLNAT_OPT"][$ch];
	
	if ($opt["settings"]) {
		$settings = &$opt["settings"];
		// if the user used CURLOPT_INFILE to specify a file to upload, remove the
		// temporary file created for the CURL binary
		if ($settings["upload-file"]["value"] && file_exists($settings["upload-file"]["value"])) unlink($settings["upload-file"]["value"]);
	}

	unset($GLOBALS["_CURLNAT_OPT"][$ch]);
}

function curl_errno($ch) {
	return (int) $GLOBALS["_CURLNAT_OPT"][$ch]["errno"];
}

function curl_error($ch) {
	return $GLOBALS["_CURLNAT_OPT"][$ch]["error"];
}

function curl_getinfo($ch,$opt=NULL) {
	if ($opt) {
		$curlinfo_tags = array(
			CURLINFO_EFFECTIVE_URL=>"url",
			CURLINFO_CONTENT_TYPE=>"content_type",
			CURLINFO_HTTP_CODE=>"http_code",
			CURLINFO_HEADER_SIZE=>"header_size",
			CURLINFO_REQUEST_SIZE=>"request_size",
			CURLINFO_FILETIME=>"filetime",
			CURLINFO_SSL_VERIFYRESULT=>"ssl_verify_result",
			CURLINFO_REDIRECT_COUNT=>"redirect_count",
			CURLINFO_TOTAL_TIME=>"total_time",
			CURLINFO_NAMELOOKUP_TIME=>"namelookup_time",
			CURLINFO_CONNECT_TIME=>"connect_time",
			CURLINFO_PRETRANSFER_TIME=>"pretransfer_time",
			CURLINFO_SIZE_UPLOAD=>"size_upload",
			CURLINFO_SIZE_DOWNLOAD=>"size_download",
			CURLINFO_SPEED_DOWNLOAD=>"speed_download",
			CURLINFO_SPEED_UPLOAD=>"speed_upload",
			CURLINFO_CONTENT_LENGTH_DOWNLOAD=>"download_content_length",
			CURLINFO_CONTENT_LENGTH_UPLOAD=>"upload_content_length",
			CURLINFO_STARTTRANSFER_TIME=>"starttransfer_time",
			CURLINFO_REDIRECT_TIME=>"redirect_time"
		);
		
		$key = $curlinfo_tags[$opt];
		return $GLOBALS["_CURLNAT_OPT"][$ch]["stats"][$key];
	} else {
		return $GLOBALS["_CURLNAT_OPT"][$ch]["stats"];
	}
}

function curl_version() {
	return "libcurlemu/".CURLNAT_VERSION."-nat";
}

}
?>