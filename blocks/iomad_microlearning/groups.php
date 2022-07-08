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
 * Script to let a user create a group for a particular company microlearnng.
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once('lib.php');
require_once(dirname(__FILE__) . '/../../course/lib.php');

$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$groupid = optional_param('groupid', 0, PARAM_INT);
$deleteids = optional_param_array('groupids', null, PARAM_INT);
$createnew = optional_param('createnew', 0, PARAM_INT);
$deleteid = optional_param('deleteid', 0, PARAM_INT);
$confirm = optional_param('confirm', null, PARAM_ALPHANUM);
$submit = optional_param('submitbutton', '', PARAM_ALPHANUM);

$context = context_system::instance();
require_login();

iomad::require_capability('block/iomad_microlearning:manage_groups', $context);

$urlparams = array();
if ($returnurl) {
    $urlparams['returnurl'] = $returnurl;
}
$companylist = new moodle_url('/blocks/iomad_company_admin/index.php', $urlparams);

$linktext = get_string('learninggroups', 'block_iomad_microlearning');

// Set the url.
$linkurl = new moodle_url('/blocks/iomad_microlearning/groups.php');

$PAGE->set_context($context);
$PAGE->set_url($linkurl);
$PAGE->set_pagelayout('base');
$PAGE->set_title($linktext);

// get output renderer
$output = $PAGE->get_renderer('block_iomad_microlearning');

// Set the page heading.
$PAGE->set_heading($linktext);

// Deal with the link back to the main microlearning page.
$buttoncaption = get_string('threads', 'block_iomad_microlearning');
$buttonlink = new moodle_url('/blocks/iomad_microlearning/threads.php');
$buttons = $OUTPUT->single_button($buttonlink, $buttoncaption, 'get');
$PAGE->set_button($buttons);

// Set the companyid
$companyid = iomad::get_my_companyid($context);

// Delete any valid groups.
if ($deleteid && confirm_sesskey()) {
    if (!$group = $DB->get_record('microlearning_thread_group', array('id' => $deleteid))) {
        print_error('nogroup', 'block_iomad_microlearning');
    }

    if ($confirm == md5($deleteid)) {

        // Get the list of group ids which are to be removed..
        if (!empty($deleteid)) {
            // Check if group has already been removed.
            if ($DB->get_record('microlearning_thread_group', ['id' => $deleteid])) {
                // If not delete it
                $DB->delete_records('microlearning_thread_group', ['id' => $deleteid]);
                $DB->set_field('microlearning_thread_user', 'groupid', 0, ['groupid' => $deleteid]);
                redirect($linkurl, get_string('groupdeletedok', 'block_iomad_microlearning'), null, \core\output\notification::NOTIFY_SUCCESS);
            }
        }
    } else {
        echo $output->header();
        echo $output->heading(get_string('deletegroup', 'block_iomad_microlearning', $group->name));
        $optionsyes = array('deleteid' => $deleteid, 'confirm' => md5($deleteid), 'sesskey' => sesskey());
        echo $output->confirm(get_string('deletegroupcheckfull', 'block_iomad_microlearning', $group->name),
                              new moodle_url('groups.php', $optionsyes), 'groups.php');
        echo $output->footer();
        die;
    }
}

// Set up the table.
$table = new \block_iomad_microlearning\tables\list_groups_table('block_iomad_microlearning_groups_table');

// Set up the initial SQL for the form.
$selectsql = "mtg.*, mt.name as threadname";
$fromsql = "{microlearning_thread_group} mtg JOIN {microlearning_thread} mt ON (mtg.threadid = mt.id)";
$wheresql = "mtg.companyid = :companyid";
$sqlparams = ['companyid' => $companyid];

// Set up the headers for the form.
$headers = array(get_string('threadname', 'block_iomad_microlearning'),
                 get_string('name'),
                 get_string('actions')
                 );

$columns = array('threadname',
                 'name',
                 'actions');

$table->set_sql($selectsql, $fromsql, $wheresql, $sqlparams);
$table->define_baseurl($linkurl);
$table->define_columns($columns);
$table->define_headers($headers);
$table->no_sorting('actions');

echo $output->header();

echo '<div class="buttons">';
echo $OUTPUT->single_button(new moodle_url('group_edit_form.php'), get_string('creategroup', 'block_iomad_microlearning'));
echo '</div>';
$table->out(30, true);

echo $output->footer();
