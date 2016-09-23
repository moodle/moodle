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
        error_log("onLaunch()");
        // TODO Launching tool. Basically tool.php code needs to go here.
        echo "This is a tool";
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
