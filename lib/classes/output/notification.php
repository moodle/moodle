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
 * Notification renderable component.
 *
 * @package    core
 * @copyright  2015 Jetha Chan
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

/**
 * Data structure representing a notification.
 *
 * @copyright 2015 Jetha Chan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.9
 * @package core
 * @category output
 */
class notification implements \renderable, \templatable {

    /**
     * A notification of level 'success'.
     */
    const NOTIFY_SUCCESS = 'success';

    /**
     * A notification of level 'warning'.
     */
    const NOTIFY_WARNING = 'warning';

    /**
     * A notification of level 'info'.
     */
    const NOTIFY_INFO = 'info';

    /**
     * A notification of level 'error'.
     */
    const NOTIFY_ERROR = 'error';

    /**
     * @var string Message payload.
     */
    protected $message = '';

    /**
     * @var string Message type.
     */
    protected $messagetype = self::NOTIFY_WARNING;

    /**
     * @var bool $announce Whether this notification should be announced assertively to screen readers.
     */
    protected $announce = true;

    /**
     * @var bool $closebutton Whether this notification should inlcude a button to dismiss itself.
     */
    protected $closebutton = true;

    /**
     * @var array $extraclasses A list of any extra classes that may be required.
     */
    protected $extraclasses = array();

    /**
     * Notification constructor.
     *
     * @param string $message the message to print out
     * @param ?string $messagetype one of the NOTIFY_* constants..
     * @param bool $closebutton Whether to show a close icon to remove the notification (default true).
     */
    public function __construct($message, $messagetype = null, $closebutton = true) {
        $this->message = $message;

        if (empty($messagetype)) {
            $messagetype = self::NOTIFY_ERROR;
        }

        $this->messagetype = $messagetype;

        $this->closebutton = $closebutton;
    }

    /**
     * Set whether this notification should be announced assertively to screen readers.
     *
     * @param bool $announce
     * @return $this
     */
    public function set_announce($announce = false) {
        $this->announce = (bool) $announce;

        return $this;
    }

    /**
     * Set whether this notification should include a button to disiss itself.
     *
     * @param bool $button
     * @return $this
     */
    public function set_show_closebutton($button = false) {
        $this->closebutton = (bool) $button;

        return $this;
    }

    /**
     * Add any extra classes that this notification requires.
     *
     * @param array $classes
     * @return $this
     */
    public function set_extra_classes($classes = array()) {
        $this->extraclasses = $classes;

        return $this;
    }

    /**
     * Get the message for this notification.
     *
     * @return string message
     */
    public function get_message() {
        return $this->message;
    }

    /**
     * Get the message type for this notification.
     *
     * @return string message type
     */
    public function get_message_type() {
        return $this->messagetype;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return array data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) {
        return array(
            'message'       => clean_text($this->message),
            'extraclasses'  => implode(' ', $this->extraclasses),
            'announce'      => $this->announce,
            'closebutton'   => $this->closebutton,
            'issuccess'         => $this->messagetype === 'success',
            'isinfo'            => $this->messagetype === 'info',
            'iswarning'         => $this->messagetype === 'warning',
            'iserror'           => $this->messagetype === 'error',
        );
    }

    public function get_template_name() {
        $templatemappings = [
            // Current types mapped to template names.
            'success'           => 'core/notification_success',
            'info'              => 'core/notification_info',
            'warning'           => 'core/notification_warning',
            'error'             => 'core/notification_error',
        ];

        if (isset($templatemappings[$this->messagetype])) {
            return $templatemappings[$this->messagetype];
        }
        return $templatemappings['error'];
    }
}
