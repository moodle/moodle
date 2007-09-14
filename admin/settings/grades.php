<?php // $Id$

// This file defines settingpages and externalpages under the "grades" section

// General settings

$temp = new admin_settingpage('gradessettings', get_string('gradessettings', 'grades'));
$temp->add(new admin_setting_special_gradeexport());
// enable outcomes checkbox
$temp->add(new admin_setting_configcheckbox('enableoutcomes', get_string('enableoutcomes', 'grades'), get_string('configenableoutcomes', 'grades'), 0, PARAM_INT));
// enable publishing in exports/imports
$temp->add(new admin_setting_configcheckbox('gradepublishing', get_string('gradepublishing', 'grades'), get_string('configgradepublishing', 'grades'), 0, PARAM_INT));
$ADMIN->add('grades', $temp);

/// Scales and outcomes

$scales = new admin_externalpage('scales', get_string('scales'), $CFG->wwwroot.'/grade/edit/scale/index.php', 'moodle/grade:manage');
$ADMIN->add('grades', $scales);
$outcomes = new admin_externalpage('outcomes', get_string('outcomes', 'grades'), $CFG->wwwroot.'/grade/edit/outcome/index.php', 'moodle/grade:manage');
$ADMIN->add('grades', $outcomes);

/// Grade category settings
require_once $CFG->libdir . '/grade/constants.php';
$temp = new admin_settingpage('gradecategorysettings', get_string('gradecategorysettings', 'grades'));
$strnoforce = get_string('noforce', 'grades');

    // Aggregation type
$options = array(-1 => $strnoforce,
                 GRADE_AGGREGATE_MEAN            =>get_string('aggregatemean', 'grades'),
                 GRADE_AGGREGATE_MEDIAN          =>get_string('aggregatemedian', 'grades'),
                 GRADE_AGGREGATE_MIN             =>get_string('aggregatemin', 'grades'),
                 GRADE_AGGREGATE_MAX             =>get_string('aggregatemax', 'grades'),
                 GRADE_AGGREGATE_MODE            =>get_string('aggregatemode', 'grades'),
                 GRADE_AGGREGATE_WEIGHTED_MEAN   =>get_string('aggregateweightedmean', 'grades'),
                 GRADE_AGGREGATE_EXTRACREDIT_MEAN=>get_string('aggregateextracreditmean', 'grades'));
$temp->add(new admin_setting_configselect('aggregation', get_string('aggregation', 'grades'), get_string('aggregationhelp', 'grades'), -1, $options));

$options = array(-1 => $strnoforce, 0 => get_string('forceoff', 'grades'), 1 => get_string('forceon', 'grades'));
$temp->add(new admin_setting_configselect('aggregateonlygraded', get_string('aggregateonlygraded', 'grades'),
            get_string('aggregateonlygradedhelp', 'grades'), -1, $options));
$temp->add(new admin_setting_configselect('aggregateoutcomes', get_string('aggregateoutcomes', 'grades'),
            get_string('aggregateoutcomeshelp', 'grades'), -1, $options));
$temp->add(new admin_setting_configselect('aggregatesubcats', get_string('aggregatesubcats', 'grades'),
            get_string('aggregatesubcatshelp', 'grades'), -1, $options));

$options = array(-1 => $strnoforce, 0 => get_string('none'));
for ($i=1; $i<=20; $i++) {
    $options[$i] = $i;
}

$temp->add(new admin_setting_configselect('keephigh', get_string('keephigh', 'grades'),
            get_string('keephighhelp', 'grades'), -1, $options));
$temp->add(new admin_setting_configselect('droplow', get_string('droplow', 'grades'),
            get_string('droplowhelp', 'grades'), -1, $options));

$ADMIN->add('grades', $temp);

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
