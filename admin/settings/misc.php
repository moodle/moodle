<?php // $Id$

// * Miscellaneous settings (still to be sorted)
$temp = new admin_settingpage('legacy', get_string('legacy','admin'));
$temp->add(new admin_setting_configcheckbox('teacherassignteachers', get_string('teacherassignteachers', 'admin'), get_string('configteacherassignteachers', 'admin'), 1));
$temp->add(new admin_setting_sitesettext('teacher', get_string('wordforteacher'), get_string('wordforteachereg'), ''));
$temp->add(new admin_setting_sitesettext('teachers', get_string('wordforteachers'), get_string('wordforteacherseg'), ''));
$temp->add(new admin_setting_sitesettext('student', get_string('wordforstudent'), get_string('wordforstudenteg'), ''));
$temp->add(new admin_setting_sitesettext('students', get_string('wordforstudents'), get_string('wordforstudentseg'), ''));
$ADMIN->add('misc', $temp);

$temp = new admin_settingpage('unsorted', get_string('unsorted', 'admin'));
$temp->add(new admin_setting_configtext('docroot', get_string('docroot', 'admin'), get_string('configdocroot', 'admin'), 'http://docs.moodle.org', PARAM_URL));
$temp->add(new admin_setting_configcheckbox('doctonewwindow', get_string('doctonewwindow', 'admin'), get_string('configdoctonewwindow', 'admin'), 0));
$temp->add(new admin_setting_configselect('bloglevel', get_string('bloglevel', 'admin'), get_string('configbloglevel', 'admin'), 4, array(5 => get_string('worldblogs','blog'),
                                                                                                                                          4 => get_string('siteblogs','blog'),
                                                                                                                                          3 => get_string('courseblogs','blog'),
                                                                                                                                          2 => get_string('groupblogs','blog'),
                                                                                                                                          1 => get_string('personalblogs','blog'),
                                                                                                                                          0 => get_string('disableblogs','blog'))));
$temp->add(new admin_setting_configselect('loglifetime', get_string('loglifetime', 'admin'), get_string('configloglifetime', 'admin'), 0, array(0 => get_string('neverdeletelogs'),
                                                                                                                                                1000 => get_string('numdays', '', 1000),
                                                                                                                                                365 => get_string('numdays', '', 365),
                                                                                                                                                180 => get_string('numdays', '', 180),
                                                                                                                                                150 => get_string('numdays', '', 150),
                                                                                                                                                120 => get_string('numdays', '', 120),
                                                                                                                                                90 => get_string('numdays', '', 90),
                                                                                                                                                60 => get_string('numdays', '', 60),
                                                                                                                                                30 => get_string('numdays', '', 30))));
$ADMIN->add('misc', $temp);

$ADMIN->add('misc', new admin_externalpage('sitefiles', get_string('sitefiles'), $CFG->wwwroot . '/files/index.php?id=' . SITEID));


?>