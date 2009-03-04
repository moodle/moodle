<?php 
// Outputs pictures from theme or core pix folder. Only used if $CFG->smartpix is
// turned on.

$matches=array(); // Reusable array variable for preg_match
 
// This does NOT use config.php. This is because doing that makes database requests
// which cause this to take longer (I benchmarked this at 16ms, 256ms with config.php)
// A version using normal Moodle functions is included in comment at end in case we
// want to switch to it in future. 

function error($text,$notfound=false) {
    header($notfound ? 'HTTP/1.0 404 Not Found' : 'HTTP/1.0 500 Internal Server Error');
    header('Content-Type: text/plain');
    print $text;
    exit;
}

// Nicked from moodlelib clean_param
function makesafe($param) {
    $param = str_replace('\\\'', '\'', $param);
    $param = str_replace('\\"', '"', $param);
    $param = str_replace('\\', '/', $param);
    $param = ereg_replace('[[:cntrl:]]|[<>"`\|\':]', '', $param);
    $param = ereg_replace('\.\.+', '', $param);
    $param = ereg_replace('//+', '/', $param);
    return ereg_replace('/(\./)+', '/', $param);
}

// Nicked from weblib
/**
 * Remove query string from url
 *
 * Takes in a URL and returns it without the querystring portion
 *
 * @param string $url the url which may have a query string attached
 * @return string
 */
function strip_querystring($url) {

    if ($commapos = strpos($url, '?')) {
        return substr($url, 0, $commapos);
    } else {
        return $url;
    }
}
 
// get query string
function get_query($name) {
    if (!empty($_SERVER['REQUEST_URI'])) {
        return explode($name, $_SERVER['REQUEST_URI']);
    } else if (!empty($_SERVER['QUERY_STRING'])) {
        return array('', '?'. $_SERVER['QUERY_STRING']);
    } else {
        return false;
    }
}
// Nicked from weblib then cutdown
/**
 * Extracts file argument either from file parameter or PATH_INFO. 
 * @param string $scriptname name of the calling script
 * @return string file path (only safe characters)
 */
function get_file_argument_limited($scriptname) {
    $relativepath = FALSE;

    // first try normal parameter (compatible method == no relative links!)
    if(isset($_GET['file'])) {
        return makesafe($_GET['file']);
    }

    // then try extract file from PATH_INFO (slasharguments method)
    if (!empty($_SERVER['PATH_INFO'])) {
        $path_info = $_SERVER['PATH_INFO'];
        // check that PATH_INFO works == must not contain the script name
        if (!strpos($path_info, $scriptname)) {
            return makesafe(rawurldecode($path_info));
        }
    }

    // now if both fail try the old way
    // (for compatibility with misconfigured or older buggy php implementations)
    $arr = get_query($scriptname);
    if (!empty($arr[1])) {
        return makesafe(rawurldecode(strip_querystring($arr[1])));
    }
    
    error('Unexpected PHP set up. Turn off the smartpix config option.');
} 
 
// We do need to get dirroot from config.php
if(!$config=@file_get_contents(dirname(__FILE__).'/../config.php')) {
    error("Can't open config.php");
}
$configlines=preg_split('/[\r\n]+/',$config);
foreach($configlines as $configline) {
    if(preg_match('/^\s?\$CFG->dirroot\s*=\s*[\'"](.*?)[\'"]\s*;/',$configline,$matches)) {
        $dirroot=$matches[1];
    }
    if(preg_match('/^\s?\$CFG->dataroot\s*=\s*[\'"](.*?)[\'"]\s*;/',$configline,$matches)) {
        $dataroot=$matches[1];
    }
    if(isset($dirroot) && isset($dataroot)) {
        break;
    }
}
if(!(isset($dirroot) && isset($dataroot))) {
    error('No line in config.php like $CFG->dirroot=\'/somewhere/whatever\';');
}

// Split path - starts with theme name, then actual image path inside pix
$path=get_file_argument_limited('smartpix.php');
$match=array();
if(!preg_match('|^/([a-zA-Z0-9_\-.]+)/([a-zA-Z0-9/_\-.]+)$|',$path,$match)) {
    error('Unexpected request format');
}
list($junk,$theme,$path)=$match;

// Check file type
if(preg_match('/\.png$/',$path)) {
    $mimetype='image/png';
} else if(preg_match('/\.gif$/',$path)) {
    $mimetype='image/gif';
} else if(preg_match('/\.jpe?g$/',$path)) {
    $mimetype='image/jpeg';
} else {
    // Note that this is a security feature as well as a lack of mime type
    // support :) Means this can't accidentally serve files from places it
    // shouldn't. Without it, you can actually access any file inside the
    // module code directory.
    error('Request for non-image file');
}

// Find actual location of image as $file
$file=false;
if(file_exists($possibility="$dirroot/theme/$theme/pix/$path")) {
    // Found the file in theme, stop looking
    $file=$possibility;
} else {
    // Is there a parent theme?
    while(true) {        
        require("$dirroot/theme/$theme/config.php"); // Sets up $THEME
        if(!$THEME->parent) {
            break;
        }        
        $theme=$THEME->parent;
        if(file_exists($possibility="$dirroot/theme/$theme/pix/$path")) {
            $file=$possibility;
            // Found in parent theme
            break;
        }    
    }
    if(!$file) {
        if(preg_match('|^mod/|',$path)) {
            if(!file_exists($possibility="$dirroot/$path")) {
                error('Requested image not found.',true);
            }
        } else {
            if(!file_exists($possibility="$dirroot/pix/$path")) {
                error('Requested image not found.',true);
            }
        }
        $file=$possibility;
    }
}

// Now we have a file that exists. Not using send_file since it requires
// proper $CFG etc.

// Handle If-Modified-Since
$filedate=filemtime($file);
$ifmodifiedsince=isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
if($ifmodifiedsince && strtotime($ifmodifiedsince)>=$filedate) {
    header('HTTP/1.0 304 Not Modified');
    exit;
}
header('Last-Modified: '.gmdate('D, d M Y H:i:s',$filedate).' GMT');

// As I'm not loading config table from DB, this is hardcoded here; expiry 
// 4 hours, unless the hacky file reduceimagecache.dat exists in dataroot
if(file_exists($reducefile=$dataroot.'/reduceimagecache.dat')) {
    $lifetime=file_read_contents($reducefile);
} else {   
    $lifetime=4*60*60;
}

// Send expire headers
header('Cache-Control: max-age='.$lifetime);
header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');

// Type
header('Content-Type: '.$mimetype);
header('Content-Length: '.filesize($file));

// Output file
$handle=fopen($file,'r');
fpassthru($handle);
fclose($handle);

// Slower Moodle-style version follows:
 
//// Outputs pictures from theme or core pix folder. Only used if $CFG->smartpix is
//// turned on.
//
//$nomoodlecookie = true; // Stops it making a session
//require_once('../config.php');
//require_once('../lib/filelib.php');
//global $CFG;
//
//$matches=array(); // Reusable array variable for preg_match
// 
//// Split path - starts with theme name, then actual image path inside pix
//$path=get_file_argument('smartpix.php');
//$match=array();
//if(!preg_match('|^/([a-z0-9_\-.]+)/([a-z0-9/_\-.]+)$|',$path,$match)) {
//    error('Unexpected request format');
//}
//list($junk,$theme,$path)=$match;
//
//// Check file type - this is not needed for the MIME types as we could
//// get those by the existing function, but it provides an extra layer of security
//// as otherwise this script could be used to view all files within dirroot/mod
//if(preg_match('/\.png$/',$path)) {
//    $mimetype='image/png';
//} else if(preg_match('/\.gif$/',$path)) {
//    $mimetype='image/gif';
//} else if(preg_match('/\.jpe?g$/',$path)) {
//    $mimetype='image/jpeg';
//} else {
//    error('Request for non-image file');
//}
//
//// Find actual location of image as $file
//$file=false;
//if(file_exists($possibility="$CFG->dirroot/theme/$theme/pix/$path")) {
//    // Found the file in theme, stop looking
//    $file=$possibility;
//} else {
//    // Is there a parent theme?
//    while(true) {        
//        require("$CFG->dirroot/theme/$theme/config.php"); // Sets up $THEME
//        if(!$THEME->parent) {
//            break;
//        }        
//        $theme=$THEME->parent;
//        if(file_exists($possibility="$CFG->dirroot/theme/$theme/pix/$path")) {
//            $file=$possibility;
//            // Found in parent theme
//            break;
//        }    
//    }
//    if(!$file) {
//        if(preg_match('|^mod/|',$path)) {
//            if(!file_exists($possibility="$CFG->dirroot/$path")) {
//                error('Requested image not found.');
//            }
//        } else {
//            if(!file_exists($possibility="$CFG->dirroot/pix/$path")) {
//                error('Requested image not found.');
//            }
//        }
//        $file=$possibility;
//    }
//}
//
//// Handle If-Modified-Since because send_file doesn't
//$filedate=filemtime($file);
//$ifmodifiedsince=isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
//if($ifmodifiedsince && strtotime($ifmodifiedsince)>=$filedate) {
//    header('HTTP/1.0 304 Not Modified');
//    exit;
//}
//// Don't need to set last-modified, send_file does that
//
//if (empty($CFG->filelifetime)) {
//    $lifetime = 86400;     // Seconds for files to remain in caches
//} else {
//    $lifetime = $CFG->filelifetime;
//}
//send_file($file,preg_replace('|^.*/|','',$file),$lifetime);
?>
