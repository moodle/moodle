<?PHP  // $Id$

/////////////////////////////////////////////////////////////
//
// MOD.PHP - contains functions to add, update and delete
//           an instance of this module
//           
//           Generally called from /course/mod.php
//
/////////////////////////////////////////////////////////////

function add_instance($forum) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $forum->timemodified = time();

    return insert_record("forum", $forum);
}


function update_instance($forum) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $forum->timemodified = time();
    $forum->id = $forum->instance;

    return update_record("forum", $forum);
}


function delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    global $CFG;

    include("$CFG->dirroot/mod/discuss/lib.php");
    
    if (! $forum = get_record("forum", "id", "$id")) {
        return false;
    }

    $result = true;

    if ($discussions = get_records("discuss", "forum", $forum->id)) {
        foreach ($discussions as $discuss) {
            if (! delete_discussion($discuss)) {
                $result = false;
            }
        }
    }

    if (! delete_records("forum_subscriptions", "forum", "$forum->id")) {
        $result = false;
    }

    if (! delete_records("forum", "id", "$forum->id")) {
        $result = false;
    }

    return $result;
}


?>
