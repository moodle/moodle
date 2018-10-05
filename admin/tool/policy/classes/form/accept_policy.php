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
 * Provides {@link tool_policy\form\accept_policy} class.
 *
 * @package     tool_policy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_policy\form;

use tool_policy\api;
use tool_policy\policy_version;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Represents the form for accepting or revoking a policy.
 *
 * @package     tool_policy
 * @copyright   2018 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class accept_policy extends \moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $PAGE, $USER;
        $mform = $this->_form;

        if (empty($this->_customdata['userids']) || !is_array($this->_customdata['userids'])) {
            throw new \moodle_exception('missingparam', 'error', '', 'userids');
        }
        if (empty($this->_customdata['versionids']) || !is_array($this->_customdata['versionids'])) {
            throw new \moodle_exception('missingparam', '', '', 'versionids');
        }
        $action = $this->_customdata['action'];
        $userids = clean_param_array($this->_customdata['userids'], PARAM_INT);
        $versionids = clean_param_array($this->_customdata['versionids'], PARAM_INT);
        $usernames = $this->validate_and_get_users($versionids, $userids, $action);
        $versionnames = $this->validate_and_get_versions($versionids);

        foreach ($usernames as $userid => $name) {
            $mform->addElement('hidden', 'userids['.$userid.']', $userid);
            $mform->setType('userids['.$userid.']', PARAM_INT);
        }

        foreach ($versionnames as $versionid => $name) {
            $mform->addElement('hidden', 'versionids['.$versionid.']', $versionid);
            $mform->setType('versionids['.$versionid.']', PARAM_INT);
        }

        $mform->addElement('hidden', 'returnurl');
        $mform->setType('returnurl', PARAM_LOCALURL);
        $useracceptancelabel = (count($usernames) > 1) ? get_string('acceptanceusers', 'tool_policy') :
                get_string('user');
        $mform->addElement('static', 'user', $useracceptancelabel, join(', ', $usernames));
        $policyacceptancelabel = (count($versionnames) > 1) ? get_string('acceptancepolicies', 'tool_policy') :
                get_string('policydochdrpolicy', 'tool_policy');
        $mform->addElement('static', 'policy', $policyacceptancelabel, join(', ', $versionnames));

        if ($action === 'revoke') {
            $mform->addElement('static', 'ack', '', get_string('revokeacknowledgement', 'tool_policy'));
            $mform->addElement('hidden', 'action', 'revoke');
        } else if ($action === 'accept') {
            $mform->addElement('static', 'ack', '', get_string('acceptanceacknowledgement', 'tool_policy'));
            $mform->addElement('hidden', 'action', 'accept');
        } else if ($action === 'decline') {
            $mform->addElement('static', 'ack', '', get_string('declineacknowledgement', 'tool_policy'));
            $mform->addElement('hidden', 'action', 'decline');
        } else {
            throw new \moodle_exception('invalidaccessparameter');
        }

        $mform->setType('action', PARAM_ALPHA);

        if (count($usernames) == 1 && isset($usernames[$USER->id])) {
            // No need to display the acknowledgement if the users are giving/revoking acceptance on their own.
            $mform->removeElement('ack');
        }

        $mform->addElement('textarea', 'note', get_string('acceptancenote', 'tool_policy'));
        $mform->setType('note', PARAM_NOTAGS);

        if (!empty($this->_customdata['showbuttons'])) {
            if ($action === 'revoke') {
                $this->add_action_buttons(true, get_string('irevokethepolicy', 'tool_policy'));
            } else if ($action === 'accept') {
                $this->add_action_buttons(true, get_string('iagreetothepolicy', 'tool_policy'));
            } else if ($action === 'decline') {
                $this->add_action_buttons(true, get_string('declinethepolicy', 'tool_policy'));
            }
        }

        $PAGE->requires->js_call_amd('tool_policy/policyactions', 'init');
    }

    /**
     * Validate userids and return usernames
     *
     * @param array $versionids int[] List of policy version ids to process.
     * @param array $userids
     * @param string $action accept|decline|revoke
     * @return array (userid=>username)
     */
    protected function validate_and_get_users($versionids, $userids, $action) {
        global $DB;

        $usernames = [];
        list($sql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);
        $params['usercontextlevel'] = CONTEXT_USER;
        $users = $DB->get_records_sql("SELECT u.id, " . get_all_user_name_fields(true, 'u') . ", " .
                \context_helper::get_preload_record_columns_sql('ctx') .
            " FROM {user} u JOIN {context} ctx ON ctx.contextlevel=:usercontextlevel AND ctx.instanceid = u.id
            WHERE u.id " . $sql, $params);

        foreach ($userids as $userid) {
            if (!isset($users[$userid])) {
                throw new \dml_missing_record_exception('user', 'id=?', [$userid]);
            }
            $user = $users[$userid];
            if (isguestuser($user)) {
                throw new \moodle_exception('noguest');
            }
            \context_helper::preload_from_record($user);
            if ($action === 'revoke') {
                api::can_revoke_policies($versionids, $userid, true);
            } else if ($action === 'accept') {
                api::can_accept_policies($versionids, $userid, true);
            } else if ($action === 'decline') {
                api::can_decline_policies($versionids, $userid, true);
            }
            $usernames[$userid] = fullname($user);
        }
        return $usernames;
    }

    /**
     * Validate versionids and return their names
     *
     * @param array $versionids
     * @return array (versionid=>name)
     */
    protected function validate_and_get_versions($versionids) {
        $versionnames = [];
        $policies = api::list_policies();
        foreach ($versionids as $versionid) {
            $version = api::get_policy_version($versionid, $policies);
            if ($version->audience == policy_version::AUDIENCE_GUESTS) {
                throw new \moodle_exception('errorpolicyversionnotfound', 'tool_policy');
            }
            $url = new \moodle_url('/admin/tool/policy/view.php', ['versionid' => $version->id]);
            $policyname = $version->name;
            if ($version->status != policy_version::STATUS_ACTIVE) {
                $policyname .= ' ' . $version->revision;
            }
            $versionnames[$version->id] = \html_writer::link($url, $policyname,
                ['data-action' => 'view', 'data-versionid' => $version->id]);
        }
        return $versionnames;
    }

    /**
     * Process form submission
     */
    public function process() {
        if ($data = $this->get_data()) {
            foreach ($data->userids as $userid) {
                if ($data->action === 'revoke') {
                    foreach ($data->versionids as $versionid) {
                        \tool_policy\api::revoke_acceptance($versionid, $userid, $data->note);
                    }
                } else if ($data->action === 'accept') {
                    \tool_policy\api::accept_policies($data->versionids, $userid, $data->note);
                } else if ($data->action === 'decline') {
                    \tool_policy\api::decline_policies($data->versionids, $userid, $data->note);
                }
            }
        }
    }
}