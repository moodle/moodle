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

namespace tool_usertours\hook;

/**
 * Provides the ability to add and remove custom client-side filters to the user tour filter list.
 *
 * @package    tool_usertours
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Provides the ability to add and remove custom client-side filters to the user tour filter list.')]
#[\core\attribute\tags('tool_usertours')]
class before_clientside_filter_fetch {
    /**
     * Create a new instance of the hook.
     *
     * @param array $filters
     */
    public function __construct(
        /** @var array The list of filters applied */
        protected array $filters,
    ) {
    }

    /**
     * Add a filter classname to the list of filters to be processed.
     *
     * @param string $classname
     * @return self
     */
    public function add_filter_by_classname(string $classname): self {
        if (!\is_a($classname, \tool_usertours\local\clientside_filter\clientside_filter::class, true)) {
            throw new \coding_exception("Invalid clientside filter class {$classname}");
        }
        $this->filters[] = $classname;

        return $this;
    }

    /**
     * Remove a filter classname from the list of filters to be processed.
     *
     * @param string $classname
     * @return self
     */
    public function remove_filter_by_classname(string $classname): self {
        $this->filters = array_filter($this->filters, fn($filter) => $filter !== $classname);
        return $this;
    }

    /**
     * Get the list of filters to be processed.
     *
     * @return array
     */
    public function get_filter_list(): array {
        return $this->filters;
    }
}
