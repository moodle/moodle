<?php 
//include simplehtml_form.php

require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
global $CFG, $DB, $USER, $OUTPUT;
require_once($CFG->dirroot.'/local/qbmanifest/forms.php');

$redirect = $CFG->wwwroot.'/local/qbmanifest/index.php';

$mform = new manifest_form();



if ($mform->is_cancelled()) {
    echo "You has clicked on cancel button.";
} else if ($fromform = $mform->get_data()) {
    
    $data = new stdClass;
    $data->added_by   = $USER->id;
       

    $file = $mform->get_new_filename('manifest_file');
    $fullpath = "upload/".$file;
    $success = $mform->save_file('manifest_file', $fullpath,true);
     
    if(!$success){
      redirect($redirect,  "Oops! something went wrong!", null, \core\output\notification::NOTIFY_ERROR); 
      exit;    
    }
    $data->file_name   = $file;
    $data->added_time = time(); 

    $filepath = $CFG->wwwroot.'/local/qbmanifest/upload/'.$file;

    $content = file_get_contents($filepath);

    $isvalide = qbjson_validate($content);

      if(is_array($isvalide) or is_object($isvalide))
      {
        $DB->insert_record('manifest_importlog', $data);
        $msg='Record have been added successfully.';
        for($c=0;$c<count($isvalide);$c++){

            $course = $isvalide[$c]->book;

            $datacourse = array();         

            $numofsections = (int) $course->level;

            $datacourse[0]['fullname'] = $course->name;
            $datacourse[0]['shortname'] = $course->code;
            $datacourse[0]['category'] = $course->category;
            $datacourse[0]['categoryid'] = $course->categorycode;
            $datacourse[0]['numsections'] = count($course->chapters);
            $datacourse[0]['summary'] = $course->summary;

            $datacourse[0]['level'] = '';
            $datacourse[0]['cardcolour'] = '';
             

            if(isset($course->otherfields))
            {
                $datacourse[0]['level'] = $course->otherfields->level;
                $datacourse[0]['cardcolour'] = $course->otherfields->cardcolour;
                
            }
            
            require_once($CFG->dirroot.'/local/qbmanifest/createcourse.php');
            $newcourse = new local_qbcourse();
            
            $cexists = $DB->get_record('course', array("shortname" => trim($course->code)));
            if(!empty($cexists)){
                $courseid = $cexists->id;
                $DB->set_field('course', 'fullname', $course->name, array('id' => $cexists->id));
                $DB->set_field('course', 'summary', $course->summary, array('id' => $cexists->id));
                $msg='Record has been updated successfully.';
                $type = 2;
            }
            else{
                $course_details = $newcourse->create_course($datacourse);
                $courseid = $course_details[0]['id'];
                $type = 1;
            }

            $newcourse->updateSections($courseid,$course->chapters,$course->otherfields,$type);

            
          // echo '<pre>'; print_r($course_details); exit;
        }
       
       rebuild_course_cache($courseid, true);       
    
       redirect($redirect, $msg, null, \core\output\notification::NOTIFY_SUCCESS);
      }
      else
      {
        unlink($filepath);
        redirect($redirect,  $isvalide, null, \core\output\notification::NOTIFY_ERROR); 
        
      }
        
           
      exit;
   
} else {
    admin_externalpage_setup('localqbcourseapi');
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('qbmformtitle', 'local_qbmanifest'));
  $mform->set_data($toform);
  
  $mform->display();
}
echo $OUTPUT->footer();



function qbjson_validate($string)
{

  $string = stripslashes($string);
    // decode the JSON data
    $result = json_decode($string);

    // switch and check possible JSON errors
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            $error = ''; // JSON is valid // No error has occurred
            break;
        case JSON_ERROR_DEPTH:
            $error = 'The maximum stack depth has been exceeded.';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            $error = 'Invalid or malformed JSON.';
            break;
        case JSON_ERROR_CTRL_CHAR:
            $error = 'Control character error, possibly incorrectly encoded.';
            break;
        case JSON_ERROR_SYNTAX:
            $error = 'Syntax error, malformed JSON.';
            break;
        // PHP >= 5.3.3
        case JSON_ERROR_UTF8:
            $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
            break;
        // PHP >= 5.5.0
        case JSON_ERROR_RECURSION:
            $error = 'One or more recursive references in the value to be encoded.';
            break;
        // PHP >= 5.5.0
        case JSON_ERROR_INF_OR_NAN:
            $error = 'One or more NAN or INF values in the value to be encoded.';
            break;
        case JSON_ERROR_UNSUPPORTED_TYPE:
            $error = 'A value of a type that cannot be encoded was given.';
            break;
        default:
            $error = 'Unknown JSON error occured.';
            break;
    }

    if ($error !== '') {
        // throw the Exception or exit // or whatever :)
        return $error;
    }

    // everything is OK
    return $result;
}