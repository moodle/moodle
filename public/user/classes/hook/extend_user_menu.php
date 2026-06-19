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

use core\output\pix_icon;
use core\url;
use core_user\output\user_action_menu\{base, divider, link};

/**
 * Hook to modify user menu.
 *
 * @package    core_user
 * @copyright  2024 Guillaume Barat <guillaumebarat@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\tags('user')]
#[\core\attribute\label('Allows plugins to add any elements to the user menu')]
final class extend_user_menu {
    /**
     * Hook to modify user menu.
     *
     * @param base[] $navitems
     */
    public function __construct(
        /** @var base[] */
        private array $navitems = [],
    ) {
    }

    /**
     * Add menu item
     *
     * @param base $item
     */
    public function add_menu_item(base $item): void {
        $this->navitems[] = $item;
    }

    /**
     * Return menu items
     *
     * @return base[]
     */
    public function get_menu_items(): array {
        return $this->navitems;
    }

    /**
     * Add navigation item.
     *
     * @param null|\stdClass $output
     *
     * @deprecated since Moodle 5.3 - please use {@see add_menu_item} instead.
     */
    #[\core\attribute\deprecated('add_menu_item', mdl: 'MDL-88938', since: '5.3')]
    public function add_navitem(?\stdClass $output): void {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

        if ($output) {
            if (property_exists($output, 'itemtype')) {
                // Provide backwards compatibility for the old stdClass object. Convert it to a proper menu item class.
                switch ($output->itemtype) {
                    case 'divider':
                        $this->navitems[] = new divider();
                        break;
                    case 'link':
                        $this->navitems[] = new link(
                            new url($output->url),
                            $output->title,
                            $output->titleattribute ?? null,
                            isset($output->pixicon) ? new pix_icon($output->pixicon, '') : null,
                            $output->imgsrc ?? null,
                        );
                        break;
                }
            }
        }
    }

    /**
     * Returns a class with the detail for the menu.
     *
     * @return array
     *
     * @deprecated since Moodle 5.3 - please use {@see get_menu_items} instead.
     */
    #[\core\attribute\deprecated('get_menu_items', mdl: 'MDL-88938', since: '5.3')]
    public function get_navitems(): array {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);

        return $this->get_menu_items();
    }
}
