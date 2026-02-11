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
 * Responsible for displaying the content upgrade page
 *
 * @package    mod_hvp
 * @copyright  2020 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use mod_hvp\content_hub_service;

require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');
require_once("locallib.php");

global $PAGE, $SITE, $OUTPUT, $CFG;

// No guest autologin.
require_login(0, false);

$context = \context_system::instance();
require_capability('mod/hvp:contenthubregistration', $context);

$pageurl = new moodle_url('/mod/hvp/content_hub_registration.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_url($pageurl);
$PAGE->set_title("{$SITE->shortname}: " . get_string('upgrade', 'hvp'));
$PAGE->set_heading(get_string('contenthub:settings:heading', 'mod_hvp'));

$settings = content_hub_service::get_registration_ui_settings();
$PAGE->requires->data_for_js('H5PSettings', $settings, true);
$PAGE->requires->js(new moodle_url('library/js/h5p-hub-registration.js'), true);
$PAGE->requires->css(new moodle_url('library/styles/h5p.css'));
$PAGE->requires->css(new moodle_url('library/styles/h5p-hub-registration.css'));

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('mod_hvp/content_hub_registration', []);
$PAGE->requires->js_call_amd('mod_hvp/contenthubregistration', 'init');

echo $OUTPUT->footer();