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

namespace core_user\hook;

/**
 * Hook to modify user menu.
 *
 * @package    core_user
 * @copyright  2024 Guillaume Barat <guillaumebarat@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read \renderer_base $renderer The page renderer object
 */
#[\core\attribute\tags('user')]
#[\core\attribute\label('Allows plugins to add any elements to the user menu')]
final class extend_user_menu {
    /**
     * Hook to modify user menu.
     *
     * @param array $navitems Menu item to add.
     */
    public function __construct(
        /** @var array The navigation items */
        public array $navitems = [],
    ) {
    }

    /**
     * Add navigation item.
     *
     * @param null|\stdClass $output
     */
    public function add_navitem(?\stdClass $output): void {
        if ($output) {
            if (property_exists($output, 'itemtype')) {
                $this->navitems[] = $output;
            }
        }
    }

    /**
     * Returns a class with the detail for the menu.
     *
     * @return array
     */
    public function get_navitems(): array {
        return $this->navitems;
    }
}
