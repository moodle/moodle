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
 * Select site administrators.
 *
 * @package    core_role
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$confirmadd = optional_param('confirmadd', 0, PARAM_INT);
$confirmdel = optional_param('confirmdel', 0, PARAM_INT);

$PAGE->set_url('/admin/roles/admins.php');

admin_externalpage_setup('admins');
if (!is_siteadmin()) {
    die;
}

$admisselector = new core_role_admins_existing_selector();
$admisselector->set_extra_fields(array('username', 'email'));

$potentialadmisselector = new core_role_admins_potential_selector();
$potentialadmisselector->set_extra_fields(array('username', 'email'));

if (optional_param('add', false, PARAM_BOOL) and confirm_sesskey()) {
    if ($userstoadd = $potentialadmisselector->get_selected_users()) {
        $user = reset($userstoadd);
        $username = fullname($user) . " ($user->username, $user->email)";
        echo $OUTPUT->header();
        $yesurl = new moodle_url('/admin/roles/admins.php', array('confirmadd'=>$user->id, 'sesskey'=>sesskey()));
        echo $OUTPUT->confirm(get_string('confirmaddadmin', 'core_role', $username), $yesurl, $PAGE->url);
        echo $OUTPUT->footer();
        die;
    }

} else if (optional_param('remove', false, PARAM_BOOL) and confirm_sesskey()) {
    if ($userstoremove = $admisselector->get_selected_users()) {
        $user = reset($userstoremove);
        if ($USER->id == $user->id) {
            // Can not remove self.
        } else {
            $username = fullname($user) . " ($user->username, $user->email)";
            echo $OUTPUT->header();
            $yesurl = new moodle_url('/admin/roles/admins.php', array('confirmdel'=>$user->id, 'sesskey'=>sesskey()));
            echo $OUTPUT->confirm(get_string('confirmdeladmin', 'core_role', $username), $yesurl, $PAGE->url);
            echo $OUTPUT->footer();
            die;
        }
    }

} else if (optional_param('main', false, PARAM_BOOL) and confirm_sesskey()) {
    if ($newmain = $admisselector->get_selected_users()) {
        $newmain = reset($newmain);
        $newmain = $newmain->id;
        $admins = array();
        foreach (explode(',', $CFG->siteadmins) as $admin) {
            $admin = (int)$admin;
            if ($admin) {
                $admins[$admin] = $admin;
            }
        }

        if (isset($admins[$newmain])) {
            unset($admins[$newmain]);
            array_unshift($admins, $newmain);
            set_config('siteadmins', implode(',', $admins));
            redirect($PAGE->url);
        }
    }

} else if ($confirmadd and confirm_sesskey()) {
    $admins = array();
    foreach (explode(',', $CFG->siteadmins) as $admin) {
        $admin = (int)$admin;
        if ($admin) {
            $admins[$admin] = $admin;
        }
    }
    $admins[$confirmadd] = $confirmadd;
    set_config('siteadmins', implode(',', $admins));
    redirect($PAGE->url);

} else if ($confirmdel and confirm_sesskey() and $confirmdel != $USER->id) {
    $admins = array();
    foreach (explode(',', $CFG->siteadmins) as $admin) {
        $admin = (int)$admin;
        if ($admin) {
            $admins[$admin] = $admin;
        }
    }
    unset($admins[$confirmdel]);
    set_config('siteadmins', implode(',', $admins));
    redirect($PAGE->url);
}

// Print header.
echo $OUTPUT->header();
?>

<div id="addadmisform">
    <h3 class="main"><?php print_string('manageadmins', 'core_role'); ?></h3>

    <form id="assignform" method="post" action="<?php echo $PAGE->url ?>">
    <div>
    <input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />

    <table class="generaltable generalbox groupmanagementtable boxaligncenter" summary="">
    <tr>
      <td id='existingcell'>
          <p>
            <label for="removeselect"><?php print_string('existingadmins', 'core_role'); ?></label>
          </p>
          <?php $admisselector->display(); ?>
          </td>
      <td id="buttonscell">
        <p class="arrow_button">
            <input name="add" id="add" type="submit" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('add'); ?>"
                   title="<?php print_string('add'); ?>" class="btn btn-secondary"/><br />
            <input name="remove" id="remove" type="submit" value="<?php echo get_string('remove').'&nbsp;'.$OUTPUT->rarrow(); ?>"
                   title="<?php print_string('remove'); ?>" class="btn btn-secondary"/><br />
            <input name="main" id="main" type="submit" value="<?php echo get_string('mainadminset', 'core_role'); ?>"
                   title="<?php print_string('mainadminset', 'core_role'); ?>" class="btn btn-secondary"/>
        </p>
      </td>
      <td id="potentialcell">
          <p>
            <label for="addselect"><?php print_string('users'); ?></label>
          </p>
          <?php $potentialadmisselector->display(); ?>
      </td>
    </tr>
    </table>
    </div>
    </form>
</div>

<?php

echo $OUTPUT->footer();
