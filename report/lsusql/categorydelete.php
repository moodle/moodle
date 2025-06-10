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
 * Script to delete a particular custom SQL category, with confirmation.
 *
 * This will only work if the category has no queries assigned to it.
 *
 * @package report_lsusql
 * @copyright 2009 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

$id = required_param('id', PARAM_INT);

// Start the page.
admin_externalpage_setup('report_lsusql', '', ['id' => $id],
        '/report/lsusql/categorydelete.php');
$context = context_system::instance();
require_capability('report/lsusql:managecategories', $context);

$category = $DB->get_record('report_lsusql_categories', array('id' => $id));
if (!$category) {
    throw new moodle_exception('invalidreportid', 'report_lsusql', report_lsusql_url('manage.php'), $id);
}

if (optional_param('confirm', false, PARAM_BOOL)) {
    require_sesskey();
    if (!$queries = $DB->get_records('report_lsusql_queries', array('categoryid' => $id))) {
        $ok = $DB->delete_records('report_lsusql_categories', array('id' => $id));
        if (!$ok) {
            throw new moodle_exception('errordeletingcategory', 'report_lsusql', report_lsusql_url('index.php'));
        }
        report_lsusql_log_delete($id);
    } else {
        throw new moodle_exception('errordeletingcategory', 'report_lsusql', report_lsusql_url('index.php'));
    }
    redirect(report_lsusql_url('manage.php'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('deletecategoryareyousure', 'report_lsusql'));
echo html_writer::tag('p', get_string('categorynamex', 'report_lsusql', $category->name ));
echo $OUTPUT->confirm(get_string('deletecategoryyesno', 'report_lsusql'),
             new single_button(report_lsusql_url('categorydelete.php',
                     ['id' => $id, 'confirm' => 1, 'sesskey' => sesskey()]), get_string('yes')),
                     new single_button(report_lsusql_url('index.php'), get_string('no')));
echo $OUTPUT->footer();
