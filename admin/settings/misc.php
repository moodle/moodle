<?php // $Id$

// * Miscellaneous settings (still to be sorted)
$temp = new admin_settingpage('misc', get_string('misc', 'admin'));
$temp->add(new admin_setting_configselect('longtimenosee', get_string('longtimenosee', 'admin'), get_string('configlongtimenosee', 'admin'), 120, array(0 => get_string('never'),
                                                                                                                                                        1000 => get_string('numdays', '', 1000),
																																				        365 => get_string('numdays', '', 365),
																																				        180 => get_string('numdays', '', 180),
																																				        150 => get_string('numdays', '', 150),
																																				        120 => get_string('numdays', '', 120),
																																				        90 => get_string('numdays', '', 90),
																																				        60 => get_string('numdays', '', 60),
																																				        30 => get_string('numdays', '', 30),
																																				        21 => get_string('numdays', '', 21),
																																				        14 => get_string('numdays', '', 14),
																																				        7 => get_string('numdays', '', 7) )));
$temp->add(new admin_setting_configselect('deleteunconfirmed', get_string('deleteunconfirmed', 'admin'), get_string('configdeleteunconfirmed', 'admin'), 168, array(0 => get_string('never'),
                                                                                                                                                                    168 => get_string('numdays', '', 7),
                                                                                                                                                                    144 => get_string('numdays', '', 6),
                                                                                                                                                                    120 => get_string('numdays', '', 5),
                                                                                                                                                                    96 => get_string('numdays', '', 4),
                                                                                                                                                                    72 => get_string('numdays', '', 3),
                                                                                                                                                                    48 => get_string('numdays', '', 2),
                                                                                                                                                                    24 => get_string('numdays', '', 1),
                                                                                                                                                                    12 => get_string('numhours', '', 12),
                                                                                                                                                                    6 => get_string('numhours', '', 6),
                                                                                                                                                                    1 => get_string('numhours', '', 1))));
$temp->add(new admin_setting_configselect('fullnamedisplay', get_string('fullnamedisplay', 'admin'), get_string('configfullnamedisplay', 'admin'), 'firstname lastname', array('language' => get_string('language'),
                                                                                                                                                                               'firstname lastname' => get_string('firstname') . ' + ' . get_string('lastname'),
																																						                       'lastname firstname' => get_string('lastname') . ' + ' . get_string('firstname'),
																																						                       'firstname' => get_string('firstname'))));
$temp->add(new admin_setting_configcheckbox('extendedusernamechars', get_string('extendedusernamechars', 'admin'), get_string('configextendedusernamechars', 'admin'), 0));
$temp->add(new admin_setting_configcheckbox('mymoodleredirect', get_string('mymoodleredirect', 'admin'), get_string('configmymoodleredirect', 'admin'), 0));
$temp->add(new admin_setting_configtext('sitepolicy', get_string('sitepolicy', 'admin'), get_string('configsitepolicy', 'admin'), '', PARAM_URL));
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
$temp->add(new admin_setting_configselect('startwday', get_string('startwday', 'admin'), get_string('helpstartofweek', 'admin'), 1, array(0 => get_string('sunday', 'calendar'), 
                                                                                                                                          1 => get_string('monday', 'calendar'), 
                                                                                                                                          2 => get_string('tuesday', 'calendar'), 
                                                                                                                                          3 => get_string('wednesday', 'calendar'), 
                                                                                                                                          4 => get_string('thursday', 'calendar'), 
                                                                                                                                          5 => get_string('friday', 'calendar'), 
                                                                                                                                          6 => get_string('saturday', 'calendar'))));
$temp->add(new admin_setting_configtext('calendar_lookahead', get_string('calendar_lookahead', 'admin'), get_string('helpupcominglookahead', 'admin'), 10, PARAM_INT));
$temp->add(new admin_setting_configtext('calendar_maxevents', get_string('calendar_maxevents', 'admin'), get_string('helpupcomingmaxevents', 'admin'), 5, PARAM_INT));
$temp->add(new admin_setting_special_calendar_weekend());

$ADMIN->add('unsorted', $temp);

?>