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

namespace core\output;

use core\output\action_menu\link;
use core\output\action_menu\subpanel;
use core\output\named_templatable;
use core\output\renderable;

/**
 * A generic submenu output class.
 *
 * This class can be used to create a list of action menu items for use inside a subpanel.
 *
 * @package    core
 * @copyright  2026 Mikel Martín <mikel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submenu implements named_templatable, renderable {
    /** @var renderable[] action menu items */
    protected array $items;

    /**
     * Constructor.
     *
     * @param renderable[] $items Action menu items. Each item should be a
     *                            {@see \core\output\action_menu\link},
     *                            {@see \core\output\action_menu\link_secondary},
     *                            {@see \core\output\action_menu\subpanel},
     *                            or any {@see renderable}.
     */
    public function __construct(array $items = []) {
        $this->items = $items;
    }

    /**
     * Add an item to the submenu.
     *
     * @param renderable $item
     * @return self For fluent (builder-style) usage.
     */
    public function add_item(renderable $item): self {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Export for template rendering.
     *
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        $items = [];

        foreach ($this->items as $item) {
            if ($item instanceof subpanel) {
                $items[] = ['subpanel' => $item->export_for_template($output)];
                continue;
            }

            if ($item instanceof link) {
                $data = $item->export_for_template($output);
                if (!str_contains(' ' . $data->classes . ' ', ' dropdown-item ')) {
                    $data->classes = trim('dropdown-item ' . $data->classes);
                }
                $items[] = ['actionmenulink' => $data];
                continue;
            }

            // Fallback: render any other renderable as raw HTML.
            $items[] = ['rawhtml' => $output->render($item)];
        }

        return ['items' => $items];
    }

    /**
     * Get the name of the template to use for this templatable.
     *
     * @param \renderer_base $renderer
     * @return string
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'core/local/submenu/submenu';
    }
}
