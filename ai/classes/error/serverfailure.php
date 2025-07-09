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

namespace core_ai\error;

/**
 * Error 5xx handler class.
 *
 * The 5xx HTTP status codes represent server errors,
 * indicating that the server failed to process a valid request due to a problem on its side.
 *
 * @package    core_ai
 * @copyright  Meirza <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class serverfailure extends base {
    #[\Override]
    public function get_errormessage(): string {
        if ($this->messagetype === static::ERROR_TYPE_MINIMAL) {
            return get_string('error:defaultmessageshort', 'core_ai');
        }

        return parent::get_errormessage();
    }
}
