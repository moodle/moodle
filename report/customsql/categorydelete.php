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
 * This will only work if the category has no queries assigned to it.
 *
 * @package report_customsql
 * @copyright 2009 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/adminlib.php');

$id = required_param('id', PARAM_INT);

// Start the page.
admin_externalpage_setup('report_customsql', '', ['id' => $id],
        '/report/customsql/categorydelete.php');
$context = context_system::instance();
require_capability('report/customsql:managecategories', $context);

$category = $DB->get_record('report_customsql_categories', array('id' => $id));
if (!$category) {
    print_error('invalidreportid', 'report_customsql', report_customsql_url('manage.php'), $id);
}

if (optional_param('confirm', false, PARAM_BOOL)) {
    require_sesskey();
    if (!$queries = $DB->get_records('report_customsql_queries', array('categoryid' => $id))) {
        $ok = $DB->delete_records('report_customsql_categories', array('id' => $id));
        if (!$ok) {
            print_error('errordeletingcategory', 'report_customsql', report_customsql_url('index.php'));
        }
        report_customsql_log_delete($id);
    } else {
        print_error('errordeletingcategory', 'report_customsql', report_customsql_url('index.php'));
    }
    redirect(report_customsql_url('manage.php'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('deletecategoryareyousure', 'report_customsql'));
echo html_writer::tag('p', get_string('categorynamex', 'report_customsql', $category->name ));
echo $OUTPUT->confirm(get_string('deletecategoryyesno', 'report_customsql'),
             new single_button(new moodle_url(report_customsql_url('categorydelete.php'),
                     array('id' => $id, 'confirm' => 1, 'sesskey' => sesskey())), get_string('yes')),
                     new single_button(new moodle_url(report_customsql_url('index.php')),
                             get_string('no')));
echo $OUTPUT->footer();
