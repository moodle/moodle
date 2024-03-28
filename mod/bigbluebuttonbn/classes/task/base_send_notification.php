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

namespace mod_bigbluebuttonbn\task;

use core\message\message;
use core\task\adhoc_task;
use mod_bigbluebuttonbn\instance;
use moodle_exception;
use stdClass;

/**
 * Class containing the abstract class for notification processes in BBB.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2023 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base_send_notification extends adhoc_task {

    /** @var instance */
    protected $instance = null;

    /** @var object */
    protected $coursecontact = null;

    /**
     * Execute the task.
     */
    public function execute() {
        $this->send_all_notifications();
    }

    /**
     * Append additional elements of custom data
     *
     * @param array $newdata
     */
    protected function append_custom_data(array $newdata): void {
        if ($currentdata = (array) $this->get_custom_data()) {
            $newdata = array_merge($currentdata, $newdata);
        }

        $this->set_custom_data($newdata);
    }

    /**
     * Set the instanceid in the custom data.
     *
     * @param int $instanceid
     */
    public function set_instance_id(int $instanceid): void {
        $this->append_custom_data(['instanceid' => $instanceid]);
    }

    /**
     * Get the bigbluebutton instance that this notification is for.
     *
     * @return instance|null null if the instance could not be loaded.
     */
    protected function get_instance(): ?instance {
        // This means the customdata is broken, and needs to be fixed.
        if (empty($this->get_custom_data()->instanceid)) {
            throw new \coding_exception("Task custom data was missing instanceid");
        }

        if ($this->instance === null) {
            $this->instance = instance::get_from_instanceid($this->get_custom_data()->instanceid);
        }

        return $this->instance;
    }

    /**
     * Get the preferred course contact for this notification.
     *
     * @return stdClass
     */
    protected function get_course_contact(): stdClass {
        global $DB;

        if ($this->coursecontact === null) {
            // Get course managers so they can be highlighted in the list.
            $coursecontext = $this->get_instance()->get_course_context();

            if ($managerroles = get_config('', 'coursecontact')) {
                $coursecontactroles = explode(',', $managerroles);
                foreach ($coursecontactroles as $roleid) {
                    $contacts = get_role_users($roleid, $coursecontext, true, 'u.id', 'u.id ASC');
                    foreach ($contacts as $contact) {
                        $this->coursecontact = $contact;
                        break;
                    }
                }
            }

            if ($this->coursecontact === null) {
                $this->coursecontact = \core_user::get_noreply_user();
            }
        }

        return $this->coursecontact;
    }

    /**
     * Get the list of recipients for the notification.
     *
     * @return stdClass[]
     */
    protected function get_recipients(): array {
        // Potential users should be active users only.
        return get_enrolled_users(
            $this->get_instance()->get_course_context(),
            'mod/bigbluebuttonbn:view',
            0,
            'u.*',
            null,
            0,
            0,
            true
        );
    }

    /**
     * Get the HTML message content.
     *
     * @return string
     */
    abstract protected function get_html_message(): string;

    /**
     * Get the plain text message content.
     *
     * @return string
     */
    protected function get_message(): string {
        return html_to_text($this->get_html_message());
    }

    /**
     * Get the short summary message.
     *
     * @return string
     */
    abstract protected function get_small_message(): string;

    /**
     * Get the preferred message format
     *
     * @return string
     */
    protected function get_message_format(): string {
        return FORMAT_HTML;
    }

    /**
     * Get the notification type.
     *
     * @return string
     */
    abstract protected function get_notification_type(): string;

    /**
     * Get the subject of the notification.
     *
     * @return string
     */
    abstract protected function get_subject(): string;

    /**
     * Send all of the notifications
     */
    protected function send_all_notifications(): void {
        $instance = $this->get_instance();

        // Cannot do anything without a valid instance.
        if (empty($instance)) {
            mtrace("Instance was empty, skipping");
            return;
        }

        foreach ($this->get_recipients() as $recipient) {
            try {
                \core_user::require_active_user($recipient, true, true);
                \core\cron::setup_user($recipient);
            } catch (moodle_exception $e) {
                // Skip sending.
                continue;
            }

            $this->send_notification_to_current_user();
        }

        \core\cron::setup_user();
    }

    /**
     * Send the notificiation to the current user.
     */
    protected function send_notification_to_current_user(): void {
        global $USER;

        $instance = $this->get_instance();

        $eventdata = new message();
        $eventdata->courseid            = $instance->get_course_id();
        $eventdata->component           = 'mod_bigbluebuttonbn';
        $eventdata->name                = $this->get_notification_type();
        $eventdata->userfrom            = $this->get_course_contact();
        $eventdata->userto              = $USER;

        $eventdata->subject             = $this->get_subject();
        $eventdata->smallmessage        = $this->get_small_message();
        $eventdata->fullmessage         = $this->get_message();
        $eventdata->fullmessageformat   = $this->get_message_format();
        $eventdata->fullmessagehtml     = $this->get_html_message();
        $eventdata->notification        = 1;
        $eventdata->contexturl          = $this->get_instance()->get_view_url();
        $eventdata->contexturlname      = $this->get_instance()->get_meeting_name();

        message_send($eventdata);
    }
}
