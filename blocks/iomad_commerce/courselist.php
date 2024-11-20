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

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');

\block_iomad_commerce\helper::require_commerce_enabled();

$delete       = optional_param('delete', 0, PARAM_INT);
$hide         = optional_param('hide', 0, PARAM_INT);
$import       = optional_param('import', 0, PARAM_INT);
$export       = optional_param('export', 0, PARAM_INT);
$confirm      = optional_param('confirm', '', PARAM_ALPHANUM);   // Md5 confirmation hash.
$sort         = optional_param('sort', 'name', PARAM_ALPHA);
$dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
$page         = optional_param('page', 0, PARAM_INT);
$perpage      = optional_param('perpage', $CFG->iomad_max_list_courses, PARAM_INT);        // How many per page.
$default      = optional_param('default', 0, PARAM_BOOL);

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

// Correct the navbar .
// Set the name for the page.
if (!$default) {
    $linktext = get_string('course_list_title', 'block_iomad_commerce');
} else {
    $linktext = get_string('course_list_title_default', 'block_iomad_commerce');
}
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/courselist.php');

// Print the page header.
$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($linktext);

$baseurl = new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/courselist.php', ['sort' => $sort, 'dir' => $dir, 'perpage' => $perpage, 'default' => $default]);
$returnurl = $baseurl;

// Is this the company set of the default set?
if ($default && iomad::has_capability('block/iomad_commerce:manage_default', $companycontext)) {
    $mycompanyid = $companyid;
    $companyid = 0;
}

// Delete a selected product from the shop, after confirmation.
if ($delete and confirm_sesskey()) {

    iomad::require_capability('block/iomad_commerce:delete_course', $companycontext);

    $invoiceableitem = $DB->get_record('course_shopsettings', ['id' => $delete, 'companyid' => $companyid], '*', MUST_EXIST);

    if ($confirm != md5($delete)) {
        echo $OUTPUT->header();
        $name = format_string($invoiceableitem->name);
        echo $OUTPUT->heading(get_string('deletecourse', 'block_iomad_commerce'), 2, 'headingblock header');
        $optionsyes = ['delete' => $delete,
                       'confirm' => md5($delete),
                       'sesskey' => sesskey(),
                       'default' => $default];
        echo $OUTPUT->confirm(get_string('coursedeletecheckfull', 'block_iomad_commerce', "'$name'"),
                              new moodle_url('courselist.php', $optionsyes), 'courselist.php');
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {
        $transaction = $DB->start_delegated_transaction();

        if ($DB->delete_records('course_shopsettings', ['id' => $delete, 'companyid' => $companyid])) {
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

// Import a selected template product to the current company shop.
if ($import and confirm_sesskey()) {

    iomad::require_capability('block/iomad_commerce:delete_course', $companycontext);

    $invoiceableitem = $DB->get_record('course_shopsettings', ['id' => $import, 'companyid' => 0], '*', MUST_EXIST);

    if ($confirm != md5($import)) {
        echo $OUTPUT->header();
        $name = format_string($invoiceableitem->name);
        echo $OUTPUT->heading(get_string('importproduct', 'block_iomad_commerce'), 2, 'headingblock header');
        $optionsyes = ['import' => $import,
                       'confirm' => md5($import),
                       'sesskey' => sesskey(),
                       'default' => $default];
        echo $OUTPUT->confirm(get_string('importproductcheckfull', 'block_iomad_commerce', "'$name'"),
                              new moodle_url('courselist.php', $optionsyes), 'courselist.php');
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {

        if (\block_iomad_commerce\helper::import_item_to_company($import, $mycompanyid)) {
            redirect($returnurl, get_string('productimportedsuccessfully', 'block_iomad_commerce'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            $transaction->rollback();
            echo $OUTPUT->header();
            echo $OUTPUT->notification($returnurl, get_string('productimportfailed', 'block_iomad_commerce'));
            die;
        }
    }
}

// Export a selected product from the current company shop as a template.
if ($export and confirm_sesskey()) {

    iomad::require_capability('block/iomad_commerce:delete_course', $companycontext);

    $invoiceableitem = $DB->get_record('course_shopsettings', ['id' => $export, 'companyid' => $companyid], '*', MUST_EXIST);

    if ($confirm != md5($export)) {
        echo $OUTPUT->header();
        $name = format_string($invoiceableitem->name);
        echo $OUTPUT->heading(get_string('exportproduct', 'block_iomad_commerce'), 2, 'headingblock header');
        $optionsyes = ['export' => $export,
                       'confirm' => md5($export),
                       'sesskey' => sesskey(),
                       'default' => $default];
        echo $OUTPUT->confirm(get_string('exportproductcheckfull', 'block_iomad_commerce', "'$name'"),
                              new moodle_url('courselist.php', $optionsyes), 'courselist.php');
        echo $OUTPUT->footer();
        die;
    } else if (data_submitted()) {

        if (\block_iomad_commerce\helper::import_item_to_company($export, 0)) {
            redirect($returnurl, get_string('productexportedsuccessfully', 'block_iomad_commerce'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            $transaction->rollback();
            echo $OUTPUT->header();
            echo $OUTPUT->notification($returnurl, get_string('productexportfailed', 'block_iomad_commerce'));
            die;
        }
    }
}

if ($hide) {
    if ($courserecord = $DB->get_record('course_shopsettings', ['id' => $hide, 'companyid' => $mycompanyid])) {
        if ($courserecord->enabled == 0 ) {
            $courserecord->enabled = 1;
        } else {
            $courserecord->enabled = 0;
        }
        $DB->update_record('course_shopsettings', $courserecord);
        redirect(new moodle_url($baseurl));
    }
}

echo $OUTPUT->header();

// Has this been setup properly
if (!\block_iomad_commerce\helper::is_commerce_configured()) {
    $link = new moodle_url('/admin/settings.php', array('section' => 'blocksettingiomad_commerce'));
    echo '<div class="alert alert-danger">' . get_string('notconfigured', 'block_iomad_commerce', $link->out()) . '</div>';
    echo $OUTPUT->footer();
    die;
}

//  Check we can actually do anything on this page.
iomad::require_capability('block/iomad_commerce:admin_view', $companycontext);

// Get the number of products.
$objectcount = $DB->count_records('course_shopsettings', ['companyid' => $companyid]);
echo $OUTPUT->paging_bar($objectcount, $page, $perpage, $baseurl);

flush();

if ($courses = $DB->get_recordset_sql('SELECT *
                                       FROM {course_shopsettings} css
                                       WHERE companyid = :companyid
                                       ORDER BY name',
                                       ['companyid' => $companyid], $page * $perpage, $perpage)) {
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
            if (iomad::has_capability('block/iomad_commerce:delete_course', $companycontext)) {
                $deleteurl = new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/courselist.php',
                                            ['delete' => $course_shopsetting->id,
                                             'sesskey' => sesskey(),
                                             'default' => $default]);
                $deletebutton = "<a href='" . $deleteurl ."'>$strdelete</a>";
            } else {
                $deletebutton = "";
            }

            if (iomad::has_capability('block/iomad_commerce:hide_course', $companycontext)) {
                $strdisplay = $strshow;
                if ($course_shopsetting->enabled) {
                    $strdisplay = $strhide;
                }

                $hideurl = new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/courselist.php',
                                          ['hide' => $course_shopsetting->id,
                                           'sesskey' => sesskey(),
                                           'default' => $default]);
                $hidebutton = "<a href='" . $hideurl ."'>$strdisplay</a>";
            } else {
                $hidebutton = "";
            }

            if (iomad::has_capability('block/iomad_commerce:edit_course', $companycontext)) {
                $editurl =  new moodle_url('edit_course_shopsettings_form.php',
                                           ["shopsettingsid" => $course_shopsetting->id,
                                            'default' => $default]);
                $editbutton = "<a href='" . $editurl . "'>$stredit</a>";
            } else {
                $editbutton = "";
            }

            if (iomad::has_capability('block/iomad_commerce:manage_default', $companycontext)) {
                if ($default) {
                    $importurl = new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/courselist.php',
                                              ['import' => $course_shopsetting->id,
                                               'sesskey' => sesskey(),
                                               'default' => $default]);
                    $importbutton = "<a href='" . $importurl ."'>" . get_string('import') . "</a>";
                } else {
                    $importurl = new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/courselist.php',
                                              ['export' => $course_shopsetting->id,
                                               'sesskey' => sesskey(),
                                               'default' => $default]);
                    $importbutton = "<a href='" . $importurl ."'>" . get_string('export', 'grades') . "</a>";
                }
            } else {
                $importbutton = "";
            }

            $table->data[] = array (format_string($course_shopsetting->name),
                                $editbutton,
                                $hidebutton,
                                $deletebutton,
                                $importbutton);
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

echo '<div class="buttons">';
if (iomad::has_capability('block/iomad_commerce:add_course', $companycontext)) {

    echo $OUTPUT->single_button(new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/edit_course_shopsettings_form.php',
                                               ['createnew' => 1,
                                                'default' => $default]),
                                                get_string('addnewcourse', 'block_iomad_commerce'));
}
if (iomad::has_capability('block/iomad_commerce:manage_default', $companycontext)) {
    if ($default) {
        $defaultstring = get_string('managecompanyproducts', 'block_iomad_commerce');
    } else {
        $defaultstring = get_string('managedefaultproducts', 'block_iomad_commerce');
    }
    echo $OUTPUT->single_button(new moodle_url($CFG->wwwroot . '/blocks/iomad_commerce/courselist.php',
                                               ['createnew' => 1,
                                                'default' => !$default]),
                                               $defaultstring);
}

echo $OUTPUT->single_button(new moodle_url($CFG->wwwroot . '/blocks/iomad_company_admin/index.php'), get_string('cancel'));
echo '</div>';

echo $OUTPUT->footer();