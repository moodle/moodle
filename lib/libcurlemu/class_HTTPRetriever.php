<?php
/* HTTP Retriever
 * Version v1.1.9
 * Copyright 2004-2006, Steve Blinch
 * http://code.blitzaffe.com
 * ============================================================================
 *
 * DESCRIPTION
 *
 * Provides a pure-PHP implementation of an HTTP v1.1 client, including support
 * for chunked transfer encoding and user agent spoofing.  Both GET and POST
 * requests are supported.
 *
 * This can be used in place of something like CURL or WGET for HTTP requests.
 * Native SSL (HTTPS) requests are also supported if the OpenSSL extension is 
 * installed under PHP v4.3.0 or greater.
 *
 * If native SSL support is not available, the class will also check for the
 * CURL extension; if it's installed, it will transparently be used for SSL
 * (HTTPS) requests.
 *
 * If neither native SSL support nor the CURL extension are available, and
 * libcurlemu (a CURL emulation library available from our web site) is found,
 * the class will also check for the CURL console binary (usually in 
 * /usr/bin/curl); if it's installed, it will transparently be used for SSL
 * requests.
 *
 * In short, if it's possible to make an HTTP/HTTPS request from your server,
 * this class can most likely do it.
 *
 *
 * HISTORY
 *
 * 1.1.9 (11-Oct-2006)
 *		- Added set_transfer_display() and default_transfer_callback()
 *		  methods for transfer progress tracking
 *		- Suppressed possible "fatal protocol error" when remote SSL server
 *		  closes the connection early
 *		- Added get_content_type() method
 *		- make_query_string() now handles arrays
 *
 * 1.1.8 (19-Jun-2006)
 *		- Added set_progress_display() and default_progress_callback()
 *		  methods for debug output
 *		- Added support for relative URLs in HTTP redirects
 *		- Added cookie support (sending and receiving)
 *		- Numerous bug fixes
 *
 * 1.1.7 (18-Apr-2006)
 *		- Added support for automatically following HTTP redirects
 *		- Added ::get_error() method to get any available error message (be
 *		  it an HTTP result error or an internal/connection error)
 *		- Added ::cache_hit variable to determine whether the page was cached
 *
 * 1.1.6 (04-Mar-2006)
 *		- Added stream_timeout class variable.
 *		- Added progress_callback class variable.
 *		- Added support for braindead servers that ignore Connection: close
 *
 *
 * EXAMPLE
 *
 * // HTTPRetriever usage example
 * require_once("class_HTTPRetriever.php");
 * $http = &new HTTPRetriever();
 *
 *
 * // Example GET request:
 * // ----------------------------------------------------------------------------
 * $keyword = "blitzaffe code"; // search Google for this keyword
 * if (!$http->get("http://www.google.com/search?hl=en&q=%22".urlencode($keyword)."%22&btnG=Search&meta=")) {
 *     echo "HTTP request error: #{$http->result_code}: {$http->result_text}";
 *     return false;
 * }
 * echo "HTTP response headers:<br><pre>";
 * var_dump($http->response_headers);
 * echo "</pre><br>";
 * 
 * echo "Page content:<br><pre>";
 * echo $http->response;
 * echo "</pre>";
 * // ----------------------------------------------------------------------------
 *  
 *
 * // Example POST request:
 * // ----------------------------------------------------------------------------
 * $keyword = "blitzaffe code"; // search Google for this keyword
 * $values = array(
 *     "hl"=>"en",
 *     "q"=>"%22".urlencode($keyword)."%22",
 *     "btnG"=>"Search",
 *     "meta"=>""
 * );
 * // Note: This example is just to demonstrate the POST equivalent of the GET
 * // example above; running this script will return a 501 Not Implemented, as
 * // Google does not support POST requests.
 * if (!$http->post("http://www.google.com/search",$http->make_query_string($values))) {
 *     echo "HTTP request error: #{$http->result_code}: {$http->result_text}";
 *     return false;
 * }
 * echo "HTTP response headers:<br><pre>";
 * var_dump($http->response_headers);
 * echo "</pre><br>";
 * 
 * echo "Page content:<br><pre>";
 * echo $http->response;
 * echo "</pre>";
 * // ----------------------------------------------------------------------------
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

// define user agent ID's
define("UA_EXPLORER",0);
define("UA_MOZILLA",1);
define("UA_FIREFOX",2);
define("UA_OPERA",3);

// define progress message severity levels
define('HRP_DEBUG',0);
define('HRP_INFO',1);
define('HRP_ERROR',2);

if (!defined("CURL_PATH")) define("CURL_PATH","/usr/bin/curl");

// if the CURL extension is not loaded, but the CURL Emulation Library is found, try
// to load it
if (!extension_loaded("curl") && !defined('HTTPR_NO_REDECLARE_CURL') ) {
	foreach (array(dirname(__FILE__)."/",dirname(__FILE__)."/libcurlemu/") as $k=>$libcurlemupath) {
		$libcurlemuinc = $libcurlemupath.'libcurlexternal.inc.php';
		if (is_readable($libcurlemuinc)) require_once($libcurlemuinc);
	}
}

class HTTPRetriever {
	
	// Constructor
	function HTTPRetriever() {
		// default HTTP headers to send with all requests
		$this->headers = array(
			"Referer"=>"",
			"User-Agent"=>"HTTPRetriever/1.0",
			"Connection"=>"close"
		);
		
		// HTTP version (has no effect if using CURL)
		$this->version = "1.1";
		
		// Normally, CURL is only used for HTTPS requests; setting this to
		// TRUE will force CURL for HTTP requests as well.  Not recommended.
		$this->force_curl = false;
		
		// If you don't want to use CURL at all, set this to TRUE.
		$this->disable_curl = false;
		
		// If HTTPS request return an error message about SSL certificates in
		// $this->error and you don't care about security, set this to TRUE
		$this->insecure_ssl = false;
		
		// Set the maximum time to wait for a connection
		$this->connect_timeout = 15;
		
		// Set the maximum time to allow a transfer to run, or 0 to disable.
		$this->max_time = 0;
		
		// Set the maximum time for a socket read/write operation, or 0 to disable.
		$this->stream_timeout = 0;
		
		// If you're making an HTTPS request to a host whose SSL certificate
		// doesn't match its domain name, AND YOU FULLY UNDERSTAND THE
		// SECURITY IMPLICATIONS OF IGNORING THIS PROBLEM, set this to TRUE.
		$this->ignore_ssl_hostname = false;
		
		// If TRUE, the get() and post() methods will close the connection
		// and return immediately after receiving the HTTP result code
		$this->result_close = false;
		
		// If set to a positive integer value, retrieved pages will be cached
		// for this number of seconds.  Any subsequent calls within the cache
		// period will return the cached page, without contacting the remote
		// server.
		$this->caching = false;
		
		// If $this->caching is enabled, this specifies the folder under which
		// cached pages are saved.
		$this->cache_path = '/tmp/';
		
		// Set these to perform basic HTTP authentication
		$this->auth_username = '';
		$this->auth_password = '';
		
		// Optionally set this to a valid callback method to have HTTPRetriever
		// provide progress messages.  Your callback must accept 2 parameters:
		// an integer representing the severity (0=debug, 1=information, 2=error),
		// and a string representing the progress message
		$this->progress_callback = null;
		
		// Optionally set this to a valid callback method to have HTTPRetriever
		// provide bytes-transferred messages.  Your callbcak must accept 2
		// parameters: an integer representing the number of bytes transferred,
		// and an integer representing the total number of bytes expected (or
		// -1 if unknown).
		$this->transfer_callback = null;
		
		// Set this to TRUE if you HTTPRetriever to transparently follow HTTP
		// redirects (code 301, 302, 303, and 307).  Optionally set this to a
		// numeric value to limit the maximum number of redirects to the specified
		// value.  (Redirection loops are detected automatically.)
		// Note that non-GET/HEAD requests will NOT be redirected except on code
		// 303, as per HTTP standards.
		$this->follow_redirects = false;
	}
	
	// Send an HTTP GET request to $url; if $ipaddress is specified, the
	// connection will be made to the selected IP instead of resolving the 
	// hostname in $url.
	//
	// If $cookies is set, it should be an array in one of two formats.
	//
	// Either: $cookies[ 'cookiename' ] = array (
	//		'/path/'=>array(
	//			'expires'=>time(),
	//			'domain'=>'yourdomain.com',
	//			'value'=>'cookievalue'
	//		)
	// );
	//
	// Or, a more simplified format:
	//	$cookies[ 'cookiename' ] = 'value';
	//
	// The former format will automatically check to make sure that the path, domain,
	// and expiration values match the HTTP request, and will only send the cookie if
	// they do match.  The latter will force the cookie to be set for the HTTP request
	// unconditionally.
	// 
	function get($url,$ipaddress = false,$cookies = false) {
		$this->method = "GET";
		$this->post_data = "";
		$this->connect_ip = $ipaddress;
		return $this->_execute_request($url,$cookies);
	}
	
	// Send an HTTP POST request to $url containing the POST data $data.  See ::get()
	// for a description of the remaining arguments.
	function post($url,$data="",$ipaddress = false,$cookies = false) {
		$this->method = "POST";
		$this->post_data = $data;
		$this->connect_ip = $ipaddress;
		return $this->_execute_request($url,$cookies);
	}
	
	// Send an HTTP HEAD request to $url.  See ::get() for a description of the arguments.	
	function head($url,$ipaddress = false,$cookies = false) {
		$this->method = "HEAD";
		$this->post_data = "";
		$this->connect_ip = $ipaddress;
		return $this->_execute_request($url,$cookies);
	}
		
	// send an alternate (non-GET/POST) HTTP request to $url
	function custom($method,$url,$data="",$ipaddress = false,$cookies = false) {
		$this->method = $method;
		$this->post_data = $data;
		$this->connect_ip = $ipaddress;
		return $this->_execute_request($url,$cookies);
	}	
	
	function array_to_query($arrayname,$arraycontents) {
		$output = "";
		foreach ($arraycontents as $key=>$value) {
			if (is_array($value)) {
				$output .= $this->array_to_query(sprintf('%s[%s]',$arrayname,urlencode($key)),$value);
			} else {
				$output .= sprintf('%s[%s]=%s&',$arrayname,urlencode($key),urlencode($value));
			}
		}
		return $output;
	}
	
	// builds a query string from the associative array array $data;
	// returns a string that can be passed to $this->post()
	function make_query_string($data) {
		$output = "";
		if (is_array($data)) {
			foreach ($data as $name=>$value) {
				if (is_array($value)) {
					$output .= $this->array_to_query(urlencode($name),$value);
				} elseif (is_scalar($value)) {
					$output .= urlencode($name)."=".urlencode($value)."&";
				} else {
					$output .= urlencode($name)."=".urlencode(serialize($value)).'&';
				}
			}
		}
		return substr($output,0,strlen($output)-1);
	}

	
	// this is pretty limited... but really, if you're going to spoof you UA, you'll probably
	// want to use a Windows OS for the spoof anyway
	//
	// if you want to set the user agent to a custom string, just assign your string to
	// $this->headers["User-Agent"] directly
	function set_user_agent($agenttype,$agentversion,$windowsversion) {
		$useragents = array(
			"Mozilla/4.0 (compatible; MSIE %agent%; Windows NT %os%)", // IE
			"Mozilla/5.0 (Windows; U; Windows NT %os%; en-US; rv:%agent%) Gecko/20040514", // Moz
			"Mozilla/5.0 (Windows; U; Windows NT %os%; en-US; rv:1.7) Gecko/20040803 Firefox/%agent%", // FFox
			"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT %os%) Opera %agent%  [en]", // Opera
		);
		$agent = $useragents[$agenttype];
		$this->headers["User-Agent"] = str_replace(array("%agent%","%os%"),array($agentversion,$windowsversion),$agent);
	}
	
	// this isn't presently used as it's now handled inline by the request parser
	function remove_chunkiness() {
		$remaining = $this->response;
		$this->response = "";
		
		while ($remaining) {
			$hexlen = strpos($remaining,"\r");
			$chunksize = substr($remaining,0,$hexlen);
			$argstart = strpos($chunksize,';');
			if ($argstart!==false) $chunksize = substr($chunksize,0,$argstart);
			$chunksize = (int) @hexdec($chunksize);

			$this->response .= substr($remaining,$hexlen+2,$chunksize);
			$remaining = substr($remaining,$hexlen+2+$chunksize+2);

			if (!$chunksize) {
				// either we're done, or something's borked... exit
				$this->response .= $remaining;
				return;
			}
		}
	}
	
	// (internal) store a page in the cache
	function _cache_store($token) {
		$values = array(
			"stats"=>$this->stats,
			"result_code"=>$this->result_code,
			"result_text"=>$this->result_text,
			"version"=>$this->version,
			"response"=>$this->response,
			"response_headers"=>$this->response_headers,
			"response_cookies"=>$this->response_cookies,
			"raw_response"=>$this->raw_response,
		);
		$values = serialize($values);

		$filename = $this->cache_path.$token.'.tmp';

		$fp = @fopen($filename,"w");
		if (!$fp) {
			$this->progress(HRP_DEBUG,"Unable to create cache file");
			return false;
		}
		fwrite($fp,$values);
		fclose($fp);

		$this->progress(HRP_DEBUG,"HTTP response stored to cache");
	}
	
	// (internal) fetch a page from the cache
	function _cache_fetch($token) {
		$this->cache_hit = false;
		$this->progress(HRP_DEBUG,"Checking for cached page value");

		$filename = $this->cache_path.$token.'.tmp';
		if (!file_exists($filename)) {
			$this->progress(HRP_DEBUG,"Page not available in cache");
			return false;
		}
		
		if (time()-filemtime($filename)>$this->caching) {
			$this->progress(HRP_DEBUG,"Page in cache is expired");
			@unlink($filename);
			return false;
		}
		
		if ($values = file_get_contents($filename)) {
			$values = unserialize($values);
			if (!$values) {
				$this->progress(HRP_DEBUG,"Invalid cache contents");
				return false;
			}
			
			$this->stats = $values["stats"];
			$this->result_code = $values["result_code"];
			$this->result_text = $values["result_text"];
			$this->version = $values["version"];
			$this->response = $values["response"];
			$this->response_headers = $values["response_headers"];
			$this->response_cookies = $values["response_cookies"];
			$this->raw_response = $values["raw_response"];
			
			$this->progress(HRP_DEBUG,"Page loaded from cache");
			$this->cache_hit = true;
			return true;
		} else {
			$this->progress(HRP_DEBUG,"Error reading cache file");
			return false;
		}
	}
	
	function parent_path($path) {
		if (substr($path,0,1)=='/') $path = substr($path,1);
		if (substr($path,-1)=='/') $path = substr($path,0,strlen($path)-1);
		$path = explode('/',$path);
		array_pop($path);
		return count($path) ? ('/' . implode('/',$path)) : '';
	}
	
	// $cookies should be an array in one of two formats.
	//
	// Either: $cookies[ 'cookiename' ] = array (
	//		'/path/'=>array(
	//			'expires'=>time(),
	//			'domain'=>'yourdomain.com',
	//			'value'=>'cookievalue'
	//		)
	// );
	//
	// Or, a more simplified format:
	//	$cookies[ 'cookiename' ] = 'value';
	//
	// The former format will automatically check to make sure that the path, domain,
	// and expiration values match the HTTP request, and will only send the cookie if
	// they do match.  The latter will force the cookie to be set for the HTTP request
	// unconditionally.
	// 	
	function response_to_request_cookies($cookies,$urlinfo) {
		
		// check for simplified cookie format (name=value)
		$cookiekeys = array_keys($cookies);
		if (!count($cookiekeys)) return;
		
		$testkey = array_pop($cookiekeys);
		if (!is_array($cookies[ $testkey ])) {
			foreach ($cookies as $k=>$v) $this->request_cookies[$k] = $v;
			return;
		}
		
		// must not be simplified format, so parse as complex format:
		foreach ($cookies as $name=>$paths) {
			foreach ($paths as $path=>$values) {
				// make sure the cookie isn't expired
				if ( isset($values['expires']) && ($values['expires']<time()) ) continue;
				
				$cookiehost = $values['domain'];
				$requesthost = $urlinfo['host'];
				// make sure the cookie is valid for this host
				$domain_match = (
					($requesthost==$cookiehost) ||
					(substr($requesthost,-(strlen($cookiehost)+1))=='.'.$cookiehost)
				);				
				
				// make sure the cookie is valid for this path
				$cookiepath = $path; if (substr($cookiepath,-1)!='/') $cookiepath .= '/';
				$requestpath = $urlinfo['path']; if (substr($requestpath,-1)!='/') $requestpath .= '/';
				if (substr($requestpath,0,strlen($cookiepath))!=$cookiepath) continue;
				
				$this->request_cookies[$name] = $values['value'];
			}
		}
	}					
	
	// Execute the request for a particular URL, and transparently follow
	// HTTP redirects if enabled.  If $cookies is specified, it is assumed
	// to be an array received from $this->response_cookies and will be
	// processed to determine which cookies are valid for this host/URL.
	function _execute_request($url,$cookies = false) {
		// valid codes for which we transparently follow a redirect
		$redirect_codes = array(301,302,303,307);
		// valid methods for which we transparently follow a redirect
		$redirect_methods = array('GET','HEAD');

		$request_result = false;
		
		$this->followed_redirect = false;
		$this->response_cookies = array();

		$previous_redirects = array();
		do {
			// send the request
			$request_result = $this->_send_request($url,$cookies);
			$lasturl = $url;
			$url = false;

			// see if a redirect code was received
			if ($this->follow_redirects && in_array($this->result_code,$redirect_codes)) {
				
				// only redirect on a code 303 or if the method was GET/HEAD
				if ( ($this->result_code==303) || in_array($this->method,$redirect_methods) ) {
					
					// parse the information from the OLD URL so that we can handle
					// relative links
					$oldurlinfo = parse_url($lasturl);
					
					$url = $this->response_headers['Location'];
					
					// parse the information in the new URL, and fill in any blanks
					// using values from the old URL
					$urlinfo = parse_url($url);
					foreach ($oldurlinfo as $k=>$v) {
						if (!$urlinfo[$k]) $urlinfo[$k] = $v;
					}
					
					// create an absolute path
					if (substr($urlinfo['path'],0,1)!='/') {
						$baseurl = $oldurlinfo['path'];
						if (substr($baseurl,-1)!='/') $baseurl = $this->parent_path($url) . '/';
						$urlinfo['path'] = $baseurl . $urlinfo['path'];
					}
					
					// rebuild the URL
					$url = $this->rebuild_url($urlinfo);
					
					$this->progress(HRP_INFO,'Redirected to '.$url);
				}
			}
			
			if ( $url && strlen($url) ) {
				
				if (isset($previous_redirects[$url])) {
					$this->error = "Infinite redirection loop";
					$request_result = false;
					break;
				}
				if ( is_numeric($this->follow_redirects) && (count($previous_redirects)>$this->follow_redirects) ) {
					$this->error = "Exceeded redirection limit";
					$request_result = false;
					break;
				}

				$previous_redirects[$url] = true;
			}

		} while ($url && strlen($url));

		// clear headers that shouldn't persist across multiple requests
		$per_request_headers = array('Host','Content-Length');
		foreach ($per_request_headers as $k=>$v) unset($this->headers[$v]);
		
		if (count($previous_redirects)>1) $this->followed_redirect = array_keys($previous_redirects);
		
		return $request_result;
	}
	
	// private - sends an HTTP request to $url
	function _send_request($url,$cookies = false) {
		$this->progress(HRP_INFO,"Initiating {$this->method} request for $url");
		if ($this->caching) {
			$cachetoken = md5($url.'|'.$this->post_data);
			if ($this->_cache_fetch($cachetoken)) return true;
		}
		
		$time_request_start = $this->getmicrotime();
		
		$urldata = parse_url($url);
		$http_host = $urldata['host'] . (isset($urldata['port']) ? ':'.$urldata['port'] : '');
		
		if (!isset($urldata["port"]) || !$urldata["port"]) $urldata["port"] = ($urldata["scheme"]=="https") ? 443 : 80;
		if (!isset($urldata["path"]) || !$urldata["path"]) $urldata["path"] = '/';
		
		if (!empty($urldata['user'])) $this->auth_username = $urldata['user'];
		if (!empty($urldata['pass'])) $this->auth_password = $urldata['pass'];
		
		//echo "Sending HTTP/{$this->version} {$this->method} request for ".$urldata["host"].":".$urldata["port"]." page ".$urldata["path"]."<br>";
		
		if ($this->version>"1.0") $this->headers["Host"] = $http_host;
		if ($this->method=="POST") {
			$this->headers["Content-Length"] = strlen($this->post_data);
			if (!isset($this->headers["Content-Type"])) $this->headers["Content-Type"] = "application/x-www-form-urlencoded";
		}
		
		if ( !empty($this->auth_username) || !empty($this->auth_password) ) {
			$this->headers['Authorization'] = 'Basic '.base64_encode($this->auth_username.':'.$this->auth_password);
		} else {
			unset($this->headers['Authorization']);
		}
		
		if (is_array($cookies)) {
			$this->response_to_request_cookies($cookies,$urldata);
		}
		
		if (($this->method=="GET") && (!empty($urldata["query"]))) $urldata["path"] .= "?".$urldata["query"];
		$request = $this->method." ".$urldata["path"]." HTTP/".$this->version."\r\n";
		$request .= $this->build_headers();
		$request .= $this->post_data;
		
		$this->response = "";
		
		// Native SSL support requires the OpenSSL extension, and was introduced in PHP 4.3.0
		$php_ssl_support = extension_loaded("openssl") && version_compare(phpversion(),"4.3.0")>=0;
		
		// if this is a plain HTTP request, or if it's an HTTPS request and OpenSSL support is available,
		// natively perform the HTTP request
		if ( ( ($urldata["scheme"]=="http") || ($php_ssl_support && ($urldata["scheme"]=="https")) ) && (!$this->force_curl) ) {
			$curl_mode = false;

			$hostname = $this->connect_ip ? $this->connect_ip : $urldata['host'];
			if ($urldata["scheme"]=="https") $hostname = 'ssl://'.$hostname;
			
			$time_connect_start = $this->getmicrotime();

			$this->progress(HRP_INFO,'Opening socket connection to '.$hostname.' port '.$urldata['port']);

			$this->expected_bytes = -1;
			$this->received_bytes = 0;
			
			$fp = @fsockopen ($hostname,$urldata["port"],$errno,$errstr,$this->connect_timeout);
			$time_connected = $this->getmicrotime();
			$connect_time = $time_connected - $time_connect_start;
			if ($fp) {
				if ($this->stream_timeout) stream_set_timeout($fp,$this->stream_timeout);
				$this->progress(HRP_INFO,"Connected; sending request");
				
				$this->progress(HRP_DEBUG,$request);
				fputs ($fp, $request);
				$this->raw_request = $request;
				
				if ($this->stream_timeout) {
					$meta = socket_get_status($fp);
					if ($meta['timed_out']) {
						$this->error = "Exceeded socket write timeout of ".$this->stream_timeout." seconds";
						$this->progress(HRP_ERROR,$this->error);
						return false;
					}
				}
				
				$this->progress(HRP_INFO,"Request sent; awaiting reply");
				
				$headers_received = false;
				$data_length = false;
				$chunked = false;
				$iterations = 0;
				while (!feof($fp)) {
		
					if ($data_length>0) {
						$line = fread($fp,$data_length);
						$data_length -= strlen($line);
					} else {
						$line = @fgets($fp,10240);
						if ($chunked) {
							$line = trim($line);
							if (!strlen($line)) continue;
							
							list($data_length,) = explode(';',$line);
							$data_length = (int) hexdec(trim($data_length));
							
							if ($data_length==0) {
								$this->progress(HRP_DEBUG,"Done");
								// end of chunked data
								break;
							}
							$this->progress(HRP_DEBUG,"Chunk length $data_length (0x$line)");
							continue;
						}
					}

					$this->response .= $line;
					
					$iterations++;
					if ($headers_received) {
						if ($time_connected>0) {
							$time_firstdata = $this->getmicrotime();
							$process_time = $time_firstdata - $time_connected;
							$time_connected = 0;
						}
						$this->received_bytes += strlen($line);
						if ($iterations % 20 == 0) {
							$this->update_transfer_counters();
						}
					}

					
					// some dumbass webservers don't respect Connection: close and just
					// leave the connection open, so we have to be diligent about
					// calculating the content length so we can disconnect at the end of
					// the response
					if ( (!$headers_received) && (trim($line)=="") ) {
						$headers_received = true;

						if (preg_match('/^Content-Length: ([0-9]+)/im',$this->response,$matches)) {
							$data_length = (int) $matches[1];
							$this->progress(HRP_DEBUG,"Content length is $data_length");
							$this->expected_bytes = $data_length;
							$this->update_transfer_counters();
						}
						if (preg_match("/^Transfer-Encoding: chunked/im",$this->response,$matches)) {
							$chunked = true;
							$this->progress(HRP_DEBUG,"Chunked transfer encoding requested");
						}
						
						if (preg_match_all("/^Set-Cookie: ((.*?)\=(.*?)(?:;\s*(.*))?)$/im",$this->response,$cookielist,PREG_SET_ORDER)) {
							// get the path for which cookies will be valid if no path is specified
							$cookiepath = preg_replace('/\/{2,}/','',$urldata['path']);
							if (substr($cookiepath,-1)!='/') {
								$cookiepath = explode('/',$cookiepath);
								array_pop($cookiepath);
								$cookiepath = implode('/',$cookiepath) . '/';
							}
							// process each cookie
							foreach ($cookielist as $k=>$cookiedata) {
								list(,$rawcookie,$name,$value,$attributedata) = $cookiedata;
								$attributedata = explode(';',trim($attributedata));
								$attributes = array();

								$cookie = array(
									'value'=>$value,
									'raw'=>trim($rawcookie),
								);
								foreach ($attributedata as $k=>$attribute) {
									list($attrname,$attrvalue) = explode('=',trim($attribute));
									$cookie[$attrname] = $attrvalue;
								}

								if (!isset($cookie['domain']) || !$cookie['domain']) $cookie['domain'] = $urldata['host'];
								if (!isset($cookie['path']) || !$cookie['path']) $cookie['path'] = $cookiepath;
								if (isset($cookie['expires']) && $cookie['expires']) $cookie['expires'] = strtotime($cookie['expires']);
								
								if (!$this->validate_response_cookie($cookie,$urldata['host'])) continue;
								
								// do not store expired cookies; if one exists, unset it
								if ( isset($cookie['expires']) && ($cookie['expires']<time()) ) {
									unset($this->response_cookies[ $name ][ $cookie['path'] ]);
									continue;//moodlefix
								}
								
								$this->response_cookies[ $name ][ $cookie['path'] ] = $cookie;
							}
						}
					}
					
					//$this->progress(HRP_INFO,"Next [$line]");
					if ($this->stream_timeout) {
						$meta = socket_get_status($fp);
						if ($meta['timed_out']) {
							$this->error = "Exceeded socket read timeout of ".$this->stream_timeout." seconds";
							$this->progress(HRP_ERROR,$this->error);
							return false;
						}
					}
					
					// check time limits if requested
					if ($this->max_time>0) {
						if ($this->getmicrotime() - $time_request_start > $this->max_time) {
							$this->error = "Exceeded maximum transfer time of ".$this->max_time." seconds";
							$this->progress(HRP_ERROR,$this->error);
							return false;
							break;
						}
					}
					if ($this->result_close) {
						if (preg_match_all("/HTTP\/([0-9\.]+) ([0-9]+) (.*?)[\r\n]/",$this->response,$matches)) {
							$resultcodes = $matches[2];
							foreach ($resultcodes as $k=>$code) {
								if ($code!=100) {
									$this->progress(HRP_INFO,'HTTP result code received; closing connection');

									$this->result_code = $code;
									$this->result_text = $matches[3][$k];
									fclose($fp);
					
									return ($this->result_code==200);
								}
							}
						}
					}
				}
				@fclose ($fp);
				
				$this->update_transfer_counters();
				
				if (is_array($this->response_cookies)) {
					// make sure paths are sorted in the order in which they should be applied
					// when setting response cookies
					foreach ($this->response_cookies as $name=>$paths) {
						ksort($this->response_cookies[$name]);
					}
				}
				$this->progress(HRP_INFO,'Request complete');
			} else {
				$this->error = strtoupper($urldata["scheme"])." connection to ".$hostname." port ".$urldata["port"]." failed";
				$this->progress(HRP_ERROR,$this->error);
				return false;
			}

		// perform an HTTP/HTTPS request using CURL
		} elseif ( !$this->disable_curl && ( ($urldata["scheme"]=="https") || ($this->force_curl) ) ) {
			$this->progress(HRP_INFO,'Passing HTTP request for $url to CURL');
			$curl_mode = true;
			if (!$this->_curl_request($url)) return false;
			
		// unknown protocol
		} else {
			$this->error = "Unsupported protocol: ".$urldata["scheme"];
			$this->progress(HRP_ERROR,$this->error);
			return false;
		}
		
		$this->raw_response = $this->response;

		$totallength = strlen($this->response);
		
		do {
			$headerlength = strpos($this->response,"\r\n\r\n");

			$response_headers = explode("\r\n",substr($this->response,0,$headerlength));
			$http_status = trim(array_shift($response_headers));
			foreach ($response_headers as $line) {
				list($k,$v) = explode(":",$line,2);
				$this->response_headers[trim($k)] = trim($v);
			}
			$this->response = substr($this->response,$headerlength+4);
	
			/* // Handled in-transfer now
			if (($this->response_headers['Transfer-Encoding']=="chunked") && (!$curl_mode)) {
				$this->remove_chunkiness();
			}
			*/
		
			if (!preg_match("/^HTTP\/([0-9\.]+) ([0-9]+) (.*?)$/",$http_status,$matches)) {
				$matches = array("",$this->version,0,"HTTP request error");
			}
			list (,$response_version,$this->result_code,$this->result_text) = $matches;

			// skip HTTP result code 100 (Continue) responses
		} while (($this->result_code==100) && ($headerlength));
		
		// record some statistics, roughly compatible with CURL's curl_getinfo()
		if (!$curl_mode) {
			$total_time = $this->getmicrotime() - $time_request_start;
			$transfer_time = $total_time - $connect_time;
			$this->stats = array(
				"total_time"=>$total_time,
				"connect_time"=>$connect_time,	// time between connection request and connection established
				"process_time"=>$process_time,	// time between HTTP request and first data (non-headers) received
				"url"=>$url,
				"content_type"=>$this->response_headers["Content-Type"],
				"http_code"=>$this->result_code,
				"header_size"=>$headerlength,
				"request_size"=>$totallength,
				"filetime"=>strtotime($this->response_headers["Date"]),
				"pretransfer_time"=>$connect_time,
				"size_download"=>$totallength,
				"speed_download"=>$transfer_time > 0 ? round($totallength / $transfer_time) : 0,
				"download_content_length"=>$totallength,
				"upload_content_length"=>0,
				"starttransfer_time"=>$connect_time,
			);
		}
		
		
		$ok = ($this->result_code==200);
		if ($ok && $this->caching) $this->_cache_store($cachetoken);

		return $ok;
	}
	
	function validate_response_cookie($cookie,$actual_hostname) {
		// make sure the cookie can't be set for a TLD, eg: '.com'		
		$cookiehost = $cookie['domain'];
		$p = strrpos($cookiehost,'.');
		if ($p===false) return false;
		
		$tld = strtolower(substr($cookiehost,$p+1));
		$special_domains = array("com", "edu", "net", "org", "gov", "mil", "int");
		$periods_required = in_array($tld,$special_domains) ? 1 : 2;
		
		$periods = substr_count($cookiehost,'.');
		if ($periods<$periods_required) return false;
		
		if (substr($actual_hostname,0,1)!='.') $actual_hostname = '.'.$actual_hostname;
		if (substr($cookiehost,0,1)!='.') $cookiehost = '.'.$cookiehost;
		$domain_match = (
			($actual_hostname==$cookiehost) ||
			(substr($actual_hostname,-strlen($cookiehost))==$cookiehost)
		);
		
		return $domain_match;

	}
	
	function build_headers() {
		$headers = "";
		foreach ($this->headers as $name=>$value) {
			$value = trim($value);
			if (empty($value)) continue;
			$headers .= "{$name}: {$value}\r\n";
		}

		if (isset($this->request_cookies) && is_array($this->request_cookies)) {
			$cookielist = array();
			foreach ($this->request_cookies as $name=>$value) {
				$cookielist[] = "{$name}={$value}";
			}
			if (count($cookielist)) $headers .= "Cookie: ".implode('; ',$cookielist)."\r\n";
		}
		
		
		$headers .= "\r\n";
		
		return $headers;
	}
	
	// opposite of parse_url()
	function rebuild_url($urlinfo) {
		$url = $urlinfo['scheme'].'://';
		
		if ($urlinfo['user'] || $urlinfo['pass']) {
			$url .= $urlinfo['user'];
			if ($urlinfo['pass']) {
				if ($urlinfo['user']) $url .= ':';
				$url .= $urlinfo['pass'];
			}
			$url .= '@';
		}
		
		$url .= $urlinfo['host'];
		if ($urlinfo['port']) $url .= ':'.$urlinfo['port'];
		
		$url .= $urlinfo['path'];
		
		if ($urlinfo['query']) $url .= '?'.$urlinfo['query'];
		if ($urlinfo['fragment']) $url .= '#'.$urlinfo['fragment'];
		
		return $url;
	}
	
	function _replace_hostname(&$url,$new_hostname) {
		$parts = parse_url($url);
		$old_hostname = $parts['host'];
		
		$parts['host'] = $new_hostname;
		
		$url = $this->rebuild_url($parts);
				
		return $old_hostname;
	}
	
	function _curl_request($url) {
		$this->error = false;

		// if a direct connection IP address was specified,	replace the hostname
		// in the URL with the IP address, and set the Host: header to the
		// original hostname
		if ($this->connect_ip) {
			$old_hostname = $this->_replace_hostname($url,$this->connect_ip);
			$this->headers["Host"] = $old_hostname;
		}
		

		unset($this->headers["Content-Length"]);
		$headers = explode("\n",$this->build_headers());
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $url); 
		curl_setopt($ch,CURLOPT_USERAGENT, $this->headers["User-Agent"]); 
		curl_setopt($ch,CURLOPT_HEADER, 1); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1); 
//		curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1); // native method doesn't support this yet, so it's disabled for consistency
		curl_setopt($ch,CURLOPT_TIMEOUT, 10);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
		
		if ($this->method=="POST") {
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$this->post_data);
		}
		if ($this->insecure_ssl) {
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
		}
		if ($this->ignore_ssl_hostname) {
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,1);
		}
		
		$this->response = curl_exec ($ch);
		if (curl_errno($ch)!=0) {
			$this->error = "CURL error #".curl_errno($ch).": ".curl_error($ch);
		}
		
		$this->stats = curl_getinfo($ch);
		curl_close($ch);
		
		return ($this->error === false);
	}
	
	function progress($level,$msg) {
		if (is_callable($this->progress_callback)) call_user_func($this->progress_callback,$level,$msg);
	}
	
	// Gets any available HTTPRetriever error message (including both internal
	// errors and HTTP errors)
	function get_error() {
		return $this->error ? $this->error : 'HTTP ' . $this->result_code.': '.$this->result_text;
	}
	
	function get_content_type() {
		if (!$ctype = $this->response_headers['Content-Type']) {
			$ctype = $this->response_headers['Content-type'];
		}
		list($ctype,) = explode(';',$ctype);
		
		return strtolower($ctype);
	}
	
	function update_transfer_counters() {
		if (is_callable($this->transfer_callback)) call_user_func($this->transfer_callback,$this->received_bytes,$this->expected_bytes);
	}

	function set_transfer_display($enabled = true) {
		if ($enabled) {
			$this->transfer_callback = array(&$this,'default_transfer_callback');
		} else {
			unset($this->transfer_callback);
		}
	}
	
	function set_progress_display($enabled = true) {
		if ($enabled) {
			$this->progress_callback = array(&$this,'default_progress_callback');
		} else {
			unset($this->progress_callback);
		}
	}
	
	function default_progress_callback($severity,$message) {
		$severities = array(
			HRP_DEBUG=>'debug',
			HRP_INFO=>'info',
			HRP_ERROR=>'error',
		);
		
		echo date('Y-m-d H:i:sa').' ['.$severities[$severity].'] '.$message."\n";
		flush();
	}

	function default_transfer_callback($transferred,$expected) {
		$msg = "Transferred " . round($transferred/1024,1);
		if ($expected>=0) $msg .= "/" . round($expected/1024,1);
		$msg .=	"KB";
		if ($expected>0) $msg .= " (".round($transferred*100/$expected,1)."%)";
		echo date('Y-m-d H:i:sa')." $msg\n";
		flush();
	}	
	
	function getmicrotime() { 
		list($usec, $sec) = explode(" ",microtime()); 
		return ((float)$usec + (float)$sec); 
	}	
}
?>