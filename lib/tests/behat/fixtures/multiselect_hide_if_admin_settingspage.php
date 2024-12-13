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
 * Test page for admin setting hide_if functionality dependent on a configmultiselect setting.
 *
 * @package   core
 * @copyright 2024 Lars Bonczek (@innoCampus, TU Berlin)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../config.php');

defined('BEHAT_SITE_RUNNING') || die();

global $CFG, $PAGE, $OUTPUT;
require_once($CFG->libdir . '/adminlib.php');
$PAGE->set_url('/lib/tests/behat/fixtures/multiselect_hide_if_admin_settingspage.php');
require_login();
$PAGE->set_context(core\context\system::instance());

// Set up dummy admin settings page.
$settings = new \admin_settingpage('hide_if_admin_settingspage', 'hide_if Test');

$settings->add(new admin_setting_configmultiselect('multiselect1', 'multiselect1', '', [], [
    1 => 'Option 1',
    2 => 'Option 2',
]));

$settings->add(new admin_setting_configcheckbox('hideIfEq_', "Hide if selection 'eq' []", '', false));
$settings->add(new admin_setting_configcheckbox('hideIfIn_', "Hide if selection 'in' []", '', false));
$settings->add(new admin_setting_configcheckbox('hideIfNeq_', "Hide if selection 'neq' []", '', false));
$settings->add(new admin_setting_configcheckbox('hideIfEq1', "Hide if selection 'eq' ['1']", '', false));
$settings->add(new admin_setting_configcheckbox('hideIfIn1', "Hide if selection 'in' ['1']", '', false));
$settings->add(new admin_setting_configcheckbox('hideIfNeq1', "Hide if selection 'neq' ['1']", '', false));
$settings->add(new admin_setting_configcheckbox('hideIfEq12', "Hide if selection 'eq' ['1', '2']", '', false));
$settings->add(new admin_setting_configcheckbox('hideIfIn12', "Hide if selection 'in' ['1', '2']", '', false));
$settings->add(new admin_setting_configcheckbox('hideIfNeq12', "Hide if selection 'neq' ['1', '2']", '', false));

$settings->hide_if('hideIfEq_', 'multiselect1[]', 'eq', '');
$settings->hide_if('hideIfIn_', 'multiselect1[]', 'in', '');
$settings->hide_if('hideIfNeq_', 'multiselect1[]', 'neq', '');
$settings->hide_if('hideIfEq1', 'multiselect1[]', 'eq', '1');
$settings->hide_if('hideIfIn1', 'multiselect1[]', 'in', '1');
$settings->hide_if('hideIfNeq1', 'multiselect1[]', 'neq', '1');
$settings->hide_if('hideIfEq12', 'multiselect1[]', 'eq', '1|2');
$settings->hide_if('hideIfIn12', 'multiselect1[]', 'in', '1|2');
$settings->hide_if('hideIfNeq12', 'multiselect1[]', 'neq', '1|2');

echo $OUTPUT->header();

$context = [
    'actionurl' => $PAGE->url->out(false),
    'sesskey' => sesskey(),
    'settings' => $settings->output_html(),
    'showsave' => true,
];
echo $OUTPUT->render_from_template('core_admin/settings', $context);

$opts = [
    'dependencies' => $settings->get_dependencies_for_javascript(),
];
$PAGE->requires->js_call_amd('core/showhidesettings', 'init', [$opts]);

echo $OUTPUT->footer();
