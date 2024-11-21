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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2018 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
require_once($CFG->dirroot . '/local/intelliboard/output/tables/reports_table.php');

require_login();
admin_externalpage_setup('intelliboardsql');

if (!is_siteadmin()) {
    throw new moodle_exception('invalidaccess', 'error');
}
if (isset($CFG->intelliboardsql) and $CFG->intelliboardsql == false) {
    throw new moodle_exception('invalidaccess', 'error');
}

$intelliboard = intelliboard(['task'=>'sqlreports']);

$table = new reports_table('reports_table');
$table->show_download_buttons_at(array());
$table->is_downloading(false);
$table->is_collapsible = false;

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('sqlreports', 'local_intelliboard'));

$table->out(20, true);

echo $OUTPUT->footer();
