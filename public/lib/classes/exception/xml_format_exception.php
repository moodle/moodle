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

namespace core\exception;

use stdClass;

/**
 * Exception thrown when there is an error parsing an XML file.
 *
 * @package     core
 * @copyright   2010 The Open University
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class xml_format_exception extends moodle_exception {
    /**
     * Constructor function
     *
     * @param string|null $errorstring Error string
     * @param int $line Line number
     * @param int $char Character number
     * @param string $link Link
     */
    public function __construct(
        /** @var ?string Error string */
        public ?string $errorstring,
        /** @var int Line number */
        public int $line,
        /** @var int Character number */
        public int $char,
        string $link = '',
    ) {
        $a = new stdClass();
        $a->errorstring = $errorstring;
        $a->errorline = $line;
        $a->errorchar = $char;
        parent::__construct('errorparsingxml', 'error', $link, $a);
    }
}
