<?php
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
global $CFG, $DB, $USER, $OUTPUT;

$coursefile = $CFG->dirroot.'/books-json/digichamps/dcl01.json';
$course_fcontent = file_get_contents($coursefile);
$ref_csname = 'DCL01'; // Reference Course Short name
$cohort_idnumber = 'bfsajman';

$isvalidjson = qbjson_validate($course_fcontent);

if(is_array($isvalidjson) or is_object($isvalidjson)) {
    $isvalidjson[0]["book"]["code"] = $isvalidjson[0]["book"]["code"].$cohort_idnumber;
    $isvalidjson[0]["book"]["name"] = $isvalidjson[0]["book"]["name"].' ('.$cohort_idnumber.')';
    $chapters = $isvalidjson[0]["book"]["chapters"];
    $chapters = array_map('addcohort_uid_item', $chapters);
    $isvalidjson[0]["book"]["chapters"] = $chapters;
    $course = $isvalidjson[0]["book"];
    $datacourse = array();         

    $numofsections = (int) $course->level;

    $datacourse[0]['fullname'] = $course["name"];
    $datacourse[0]['shortname'] = $course["code"];
    $datacourse[0]['category'] = $course["category"];
    $datacourse[0]['categoryid'] = $course["categorycode"];
    $datacourse[0]['numsections'] = count($course["chapters"]);
    $datacourse[0]['summary'] = $course["summary"];

    $datacourse[0]['level'] = '';
    $datacourse[0]['cardcolour'] = '';
             

    if(isset($course["otherfields"])) {
        $datacourse[0]['level'] = $course["otherfields"]["level"];
        $datacourse[0]['cardcolour'] = $course["otherfields"]["cardcolour"];
    }

    require_once($CFG->dirroot.'/local/qbmanifest/clonecreatecourse.php');
    $newcourse = new local_qbcourse($ref_csname, $cohort_idnumber);

    $cexists = $DB->get_record('course', array("shortname" => trim($course["code"])));
    if(!empty($cexists)){
        $courseid = $cexists->id;
        $DB->set_field('course', 'fullname', $course["name"], array('id' => $cexists->id));
        $DB->set_field('course', 'summary', $course["summary"], array('id' => $cexists->id));
        $msg='Record has been updated successfully.';
        $type = 2;
    }
    else{
        $course_details = $newcourse->create_course($datacourse);
        $courseid = $course_details[0]['id'];
        $type = 1;
    }

    $newcourse->updateSections($courseid,$course["chapters"],$course["otherfields"],$type);
    rebuild_course_cache($courseid, true);
    echo "Course ID >> $courseid";
}

exit;


function qbjson_validate($string)
{

  $string = stripslashes($string);
    // decode the JSON data
    $result = json_decode($string, true);

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

function addcohort_uid_item($item){
    global $cohort_idnumber;
    $item["uid"] = $item["uid"].'-'.$cohort_idnumber;
    if(isset($item["children"])){
        $children = $item["children"];
        $children = array_map('addcohort_uid_item', $children);
        $item["children"] = $children;
    }
    return $item;
}

//echo "<pre>"; print_r($v); echo "</pre>";