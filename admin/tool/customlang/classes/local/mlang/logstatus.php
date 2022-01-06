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
 * Language string based on David Mudrak langstring from local_amos.
 *
 * @package    tool_customlang
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_customlang\local\mlang;

use moodle_exception;
use stdclass;

/**
 * Class containing a lang string cleaned.
 *
 * @package    tool_customlang
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Represents a single string
 */
class logstatus {

    /** @var langstring the current string */
    public $langstring = null;

    /** @var string the component */
    public $component = null;

    /** @var string the string ID */
    public $stringid = null;

    /** @var string the original filename */
    public $filename = null;

    /** @var int the error level */
    public $errorlevel = null;

    /** @var string the message identifier */
    private $message;

    /**
     * Class creator.
     *
     * @param string $message the message identifier to display
     * @param string $errorlevel the notice level
     * @param string|null $filename the filename of this log
     * @param string|null $component the component of this log
     * @param langstring|null $langstring the langstring of this log
     */
    public function __construct(string $message, string $errorlevel, ?string $filename = null,
            ?string $component = null, ?langstring $langstring = null) {

        $this->filename = $filename;
        $this->component = $component;
        $this->langstring = $langstring;
        $this->message = $message;
        $this->errorlevel = $errorlevel;

        if ($langstring) {
            $this->stringid = $langstring->id;
        }
    }

    /**
     * Get the log message.
     *
     * @return string the log message.
     */
    public function get_message(): string {
        return get_string($this->message, 'tool_customlang', $this);
    }
}
