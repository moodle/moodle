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
 * Manage user profile fields.
 * @package core_user
 * @copyright  2007 onwards Shane Elliot {@link http://pukunui.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');
require_once($CFG->dirroot.'/user/profile/definelib.php');

admin_externalpage_setup('profilefields');

$action   = optional_param('action', '', PARAM_ALPHA);

$redirect = $CFG->wwwroot.'/user/profile/index.php';

$strdefaultcategory = get_string('profiledefaultcategory', 'admin');
$strcreatefield     = get_string('profilecreatefield', 'admin');


// Do we have any actions to perform before printing the header.

switch ($action) {
    case 'movecategory':
        $id  = required_param('id', PARAM_INT);
        $dir = required_param('dir', PARAM_ALPHA);

        if (confirm_sesskey()) {
            profile_move_category($id, $dir);
        }
        redirect($redirect);
        break;
    case 'movefield':
        $id  = required_param('id', PARAM_INT);
        $dir = required_param('dir', PARAM_ALPHA);

        if (confirm_sesskey()) {
            profile_move_field($id, $dir);
        }
        redirect($redirect);
        break;
    case 'deletecategory':
        $id      = required_param('id', PARAM_INT);
        if (confirm_sesskey()) {
            profile_delete_category($id);
        }
        redirect($redirect, get_string('deleted'));
        break;
    case 'deletefield':
        $id      = required_param('id', PARAM_INT);
        $confirm = optional_param('confirm', 0, PARAM_BOOL);

        // If no userdata for profile than don't show confirmation.
        $datacount = $DB->count_records('user_info_data', array('fieldid' => $id));
        if (((data_submitted() and $confirm) or ($datacount === 0)) and confirm_sesskey()) {
            profile_delete_field($id);
            redirect($redirect, get_string('deleted'));
        }

        // Ask for confirmation, as there is user data available for field.
        $fieldname = $DB->get_field('user_info_field', 'name', array('id' => $id));
        $optionsyes = array ('id' => $id, 'confirm' => 1, 'action' => 'deletefield', 'sesskey' => sesskey());
        $strheading = get_string('profiledeletefield', 'admin', format_string($fieldname));
        $PAGE->navbar->add($strheading);
        echo $OUTPUT->header();
        echo $OUTPUT->heading($strheading);
        $formcontinue = new single_button(new moodle_url($redirect, $optionsyes), get_string('yes'), 'post');
        $formcancel = new single_button(new moodle_url($redirect), get_string('no'), 'get');
        echo $OUTPUT->confirm(get_string('profileconfirmfielddeletion', 'admin', $datacount), $formcontinue, $formcancel);
        echo $OUTPUT->footer();
        die;
        break;
    default:
        // Normal form.
}

// Show all categories.
$categories = $DB->get_records('user_info_category', null, 'sortorder ASC');

// Check that we have at least one category defined.
if (empty($categories)) {
    $defaultcategory = new stdClass();
    $defaultcategory->name = $strdefaultcategory;
    $defaultcategory->sortorder = 1;
    $DB->insert_record('user_info_category', $defaultcategory);
    redirect($redirect);
}

// Print the header.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('profilefields', 'admin'));

$outputcategories = [];
$options = profile_list_datatypes();

foreach ($categories as $category) {
    // Category fields.
    $outputfields = [];
    if ($fields = $DB->get_records('user_info_field', array('categoryid' => $category->id), 'sortorder ASC')) {
        foreach ($fields as $field) {
            $fieldname = format_string($field->name);
            $component = 'profilefield_' . $field->datatype;
            $classname = "\\$component\\helper";
            if (class_exists($classname) && method_exists($classname, 'get_fieldname')) {
                $fieldname = $classname::get_fieldname($field->name);
            }
            $outputfields[] = [
                'id' => $field->id,
                'shortname' => $field->shortname,
                'datatype' => $field->datatype,
                'name' => $fieldname,
                'isfirst' => !count($outputfields),
                'islast' => count($outputfields) == count($fields) - 1,
            ];
        }
    }

    // Add new field menu.
    $menu = new \action_menu();
    $menu->set_alignment(\action_menu::BL, \action_menu::BL);
    $menu->set_menu_trigger($strcreatefield);
    foreach ($options as $type => $fieldname) {
        $action = new \action_menu_link_secondary(new \moodle_url('#'), null, $fieldname,
            ['data-action' => 'createfield', 'data-categoryid' => $category->id, 'data-datatype' => $type,
                'data-datatypename' => $fieldname]);
        $menu->add($action);
    }
    $menu->attributes['class'] .= ' float-left mr-1';

    // Add category information to the template.
    $outputcategories[] = [
        'id' => $category->id,
        'name' => format_string($category->name),
        'fields' => $outputfields,
        'hasfields' => count($outputfields),
        'isfirst' => !count($outputcategories),
        'islast' => count($outputcategories) == count($categories) - 1,
        'candelete' => count($categories) > 1,
        'addfieldmenu' => $menu->export_for_template($OUTPUT),
    ];
}

echo $OUTPUT->render_from_template('core_user/edit_profile_fields', [
    'categories' => $outputcategories,
    'sesskey' => sesskey(),
    'baseurl' => (new moodle_url('/user/profile/index.php'))->out(false)
]);

echo $OUTPUT->footer();
