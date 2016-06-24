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
 * Contains class used to prepare a message for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_message\output;

use renderable;
use templatable;

/**
 * Class to prepare a message for display.
 *
 * @package   core_message
 * @copyright 2016 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message implements templatable, renderable {

    /**
     * The message.
     */
    protected $message;

    /**
     * Constructor.
     *
     * @param \stdClass $message
     */
    public function __construct($message) {
        $this->message = $message;
    }

    public function export_for_template(\renderer_base $output) {
        $message = new \stdClass();
        $message->id = $this->message->id;
        $message->text = $this->message->text;
        $message->displayblocktime = $this->message->displayblocktime;
        $message->blocktime = userdate($this->message->timecreated, get_string('strftimedaydate'));
        $message->position = 'left';
        if ($this->message->currentuserid == $this->message->useridfrom) {
            $message->position = 'right';
        }
        $message->timesent = userdate($this->message->timecreated, get_string('strftimetime'));
        $message->isread = !empty($this->message->timeread) ? 1 : 0;

        return $message;
    }
}
