<?php  //$Id$

$settings->add(new admin_setting_configtext('scorm_framewidth', get_string('width', 'scorm'),
                   get_string('framewidth', 'scorm'), 100));

$settings->add(new admin_setting_configtext('scorm_frameheight', get_string('height', 'scorm'),
                   get_string('frameheight', 'scorm'), 500));

$settings->add(new admin_setting_configtext('scorm_maxattempts', get_string('maximumattempts', 'scorm'),
                   '', 6));

$settings->add(new admin_setting_configtext('scorm_updatetime', get_string('updatetime', 'scorm'),
                   '', 2));

$settings->add(new admin_setting_configcheckbox('scorm_allowtypeexternal', get_string('allowtypeexternal', 'scorm'),
                   '', 0));

$settings->add(new admin_setting_configcheckbox('scorm_allowtypelocalsync', get_string('allowtypelocalsync', 'scorm'),
                   '', 0));

$settings->add(new admin_setting_configcheckbox('scorm_allowtypeimsrepository', get_string('allowtypeimsrepository', 'scorm'),
                   '', 0));



