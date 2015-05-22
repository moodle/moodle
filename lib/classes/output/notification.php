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
use stdClass;

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
     * A generic message.
     */
    const NOTIFY_MESSAGE = 'message';
    /**
     * A message notifying the user of a successful operation.
     */
    const NOTIFY_SUCCESS = 'success';
    /**
     * A message notifying the user that a problem occurred.
     */
    const NOTIFY_PROBLEM = 'problem';
    /**
     * A message to display during a redirect..
     */
    const NOTIFY_REDIRECT = 'redirect';

    /**
     * @var string Message payload.
     */
    private $message = '';

    /**
     * @var string Message type.
     */
    private $messagetype = self::NOTIFY_PROBLEM;

    /**
     * Notification constructor.
     *
     * @param string $message the message to print out
     * @param string $messagetype normally NOTIFY_PROBLEM or NOTIFY_SUCCESS.
     */
    public function __construct($message, $messagetype = self::NOTIFY_PROBLEM) {

        $this->message = clean_text($message);
        $this->messagetype = $messagetype;

    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) {

        $data = new stdClass();

        $data->type = $this->messagetype;
        $data->message = $this->message;

        return $data;
    }
}
