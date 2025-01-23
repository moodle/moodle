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

namespace core_badges\local\backpack\ob;

use core\url;

/**
 * Class assertion_exporter_interface represents the interface for exporting credential achievements (or assertions) to a backpack.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface assertion_exporter_interface {
    /**
     * Export the assertion data to an array.
     *
     * @param bool $nested Whether to include nested badge/issuer data.
     * @param bool $usesalt Whether to use a salt for the recipient.
     * @return array The exported assertion data.
     */
    public function export(
        bool $nested = true,
        bool $usesalt = true,
    ): array;

    /**
     * Get the JSON representation of the assertion.
     *
     * @return string The JSON representation of the assertion.
     */
    public function get_json(): string;

    /**
     * Whether the assertion is revoked.
     *
     * @return bool True if the assertion is revoked, false otherwise.
     */
    public function is_revoked(): bool;

    /**
     * Get the URL to the JSON representation of the assertion.
     *
     * @return url The URL to the JSON representation of the assertion.
     */
    public function get_json_url(): url;
}
