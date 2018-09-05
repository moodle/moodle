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
 * Badges external API
 *
 * @package    core_badges
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/badgeslib.php');

/**
 * Badges external functions
 *
 * @package    core_badges
 * @category   external
 * @copyright  2016 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
class core_badges_external extends external_api {

    /**
     * Describes the parameters for get_user_badges.
     *
     * @return external_function_parameters
     * @since Moodle 3.1
     */
    public static function get_user_badges_parameters() {
        return new external_function_parameters (
            array(
                'userid' => new external_value(PARAM_INT, 'Badges only for this user id, empty for current user', VALUE_DEFAULT, 0),
                'courseid' => new external_value(PARAM_INT, 'Filter badges by course id, empty all the courses', VALUE_DEFAULT, 0),
                'page' => new external_value(PARAM_INT, 'The page of records to return.', VALUE_DEFAULT, 0),
                'perpage' => new external_value(PARAM_INT, 'The number of records to return per page', VALUE_DEFAULT, 0),
                'search' => new external_value(PARAM_RAW, 'A simple string to search for', VALUE_DEFAULT, ''),
                'onlypublic' => new external_value(PARAM_BOOL, 'Whether to return only public badges', VALUE_DEFAULT, false),
            )
        );
    }

    /**
     * Returns the list of badges awarded to a user.
     *
     * @param int $userid       user id
     * @param int $courseid     course id
     * @param int $page         page of records to return
     * @param int $perpage      number of records to return per page
     * @param string  $search   a simple string to search for
     * @param bool $onlypublic  whether to return only public badges
     * @return array array containing warnings and the awarded badges
     * @since  Moodle 3.1
     * @throws moodle_exception
     */
    public static function get_user_badges($userid = 0, $courseid = 0, $page = 0, $perpage = 0, $search = '', $onlypublic = false) {
        global $CFG, $USER;

        $warnings = array();

        $params = array(
            'userid' => $userid,
            'courseid' => $courseid,
            'page' => $page,
            'perpage' => $perpage,
            'search' => $search,
            'onlypublic' => $onlypublic,
        );
        $params = self::validate_parameters(self::get_user_badges_parameters(), $params);

        if (empty($CFG->enablebadges)) {
            throw new moodle_exception('badgesdisabled', 'badges');
        }

        if (empty($CFG->badges_allowcoursebadges) && $params['courseid'] != 0) {
            throw new moodle_exception('coursebadgesdisabled', 'badges');
        }

        // Default value for userid.
        if (empty($params['userid'])) {
            $params['userid'] = $USER->id;
        }

        // Validate the user.
        $user = core_user::get_user($params['userid'], '*', MUST_EXIST);
        core_user::require_active_user($user);

        $usercontext = context_user::instance($user->id);
        self::validate_context($usercontext);

        if ($USER->id != $user->id) {
            require_capability('moodle/badges:viewotherbadges', $usercontext);
            // We are looking other user's badges, we must retrieve only public badges.
            $params['onlypublic'] = true;
        }

        $userbadges = badges_get_user_badges($user->id, $params['courseid'], $params['page'], $params['perpage'], $params['search'],
                                                $params['onlypublic']);

        $result = array();
        $result['badges'] = array();
        $result['warnings'] = $warnings;

        foreach ($userbadges as $badge) {
            $context = ($badge->type == BADGE_TYPE_SITE) ? context_system::instance() : context_course::instance($badge->courseid);
            $badge->badgeurl = moodle_url::make_webservice_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/',
                                                                            'f1')->out(false);
            // Return all the information if we are requesting our own badges.
            // Or, if we have permissions for configuring badges in the badge context.
            if ($USER->id == $user->id or has_capability('moodle/badges:configuredetails', $context)) {
                $result['badges'][] = (array) $badge;
            } else {
                $result['badges'][] = array(
                    'name' => $badge->name,
                    'description' => $badge->description,
                    'badgeurl' => $badge->badgeurl,
                    'issuername' => $badge->issuername,
                    'issuerurl' => $badge->issuerurl,
                    'issuercontact' => $badge->issuercontact,
                    'uniquehash' => $badge->uniquehash,
                    'dateissued' => $badge->dateissued,
                    'dateexpire' => $badge->dateexpire,
                );
            }
        }

        return $result;
    }

    /**
     * Describes the get_user_badges return value.
     *
     * @return external_single_structure
     * @since Moodle 3.1
     */
    public static function get_user_badges_returns() {
        return new external_single_structure(
            array(
                'badges' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Badge id.', VALUE_OPTIONAL),
                            'name' => new external_value(PARAM_TEXT, 'Badge name.'),
                            'description' => new external_value(PARAM_NOTAGS, 'Badge description.'),
                            'badgeurl' => new external_value(PARAM_URL, 'Badge URL.'),
                            'timecreated' => new external_value(PARAM_INT, 'Time created.', VALUE_OPTIONAL),
                            'timemodified' => new external_value(PARAM_INT, 'Time modified.', VALUE_OPTIONAL),
                            'usercreated' => new external_value(PARAM_INT, 'User created.', VALUE_OPTIONAL),
                            'usermodified' => new external_value(PARAM_INT, 'User modified.', VALUE_OPTIONAL),
                            'issuername' => new external_value(PARAM_NOTAGS, 'Issuer name.'),
                            'issuerurl' => new external_value(PARAM_URL, 'Issuer URL.'),
                            'issuercontact' => new external_value(PARAM_RAW, 'Issuer contact.'),
                            'expiredate' => new external_value(PARAM_INT, 'Expire date.', VALUE_OPTIONAL),
                            'expireperiod' => new external_value(PARAM_INT, 'Expire period.', VALUE_OPTIONAL),
                            'type' => new external_value(PARAM_INT, 'Type.', VALUE_OPTIONAL),
                            'courseid' => new external_value(PARAM_INT, 'Course id.', VALUE_OPTIONAL),
                            'message' => new external_value(PARAM_RAW, 'Message.', VALUE_OPTIONAL),
                            'messagesubject' => new external_value(PARAM_TEXT, 'Message subject.', VALUE_OPTIONAL),
                            'attachment' => new external_value(PARAM_INT, 'Attachment.', VALUE_OPTIONAL),
                            'status' => new external_value(PARAM_INT, 'Status.', VALUE_OPTIONAL),
                            'issuedid' => new external_value(PARAM_INT, 'Issued id.', VALUE_OPTIONAL),
                            'uniquehash' => new external_value(PARAM_ALPHANUM, 'Unique hash.'),
                            'dateissued' => new external_value(PARAM_INT, 'Date issued.'),
                            'dateexpire' => new external_value(PARAM_INT, 'Date expire.'),
                            'visible' => new external_value(PARAM_INT, 'Visible.', VALUE_OPTIONAL),
                        )
                    )
                ),
                'warnings' => new external_warnings(),
            )
        );
    }
}
