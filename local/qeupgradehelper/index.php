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
 * This plugin can help upgrade site with a large number of question attempts
 * from Moodle 2.0 to 2.1.
 *
 * This screen is the main entry-point to the plugin, it gives the admin a list
 * of options available to them.
 *
 * @package    local
 * @subpackage qeupgradehelper
 * @copyright  2010 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));
admin_externalpage_setup('qeupgradehelper');

$renderer = $PAGE->get_renderer('local_qeupgradehelper');

$actions = array();
if (local_qeupgradehelper_is_upgraded()) {
    $detected = get_string('upgradedsitedetected', 'local_qeupgradehelper');
    $actions[] = local_qeupgradehelper_action::make('listtodo');
    $actions[] = local_qeupgradehelper_action::make('listupgraded');
    $actions[] = local_qeupgradehelper_action::make('extracttestcase');
    $actions[] = local_qeupgradehelper_action::make('cronsetup');

} else {
    $detected = get_string('oldsitedetected', 'local_qeupgradehelper');
    $actions[] = local_qeupgradehelper_action::make('listpreupgrade');
    $actions[] = local_qeupgradehelper_action::make('extracttestcase');
    $actions[] = local_qeupgradehelper_action::make('cronsetup');
}

echo $renderer->index_page($detected, $actions);
