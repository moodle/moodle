<?php

// This file defines settingpages and externalpages under the "grades" section

if (has_capability('moodle/grade:manage', $systemcontext)
 or has_capability('moodle/grade:manageletters', $systemcontext)) { // speedup for non-admins, add all caps used on this page

    require_once $CFG->libdir.'/grade/constants.php';
    $display_types = array(GRADE_DISPLAY_TYPE_REAL => get_string('real', 'grades'),
                           GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades'),
                           GRADE_DISPLAY_TYPE_LETTER => get_string('letter', 'grades'),
                           GRADE_DISPLAY_TYPE_REAL_PERCENTAGE => get_string('realpercentage', 'grades'),
                           GRADE_DISPLAY_TYPE_REAL_LETTER => get_string('realletter', 'grades'),
                           GRADE_DISPLAY_TYPE_LETTER_REAL => get_string('letterreal', 'grades'),
                           GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE => get_string('letterpercentage', 'grades'),
                           GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER => get_string('percentageletter', 'grades'),
                           GRADE_DISPLAY_TYPE_PERCENTAGE_REAL => get_string('percentagereal', 'grades')
                           );
    asort($display_types);

    // General settings

    $temp = new admin_settingpage('gradessettings', get_string('generalsettings', 'grades'), 'moodle/grade:manage');
    if ($ADMIN->fulltree) {

        // new CFG variable for gradebook (what roles to display)
        $temp->add(new admin_setting_special_gradebookroles());

        // enable outcomes checkbox now in subsystems area

        $temp->add(new admin_setting_grade_profilereport());

        $temp->add(new admin_setting_configselect('grade_aggregationposition', get_string('aggregationposition', 'grades'),
                                                  get_string('aggregationposition_help', 'grades'), GRADE_REPORT_AGGREGATION_POSITION_LAST,
                                                  array(GRADE_REPORT_AGGREGATION_POSITION_FIRST => get_string('positionfirst', 'grades'),
                                                        GRADE_REPORT_AGGREGATION_POSITION_LAST => get_string('positionlast', 'grades'))));

        $temp->add(new admin_setting_regradingcheckbox('grade_includescalesinaggregation', get_string('includescalesinaggregation', 'grades'), get_string('includescalesinaggregation_help', 'grades'), 1));

        $temp->add(new admin_setting_configcheckbox('grade_hiddenasdate', get_string('hiddenasdate', 'grades'), get_string('hiddenasdate_help', 'grades'), 0));

        // enable publishing in exports/imports
        $temp->add(new admin_setting_configcheckbox('gradepublishing', get_string('gradepublishing', 'grades'), get_string('gradepublishing_help', 'grades'), 0));

        $temp->add(new admin_setting_configselect('grade_export_displaytype', get_string('gradeexportdisplaytype', 'grades'),
                                                  get_string('gradeexportdisplaytype_desc', 'grades'), GRADE_DISPLAY_TYPE_REAL, $display_types));

        $temp->add(new admin_setting_configselect('grade_export_decimalpoints', get_string('gradeexportdecimalpoints', 'grades'),
                                                  get_string('gradeexportdecimalpoints_desc', 'grades'), 2,
                                                  array( '0' => '0',
                                                         '1' => '1',
                                                         '2' => '2',
                                                         '3' => '3',
                                                         '4' => '4',
                                                         '5' => '5')));
        $temp->add(new admin_setting_configselect('grade_navmethod', get_string('navmethod', 'grades'), null, 0,
                                                  array(GRADE_NAVMETHOD_DROPDOWN => get_string('dropdown', 'grades'),
                                                        GRADE_NAVMETHOD_TABS => get_string('tabs', 'grades'),
                                                        GRADE_NAVMETHOD_COMBO => get_string('combo', 'grades'))));

        $temp->add(new admin_setting_special_gradeexport());

        $temp->add(new admin_setting_special_gradelimiting());
    }
    $ADMIN->add('grades', $temp);

    /// Grade category settings
    $temp = new admin_settingpage('gradecategorysettings', get_string('gradecategorysettings', 'grades'), 'moodle/grade:manage');
    if ($ADMIN->fulltree) {
        $temp->add(new admin_setting_configcheckbox('grade_hideforcedsettings', get_string('hideforcedsettings', 'grades'), get_string('hideforcedsettings_help', 'grades'), '1'));

        $strnoforce = get_string('noforce', 'grades');

        // Aggregation type
        $options = array(GRADE_AGGREGATE_MEAN            =>get_string('aggregatemean', 'grades'),
                         GRADE_AGGREGATE_WEIGHTED_MEAN   =>get_string('aggregateweightedmean', 'grades'),
                         GRADE_AGGREGATE_WEIGHTED_MEAN2  =>get_string('aggregateweightedmean2', 'grades'),
                         GRADE_AGGREGATE_EXTRACREDIT_MEAN=>get_string('aggregateextracreditmean', 'grades'),
                         GRADE_AGGREGATE_MEDIAN          =>get_string('aggregatemedian', 'grades'),
                         GRADE_AGGREGATE_MIN             =>get_string('aggregatemin', 'grades'),
                         GRADE_AGGREGATE_MAX             =>get_string('aggregatemax', 'grades'),
                         GRADE_AGGREGATE_MODE            =>get_string('aggregatemode', 'grades'),
                         GRADE_AGGREGATE_SUM             =>get_string('aggregatesum', 'grades'));

        $defaultvisible = array(GRADE_AGGREGATE_MEAN, GRADE_AGGREGATE_WEIGHTED_MEAN, GRADE_AGGREGATE_WEIGHTED_MEAN2,
                                GRADE_AGGREGATE_EXTRACREDIT_MEAN, GRADE_AGGREGATE_MEDIAN, GRADE_AGGREGATE_MIN,
                                GRADE_AGGREGATE_MAX, GRADE_AGGREGATE_MODE, GRADE_AGGREGATE_SUM);

        $defaults = array('value'=>GRADE_AGGREGATE_WEIGHTED_MEAN2, 'forced'=>false, 'adv'=>false);
        $temp->add(new admin_setting_gradecat_combo('grade_aggregation', get_string('aggregation', 'grades'), get_string('aggregation_help', 'grades'), $defaults, $options));

        $temp->add(new admin_setting_configmultiselect('grade_aggregations_visible', get_string('aggregationsvisible', 'grades'),
                                                       get_string('aggregationsvisiblehelp', 'grades'), $defaultvisible, $options));

        $options = array(0 => get_string('no'), 1 => get_string('yes'));

        $defaults = array('value'=>1, 'forced'=>false, 'adv'=>true);
        $temp->add(new admin_setting_gradecat_combo('grade_aggregateonlygraded', get_string('aggregateonlygraded', 'grades'),
                    get_string('aggregateonlygraded_help', 'grades'), $defaults, $options));
        $defaults = array('value'=>0, 'forced'=>false, 'adv'=>true);
        $temp->add(new admin_setting_gradecat_combo('grade_aggregateoutcomes', get_string('aggregateoutcomes', 'grades'),
                    get_string('aggregateoutcomes_help', 'grades'), $defaults, $options));
        $temp->add(new admin_setting_gradecat_combo('grade_aggregatesubcats', get_string('aggregatesubcats', 'grades'),
                    get_string('aggregatesubcats_help', 'grades'), $defaults, $options));

        $options = array(0 => get_string('none'));
        for ($i=1; $i<=20; $i++) {
            $options[$i] = $i;
        }

        $defaults['value'] = 0;
        $defaults['forced'] = true;
        $temp->add(new admin_setting_gradecat_combo('grade_keephigh', get_string('keephigh', 'grades'),
                    get_string('keephigh_help', 'grades'), $defaults, $options));
        $defaults['forced'] = false;
        $temp->add(new admin_setting_gradecat_combo('grade_droplow', get_string('droplow', 'grades'),
                    get_string('droplow_help', 'grades'), $defaults, $options));
    }
    $ADMIN->add('grades', $temp);


    /// Grade item settings
    $temp = new admin_settingpage('gradeitemsettings', get_string('gradeitemsettings', 'grades'), 'moodle/grade:manage');
    if ($ADMIN->fulltree) {
        $temp->add(new admin_setting_configselect('grade_displaytype', get_string('gradedisplaytype', 'grades'),
                                                  get_string('gradedisplaytype_help', 'grades'), GRADE_DISPLAY_TYPE_REAL, $display_types));

        $temp->add(new admin_setting_configselect('grade_decimalpoints', get_string('decimalpoints', 'grades'),
                                                  get_string('decimalpoints_help', 'grades'), 2,
                                                  array( '0' => '0',
                                                         '1' => '1',
                                                         '2' => '2',
                                                         '3' => '3',
                                                         '4' => '4',
                                                         '5' => '5')));

        $temp->add(new admin_setting_configmultiselect('grade_item_advanced', get_string('gradeitemadvanced', 'grades'), get_string('gradeitemadvanced_help', 'grades'),
                                                       array('iteminfo', 'idnumber', 'gradepass', 'plusfactor', 'multfactor', 'display', 'decimals', 'hiddenuntil', 'locktime'),
                                                       array('iteminfo' => get_string('iteminfo', 'grades'),
                                                             'idnumber' => get_string('idnumbermod'),
                                                             'gradetype' => get_string('gradetype', 'grades'),
                                                             'scaleid' => get_string('scale'),
                                                             'grademin' => get_string('grademin', 'grades'),
                                                             'grademax' => get_string('grademax', 'grades'),
                                                             'gradepass' => get_string('gradepass', 'grades'),
                                                             'plusfactor' => get_string('plusfactor', 'grades'),
                                                             'multfactor' => get_string('multfactor', 'grades'),
                                                             'display' => get_string('gradedisplaytype', 'grades'),
                                                             'decimals' => get_string('decimalpoints', 'grades'),
                                                             'hidden' => get_string('hidden', 'grades'),
                                                             'hiddenuntil' => get_string('hiddenuntil', 'grades'),
                                                             'locked' => get_string('locked', 'grades'),
                                                             'locktime' => get_string('locktime', 'grades'),
                                                             'aggregationcoef' => get_string('aggregationcoef', 'grades'),
                                                             'parentcategory' => get_string('parentcategory', 'grades'))));
    }
    $ADMIN->add('grades', $temp);


    /// Scales and outcomes

    $scales = new admin_externalpage('scales', get_string('scales'), $CFG->wwwroot.'/grade/edit/scale/index.php', 'moodle/grade:manage');
    $ADMIN->add('grades', $scales);
    if (!empty($CFG->enableoutcomes)) {
        $outcomes = new admin_externalpage('outcomes', get_string('outcomes', 'grades'), $CFG->wwwroot.'/grade/edit/outcome/index.php', 'moodle/grade:manage');
        $ADMIN->add('grades', $outcomes);
    }
    $letters = new admin_externalpage('letters', get_string('letters', 'grades'), $CFG->wwwroot.'/grade/edit/letter/index.php', 'moodle/grade:manageletters');
    $ADMIN->add('grades', $letters);

    // The plugins must implement a settings.php file that adds their admin settings to the $settings object

    // Reports
    $ADMIN->add('grades', new admin_category('gradereports', get_string('reportsettings', 'grades')));
    foreach (get_plugin_list('gradereport') as $plugin => $plugindir) {
     // Include all the settings commands for this plugin if there are any
        if (file_exists($plugindir.'/settings.php')) {
            $settings = new admin_settingpage('gradereport'.$plugin, get_string('pluginname', 'gradereport_'.$plugin), 'moodle/grade:manage');
            include($plugindir.'/settings.php');
            if ($settings) {
                $ADMIN->add('gradereports', $settings);
            }
        }
    }

    // Imports
    $ADMIN->add('grades', new admin_category('gradeimports', get_string('importsettings', 'grades')));
    foreach (get_plugin_list('gradeimport') as $plugin => $plugindir) {

     // Include all the settings commands for this plugin if there are any
        if (file_exists($plugindir.'/settings.php')) {
            $settings = new admin_settingpage('gradeimport'.$plugin, get_string('pluginname', 'gradeimport_'.$plugin), 'moodle/grade:manage');
            include($plugindir.'/settings.php');
            if ($settings) {
                $ADMIN->add('gradeimports', $settings);
            }
        }
    }


    // Exports
    $ADMIN->add('grades', new admin_category('gradeexports', get_string('exportsettings', 'grades')));
    foreach (get_plugin_list('gradeexport') as $plugin => $plugindir) {
     // Include all the settings commands for this plugin if there are any
        if (file_exists($plugindir.'/settings.php')) {
            $settings = new admin_settingpage('gradeexport'.$plugin, get_string('pluginname', 'gradeexport_'.$plugin), 'moodle/grade:manage');
            include($plugindir.'/settings.php');
            if ($settings) {
                $ADMIN->add('gradeexports', $settings);
            }
        }
    }

} // end of speedup

