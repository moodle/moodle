<?PHP  // $Id$

/////////////////////////////////////////////////////////////
//
// MOD.PHP - contains functions to add, update and delete
//           an instance of this module
//           
//           Generally called from /course/mod.php
//
/////////////////////////////////////////////////////////////

function add_instance($survey) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    if (!$template = get_record("survey", "id", $survey->template)) {
        return 0;
    }

    $survey->questions    = $template->questions; 
    $survey->timecreated  = time();
    $survey->timemodified = $survey->timecreated;

    return insert_record("survey", $survey);

}


function update_instance($survey) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    if (!$template = get_record("survey", "id", $survey->template)) {
        return 0;
    }

    $survey->id           = $survey->instance; 
    $survey->questions    = $template->questions; 
    $survey->timemodified = time();

    return update_record("survey", $survey);
}

function delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $survey = get_record("survey", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("survey_analysis", "survey", "$survey->id")) {
        $result = false;
    }

    if (! delete_records("survey_answers", "survey", "$survey->id")) {
        $result = false;
    }

    if (! delete_records("survey", "id", "$survey->id")) {
        $result = false;
    }

    return $result;
}


?>
