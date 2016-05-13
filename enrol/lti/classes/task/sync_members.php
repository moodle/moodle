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
 * Handles synchronising members using the enrolment LTI.
 *
 * @package    enrol_lti
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_lti\task;

/**
 * Task for synchronising members using the enrolment LTI.
 *
 * @package    enrol_lti
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sync_members extends \core\task\scheduled_task {

    /**
     * The LTI message type.
     */
    const LTI_MESSAGE_TYPE = 'basic-lis-readmembershipsforcontext';

    /**
     * The LTI version.
     */
    const LTI_VERSION = 'LTI-1p0';

    /**
     * Get a descriptive name for this task.
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasksyncmembers', 'enrol_lti');
    }

    /**
     * Performs the synchronisation of members.
     *
     * @return bool|void
     */
    public function execute() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/enrol/lti/ims-blti/OAuth.php');
        require_once($CFG->dirroot . '/enrol/lti/ims-blti/OAuthBody.php');

        // Check if the authentication plugin is disabled.
        if (!is_enabled_auth('lti')) {
            mtrace('Skipping task - ' . get_string('pluginnotenabled', 'auth', get_string('pluginname', 'auth_lti')));
            return true;
        }

        // Check if the enrolment plugin is disabled - isn't really necessary as the task should not run if
        // the plugin is disabled, but there is no harm in making sure core hasn't done something wrong.
        if (!enrol_is_enabled('lti')) {
            mtrace('Skipping task - ' . get_string('enrolisdisabled', 'enrol_lti'));
            return true;
        }

        // Get all the enabled tools.
        if ($tools = \enrol_lti\helper::get_lti_tools(array('status' => ENROL_INSTANCE_ENABLED, 'membersync' => 1))) {
            $ltiplugin = enrol_get_plugin('lti');
            $consumers = array();
            $currentusers = array();
            $userphotos = array();
            foreach ($tools as $tool) {
                mtrace("Starting - Member sync for shared tool '$tool->id' for the course '$tool->courseid'.");

                // Variables to keep track of information to display later.
                $usercount = 0;
                $enrolcount = 0;
                $unenrolcount = 0;

                // We check for all the users - users can access the same tool from different consumers.
                if ($ltiusers = $DB->get_records('enrol_lti_users', array('toolid' => $tool->id), 'lastaccess DESC')) {
                    foreach ($ltiusers as $ltiuser) {
                        $mtracecontent = "for the user '$ltiuser->userid' in the tool '$tool->id' for the course " .
                            "'$tool->courseid'";
                        $usercount++;

                        // Check if we do not have a membershipsurl - this can happen if the sync process has an unexpected error.
                        if (!$ltiuser->membershipsurl) {
                            mtrace("Skipping - Empty membershipsurl $mtracecontent.");
                            continue;
                        }

                        // Check if we do not have a membershipsid - this can happen if the sync process has an unexpected error.
                        if (!$ltiuser->membershipsid) {
                            mtrace("Skipping - Empty membershipsid $mtracecontent.");
                            continue;
                        }

                        $consumer = sha1($ltiuser->membershipsurl . ':' . $ltiuser->membershipsid . ':' .
                            $ltiuser->consumerkey . ':' . $ltiuser->consumersecret);
                        if (in_array($consumer, $consumers)) {
                            // We have already synchronised with this consumer.
                            continue;
                        }

                        $consumers[] = $consumer;

                        $params = array(
                            'lti_message_type' => self::LTI_MESSAGE_TYPE,
                            'id' => $ltiuser->membershipsid,
                            'lti_version' => self::LTI_VERSION
                        );

                        mtrace("Calling memberships url '$ltiuser->membershipsurl' with body '" .
                            json_encode($params) . "'");

                        try {
                            $response = sendOAuthParamsPOST('POST', $ltiuser->membershipsurl, $ltiuser->consumerkey,
                                $ltiuser->consumersecret, 'application/x-www-form-urlencoded', $params);
                        } catch (\Exception $e) {
                            mtrace("Skipping - No response received $mtracecontent from '$ltiuser->membershipsurl'");
                            mtrace($e->getMessage());
                            continue;
                        }

                        // Check the response from the consumer.
                        $data = new \SimpleXMLElement($response);

                        // Check if we did not receive a valid response.
                        if (empty($data->statusinfo)) {
                            mtrace("Skipping - Bad response received $mtracecontent from '$ltiuser->membershipsurl'");
                            mtrace('Skipping - Error parsing the XML received \'' . substr($response, 0, 125) .
                                '\' ... (Displaying only 125 chars)');
                            continue;
                        }

                        // Check if we did not receive a valid response.
                        if (strpos(strtolower($data->statusinfo->codemajor), 'success') === false) {
                            mtrace('Skipping - Error received from the remote system: ' . $data->statusinfo->codemajor
                                . ' ' . $data->statusinfo->severity . ' ' . $data->statusinfo->codeminor);
                            continue;
                        }

                        $members = $data->memberships->member;
                        mtrace(count($members) . ' members received.');
                        foreach ($members as $member) {
                            // Set the user data.
                            $user = new \stdClass();
                            $user->username = \enrol_lti\helper::create_username($ltiuser->consumerkey, $member->user_id);
                            $user->firstname = \core_user::clean_field($member->person_name_given, 'firstname');
                            $user->lastname = \core_user::clean_field($member->person_name_family, 'lastname');
                            $user->email = \core_user::clean_field($member->person_contact_email_primary, 'email');

                            // Get the user data from the LTI consumer.
                            $user = \enrol_lti\helper::assign_user_tool_data($tool, $user);

                            if (!$dbuser = $DB->get_record('user', array('username' => $user->username, 'deleted' => 0))) {
                                if ($tool->membersyncmode == \enrol_lti\helper::MEMBER_SYNC_ENROL_AND_UNENROL ||
                                    $tool->membersyncmode == \enrol_lti\helper::MEMBER_SYNC_ENROL_NEW) {
                                    // If the email was stripped/not set then fill it with a default one. This
                                    // stops the user from being redirected to edit their profile page.
                                    if (empty($user->email)) {
                                        $user->email = $user->username .  "@example.com";
                                    }

                                    $user->auth = 'lti';
                                    $user->id = user_create_user($user);

                                    // Add the information to the necessary arrays.
                                    $currentusers[] = $user->id;
                                    $userphotos[$user->id] = $member->user_image;
                                }
                            } else {
                                // If email is empty remove it, so we don't update the user with an empty email.
                                if (empty($user->email)) {
                                    unset($user->email);
                                }

                                $user->id = $dbuser->id;
                                user_update_user($user);

                                // Add the information to the necessary arrays.
                                $currentusers[] = $user->id;
                                $userphotos[$user->id] = $member->user_image;
                            }
                            if ($tool->membersyncmode == \enrol_lti\helper::MEMBER_SYNC_ENROL_AND_UNENROL ||
                                $tool->membersyncmode == \enrol_lti\helper::MEMBER_SYNC_ENROL_NEW) {
                                // Enrol the user in the course.
                                \enrol_lti\helper::enrol_user($tool, $user->id);
                            }
                        }
                    }
                    // Now we check if we have to unenrol users who were not listed.
                    if ($tool->membersyncmode == \enrol_lti\helper::MEMBER_SYNC_ENROL_AND_UNENROL ||
                        $tool->membersyncmode == \enrol_lti\helper::MEMBER_SYNC_UNENROL_MISSING) {
                        // Go through the users and check if any were never listed, if so, remove them.
                        foreach ($ltiusers as $ltiuser) {
                            if (!in_array($ltiuser->userid, $currentusers)) {
                                $instance = new \stdClass();
                                $instance->id = $tool->enrolid;
                                $instance->courseid = $tool->courseid;
                                $instance->enrol = 'lti';
                                $ltiplugin->unenrol_user($instance, $ltiuser->id);
                            }
                        }
                    }
                }
                mtrace("Completed - Synced members for tool '$tool->id' in the course '$tool->courseid'. " .
                     "Processed $usercount users; enrolled $enrolcount members; unenrolled $unenrolcount members.");
                mtrace("");
            }

            // Sync the user profile photos.
            mtrace("Started - Syncing user profile images.");
            $counter = 0;
            if (!empty($userphotos)) {
                foreach ($userphotos as $userid => $url) {
                    if ($url) {
                        $result = \enrol_lti\helper::update_user_profile_image($userid, $url);
                        if ($result === \enrol_lti\helper::PROFILE_IMAGE_UPDATE_SUCCESSFUL) {
                            $counter++;
                            mtrace("Profile image succesfully downloaded and created for user '$userid' from $url.");
                        } else {
                            mtrace($result);
                        }
                    }
                }
            }
            mtrace("Completed - Synced $counter profile images.");
        }
    }
}
