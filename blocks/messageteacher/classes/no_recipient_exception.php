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
 * Exception class indicating an invalid recipient
 *
 * @package    block_messageteacher
 * @copyright  2018 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_messageteacher;

defined('MOODLE_INTERNAL') || die();

/**
 * Exception thrown when a recipient is invalid.
 */
class no_recipient_exception  extends \moodle_exception {

    /**
     * Set the exception message with the invalid recipient ID.
     *
     * @param int $recipientid
     */
    public function __construct($recipientid) {
        parent::__construct('norecipient', 'block_messageteacher', $recipientid);
    }
}
