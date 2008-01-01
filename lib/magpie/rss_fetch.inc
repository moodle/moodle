<?php
/*
 * Project:     MagpieRSS: a simple RSS integration tool
 * File:        rss_fetch.inc, a simple functional interface
                to fetching and parsing RSS files, via the
                function fetch_rss()
 * Author:      Kellan Elliott-McCrea <kellan@protest.net>
 * License:     GPL
 *
 * The lastest version of MagpieRSS can be obtained from:
 * http://magpierss.sourceforge.net
 *
 * For questions, help, comments, discussion, etc., please join the
 * Magpie mailing list:
 * magpierss-general@lists.sourceforge.net
 *
 */
 
// Setup MAGPIE_DIR for use on hosts that don't include
// the current path in include_path.
// with thanks to rajiv and smarty
if (!defined('DIR_SEP')) {
    define('DIR_SEP', DIRECTORY_SEPARATOR);
}

if (!defined('MAGPIE_DIR')) {
    define('MAGPIE_DIR', dirname(__FILE__) . DIR_SEP);
}

require_once( MAGPIE_DIR . 'rss_parse.inc' );
require_once( MAGPIE_DIR . 'rss_cache.inc' );

// moodlefix start
// for including 3rd party libraries
//define('MAGPIE_EXTLIB', MAGPIE_DIR . 'extlib' . DIR_SEP);
//require_once( MAGPIE_EXTLIB . 'Snoopy.class.inc');
require_once($CFG->libdir.'/filelib.php');
// moodlefix end

/* 
 * CONSTANTS - redefine these in your script to change the
 * behaviour of fetch_rss() currently, most options effect the cache
 *
 * MAGPIE_CACHE_ON - Should Magpie cache parsed RSS objects? 
 * For me a built in cache was essential to creating a "PHP-like" 
 * feel to Magpie, see rss_cache.inc for rationale
 *
 *
 * MAGPIE_CACHE_DIR - Where should Magpie cache parsed RSS objects?
 * This should be a location that the webserver can write to.   If this 
 * directory does not already exist Mapie will try to be smart and create 
 * it.  This will often fail for permissions reasons.
 *
 *
 * MAGPIE_CACHE_AGE - How long to store cached RSS objects? In seconds.
 *
 *
 * MAGPIE_CACHE_FRESH_ONLY - If remote fetch fails, throw error
 * instead of returning stale object?
 *
 * MAGPIE_DEBUG - Display debugging notices?
 *
 * MAGPIE_PROXY_HOST, MAGPIE_PROXY_PORT - Proxy settings if needed
*/


/*=======================================================================*\
    Function: fetch_rss: 
    Purpose:  return RSS object for the give url
              maintain the cache
    Input:    url of RSS file
              (optional) postdata to send (will force a POST rather than a GET)
    Output:   parsed RSS object (see rss_parse.inc)

    NOTES ON CACHEING:  
    If caching is on (MAGPIE_CACHE_ON) fetch_rss will first check the cache.
    
    NOTES ON RETRIEVING REMOTE FILES:
    If conditional gets are on (MAGPIE_CONDITIONAL_GET_ON) fetch_rss will
    return a cached object, and touch the cache object upon recieving a
    304.
    
    NOTES ON FAILED REQUESTS:
    If there is an HTTP error while fetching an RSS object, the cached
    version will be return, if it exists (and if MAGPIE_CACHE_FRESH_ONLY is off)
\*=======================================================================*/

define('MAGPIE_VERSION', '0.72');

$MAGPIE_ERROR = "";

function fetch_rss ($url, $postdata=null) {
    // initialize constants
    init();
    
    if ( !isset($url) ) {
        do_error("fetch_rss called without a url");
        return false;
    }
    
    // if cache is disabled
    if ( !MAGPIE_CACHE_ON ) {
        // fetch file, and parse it
        $resp = _fetch_remote_file( $url, null, $postdata );
        if ( is_success( $resp->status ) ) {
            return _response_to_rss( $resp );
        }
        else {
            do_error("Failed to fetch $url and cache is off");
            return false;
        }
    } 
    // else cache is ON
    else {
        // Flow
        // 1. check cache
        // 2. if there is a hit, make sure its fresh
        // 3. if cached obj fails freshness check, fetch remote
        // 4. if remote fails, return stale object, or error
        
        $cache = new RSSCache( MAGPIE_CACHE_DIR, MAGPIE_CACHE_AGE );
        
        if (MAGPIE_DEBUG and $cache->ERROR) {
            do_debug($cache->ERROR, E_USER_WARNING);
        }
        
        
        $cache_status    = 0;       // response of check_cache
        $request_headers = array(); // HTTP headers to send with fetch
        $rss             = 0;       // parsed RSS object
        $errormsg        = 0;       // errors, if any
        
        // store parsed XML by desired output encoding
        // as character munging happens at parse time
        $cache_key       = $url . MAGPIE_OUTPUT_ENCODING;
        // if we're sending postdata, we should add that to the cache key.
        // since GET data would be part of the url anyway.
        if (!empty($postdata) && is_array($postdata)) {
            $cache_key .= serialize($postdata);
        }

        if (!$cache->ERROR) {
            // return cache HIT, MISS, or STALE
            $cache_status = $cache->check_cache( $cache_key);
        }
                
        // if object cached, and cache is fresh, return cached obj
        if ( $cache_status == 'HIT' ) {
            $rss = $cache->get( $cache_key );
            if ( isset($rss) and $rss ) {
                // should be cache age
                $rss->from_cache = 1;
                if ( MAGPIE_DEBUG > 1) {
                    do_debug("MagpieRSS: Cache HIT", E_USER_NOTICE);
                }
                return $rss;
            }
        }
        
        // else attempt a conditional get
        
        // setup headers
        if ( $cache_status == 'STALE' ) {
            $rss = $cache->get( $cache_key );
            if ( $rss and !empty($rss->etag) and !empty($rss->last_modified) ) { // moodle fixes
                $request_headers['If-None-Match'] = $rss->etag;
                $request_headers['If-Last-Modified'] = $rss->last_modified;
            }
        }
        
        $resp = _fetch_remote_file( $url, $request_headers, $postdata );
        
        if (isset($resp) and $resp) {
          if ($resp->status == '304' ) {
                // we have the most current copy
                if ( MAGPIE_DEBUG > 1) {
                    do_debug("Got 304 for $url");
                }
                // reset cache on 304 (at minutillo insistent prodding)
                $cache->set($cache_key, $rss);
                return $rss;
            }
            elseif ( is_success( $resp->status ) ) {
                $rss = _response_to_rss( $resp );
                if ( $rss ) {
                    if (MAGPIE_DEBUG > 1) {
                        do_debug("Fetch successful");
                    }
                    // add object to cache
                    $cache->set( $cache_key, $rss );
                    return $rss;
                }
            }
            else {
                $errormsg = "Failed to fetch $url ";
                if ( $resp->status == '-100' ) {
                    $errormsg .= "(Request timed out after " . MAGPIE_FETCH_TIME_OUT . " seconds)";
                }
                elseif ( $resp->error ) {
                    # compensate for Snoopy's annoying habbit to tacking
                    # on '\n'
                    $http_error = trim($resp->error); // moodle fix 
                    $errormsg .= "(HTTP Error: $http_error)";
                }
                else {
                    $errormsg .=  "(HTTP Response: " . $resp->response_code .')';
                }
            }
        }
        else {
            $errormsg = "Unable to retrieve RSS file for unknown reasons.";
        }
        
        // else fetch failed
        
        // attempt to return cached object
        if ($rss) {
            if ( MAGPIE_DEBUG ) {
                do_debug("Returning STALE object for $url");
            }
            return $rss;
        }
        
        // else we totally failed
        do_error( $errormsg ); 
        
        return false;
        
    } // end if ( !MAGPIE_CACHE_ON ) {
} // end fetch_rss()

/*=======================================================================*\
    Function:   error
    Purpose:    set MAGPIE_ERROR, and trigger error
\*=======================================================================*/
//Daryl Hawes note: had to rename this function from error to do_error for moodle
// due to conflict with existing moodle function name
function do_error ($errormsg, $lvl=E_USER_WARNING) {
        global $MAGPIE_ERROR;
        
        // append PHP's error message if track_errors enabled
        if ( isset($php_errormsg) ) { 
            $errormsg .= " ($php_errormsg)";
        }
        if ( $errormsg ) {
            $errormsg = "MagpieRSS: $errormsg";
            $MAGPIE_ERROR = $errormsg;
            trigger_error( $errormsg, $lvl);                
        }
}

//Daryl Hawes note: renamed this function from debug to do_debug for moodle
function do_debug ($debugmsg, $lvl=E_USER_NOTICE) {
    trigger_error("MagpieRSS [debug] $debugmsg", $lvl);
}
            
/*=======================================================================*\
    Function:   magpie_error
    Purpose:    accessor for the magpie error variable
\*=======================================================================*/
function magpie_error ($errormsg="") {
    global $MAGPIE_ERROR;
    
    if ( isset($errormsg) and $errormsg ) { 
        $MAGPIE_ERROR = $errormsg;
    }
    
    return $MAGPIE_ERROR;   
}

/*=======================================================================*\
    Function:   _fetch_remote_file
    Purpose:    retrieve an arbitrary remote file
    Input:      url of the remote file
                headers to send along with the request (optional)
    Output:     an HTTP response object (see Snoopy.class.inc)  
\*=======================================================================*/
function _fetch_remote_file ($url, $headers = "", $postdata=null ) {
    // moodlefix
    return download_file_content($url, $headers, $postdata, true, MAGPIE_FETCH_TIME_OUT);
/*
    // Snoopy is an HTTP client in PHP
    $client = new Snoopy();
    $client->proxy_host = MAGPIE_PROXY_HOST;
    $client->proxy_port = MAGPIE_PROXY_PORT;
    $client->agent = MAGPIE_USER_AGENT;
    $client->read_timeout = MAGPIE_FETCH_TIME_OUT;
    $client->use_gzip = MAGPIE_USE_GZIP;
    if (is_array($headers) ) {
        $client->rawheaders = $headers;
    }
    if (!empty($postdata) && is_array($postdata)) {
        @$client->submit($url,$postdata);
    } else {
        @$client->fetch($url);
    }
    return $client;
*/
    // end of moodlefix

}

/*=======================================================================*\
    Function:   _response_to_rss
    Purpose:    parse an HTTP response object into an RSS object
    Input:      an HTTP response object (see Snoopy)
    Output:     parsed RSS object (see rss_parse)
\*=======================================================================*/
function _response_to_rss ($resp) {
    $rss = new MagpieRSS( $resp->results, MAGPIE_OUTPUT_ENCODING, MAGPIE_INPUT_ENCODING, MAGPIE_DETECT_ENCODING );
    
    // if RSS parsed successfully       
    if ( $rss and !$rss->ERROR) {
        
        // find Etag, and Last-Modified
        foreach($resp->headers as $h) {
            // 2003-03-02 - Nicola Asuni (www.tecnick.com) - fixed bug "Undefined offset: 1"
            if (strpos($h, ": ")) {
                list($field, $val) = explode(": ", $h, 2);
            }
            else {
                $field = $h;
                $val = "";
            }
// start of moodle modification
            if ( strtolower($field) == 'etag' ) { // field names are case insensitive - sites are sending Etag, ETag, etc.
                $rss->etag = rtrim($val);         // trailing newline problem discovered by Matthew Bockol
            }
            
            if ( strtolower($field) == 'last-modified' ) { // field names are case insensitive
                $rss->last_modified = rtrim($val);         // we do not want any whitespace after it
            }
// end of moodle modification
        }
        
        return $rss;    
    } // else construct error message
    else {
        $errormsg = "Failed to parse RSS file.";
        
        if ($rss) {
            $errormsg .= " (" . $rss->ERROR . ")";
        }
        do_error($errormsg);
        
        return false;
    } // end if ($rss and !$rss->error)
}

/*=======================================================================*\
    Function:   init
    Purpose:    setup constants with default values
                check for user overrides
\*=======================================================================*/
function init () {
    if ( defined('MAGPIE_INITALIZED') ) {
        return;
    }
    else {
        define('MAGPIE_INITALIZED', true);
    }
    
    if ( !defined('MAGPIE_CACHE_ON') ) {
        define('MAGPIE_CACHE_ON', true);
    }

    if ( !defined('MAGPIE_CACHE_DIR') ) {
        define('MAGPIE_CACHE_DIR', './cache');
    }

    if ( !defined('MAGPIE_CACHE_AGE') ) {
        define('MAGPIE_CACHE_AGE', 60*60); // one hour
    }

    if ( !defined('MAGPIE_CACHE_FRESH_ONLY') ) {
        define('MAGPIE_CACHE_FRESH_ONLY', false);
    }

    if ( !defined('MAGPIE_OUTPUT_ENCODING') ) {
        define('MAGPIE_OUTPUT_ENCODING', 'ISO-8859-1');
    }
    
    if ( !defined('MAGPIE_INPUT_ENCODING') ) {
        define('MAGPIE_INPUT_ENCODING', null);
    }
    
    if ( !defined('MAGPIE_DETECT_ENCODING') ) {
        define('MAGPIE_DETECT_ENCODING', true);
    }
    
    if ( !defined('MAGPIE_DEBUG') ) {
        define('MAGPIE_DEBUG', 0);
    }
    
    if ( !defined('MAGPIE_USER_AGENT') ) {
        $ua = 'MagpieRSS/'. MAGPIE_VERSION . ' (+http://magpierss.sf.net';
        
        if ( MAGPIE_CACHE_ON ) {
            $ua = $ua . ')';
        }
        else {
            $ua = $ua . '; No cache)';
        }
        
        define('MAGPIE_USER_AGENT', $ua);
    }
    
    if ( !defined('MAGPIE_FETCH_TIME_OUT') ) {
        define('MAGPIE_FETCH_TIME_OUT', 5); // 5 second timeout
    }
    
    // use gzip encoding to fetch rss files if supported?
    if ( !defined('MAGPIE_USE_GZIP') ) {
        define('MAGPIE_USE_GZIP', true);    
    }
}

// NOTE: the following code should really be in Snoopy, or at least
// somewhere other then rss_fetch!

/*=======================================================================*\
    HTTP STATUS CODE PREDICATES
    These functions attempt to classify an HTTP status code
    based on RFC 2616 and RFC 2518.
    
    All of them take an HTTP status code as input, and return true or false

    All this code is adapted from LWP's HTTP::Status.
\*=======================================================================*/


/*=======================================================================*\
    Function:   is_info
    Purpose:    return true if Informational status code
\*=======================================================================*/
function is_info ($sc) { 
    return $sc >= 100 && $sc < 200; 
}

/*=======================================================================*\
    Function:   is_success
    Purpose:    return true if Successful status code
\*=======================================================================*/
function is_success ($sc) { 
    return $sc >= 200 && $sc < 300; 
}

/*=======================================================================*\
    Function:   is_redirect
    Purpose:    return true if Redirection status code
\*=======================================================================*/
function is_redirect ($sc) { 
    return $sc >= 300 && $sc < 400; 
}

/*=======================================================================*\
    Function:   is_error
    Purpose:    return true if Error status code
\*=======================================================================*/
function is_error ($sc) { 
    return $sc >= 400 && $sc < 600; 
}

/*=======================================================================*\
    Function:   is_client_error
    Purpose:    return true if Error status code, and its a client error
\*=======================================================================*/
function is_client_error ($sc) { 
    return $sc >= 400 && $sc < 500; 
}

/*=======================================================================*\
    Function:   is_client_error
    Purpose:    return true if Error status code, and its a server error
\*=======================================================================*/
function is_server_error ($sc) { 
    return $sc >= 500 && $sc < 600; 
}

?>
