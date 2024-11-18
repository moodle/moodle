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

/**
 * Script to let a user create a course for a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(dirname(__FILE__) . '/../iomad_company_admin/lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');

\block_iomad_commerce\helper::require_commerce_enabled();

$returnurl      = optional_param('returnurl', '', PARAM_LOCALURL);
$shopsettingsid = optional_param('shopsettingsid', 0, PARAM_INTEGER);
$new            = optional_param('createnew', 0, PARAM_INTEGER);
$default        = optional_param('default', false, PARAM_BOOL);

require_login();

$systemcontext = context_system::instance();

// Set the companyid
$companyid = iomad::get_my_companyid($systemcontext);
$companycontext = \core\context\company::instance($companyid);
$company = new company($companyid);

$urlparams = [];
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$urlparams['default'] = $default;

$companylist = new moodle_url('/blocks/iomad_commerce/courselist.php', $urlparams);

// Is this the company set of the default set?
if ($default && iomad::has_capability('block/iomad_commerce:manage_default', $companycontext)) {
    $companyid = 0;
    $companycourses = $DB->get_records_sql_menu("SELECT c.id, c.fullname
                                                 FROM {course} c
                                                 JOIN {iomad_courses} ic ON (c.id = ic.courseid)
                                                 ORDER BY c.fullname");    
} else {
    $companycourses = $company->get_menu_courses(true, false);
}

$priceblocks = [];

if (!$new) {
    $isadding = false;
    $shopsettings = $DB->get_record('course_shopsettings', ['id' => $shopsettingsid], '*', MUST_EXIST);
    $courses = $DB->get_records('course_shopsettings_courses', ['itemid' => $shopsettingsid]);

    $shopsettings->itemcourses = [];
    foreach ($courses as $course) {
        $shopsettings->tags = \block_iomad_commerce\helper::get_course_tags($course->courseid);
        $shopsettings->itemcourses[] = $course->courseid;
    }
    
    //  Get any price bandings
    $shopsettings->block_start = [];
    $pricebands = $DB->get_records('course_shopblockprice', ['itemid' => $shopsettingsid], 'price_bracket_start');
    foreach ($pricebands as $priceband) {
        $shopsettings->item_block_start[] = $priceband->price_bracket_start;
        $shopsettings->item_block_price[] = $priceband->price;
    }
    $shopsettings->short_summary_editor = ['text' => $shopsettings->short_description];
    $shopsettings->summary_editor = ['text' => $shopsettings->long_description];
    $shopsettings->default = $default;
     $shopsettings->currency =  $shopsettings->single_purchase_currency;

    iomad::require_capability('block/iomad_commerce:edit_course', $companycontext);
} else {
    $isadding = true;
    $shopsettingsid = 0;
    $shopsettings = (object) ['companyid' => $companyid];
    $course = null;
    $priceblocks = null;
    $shopsettings->default = $default;
    if (!empty($CFG->commerce_admin_currency)) {
        $shopsettings->currency = $CFG->commerce_admin_currency;
    } else {
        $shopsettings->currency = 'GBP';
    }

    iomad::require_capability('block/iomad_commerce:add_course', $companycontext);
}

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('course_list_title', 'block_iomad_commerce');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_commerce/courselist.php');

$title = 'edit_course_shopsettings';
if ($isadding) {
    $title = 'addnewcourse';
}

// Print the page header.
$PAGE->set_context($companycontext);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);
$PAGE->set_heading(get_string($title, 'block_iomad_commerce'));

/* next line copied from /course/edit.php */
$editoroptions = ['maxfiles' => EDITOR_UNLIMITED_FILES,
                  'maxbytes' => $CFG->maxbytes,
                  'trusttext' => false,
                  'noclean' => true];

$mform = new block_iomad_commerce\forms\product_edit_form(new moodle_url('/blocks/iomad_commerce/edit_course_shopsettings_form.php'), $isadding, $shopsettingsid, $companycourses, $priceblocks, $editoroptions);
$mform->set_data($shopsettings);

if ($mform->is_cancelled()) {
    redirect($companylist);

} else if ($data = $mform->get_data()) {
    $data->userid = $USER->id;

    $transaction = $DB->start_delegated_transaction();

    if ($isadding) {
        $data->single_purchase_currency = $data->currency;
        $data->id = $DB->insert_record('course_shopsettings', $data);
    } else {
        $data->single_purchase_currency = $data->currency;
        $DB->update_record('course_shopsettings', $data);
    }

    $DB->delete_records('course_shopblockprice', ['itemid' => $data->id]);

    // Deal with the License price blocks. 
    foreach ($data->item_block_start as $blockid => $itemblock) {
        if (!empty($itemblock)) {
            $priceblock = (object) [];
            $priceblock->itemid = $data->id;
            $priceblock->currency = $data->currency;
            $priceblock->price_bracket_start = $itemblock;
            $priceblock->price = $data->item_block_price[$blockid];
            $priceblock->validlength = $data->single_purchase_validlength;
            $priceblock->shelflife = $data->single_purchase_shelflife;

            $DB->insert_record('course_shopblockprice', $priceblock, false, false);
        }
    }

    // Delete associated courses.
    $DB->delete_records('course_shopsettings_courses', ['itemid' => $data->id]);
    foreach ($data->itemcourses as $itemcourse) {
        $DB->insert_record('course_shopsettings_courses', ['itemid' => $data->id, 'courseid' => $itemcourse]);
    }

    // Delete course_shoptag records.
    $DB->delete_records('course_shoptag', ['itemid' => $data->id]);

    // Find shoptag ids.
    $tags = preg_split('/\s*,\s*/', $data->tags);
    $newcourseshoptagrecord = (object) [];
    $newcourseshoptagrecord->itemid = $data->id;
    foreach ($tags as $tag) {
        if (!$st = $DB->get_record('shoptag', ['tag' => $tag])) {
            $st = (object) [];
            $st->id = $DB->insert_record('shoptag', (object) ['tag' => $tag], true);
        }

        $newcourseshoptagrecord->shoptagid = $st->id;
        $DB->insert_record('course_shoptag', $newcourseshoptagrecord);
    }

    $transaction->allow_commit();

    redirect($companylist, get_string('itemaddedsuccessfully', 'block_iomad_commerce'), null, \core\output\notification::NOTIFY_SUCCESS);

} else {

    echo $OUTPUT->header();

    $mform->display();

    echo $OUTPUT->footer();
}