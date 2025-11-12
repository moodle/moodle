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
 * Class badge_exporter_interface represents the interface for exporting achievement (or badgeclass) data to a backpack.
 *
 * @package    core_badges
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface badge_exporter_interface {
    /**
     * Export the badge data to an array.
     *
     * @param bool $nested Whether to include nested issuer data.
     * @return array The exported badge data.
     */
    public function export(
        bool $nested = true,
    ): array;

    /**
     * Export the badge's related badges.
     *
     * @return array The related badges data.
     */
    public function export_related_badges(): array;

    /**
     * Export the badge's criteria.
     *
     * @return array|string The criteria badge data or the URL to the criteria when there is no narrative.
     */
    public function export_criteria(): array|string;

    /**
     * Export the badge's alignments.
     *
     * @return array The alignments data.
     */
    public function export_alignments(): array;

    /**
     * Get the JSON representation of the badge.
     *
     * @return string The JSON representation of the badge.
     */
    public function get_json(): string;

    /**
     * Get the URL to the JSON representation of the badge.
     *
     * @return url The URL to the JSON representation of the badge.
     */
    public function get_json_url(): url;
}
