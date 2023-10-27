<?php
require_once('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
global $CFG, $DB, $USER, $OUTPUT;

//$ref_csname = 'DCL02'; // Reference Course Short name
//$cohort_idnumber = 'bfsajman';
// Example http://qubits.localhost.com/local/qbmanifest/clonecourses.php?cshortname=DCL03&cohortid=bfsajman
// Deprecated this param &mfile=/books-json/digichamps/dcl03.json

require_login();

if(!is_siteadmin()){
    throw new \moodle_exception('accessdenied');
}

$farr = array(
    "DCL01" => "/books-json/digichamps/dcl01.json",
    "DCL02" => "/books-json/digichamps/dcl02.json",
    "DCL03" => "/books-json/digichamps/dcl03.json",
    "DCL04" => "/books-json/digichamps/dcl04.json",
    "DCL05" => "/books-json/digichamps/dcl05.json",
    "DCL06" => "/books-json/digichamps/dcl06.json",
    "DCL07" => "/books-json/digichamps/dcl07.json",
    "DCL08" => "/books-json/digichamps/dcl08.json",
    "DCL09" => "/books-json/digichamps/dcl09.json",
    "DCL10" => "/books-json/digichamps/dcl10.json",
    "DCL11" => "/books-json/digichamps/dcl11.json",
    "DCL12" => "/books-json/digichamps/dcl12.json",
    "DPL01" => "/books-json/digipro/dpl01.json",
    "DPL02" => "/books-json/digipro/dpl02.json",
    "DPL03" => "/books-json/digipro/dpl03.json",
    "DPL04" => "/books-json/digipro/dpl04.json",
    "DPL05" => "/books-json/digipro/dpl05.json",
    "DPL06" => "/books-json/digipro/dpl06.json",
    "DPL07" => "/books-json/digipro/dpl07.json",
    "DPL08" => "/books-json/digipro/dpl08.json",
    "DPL09" => "/books-json/digipro/dpl09.json",
    "DPL10" => "/books-json/digipro/dpl10.json",
    "DPL11" => "/books-json/digipro/dpl11.json",
    "DPL12" => "/books-json/digipro/dpl12.json",
    "DJL01" => "/books-json/djl01.json"
);


$ref_csname = required_param('cshortname', PARAM_ALPHANUMEXT);
$cohort_idnumber = required_param('cohortid', PARAM_ALPHANUMEXT);
//$cfilename = required_param('mfile', PARAM_RAW);
$cfilename = $farr[$ref_csname];

$rcohort = $DB->get_record("cohort", [
    "idnumber" => $cohort_idnumber
], '*', MUST_EXIST);

$coursecategory = $DB->get_record("course_categories", [
    "idnumber" => $cohort_idnumber
], '*');

if(empty($coursecategory)){
    $coursecategory = new stdClass;
    $coursecategory->name = $rcohort->description;
    $coursecategory->idnumber = $rcohort->idnumber;
    $coursecategory->parent = 0;
    $rcat = core_course_category::create($coursecategory, '');
}


$coursefile = $CFG->dirroot.$cfilename; // '/books-json/digichamps/dcl02.json'
$course_fcontent = file_get_contents($coursefile);


$isvalidjson = qbjson_validate($course_fcontent);

if(is_array($isvalidjson) or is_object($isvalidjson)) {
    $isvalidjson1 = json_decode($course_fcontent);
    $course = $isvalidjson1[0]->book;
    $course->code = $course->code.$cohort_idnumber;
    $course->name = $course->name.' ('.$cohort_idnumber.')';
    $chapters = $course->chapters;
    $course->chapters = array_map('addcohort_uid_item_obj', $chapters);
    
    $datacourse = array();         

            $numofsections = (int) $course->level;

            $datacourse[0]['fullname'] = $course->name;
            $datacourse[0]['shortname'] = $course->code;
            $datacourse[0]['category'] = $coursecategory->name;
            $datacourse[0]['categoryid'] = $coursecategory->idnumber;

            $datacourse[0]['numsections'] = count($course->chapters);
            $datacourse[0]['summary'] = $course->summary;

            $datacourse[0]['level'] = '';
            $datacourse[0]['cardcolour'] = '';
             

            if(isset($course->otherfields))
            {
                $datacourse[0]['level'] = $course->otherfields->level;
                $datacourse[0]['cardcolour'] = $course->otherfields->cardcolour;
                
            }
            
            require_once($CFG->dirroot.'/local/qbmanifest/clonecreatecourse.php');
            $newcourse = new local_clone_qbcourse($ref_csname, $cohort_idnumber);
            
            $cexists = $DB->get_record('course', array("shortname" => trim($course->code)));
            if(!empty($cexists)){
                $courseid = $cexists->id;
                $DB->set_field('course', 'fullname', $course->name, array('id' => $cexists->id));
                $DB->set_field('course', 'summary', $course->summary, array('id' => $cexists->id));
                $msg='Record has been updated successfully.';
                $type = 2;
                if($cohort_idnumber=="dnsbarsha")
                  $courseinstance = $DB->insert_record('enrol', array('courseid'=>$courseid, 'enrol'=>'oneroster', 'status'=> ENROL_INSTANCE_ENABLED));
            }
            else{
                $course_details = $newcourse->create_course($datacourse);
                $courseid = $course_details[0]['id'];
                $type = 1;
                if($cohort_idnumber=="dnsbarsha"){
                    $courseinstance = $DB->get_record('enrol', array('courseid'=>$courseid, 'enrol'=>'oneroster'));
                    if(empty($courseinstance)){
                        $courseinstance = $DB->insert_record('enrol', array('courseid'=>$courseid, 'enrol'=>'oneroster', 'status'=> ENROL_INSTANCE_ENABLED));
                    }
                }
            }

            $newcourse->updateSections($courseid,$course->chapters,$course->otherfields,$type);
            rebuild_course_cache($courseid, true);

    echo "<pre>"; 
    //print_r($isvalidjson);
    print_r($course);
    echo "</pre>"; exit;

    
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

function addcohort_uid_item_obj($item){
    global $cohort_idnumber;
    $item->uid = $item->uid.'-'.$cohort_idnumber;
    if(isset($item->children)){
        $children = $item->children;
        $children = array_map('addcohort_uid_item_obj', $children);
        $item->children = $children;
    }
    return $item;
}

//echo "<pre>"; print_r($v); echo "</pre>";