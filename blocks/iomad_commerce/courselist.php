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
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once('lib.php');

require_commerce_enabled();

$delete       = optional_param('delete', 0, PARAM_INT);
$hide         = optional_param('hide', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_courses, PARAM_INT);        // How many per page.

global $DB;

require_login(null, false); // Adds to $PAGE, creates $OUTPUT.

$context = context_system::instance();

// Correct the navbar .
// Set the name for the page.
$linktext = get_string('course_list_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/courselist.php');

// Print the page header.
$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($linktext);

$baseurl = new moodle_url(basename(__FILE__), array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
$returnurl = $baseurl;

if ($delete and confirm_sesskey()) {              // Delete a selected course from the shop, after confirmation.

    iomad::require_capability('block/iomad_commerce:delete_course', $context);

    $invoiceableitem = $DB->get_record('course_shopsettings', array('id' => $delete), '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        $course = $DB->get_record('course', array('id' => $invoiceableitem->courseid), 'fullname', MUST_EXIST);
        $name = $course->fullname;
        echo $OUTPUT->heading(get_string('deletecourse', 'block_iomad_commerce'), 2, 'headingblock header');
        $optionsyes = array('delete' => $delete, 'confirm' => md5($delete), 'sesskey' => sesskey());
        echo $OUTPUT->confirm(get_string('coursedeletecheckfull', 'block_iomad_commerce', "'$name'"),
                              new moodle_url('courselist.php', $optionsyes), 'courselist.php');
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $transaction = $DB->start_delegated_transaction();

        if ($DB->delete_records('course_shopsettings', array('id' => $delete))) {
            $transaction->allow_commit();
            redirect($returnurl);
        } else {
            $transaction->rollback();
            echo $OUTPUT->header();
            echo $OUTPUT->notification($returnurl, get_string('deletednot', '', $invoiceableitem->name));
            die;
        }

        $transaction->rollback();
    }
}

if ($hide) {
    $courserecord = $DB->get_record('course_shopsettings', array('id' => $hide));
    if ($courserecord->enabled == 0 ) {
        $courserecord->enabled = 1;
    } else {
        $courserecord->enabled = 0;
    }
    $DB->update_record('course_shopsettings', $courserecord);
    redirect(new moodle_url($baseurl));
}

echo $OUTPUT->header();

// Has this been setup properly
if (!is_commerce_configured()) {
    $link = new moodle_url('/admin/settings.php', array('section' => 'blocksettingiomad_commerce'));
    echo '<div class="alert alert-danger">' . get_string('notconfigured', 'block_iomad_commerce', $link->out()) . '</div>';
    echo $OUTPUT->footer();
    die;
}

//  Check we can actually do anything on this page.
iomad::require_capability('block/iomad_commerce:admin_view', $context);

// Get the number of companies.
$objectcount = $DB->count_records('course_shopsettings');
echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);

flush();

if ($courses = $DB->get_recordset_sql('SELECT css.*, c.fullname
                                       FROM {course_shopsettings} css
                                            INNER JOIN {course} c ON c.id = css.courseid
                                       ORDER BY c.fullname', null, $page * $perpage, $perpage)) {
    if (!empty($courses)) {
        $stredit   = get_string('edit');
        $strdelete = get_string('delete');
        $strhide = get_string('hide', 'block_iomad_commerce');
        $strshow = get_string('show', 'block_iomad_commerce');

        $table = new html_table();
        $table->head = array (get_string('name'), "", "", "");
        $table->align = array ("left", "center", "center", "center");
        $table->width = "600px";

        foreach ($courses as $course_shopsetting) {
            if (iomad::has_capability('block/iomad_commerce:delete_course', $context)) {
                $deletebutton = "<a href=\"courselist.php?delete=$course_shopsetting->id&amp;sesskey=".
                                 sesskey()."\">$strdelete</a>";
            } else {
                $deletebutton = "";
            }

            if (iomad::has_capability('block/iomad_commerce:hide_course', $context)) {
                $strdisplay = $strshow;
                if ($course_shopsetting->enabled) {
                    $strdisplay = $strhide;
                }

                $hidebutton = "<a href=\"courselist.php?hide=$course_shopsetting->id&amp;sesskey=".sesskey()."\">$strdisplay</a>";
            } else {
                $hidebutton = "";
            }

            if (iomad::has_capability('block/iomad_commerce:edit_course', $context)) {
                $editbutton = "<a href='" . new moodle_url('edit_course_shopsettings_form.php',
                                                            array("shopsettingsid" => $course_shopsetting->id)) .
                              "'>$stredit</a>";
            } else {
                $editbutton = "";
            }

            $table->data[] = array ("$course_shopsetting->fullname",
                                $editbutton,
                                $hidebutton,
                                $deletebutton);
        }

        if (!empty($table)) {
            echo html_writer::table($table);
            echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);
        }

    } else {
        echo "<p>" . get_string('nocoursesontheshop', 'block_iomad_commerce') . "</p>";
    }

    $courses->close();
}

if (iomad::has_capability('block/iomad_commerce:add_course', $context)) {
    echo '<div class="buttons">';

    echo $OUTPUT->single_button(new moodle_url('edit_course_shopsettings_form.php?createnew=1'),
                                                get_string('addnewcourse', 'block_iomad_commerce'), 'get');
    echo $OUTPUT->single_button(new moodle_url('/blocks/iomad_company_admin/index.php'), get_string('cancel'), 'get');

    echo '</div>';
}

echo $OUTPUT->footer();
