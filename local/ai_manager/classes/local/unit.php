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

namespace local_ai_manager\local;

// phpcs:disable moodle.Commenting.InlineComment.DocBlock
// In case of an enum this phpcs rule triggers wrongly.
/**
 * Enum for defining different units in which the AI tool costs are being calculated.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum unit {
    // phpcs:enable moodle.Commenting.InlineComment.DocBlock

    case TOKEN;
    case COUNT;

    /**
     * Helper function to get a string representation of the enum constants.
     *
     * @return string localized string representation of the enum constants
     */
    public function to_string(): string {
        return get_string('unit_' . strtolower($this->name), 'local_ai_manager');
    }
}
