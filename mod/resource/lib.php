<?PHP  // $Id$

define("REFERENCE",   "1");
define("WEBPAGE",     "2");
define("UPLOADEDFILE","3");
define("PLAINTEXT",   "4");
define("WEBLINK",     "5");
define("HTML",        "6");

$RESOURCE_TYPE = array (REFERENCE    => get_string("resourcetype1", "resource"),
                        WEBPAGE      => get_string("resourcetype2", "resource"),
                        UPLOADEDFILE => get_string("resourcetype3", "resource"),
                        PLAINTEXT    => get_string("resourcetype4", "resource"),
                        WEBLINK      => get_string("resourcetype5", "resource"),
                        HTML         => get_string("resourcetype6", "resource") );

function resource_list_all_resources($courseid=0, $sort="name ASC", $recent=0) {
    // Returns list of all resource links in an array of strings
 
    global $CFG, $USER;

    if ($courseid) {
        if (! $course = get_record("course", "id", $courseid)) {
            error("Could not find the specified course");
        }

        require_login($course->id);

    } else {
        if (! $course = get_record("course", "category", 0)) {
            error("Could not find a top-level course!");
        }
    }

    if ($resources = get_all_instances_in_course("resource", $course->id, $sort)) {
        foreach ($resources as $resource) {
            $link = "<A HREF=\"$CFG->wwwroot/mod/resource/view.php?id=$resource->coursemodule\">$resource->name</A>";
            if ($USER->editing) {
                $link .= "&nbsp; &nbsp; 
                    <A HREF=\"$CFG->wwwroot/course/mod.php?delete=$resource->coursemodule\"><IMG 
                       SRC=\"$CFG->wwwroot/pix/t/delete.gif\" BORDER=0 ALT=Delete></A>
                    <A HREF=\"$CFG->wwwroot/course/mod.php?update=$resource->coursemodule\"><IMG 
                       SRC=\"$CFG->wwwroot/pix/t/edit.gif\" BORDER=0 ALT=Update></A>";
            }
            $links[] = $link;
        }
    }

    return $links;
}
 

function resource_user_outline($course, $user, $mod, $resource) {
    if ($logs = get_records_sql("SELECT * FROM log
                                 WHERE user='$user->id' AND module='resource' 
                                       AND action='view' AND info='$resource->id'
                                       ORDER BY time ASC")) {

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

    if ($logs = get_records_sql("SELECT * FROM log
                                 WHERE user='$user->id' AND module='resource' 
                                       AND action='view' AND info='$resource->id'
                                       ORDER BY time ASC")) {

        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $strmostrecently = get_string("mostrecently");
        $strnumviews = get_string("numviews", "", $numviews);

        echo "$strnumviews - $strmostrecently ".userdate($lastlog->time);

    } else {
        print_string("neverseen", "resource");
    }
}

function resource_add_instance($resource) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $resource->timemodified = time();

    return insert_record("resource", $resource);
}


function resource_update_instance($resource) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $resource->id = $resource->instance;
    $resource->timemodified = time();

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


?>
