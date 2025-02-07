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

namespace core_courseformat\local\overview;

/**
 * Class resourceoverview
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resourceoverview extends \core_courseformat\activityoverviewbase {
    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'type' => $this->get_extra_type_overview(),
        ];
    }

    /**
     * Retrieves an overview item for the extra type of the resource.
     *
     * @return overviewitem|null
     */
    private function get_extra_type_overview(): ?overviewitem {
        // Only resource activities shows the type overview
        // because they are aggregated in one table.
        $archetype = plugin_supports(
            type: 'mod',
            name: $this->cm->modname,
            feature: FEATURE_MOD_ARCHETYPE,
            default: MOD_ARCHETYPE_OTHER
        );
        if ($archetype != MOD_ARCHETYPE_RESOURCE) {
            return null;
        }

        return new overviewitem(
            name: get_string('resource_type'),
            value: $this->cm->modfullname,
            content: $this->cm->modfullname,
        );
    }
}
