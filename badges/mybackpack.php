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
 * User backpack settings page.
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

require_once(dirname(dirname(__FILE__)) . '/config.php');
require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->dirroot . '/badges/backpack_form.php');
require_once($CFG->dirroot . '/badges/lib/backpacklib.php');

require_login();

if (empty($CFG->enablebadges)) {
    print_error('badgesdisabled', 'badges');
}

$context = context_user::instance($USER->id);
require_capability('moodle/badges:manageownbadges', $context);

$clear = optional_param('clear', false, PARAM_BOOL);

if (empty($CFG->badges_allowexternalbackpack)) {
    redirect($CFG->wwwroot);
}

$PAGE->set_url(new moodle_url('/badges/mybackpack.php'));
$PAGE->set_context($context);

$title = get_string('mybackpack', 'badges');
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('mydashboard');

navigation_node::override_active_url(new moodle_url('/badges/mybadges.php'));
$PAGE->navbar->add(get_string('mybackpack', 'badges'));

if ($clear) {
     $DB->delete_records('badge_backpack', array('userid' => $USER->id));
     redirect(new moodle_url('/badges/mybadges.php'));
}

$backpack = $DB->get_record('badge_backpack', array('userid' => $USER->id));

if ($backpack) {
    $bp = new OpenBadgesBackpackHandler($backpack);
    $request = $bp->get_groups();

    if (empty($request->groups)) {
        unset($SESSION->badgesparams);
        redirect(new moodle_url('/badges/mybadges.php'), get_string('error:nogroups', 'badges'), 20);
    }

    $params = array(
            'data' => $backpack,
            'backpackuid' => $request->userId,
            'groups' => $request->groups
    );
    $groupform = new edit_backpack_group_form(new moodle_url('/badges/mybackpack.php'), $params);

    if ($groupform->is_cancelled()) {
        redirect(new moodle_url('/badges/mybadges.php'));
    } else if ($groupdata = $groupform->get_data()) {
        $obj = new stdClass();
        $obj->userid = $groupdata->userid;
        $obj->email = $groupdata->email;
        $obj->backpackurl = $groupdata->backpackurl;
        $obj->backpackuid = $groupdata->backpackuid;
        $obj->backpackgid = $groupdata->backpackgid;
        $obj->autosync = 0;
        $obj->password = '';
        if ($rec = $DB->get_record('badge_backpack', array('userid' => $groupdata->userid))) {
            $obj->id = $rec->id;
            $DB->update_record('badge_backpack', $obj);
        } else {
            $DB->insert_record('badge_backpack', $obj);
        }
        redirect(new moodle_url('/badges/mybadges.php'));
    }

    echo $OUTPUT->header();
    $groupform->display();
    echo $OUTPUT->footer();
    die();
} else {
    $form = new edit_backpack_form();

    if ($form->is_cancelled()) {
        redirect(new moodle_url('/badges/mybadges.php'));
    } else if (($data = $form->get_data()) || !empty($SESSION->badgesparams)) {
        if (empty($SESSION->badgesparams)) {
            $bp = new OpenBadgesBackpackHandler($data);
            $request = $bp->get_groups();

            // If there is an error, start over.
            if (is_array($request) && $request['status'] == 'missing') {
                unset($SESSION->badgesparams);
                redirect(new moodle_url('/badges/mybackpack.php'), $request['message'], 10);
            } else if (empty($request->groups)) {
                unset($SESSION->badgesparams);
                redirect(new moodle_url('/badges/mybadges.php'), get_string('error:nogroups', 'badges'), 20);
            }

            $params = array(
                    'data' => $data,
                    'backpackuid' => $request->userId,
                    'groups' => $request->groups
            );
            $SESSION->badgesparams = $params;
        }
        $groupform = new edit_backpack_group_form(new moodle_url('/badges/mybackpack.php', array('addgroups' => true)), $SESSION->badgesparams);

        if ($groupform->is_cancelled()) {
            unset($SESSION->badgesparams);
            redirect(new moodle_url('/badges/mybadges.php'));
        } else if ($groupdata = $groupform->get_data()) {
            $obj = new stdClass();
            $obj->userid = $groupdata->userid;
            $obj->email = $groupdata->email;
            $obj->backpackurl = $groupdata->backpackurl;
            $obj->backpackuid = $groupdata->backpackuid;
            $obj->backpackgid = $groupdata->backpackgid;
            $obj->autosync = 0;
            $obj->password = '';
            if ($rec = $DB->get_record('badge_backpack', array('userid' => $groupdata->userid))) {
                $obj->id = $rec->id;
                $DB->update_record('badge_backpack', $obj);
            } else {
                $DB->insert_record('badge_backpack', $obj);
            }
            unset($SESSION->badgesparams);
            redirect(new moodle_url('/badges/mybadges.php'));
        }

        echo $OUTPUT->header();
        $groupform->display();
        echo $OUTPUT->footer();
        die();
    }

    echo $OUTPUT->header();
    $form->display();
    echo $OUTPUT->footer();
}
