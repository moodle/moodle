<?php // $Id$

$CHOICE_MAX_NUMBER = 6;

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
    if ($current = get_record("choice_responses", "choice", $choice->id, "userid", $user->id)) {
        $result->info = "'".choice_get_answer($choice, $current->answer)."'";
        $result->time = $current->timemodified;
        return $result;
    }
    return NULL;
}


function choice_user_complete($course, $user, $mod, $choice) {
    if ($current = get_record("choice_responses", "choice", $choice->id, "userid", $user->id)) {
        $result->info = "'".choice_get_answer($choice, $current->answer)."'";
        $result->time = $current->timemodified;
        echo get_string("answered", "choice").": $result->info , last updated ".userdate($result->time);
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
    $result = insert_record("choice", $choice);
    if ($result) {
        for ($i = 1; $i < 7; $i++) {                      
            if (!empty($choice->{'newanswer'.$i})) {         
                $choiceanswers->answer = $choice->{'newanswer'.$i};
                $choiceanswers->choice = $result;
                $choiceanswers->timemodified = time();
                insert_record("choice_answers", $choiceanswers);                
            } else {
                break;
            }
        }
    }
    if ($choice->addmorechoices > 0) { //make sure the page is reloaded if the user wants to add more choices.
       redirect('mod.php?update='.$choice->coursemodule.'&return=true&addmore='.$choice->addmorechoices.'&sesskey='.sesskey());
    } else {        
       return $result; 
    }    
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
    
    $i = 0;
    foreach ($choice as $current=>$value) {        
    
        if (strstr($current, "choiceanswer")) {
           //this val is old, so update_record
           if (!empty($value)) {
                $choiceanswers->id = substr($current, 14); //get the ID of the answer that needs to be updated.
                $choiceanswers->answer = $value;
                $choiceanswers->choice = $choice->id;
                $choiceanswers->timemodified = time();
                update_record("choice_answers", $choiceanswers);                
            }                                                              
        } else if (strstr($current, "newanswer")) {
            //this val is new, so insert_record            
            if (!empty($value)) {
                $choiceanswers->id = "";
                $choiceanswers->answer = $value;
                $choiceanswers->choice = $choice->id;
                $choiceanswers->timemodified = time();
                insert_record("choice_answers", $choiceanswers);                
            }  
        }      
               
    }
   
    
    
    
//    global $db; $db->debug=true;

      $result = update_record("choice", $choice);
      
      
      if ($choice->addmorechoices > 0) { //make sure the page is reloaded if the user wants to add more choices.
         redirect('mod.php?update='.$choice->coursemodule.'&return=true&addmore='.$choice->addmorechoices.'&sesskey='.$choice->sesskey);
      } else {        
         return $result; 
      }
}


function choice_delete_instance($id) {
// Given an ID of an instance of this module, 
// this function will permanently delete the instance 
// and any data that depends on it.  

    if (! $choice = get_record("choice", "id", "$id")) {
        return false;
    }

    $result = true;

    if (! delete_records("choice_responses", "choice", "$choice->id")) {
        $result = false;
    }

    if (! delete_records("choice", "id", "$choice->id")) {
        $result = false;
    }
    
    if (! delete_records("choice_answers", "choice", "$choice->id")) {
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
                                      {$CFG->prefix}choice_responses c
                                 WHERE c.choice = '$choiceid' and
                                       u.id = c.userid");

    //Return students array (it contains an array of unique users)
    return ($students);
}


function choice_get_answer($choice, $code) {
// Returns text string which is the answer that matches the code
    if ($result = get_record("choice_answers", "id", $code)) {
       return $result->answer;
    } else {
        return get_string("notanswered", "choice");
    }            
}

function choice_get_choice($choiceid) {

// Gets a full choice record      
   if ($choice = get_record("choice", "id", $choiceid)) {
       if ($choices = get_records("choice_answers", "choice", $choiceid, "id")) {
           $inti = 1;
           foreach ($choices as $aa) {                         
              $choice->answer[$aa->id] = $aa->answer;     
              $choice->answerid[$aa->id] = $aa->id;            
              $inti = $inti +1;
           }        
           return $choice;
       } else {     
          return false;
       }
   }
}

?>
