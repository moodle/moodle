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
 * @package   block_iomad_company_admin
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once( '../../config.php');
require_once( 'lib.php');
require_once($CFG->dirroot . '/local/iomad/lib/user.php');

$delete       = optional_param('delete', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_classrooms, PARAM_INT);        // How many per page.

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

// Check we can actually do anything on this page.
iomad::require_capability('block/iomad_company_admin:classrooms', $companycontext);

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('classrooms', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/classroom_list.php');

// Print the page header.
$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading(get_string('classrooms_for', 'block_iomad_company_admin', $company->get_name()));
$PAGE->navbar->add($linktext, $linkurl);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort,
                                                    'dir' => $dir,
                                                    'perpage' => $perpage));
$returnurl = $baseurl;

if ($delete and confirm_sesskey()) {
    // Delete a selected override template, after confirmation.

    iomad::require_capability('block/iomad_company_admin:classrooms_delete', $companycontext);

    $classroom = $DB->get_record('classroom', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        $name = $classroom->name;
        echo $OUTPUT->heading(get_string('classroom_delete', $block), 2, 'headingblock header');
        $optionsyes = array('delete' => $delete, 'confirm' => md5 ($delete), 'sesskey ' => sesskey());
        echo $OUTPUT->confirm(get_string('classroom_delete_checkfull', $block, "'$name'"),
                               new moodle_url('classroom_list.php', $optionsyes),
                               'classroom_list.php');
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $transaction = $DB->start_delegated_transaction();

        if ( $DB->delete_records('classroom', array('id' => $delete)) ) {
            $transaction->allow_commit();
            redirect($returnurl, get_string('classroomdeletedok', 'block_iomad_company_admin'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            $transaction->rollback();
            echo $OUTPUT->header();
            echo $OUTPUT->notification($returnurl, get_string('deletednot', '', $classroom->name));
            die;
        }

        $transaction->rollback();
    }

}
// Set up the page buttons.
$buttons = "";
if (iomad::has_capability('block/iomad_company_admin:classrooms_add', $companycontext)) {
    $linkurl = new moodle_url('/blocks/iomad_company_admin/classroom_edit_form.php');
    $buttons = $OUTPUT->single_button($linkurl, get_string('classrooms_add', 'block_iomad_company_admin'), 'get');
}

$PAGE->set_button($buttons);

// Set up the table
$table = new block_iomad_company_admin\tables\teaching_locations_table('teaching_locations_table');

$tableheaders = [get_string('name'),
                 get_string('classroom_capacity', 'block_iomad_company_admin'),
                 get_string('address')];

$tablecolumns = ['name',
                 'capacity',
                 'address'];

// Are we adding the actions buttons?
if (iomad::has_capability('block/iomad_company_admin:classrooms_delete', $companycontext) ||
    iomad::has_capability('block/iomad_company_admin:classrooms_edit', $companycontext)) {
    $tableheaders[] = "";
    $tablecolumns[] = 'actions';
}

$table->set_sql("*", "{classroom}", "companyid = :companyid", ['companyid' => $companyid]);
$table->define_baseurl($baseurl);
$table->define_columns($tablecolumns);
$table->define_headers($tableheaders);
$table->sort_default_column = 'name DESC';
$table->no_sorting('actions');
$table->no_sorting('address');

if (iomad::has_capability('block/iomad_company_admin:classrooms_add', $companycontext)) {
    $buttonlink = new moodle_url($CFG->wwwroot . "/blocks/iomad_company_admin/classroom_edit_form.php");
    $buttoncaption =  get_string('classrooms_add', 'block_iomad_company_admin');
    $PAGE->set_button($OUTPUT->single_button($buttonlink, $buttoncaption, 'get'));
}

echo $OUTPUT->header();

$table->out(30, true);

echo $OUTPUT->footer();
