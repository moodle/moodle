<?php  //$Id$
require_once('../mod/scorm/locallib.php');
$yesno = array(0 => get_string('no'),
               1 => get_string('yes'));
               
$settings->add(new admin_setting_configselect('scorm/grademethod', get_string('grademethod', 'scorm'),get_string('grademethoddesc', 'scorm'), '', scorm_get_grade_method_array()));

for ($i=0; $i<=100; $i++) {
  $grades[$i] = "$i";
}
$settings->add(new admin_setting_configselect('scorm/maxgrade', get_string('maximumgrade'),get_string('maximumgradedesc','scorm'), '', $grades));


$settings->add(new admin_setting_configtext('scorm/maxattempts', get_string('maximumattempts', 'scorm'),
                   '', 6));


$settings->add(new admin_setting_configselect('scorm/displayattemptstatus', get_string('displayattemptstatus', 'scorm'),get_string('displayattemptstatusdesc', 'scorm'),'',$yesno));

$settings->add(new admin_setting_configselect('scorm/displaycoursestructure', get_string('displaycoursestructure', 'scorm'),get_string('displaycoursestructuredesc', 'scorm'),'',$yesno));

$settings->add(new admin_setting_configselect('scorm/forcecompleted', get_string('forcecompleted', 'scorm'),get_string('forcecompleteddesc', 'scorm'),'',$yesno));

$settings->add(new admin_setting_configselect('scorm/forcenewattempt', get_string('forcenewattempt', 'scorm'),get_string('forcenewattemptdesc', 'scorm'),'',$yesno));

$settings->add(new admin_setting_configselect('scorm/lastattemptlock', get_string('lastattemptlock', 'scorm'),get_string('lastattemptlockdesc', 'scorm'),'',$yesno));

$settings->add(new admin_setting_configselect('scorm/whatgrade', get_string('whatgrade', 'scorm'), get_string('whatgradedesc', 'scorm'), '', scorm_get_what_grade_array()));

$settings->add(new admin_setting_configtext('scorm/framewidth', get_string('width', 'scorm'),
                   get_string('framewidth', 'scorm'), 100));

$settings->add(new admin_setting_configtext('scorm/frameheight', get_string('height', 'scorm'),
                   get_string('frameheight', 'scorm'), 500));

                   
                   
$settings->add(new admin_setting_configselect('scorm/popup', get_string('display','scorm'), get_string('displaydesc','scorm'), '', scorm_get_popup_display_array()));

foreach(scorm_get_popup_options_array() as $key => $value){
    $settings->add(new admin_setting_configcheckbox('scorm/'.$key, get_string($key, 'scorm'),'',$value));
}

$settings->add(new admin_setting_configselect('scorm/skipview', get_string('skipview', 'scorm'), get_string('skipviewdesc', 'scorm'), '', scorm_get_skip_view_array()));
        
$settings->add(new admin_setting_configselect('scorm/hidebrowse', get_string('hidebrowse', 'scorm'), get_string('hidebrowsedesc', 'scorm'), '', $yesno));

$settings->add(new admin_setting_configselect('scorm/hidetoc', get_string('hidetoc', 'scorm'), get_string('hidetocdesc', 'scorm'), '', scorm_get_hidetoc_array()));
    
$settings->add(new admin_setting_configselect('scorm/hidenav', get_string('hidenav', 'scorm'), get_string('hidenavdesc', 'scorm'), '', $yesno));

$settings->add(new admin_setting_configselect('scorm/auto', get_string('autocontinue', 'scorm'), get_string('autocontinuedesc', 'scorm'), '', $yesno));

$settings->add(new admin_setting_configselect('scorm/updatefreq', get_string('updatefreq', 'scorm'), get_string('updatefreqdesc', 'scorm'), '', scorm_get_updatefreq_array()));

$settings->add(new admin_setting_configtext('scorm/updatetime', get_string('updatetime', 'scorm'),
                   '', 2));

$settings->add(new admin_setting_configcheckbox('scorm/allowtypeexternal', get_string('allowtypeexternal', 'scorm'),
                   '', 0));

$settings->add(new admin_setting_configcheckbox('scorm/allowtypelocalsync', get_string('allowtypelocalsync', 'scorm'),
                   '', 0));

$settings->add(new admin_setting_configcheckbox('scorm/allowtypeimsrepository', get_string('allowtypeimsrepository', 'scorm'),
                   '', 0));

$settings->add(new admin_setting_configcheckbox('scorm/allowapidebug', get_string('allowapidebug', 'scorm'),
                   '', 0));

$settings->add(new admin_setting_configtext('scorm/apidebugmask', get_string('apidebugmask', 'scorm'),
                   '', '.*'));
