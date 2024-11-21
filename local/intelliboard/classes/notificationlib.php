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
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");

class local_intelliboard_notificationlib extends external_api {
    public static function send_notifications_parameters() {
        return new external_function_parameters(
            array(
                'notifications'  => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'id'         => new external_value(PARAM_INT, 'Notification id'),
                            'type'       => new external_value(PARAM_INT, 'Notification type'),
                            'name'       => new external_value(PARAM_TEXT, 'Notification name'),
                            'userid'     => new external_value(PARAM_INT, 'User that created notification'),
                            'email'      => new external_value(PARAM_TEXT, 'Emails where this notification should to go'),
                            'cc'         => new external_value(PARAM_TEXT, 'Copy emails where this notification should to go'),
                            'subject'    => new external_value(PARAM_TEXT, 'Notification subject'),
                            'message'    => new external_value(PARAM_RAW, 'Notification message'),
                            'attachment' => new external_value(PARAM_TEXT, 'Notification attachment'),
                            'params'     => new external_value(PARAM_TEXT, 'Notification dynamic params'),
                            'tags'       => new external_value(PARAM_TEXT, 'Notification tags'),
                            'frequency'  => new external_value(PARAM_INT, 'Notification frequency'),
                        )
                    )
                ),
                'params' => new external_single_structure(
                    array(
                        'learner_roles' => new external_value(PARAM_SEQUENCE, 'Learner Roles'),
                    )
                )
            )
        );
    }

    public static function send_notifications($notifications, $params) {
        $notifications = array_map(function($notification) {
            foreach (array('params', 'tags', 'email', 'cc') as $key) {
                $notification[$key] = json_decode($notification[$key], true);
            }
            return $notification;
        }, $notifications);

        $notification = new local_intelliboard_notification();
        $notification->send_notifications($notifications, array(), $params);

        return array('state' => true);
    }

    public static function send_notifications_returns() {
        return new external_single_structure(
            array(
                'state' => new external_value(PARAM_BOOL, 'State'),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function save_notification_parameters() {
        return new external_function_parameters(
            array(
                'notification'  => new external_single_structure(
                        array(
                            'type'       => new external_value(PARAM_INT, 'Notification type'),
                            'name'       => new external_value(PARAM_TEXT, 'Notification name'),
                            'externalid' => new external_value(PARAM_INT, 'Notification Intelliboard ID'),
                            'userid'     => new external_value(PARAM_INT, 'User that created notification'),
                            'email'      => new external_value(PARAM_TEXT, 'Emails where this notification should to go'),
                            'cc'         => new external_value(PARAM_TEXT, 'Copy emails where this notification should to go'),
                            'state'      => new external_value(PARAM_INT, 'Notification state'),
                            'subject'    => new external_value(PARAM_TEXT, 'Notification subject'),
                            'message'    => new external_value(PARAM_RAW, 'Notification message'),
                            'attachment' => new external_value(PARAM_TEXT, 'Notification attachment'),
                            'params'     => new external_value(PARAM_TEXT, 'Notification dynamic params'),
                            'tags'       => new external_value(PARAM_TEXT, 'Notification tags'),
                        )
                    )
                )
            );
    }

    /**
     * Create one or more assigns
     *
     * @param array $notification.
     * @return array An array with id
     * @since Moodle 2.5
     */
    public static function save_notification($notification) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        foreach (['email', 'cc'] as $key) {
            if (empty($notification[$key])) {
                $notification[$key] = null;
            }
        }

        $notification = (object) $notification;
        $params = empty($notification->params)? array() : json_decode($notification->params, true);
        unset($notification->params);

        if ($old = $DB->get_record('local_intelliboard_ntf',array('externalid' => $notification->externalid), 'id') ) {
            $id = $old->id;
            $notification->id = $old->id;
            $DB->update_record('local_intelliboard_ntf', $notification);
            $DB->delete_records('local_intelliboard_ntf_pms', array('notificationid' => $notification->id));
        } else {
            $id = $DB->insert_record('local_intelliboard_ntf', $notification);
        }

        $paramsToSave = array();

        foreach ($params as $key => $values) {
            $values = is_array($values)? $values : array($values);

            foreach ($values as $value) {
                $paramsToSave[] = (object) [
                    'name' => $key,
                    'value' => $value,
                    'notificationid' => $id
                ];
            }

        }

        $DB->insert_records('local_intelliboard_ntf_pms', $paramsToSave);
        $transaction->allow_commit();

        return compact('id');
    }

    public static function save_notification_returns() {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Notification ID on Moodle'),
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function delete_notification_parameters() {
        return new external_function_parameters(
            array(
                'externalid' =>  new external_value(PARAM_INT, 'External Notification ID'),
            )
        );
    }

    /**
     *
     * @param int $id
     * @return null
     * @since Moodle 2.5
     */
    public static function delete_notification($id) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        if ($notification = $DB->get_record('local_intelliboard_ntf',array('externalid' => $id), 'id')) {
            $DB->delete_records('local_intelliboard_ntf', array('id' => $notification->id));
            $DB->delete_records('local_intelliboard_ntf_pms', array('notificationid' => $notification->id));
        }

        $transaction->allow_commit();

        return null;
    }

    public static function delete_notification_returns() {
        return null;
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.5
     */
    public static function get_history_parameters() {
        return new external_function_parameters(
            array(
                'userid' =>  new external_value(PARAM_INT, 'Get history by app user'),
                'limit' => new external_value(PARAM_INT, 'Limit entries'),
                'offset' => new external_value(PARAM_INT, 'Offset entries'),
                'search' => new external_value(PARAM_TEXT, 'Search in history'),
                'order' => new external_single_structure(
                    array(
                        'key'       => new external_value(PARAM_TEXT, 'Order key'),
                        'direction'  => new external_value(PARAM_TEXT, 'Order direction'),
                    )
                )
            )
        );
    }

    /**
     *
     * @param int $userid
     * @param int $limit
     * @param int $offset
     * @param string $search
     * @param array $order
     * @return null
     * @since Moodle 2.5
     */
    public static function get_history($userid, $limit, $offset, $search, $order) {
        global $DB;

        $sql = "SELECT
            linh.id,
            linh.notificationid, 
            linh.email, 
            linh.timesent,
            linh.notificationname
            FROM {local_intelliboard_ntf_hst} linh
            WHERE linh.userid = :userid
        ";
        $countSql = "SELECT COUNT(*) FROM {local_intelliboard_ntf_hst} linh WHERE linh.userid = :userid";

        $params = compact('userid');

        if ($search) {
            $sql .= ' AND linh.notificationname LIKE :name';
            $countSql .= ' AND linh.notificationname LIKE :name';
            $params['name'] = '%' . $search . '%';
        }

        if ($order) {
            $direction = $order['direction'] === 'desc'? 'DESC' : 'ASC';
            $sql .= ' ORDER BY ' . $order['key'] . ' ' . $direction;
        }

        if ($limit) {
            $sql .= ' LIMIT ' . $limit;
        }

        if ($offset) {
            $sql .= ' OFFSET ' . $offset;
        }

        $data = $DB->get_records_sql($sql, $params);
        $count = $DB->count_records_sql($countSql, $params);

        return compact('data', 'count');
    }

    public static function get_history_returns() {
        return new external_single_structure(
                array(
                    'data' => new external_multiple_structure(
                        new external_single_structure(
                            array(
                                'id' => new external_value(PARAM_INT, 'Notification Entry ID'),
                                'notificationid' => new external_value(PARAM_INT, 'Notification ID on App'),
                                'email' => new external_value(PARAM_TEXT, 'Receiver email'),
                                'timesent' => new external_value(PARAM_INT, 'Notification sending time'),
                                'notificationname' => new external_value(PARAM_TEXT, 'Notification name'),
                            )
                        )
                    ),
                    'count' => new external_value(PARAM_INT, 'Count of history entries')
                )
        );
    }


    public static function clear_notifications_parameters() {
        return new external_function_parameters(
            array(
                'removeHistory' =>  new external_value(PARAM_INT, 'set if remove history too'),
            )
        );
    }

    public static function clear_notifications($removeHistory) {
        global $DB;

        $transaction = $DB->start_delegated_transaction();

        $DB->delete_records('local_intelliboard_ntf');
        $DB->delete_records('local_intelliboard_ntf_pms');

        if ($removeHistory) {
            $DB->delete_records('local_intelliboard_ntf_hst');
        }

        $transaction->allow_commit();

        return null;
    }

    public static function clear_notifications_returns() {
        return null;
    }

}