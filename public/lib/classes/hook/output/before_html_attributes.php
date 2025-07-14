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

namespace core\hook\output;

/**
 * Class before_html_attributes
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read \renderer_base $renderer The page renderer object
 * @property array $attributes The list of HTML attributes to be added to the tag.
 */
#[\core\attribute\tags('output')]
#[\core\attribute\label('Allows plugins to add, remove or modify any attributes of the html tag.')]
#[\core\attribute\hook\replaces_callbacks('add_htmlattributes')]
final class before_html_attributes {
    /**
     * Constructor for the before_html_attributes hook.
     *
     * @param \renderer_base $renderer The page renderer object
     * @param array $attributes The list of HTML attributes initially on the tag
     */
    public function __construct(
        /** @var \renderer_base The page renderer */
        public readonly \renderer_base $renderer,
        /** @var array The list of HTML attributes initially on the tag */
        private array $attributes = [],
    ) {
    }

    /**
     * Add an HTML attribute to the list.
     *
     * @param string $name
     * @param string $value
     */
    public function add_attribute(string $name, string $value): void {
        $this->attributes[$name] = $value;
    }

    /**
     * Get the list of attributes.
     *
     * @return array
     */
    public function get_attributes(): array {
        return $this->attributes;
    }

    /**
     * Remove an HTML attribute from the list.
     *
     * @param string $name
     */
    public function remove_attribute(string $name): void {
        unset($this->attributes[$name]);
    }

    /**
     * Process legacy callbacks.
     */
    public function process_legacy_callbacks(): void {
        // Legacy callback 'add_htmlattributes' is deprecated since Moodle 4.4.

        // This function should return an array of html attribute names => values.
        $pluginswithfunction = get_plugins_with_function(
            function: 'add_htmlattributes',
            migratedtohook: true,
        );
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $newattrs = $function();
                unset($newattrs['dir']);
                unset($newattrs['lang']);
                unset($newattrs['xmlns']);
                unset($newattrs['xml:lang']);
                foreach ($newattrs as $name => $value) {
                    $this->add_attribute($name, $value);
                }
            }
        }
    }
}
