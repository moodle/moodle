<?php

// This file defines settingpages and externalpages under the "grades" section

if (has_capability('moodle/grade:manage', $systemcontext)
 or has_capability('moodle/grade:manageletters', $systemcontext)) { // speedup for non-admins, add all caps used on this page

    require_once $CFG->libdir.'/grade/constants.php';
    $display_types = array(GRADE_DISPLAY_TYPE_REAL => new lang_string('real', 'grades'),
                           GRADE_DISPLAY_TYPE_PERCENTAGE => new lang_string('percentage', 'grades'),
                           GRADE_DISPLAY_TYPE_LETTER => new lang_string('letter', 'grades'),
                           GRADE_DISPLAY_TYPE_REAL_PERCENTAGE => new lang_string('realpercentage', 'grades'),
                           GRADE_DISPLAY_TYPE_REAL_LETTER => new lang_string('realletter', 'grades'),
                           GRADE_DISPLAY_TYPE_LETTER_REAL => new lang_string('letterreal', 'grades'),
                           GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE => new lang_string('letterpercentage', 'grades'),
                           GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER => new lang_string('percentageletter', 'grades'),
                           GRADE_DISPLAY_TYPE_PERCENTAGE_REAL => new lang_string('percentagereal', 'grades')
                           );
    asort($display_types);

    // General settings

    $temp = new admin_settingpage('gradessettings', new lang_string('generalsettings', 'grades'), 'moodle/grade:manage');
    if ($ADMIN->fulltree) {

        // new CFG variable for gradebook (what roles to display)
        $temp->add(new admin_setting_special_gradebookroles());

        // enable outcomes checkbox now in subsystems area

        $temp->add(new admin_setting_grade_profilereport());

        $temp->add(new admin_setting_configselect('grade_aggregationposition', new lang_string('aggregationposition', 'grades'),
                                                  new lang_string('aggregationposition_help', 'grades'), GRADE_REPORT_AGGREGATION_POSITION_LAST,
                                                  array(GRADE_REPORT_AGGREGATION_POSITION_FIRST => new lang_string('positionfirst', 'grades'),
                                                        GRADE_REPORT_AGGREGATION_POSITION_LAST => new lang_string('positionlast', 'grades'))));

        $temp->add(new admin_setting_regradingcheckbox('grade_includescalesinaggregation', new lang_string('includescalesinaggregation', 'grades'), new lang_string('includescalesinaggregation_help', 'grades'), 1));

        $temp->add(new admin_setting_configcheckbox('grade_hiddenasdate', new lang_string('hiddenasdate', 'grades'), new lang_string('hiddenasdate_help', 'grades'), 0));

        // enable publishing in exports/imports
        $temp->add(new admin_setting_configcheckbox('gradepublishing', new lang_string('gradepublishing', 'grades'), new lang_string('gradepublishing_help', 'grades'), 0));

        $temp->add(new admin_setting_configselect('grade_export_displaytype', new lang_string('gradeexportdisplaytype', 'grades'),
                                                  new lang_string('gradeexportdisplaytype_desc', 'grades'), GRADE_DISPLAY_TYPE_REAL, $display_types));

        $temp->add(new admin_setting_configselect('grade_export_decimalpoints', new lang_string('gradeexportdecimalpoints', 'grades'),
                                                  new lang_string('gradeexportdecimalpoints_desc', 'grades'), 2,
                                                  array( '0' => '0',
                                                         '1' => '1',
                                                         '2' => '2',
                                                         '3' => '3',
                                                         '4' => '4',
                                                         '5' => '5')));
        $temp->add(new admin_setting_configselect('grade_navmethod', new lang_string('navmethod', 'grades'), null, 0,
                                                  array(GRADE_NAVMETHOD_DROPDOWN => new lang_string('dropdown', 'grades'),
                                                        GRADE_NAVMETHOD_TABS => new lang_string('tabs', 'grades'),
                                                        GRADE_NAVMETHOD_COMBO => new lang_string('combo', 'grades'))));

        $temp->add(new admin_setting_configtext('grade_export_userprofilefields', new lang_string('gradeexportuserprofilefields', 'grades'), new lang_string('gradeexportuserprofilefields_desc', 'grades'), 'firstname,lastname,idnumber,institution,department,email', PARAM_TEXT));

        $temp->add(new admin_setting_configtext('grade_export_customprofilefields', new lang_string('gradeexportcustomprofilefields', 'grades'), new lang_string('gradeexportcustomprofilefields_desc', 'grades'), '', PARAM_TEXT));

        $temp->add(new admin_setting_configcheckbox('recovergradesdefault', new lang_string('recovergradesdefault', 'grades'), new lang_string('recovergradesdefault_help', 'grades'), 0));

        $temp->add(new admin_setting_special_gradeexport());

        $temp->add(new admin_setting_special_gradelimiting());

        $temp->add(new admin_setting_configcheckbox('grade_report_showmin',
                                                    get_string('minimum_show', 'grades'),
                                                    get_string('minimum_show_help', 'grades'), '1'));

        $temp->add(new admin_setting_special_gradepointmax());

        $temp->add(new admin_setting_special_gradepointdefault());

        $temp->add(new admin_setting_my_grades_report());

        $temp->add(new admin_setting_configtext('gradereport_mygradeurl', new lang_string('externalurl', 'grades'),
                new lang_string('externalurl_desc', 'grades'), ''));
    }
    $ADMIN->add('grades', $temp);

    /// Grade category settings
    $temp = new admin_settingpage('gradecategorysettings', new lang_string('gradecategorysettings', 'grades'), 'moodle/grade:manage');
    if ($ADMIN->fulltree) {
        $temp->add(new admin_setting_configcheckbox('grade_hideforcedsettings', new lang_string('hideforcedsettings', 'grades'), new lang_string('hideforcedsettings_help', 'grades'), '1'));

        $strnoforce = new lang_string('noforce', 'grades');

        // Aggregation type
        $options = array(GRADE_AGGREGATE_MEAN            =>new lang_string('aggregatemean', 'grades'),
                         GRADE_AGGREGATE_WEIGHTED_MEAN   =>new lang_string('aggregateweightedmean', 'grades'),
                         GRADE_AGGREGATE_WEIGHTED_MEAN2  =>new lang_string('aggregateweightedmean2', 'grades'),
                         GRADE_AGGREGATE_EXTRACREDIT_MEAN=>new lang_string('aggregateextracreditmean', 'grades'),
                         GRADE_AGGREGATE_MEDIAN          =>new lang_string('aggregatemedian', 'grades'),
                         GRADE_AGGREGATE_MIN             =>new lang_string('aggregatemin', 'grades'),
                         GRADE_AGGREGATE_MAX             =>new lang_string('aggregatemax', 'grades'),
                         GRADE_AGGREGATE_MODE            =>new lang_string('aggregatemode', 'grades'),
                         GRADE_AGGREGATE_SUM             =>new lang_string('aggregatesum', 'grades'));

        $defaultvisible = array(GRADE_AGGREGATE_SUM);

        $defaults = array('value' => GRADE_AGGREGATE_SUM, 'forced' => false, 'adv' => false);
        $temp->add(new admin_setting_gradecat_combo('grade_aggregation', new lang_string('aggregation', 'grades'), new lang_string('aggregation_help', 'grades'), $defaults, $options));

        $temp->add(new admin_setting_configmultiselect('grade_aggregations_visible', new lang_string('aggregationsvisible', 'grades'),
                                                       new lang_string('aggregationsvisiblehelp', 'grades'), $defaultvisible, $options));

        $options = array(0 => new lang_string('no'), 1 => new lang_string('yes'));

        $defaults = array('value'=>1, 'forced'=>false, 'adv'=>true);
        $temp->add(new admin_setting_gradecat_combo('grade_aggregateonlygraded', new lang_string('aggregateonlygraded', 'grades'),
                    new lang_string('aggregateonlygraded_help', 'grades'), $defaults, $options));
        $defaults = array('value'=>0, 'forced'=>false, 'adv'=>true);
        $temp->add(new admin_setting_gradecat_combo('grade_aggregateoutcomes', new lang_string('aggregateoutcomes', 'grades'),
                    new lang_string('aggregateoutcomes_help', 'grades'), $defaults, $options));

        $options = array(0 => new lang_string('none'));
        for ($i=1; $i<=20; $i++) {
            $options[$i] = $i;
        }

        $defaults['value'] = 0;
        $defaults['forced'] = true;
        $temp->add(new admin_setting_gradecat_combo('grade_keephigh', new lang_string('keephigh', 'grades'),
                    new lang_string('keephigh_help', 'grades'), $defaults, $options));
        $defaults['forced'] = false;
        $temp->add(new admin_setting_gradecat_combo('grade_droplow', new lang_string('droplow', 'grades'),
                    new lang_string('droplow_help', 'grades'), $defaults, $options));

        $temp->add(new admin_setting_configcheckbox('grade_overridecat', new lang_string('overridecat', 'grades'),
                   new lang_string('overridecat_help', 'grades'), 1));
    }
    $ADMIN->add('grades', $temp);


    /// Grade item settings
    $temp = new admin_settingpage('gradeitemsettings', new lang_string('gradeitemsettings', 'grades'), 'moodle/grade:manage');
    if ($ADMIN->fulltree) {
        $temp->add(new admin_setting_configselect('grade_displaytype', new lang_string('gradedisplaytype', 'grades'),
                                                  new lang_string('gradedisplaytype_help', 'grades'), GRADE_DISPLAY_TYPE_REAL, $display_types));

        $temp->add(new admin_setting_configselect('grade_decimalpoints', new lang_string('decimalpoints', 'grades'),
                                                  new lang_string('decimalpoints_help', 'grades'), 2,
                                                  array( '0' => '0',
                                                         '1' => '1',
                                                         '2' => '2',
                                                         '3' => '3',
                                                         '4' => '4',
                                                         '5' => '5')));

        $temp->add(new admin_setting_configmultiselect('grade_item_advanced', new lang_string('gradeitemadvanced', 'grades'), new lang_string('gradeitemadvanced_help', 'grades'),
                                                       array('iteminfo', 'idnumber', 'gradepass', 'plusfactor', 'multfactor', 'display', 'decimals', 'hiddenuntil', 'locktime'),
                                                       array('iteminfo' => new lang_string('iteminfo', 'grades'),
                                                             'idnumber' => new lang_string('idnumbermod'),
                                                             'gradetype' => new lang_string('gradetype', 'grades'),
                                                             'scaleid' => new lang_string('scale'),
                                                             'grademin' => new lang_string('grademin', 'grades'),
                                                             'grademax' => new lang_string('grademax', 'grades'),
                                                             'gradepass' => new lang_string('gradepass', 'grades'),
                                                             'plusfactor' => new lang_string('plusfactor', 'grades'),
                                                             'multfactor' => new lang_string('multfactor', 'grades'),
                                                             'display' => new lang_string('gradedisplaytype', 'grades'),
                                                             'decimals' => new lang_string('decimalpoints', 'grades'),
                                                             'hidden' => new lang_string('hidden', 'grades'),
                                                             'hiddenuntil' => new lang_string('hiddenuntil', 'grades'),
                                                             'locked' => new lang_string('locked', 'grades'),
                                                             'locktime' => new lang_string('locktime', 'grades'),
                                                             'aggregationcoef' => new lang_string('aggregationcoef', 'grades'),
                                                             'parentcategory' => new lang_string('parentcategory', 'grades'))));
    }
    $ADMIN->add('grades', $temp);


    /// Scales and outcomes

    $scales = new admin_externalpage('scales', new lang_string('scales'), $CFG->wwwroot.'/grade/edit/scale/index.php', 'moodle/grade:manage');
    $ADMIN->add('grades', $scales);
    if (!empty($CFG->enableoutcomes)) {
        $outcomes = new admin_externalpage('outcomes', new lang_string('outcomes', 'grades'), $CFG->wwwroot.'/grade/edit/outcome/index.php', 'moodle/grade:manage');
        $ADMIN->add('grades', $outcomes);
    }
    $letters = new admin_externalpage('letters', new lang_string('letters', 'grades'), $CFG->wwwroot.'/grade/edit/letter/index.php', 'moodle/grade:manageletters');
    $ADMIN->add('grades', $letters);

    // The plugins must implement a settings.php file that adds their admin settings to the $settings object

    // Reports
    $ADMIN->add('grades', new admin_category('gradereports', new lang_string('reportsettings', 'grades')));
    foreach (core_component::get_plugin_list('gradereport') as $plugin => $plugindir) {
     // Include all the settings commands for this plugin if there are any
        if (file_exists($plugindir.'/settings.php')) {
            $settings = new admin_settingpage('gradereport'.$plugin, new lang_string('pluginname', 'gradereport_'.$plugin), 'moodle/grade:manage');
            include($plugindir.'/settings.php');
            if ($settings) {
                $ADMIN->add('gradereports', $settings);
            }
        }
    }

    // Imports
    $ADMIN->add('grades', new admin_category('gradeimports', new lang_string('importsettings', 'grades')));
    foreach (core_component::get_plugin_list('gradeimport') as $plugin => $plugindir) {

     // Include all the settings commands for this plugin if there are any
        if (file_exists($plugindir.'/settings.php')) {
            $settings = new admin_settingpage('gradeimport'.$plugin, new lang_string('pluginname', 'gradeimport_'.$plugin), 'moodle/grade:manage');
            include($plugindir.'/settings.php');
            if ($settings) {
                $ADMIN->add('gradeimports', $settings);
            }
        }
    }


    // Exports
    $ADMIN->add('grades', new admin_category('gradeexports', new lang_string('exportsettings', 'grades')));
    foreach (core_component::get_plugin_list('gradeexport') as $plugin => $plugindir) {
     // Include all the settings commands for this plugin if there are any
        if (file_exists($plugindir.'/settings.php')) {
            $settings = new admin_settingpage('gradeexport'.$plugin, new lang_string('pluginname', 'gradeexport_'.$plugin), 'moodle/grade:manage');
            include($plugindir.'/settings.php');
            if ($settings) {
                $ADMIN->add('gradeexports', $settings);
            }
        }
    }

} // end of speedup

