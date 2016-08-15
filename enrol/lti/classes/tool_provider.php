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
 * Extends the IMS Tool provider library for the LTI enrolment.
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_lti;

defined('MOODLE_INTERNAL') || die;

use IMSGlobal\LTI\Profile;
use IMSGlobal\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\DataConnector;
require_once($CFG->dirroot . '/user/lib.php');

/**
 * Extends the IMS Tool provider library for the LTI enrolment.
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_provider extends ToolProvider\ToolProvider {

    private function stripBaseUrl($url) {
        if (substr($url, 0, strlen($this->baseUrl)) == $this->baseUrl) {
            return substr($url, strlen($this->baseUrl));
        }
        # TODO Oh no this will break!!
        // TODO see if we can override something so it can serve icons and urls etc that are not from base Url
        print_object($icon);
        print_object($this->baseUrl);
        print_object("no good!!");
        die;
        return null;
    }

    function __construct($toolid) {
        global $CFG, $SITE;

        $token = \enrol_lti\helper::generate_tool_token($toolid);

        $this->debugMode = $CFG->debugdeveloper;
        $tool = \enrol_lti\helper::get_lti_tool($toolid);
        $this->tool = $tool;

        $dataconnector = new data_connector();
        parent::__construct($dataconnector);

        #$this->baseUrl = $CFG->wwwroot . '/enrol/lti/proxy.php';
        $this->baseUrl = $CFG->wwwroot;
        $toolpath = $this->stripBaseUrl(\enrol_lti\helper::get_proxy_url($tool));

        $vendorid = $SITE->shortname;
        $vendorname = $SITE->fullname;
        $vendordescription = trim(html_to_text($SITE->summary));
        $this->vendor = new Profile\Item($vendorid, $vendorname, $vendordescription, $CFG->wwwroot);

        $name = \enrol_lti\helper::get_name($tool);
        $description = \enrol_lti\helper::get_description($tool);
        $icon = \enrol_lti\helper::get_icon($tool)->out();
        // Strip the baseUrl off the icon path.
        $icon = $this->stripBaseUrl($icon);

        $this->product = new Profile\Item(
            $token,
            $name,
            $description,
            \enrol_lti\helper::get_proxy_url($tool),
            '1.0'
        );

        $requiredmessages = array(
            new Profile\Message(
                'basic-lti-launch-request',
                $toolpath,
                array(
                   'Context.id',
                   'CourseSection.title',
                   'CourseSection.label',
                   'CourseSection.sourcedId',
                   'CourseSection.longDescription',
                   'CourseSection.timeFrame.begin',
                   'ResourceLink.id',
                   'ResourceLink.title',
                   'ResourceLink.description',
                   'User.id',
                   'User.username',
                   'Person.name.full',
                   'Person.name.given',
                   'Person.name.family',
                   'Person.email.primary',
                   'Person.sourcedId',
                   'Person.name.middle',
                   'Person.address.street1',
                   'Person.address.locality',
                   'Person.address.country',
                   'Person.address.timezone',
                   'Person.phone.primary',
                   'Person.phone.mobile',
                   'Person.webaddress',
                   'Membership.role',
                   'Result.sourcedId',
                   'Result.autocreate'
                )
            )
        );
        $optionalmessages = array(
            #new Profile\Message('ContentItemSelectionRequest', $toolpath, array('User.id', 'Membership.role')),
            #new Profile\Message('DashboardRequest', $toolpath, array('User.id'), array('a' => 'User.id'), array('b' => 'User.id'))
        );

        $this->resourceHandlers[] = new Profile\ResourceHandler(
             new Profile\Item(
                 $token,
                 \enrol_lti\helper::get_name($tool),
                 $description
             ),
             $icon,
             $requiredmessages,
             $optionalmessages
        );

        $this->requiredServices[] = new Profile\ServiceDefinition(array('application/vnd.ims.lti.v2.toolproxy+json'), array('POST'));

        # TODO should the other requests be here?
        $this->setParameterConstraint('oauth_consumer_key', TRUE, 50, array('basic-lti-launch-request', 'ContentItemSelectionRequest', 'DashboardRequest'));
        $this->setParameterConstraint('resource_link_id', TRUE, 50, array('basic-lti-launch-request'));
        $this->setParameterConstraint('user_id', TRUE, 50, array('basic-lti-launch-request'));
        $this->setParameterConstraint('roles', TRUE, NULL, array('basic-lti-launch-request'));

    }

    function onError() {
        error_log("onError()");
        error_log($this->reason);
        $message = $this->message;
        if ($this->debugMode && !empty($this->reason)) {
            $message = $this->reason;
        }

        $this->errorOutput = ''; # TODO remove this
        \core\notification::error($message); # TODO is it better to have a generic, yet translatable error?
    }

    function onLaunch() {
        global $DB, $SESSION, $CFG;

        // Before we do anything check that the context is valid.
        $tool = $this->tool;
        $context = \context::instance_by_id($tool->contextid);

        // Set the user data.
        $user = new \stdClass();
        $user->username = \enrol_lti\helper::create_username($this->consumer->getKey(), $this->user->ltiUserId);
        if (!empty($this->user->firstname)) {
            $user->firstname = $this->user->firstname;
        } else {
            $user->firstname = $this->user->getRecordId();
        }
        if (!empty($this->user->lastname)) {
            $user->lastname = $this->user->lastname;
        } else {
            // TODO is this actually the same as $ltirequest->info['context_id'];
            $user->lastname = $this->tool->contextid;
        }

        $user->email = \core_user::clean_field($this->user->email, 'email');

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
            $user->id = \user_create_user($user);

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
                \user_update_user($user);

                // Get the updated user record.
                $user = $DB->get_record('user', array('id' => $user->id));
            }
        }

        // Update user image. TODO
        $image = false;
        if (!empty($this->resourceLink->getSetting('user_image'))) {
            $image = $this->resourceLink->getSetting('user_image');
        } else if (!empty($this->resourceLink->getSetting('custom_user_image'))) {
            $image = $this->resourceLink->getSetting('custom_user_image');
        }

        // Check if there is an image to process.
        if ($image) {
            \enrol_lti\helper::update_user_profile_image($user->id, $image);
        }

        // Check if we are an instructor.
        $isinstructor = $this->user->isStaff() || $this->user->isAdmin();

        if ($context->contextlevel == CONTEXT_COURSE) {
            $courseid = $context->instanceid;
            $urltogo = new \moodle_url('/course/view.php', array('id' => $courseid));

            // May still be set from previous session, so unset it.
            unset($SESSION->forcepagelayout);
        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $cmid = $context->instanceid;
            $cm = get_coursemodule_from_id(false, $context->instanceid, 0, false, MUST_EXIST);
            $urltogo = new \moodle_url('/mod/' . $cm->modname . '/view.php', array('id' => $cm->id));

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
        $sourceid = $this->user->ltiResultSourcedId;
        $serviceurl = $this->resourceLink->getSetting('lis_outcome_service_url');

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
            $userlog = new \stdClass();
            $userlog->userid = $user->id;
            $userlog->toolid = $tool->id;
            $userlog->serviceurl = $serviceurl;
            $userlog->sourceid = $sourceid;
            $userlog->consumerkey = $this->consumer->getKey();
            $userlog->consumersecret = $tool->secret;
            $userlog->lastgrade = 0;
            $userlog->lastaccess = time();
            $userlog->timecreated = time();
            $userlog->membershipsurl = $this->resourceLink->getSetting('ext_ims_lis_memberships_url');
            $userlog->membershipsid = $this->resourceLink->getSetting('ext_ims_lis_memberships_id');

            $DB->insert_record('enrol_lti_users', $userlog);
        }

        // Finalise the user log in.
        complete_user_login($user);

        if (empty($CFG->allowframembedding)) {
            // Provide an alternative link.
            $stropentool = get_string('opentool', 'enrol_lti');
            echo \html_writer::tag('p', get_string('frameembeddingnotenabled', 'enrol_lti'));
            echo \html_writer::link($urltogo, $stropentool, array('target' => '_blank'));
        } else {
            // All done, redirect the user to where they want to go.
            redirect($urltogo);
        }
    }
    function onRegister() {
        global $OUTPUT;

        $returnurl = $_POST['launch_presentation_return_url'];
        if (strpos($returnurl, '?') === false) {
            $separator = '?';
        } else {
            $separator = '&';
        }
        $guid = $this->consumer->getKey();
        $returnurl = $returnurl . $separator . 'lti_msg=Successful+registration';
        $returnurl = $returnurl . '&status=success';
        $returnurl = $returnurl . "&tool_proxy_guid=$guid";
        $ok = $this->doToolProxyService($_POST['tc_profile_url']); # TODO only do this right before registering.

        echo $OUTPUT->render_from_template("enrol_lti/proxy_registration", array("returnurl" => $returnurl)); # TODO move out output

    }
}
