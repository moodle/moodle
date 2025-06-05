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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');


require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Assignment extension dates form
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_extension_form extends moodleform {
    /** @var array $instance - The data passed to this form */
    private $instance;

    /**
     * Define the form - called by parent constructor
     */
    public function definition() {
        global $DB;

        $mform = $this->_form;
        $params = $this->_customdata;

        // Instance variable is used by the form validation function.
        $instance = $params['instance'];
        $this->instance = $instance;

        // Get the assignment class.
        /** @var assign $assign */
        $assign = $params['assign'];
        $userlist = $params['userlist'];

        // Load current extensions for all selected users.
        [$usercondition, $params] = $DB->get_in_or_equal($userlist);
        array_unshift($params, $assign->get_instance()->id);
        $userinfo = $DB->get_records_sql("
                SELECT u.*, auf.extensionduedate
                  FROM {user} u
             LEFT JOIN {assign_user_flags} auf ON auf.userid = u.id AND auf.assignment = ?
                 WHERE u.id $usercondition
            ", $params);

        // Prepare display of up to 5 users.
        // TODO Does not support custom user profile fields (MDL-70456).
        $extrauserfields = \core_user\fields::get_identity_fields($assign->get_context(), false);
        $displayedusercount = 0;
        $usershtml = '';
        foreach ($userlist as $userid) {
            $displayedusercount += 1;
            if ($displayedusercount > 5) {
                $usershtml .= get_string('moreusers', 'assign', count($userlist) - 5);
                break;
            }

            $user = $userinfo[$userid];
            $usershtml .= $assign->get_renderer()->render(
                new assign_user_summary($user,
                    $assign->get_course()->id,
                    has_capability('moodle/site:viewfullnames',
                        $assign->get_course_context()),
                    $assign->is_blind_marking(),
                    $assign->get_uniqueid_for_user($user->id),
                    $extrauserfields,
                    !$assign->is_active_user($userid)
                ),
            );
        }

        // Find the range of extensions that currently exist for these users.
        $earliestextension = null;
        $lateststextension = 0;
        $userswithoutanextension = 0;
        foreach ($userlist as $userid) {
            $user = $userinfo[$userid];

            if ($user->extensionduedate) {
                $lateststextension = max($lateststextension, $user->extensionduedate);
                if ($earliestextension !== null) {
                    $earliestextension = min($earliestextension, $user->extensionduedate);
                } else {
                    $earliestextension = $user->extensionduedate;
                }
            } else {
                $userswithoutanextension += 1;
            }
        }

        $listusersmessage = get_string('grantextensionforusers', 'assign', count($userlist));
        $mform->addElement('header', 'general', $listusersmessage);
        $mform->addElement('static', 'userslist', get_string('selectedusers', 'assign'), $usershtml);

        if ($instance->allowsubmissionsfromdate) {
            $mform->addElement('static', 'allowsubmissionsfromdate', get_string('allowsubmissionsfromdate', 'assign'),
                               userdate($instance->allowsubmissionsfromdate));
        }

        $finaldate = 0;
        if ($instance->duedate) {
            $mform->addElement('static', 'duedate', get_string('duedate', 'assign'), userdate($instance->duedate));
            $finaldate = $instance->duedate;
        }
        if ($instance->cutoffdate) {
            $mform->addElement('static', 'cutoffdate', get_string('cutoffdate', 'assign'), userdate($instance->cutoffdate));
            $finaldate = $instance->cutoffdate;
        }

        if ($userswithoutanextension == count($userlist)) {
            // All users don't have an extension yet.
            $currentdatesdisplay = get_string('extensionduedatenone', 'assign');

        } else {
            if ($earliestextension == $lateststextension) {
                $currentdatesdisplay = userdate($lateststextension);
            } else {
                $currentdatesdisplay = get_string('extensionduedaterange', 'assign', (object) [
                    'earliest' => userdate($earliestextension),
                    'latest' => userdate($lateststextension),
                ]);
            }
            if ($userswithoutanextension) {
                $currentdatesdisplay .= '<br>' . get_string('extensionduedatewithout', 'assign', $userswithoutanextension);
            }
        }

        $mform->addElement('static', 'currentextension', get_string('extensionduedatecurrent', 'assign'),
            $currentdatesdisplay);

        if ($lateststextension) {
            // If there are existing extensions, edit based on the current (latest) value.
            $defaultdate = $lateststextension;
        } else {
            // Otherwise take the later of the deadline and one minute before midnight tonight (server time).
            $endoftoday = \core\di::get(\core\clock::class)->now()->setTime(23, 59);
            $defaultdate = max($finaldate, $endoftoday->getTimestamp());
        }
        $mform->addElement('date_time_selector', 'extensionduedate',
                           get_string('extensionduedate', 'assign'), array('optional'=>true));
        $mform->setDefault('extensionduedate', $defaultdate);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'userid');
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'selectedusers');
        $mform->setType('selectedusers', PARAM_SEQUENCE);
        $mform->addElement('hidden', 'action', 'saveextension');
        $mform->setType('action', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('savechanges', 'assign'));
    }

    /**
     * Perform validation on the extension form
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if ($this->instance->duedate && $data['extensionduedate']) {
            if ($this->instance->duedate > $data['extensionduedate']) {
                $errors['extensionduedate'] = get_string('extensionnotafterduedate', 'assign');
            }
        }
        if ($this->instance->allowsubmissionsfromdate && $data['extensionduedate']) {
            if ($this->instance->allowsubmissionsfromdate > $data['extensionduedate']) {
                $errors['extensionduedate'] = get_string('extensionnotafterfromdate', 'assign');
            }
        }

        return $errors;
    }
}
