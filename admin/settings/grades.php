<?php // $Id$

// This file defines settingpages and externalpages under the "grades" section

// General settings

$temp = new admin_settingpage('gradessettings', get_string('gradessettings', 'grades'));
$temp->add(new admin_setting_special_gradeexport());
// enable outcomes checkbox
$temp->add(new admin_setting_configcheckbox('enableoutcomes', get_string('enableoutcomes', 'grades'), get_string('configenableoutcomes', 'grades'), 0, PARAM_INT));
$ADMIN->add('grades', $temp);

/// Scales and outcomes

$scales = new admin_externalpage('scales', get_string('scales'), $CFG->wwwroot.'/grade/edit/scale/index.php', 'moodle/grade:manage');
$ADMIN->add('grades', $scales);
$outcomes = new admin_externalpage('outcomes', get_string('outcomes', 'grades'), $CFG->wwwroot.'/grade/edit/outcome/index.php', 'moodle/grade:manage');
$ADMIN->add('grades', $outcomes);


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
