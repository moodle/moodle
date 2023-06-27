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

defined('MOODLE_INTERNAL') || die();

/**
 * External message popup API
 *
 * @package    message_popup
 * @category   external
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . "/message/lib.php");

/**
 * Message external functions
 *
 * @package    message_popup
 * @category   external
 * @copyright  2016 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_popup_external extends external_api {

    /**
     * Get popup notifications parameters description.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function get_popup_notifications_parameters() {
        return new external_function_parameters(
            array(
                'useridto' => new external_value(PARAM_INT, 'the user id who received the message, 0 for current user'),
                'newestfirst' => new external_value(
                    PARAM_BOOL, 'true for ordering by newest first, false for oldest first',
                    VALUE_DEFAULT, true),
                'limit' => new external_value(PARAM_INT, 'the number of results to return', VALUE_DEFAULT, 0),
                'offset' => new external_value(PARAM_INT, 'offset the result set by a given amount', VALUE_DEFAULT, 0)
            )
        );
    }

    /**
     * Get notifications function.
     *
     * @since  3.2
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @param  int      $useridto           the user id who received the message
     * @param  bool     $newestfirst        true for ordering by newest first, false for oldest first
     * @param  int      $limit              the number of results to return
     * @param  int      $offset             offset the result set by a given amount
     * @return external_description
     */
    public static function get_popup_notifications($useridto, $newestfirst, $limit, $offset) {
        global $USER, $PAGE;

        $params = self::validate_parameters(
            self::get_popup_notifications_parameters(),
            array(
                'useridto' => $useridto,
                'newestfirst' => $newestfirst,
                'limit' => $limit,
                'offset' => $offset,
            )
        );

        $context = context_system::instance();
        self::validate_context($context);

        $useridto = $params['useridto'];
        $newestfirst = $params['newestfirst'];
        $limit = $params['limit'];
        $offset = $params['offset'];
        $issuperuser = has_capability('moodle/site:readallmessages', $context);
        $renderer = $PAGE->get_renderer('core_message');

        if (empty($useridto)) {
            $useridto = $USER->id;
        }

        // Check if the current user is the sender/receiver or just a privileged user.
        if ($useridto != $USER->id and !$issuperuser) {
            throw new moodle_exception('accessdenied', 'admin');
        }

        if (!empty($useridto)) {
            if (!core_user::is_real_user($useridto)) {
                throw new moodle_exception('invaliduser');
            }
        }

        $sort = $newestfirst ? 'DESC' : 'ASC';
        $notifications = \message_popup\api::get_popup_notifications($useridto, $sort, $limit, $offset);
        $notificationcontexts = [];

        if ($notifications) {
            foreach ($notifications as $notification) {

                $notificationoutput = new \message_popup\output\popup_notification($notification);

                $notificationcontext = $notificationoutput->export_for_template($renderer);

                // Keep this for BC.
                $notificationcontext->deleted = false;
                $notificationcontexts[] = $notificationcontext;
            }
        }

        return array(
            'notifications' => $notificationcontexts,
            'unreadcount' => \message_popup\api::count_unread_popup_notifications($useridto),
        );
    }

    /**
     * Get notifications return description.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function get_popup_notifications_returns() {
        return new external_single_structure(
            array(
                'notifications' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id' => new external_value(PARAM_INT, 'Notification id (this is not guaranteed to be unique
                                within this result set)'),
                            'useridfrom' => new external_value(PARAM_INT, 'User from id'),
                            'useridto' => new external_value(PARAM_INT, 'User to id'),
                            'subject' => new external_value(PARAM_TEXT, 'The notification subject'),
                            'shortenedsubject' => new external_value(PARAM_TEXT, 'The notification subject shortened
                                with ellipsis'),
                            'text' => new external_value(PARAM_RAW, 'The message text formated'),
                            'fullmessage' => new external_value(PARAM_RAW, 'The message'),
                            'fullmessageformat' => new external_format_value('fullmessage'),
                            'fullmessagehtml' => new external_value(PARAM_RAW, 'The message in html'),
                            'smallmessage' => new external_value(PARAM_RAW, 'The shorten message'),
                            'contexturl' => new external_value(PARAM_RAW, 'Context URL'),
                            'contexturlname' => new external_value(PARAM_TEXT, 'Context URL link name'),
                            'timecreated' => new external_value(PARAM_INT, 'Time created'),
                            'timecreatedpretty' => new external_value(PARAM_TEXT, 'Time created in a pretty format'),
                            'timeread' => new external_value(PARAM_INT, 'Time read'),
                            'read' => new external_value(PARAM_BOOL, 'notification read status'),
                            'deleted' => new external_value(PARAM_BOOL, 'notification deletion status'),
                            'iconurl' => new external_value(PARAM_URL, 'URL for notification icon'),
                            'component' => new external_value(PARAM_TEXT, 'The component that generated the notification',
                                VALUE_OPTIONAL),
                            'eventtype' => new external_value(PARAM_TEXT, 'The type of notification', VALUE_OPTIONAL),
                            'customdata' => new external_value(PARAM_RAW, 'Custom data to be passed to the message processor.
                                The data here is serialised using json_encode().', VALUE_OPTIONAL),
                        ), 'message'
                    )
                ),
                'unreadcount' => new external_value(PARAM_INT, 'the number of unread message for the given user'),
            )
        );
    }

    /**
     * Get unread notification count parameters description.
     *
     * @return external_function_parameters
     * @since 3.2
     */
    public static function get_unread_popup_notification_count_parameters() {
        return new external_function_parameters(
            array(
                'useridto' => new external_value(PARAM_INT, 'the user id who received the message, 0 for any user', VALUE_REQUIRED),
            )
        );
    }

    /**
     * Get unread notification count function.
     *
     * @since  3.2
     * @throws invalid_parameter_exception
     * @throws moodle_exception
     * @param  int      $useridto       the user id who received the message
     * @return external_description
     */
    public static function get_unread_popup_notification_count($useridto) {
        global $USER;

        $params = self::validate_parameters(
            self::get_unread_popup_notification_count_parameters(),
            array('useridto' => $useridto)
        );

        $context = context_system::instance();
        self::validate_context($context);

        $useridto = $params['useridto'];

        if (!empty($useridto)) {
            if (core_user::is_real_user($useridto)) {
                $userto = core_user::get_user($useridto, '*', MUST_EXIST);
            } else {
                throw new moodle_exception('invaliduser');
            }
        }

        // Check if the current user is the sender/receiver or just a privileged user.
        if ($useridto != $USER->id and !has_capability('moodle/site:readallmessages', $context)) {
            throw new moodle_exception('accessdenied', 'admin');
        }

        return \message_popup\api::count_unread_popup_notifications($useridto);
    }

    /**
     * Get unread popup notification count return description.
     *
     * @return external_single_structure
     * @since 3.2
     */
    public static function get_unread_popup_notification_count_returns() {
        return new external_value(PARAM_INT, 'The count of unread popup notifications');
    }
}
