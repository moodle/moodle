<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     local_aiquestions
 * @category    admin
 * @copyright   2023 Ruthy Salomon <ruthy.salomon@gmail.com> , Yedidia Klein <yedidia@openapp.co.il>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');
defined('MOODLE_INTERNAL') || die();
require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_heading(get_string('pluginname', 'local_aiquestions'));
$PAGE->set_title(get_string('pluginname', 'local_aiquestions'));
$PAGE->set_url('/local/aiquestions/');
$PAGE->set_pagelayout('standard');
$PAGE->navbar->add(get_string('pluginname', 'local_aiquestions'), new moodle_url('/local/aiquestions/'));


echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_aiquestions/index', []);
echo $OUTPUT->footer();
