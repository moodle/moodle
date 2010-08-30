<?php  //$Id$

require_once($CFG->dirroot.'/mod/resource/lib.php');

global $RESOURCE_WINDOW_OPTIONS; // make sure we have the pesky global

$checkedyesno = array(''=>get_string('no'), 'checked'=>get_string('yes')); // not nice at all

$settings->add(new admin_setting_configtext('resource_framesize', get_string('framesize', 'resource'),
                   get_string('configframesize', 'resource'), 130, PARAM_INT));

$settings->add(new admin_setting_configtext('resource_websearch', get_string('websearchdefault', 'resource'),
                   get_string('configwebsearch', 'resource'), 'http://google.com/'));

$settings->add(new admin_setting_configtext('resource_defaulturl', get_string('resourcedefaulturl', 'resource'),
                   get_string('configdefaulturl', 'resource'), 'http://'));

$settings->add(new admin_setting_configpasswordunmask('resource_secretphrase', get_string('password'),
                   get_string('configsecretphrase', 'resource'), random_string(20)));

$woptions = array('' => get_string('pagewindow', 'resource'), 'checked' => get_string('newwindow', 'resource'));
$settings->add(new admin_setting_configselect('resource_popup', get_string('display', 'resource'),
                   get_string('configpopup', 'resource'), '', $woptions));

foreach ($RESOURCE_WINDOW_OPTIONS as $optionname) {
    $popupoption = "resource_popup$optionname";
    if ($popupoption == 'resource_popupheight') {
        $settings->add(new admin_setting_configtext('resource_popupheight', get_string('newheight', 'resource'),
                           get_string('configpopupheight', 'resource'), 450, PARAM_INT));
    } else if ($popupoption == 'resource_popupwidth') {
        $settings->add(new admin_setting_configtext('resource_popupwidth', get_string('newwidth', 'resource'),
                           get_string('configpopupwidth', 'resource'), 620, PARAM_INT));
    } else {
        $settings->add(new admin_setting_configselect($popupoption, get_string('new'.$optionname, 'resource'),
                           get_string('configpopup'.$optionname, 'resource'), 'checked', $checkedyesno));
    }
}

$settings->add(new admin_setting_configcheckbox('resource_autofilerename', get_string('autofilerename', 'resource'),
                   get_string('configautofilerenamesettings', 'resource'), 1));

$settings->add(new admin_setting_configcheckbox('resource_blockdeletingfile', get_string('blockdeletingfile', 'resource'),
                   get_string('configblockdeletingfilesettings', 'resource'), 1));

?>
