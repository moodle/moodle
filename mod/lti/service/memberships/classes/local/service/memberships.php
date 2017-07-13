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
 * This file contains a class definition for the Memberships service
 *
 * @package    ltiservice_memberships
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @author     Stephen Vickers
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace ltiservice_memberships\local\service;

defined('MOODLE_INTERNAL') || die();

/**
 * A service implementing Memberships.
 *
 * @package    ltiservice_memberships
 * @since      Moodle 3.0
 * @copyright  2015 Vital Source Technologies http://vitalsource.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class memberships extends \mod_lti\local\ltiservice\service_base {

    /** Default prefix for context-level roles */
    const CONTEXT_ROLE_PREFIX = 'http://purl.imsglobal.org/vocab/lis/v2/membership#';
    /** Context-level role for Instructor */
    const CONTEXT_ROLE_INSTRUCTOR = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Instructor';
    /** Context-level role for Learner */
    const CONTEXT_ROLE_LEARNER = 'http://purl.imsglobal.org/vocab/lis/v2/membership#Learner';
    /** Capability used to identify Instructors */
    const INSTRUCTOR_CAPABILITY = 'moodle/course:manageactivities';

    /**
     * Class constructor.
     */
    public function __construct() {

        parent::__construct();
        $this->id = 'memberships';
        $this->name = get_string('servicename', 'ltiservice_memberships');

    }

    /**
     * Get the resources for this service.
     *
     * @return array
     */
    public function get_resources() {

        if (empty($this->resources)) {
            $this->resources = array();
            $this->resources[] = new \ltiservice_memberships\local\resource\contextmemberships($this);
            $this->resources[] = new \ltiservice_memberships\local\resource\linkmemberships($this);
        }

        return $this->resources;

    }

    /**
     * Get the JSON for members.
     *
     * @param \mod_lti\local\ltiservice\resource_base $resource       Resource handling the request
     * @param \context_course   $context    Course context
     * @param string            $id         Course ID
     * @param object            $tool       Tool instance object
     * @param string            $role       User role requested (empty if none)
     * @param int               $limitfrom  Position of first record to be returned
     * @param int               $limitnum   Maximum number of records to be returned
     * @param object            $lti        LTI instance record
     * @param info_module       $info       Conditional availability information for LTI instance (null if context-level request)
     *
     * @return array
     */
    public static function get_users_json($resource, $context, $id, $tool, $role, $limitfrom, $limitnum, $lti, $info) {

        $withcapability = '';
        $exclude = array();
        if (!empty($role)) {
            if ((strpos($role, 'http://') !== 0) && (strpos($role, 'https://') !== 0)) {
                $role = self::CONTEXT_ROLE_PREFIX . $role;
            }
            if ($role === self::CONTEXT_ROLE_INSTRUCTOR) {
                $withcapability = self::INSTRUCTOR_CAPABILITY;
            } else if ($role === self::CONTEXT_ROLE_LEARNER) {
                $exclude = array_keys(get_enrolled_users($context, self::INSTRUCTOR_CAPABILITY, 0, 'u.id',
                                                         null, null, null, true));
            }
        }
        $users = get_enrolled_users($context, $withcapability, 0, 'u.*', null, $limitfrom, $limitnum, true);
        if (count($users) < $limitnum) {
            $limitfrom = 0;
            $limitnum = 0;
        }
        $json = self::users_to_json($resource, $users, $id, $tool, $exclude, $limitfrom, $limitnum, $lti, $info);

        return $json;

    }

    /**
     * Get the JSON representation of the users.
     *
     * Note that when a limit is set and the exclude array is not empty, then the number of memberships
     * returned may be less than the limit.
     *
     * @param \mod_lti\local\ltiservice\resource_base $resource       Resource handling the request
     * @param array  $users               Array of user records
     * @param string $id                  Course ID
     * @param object $tool                Tool instance object
     * @param array  $exclude             Array of user records to be excluded from the response
     * @param int    $limitfrom           Position of first record to be returned
     * @param int    $limitnum            Maximum number of records to be returned
     * @param object $lti                 LTI instance record
     * @param \core_availability\info_module  $info     Conditional availability information for LTI instance
     *
     * @return string
     */
    private static function users_to_json($resource, $users, $id, $tool, $exclude, $limitfrom, $limitnum,
                                         $lti, $info) {

        $nextpage = 'null';
        if ($limitnum > 0) {
            $limitfrom += $limitnum;
            $nextpage = "\"{$resource->get_endpoint()}?limit={$limitnum}&amp;from={$limitfrom}\"";
        }
        $json = <<< EOD
{
  "@context" : "http://purl.imsglobal.org/ctx/lis/v2/MembershipContainer",
  "@type" : "Page",
  "@id" : "{$resource->get_endpoint()}",
  "nextPage" : {$nextpage},
  "pageOf" : {
    "@type" : "LISMembershipContainer",
    "membershipSubject" : {
      "@type" : "Context",
      "contextId" : "{$id}",
      "membership" : [

EOD;
        $enabledcapabilities = lti_get_enabled_capabilities($tool);
        $sep = '        ';
        foreach ($users as $user) {
            $include = !in_array($user->id, $exclude);
            if ($include && !empty($info)) {
                $include = $info->is_user_visible($info->get_course_module(), $user->id);
            }
            if ($include) {
                $member = new \stdClass();
                if (in_array('User.id', $enabledcapabilities)) {
                    $member->userId = $user->id;
                }
                if (in_array('Person.sourcedId', $enabledcapabilities)) {
                    $member->sourcedId = format_string($user->idnumber);
                }
                if (in_array('Person.name.full', $enabledcapabilities)) {
                    $member->name = format_string("{$user->firstname} {$user->lastname}");
                }
                if (in_array('Person.name.given', $enabledcapabilities)) {
                    $member->givenName = format_string($user->firstname);
                }
                if (in_array('Person.name.family', $enabledcapabilities)) {
                    $member->familyName = format_string($user->lastname);
                }
                if (in_array('Person.email.primary', $enabledcapabilities)) {
                    $member->email = format_string($user->email);
                }
                if (in_array('Result.sourcedId', $enabledcapabilities) && !empty($lti) && !empty($lti->servicesalt)) {
                    $member->resultSourcedId = json_encode(lti_build_sourcedid($lti->id, $user->id, $lti->servicesalt,
                                                           $lti->typeid));
                }
                $roles = explode(',', lti_get_ims_role($user->id, null, $id, true));

                $membership = new \stdClass();
                $membership->status = 'Active';
                $membership->member = $member;
                $membership->role = $roles;

                $json .= $sep . json_encode($membership);
                $sep = ",\n        ";
            }

        }

        $json .= <<< EOD

      ]
    }
  }
}
EOD;

        return $json;

    }

}
