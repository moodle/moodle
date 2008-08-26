<?php // $Id$

if ($hassiteconfig) { // speedup for non-admins, add all caps used on this page

    $optionalsubsystems->add(new admin_setting_configcheckbox('enablegroupings', get_string('enablegroupings', 'admin'), get_string('configenablegroupings', 'admin'), 0));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enableoutcomes', get_string('enableoutcomes', 'grades'), get_string('configenableoutcomes', 'grades'), 0));

    $optionalsubsystems->add(new admin_setting_configcheckbox('usetags', get_string('usetags','admin'),get_string('configusetags', 'admin'), '1'));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enablenotes', get_string('enablenotes', 'notes'), get_string('configenablenotes', 'notes'), 1));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enableportfolios', get_string('enabled', 'portfolio'), get_string('enableddesc', 'portfolio'), 0));

    $optionalsubsystems->add(new admin_setting_configcheckbox('messaging', get_string('messaging', 'admin'), get_string('configmessaging','admin'), 1));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enablestats', get_string('enablestats', 'admin'), get_string('configenablestats', 'admin'), 0));

    $optionalsubsystems->add(new admin_setting_configcheckbox('enablerssfeeds', get_string('enablerssfeeds', 'admin'), get_string('configenablerssfeeds', 'admin'), 0));

    $optionalsubsystems->add(new admin_setting_configselect('bloglevel', get_string('bloglevel', 'admin'),
                                get_string('configbloglevel', 'admin'), 4, array(5 => get_string('worldblogs','blog'),
                                                                                 4 => get_string('siteblogs','blog'),
                                                                                 3 => get_string('courseblogs','blog'),
                                                                                 2 => get_string('groupblogs','blog'),
                                                                                 1 => get_string('personalblogs','blog'),
                                                                                 0 => get_string('disableblogs','blog'))));

    $options = array('off'=>get_string('off', 'mnet'), 'strict'=>get_string('on', 'mnet'));
    $optionalsubsystems->add(new admin_setting_configselect('mnet_dispatcher_mode', get_string('net', 'mnet'), get_string('configmnet', 'mnet'), 'off', $options));

}
