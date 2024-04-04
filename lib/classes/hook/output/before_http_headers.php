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
 * Class before_http_headers
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read \renderer_base $renderer The page renderer object
 */
#[\core\attribute\tags('output')]
#[\core\attribute\label('Allows plugins to make changes before headers are sent')]
#[\core\attribute\hook\replaces_callbacks('before_http_headers')]
class before_http_headers {
    /**
     * Hook to allow subscribers to modify the process before headers are sent.
     *
     * @param \renderer_base $renderer
     */
    public function __construct(
        /** @var \renderer_base The page renderer object */
        public readonly \renderer_base $renderer,
    ) {
    }


    /**
     * Process legacy callbacks.
     */
    public function process_legacy_callbacks(): void {
        $pluginswithfunction = get_plugins_with_function(function: 'before_http_headers', migratedtohook: true);
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $function();
            }
        }
    }
}
