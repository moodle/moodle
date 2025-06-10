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
 * Custom SQL reporting categories.
 *
 * Users with the report/lsusql:managecategories capability can enter custom
 *
 * This page shows the list of categories, with edit icons, and an add new button
 * if you have the report/lsusql:managecategories capability.
 *
 * @package report_lsusql
 * @copyright 2013 The Open University
 * @copyright 2022 Louisiana State University
 * @copyright 2022 Robert Russo
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/categoryadd_form.php');
require_once($CFG->libdir . '/adminlib.php');

$context = context_system::instance();
admin_externalpage_setup('report_lsusql', '', null, '/report/lsusql/addcategory.php');
require_capability('report/lsusql:managecategories', $context);

$relativeurl = 'addcategory.php';

// Are we editing an existing report, or creating a new one.
$id = optional_param('id', 0, PARAM_INT);

$queryparams = array();

if ($id) {
    $queryparams['categoryid'] = $id;
    $isadding = false;
    // Editing an existing category.
    $category = $DB->get_record('report_lsusql_categories',
            array('id' => $id), '*', MUST_EXIST);
} else {
    $queryparams['categoryid'] = null;
    $isadding = true;
}

$mform = new report_lsusql_addcategory_form(report_lsusql_url($relativeurl), $queryparams);

if ($mform->is_cancelled()) {
    redirect(report_lsusql_url('manage.php'));
}

if ($data = $mform->get_data()) {
    if ($isadding) {
        $DB->insert_record('report_lsusql_categories', $data);
    } else {
        $updrec = new stdClass();
        $updrec->id = $data->id;
        $updrec->name = $data->name;
        $DB->update_record('report_lsusql_categories', $updrec);
    }
    redirect(report_lsusql_url('manage.php'));
}

if ($id) {
    $headstr = get_string('editcategory', 'report_lsusql');
} else {
    $headstr = get_string('addcategory', 'report_lsusql');
}

echo $OUTPUT->header() . $OUTPUT->heading($headstr);

if ($id) {
    $mform->set_data($category);
}

$mform->display();

echo $OUTPUT->footer();
