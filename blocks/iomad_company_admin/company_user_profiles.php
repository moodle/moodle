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

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once('profiledefinelib.php');
require_once('lib.php');

$action   = optional_param('action', '', PARAM_ALPHA);
$companyid = optional_param('companyid', 0, PARAM_INTEGER);

$redirect = new moodle_url($CFG->wwwroot.'/blocks/iomad_company_admin/company_user_profiles.php');

$strchangessaved    = get_string('changessaved');
$strcancelled       = get_string('cancelled');
$strdefaultcategory = get_string('profiledefaultcategory', 'admin');
$strnofields        = get_string('profilenofieldsdefined', 'admin');
$strcreatefield     = get_string('profilecreatefield', 'admin');

$context = context_system::instance();
require_login();

// Correct the navbar.
// Set the name for the page.
$linktext = get_string('companyprofilefields', 'block_iomad_company_admin');
// Set the url.
$linkurl = new moodle_url('/blocks/iomad_company_admin/company_user_profiles.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// Set the page heading.
$PAGE->set_heading($linktext);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Do we have any actions to perform before printing the header?

switch ($action) {
    case 'movefield':
        $id  = required_param('id', PARAM_INT);
        $dir = required_param('dir', PARAM_ALPHA);

        if (confirm_sesskey()) {
            profile_move_field($id, $dir);
        }
        redirect($redirect, get_string('eventuserinfofieldupdated'), null, \core\output\notification::NOTIFY_SUCCESS);
        break;
    case 'deletefield':
        $id      = required_param('id', PARAM_INT);
        $confirm = optional_param('confirm', 0, PARAM_BOOL);

        $datacount = $DB->count_records('user_info_data', array('fieldid' => $id));
        if (data_submitted() and ($confirm and confirm_sesskey()) or $datacount === 0) {
            profile_delete_field($id);
            redirect($redirect, get_string('eventuserinfofielddeleted'), null, \core\output\notification::NOTIFY_SUCCESS);
        }

        // Ask for confirmation.
        $optionsyes = array ('id' => $id, 'confirm' => 1, 'action' => 'deletefield', 'sesskey' => sesskey());
        $strheading = get_string('profiledeletefield', 'admin');
        $PAGE->navbar->add($strheading);
        echo $OUTPUT->header();
        echo $OUTPUT->heading($strheading);
        $formcontinue = new single_button(new moodle_url($redirect, $optionsyes), get_string('yes'), 'post');
        $formcancel = new single_button(new moodle_url($redirect), get_string('no'), 'get');
        echo $OUTPUT->confirm(get_string('profileconfirmfielddeletion', 'admin', $datacount), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        die;
        break;
    case 'editfield':
        $id       = optional_param('id', 0, PARAM_INT);
        $datatype = optional_param('datatype', '', PARAM_ALPHA);

        profile_edit_field($id, $datatype, $redirect, $companyid);
        die;
        break;
    default:
        // Normal form.
}

$urlparams = array('companyid' => $companyid, 'action' => $action);
if (!empty($returnurl)) {
    $urlparams['returnurl'] = $returnurl;
}


require_login(null, false); // Adds to $PAGE, creates $OUTPUT.
$context = $PAGE->context;

echo $OUTPUT->header();

iomad::require_capability('block/iomad_company_admin:company_user_profiles', $context);

// Check that we have at least one category defined.
if ($DB->count_records('user_info_category') == 0) {
    $defaultcategory = new stdClass();
    $defaultcategory->name = $strdefaultcategory;
    $defaultcategory->sortorder = 1;
    $DB->insert_record('user_info_category', $defaultcategory);
    redirect($redirect);
}

// Check if we have a company ID, if so just pull that one back.
if (!empty($companyid)) {
    $company = $DB->get_record('company', array('id' => $companyid), '*', MUST_EXIST);

    // Get the company category.
    $categories = array();
    $profileinfo = new stdclass();
    $profileinfo->profileid = $company->profileid;
    $categories[$company->profileid] = $profileinfo;
} else {
    // Check if can view every company profile.
    if (!iomad::has_capability('block/iomad_company_admin:allcompany_user_profiles', $context)) {
        // Get the company from the users profile.
        $categories = $DB->get_records('company', array('id' => $companyid), 'sortorder ASC', 'profileid');
    } else {
        // Get all the companies/categories.
        $categories = $DB->get_records_sql("SELECT id AS profileid FROM {user_info_category}");
    }
}

foreach ($categories as $category) {
    $table = new html_table();
    $table->head  = array(get_string('profilefield', 'admin'), get_string('edit'));
    $table->align = array('left', 'right');
    $table->width = '95%';
    $table->attributes['class'] = 'generaltable profilefield';
    $table->data = array();

    if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->profileid), 'sortorder ASC')) {
        foreach ($fields as $field) {
            $table->data[] = array(format_string($field->name), profile_field_icons($field));
        }
    }

    // Get the category name.
    $categoryinfo = $DB->get_record('user_info_category', array('id' => $category->profileid));

    echo $OUTPUT->heading(format_string($categoryinfo->name));
    if (count($table->data)) {
        echo html_writer::table($table);
    } else {
        echo $OUTPUT->notification($strnofields);
    }

} // End of $categories foreach.

echo '<hr />';
echo '<div class="profileeditor">';

// Create a new field link.
$options = profile_list_datatypes();
$popupurl = new moodle_url('/blocks/iomad_company_admin/company_user_profiles.php?id=0&action=editfield');
if (!empty($companyid)) {
    // Need to add the company ID tag to the edit URL.
    $popupurl = $popupurl . '&companyid='.$companyid;
}
echo $OUTPUT->single_select($popupurl, 'datatype', $options, '', array('' => $strcreatefield), 'newfieldform');

// Add a div with a class so themers can hide, style or reposition the text.
html_writer::start_tag('div', array('class' => 'adminuseractionhint'));
html_writer::end_tag('div');

echo '</div>';

echo $OUTPUT->footer();
die;


/***** Some functions relevant to this script *****/

/**
 * Create a string containing the editing icons for the user profile categories
 * @param   object   the category object
 * @return  string   the icon string
 */
function profile_category_icons($category) {
    global $CFG, $USER, $DB, $OUTPUT;

    $strdelete   = get_string('delete');
    $strmoveup   = get_string('moveup');
    $strmovedown = get_string('movedown');
    $stredit     = get_string('edit');

    $categorycount = $DB->count_records('user_info_category');
    $fieldcount    = $DB->count_records('user_info_field', array('categoryid' => $category->id));

    // Edit!
    $editstr = '<a title="'.$stredit.'" href="company_user_profiles.php?id='.$category->id.
               '&amp;action=editcategory"><img src="'.$OUTPUT->image_url('t/edit') .
               '" alt="'.$stredit.'" class="iconsmall" /></a> ';

    // Delete!
    // Can only delete the last category if there are no fields in it.
    if (($categorycount > 1) or ($fieldcount == 0)) {
        $editstr .= '<a title="'.$strdelete.'" href="company_user_profiles.php?id='.$category->id.'&amp;action=deletecategory';
        $editstr .= '"><img src="'.$OUTPUT->image_url('t/delete') . '" alt="'.$strdelete.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$OUTPUT->image_url('spacer') . '" alt="" class="iconsmall" /> ';
    }

    // Move up!
    if ($category->sortorder > 1) {
        $editstr .= '<a title="'.$strmoveup.'" href="company_user_profiles.php?id='.$category->id.
                    '&amp;action=movecategory&amp;dir=up&amp;sesskey='.sesskey().'"><img src="'
                    .$OUTPUT->image_url('t/up') . '" alt="'.$strmoveup.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$OUTPUT->image_url('spacer') . '" alt="" class="iconsmall" /> ';
    }

    // Move down!
    if ($category->sortorder < $categorycount) {
        $editstr .= '<a title="'.$strmovedown.'" href="company_user_profiles.php?id='.$category->id.
                    '&amp;action=movecategory&amp;dir=down&amp;sesskey='.sesskey().'"><img src="'
                    .$OUTPUT->image_url('t/down') . '" alt="'.$strmovedown.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$OUTPUT->image_url('spacer') . '" alt="" class="iconsmall" /> ';
    }

    return $editstr;
}

/**
 * Create a string containing the editing icons for the user profile fields
 * @param   object   the field object
 * @return  string   the icon string
 */
function profile_field_icons($field) {
    global $CFG, $USER, $DB, $OUTPUT;

    $strdelete   = get_string('delete');
    $strmoveup   = get_string('moveup');
    $strmovedown = get_string('movedown');
    $stredit     = get_string('edit');

    $fieldcount = $DB->count_records('user_info_field', array('categoryid' => $field->categoryid));
    $datacount  = $DB->count_records('user_info_data', array('fieldid' => $field->id));

    // Edit!
    $editstr = '<a title="'.$stredit.'" href="company_user_profiles.php?id='.$field->id.
               '&amp;action=editfield"><img src="'.$OUTPUT->image_url('t/edit') .
               '" alt="'.$stredit.'" class="iconsmall" /></a> ';

    // Delete!
    $editstr .= '<a title="'.$strdelete.'" href="company_user_profiles.php?id='.$field->id.'&amp;action=deletefield';
    $editstr .= '"><img src="'.$OUTPUT->image_url('t/delete') . '" alt="'.$strdelete.'" class="iconsmall" /></a> ';

    // Move up!
    if ($field->sortorder > 1) {
        $editstr .= '<a title="'.$strmoveup.'" href="company_user_profiles.php?id='.
                    $field->id.'&amp;action=movefield&amp;dir=up&amp;sesskey='.sesskey().
                    '"><img src="'.$OUTPUT->image_url('t/up') . '" alt="'.$strmoveup.
                    '" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$OUTPUT->image_url('spacer') . '" alt="" class="iconsmall" /> ';
    }

    // Move down!
    if ($field->sortorder < $fieldcount) {
        $editstr .= '<a title="'.$strmovedown.'" href="company_user_profiles.php?id='.
                    $field->id.'&amp;action=movefield&amp;dir=down&amp;sesskey='.sesskey().
                    '"><img src="'.$OUTPUT->image_url('t/down') .
                    '" alt="'.$strmovedown.'" class="iconsmall" /></a> ';
    } else {
        $editstr .= '<img src="'.$OUTPUT->image_url('spacer') . '" alt="" class="iconsmall" /> ';
    }

    return $editstr;
}

