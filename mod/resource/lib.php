<?PHP  // $Id$

define("REFERENCE",   "1");
define("WEBPAGE",     "2");
define("UPLOADEDFILE","3");
define("PLAINTEXT",   "4");
define("WEBLINK",     "5");
define("HTML",        "6");
define("PROGRAM",     "7");
define("WIKITEXT",    "8");

$RESOURCE_TYPE = array (REFERENCE    => get_string("resourcetype1", "resource"),
                        WEBPAGE      => get_string("resourcetype2", "resource"),
                        UPLOADEDFILE => get_string("resourcetype3", "resource"),
                        PLAINTEXT    => get_string("resourcetype4", "resource"),
                        WEBLINK      => get_string("resourcetype5", "resource"),
                        HTML         => get_string("resourcetype6", "resource"),
                        PROGRAM      => get_string("resourcetype7", "resource"),
                        WIKITEXT     => get_string("resourcetype8", "resource") );

if (!isset($CFG->resource_framesize)) {
    set_config("resource_framesize", 130);
} 

$RESOURCE_WINDOW_OPTIONS = array("resizable", "scrollbars", "directories", "location", 
                                 "menubar", "toolbar", "status", "height", "width");

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

   if ($resource = get_record("resource", "id", $coursemodule->instance)) {
       if (($resource->type == UPLOADEDFILE or $resource->type == WEBLINK) and !empty($resource->alltext)) {
           return urlencode("target=\"resource$resource->id\" onClick=\"return ".
                            "openpopup('/mod/resource/view.php?inpopup=true&id=".
                            $coursemodule->id.
                            "','resource$resource->id','$resource->alltext');\"");
       }
   }

   return false;
}


?>
