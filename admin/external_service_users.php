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
 * Web services function UI
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('external_forms.php');
require_once($CFG->dirroot.'/admin/webservice/lib.php');

$id = required_param('id', PARAM_INT);

$PAGE->set_url('admin/external_service_users.php', array('id'=>$id));

admin_externalpage_setup('externalserviceusers');
admin_externalpage_print_header();

/// Get the user_selector we will need.
$potentialuserselector = new service_potential_user_selector('addselect', array('serviceid' => $id));
$alloweduserselector = new service_allowed_user_selector('removeselect', array('serviceid' => $id));

/// Process incoming user assignments to the service
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstoassign = $potentialuserselector->get_selected_users();
            if (!empty($userstoassign)) {

                foreach ($userstoassign as $adduser) {
                    global $DB;
                    $serviceuser = new object();
                    $serviceuser->externalserviceid = $id;
                    $serviceuser->userid = $adduser->id;
                    $serviceuser->timecreated = mktime();
                    $DB->insert_record('external_services_users', $serviceuser);
                    add_to_log(1, 'core', 'assign', 'admin/external_service_users.php?id='.$id, 'add', '', $adduser->id);
                }

                $potentialuserselector->invalidate_selected_users();
                $alloweduserselector->invalidate_selected_users();
                }
        }

/// Process removing user assignments to the service
        if (optional_param('remove', false, PARAM_BOOL) && confirm_sesskey()) {
         $userstoremove = $alloweduserselector->get_selected_users();
            if (!empty($userstoremove)) {

                foreach ($userstoremove as $removeuser) {
                    global $DB;
                    $DB->delete_records('external_services_users', array('externalserviceid' => $id, 'userid' => $removeuser->id));
                    add_to_log(1, 'core', 'assign', 'admin/external_service_users.php?id='.$id, 'remove', '', $removeuser->id);
                }

                $potentialuserselector->invalidate_selected_users();
                $alloweduserselector->invalidate_selected_users();
                }
        }


/// display the UI
?>
<form id="assignform" method="post" action="external_service_users.php?id=<?php echo $id ?>"><div>
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />

  <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="removeselect"><?php print_string('serviceusers', 'webservice'); ?></label></p>
          <?php $alloweduserselector->display() ?>
      </td>
      <td id="buttonscell">
          <div id="addcontrols">
              <input name="add" id="add" type="submit" value="<?php echo $THEME->larrow.'&nbsp;'.get_string('add'); ?>" title="<?php print_string('add'); ?>" /><br />  
          </div>

          <div id="removecontrols">
              <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$THEME->rarrow; ?>" title="<?php print_string('remove'); ?>" />
          </div>
      </td>
      <td id="potentialcell">
          <p><label for="addselect"><?php print_string('potusers', 'webservice'); ?></label></p>
          <?php $potentialuserselector->display() ?>
      </td>
    </tr>
  </table>
</div></form>

<?php

echo $OUTPUT->footer();