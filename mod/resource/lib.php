<?php  // $Id$

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

if (!isset($CFG->resource_secretphrase)) {
    set_config("resource_secretphrase", random_string(20));
}

if (!isset($CFG->resource_popup)) {
    set_config("resource_popup", "");
}

if (!isset($CFG->resource_windowsettings)) {
    set_config("resource_windowsettings", "0");
}

if (!isset($CFG->resource_parametersettings)) {
    set_config("resource_parametersettings", "0");
}

if (!isset($CFG->resource_allowlocalfiles)) {
    set_config("resource_allowlocalfiles", "0");
}

if (!isset($CFG->resource_hide_repository)) {
    set_config("resource_hide_repository", "1");
}

define('RESOURCE_LOCALPATH', 'LOCALPATH');

$RESOURCE_WINDOW_OPTIONS = array('resizable', 'scrollbars', 'directories', 'location',
                                 'menubar', 'toolbar', 'status', 'height', 'width');

foreach ($RESOURCE_WINDOW_OPTIONS as $popupoption) {
    $popupoption = "resource_popup$popupoption";
    if (!isset($CFG->$popupoption)) {
        if ($popupoption == 'resource_popupheight') {
            set_config($popupoption, 450);
        } else if ($popupoption == 'resource_popupwidth') {
            set_config($popupoption, 620);
        } else {
            set_config($popupoption, 'checked');
        }
    }
}

/**
* resource_base is the base class for resource types
*
* This class provides all the functionality for a resource
*/

class resource_base {

var $cm;
var $course;
var $resource;


/**
* Constructor for the base resource class
*
* Constructor for the base resource class.
* If cmid is set create the cm, course, resource objects.
* and do some checks to make sure people can be here, and so on.
*
* @param cmid   integer, the current course module id - not set for new resources
*/
function resource_base($cmid=0) {

    global $CFG;
    global $course;   // Ugly hack, needed for course language ugly hack

    if ($cmid) {
        if (! $this->cm = get_record("course_modules", "id", $cmid)) {
            error("Course Module ID was incorrect");
        }

        if (! $this->course = get_record("course", "id", $this->cm->course)) {
            error("Course is misconfigured");
        }

        $course = $this->course;  // Make it a global so we can see it later

        require_course_login($this->course, true, $this->cm);

        if (! $this->resource = get_record("resource", "id", $this->cm->instance)) {
            error("Resource ID was incorrect");
        }

        $this->strresource  = get_string("modulename", "resource");
        $this->strresources = get_string("modulenameplural", "resource");

        if ($this->course->category) {
            $this->navigation = "<a target=\"{$CFG->framename}\" href=\"$CFG->wwwroot/course/view.php?id={$this->course->id}\">{$this->course->shortname}</a> -> ".
                                "<a target=\"{$CFG->framename}\" href=\"index.php?id={$this->course->id}\">$this->strresources</a> ->";
        } else {
            $this->navigation = "<a target=\"{$CFG->framename}\" href=\"index.php?id={$this->course->id}\">$this->strresources</a> ->";
        }

        if (!$this->cm->visible and !isteacher($this->course->id)) {
            $pagetitle = strip_tags($this->course->shortname.': '.$this->strresource);
            print_header($pagetitle, $this->course->fullname, "$this->navigation $this->strresource", "", "", true, '', navmenu($this->course, $this->cm));
            notice(get_string("activityiscurrentlyhidden"), "$CFG->wwwroot/course/view.php?id={$this->course->id}");
        }
    }
}


/**
* Display function does nothing in the base class
*/
function display() {

}


/**
* Display the resource with the course blocks.
*/
function display_course_blocks_start() {

    global $CFG;
    global $USER;

    require_once($CFG->libdir.'/blocklib.php');
    require_once($CFG->libdir.'/pagelib.php');

    $PAGE = page_create_object(PAGE_COURSE_VIEW, $this->course->id);
    $this->PAGE = $PAGE;
    $pageblocks = blocks_setup($PAGE);

    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

/// Print the page header

    if (!empty($edit) && $PAGE->user_allowed_editing()) {
        if ($edit == 'on') {
            $USER->editing = true;
        } else if ($edit == 'off') {
            $USER->editing = false;
        }
    }
    $morebreadcrumbs = array($this->strresources   => 'index.php?id='.$this->course->id,
                             $this->resource->name => '');

    $PAGE->print_header($this->course->shortname.': %fullname%', $morebreadcrumbs);

    echo '<table id="layout-table"><tr>';

    if((blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing())) {
        echo '<td style="width: '.$blocks_preferred_width.'px;" id="left-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
        echo '</td>';
    }

    echo '<td id="middle-column">';
    echo '<div id="resource">';

}


/**
 * Finish displaying the resource with the course blocks
 */
function display_course_blocks_end() {

    global $CFG;

    $PAGE = $this->PAGE;
    $pageblocks = blocks_setup($PAGE);
    $blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 210);

    echo '</div>';
    echo '</td>';

    if((blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $PAGE->user_is_editing())) {
        echo '<td style="width: '.$blocks_preferred_width.'px;" id="right-column">';
        blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
        echo '</td>';
    }

    echo '</tr></table>';

    print_footer($this->course);

}


function setup(&$form) {
    global $CFG, $usehtmleditor;

    if (! empty($form->course)) {
        if (! $this->course = get_record("course", "id", $form->course)) {
            error("Course is misconfigured");
        }
    }

    if (empty($form->name)) {
        $form->name = "";
    }
    if (empty($form->type)) {
        $form->type = "";
    }
    if (empty($form->summary)) {
        $form->summary = "";
    }
    if (empty($form->reference)) {
        $form->reference = "";
    }
    if (empty($form->alltext)) {
        $form->alltext = "";
    }
    if (empty($form->options)) {
        $form->options = "";
    }
    $nohtmleditorneeded = true;

    print_heading_with_help(get_string("resourcetype$form->type", 'resource'), $form->type, 'resource/type');

    include("$CFG->dirroot/mod/resource/type/common.html");
}


function setup_end() {
    global $CFG;

    include("$CFG->dirroot/mod/resource/type/common_end.html");
}


function add_instance($resource) {
// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will create a new instance and return the id number
// of the new instance.

    global $RESOURCE_WINDOW_OPTIONS;

    $resource->timemodified = time();

    if (isset($resource->windowpopup)) {
        if ($resource->windowpopup) {
            $optionlist = array();
            foreach ($RESOURCE_WINDOW_OPTIONS as $option) {
                if (isset($resource->$option)) {
                    $optionlist[] = $option."=".$resource->$option;
                }
            }
            $resource->popup = implode(',', $optionlist);
            $resource->options = "";
        } else {
            if (isset($resource->framepage)) {
                $resource->options = "frame";
            } else {
                $resource->options = "";
            }
            $resource->popup = "";
        }
    }

    if (isset($resource->parametersettingspref)) {
        set_user_preference('resource_parametersettingspref', $resource->parametersettingspref);
    }
    if (isset($resource->windowsettingspref)) {
        set_user_preference('resource_windowsettingspref', $resource->windowsettingspref);
    }

    return insert_record("resource", $resource);
}


function update_instance($resource) {
// Given an object containing all the necessary data,
// (defined by the form in mod.html) this function
// will update an existing instance with new data.

    global $RESOURCE_WINDOW_OPTIONS;

    $resource->id = $resource->instance;
    $resource->timemodified = time();

    if (isset($resource->windowpopup)) {
        if ($resource->windowpopup) {
            $optionlist = array();
            foreach ($RESOURCE_WINDOW_OPTIONS as $option) {
                if (isset($resource->$option)) {
                    $optionlist[] = $option."=".$resource->$option;
                }
            }
            $resource->popup = implode(',', $optionlist);
            $resource->options = "";
        } else {
            if (isset($resource->framepage)) {
                $resource->options = "frame";
            } else {
                $resource->options = "";
            }
            $resource->popup = "";
        }
    }

    if (isset($resource->parametersettingspref)) {
        set_user_preference('resource_parametersettingspref', $resource->parametersettingspref);
    }
    if (isset($resource->windowsettingspref)) {
        set_user_preference('resource_windowsettingspref', $resource->windowsettingspref);
    }

    return update_record("resource", $resource);
}


function delete_instance($id) {
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



} /// end of class definition



function resource_add_instance($resource) {
    global $CFG;

    $resource->type = clean_filename($resource->type);   // Just to be safe

    require_once("$CFG->dirroot/mod/resource/type/$resource->type/resource.class.php");
    $resourceclass = "resource_$resource->type";
    $res = new $resourceclass();

    return $res->add_instance($resource);
}

function resource_update_instance($resource) {
    global $CFG;

    $resource->type = clean_filename($resource->type);   // Just to be safe

    require_once("$CFG->dirroot/mod/resource/type/$resource->type/resource.class.php");
    $resourceclass = "resource_$resource->type";
    $res = new $resourceclass();

    return $res->update_instance($resource);
}

function resource_delete_instance($id) {
    global $CFG;

    if (! $resource = get_record("resource", "id", "$id")) {
        return false;
    }

    $resource->type = clean_filename($resource->type);   // Just to be safe

    require_once("$CFG->dirroot/mod/resource/type/$resource->type/resource.class.php");
    $resourceclass = "resource_$resource->type";
    $res = new $resourceclass();

    return $res->delete_instance($id);
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
    global $CFG;

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
       if (!empty($resource->popup)) {
           $info->extra =  urlencode("target=\"resource$resource->id\" onclick=\"return ".
                            "openpopup('/mod/resource/view.php?inpopup=true&amp;id=".
                            $coursemodule->id.
                            "','resource$resource->id','$resource->popup');\"");
       }

       require_once($CFG->libdir.'/filelib.php');

       if ($resource->type == 'file') {
           $icon = mimeinfo("icon", $resource->reference);
           if ($icon != 'unknown.gif') {
               $info->icon ="f/$icon";
           } else {
               $info->icon ="f/web.gif";
           }
       } else if ($resource->type == 'directory') {
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
            $prefix = "fetch.php?id=$cm->id&amp;url=";
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

function resource_is_url($path) {
    if (strpos($path, '://')) {     // eg http:// https:// ftp://  etc
        return true;
    }
    if (strpos($path, '/') === 0) { // Starts with slash
        return true;
    }
    return false;
}

function resource_get_resource_types() {
/// Returns a menu of current resource types, in standard order
    global $resource_standard_order, $CFG;

    $resources = array();

    /// Standard resource types
    $standardresources = array('text','html','file','directory');
    foreach ($standardresources as $resourcetype) {
        $resources[$resourcetype] = get_string("resourcetype$resourcetype", 'resource');
    }

    /// Drop-in extra resource types
    $resourcetypes = get_list_of_plugins('mod/resource/type');
    foreach ($resourcetypes as $resourcetype) {
        if (!empty($CFG->{'resource_hide_'.$resourcetype})) {  // Not wanted
            continue;
        }
        if (!in_array($resourcetype, $resources)) {
            $resources[$resourcetype] = get_string("resourcetype$resourcetype", 'resource');
        }
    }
    return $resources;
}
?>
