<?php  // $Id$

/// This page prints a particular instance of lams
/// (Replace lams with the name of your module)

require_once("../../config.php");
require_once("lib.php");
require_once("constants.php");

$id = optional_param('id', 0, PARAM_INT);    // Course Module ID, or

if (! $cm = get_coursemodule_from_id('lams', $id)) {
    error("Course Module ID was incorrect");
}

if (! $course = get_record("course", "id", $cm->course)) {
    error("Course is misconfigured");
}

if (! $lams = get_record("lams", "id", $cm->instance)) {
    error("Course module is incorrect");
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, "lams", "view", "view.php?id=$cm->id", "$lams->id");

/// Print the page header
$navigation = build_navigation('', $cm);
print_header_simple(format_string($lams->name), "", $navigation, "", "", true,
        update_module_button($cm->id, $course->id, get_string("lesson","lams")), navmenu($course, $cm));

echo '<table id="layout-table"><tr>';
echo '<td id="middle-column">';
print_heading(format_string($lams->name));

//$strlamss = get_string("modulenameplural", "lams");
//$strlams  = get_string("modulename", "lams");

//print_header("$course->shortname: $lams->name", "$course->fullname",
//             "$navigation <A HREF=index.php?id=$course->id>$strlamss</A> -> $lams->name",
//              "", "", true, update_module_button($cm->id, $course->id, $strlams),
//              navmenu($course, $cm));

/// Print the main part of the page
if(has_capability('mod/lams:manage', $context)){
    $datetime =    date("F d,Y g:i a");
    $plaintext = trim($datetime).trim($USER->username).trim($LAMSCONSTANTS->monitor_method).trim($CFG->lams_serverid).trim($CFG->lams_serverkey);
    $hash = sha1(strtolower($plaintext));
    $url = $CFG->lams_serverurl.$LAMSCONSTANTS->login_request.
        '?'.$LAMSCONSTANTS->param_uid.'='.$USER->username.
        '&'.$LAMSCONSTANTS->param_method.'='.$LAMSCONSTANTS->monitor_method.
        '&'.$LAMSCONSTANTS->param_timestamp.'='.urlencode($datetime).
        '&'.$LAMSCONSTANTS->param_serverid.'='.$CFG->lams_serverid.
        '&'.$LAMSCONSTANTS->param_hash.'='.$hash.
        '&'.$LAMSCONSTANTS->param_lsid.'='.$lams->learning_session_id.
        '&'.$LAMSCONSTANTS->param_courseid.'='.$lams->course;
    print_simple_box_start('center');
    echo '<a target="LAMS Monitor" title="LAMS Monitor" href="'.$url.'">'.get_string("openmonitor", "lams").'</a>';
    print_simple_box_end();

    $plaintext = trim($datetime).trim($USER->username).trim($LAMSCONSTANTS->learner_method).trim($CFG->lams_serverid).trim($CFG->lams_serverkey);
    $hash = sha1(strtolower($plaintext));
    $url = $CFG->lams_serverurl.$LAMSCONSTANTS->login_request.
        '?'.$LAMSCONSTANTS->param_uid.'='.$USER->username.
        '&'.$LAMSCONSTANTS->param_method.'='.$LAMSCONSTANTS->learner_method.
        '&'.$LAMSCONSTANTS->param_timestamp.'='.urlencode($datetime).
        '&'.$LAMSCONSTANTS->param_serverid.'='.$CFG->lams_serverid.
        '&'.$LAMSCONSTANTS->param_hash.'='.$hash.
        '&'.$LAMSCONSTANTS->param_lsid.'='.$lams->learning_session_id.
        '&'.$LAMSCONSTANTS->param_courseid.'='.$lams->course;
    print_simple_box_start('center');
    echo '<a target="LAMS Learner" title="LAMS Learner" href="'.$url.'">'.get_string("openlearner", "lams").'</a>';
    print_simple_box_end();
}else if(has_capability('mod/lams:participate', $context)){
    $datetime =    date("F d,Y g:i a");
    $plaintext = trim($datetime).trim($USER->username).trim($LAMSCONSTANTS->learner_method).trim($CFG->lams_serverid).trim($CFG->lams_serverkey);
    $hash = sha1(strtolower($plaintext));
    $url = $CFG->lams_serverurl.$LAMSCONSTANTS->login_request.
        '?'.$LAMSCONSTANTS->param_uid.'='.$USER->username.
        '&'.$LAMSCONSTANTS->param_method.'='.$LAMSCONSTANTS->learner_method.
        '&'.$LAMSCONSTANTS->param_timestamp.'='.urlencode($datetime).
        '&'.$LAMSCONSTANTS->param_serverid.'='.$CFG->lams_serverid.
        '&'.$LAMSCONSTANTS->param_hash.'='.$hash.
        '&'.$LAMSCONSTANTS->param_lsid.'='.$lams->learning_session_id.
        '&'.$LAMSCONSTANTS->param_courseid.'='.$lams->course;
    print_simple_box_start('center');
    echo '<a target="LAMS Learner" title="LAMS Learner" href="'.$url.'">'.get_string("openlearner", "lams").'</a>';
    print_simple_box_end();
}

if ($lams->introduction) {
    print_box(format_text($lams->introduction), 'generalbox', 'intro');
}


/// Finish the page
echo '</td></tr></table>';



/// Finish the page
print_footer($course);

?>
