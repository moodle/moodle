<?PHP  // $Id$

function assignment_add_instance($assignment) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $assignment->timemodified = time();

    return insert_record("assignment", $assignment);
}


function assignment_update_instance($assignment) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $assignment->timemodified = time();
    $assignment->id = $assignment->instance;

    return update_record("assignment", $assignment);
}


function assignment_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $assignment = get_record("assignment", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("assignment_submissions", "assignment", "$assignment->id")) {
        $result = false;
    }

    if (! delete_records("assignment", "id", "$assignment->id")) {
        $result = false;
    }

    return $result;
}


?>
