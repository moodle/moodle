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
 * @package   block_iomad_microlearning
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Script to let a user create a group for a particular company.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$groupid = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_microlearning:manage_groups', $context);

$grouplist = new moodle_url('/blocks/iomad_microlearning/groups.php');

if (empty($groupid)) {
    $linktext = get_string('creategroup', 'block_iomad_microlearning');
} else {
    $linktext = get_string('editgroup', 'block_iomad_microlearning');
}

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_microlearning/group_edit_form.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_microlearning');

// Set the page heading.
$PAGE->set_heading($linktext);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Set up the initial forms.
$editform = new \block_iomad_microlearning\forms\group_edit_form($PAGE->url, $companyid, $groupid, $output);
if (!empty($groupid)) {
    $group = $DB->get_record('microlearning_thread_group', array('id' => $groupid));
    $group->fullname = $group->name;
    $editform->set_data($group);
}

if ($editform->is_cancelled()) {
    redirect($grouplist);
    die;
} else if ($createdata = $editform->get_data()) {

    // Deal with leading/trailing spaces.
    $createdata->name = trim($createdata->name);

    // Create or update the group.
    if (empty($createdata->id)) {
        // We are creating a new group.
        $DB->insert_record('microlearning_thread_group',
                           ['name' => $createdata->name,
                            'companyid' => $createdata->companyid,
                            'threadid' => $createdata->threadid]);
        $redirectmessage = get_string('groupcreatedok', 'block_iomad_microlearning');
    } else {
        $current = $DB->get_record('microlearning_thread_group', array('id' => $createdata->id));
        $current->name = $createdata->name;
        $current->threadid = $createdata->threadid;
        $DB->update_record('microlearning_thread_group', $current);
        $redirectmessage = get_string('groupupdatedok', 'block_iomad_microlearning');
    }

    redirect($grouplist, $redirectmessage, null, \core\output\notification::NOTIFY_SUCCESS);
    die;
} else {
    echo $output->header();

    $editform->display();

    echo $output->footer();
}

