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
use core\router\response\not_found_response;

/**
 * An exception to describe the case where a requested item was not found.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

 */
class not_found_exception extends \moodle_exception implements response_aware_exception {
    /**
     * Constructor for a new not found exception.
     *
     * @param string $itemtype The type of item that was not found.
     * @param string $identifier The identifier of the item that was not found.
     */
    public function __construct(
        string $itemtype,
        string $identifier,
    ) {
        parent::__construct(
            errorcode: 'itemnotfound',
            a: [
                'itemtype' => $itemtype,
                'identifier' => $identifier,
            ],
        );
    }

    #[\Override]
    public function get_response_classname(): string {
        return not_found_response::class;
    }
}
