<?php // $Id$

$COLUMN_HEIGHT = 300;

define('CHOICE_PUBLISH_ANONYMOUS', '0');
define('CHOICE_PUBLISH_NAMES',     '1');

define('CHOICE_RELEASE_NOT',          '0');
define('CHOICE_RELEASE_AFTER_ANSWER', '1');
define('CHOICE_RELEASE_AFTER_CLOSE',  '2');
define('CHOICE_RELEASE_ALWAYS',       '3');

define('CHOICE_DISPLAY_HORIZONTAL',  '0');
define('CHOICE_DISPLAY_VERTICAL',    '1');

$CHOICE_PUBLISH = array (CHOICE_PUBLISH_ANONYMOUS  => get_string('publishanonymous', 'choice'),
                         CHOICE_PUBLISH_NAMES      => get_string('publishnames', 'choice'));

$CHOICE_RELEASE = array (CHOICE_RELEASE_NOT          => get_string('publishnot', 'choice'),
                         CHOICE_RELEASE_AFTER_ANSWER => get_string('publishafteranswer', 'choice'),
                         CHOICE_RELEASE_AFTER_CLOSE  => get_string('publishafterclose', 'choice'),
                         CHOICE_RELEASE_ALWAYS       => get_string('publishalways', 'choice'));

$CHOICE_DISPLAY = array (CHOICE_DISPLAY_HORIZONTAL   => get_string('displayhorizontal', 'choice'),
                         CHOICE_DISPLAY_VERTICAL     => get_string('displayvertical','choice'));

/// Standard functions /////////////////////////////////////////////////////////

function choice_user_outline($course, $user, $mod, $choice) {
    if ($answer = get_record('choice_answers', 'choiceid', $choice->id, 'userid', $user->id)) {
        $result->info = "'".format_string(choice_get_option_text($choice, $answer->optionid))."'";
        $result->time = $answer->timemodified;
        return $result;
    }
    return NULL;
}


function choice_user_complete($course, $user, $mod, $choice) {
    if ($answer = get_record('choice_answers', "choiceid", $choice->id, "userid", $user->id)) {
        $result->info = "'".format_string(choice_get_option_text($choice, $answer->optionid))."'";
        $result->time = $answer->timemodified;
        echo get_string("answered", "choice").": $result->info. ".get_string("updated", '', userdate($result->time));
    } else {
        print_string("notanswered", "choice");
    }
}


function choice_add_instance($choice) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will create a new instance and return the id number 
// of the new instance.

    $choice->timemodified = time();

    if (!empty($choice->timerestrict) and $choice->timerestrict) {
        $choice->timeopen = make_timestamp($choice->openyear, $choice->openmonth, $choice->openday,
                                     $choice->openhour, $choice->openminute, 0);
        $choice->timeclose = make_timestamp($choice->closeyear, $choice->closemonth, $choice->closeday,
                                      $choice->closehour, $choice->closeminute, 0);
    } else {
        $choice->timeopen = 0;
        $choice->timeclose = 0;
    }

    //insert answers    
    if ($choice->id = insert_record("choice", $choice)) {
        foreach ($choice as $name => $value) {        
            if (strstr($name, "newoption")) {   /// New option
                $value = trim($value);
                if (isset($value) && $value <> '') {
                    $option = NULL;
                    $option->text = $value;
                    $option->choiceid = $choice->id;
                    $option->maxanswers = $choice->{'newlimit'.substr($name, 9)};
                    $option->timemodified = time();
                    insert_record("choice_options", $option);                
                }
            }
        }
    }
    return $choice->id;
}


function choice_update_instance($choice) {
// Given an object containing all the necessary data, 
// (defined by the form in mod.html) this function 
// will update an existing instance with new data.

    $choice->id = $choice->instance;
    $choice->timemodified = time();


    if (!empty($choice->timerestrict) and $choice->timerestrict) {
        $choice->timeopen = make_timestamp($choice->openyear, $choice->openmonth, $choice->openday,
                                     $choice->openhour, $choice->openminute, 0);
        $choice->timeclose = make_timestamp($choice->closeyear, $choice->closemonth, $choice->closeday,
                                      $choice->closehour, $choice->closeminute, 0);
    } else {
        $choice->timeopen = 0;
        $choice->timeclose = 0;
    }
    
    //update answers
    
    foreach ($choice as $name => $value) {        
        $value = trim($value);

        if (strstr($name, "oldoption")) {  // Old option
            if (isset($value) && $value <> '') {
                $option = NULL;
                $option->id = substr($name, 9); // Get the ID of the answer that needs to be updated.
                $option->text = $value;
                $option->choiceid = $choice->id;
                $option->maxanswers = $choice->{'oldlimit'.substr($name, 9)};
                $option->timemodified = time();
                update_record("choice_options", $option);
            } else { //empty old option - needs to be deleted.
                delete_records("choice_options", "id", substr($name, 9));
            }
        } else if (strstr($name, "newoption")) {   /// New option
            if (isset($value)&& $value <> '') {
                $option = NULL;
                $option->text = $value;
                $option->choiceid = $choice->id;
                $option->maxanswers = $choice->{'newlimit'.substr($name, 9)};
                $option->timemodified = time();
                insert_record("choice_options", $option);                
            }
        }      
    }

    return update_record('choice', $choice);
      
}


function choice_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $choice = get_record("choice", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("choice_answers", "choiceid", "$choice->id")) {
        $result = false;
    }

    if (! delete_records("choice_options", "choiceid", "$choice->id")) {
        $result = false;
    }

    if (! delete_records("choice", "id", "$choice->id")) {
        $result = false;
    }
    

    return $result;
}

function choice_get_participants($choiceid) {
//Returns the users with data in one choice
//(users with records in choice_responses, students)

    global $CFG;

    //Get students
    $students = get_records_sql("SELECT DISTINCT u.id, u.id
                                 FROM {$CFG->prefix}user u,
                                      {$CFG->prefix}choice_answers a
                                 WHERE a.choiceid = '$choiceid' and
                                       u.id = a.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}


function choice_get_option_text($choice, $id) {
// Returns text string which is the answer that matches the id
    if ($result = get_record("choice_options", "id", $id)) {
        return $result->text;
    } else {
        return get_string("notanswered", "choice");
    }            
}

function choice_get_choice($choiceid) {
// Gets a full choice record      

    if ($choice = get_record("choice", "id", $choiceid)) {
        if ($options = get_records("choice_options", "choiceid", $choiceid, "id")) {
            foreach ($options as $option) {                         
                $choice->option[$option->id] = $option->text; 
                $choice->maxanswers[$option->id] = $option->maxanswers;
            }        
            return $choice;
        }
    }
    return false;
}

function choice_get_view_actions() {
    return array('view','view all','report');
}

function choice_get_post_actions() {
    return array('choose','choose again');
}

?>
