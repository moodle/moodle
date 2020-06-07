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

namespace core;

/**
 * User Alert notifications.
 *
 * @package    core
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use stdClass;

defined('MOODLE_INTERNAL') || die();

class notification {
    /**
     * A notification of level 'success'.
     */
    const SUCCESS = 'success';

    /**
     * A notification of level 'warning'.
     */
    const WARNING = 'warning';

    /**
     * A notification of level 'info'.
     */
    const INFO = 'info';

    /**
     * A notification of level 'error'.
     */
    const ERROR = 'error';

    /**
     * Add a message to the session notification stack.
     *
     * @param string $message The message to add to the stack
     * @param string $level   The type of message to add to the stack
     */
    public static function add($message, $level = null) {
        global $PAGE, $SESSION;

        if ($PAGE && $PAGE->state === \moodle_page::STATE_IN_BODY) {
            // Currently in the page body - just render and exit immediately.
            // We insert some code to immediately insert this into the user-notifications created by the header.
            $id = uniqid();
            echo \html_writer::span(
                $PAGE->get_renderer('core')->render(new \core\output\notification($message, $level)),
                '', array('id' => $id));

            // Insert this JS here using a script directly rather than waiting for the page footer to load to avoid
            // ensure that the message is added to the user-notifications section as soon as possible after it is created.
            echo \html_writer::script(
                    "(function() {" .
                        "var notificationHolder = document.getElementById('user-notifications');" .
                        "if (!notificationHolder) { return; }" .
                        "var thisNotification = document.getElementById('{$id}');" .
                        "if (!thisNotification) { return; }" .
                        "notificationHolder.appendChild(thisNotification.firstChild);" .
                        "thisNotification.remove();" .
                    "})();"
                );
            return;
        }

        // Add the notification directly to the session.
        // This will either be fetched in the header, or by JS in the footer.
        if (!isset($SESSION->notifications) || !array($SESSION->notifications)) {
            // Initialise $SESSION if necessary.
            if (!is_object($SESSION)) {
                $SESSION = new stdClass();
            }
            $SESSION->notifications = [];
        }
        $SESSION->notifications[] = (object) array(
            'message'   => $message,
            'type'      => $level,
        );
    }

    /**
     * @param string[] $icon The icon to use. Required keys are 'pix' and 'component'.
     * @param string $message The message to display.
     * @param array $actions An array of action links
     * @param string $region Optional region name
     * @throws \coding_exception
     */
    public static function add_call_to_action(array $icon, string $message, array $actions, string $region = ''): void {
        global $OUTPUT, $PAGE;

        $context = new stdClass();
        $context->icon = $icon;
        $context->message = $message;
        $context->region = $region;

        $context->actions = array_map(function($action) {
            $data = [];
            foreach ($action['data'] as $name => $value) {
                $data[] = ['name' => $name, 'value' => $value];
            }
            $action['data'] = $data;

            return $action;
        }, $actions);

        $notification = $OUTPUT->render_from_template('core/local/notification/cta', $context);

        if ($PAGE && $PAGE->state === \moodle_page::STATE_IN_BODY) {
            $id = uniqid();
            echo \html_writer::span($notification, '', ['id' => $id]);
            echo \html_writer::script(
                    "(function() {" .
                    "var notificationHolder = document.getElementById('user-notifications');" .
                    "if (!notificationHolder) { return; }" .
                    "var thisNotification = document.getElementById('{$id}');" .
                    "if (!thisNotification) { return; }" .
                    "notificationHolder.insertBefore(thisNotification.firstChild, notificationHolder.firstChild);" .
                    "thisNotification.remove();" .
                    "})();"
            );
        } else {
            throw new \coding_exception('You are calling add_call_to_action() either too early or too late.');
        }
    }

    /**
     * Fetch all of the notifications in the stack and clear the stack.
     *
     * @return array All of the notifications in the stack
     */
    public static function fetch() {
        global $SESSION;

        if (!isset($SESSION) || !isset($SESSION->notifications)) {
            return [];
        }

        $notifications = $SESSION->notifications;
        unset($SESSION->notifications);

        $renderables = [];
        foreach ($notifications as $notification) {
            $renderable = new \core\output\notification($notification->message, $notification->type);
            $renderables[] = $renderable;
        }

        return $renderables;
    }

    /**
     * Fetch all of the notifications in the stack and clear the stack.
     *
     * @return array All of the notifications in the stack
     */
    public static function fetch_as_array(\renderer_base $renderer) {
        $notifications = [];
        foreach (self::fetch() as $notification) {
            $notifications[] = [
                'template'  => $notification->get_template_name(),
                'variables' => $notification->export_for_template($renderer),
            ];
        }
        return $notifications;
    }

    /**
     * Add a success message to the notification stack.
     *
     * @param string $message The message to add to the stack
     */
    public static function success($message) {
        return self::add($message, self::SUCCESS);
    }

    /**
     * Add a info message to the notification stack.
     *
     * @param string $message The message to add to the stack
     */
    public static function info($message) {
        return self::add($message, self::INFO);
    }

    /**
     * Add a warning message to the notification stack.
     *
     * @param string $message The message to add to the stack
     */
    public static function warning($message) {
        return self::add($message, self::WARNING);
    }

    /**
     * Add a error message to the notification stack.
     *
     * @param string $message The message to add to the stack
     */
    public static function error($message) {
        return self::add($message, self::ERROR);
    }
}
