<?PHP  // $Id$

define("REFERENCE",   "1");
define("WEBPAGE",     "2");
define("UPLOADEDFILE","3");
define("PLAINTEXT",   "4");
define("WEBLINK",     "5");
define("HTML",        "6");
define("PROGRAM",     "7");
define("WIKITEXT",    "8");
define("DIRECTORY",   "9");

$RESOURCE_TYPE = array (REFERENCE    => get_string("resourcetype1", "resource"),
                        WEBPAGE      => get_string("resourcetype2", "resource"),
                        UPLOADEDFILE => get_string("resourcetype3", "resource"),
                        PLAINTEXT    => get_string("resourcetype4", "resource"),
                        WEBLINK      => get_string("resourcetype5", "resource"),
                        HTML         => get_string("resourcetype6", "resource"),
                        PROGRAM      => get_string("resourcetype7", "resource"),
                        WIKITEXT     => get_string("resourcetype8", "resource"),
                        DIRECTORY    => get_string("resourcetype9", "resource") );

if (!isset($CFG->resource_framesize)) {
    set_config("resource_framesize", 130);
} 

if (!isset($CFG->resource_websearch)) {
    set_config("resource_websearch", "http://google.com/");
} 

if (!isset($CFG->resource_defaulturl)) {
    set_config("resource_defaulturl", "http://");
}  

if (!isset($CFG->resource_filterexternalpages)) {
    set_config("resource_filterexternalpages", false);
}  

$RESOURCE_WINDOW_OPTIONS = array("resizable", "scrollbars", "directories", "location", 
                                 "menubar", "toolbar", "status", "height", "width");

if (!isset($CFG->resource_popup)) {
    set_config("resource_popup", "");
}  

foreach ($RESOURCE_WINDOW_OPTIONS as $popupoption) {
    $popupoption = "resource_popup$popupoption";
    if (!isset($CFG->$popupoption)) {
        if ($popupoption == "resource_popupheight") {
            set_config($popupoption, 450);
        } else if ($popupoption == "resource_popupwidth") {
            set_config($popupoption, 620);
        } else {
            set_config($popupoption, "checked");
        }
    }  
}

function resource_add_instance($resource) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    global $RESOURCE_WINDOW_OPTIONS;

    $resource->timemodified = time();

    if (isset($resource->setnewwindow)) {
        $optionlist = array();
        foreach ($RESOURCE_WINDOW_OPTIONS as $option) {
            if (isset($resource->$option)) {
                $optionlist[] = $option."=".$resource->$option;
            }
        }
        $resource->alltext = implode(',', $optionlist);
    }

    return insert_record("resource", $resource);
}


function resource_update_instance($resource) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    global $RESOURCE_WINDOW_OPTIONS;

    $resource->id = $resource->instance;
    $resource->timemodified = time();

    if (isset($resource->setnewwindow)) {
        $optionlist = array();
        foreach ($RESOURCE_WINDOW_OPTIONS as $option) {
            if (isset($resource->$option)) {
                $optionlist[] = $option."=".$resource->$option;
            }
        }
        $resource->alltext = implode(',', $optionlist);
    }

    return update_record("resource", $resource);
}


function resource_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $resource = get_record("resource", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("resource", "id", "$resource->id")) {
        $result = false;
    }

    return $result;
}


function resource_user_outline($course, $user, $mod, $resource) {
    if ($logs = get_records_select("log", "userid='$user->id' AND module='resource' 
                                           AND action='view' AND info='$resource->id'", "time ASC")) {

        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $result->info = get_string("numviews", "", $numviews);
        $result->time = $lastlog->time;

        return $result;
    }
    return NULL;
}


function resource_user_complete($course, $user, $mod, $resource) {
    global $CFG, $THEME;

    if ($logs = get_records_select("log", "userid='$user->id' AND module='resource' 
                                           AND action='view' AND info='$resource->id'", "time ASC")) {
        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $strmostrecently = get_string("mostrecently");
        $strnumviews = get_string("numviews", "", $numviews);

        echo "$strnumviews - $strmostrecently ".userdate($lastlog->time);

    } else {
        print_string("neverseen", "resource");
    }
}

function resource_get_participants($resourceid) {
//Returns the users with data in one resource
//(NONE, byt must exists on EVERY mod !!)

    return false;
}

function resource_get_coursemodule_info($coursemodule) {
/// Given a course_module object, this function returns any 
/// "extra" information that may be needed when printing
/// this activity in a course listing.
///
/// See get_array_of_activities() in course/lib.php
///

   global $CFG;

   $info = NULL;

   if ($resource = get_record("resource", "id", $coursemodule->instance)) {
       if (($resource->type == UPLOADEDFILE or $resource->type == WEBLINK) and !empty($resource->alltext)) {
           $info->extra =  urlencode("target=\"resource$resource->id\" onClick=\"return ".
                            "openpopup('/mod/resource/view.php?inpopup=true&id=".
                            $coursemodule->id.
                            "','resource$resource->id','$resource->alltext');\"");
       }

       require_once("$CFG->dirroot/files/mimetypes.php");

       if ($resource->type == UPLOADEDFILE or $resource->type == WEBLINK or $resource->type == WEBPAGE) {
	       $icon = mimeinfo("icon", $resource->reference);
           if ($icon != 'unknown.gif') {
		       $info->icon ="f/$icon";
           }
       } else if ($resource->type == DIRECTORY) {
		   $info->icon ="f/folder.gif";
       }
   }

   return $info;
}
 
function resource_fetch_remote_file ($cm, $url, $headers = "" ) {
/// Snoopy is an HTTP client in PHP

    global $CFG;

    require_once("$CFG->libdir/snoopy/Snoopy.class.inc");

    $client = new Snoopy();
    $ua = 'Moodle/'. $CFG->release . ' (+http://moodle.org';    
    if ( $CFG->resource_usecache ) {
        $ua = $ua . ')';
    } else {
        $ua = $ua . '; No cache)';
    }
    $client->agent = $ua;
    $client->read_timeout = 5;
    $client->use_gzip = true;
    if (is_array($headers) ) {
        $client->rawheaders = $headers;
    }
    
    @$client->fetch($url);
    if ( $client->status >= 200 && $client->status < 300 ) {
        $tags = array("A"      => "href=",
                      "IMG"    => "src=",
                      "LINK"   => "href=",
                      "AREA"   => "href=",
                      "FRAME"  => "src=",
                      "IFRAME" => "src=",
                      "FORM"   => "action=");
    
        foreach ($tags as $tag => $key) {
            $prefix = "fetch.php?id=$cm->id&url=";
            if ( $tag == "IMG" or $tag == "LINK" or $tag == "FORM") {
                $prefix = "";
            }
            $client->results = resource_redirect_tags($client->results, $url, $tag, $key,$prefix);
        }
    } else {
        if ( $client->status >= 400 && $client->status < 500) {
            $client->results = get_string("fetchclienterror","resource");  // Client error
        } elseif ( $client->status >= 500 && $client->status < 600) {
            $client->results = get_string("fetchservererror","resource");  // Server error
        } else {
            $client->results = get_string("fetcherror","resource");     // Redirection? HEAD? Unknown error.
        }
    }
    return $client;
}

function resource_redirect_tags($text, $url, $tagtoparse, $keytoparse,$prefix = "" ) {
    $valid = 1;
    if ( strpos($url,"?") == FALSE ) {
        $valid = 1;
    }
    if ( $valid ) {
        $lastpoint = strrpos($url,".");
        $lastslash = strrpos($url,"/");
        if ( $lastpoint > $lastslash ) {
            $root = substr($url,0,$lastslash+1);
        } else {
            $root = $url;
        }
        if ( $root == "http://" or 
             $root == "https://") {
            $root = $url;
        }
        if ( substr($root,strlen($root)-1) == '/' ) {
            $root = substr($root,0,-1);
        }
        
        $mainroot = $root;
        $lastslash = strrpos($mainroot,"/");
        while ( $lastslash > 9) {
            $mainroot = substr($mainroot,0,$lastslash);
        
            $lastslash = strrpos($mainroot,"/");
        }

        $regex = "/<$tagtoparse (.+?)>/is";    
        $count = preg_match_all($regex, $text, $hrefs);    
        for ( $i = 0; $i < $count; $i++) {
            $tag = $hrefs[1][$i];
            
            $poshref = strpos(strtolower($tag),strtolower($keytoparse));
            $start = $poshref + strlen($keytoparse);
            $left = substr($tag,0,$start);
            if ( $tag[$start] == '"' ) {
                $left .= '"';
                $start++;
            }
            $posspace   = strpos($tag," ", $start+1);
            $right = "";
            if ( $posspace != FALSE) {
                $right = substr($tag, $posspace);
            }
            $end = strlen($tag)-1;
            if ( $tag[$end] == '"' ) {
                $right = '"' . $right;
            }
            $finalurl = substr($tag,$start,$end-$start+$diff);
            // Here, we could have these possible values for $finalurl:
            //     file.ext                             Add current root dir
            //     http://(domain)                      don't care
            //     http://(domain)/                     don't care
            //     http://(domain)/folder               don't care
            //     http://(domain)/folder/              don't care
            //     http://(domain)/folder/file.ext      don't care
            //     folder/                              Add current root dir
            //     folder/file.ext                      Add current root dir
            //     /folder/                             Add main root dir
            //     /folder/file.ext                     Add main root dir

            // Special case: If finalurl contains a ?, it won't be parsed
            $valid = 1;

            if ( strpos($finalurl,"?") == FALSE ) {
                $valid = 1;
            }
            if ( $valid ) {
                if ( $finalurl[0] == "/" ) {
                    $finalurl = $mainroot . $finalurl;
                } elseif ( strtolower(substr($finalurl,0,7)) != "http://" and 
                           strtolower(substr($finalurl,0,8)) != "https://") {
                     if ( $finalurl[0] == "/") {
                        $finalurl = $mainroot . $finalurl;
                     } else {
                        $finalurl = "$root/$finalurl";
                     }
                }
    
                $text = str_replace($tag,"$left$prefix$finalurl$right",$text);
            }
        }
    }
    return $text;
}

?>
