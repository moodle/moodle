<?php
       // enrol_config.php - allows admin to edit all enrollment variables
       //                    Yes, enrol is correct English spelling.

    require_once("../config.php");
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('enrolment');

    $enrol = required_param('enrol', PARAM_ALPHA);
    $PAGE->set_pagetype('admin-enrol-' . $enrol);

    require_once("$CFG->dirroot/enrol/enrol.class.php");   /// Open the factory class

    $enrolment = enrolment_factory::factory($enrol);

/// If data submitted, then process and store.

    if ($frm = data_submitted()) {
        if (!confirm_sesskey()) {
            print_error('confirmsesskeybad', 'error');
        }
        if ($enrolment->process_config($frm)) {
            redirect("enrol.php?sesskey=".sesskey(), get_string("changessaved"), 1);
        }
    } else {
        $frm = $CFG;
    }

/// Otherwise fill and print the form.

    /// get language strings
    $str = get_strings(array('enrolmentplugins', 'configuration', 'users', 'administration'));

    unset($options);

    $modules = get_plugin_list('enrol');
    foreach ($modules as $module => $enroldir) {
        $options[$module] = get_string("enrolname", "enrol_$module");
    }
    asort($options);

    admin_externalpage_print_header();

    echo "<form id=\"enrolmenu\" method=\"post\" action=\"enrol_config.php\">";
    echo "<div>";
    echo "<input type=\"hidden\" name=\"sesskey\" value=\"".sesskey()."\" />";
    echo "<input type=\"hidden\" name=\"enrol\" value=\"".$enrol."\" />";

/// Print current enrolment type description
    echo $OUTPUT->box_start();
    echo $OUTPUT->heading($options[$enrol]);

    echo $OUTPUT->box_start('informationbox');
    print_string("description", "enrol_$enrol");
    echo $OUTPUT->box_end();

    echo "<hr />";

    $enrolment->config_form($frm);

    echo "<p class=\"centerpara\"><input type=\"submit\" value=\"".get_string("savechanges")."\" /></p>\n";
    echo $OUTPUT->box_end();
    echo "</div>";
    echo "</form>";

    echo $OUTPUT->footer();

    exit;

