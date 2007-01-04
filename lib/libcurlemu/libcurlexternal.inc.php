<?php
/* CURL Extension Emulation Library (Console Binary)
 * Copyright 2004-2005, Steve Blinch
 * http://code.blitzaffe.com
 * ============================================================================
 *
 * DESCRIPTION
 *
 * Provides a pure-PHP implementation of the PHP CURL extension, for use on
 * systems which do not already have the CURL extension installed.  It emulates
 * all of the curl_* functions normally provided by the CURL extension itself
 * by wrapping the CURL console binary.
 *
 * This library will automatically detect whether or not the "real" CURL
 * extension is installed, and if so, it will not interfere.  Thus, it can be
 * used to ensure that, one way or another, the CURL functions are available
 * for use.
 *
 * This library is actually a wrapper for the CURL console application (usually
 * found in /usr/bin/curl), so you must have the CURL binary installed in order
 * to use this script.
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
 
// if the real CURL PHP extension is installed, exit without doing anything
if (!extension_loaded("curl")) {

// if the CURL path was not defined by the calling script, define it
if (!defined("CURL_PATH")) define("CURL_PATH","/usr/bin/curl");

// if the CURL binary was not found, do one of the following:
//   - if CURLEXT_MISSING_ABORT was defined, then exit without implementing the CURL functions
//   - if CURLEXT_MISSING_IGNORE was defined, then implement the CURL functions anyway (even
//     though they won't work without the CURL binary installed)
//   - otherwise, raise a fatal error and halt the script
if (!function_exists('is_executable') or !is_executable(CURL_PATH)) {//moodlefix
	
 	if (defined("CURLEXT_MISSING_ABORT") && CURLEXT_MISSING_ABORT) {
 		return;
 	} elseif (defined("CURLEXT_MISSING_IGNORE") && CURLEXT_MISSING_IGNORE) {
 		// proceed and implement the CURL functions anyway, even though they won't work
 	} else {
		trigger_error("CURL extension is not loaded, and the commandline version of CURL was not found at ".CURL_PATH,E_USER_ERROR);
	}
}

define("CURLEXT_VERSION","1.0.0");

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
    if(!isset($GLOBALS["_CURLEXT_OPT"])) {//moodlefix
        $GLOBALS["_CURLEXT_OPT"] = array();//moodlefix
        $GLOBALS["_CURLEXT_OPT"]["index"] = 0;//moodlefix
    }//moodlefix
	$i = $GLOBALS["_CURLEXT_OPT"]["index"]++;
	$GLOBALS["_CURLEXT_OPT"][$i] = array("url"=>$url, "verbose"=>false, "fail_on_error"=>false);//moodlefix
	$GLOBALS["_CURLEXT_OPT"][$i]["args"] = array();//moodlefix
    $GLOBALS["_CURLEXT_OPT"][$i]["settings"] = array();//moodlefix
	return $i;
}

// Set an option for a CURL emulation transfer 
function curl_setopt($ch,$option,$value) {
	
	$opt = &$GLOBALS["_CURLEXT_OPT"][$ch];
	$args = &$opt["args"];
	$settings = &$opt["settings"];
	
	switch($option) {
		case CURLOPT_URL:
			$opt["url"] = $value;
			break;
		case CURLOPT_VERBOSE:
			$opt["verbose"] = $value>0;
			break;
		case CURLOPT_USERPWD:
			if ($value==="") $value = false;
			$settings["user"] = $value;
			break;
		case CURLOPT_PROXYUSERPWD:
			if ($value==="") $value = false;
			$settings["proxy-user"] = $value;
			break;
		case CURLOPT_COOKIE:
			if ($value==="") $value = false;
			if ( is_bool($value) || (strpos($value,"=")!==false) ) $settings["cookie"] = $value;
			break;
		case CURLOPT_COOKIEFILE:
			if ($value==="") $value = false;
			$settings["cookie"] = $value;
			break;
		case CURLOPT_COOKIEJAR:
			if ($value==="") $value = false;
			$settings["cookie-jar"] = $value;
			break;
		case CURLOPT_CUSTOMREQUEST:
			if ($value==="") $value = false;
			$settings["request"] = $value;
			break;
		case CURLOPT_PROXY:
			if ($value==="") $value = false;
			$settings["proxy"] = $value;
			break;
		case CURLOPT_INTERFACE:
			if ($value==="") $value = false;
			$settings["interface"] = $value;
			break;
		case CURLOPT_KRB4LEVEL:
			if ($value==="") $value = false;
			$settings["krb4"] = $value;
			break;
		case CURLOPT_SSLCERT:
			$pass = "";
			if (is_string($settings["cert"])) {
				list(,$pass) = explode(":",$settings["cert"]);
				if (strlen($pass)) $pass = ":$pass";
			}
			$settings["cert"] = $value.$pass;
			break;
		case CURLOPT_SSLCERTPASSWD:
			$filename = "";
			if (is_string($settings["cert"])) {
				list($filename,) = explode(":",$settings["cert"]);
			}
			$settings["cert"] = $filename.":".$value;
			break;
		case CURLOPT_RANGE:
			if ($value==="") $value = false;
			$settings["range"] = $value;
			break;
		case CURLOPT_REFERER:
			if ($value==="") $value = false;
			$settings["referer"] = $value;
			break;
		case CURLOPT_NOBODY:
			$settings["head"] = $value>0;
			break;
		case CURLOPT_FAILONERROR:
			$opt["fail_on_error"] = $value>0;
			break;
		case CURLOPT_USERAGENT:
			$settings["user-agent"] = $value;
			break;
		case CURLOPT_HEADER:
			$settings["include"] = $value>0;
			break;
		case CURLOPT_RETURNTRANSFER:
			$opt["return_transfer"] = $value>0;
			break;
		case CURLOPT_TIMEOUT:
			$settings["max-time"] = (int) $value;
			break;
		case CURLOPT_HTTPHEADER:
			reset($value);
			foreach ($value as $k=>$header) $args[] = "header=".$header;
			break;
		case CURLOPT_POST:
			$settings["data"]["enabled"] = $value>0;
			break;
		case CURLOPT_POSTFIELDS:
			if ($value==="") $value = false;
			$settings["data"]["value"] = $value;
			break;
		case CURLOPT_SSL_VERIFYPEER:
			$settings["insecure"] = ($value==0);
			break;
		case CURLOPT_SSL_VERIFYHOST:
			// not supported by the commandline client
			break;
		case CURLOPT_FOLLOWLOCATION:
			$settings["location"] = $value>0;
			break;
		case CURLOPT_PUT:
			$settings["upload-file"]["enabled"] = $value>0;
			break;
		case CURLOPT_INFILE:
			if ($value==="") $value = false;
			
			if (is_resource($value)) {
				
				// Ugh, this is a terrible hack.  The CURL extension accepts a file handle, but
				// the CURL binary obviously wants a filename.  Since you can't derive a filename
				// from a file handle, we have to make a copy of the file from the file handle,
				// then pass the temporary filename to the CURL binary.
				
				$tmpfilename = tempnam("/tmp","cif");
				$fp = @fopen($tmpfilename,"w");
				if (!$fp) {
					trigger_error("CURL emulation library could not create a temporary file for CURLOPT_INFILE; upload aborted",E_USER_WARNING);
				} else {
					while (!feof($value)) {
						$contents = fread($value,8192);
						fwrite($fp,$contents);
					}
					fclose($fp);
					// if a temporary file was previously created, unlink it
					if ($settings["upload-file"]["value"] && file_exists($settings["upload-file"]["value"])) unlink($settings["upload-file"]["value"]);
					
					// set the new upload-file filename
					$settings["upload-file"]["value"] = $tmpfilename;
				}
			} else {
				trigger_error("CURLOPT_INFILE must specify a valid file resource",E_USER_WARNING);
			}
			
			break;
		case CURLOPT_MUTE:
			// we're already mute, no?
			break;
		case CURLOPT_LOW_SPEED_LIMIT:
			$settings["speed-limit"] = (int) $value;
			break;
		case CURLOPT_LOW_SPEED_TIME:
			$settings["speed-time"] = (int) $value;
			break;
		case CURLOPT_RESUME_FROM:
			$settings["continue-at"] = (int) $value;
			break;
		case CURLOPT_CAINFO:
			if ($value==="") $value = false;
			$settings["cacert"] = $value;
			break;
		case CURLOPT_SSLVERSION:
			$value = (int) $value;
			switch($value) {
				case 2:
				case 3:
					unset($settings["sslv2"]);
					unset($settings["sslv3"]);
					$settings["sslv".$value] = true;
					break;
			}
			break;
		case CURLOPT_TIMECONDITION:
			// untested - I'm lazy :)
			if (!isset($settings["time-cond"]["enabled"])) $settings["time-cond"]["enabled"] = false;
			if (!$settings["time-cond"]["value"]) $settings["time-cond"]["value"] = 1;

			$settings["time-cond"]["value"] = abs($settings["time-cond"]["value"]);
			if ($value==TIMECOND_ISUNMODSINCE) {
				$settings["time-cond"]["value"] *= -1;
			}			
			
			break;
		case CURLOPT_TIMEVALUE:
			// untested - I'm lazy :)
			if ($settings["time-cond"]["value"]) {
				$sign = $settings["time-cond"]["value"] / abs($settings["time-cond"]["value"]);
			} else {
				$sign = 1;
			}
			$settings["time-cond"]["value"] = (int) $value * $sign;
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
		// FTP stuff not implemented
		case CURLOPT_QUOTE:
		case CURLOPT_POSTQUOTE:
		case CURLOPT_UPLOAD:
		case CURLOPT_FTPLISTONLY:
		case CURLOPT_FTPAPPEND:
		case CURLOPT_FTPPORT:
		// Other stuff not implemented
		case CURLOPT_NETRC:
		default:
			trigger_error("CURL emulation does not implement CURL option "._curlopt_name($option),E_USER_WARNING);
			break;
	}
}

// Perform a CURL emulation session
function curl_exec($ch) {
	$opt = &$GLOBALS["_CURLEXT_OPT"][$ch];
	$url = $opt["url"];
	$verbose = $opt["verbose"];
	
	// ask commandline CURL to return its statistics at the end of its output
	$opt["settings"]["write-out"] = "%{http_code}|%{time_total}|%{time_namelookup}|%{time_connect}|%{time_pretransfer}|%{time_starttransfer}|%{size_download}|%{size_upload}|%{size_header}|%{size_request}|%{speed_download}|%{speed_upload}|||||||%{content_type}|%{url_effective}";
	$writeout_order = array(
		CURLINFO_HTTP_CODE,
		CURLINFO_TOTAL_TIME,
		CURLINFO_NAMELOOKUP_TIME,
		CURLINFO_CONNECT_TIME,
		CURLINFO_PRETRANSFER_TIME,
		CURLINFO_STARTTRANSFER_TIME,
		CURLINFO_SIZE_DOWNLOAD,
		CURLINFO_SIZE_UPLOAD,
		CURLINFO_HEADER_SIZE,
		CURLINFO_REQUEST_SIZE,
		CURLINFO_SPEED_DOWNLOAD,
		CURLINFO_SPEED_UPLOAD,

		// the following 5 items are not provided by commandline CURL, and thus are left empty
		CURLINFO_FILETIME,
		CURLINFO_REDIRECT_TIME,
		CURLINFO_SSL_VERIFYRESULT,
		CURLINFO_CONTENT_LENGTH_DOWNLOAD,
		CURLINFO_CONTENT_LENGTH_UPLOAD,
		CURLINFO_REDIRECT_COUNT,

		CURLINFO_CONTENT_TYPE,
		CURLINFO_EFFECTIVE_URL,
	);

	// if the CURLOPT_NOBODY option was specified (to remove the body from the output),
	// but an output file handle was set, we need to tell CURL to return the body so
	// that we can write it to the output handle and strip it from the output
	if (!empty($opt["settings"]["head"]) && $opt["output_handle"]) {//moodlefix
		unset($opt["settings"]["head"]);
		$strip_body = true;
	} else {
        $strip_body = false;
    }
	// if the CURLOPT_HEADER option was NOT specified, but a header file handle was
	// specified, we again need to tell CURL to return the headers so we can write
	// them, then strip them from the output
	if (!isset($opt["settings"]["include"]) && isset($opt["header_handle"])) {
		$opt["settings"]["include"] = true;
		$strip_headers = true;
	} else {
        $strip_headers = false;//moodlefix
    }

	// build the CURL argument list
	$arguments = "";
	foreach ($opt["args"] as $k=>$arg) {
		list($argname,$argval) = explode('=',$arg,2);
		$arguments .= "--$argname ".escapeshellarg($argval)." ";
	}	
	foreach ($opt["settings"] as $argname=>$argval) {
		if (is_array($argval)) {
			if (isset($argval["enabled"]) && !$argval["enabled"]) continue;
			$argval = $argval["value"];
		}
		if ($argval===false) continue;
		if (is_bool($argval)) $argval = "";
		$arguments .= "--$argname ".escapeshellarg($argval)." ";
	}

	// build the CURL commandline and execute it
	$cmd = CURL_PATH." ".$arguments." ".escapeshellarg($url);
	
	if ($verbose) echo "libcurlemu: Executing: $cmd\n";
	exec($cmd,$output,$ret);
	
	if ($verbose) {
		echo "libcurlemu: Result: ";
		var_dump($output);
		echo "libcurlemu: Exit code: $ret\n";
	}
	
	// check for errors
	$opt["errno"] = $ret;
	if ($ret) $opt["error"] = "CURL error #$ret";
	
	// die if CURLOPT_FAILONERROR is set and the HTTP result code is greater than 300
	if ($opt["fail_on_error"]) {//moodlefix
		if (preg_match("/^HTTP\/1.[0-9]+ ([0-9]{3}) /",$output[0],$matches)) {
			$resultcode = (int) $matches[1];
			if ($resultcode>300) die;
		} else {
			die; // couldn't get result code!
		}
	}
	
	// pull the statistics out from the output
	$stats = explode('|',array_pop($output));
	foreach ($writeout_order as $k=>$item) {
		$opt["stats"][$item] = $stats[$k];
	}

	// build the response string
	$output = implode("\r\n",$output);

	
	// find the header end position if needed
	if ($strip_headers || $strip_body || isset($opt["header_handle"])) {
		$headerpos = strpos($output,"\r\n\r\n");
	}

	// if a file handle was provided for header output, extract the headers
	// and write them to the handle
	if (isset($opt["header_handle"])) {
		$headers = substr($output,0,$headerpos);
		fwrite($opt["header_handle"],$headers);
	}
	
	// if the caller did not request headers in the output, strip them
	if ($strip_headers) {
		$output = substr($output,$headerpos+4);
	}
	
	// if the caller did not request the response body in the output, strip it
	if ($strip_body) {
		if ($strip_headers) {
			$body = $output;
			$output = "";
		} else {
			$body = substr($output,$headerpos+4);
			$output = substr($output,0,$headerpos);
		}
	}
	
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
	$opt = &$GLOBALS["_CURLEXT_OPT"][$ch];
	
	if ($opt["settings"]) {
		$settings = &$opt["settings"];
		// if the user used CURLOPT_INFILE to specify a file to upload, remove the
		// temporary file created for the CURL binary
		if (!empty($settings["upload-file"]["value"]) && file_exists($settings["upload-file"]["value"])) unlink($settings["upload-file"]["value"]);//moodlefix
	}

	unset($GLOBALS["_CURLEXT_OPT"][$ch]);
}

function curl_errno($ch) {
	return (int) $GLOBALS["_CURLEXT_OPT"][$ch]["errno"];
}

function curl_error($ch) {
	return $GLOBALS["_CURLEXT_OPT"][$ch]["error"];
}

function curl_getinfo($ch,$opt=NULL) {
	if ($opt) {
		return $GLOBALS["_CURLEXT_OPT"][$ch]["stats"][$opt];
	} else {
		$curlinfo_tags = array(
			"url"=>CURLINFO_EFFECTIVE_URL,
			"content_type"=>CURLINFO_CONTENT_TYPE,
			"http_code"=>CURLINFO_HTTP_CODE,
			"header_size"=>CURLINFO_HEADER_SIZE,
			"request_size"=>CURLINFO_REQUEST_SIZE,
			"filetime"=>CURLINFO_FILETIME,
			"ssl_verify_result"=>CURLINFO_SSL_VERIFYRESULT,
			"redirect_count"=>CURLINFO_REDIRECT_COUNT,
			"total_time"=>CURLINFO_TOTAL_TIME,
			"namelookup_time"=>CURLINFO_NAMELOOKUP_TIME,
			"connect_time"=>CURLINFO_CONNECT_TIME,
			"pretransfer_time"=>CURLINFO_PRETRANSFER_TIME,
			"size_upload"=>CURLINFO_SIZE_UPLOAD,
			"size_download"=>CURLINFO_SIZE_DOWNLOAD,
			"speed_download"=>CURLINFO_SPEED_DOWNLOAD,
			"speed_upload"=>CURLINFO_SPEED_UPLOAD,
			"download_content_length"=>CURLINFO_CONTENT_LENGTH_DOWNLOAD,
			"upload_content_length"=>CURLINFO_CONTENT_LENGTH_UPLOAD,
			"starttransfer_time"=>CURLINFO_STARTTRANSFER_TIME,
			"redirect_time"=>CURLINFO_REDIRECT_TIME
		);
		$res = array();
		foreach ($curlinfo_tags as $tag=>$opt) {
			$res[$tag] = $GLOBALS["_CURLEXT_OPT"][$ch]["stats"][$opt];
		}
		return $res;
	}
}

function curl_version() {
	return "libcurlemu/".CURLEXT_VERSION."-ext";
}

}
?>