<?php // $Id$

/// Add settings for this module to the $settings object (it's already defined)
$settings->add(new admin_setting_configselect('grade_report_aggregationposition', get_string('aggregationposition', 'grades'),
                                          get_string('configaggregationposition', 'grades'), false,
                                          array( '0' => 'left',
                                                 '1' => 'right')));
$settings->add(new admin_setting_configselect('grade_report_aggregationview', get_string('aggregationview', 'grades'),
                                          get_string('configaggregationview', 'grades'), false,
                                          array( '0' => 'full',
                                                 '1' => 'compact')));
$settings->add(new admin_setting_configcheckbox('grade_report_bulkcheckboxes', get_string('bulkcheckboxes', 'grades'),
                                            get_string('configbulkcheckboxes', 'grades'), 0));
$settings->add(new admin_setting_configcheckbox('grade_report_enableajax', get_string('enableajax', 'grades'),
                                            get_string('configenableajax', 'grades'), 0));
$settings->add(new admin_setting_configselect('grade_report_gradedisplaytype', get_string('gradedisplaytype', 'grades'),
                                          get_string('configgradedisplaytype', 'grades'), false,
                                          array( '0' => 'raw',
                                                 '1' => 'percentage')));
$settings->add(new admin_setting_configcheckbox('grade_report_showeyecons', get_string('showeyecons', 'grades'),
                                            get_string('configshoweyecons', 'grades'), 0));
$settings->add(new admin_setting_configcheckbox('grade_report_showgroups', get_string('showgroups', 'grades'),
                                            get_string('configshowgroups', 'grades'), 0));
$settings->add(new admin_setting_configcheckbox('grade_report_showlocks', get_string('showlocks', 'grades'),
                                            get_string('configshowlocks', 'grades'), 0));
$settings->add(new admin_setting_configcheckbox('grade_report_shownotes', get_string('shownotes', 'grades'),
                                            get_string('configshownotes', 'grades'), 0));
$settings->add(new admin_setting_configcheckbox('grade_report_showscales', get_string('showscales', 'grades'),
                                            get_string('configshowscales', 'grades'), 0));
$settings->add(new admin_setting_configtext('grade_report_studentsperpage', get_string('studentsperpage', 'grades'),
                                        get_string('configstudentsperpage', 'grades'), 20));
$settings->add(new admin_setting_configselect('grade_report_feedbackformat', get_string('feedbackformat', 'grades'),
                                          get_string('configfeedbackformat', 'grades'), false,
                                          array( '0' => 'text',
                                                 '1' => 'html')));
$settings->add(new admin_setting_configselect('grade_report_decimalpoints', get_string('decimalpoints', 'grades'),
                                          get_string('configdecimalpoints', 'grades'), 2,
                                          array( '0' => '0',
                                                 '1' => '1',
                                                 '2' => '2',
                                                 '3' => '3',
                                                 '4' => '4',
                                                 '5' => '5')));

?>
