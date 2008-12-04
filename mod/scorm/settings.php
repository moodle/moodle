<?php  //$Id$

require_once($CFG->dirroot . '/mod/scorm/locallib.php');

$settings->add(new admin_setting_configselect('scorm_grademethod', get_string('grademethod', 'scorm'),get_string('grademethoddesc', 'scorm'), GRADEHIGHEST, scorm_get_grade_method_array()));

for ($i=0; $i<=100; $i++) {
  $grades[$i] = "$i";
}
$settings->add(new admin_setting_configselect('scorm_maxgrade', get_string('maximumgrade'),get_string('maximumgradedesc','scorm'), 100, $grades));

$settings->add(new admin_setting_configselect('scorm_maxattempts', get_string('maximumattempts', 'scorm'), get_string('maximumattemptsdesc', 'scorm'), 0, scorm_get_attempts_array()));

$settings->add(new admin_setting_configselect('scorm_whatgrade', get_string('whatgrade', 'scorm'), get_string('whatgradedesc', 'scorm'), HIGHESTATTEMPT, scorm_get_what_grade_array()));

$settings->add(new admin_setting_configtext('scorm_framewidth', get_string('width', 'scorm'),
                   get_string('framewidth', 'scorm'), '100%'));

$settings->add(new admin_setting_configtext('scorm_frameheight', get_string('height', 'scorm'),
                   get_string('frameheight', 'scorm'), 500));

$settings->add(new admin_setting_configselect('scorm_popup', get_string('display','scorm'), get_string('displaydesc','scorm'), 0, scorm_get_popup_display_array()));

foreach(scorm_get_popup_options_array() as $key => $value){
    $settings->add(new admin_setting_configcheckbox('scorm_'.$key, get_string($key, 'scorm'),'',$value));
}

$settings->add(new admin_setting_configselect('scorm_skipview', get_string('skipview', 'scorm'), get_string('skipviewdesc', 'scorm'), 0, scorm_get_skip_view_array()));

$yesno = array(0 => get_string('no'),
               1 => get_string('yes'));

$settings->add(new admin_setting_configselect('scorm_hidebrowse', get_string('hidebrowse', 'scorm'), get_string('hidebrowsedesc', 'scorm'), 0, $yesno));

$settings->add(new admin_setting_configselect('scorm_hidetoc', get_string('hidetoc', 'scorm'), get_string('hidetocdesc', 'scorm'), 0, scorm_get_hidetoc_array()));

$settings->add(new admin_setting_configselect('scorm_hidenav', get_string('hidenav', 'scorm'), get_string('hidenavdesc', 'scorm'), 0, $yesno));

$settings->add(new admin_setting_configselect('scorm_auto', get_string('autocontinue', 'scorm'), get_string('autocontinuedesc', 'scorm'), 0, $yesno));

$settings->add(new admin_setting_configselect('scorm_updatefreq', get_string('updatefreq', 'scorm'), get_string('updatefreqdesc', 'scorm'), 0, scorm_get_updatefreq_array()));
?>