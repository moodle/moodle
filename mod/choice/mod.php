<?PHP  // $Id$

/////////////////////////////////////////////////////////////
//
// MOD.PHP - contains functions to add, update and delete
//           an instance of this module
//           
//           Generally called from /course/mod.php
//
/////////////////////////////////////////////////////////////

function add_instance($form) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.
//
    GLOBAL $db;

    $timenow = time();

    if (!$rs = $db->Execute("INSERT into choice
                                SET course   = '$form->course', 
                                    name     = '$form->name',
                                    text     = '$form->text',
                                    answer1  = '$form->answer1',
                                    answer2  = '$form->answer2',
                                    timemodified = '$timenow'")) {
        return 0;
    }
    
    // Get it out again - this is the most compatible way to determine the ID
    if ($rs = $db->Execute("SELECT id FROM choice
                            WHERE course = $form->course AND timemodified = '$timenow'")) {
        return $rs->fields[0];
    } else {
        return 0;
    }
}


function update_instance($form) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.
//
    GLOBAL $db;

    $timenow = time();

    if (!$rs = $db->Execute("UPDATE choice
                                SET course   = '$form->course', 
                                    name     = '$form->name',
                                    text     = '$form->text',
                                    answer1  = '$form->answer1',
                                    answer2  = '$form->answer2',
                                    timemodified = '$timenow'
                              WHERE id = '$form->instance' ")) {
        return false;
    }
    return true;
}


function delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  
//
    GLOBAL $db;

    if (!$rs = $db->Execute("DELETE from choice WHERE id = '$id' ")) {
        return false;
    }

    return true;
    
}


?>
