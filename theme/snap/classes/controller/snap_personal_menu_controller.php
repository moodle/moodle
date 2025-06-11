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

namespace theme_snap\controller;

use theme_snap\output\core_renderer;

/**
 * Deadlines Controller.
 * Handles requests regarding user deadlines and other CTAs.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class snap_personal_menu_controller extends controller_abstract {
    /**
     * Do any security checks needed for the passed action
     *
     * @param string $action
     */
    public function require_capability($action) {
    }

    /**
     * Get the user's deadlines.
     *
     * @return string
     */
    public function get_deadlines_action() {
        return json_encode([
            'html' => \theme_snap\local::deadlines(),
        ]);
    }

    /**
     * Get forum posts for forums current user is enrolled on.
     *
     * @return string
     */
    public function get_forumposts_action() {
        return json_encode(array(
            'html' => \theme_snap\local::render_recent_forum_activity(),
        ));
    }

    /**
     * Get the user's graded work.
     *
     * @return string
     */
    public function get_graded_action() {
        return json_encode(array(
            'html' => \theme_snap\local::graded(),
        ));
    }

    // BEGIN LSU Course Card Quick Links.
    /**
     * For the coursecard template render quick links on the course cards.
     *
     * @return string - json obj.
     */
    public function get_course_card_quick_links_action() {
        global $PAGE;

        $renderer = $PAGE->get_renderer('theme_snap', 'core', RENDERER_TARGET_GENERAL);
        $courseid = optional_param('courseid', false, PARAM_SEQUENCE);
        $courses = enrol_get_my_courses();
        $quicklinks = $renderer->get_quick_links($courses[$courseid]);

        return json_encode(array(
            'quicklinks' => $quicklinks['quicklinks'],
            'ccqlrender' => $quicklinks['ccqlrender'],
            'courseid' => $courseid
        ));
    }
    // END LSU Course Card Quick Links.

    /**
     * Get the user's messages.
     *
     * @return string
     */
    public function get_messages_action() {
        return json_encode(array(
            'html' => \theme_snap\local::messages(),
        ));
    }

    /**
     * Get the user's grading from courses they teach.
     *
     * @return string
     */
    public function get_grading_action() {
        return json_encode(array(
            'html' => \theme_snap\local::grading(),
        ));
    }

    /**
     * Get user's current login status.
     *
     * @return string
     * @throws \coding_exception
     */
    public function get_loginstatus_action() {
        $failedactionmsg = optional_param('failedactionmsg', null, PARAM_TEXT);
        $loggedin = isloggedin();
        $return = [
            'loggedin' => $loggedin,
        ];
        if (!$loggedin) {
            if (!empty($failedactionmsg)) {
                $return['loggedoutmsg'] = get_string('loggedoutfailmsg', 'theme_snap', $failedactionmsg);
            } else {
                $return['loggedoutmsg'] = get_string('loggedoutmsg', 'theme_snap');
            }
            $return['loggedouttitle'] = get_string('loggedoutmsgtitle', 'theme_snap');
            $return['loggedoutcontinue'] = get_string('continue');
        }
        return json_encode($return);
    }
}
