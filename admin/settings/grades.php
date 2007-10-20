<?php // $Id$

// This file defines settingpages and externalpages under the "grades" section

// General settings

require_once $CFG->libdir.'/grade/constants.php';

$temp = new admin_settingpage('gradessettings', get_string('gradessettings', 'grades'));

// enable outcomes checkbox
$temp->add(new admin_setting_configcheckbox('enableoutcomes', get_string('enableoutcomes', 'grades'), get_string('configenableoutcomes', 'grades'), 0, PARAM_INT));

$temp->add(new admin_setting_configselect('grade_aggregationposition', get_string('aggregationposition', 'grades'),
                                          get_string('configaggregationposition', 'grades'), GRADE_REPORT_AGGREGATION_POSITION_LAST,
                                          array(GRADE_REPORT_AGGREGATION_POSITION_FIRST => get_string('positionfirst', 'grades'),
                                                GRADE_REPORT_AGGREGATION_POSITION_LAST => get_string('positionlast', 'grades'))));

$temp->add(new admin_setting_configselect('grade_displaytype', get_string('gradedisplaytype', 'grades'),
                                          get_string('configgradedisplaytype', 'grades'), GRADE_DISPLAY_TYPE_REAL,
                                          array(GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                                                GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades'))));

$temp->add(new admin_setting_configselect('grade_decimalpoints', get_string('decimalpoints', 'grades'),
                                          get_string('configdecimalpoints', 'grades'), 2,
                                          array( '0' => '0',
                                                 '1' => '1',
                                                 '2' => '2',
                                                 '3' => '3',
                                                 '4' => '4',
                                                 '5' => '5')));

$temp->add(new admin_setting_configcheckbox('grade_hiddenasdate', get_string('hiddenasdate', 'grades'), get_string('confighiddenasdate', 'grades'), 0, PARAM_INT));

// enable publishing in exports/imports
$temp->add(new admin_setting_configcheckbox('gradepublishing', get_string('gradepublishing', 'grades'), get_string('configgradepublishing', 'grades'), 0, PARAM_INT));

$temp->add(new admin_setting_configselect('grade_export_displaytype', get_string('gradeexportdisplaytype', 'grades'),
                                          get_string('configgradeexportdisplaytype', 'grades'), GRADE_DISPLAY_TYPE_REAL,
                                          array(GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                                                GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                                                GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades'))));

$temp->add(new admin_setting_configselect('grade_export_decimalpoints', get_string('gradeexportdecimalpoints', 'grades'),
                                          get_string('configexportdecimalpoints', 'grades'), 2,
                                          array( '0' => '0',
                                                 '1' => '1',
                                                 '2' => '2',
                                                 '3' => '3',
                                                 '4' => '4',
                                                 '5' => '5')));

$temp->add(new admin_setting_special_gradeexport());
$ADMIN->add('grades', $temp);

/// Grade category settings
$temp = new admin_settingpage('gradecategorysettings', get_string('gradecategorysettings', 'grades'));

$temp->add(new admin_setting_configcheckbox('grade_hideforcedsettings', get_string('hideforcedsettings', 'grades'), get_string('confighideforcedsettings', 'grades'), 0, PARAM_INT));

$strnoforce = get_string('noforce', 'grades');

// Aggregation type
$options = array(-1                              =>$strnoforce,
                 GRADE_AGGREGATE_MEAN            =>get_string('aggregatemean', 'grades'),
                 GRADE_AGGREGATE_MEDIAN          =>get_string('aggregatemedian', 'grades'),
                 GRADE_AGGREGATE_MIN             =>get_string('aggregatemin', 'grades'),
                 GRADE_AGGREGATE_MAX             =>get_string('aggregatemax', 'grades'),
                 GRADE_AGGREGATE_MODE            =>get_string('aggregatemode', 'grades'),
                 GRADE_AGGREGATE_WEIGHTED_MEAN   =>get_string('aggregateweightedmean', 'grades'),
                 GRADE_AGGREGATE_EXTRACREDIT_MEAN=>get_string('aggregateextracreditmean', 'grades'));
$temp->add(new admin_category_regrade_select('grade_aggregation', get_string('aggregation', 'grades'), get_string('aggregationhelp', 'grades'), -1, $options));

$options = array(-1 => $strnoforce, 0 => get_string('forceoff', 'grades'), 1 => get_string('forceon', 'grades'));
$temp->add(new admin_category_regrade_select('grade_aggregateonlygraded', get_string('aggregateonlygraded', 'grades'),
            get_string('aggregateonlygradedhelp', 'grades'), -1, $options));
$temp->add(new admin_category_regrade_select('grade_aggregateoutcomes', get_string('aggregateoutcomes', 'grades'),
            get_string('aggregateoutcomeshelp', 'grades'), -1, $options));
$temp->add(new admin_category_regrade_select('grade_aggregatesubcats', get_string('aggregatesubcats', 'grades'),
            get_string('aggregatesubcatshelp', 'grades'), -1, $options));

$options = array(-1 => $strnoforce, 0 => get_string('none'));
for ($i=1; $i<=20; $i++) {
    $options[$i] = $i;
}

$temp->add(new admin_category_regrade_select('grade_keephigh', get_string('keephigh', 'grades'),
            get_string('keephighhelp', 'grades'), -1, $options));
$temp->add(new admin_category_regrade_select('grade_droplow', get_string('droplow', 'grades'),
            get_string('droplowhelp', 'grades'), -1, $options));

$ADMIN->add('grades', $temp);

/// Scales and outcomes

$scales = new admin_externalpage('scales', get_string('scales'), $CFG->wwwroot.'/grade/edit/scale/index.php', 'moodle/grade:manage');
$ADMIN->add('grades', $scales);
$outcomes = new admin_externalpage('outcomes', get_string('outcomes', 'grades'), $CFG->wwwroot.'/grade/edit/outcome/index.php', 'moodle/grade:manage');
$ADMIN->add('grades', $outcomes);
$letters = new admin_externalpage('letters', get_string('letters', 'grades'), $CFG->wwwroot.'/grade/edit/letter/edit.php', 'moodle/grade:manageletters');
$ADMIN->add('grades', $letters);

// The plugins must implement a settings.php file that adds their admin settings to the $settings object

// Reports

$first = true;
foreach (get_list_of_plugins('grade/report') as $plugin) {
 // Include all the settings commands for this plugin if there are any
    if ($first) {
        $ADMIN->add('grades', new admin_category('gradereports', get_string('reportsettings', 'grades')));
        $first = false;
    }

    if (file_exists($CFG->dirroot.'/grade/report/'.$plugin.'/settings.php')) {

        $settings = new admin_settingpage('gradereport'.$plugin, get_string('modulename', 'gradereport_'.$plugin));
        include_once($CFG->dirroot.'/grade/report/'.$plugin.'/settings.php');
        $ADMIN->add('gradereports', $settings);
    }
}

// Imports

$first = true;
foreach (get_list_of_plugins('grade/import') as $plugin) {

 // Include all the settings commands for this plugin if there are any
    if (file_exists($CFG->dirroot.'/grade/import/'.$plugin.'/settings.php')) {
        if ($first) {
            $ADMIN->add('grades', new admin_category('gradeimports', get_string('imports')));
            $first = false;
        }

        $settings = new admin_settingpage('gradeimport'.$plugin, get_string('modulename', 'gradeimport_'.$plugin));
        include_once($CFG->dirroot.'/grade/import/'.$plugin.'/settings.php');
        $ADMIN->add('gradeimports', $settings);
    }
}


// Exports

$first = true;
foreach (get_list_of_plugins('grade/export') as $plugin) {
 // Include all the settings commands for this plugin if there are any
    if (file_exists($CFG->dirroot.'/grade/export/'.$plugin.'/settings.php')) {
        if ($first) {
            $ADMIN->add('grades', new admin_category('gradeexports', get_string('exports')));
            $first = false;
        }

        $settings = new admin_settingpage('gradeexport'.$plugin, get_string('modulename', 'gradeexport_'.$plugin));
        include_once($CFG->dirroot.'/grade/export/'.$plugin.'/settings.php');
        $ADMIN->add('gradeexports', $settings);
    }
}
?>
