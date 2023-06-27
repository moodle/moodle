<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Edwiser Bridge - WordPress and Moodle integration.
 * File displays the edwiser bridge settings.
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */



require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');
// require_once('mod_form.php');
require_once('classes/class-setup-wizard.php');
require_once(dirname(__FILE__) . '/lib.php');

global $CFG, $COURSE, $PAGE;


$setupwizard = new eb_setup_wizard();

// Check progress and redirect accordingly.
$progress  = isset( $CFG->eb_setup_progress ) ? $CFG->eb_setup_progress : '';
if ( ! empty( $progress ) ) {

    $nextstep = $setupwizard->get_next_step( $progress );

    if ( ! isset( $_GET['current_step'] ) /*|| ( isset( $_GET['current_step'] ) && $_GET['current_step'] != $nextstep )*/ ) {
        $redirecturl = $CFG->wwwroot . '/local/edwiserbridge/setup_wizard.php?current_step=' . $nextstep;
        redirect ($redirecturl);
    }
}


// Check if the get parameter have same progress.



$PAGE->requires->jquery();
$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->jquery_plugin('ui-css');

// Restrict normal user to access this page.
admin_externalpage_setup('edwiserbridge_conn_synch_settings');

$stringmanager = get_string_manager();
$strings = $stringmanager->load_component_strings('local_edwiserbridge', 'en');
$PAGE->requires->strings_for_js(array_keys($strings), 'local_edwiserbridge');



// Require Login.
require_login();
$context = context_system::instance();
$baseurl = $CFG->wwwroot . '/local/edwiserbridge/setup_wizard.php';



/*
 * Necessary page requirements.
 */

$PAGE->set_pagelayout("popup");

$PAGE->set_context($context);
$PAGE->set_url('/local/edwiserbridge/edwiserbridge.php?tab=settings');

$PAGE->set_title(get_string('eb-setup-page-title', 'local_edwiserbridge'));


$PAGE->requires->css('/local/edwiserbridge/styles/style.css');
$PAGE->requires->css('/local/edwiserbridge/styles/setup-wizard.css');
$PAGE->requires->js(new moodle_url('/local/edwiserbridge/js/eb_settings.js'));



// Actual page template output starts here. 

// Output page header.
echo $OUTPUT->header();

// Start page container
echo $OUTPUT->container_start();

// This outputs setup wizard template.
// This will use classes/class-setup-wizard.php file.
$setupwizard->eb_setup_wizard_template();


// End page container
echo $OUTPUT->container_end();

// Output footer.
echo $OUTPUT->footer();
