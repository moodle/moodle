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
 * Class shortlink_handler_interface.
 *
 * @package    core
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface shortlink_handler_interface {
    /**
     * Get the valid link types for this handler.
     *
     * @return array
     */
    public function get_valid_linktypes(): array;

    /**
     * Handle processing of a shortlink, returning the relevant URL.
     *
     * If no valid URL is found, this method should return null.
     *
     * @param string $type
     * @param string $identifier
     * @return null|\core\url
     */
    public function process_shortlink(
        string $type,
        string $identifier,
    ): ?\core\url;
}
