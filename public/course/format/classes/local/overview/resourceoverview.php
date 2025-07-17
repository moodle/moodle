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

use core\url;
use action_link;
use core\output\local\properties\button;
use core\output\local\properties\text_align;

/**
 * Class resourceoverview
 *
 * @package    core_courseformat
 * @copyright  2025 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class resourceoverview extends \core_courseformat\activityoverviewbase {

    #[\Override]
    public function get_actions_overview(): ?overviewitem {
        if (!$this->is_resource()) {
            // Only resource activities show the actions overview
            // because they are aggregated in one table.
            return null;
        }

        if (!has_capability('report/log:view', $this->context)) {
            return null;
        }

        $content = new action_link(
            url: new url(
                '/report/log/index.php?',
                ['id' => $this->cm->course, 'modid' => $this->cm->id, 'chooselog' => 1, 'modaction' => 'r']
            ),
            text: get_string('view'),
            attributes: ['class' => button::BODY_OUTLINE->classes()],
        );

        return new overviewitem(
            name: get_string('actions'),
            value: '',
            content: $content,
            textalign: text_align::CENTER,
        );
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        return [
            'type' => $this->get_extra_type_overview(),
        ];
    }

    /**
     * Retrieves an overview item for the extra type of the resource.
     *
     * @return overviewitem|null The overview item for the resource type.
     */
    private function get_extra_type_overview(): ?overviewitem {
        if (!$this->is_resource()) {
            // Only resource activities show the type.
            return null;
        }

        return new overviewitem(
            name: get_string('resource_type'),
            value: $this->cm->modfullname,
            content: $this->cm->modfullname,
        );
    }

    /**
     * Checks if the current activity is a resource type.
     *
     * @return bool True if the activity is a resource type, false otherwise.
     */
    protected function is_resource(): bool {
        // Check if the activity is a resource type.
        $archetype = plugin_supports(
            type: 'mod',
            name: $this->cm->modname,
            feature: FEATURE_MOD_ARCHETYPE,
            default: MOD_ARCHETYPE_OTHER
        );
        return $archetype === MOD_ARCHETYPE_RESOURCE;
    }
}
