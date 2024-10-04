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
 * Renderable for the availability info.
 *
 * @package   core_availability
 * @copyright 2021 Bas Brands <bas@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability\output;
use core_availability_multiple_messages;
use renderable;
use templatable;
use stdClass;

/**
 * Base class to render availability info.
 *
 * @package   core_availability
 * @copyright 2021 Bas Brands <bas@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability_info implements renderable, templatable {

    /** @var core_availability_multiple_messages availabilitymessages the course format class */
    protected $availabilitymessages;

    /**
     * Constructor.
     *
     * @param core_availability_multiple_messages $renderable the availability messages
     */
    public function __construct(core_availability_multiple_messages $renderable) {
        $this->availabilitymessages = $renderable;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {

        $template = $this->get_item_template($this->availabilitymessages);

        $template->id = uniqid();

        return $template;
    }

    /**
     * Get the item base template.
     *
     * @return stdClass the template base
     */
    protected function get_item_base_template(): stdClass {
        return (object)[
            'id' => false,
            'items' => [],
            'hasitems' => false,
        ];
    }

    /**
     * Get the item template.
     *
     * @param core_availability_multiple_messages $availability the availability messages
     * @return stdClass the template
     */
    protected function get_item_template(core_availability_multiple_messages $availability): stdClass {

        $template = $this->get_item_base_template();

        $template->header = $this->get_item_header($availability);

        foreach ($availability->items as $item) {
            if (is_string($item)) {
                $simple_item = $this->get_item_base_template();
                $simple_item->header = $item;
                $template->items[] = $simple_item;
            } else {
                $template->items[] = $this->get_item_template($item);
            }
        }

        $template->hasitems = !empty($template->items);

        return $template;
    }

    /**
     * Get the item header.
     * Depending on availability configuration this will return a string from a combined string identifier.
     * For example: list_root_and_hidden, list_and, list_root_or_hidden, list_root_or, etc.
     *
     * @param core_availability_multiple_messages $availability the availability messages
     * @return string the item header
     */
    protected function get_item_header(core_availability_multiple_messages $availability): string {
        $stridentifier = 'list_' . ($availability->root ? 'root_' : '') .
            ($availability->andoperator ? 'and' : 'or') .
            ($availability->treehidden ? '_hidden' : '');

        return get_string($stridentifier, 'availability');
    }
}
