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

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/admin/webservice/lib.php');

$id = required_param('id', PARAM_INT);

$PAGE->set_url('/admin/webservice/service_users.php', array('id'=>$id));
$PAGE->navbar->ignore_active(true);
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('webservices', 'webservice'));
$PAGE->navbar->add(get_string('externalservices', 'webservice'), new moodle_url('/admin/settings.php?section=externalservices'));
$PAGE->navbar->add(get_string('serviceusers', 'webservice'));

$PAGE->requires->js('/admin/webservice/script.js');

admin_externalpage_setup('externalserviceusers');
admin_externalpage_print_header();


/// Get the user_selector we will need.
$potentialuserselector = new service_user_selector('addselect', array('serviceid' => $id, 'displayallowedusers' => 0));
$alloweduserselector = new service_user_selector('removeselect', array('serviceid' => $id, 'displayallowedusers' => 1));

/// Process incoming user assignments to the service
        if (optional_param('add', false, PARAM_BOOL) && confirm_sesskey()) {
            $userstoassign = $potentialuserselector->get_selected_users();
            if (!empty($userstoassign)) {

                foreach ($userstoassign as $adduser) {
                    $serviceuser = new object();
                    $serviceuser->externalserviceid = $id;
                    $serviceuser->userid = $adduser->id;
                    $serviceuser->timecreated = mktime();
                    $DB->insert_record('external_services_users', $serviceuser);
                    add_to_log(1, 'core', 'assign', $CFG->admin.'/webservice/service_users.php?id='.$id, 'add', '', $adduser->id);
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
                    $DB->delete_records('external_services_users', array('externalserviceid' => $id, 'userid' => $removeuser->id));
                    add_to_log(1, 'core', 'assign', $CFG->admin.'/webservice/service_users.php?id='.$id, 'remove', '', $removeuser->id);
                }

                $potentialuserselector->invalidate_selected_users();
                $alloweduserselector->invalidate_selected_users();
                }
        }
/// Print the form.
/// display the UI
?>
<form id="assignform" method="post" action="service_users.php?id=<?php echo $id ?>"><div>
  <input type="hidden" name="sesskey" value="<?php echo sesskey() ?>" />

  <table summary="" class="roleassigntable generaltable generalbox boxaligncenter" cellspacing="0">
    <tr>
      <td id="existingcell">
          <p><label for="removeselect"><?php print_string('serviceusers', 'webservice'); ?></label></p>
          <?php $alloweduserselector->display() ?>
      </td>
      <td id="buttonscell">
          <div id="addcontrols">
              <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>" title="<?php print_string('add'); ?>" /><br />
          </div>

          <div id="removecontrols">
              <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php print_string('remove'); ?>" />
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

/// save user settings (administrator clicked on update button)
if (optional_param('updateuser', false, PARAM_BOOL) && confirm_sesskey()) {
    $useridtoupdate = optional_param('userid', false, PARAM_INT);
    $iprestriction = optional_param('iprestriction', '', PARAM_TEXT);
    $serviceuserid = optional_param('serviceuserid', '', PARAM_INT);
    $fromday = optional_param('fromday'.$useridtoupdate, '', PARAM_INT);
    $frommonth = optional_param('frommonth'.$useridtoupdate, '', PARAM_INT);
    $fromyear = optional_param('fromyear'.$useridtoupdate, '', PARAM_INT);
    $addcap = optional_param('addcap', false, PARAM_INT);
    $enablevaliduntil = optional_param('enablevaliduntil', false, PARAM_INT);
    if (!empty($fromday) && !empty($frommonth) && !empty($fromyear)) {
        $validuntil = mktime(23, 59, 59, $frommonth, $fromday, $fromyear);
    } else {
        $validuntil = "";
    }

    $serviceuser = new object();
    $serviceuser->id = $serviceuserid;
    if ($enablevaliduntil) {
        $serviceuser->validuntil = $validuntil;
    } else {
        $serviceuser->validuntil = null; //the valid until field is disabled, we reset the value
    }
    $serviceuser->iprestriction = $iprestriction;
    $DB->update_record('external_services_users', $serviceuser);

    //TODO: assign capability
}

//display the list of allowed users with their options (ip/timecreated / validuntil...)
//check that the user has the service required capability (if needed)
$sql = " SELECT u.id as id, esu.id as serviceuserid, u.email as email, u.firstname as firstname, u.lastname as lastname,
                  esu.iprestriction as iprestriction, esu.validuntil as validuntil,
                  esu.timecreated as timecreated
                  FROM {user} u, {external_services_users} esu
                  WHERE username <> 'guest' AND deleted = 0 AND confirmed = 1
                        AND esu.userid = u.id
                        AND esu.externalserviceid = ?";
$allowedusers = $DB->get_records_sql($sql, array($id));
if (!empty($allowedusers)) {
    echo $OUTPUT->box_start('generalbox', 'alloweduserlist');

    echo "<label><strong>".get_string('serviceuserssettings', 'webservice').":</strong></label>";
    echo "<br/><br/><span style=\"font-size:85%\">"; //reduce font of the user settings
    foreach($allowedusers as $user) {

        echo print_collapsible_region_start('', 'usersettings'.$user->id,$user->firstname." ".$user->lastname.", ".$user->email,false,true,true);

        //user settings form
        $contents = "<div class=\"fcontainer clearfix\">";

        //ip restriction textfield
        $iprestid = 'iprest'.$user->id;
        $contents .= "<div class=\"fitem\"><div class=\"fitemtitle\"><label for=\"$iprestid\">".get_string('iprestriction','webservice')." </label></div><div class=\"felement\">";
        $contents .= '<input type="text" id="'.$iprestid.'" name="iprestriction" style="width: 30em;" value="'.s($user->iprestriction).'" />';
        $contents .= "</div></div>";
        //valid until date selector
        $contents .= "<div class=\"fitem\"><div class=\"fitemtitle\"><label>".get_string('validuntil','webservice')." </label></div><div class=\"felement\">";
        // the following date selector needs to have specific day/month/year field ids because we use javascript (enable/disable).
        $selectors = html_select::make_time_selectors(array('days' => 'fromday'.$user->id,'months' => 'frommonth'.$user->id, 'years' => 'fromyear'.$user->id),$user->validuntil);
        foreach ($selectors as $select) {
            if (empty($user->validuntil)) {
                $select->disabled = true;
            }
            $contents .= $OUTPUT->select($select);
        }
        $checkbox = new html_select_option();
        $checkbox->value = 1;
        $checkbox->id = 'enablevaliduntil'.$user->id;
        $checkbox->name = 'enablevaliduntil';
        $checkbox->selected = empty($user->validuntil)?false:true;
        $checkbox->text = get_string('enabled', 'webservice');
        $checkbox->label->text = get_string('enabled', 'webservice');
        $checkbox->alt = get_string('enabled', 'webservice');
        $checkbox->add_action('change', 'external_disablevaliduntil', array($user->id)); //into admin/webservice/script.js
        $contents .= $OUTPUT->checkbox($checkbox, 'enablevaliduntil');
        $contents .= ""; //init date selector disable status

        $contents .= "</div></div>";
        //TO IMPLEMENT : assign the required capability (if needed)
        $contents .=  "<div class=\"fitem\"><div class=\"fitemtitle\"><label>".get_string('addrequiredcapability','webservice')." </label></div><div class=\"felement fcheckbox\">";
        $checkbox = new html_select_option();
        $checkbox->value = $user->id;
        $checkbox->selected = false;
        $checkbox->text = ' ';
        $checkbox->label->text = ' ';
        $checkbox->alt = 'TODO:'.get_string('addrequiredcapability', 'webservice');
        $contents .= $OUTPUT->checkbox($checkbox, 'addcap')."</div></div>";
        $contents .= '<div><input type="submit" name="submit" value="'.s(get_string('update')).'" /></div>';
        $contents .= '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        $contents .= '<input type="hidden" name="id" value="'.$id.'" />';
        $contents .= '<input type="hidden" name="userid" value="'.$user->id.'" />';
        $contents .= '<input type="hidden" name="serviceuserid" value="'.$user->serviceuserid.'" />';
        $contents .= '<input type="hidden" name="updateuser" value="1" />';
        $contents .= "</div>";

        echo html_writer::tag('form', array('target'=>'service_users.php', 'method'=>'post', 'id'=>'usersetting'.$user->id), $contents);

        echo print_collapsible_region_end(true);


    }
    echo "</span>";
    echo $OUTPUT->box_end();
}


echo $OUTPUT->footer();
