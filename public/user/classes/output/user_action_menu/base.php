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

declare(strict_types=1);

namespace core_user\output\user_action_menu;

use core\output\{renderer_base, templatable};

/**
 * Base user action menu item
 *
 * @package     core_user
 * @copyright   2026 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class base implements templatable {
    /**
     * Constructor
     *
     * @param string[] $classes Optional list classes for the template
     * @param string[] $attributes Optional attributes for the template as name => value
     */
    public function __construct(
        /** @var string[] */
        private array $classes = [],
        /** @var string[] */
        private array $attributes = [],
    ) {
    }

    /**
     * Return the action menu type, automatically derived from its class name
     *
     * @return string
     */
    public function get_action_menu_type(): string {
        $classparts = explode('\\', get_class($this));
        return end($classparts);
    }

    /**
     * Transform attributes property from name => value into one suitable for template consumption
     *
     * @return array[]
     */
    private function get_attributes(): array {
        return array_map(
            fn(string $name, string $value): array => ['name' => $name, 'value' => $value],
            array_keys($this->attributes),
            $this->attributes,
        );
    }

    #[\Override]
    public function export_for_template(renderer_base $output): array {
        return [
            $this->get_action_menu_type() => true,
            'itemtype' => $this->get_action_menu_type(),
            'classes' => $this->classes,
            'attributes' => $this->get_attributes(),
        ];
    }
}
