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

use context;
use core\notification;
use core_user;
use enrol_lti\output\registration;
use html_writer;
use IMSGlobal\LTI\Profile\Item;
use IMSGlobal\LTI\Profile\Message;
use IMSGlobal\LTI\Profile\ResourceHandler;
use IMSGlobal\LTI\Profile\ServiceDefinition;
use IMSGlobal\LTI\ToolProvider\ToolProvider;
use moodle_exception;
use moodle_url;
use stdClass;

require_once($CFG->dirroot . '/user/lib.php');

/**
 * Extends the IMS Tool provider library for the LTI enrolment.
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_provider extends ToolProvider {

    /**
     * @var stdClass $tool The object representing the enrol instance providing this LTI tool
     */
    protected $tool;

    /**
     * Remove $this->baseUrl (wwwroot) from a given url string and return it.
     *
     * @param string $url The url from which to remove the base url
     * @return string|null A string of the relative path to the url, or null if it couldn't be determined.
     */
    protected function strip_base_url($url) {
        if (substr($url, 0, strlen($this->baseUrl)) == $this->baseUrl) {
            return substr($url, strlen($this->baseUrl));
        }
        return null;
    }

    /**
     * Create a new instance of tool_provider to handle all the LTI tool provider interactions.
     *
     * @param int $toolid The id of the tool to be provided.
     */
    public function __construct($toolid) {
        global $CFG, $SITE;

        $token = helper::generate_proxy_token($toolid);

        $tool = helper::get_lti_tool($toolid);
        $this->tool = $tool;

        $dataconnector = new data_connector();
        parent::__construct($dataconnector);

        // Override debugMode and set to the configured value.
        $this->debugMode = $CFG->debugdeveloper;

        $this->baseUrl = $CFG->wwwroot;
        $toolpath = helper::get_launch_url($toolid);
        $toolpath = $this->strip_base_url($toolpath);

        $vendorid = $SITE->shortname;
        $vendorname = $SITE->fullname;
        $vendordescription = trim(html_to_text($SITE->summary));
        $this->vendor = new Item($vendorid, $vendorname, $vendordescription, $CFG->wwwroot);

        $name = helper::get_name($tool);
        $description = helper::get_description($tool);
        $icon = helper::get_icon($tool)->out();
        $icon = $this->strip_base_url($icon);

        $this->product = new Item(
            $token,
            $name,
            $description,
            helper::get_proxy_url($tool),
            '1.0'
        );

        $requiredmessages = [
            new Message(
                'basic-lti-launch-request',
                $toolpath,
                [
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
                ]
            )
        ];
        $optionalmessages = [
        ];

        $this->resourceHandlers[] = new ResourceHandler(
             new Item(
                 $token,
                 helper::get_name($tool),
                 $description
             ),
             $icon,
             $requiredmessages,
             $optionalmessages
        );

        $this->requiredServices[] = new ServiceDefinition(['application/vnd.ims.lti.v2.toolproxy+json'], ['POST']);
        $this->requiredServices[] = new ServiceDefinition(['application/vnd.ims.lis.v2.membershipcontainer+json'], ['GET']);
    }

    /**
     * Override onError for custom error handling.
     * @return void
     */
    protected function onError() {
        global $OUTPUT;

        $message = $this->message;
        if ($this->debugMode && !empty($this->reason)) {
            $message = $this->reason;
        }

        // Display the error message from the provider's side if the consumer has not specified a URL to pass the error to.
        if (empty($this->returnUrl)) {
            $this->errorOutput = $OUTPUT->notification(get_string('failedrequest', 'enrol_lti', ['reason' => $message]), 'error');
        }
    }

    /**
     * Override onLaunch with tool logic.
     * @return void
     */
    protected function onLaunch() {
        global $DB, $SESSION, $CFG;

        // Check for valid consumer.
        if (empty($this->consumer) || $this->dataConnector->loadToolConsumer($this->consumer) === false) {
            $this->ok = false;
            $this->message = get_string('invalidtoolconsumer', 'enrol_lti');
            return;
        }

        $url = helper::get_launch_url($this->tool->id);
        // If a tool proxy has been stored for the current consumer trying to access a tool,
        // check that the tool is being launched from the correct url.
        $correctlaunchurl = false;
        if (!empty($this->consumer->toolProxy)) {
            $proxy = json_decode($this->consumer->toolProxy);
            $handlers = $proxy->tool_profile->resource_handler;
            foreach ($handlers as $handler) {
                foreach ($handler->message as $message) {
                    $handlerurl = new moodle_url($message->path);
                    $fullpath = $handlerurl->out(false);
                    if ($message->message_type == "basic-lti-launch-request" && $fullpath == $url) {
                        $correctlaunchurl = true;
                        break 2;
                    }
                }
            }
        } else if ($this->tool->secret == $this->consumer->secret) {
            // Test if the LTI1 secret for this tool is being used. Then we know the correct tool is being launched.
            $correctlaunchurl = true;
        }
        if (!$correctlaunchurl) {
            $this->ok = false;
            $this->message = get_string('invalidrequest', 'enrol_lti');
            return;
        }

        // Before we do anything check that the context is valid.
        $tool = $this->tool;
        $context = context::instance_by_id($tool->contextid);

        // Set the user data.
        $user = new stdClass();
        $user->username = helper::create_username($this->consumer->getKey(), $this->user->ltiUserId);
        if (!empty($this->user->firstname)) {
            $user->firstname = $this->user->firstname;
        } else {
            $user->firstname = $this->user->getRecordId();
        }
        if (!empty($this->user->lastname)) {
            $user->lastname = $this->user->lastname;
        } else {
            $user->lastname = $this->tool->contextid;
        }

        $user->email = core_user::clean_field($this->user->email, 'email');

        // Get the user data from the LTI consumer.
        $user = helper::assign_user_tool_data($tool, $user);

        // Check if the user exists.
        if (!$dbuser = $DB->get_record('user', ['username' => $user->username, 'deleted' => 0])) {
            // If the email was stripped/not set then fill it with a default one. This
            // stops the user from being redirected to edit their profile page.
            if (empty($user->email)) {
                $user->email = $user->username .  "@example.com";
            }

            $user->auth = 'lti';
            $user->id = \user_create_user($user);

            // Get the updated user record.
            $user = $DB->get_record('user', ['id' => $user->id]);
        } else {
            if (helper::user_match($user, $dbuser)) {
                $user = $dbuser;
            } else {
                // If email is empty remove it, so we don't update the user with an empty email.
                if (empty($user->email)) {
                    unset($user->email);
                }

                $user->id = $dbuser->id;
                \user_update_user($user);

                // Get the updated user record.
                $user = $DB->get_record('user', ['id' => $user->id]);
            }
        }

        // Update user image.
        if (isset($this->user) && isset($this->user->image) && !empty($this->user->image)) {
            $image = $this->user->image;
        } else {
            // Use custom_user_image parameter as a fallback.
            $image = $this->resourceLink->getSetting('custom_user_image');
        }

        // Check if there is an image to process.
        if ($image) {
            helper::update_user_profile_image($user->id, $image);
        }

        // Check if we need to force the page layout to embedded.
        $isforceembed = $this->resourceLink->getSetting('custom_force_embed') == 1;

        // Check if we are an instructor.
        $isinstructor = $this->user->isStaff() || $this->user->isAdmin();

        if ($context->contextlevel == CONTEXT_COURSE) {
            $courseid = $context->instanceid;
            $urltogo = new moodle_url('/course/view.php', ['id' => $courseid]);

        } else if ($context->contextlevel == CONTEXT_MODULE) {
            $cm = get_coursemodule_from_id(false, $context->instanceid, 0, false, MUST_EXIST);
            $urltogo = new moodle_url('/mod/' . $cm->modname . '/view.php', ['id' => $cm->id]);

            // If we are a student in the course module context we do not want to display blocks.
            if (!$isforceembed && !$isinstructor) {
                $isforceembed = true;
            }
        } else {
            throw new \moodle_exception('invalidcontext');
            exit();
        }

        // Force page layout to embedded if necessary.
        if ($isforceembed) {
            $SESSION->forcepagelayout = 'embedded';
        } else {
            // May still be set from previous session, so unset it.
            unset($SESSION->forcepagelayout);
        }

        // Enrol the user in the course with no role.
        $result = helper::enrol_user($tool, $user->id);

        // Display an error, if there is one.
        if ($result !== helper::ENROLMENT_SUCCESSFUL) {
            throw new \moodle_exception($result, 'enrol_lti');
            exit();
        }

        // Give the user the role in the given context.
        $roleid = $isinstructor ? $tool->roleinstructor : $tool->rolelearner;
        role_assign($roleid, $user->id, $tool->contextid);

        // Login user.
        $sourceid = $this->user->ltiResultSourcedId;
        $serviceurl = $this->resourceLink->getSetting('lis_outcome_service_url');

        // Check if we have recorded this user before.
        if ($userlog = $DB->get_record('enrol_lti_users', ['toolid' => $tool->id, 'userid' => $user->id])) {
            if ($userlog->sourceid != $sourceid) {
                $userlog->sourceid = $sourceid;
            }
            if ($userlog->serviceurl != $serviceurl) {
                $userlog->serviceurl = $serviceurl;
            }
            if (empty($userlog->consumersecret)) {
                $userlog->consumersecret = $this->consumer->secret;
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
            $userlog->consumerkey = $this->consumer->getKey();
            $userlog->consumersecret = $this->consumer->secret;
            $userlog->lastgrade = 0;
            $userlog->lastaccess = time();
            $userlog->timecreated = time();
            $userlog->membershipsurl = $this->resourceLink->getSetting('ext_ims_lis_memberships_url');
            $userlog->membershipsid = $this->resourceLink->getSetting('ext_ims_lis_memberships_id');

            $DB->insert_record('enrol_lti_users', $userlog);
        }

        // Finalise the user log in.
        complete_user_login($user);

        // Everything's good. Set appropriate OK flag and message values.
        $this->ok = true;
        $this->message = get_string('success');

        if (empty($CFG->allowframembedding)) {
            // Provide an alternative link.
            $stropentool = get_string('opentool', 'enrol_lti');
            echo html_writer::tag('p', get_string('frameembeddingnotenabled', 'enrol_lti'));
            echo html_writer::link($urltogo, $stropentool, ['target' => '_blank']);
        } else {
            // All done, redirect the user to where they want to go.
            redirect($urltogo);
        }
    }

    /**
     * Override onRegister with registration code.
     */
    protected function onRegister() {
        global $PAGE;

        if (empty($this->consumer)) {
            $this->ok = false;
            $this->message = get_string('invalidtoolconsumer', 'enrol_lti');
            return;
        }

        if (empty($this->returnUrl)) {
            $this->ok = false;
            $this->message = get_string('returnurlnotset', 'enrol_lti');
            return;
        }

        if ($this->doToolProxyService()) {
            // Map tool consumer and published tool, if necessary.
            $this->map_tool_to_consumer();

            // Indicate successful processing in message.
            $this->message = get_string('successfulregistration', 'enrol_lti');

            // Prepare response.
            $returnurl = new moodle_url($this->returnUrl);
            $returnurl->param('lti_msg', get_string("successfulregistration", "enrol_lti"));
            $returnurl->param('status', 'success');
            $guid = $this->consumer->getKey();
            $returnurl->param('tool_proxy_guid', $guid);

            $returnurlout = $returnurl->out(false);

            $registration = new registration($returnurlout);
            $output = $PAGE->get_renderer('enrol_lti');
            echo $output->render($registration);

        } else {
            // Tell the consumer that the registration failed.
            $this->ok = false;
            $this->message = get_string('couldnotestablishproxy', 'enrol_lti');
        }
    }

    /**
     * Performs mapping of the tool consumer to a published tool.
     *
     * @throws moodle_exception
     */
    public function map_tool_to_consumer() {
        global $DB;

        if (empty($this->consumer)) {
            throw new moodle_exception('invalidtoolconsumer', 'enrol_lti');
        }

        // Map the consumer to the tool.
        $mappingparams = [
            'toolid' => $this->tool->id,
            'consumerid' => $this->consumer->getRecordId()
        ];
        $mappingexists = $DB->record_exists('enrol_lti_tool_consumer_map', $mappingparams);
        if (!$mappingexists) {
            $DB->insert_record('enrol_lti_tool_consumer_map', (object) $mappingparams);
        }
    }
}
