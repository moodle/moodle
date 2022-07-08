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

global $DB, $email;

$block = 'block_iomad_company_admin';

// Get the SYSTEM context.
$context = context_system::instance();

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('classrooms', $block);
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/classroom_list.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the companyid
$companyid = iomad::get_my_companyid($context);
$company = new company($companyid);

// Set the page heading.
$PAGE->set_heading(get_string('classrooms_for', $block, $company->get_name()));

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort,
                                                    'dir' => $dir,
                                                    'perpage' => $perpage));
$returnurl = $baseurl;


if ($delete and confirm_sesskey()) {
    // Delete a selected override template, after confirmation.

    iomad::require_capability('block/iomad_company_admin:classrooms_delete', $context);

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
echo $OUTPUT->header();

// Check we can actually do anything on this page.
iomad::require_capability('block/iomad_company_admin:classrooms', $context);

// Get the number of templates.
$objectcount = $DB->count_records('classroom', array('companyid' => $companyid));
echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);

flush();

if ($classrooms = $DB->get_records('classroom', array('companyid' => $companyid),
                                     'name', '*', $page, $perpage)) {
    $stredit   = get_string('edit');
    $strdelete = get_string('delete');

    $table = new html_table();
    $table->head = array ("Name", "Capacity",  "");
    $table->align = array ("left", "left", "center");
    $table->width = "95%";
    $sesskey = sesskey();

    foreach ($classrooms as $classroom) {
        if (iomad::has_capability('block/iomad_company_admin:classrooms_delete', $context)) {
            $deleteurl = new moodle_url($CFG->wwwroot . '/blocks/iomad_company_admin/classroom_list.php',
                                        ['delete' => $classroom->id,
                                        'sesskeyy' => $sesskey]);
            $deletebutton = "<a href='" . $deleteurl . "'><i class='icon fa fa-trash fa-fw' title='" . get_string('delete') . "' role='img' aria-label='" . get_string('delete') . "'></i></a>";
        } else {
            $deletebutton = "";
        }

        if (iomad::has_capability('block/iomad_company_admin:classrooms_edit', $context)) {
            $editurl = new moodle_url($CFG->wwwroot . '/blocks/iomad_company_admin/classroom_edit_form.php',
                                      ['id' => $classroom->id]);
            $editbutton = "<a href='" . $editurl . "'><i class='icon fa fa-cog fa-fw' title='" . get_string('edit') . "' role='img' aria-label='" . get_string('edit') . "'></i></a>";
        } else {
            $editbutton = "";
        }

        // Is it virtual?
        if (!empty($classroom->isvirtual)) {
            $classroom->capacity = get_string('virtual', 'block_iomad_company_admin');
        }
        $table->data[] = array ($classroom->name,
                            $classroom->capacity,
                            $editbutton . "&nbsp" . $deletebutton);
    }

    if (!empty($table)) {
        echo html_writer::table($table);
        echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);
    }
} else {
    echo '<div class="alert alert-warning">' . get_string('nolocations', 'block_iomad_company_admin') . '</div>';
}

if (iomad::has_capability('block/iomad_company_admin:classrooms_add', $context)) {
    echo "<a class=\"btn btn-success\" href=\"classroom_edit_form.php\">" . get_string('classrooms_add', $block) . "</a>&nbsp";
}

// exit button
$link = new moodle_url('/my');
echo '<a class="btn btn-primary" href="' . $link . '">' . get_string('todashboard', 'block_iomad_company_admin') . '</a>';

echo $OUTPUT->footer();
