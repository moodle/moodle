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
 * Custom moove extras functions
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\util;

use moodle_url;

/**
 * Class to get some extras info in Moodle.
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class extras {
    /**
     * Returns the buttons displayed at the page header
     *
     * @param \context_course $context
     * @param \stdClass $user
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public static function get_mypublic_headerbuttons($context, $user) {
        global $USER, $CFG;

        $headerbuttons = [];

        // Check to see if we should be displaying a message button.
        if (!empty($CFG->messaging) && $USER->id != $user->id && has_capability('moodle/site:sendmessage', $context)) {
            $iscontact = !empty(\core_message\api::get_contact($USER->id, $user->id)) ? 1 : 0;
            $contacttitle = $iscontact ? 'removecontact' : 'addcontact';
            $contacturlaction = $iscontact ? 'removecontact' : 'addcontact';
            $contactimage = $iscontact ? 'fa fa-user-times' : 'fa fa-address-card';
            $headerbuttons = [
                [
                    'title' => get_string('sendmessage', 'core_message'),
                    'url' => new \moodle_url('/message/index.php', array('id' => $user->id)),
                    'icon' => 'fa fa-comment-o',
                    'class' => 'btn-header btn btn-sm btn-success'
                ],
                [
                    'title' => get_string($contacttitle, 'theme_moove'),
                    'url' => new \moodle_url('/message/index.php', [
                            'user1' => $USER->id,
                            'user2' => $user->id,
                            $contacturlaction => $user->id,
                            'sesskey' => sesskey()]
                    ),
                    'icon' => $contactimage,
                    'class' => 'btn-header btn btn-sm btn-dark ajax-contact-button',
                    'linkattributes' => \core_message\helper::togglecontact_link_params($user, $iscontact),
                ]
            ];

            \core_message\helper::togglecontact_requirejs();
            \core_message\helper::messageuser_requirejs();
        }

        return $headerbuttons;
    }

    /**
     * Returns edit profile url
     *
     * @param \stdClass $user
     * @param int $courseid
     *
     * @return false|moodle_url
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function get_mypublic_editprofile_url($user, $courseid = 1) {
        global $USER;

        $iscurrentuser = $user->id == $USER->id;

        $systemcontext = \context_system::instance();
        $usercontext = \context_user::instance($USER->id);

        // Edit profile.
        if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
            if (($iscurrentuser || is_siteadmin($USER) || !is_siteadmin($user)) && has_capability('moodle/user:update',
                    $systemcontext)) {
                return new moodle_url('/user/editadvanced.php',
                    ['id' => $user->id, 'course' => $courseid, 'returnto' => 'profile']
                );
            }

            if ((has_capability('moodle/user:editprofile', $usercontext) && !is_siteadmin($user))
                || ($iscurrentuser && has_capability('moodle/user:editownprofile', $systemcontext))) {
                $userauthplugin = false;
                if (!empty($user->auth)) {
                    $userauthplugin = get_auth_plugin($user->auth);
                }

                if ($userauthplugin && $userauthplugin->can_edit_profile()) {
                    $url = $userauthplugin->edit_profile_url();
                    if (empty($url)) {
                        if (empty($course)) {
                            return new moodle_url('/user/edit.php', array('id' => $user->id, 'returnto' => 'profile'));
                        }

                         return new moodle_url('/user/edit.php', array('id' => $user->id, 'course' => $course->id,
                                'returnto' => 'profile'));
                    }
                }
            }
        }

        return false;
    }
}
