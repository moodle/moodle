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
 * The main entry point for the external system.
 *
 * @package    enrol_lti
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/enrol/lti/ims-blti/blti.php');

$toolid = required_param('id', PARAM_INT);

// Get the tool.
$tool = \enrol_lti\helper::get_lti_tool($toolid);

// Create the BLTI request.
$ltirequest = new BLTI($tool->secret, false, false);

// Correct launch request.
if ($ltirequest->valid) {
    // Check if the authentication plugin is disabled.
    if (!is_enabled_auth('lti')) {
        print_error('pluginnotenabled', 'auth', '', get_string('pluginname', 'auth_lti'));
        exit();
    }

    // Check if the enrolment plugin is disabled.
    if (!enrol_is_enabled('lti')) {
        print_error('enrolisdisabled', 'enrol_lti');
        exit();
    }

    // Check if the enrolment instance is disabled.
    if ($tool->status != ENROL_INSTANCE_ENABLED) {
        print_error('enrolisdisabled', 'enrol_lti');
        exit();
    }

    // Before we do anything check that the context is valid.
    $context = context::instance_by_id($tool->contextid);

    // Set the user data.
    $user = new stdClass();
    $user->username = \enrol_lti\helper::create_username($ltirequest->info['oauth_consumer_key'], $ltirequest->info['user_id']);
    if (!empty($ltirequest->info['lis_person_name_given'])) {
        $user->firstname = $ltirequest->info['lis_person_name_given'];
    } else {
        $user->firstname = $ltirequest->info['user_id'];
    }
    if (!empty($ltirequest->info['lis_person_name_family'])) {
        $user->lastname = $ltirequest->info['lis_person_name_family'];
    } else {
        $user->lastname = $ltirequest->info['context_id'];
    }

    $user->email = \core_user::clean_field($ltirequest->getUserEmail(), 'email');

    // Get the user data from the LTI consumer.
    $user = \enrol_lti\helper::assign_user_tool_data($tool, $user);

    // Check if the user exists.
    if (!$dbuser = $DB->get_record('user', array('username' => $user->username, 'deleted' => 0))) {
        // If the email was stripped/not set then fill it with a default one. This
        // stops the user from being redirected to edit their profile page.
        if (empty($user->email)) {
            $user->email = $user->username .  "@example.com";
        }

        $user->auth = 'lti';
        $user->id = user_create_user($user);

        // Get the updated user record.
        $user = $DB->get_record('user', array('id' => $user->id));
    } else {
        if (\enrol_lti\helper::user_match($user, $dbuser)) {
            $user = $dbuser;
        } else {
            // If email is empty remove it, so we don't update the user with an empty email.
            if (empty($user->email)) {
                unset($user->email);
            }

            $user->id = $dbuser->id;
            user_update_user($user);

            // Get the updated user record.
            $user = $DB->get_record('user', array('id' => $user->id));
        }
    }

    // Update user image.
    $image = false;
    if (!empty($ltirequest->info['user_image'])) {
        $image = $ltirequest->info['user_image'];
    } else if (!empty($ltirequest->info['custom_user_image'])) {
        $image = $ltirequest->info['custom_user_image'];
    }

    // Check if there is an image to process.
    if ($image) {
        \enrol_lti\helper::update_user_profile_image($user->id, $image);
    }

    // Check if we are an instructor.
    $isinstructor = $ltirequest->isInstructor();

    if ($context->contextlevel == CONTEXT_COURSE) {
        $courseid = $context->instanceid;
        $urltogo = new moodle_url('/course/view.php', array('id' => $courseid));

        // May still be set from previous session, so unset it.
        unset($SESSION->forcepagelayout);
    } else if ($context->contextlevel == CONTEXT_MODULE) {
        $cmid = $context->instanceid;
        $cm = get_coursemodule_from_id(false, $context->instanceid, 0, false, MUST_EXIST);
        $urltogo = new moodle_url('/mod/' . $cm->modname . '/view.php', array('id' => $cm->id));

        // If we are a student in the course module context we do not want to display blocks.
        if (!$isinstructor) {
            // Force the page layout.
            $SESSION->forcepagelayout = 'embedded';
        } else {
            // May still be set from previous session, so unset it.
            unset($SESSION->forcepagelayout);
        }
    } else {
        print_error('invalidcontext');
        exit();
    }

    // Enrol the user in the course with no role.
    $result = \enrol_lti\helper::enrol_user($tool, $user->id);

    // Display an error, if there is one.
    if ($result !== \enrol_lti\helper::ENROLMENT_SUCCESSFUL) {
        print_error($result, 'enrol_lti');
        exit();
    }

    // Give the user the role in the given context.
    $roleid = $isinstructor ? $tool->roleinstructor : $tool->rolelearner;
    role_assign($roleid, $user->id, $tool->contextid);

    // Login user.
    $sourceid = (!empty($ltirequest->info['lis_result_sourcedid'])) ? $ltirequest->info['lis_result_sourcedid'] : '';
    $serviceurl = (!empty($ltirequest->info['lis_outcome_service_url'])) ? $ltirequest->info['lis_outcome_service_url'] : '';

    // Check if we have recorded this user before.
    if ($userlog = $DB->get_record('enrol_lti_users', array('toolid' => $tool->id, 'userid' => $user->id))) {
        if ($userlog->sourceid != $sourceid) {
            $userlog->sourceid = $sourceid;
        }
        if ($userlog->serviceurl != $serviceurl) {
            $userlog->serviceurl = $serviceurl;
        }
        $userlog->lastaccess = time();
        $DB->update_record('enrol_lti_users', $userlog);
    } else {
        // Add the user details so we can use it later when syncing grades and members.
        $userlog = new stdClass();
        $userlog->userid = $user->id;
        $userlog->toolid = $tool->id;
        $userlog->serviceurl = $serviceurl;
        $userlog->sourceid = $sourceid;
        $userlog->consumerkey = $ltirequest->info['oauth_consumer_key'];
        $userlog->consumersecret = $tool->secret;
        $userlog->lastgrade = 0;
        $userlog->lastaccess = time();
        $userlog->timecreated = time();

        if (!empty($ltirequest->info['ext_ims_lis_memberships_url'])) {
            $userlog->membershipsurl = $ltirequest->info['ext_ims_lis_memberships_url'];
        } else {
            $userlog->membershipsurl = '';
        }

        if (!empty($ltirequest->info['ext_ims_lis_memberships_id'])) {
            $userlog->membershipsid = $ltirequest->info['ext_ims_lis_memberships_id'];
        } else {
            $userlog->membershipsid = '';
        }
        $DB->insert_record('enrol_lti_users', $userlog);
    }

    // Finalise the user log in.
    complete_user_login($user);

    if (empty($CFG->allowframembedding)) {
        // Provide an alternative link.
        $stropentool = get_string('opentool', 'enrol_lti');
        echo html_writer::tag('p', get_string('frameembeddingnotenabled', 'enrol_lti'));
        echo html_writer::link($urltogo, $stropentool, array('target' => '_blank'));
    } else {
        // All done, redirect the user to where they want to go.
        redirect($urltogo);
    }
} else {
    echo $ltirequest->message;
}
