<?PHP  // $Id$

/////////////////////////////////////////////////////////////
//
// MOD.PHP - contains functions to add, update and delete
//           an instance of this module
//           
//           Generally called from /course/mod.php
//
/////////////////////////////////////////////////////////////

function add_instance($journal) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $journal->timemodified = time();

    return insert_record("journal", $journal);
}


function update_instance($journal) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $journal->timemodified = time();
    $journal->id = $journal->instance;

    return update_record("journal", $journal);
}


function delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $journal = get_record("journal", "id", $id)) {
        return false;
    }

    $result = true;

    if (! delete_records("journal_entries", "journal", $journal->id)) {
        $result = false;
    }

    if (! delete_records("journal", "id", $journal->id)) {
        $result = false;
    }

    return $result;

}


?>
