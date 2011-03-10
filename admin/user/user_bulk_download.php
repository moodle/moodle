<?php
/**
* script for downloading of user lists
*/

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$format = optional_param('format', '', PARAM_ALPHA);

require_login();
admin_externalpage_setup('userbulk');
require_capability('moodle/user:update', get_context_instance(CONTEXT_SYSTEM));

$return = $CFG->wwwroot.'/'.$CFG->admin.'/user/user_bulk.php';

if (empty($SESSION->bulk_users)) {
    redirect($return);
}

if ($format) {
    $fields = array('id'        => 'id',
                    'username'  => 'username',
                    'email'     => 'email',
                    'firstname' => 'firstname',
                    'lastname'  => 'lastname',
                    'idnumber'  => 'idnumber',
                    'institution' => 'institution',
                    'department' => 'department',
                    'phone1'    => 'phone1',
                    'phone2'    => 'phone2',
                    'city'      => 'city',
                    'url'       => 'url',
                    'icq'       => 'icq',
                    'skype'     => 'skype',
                    'aim'       => 'aim',
                    'yahoo'     => 'yahoo',
                    'msn'       => 'msn',
                    'country'   => 'country');

    if ($extrafields = $DB->get_records('user_info_field')) {
        foreach ($extrafields as $n=>$v){
            $fields['profile_field_'.$v->shortname] = 'profile_field_'.$v->shortname;
        }
    }

    switch ($format) {
        case 'csv' : user_download_csv($fields);
        case 'ods' : user_download_ods($fields);
        case 'xls' : user_download_xls($fields);

    }
    die;
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('download', 'admin'));

echo $OUTPUT->box_start();
echo '<ul>';
echo '<li><a href="user_bulk_download.php?format=csv">'.get_string('downloadtext').'</a></li>';
echo '<li><a href="user_bulk_download.php?format=ods">'.get_string('downloadods').'</a></li>';
echo '<li><a href="user_bulk_download.php?format=xls">'.get_string('downloadexcel').'</a></li>';
echo '</ul>';
echo $OUTPUT->box_end();

echo $OUTPUT->continue_button($return);

echo $OUTPUT->footer();

function user_download_ods($fields) {
    global $CFG, $SESSION, $DB;

    require_once("$CFG->libdir/odslib.class.php");
    require_once($CFG->dirroot.'/user/profile/lib.php');

    $filename = clean_filename(get_string('users').'.ods');

    $workbook = new MoodleODSWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();

    $worksheet[0] =& $workbook->add_worksheet('');
    $col = 0;
    foreach ($fields as $fieldname) {
        $worksheet[0]->write(0, $col, $fieldname);
        $col++;
    }

    $row = 1;
    foreach ($SESSION->bulk_users as $userid) {
        if (!$user = $DB->get_record('user', array('id'=>$userid))) {
            continue;
        }
        $col = 0;
        profile_load_data($user);
        foreach ($fields as $field=>$unused) {
            $worksheet[0]->write($row, $col, $user->$field);
            $col++;
        }
        $row++;
    }

    $workbook->close();
    die;
}

function user_download_xls($fields) {
    global $CFG, $SESSION, $DB;

    require_once("$CFG->libdir/excellib.class.php");
    require_once($CFG->dirroot.'/user/profile/lib.php');

    $filename = clean_filename(get_string('users').'.xls');

    $workbook = new MoodleExcelWorkbook('-');
    $workbook->send($filename);

    $worksheet = array();

    $worksheet[0] =& $workbook->add_worksheet('');
    $col = 0;
    foreach ($fields as $fieldname) {
        $worksheet[0]->write(0, $col, $fieldname);
        $col++;
    }

    $row = 1;
    foreach ($SESSION->bulk_users as $userid) {
        if (!$user = $DB->get_record('user', array('id'=>$userid))) {
            continue;
        }
        $col = 0;
        profile_load_data($user);
        foreach ($fields as $field=>$unused) {
            $worksheet[0]->write($row, $col, $user->$field);
            $col++;
        }
        $row++;
    }

    $workbook->close();
    die;
}

function user_download_csv($fields) {
    global $CFG, $SESSION, $DB;

    require_once($CFG->dirroot.'/user/profile/lib.php');

    $filename = clean_filename(get_string('users').'.csv');

    header("Content-Type: application/download\n");
    header("Content-Disposition: attachment; filename=$filename");
    header("Expires: 0");
    header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
    header("Pragma: public");

    $delimiter = get_string('listsep', 'langconfig');
    $encdelim  = '&#'.ord($delimiter);

    $row = array();
    foreach ($fields as $fieldname) {
        $row[] = str_replace($delimiter, $encdelim, $fieldname);
    }
    echo implode($delimiter, $row)."\n";

    foreach ($SESSION->bulk_users as $userid) {
        $row = array();
        if (!$user = $DB->get_record('user', array('id'=>$userid))) {
            continue;
        }
        profile_load_data($user);
        foreach ($fields as $field=>$unused) {
            $row[] = str_replace($delimiter, $encdelim, $user->$field);
        }
        echo implode($delimiter, $row)."\n";
    }
    die;
}
