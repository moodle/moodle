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
 * Hook to allow subscribers to add HTML content after the main region content has been generated.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read \renderer_base $renderer The page renderer object
 */
#[\core\attribute\tags('output')]
#[\core\attribute\label('Allows plugins to add any elements to the footer before JS is finalized')]
#[\core\attribute\hook\replaces_callbacks('standard_after_main_region_html')]
final class after_standard_main_region_html_generation {
    /**
     * Hook to allow subscribers to add HTML content after the main region content has been generated.
     *
     * @param renderer_base $renderer
     * @param string $output Initial output
     */
    public function __construct(
        /** @var \renderer_base The page renderer object */
        public readonly \renderer_base $renderer,
        /** @var string The collected output */
        private string $output = '',
    ) {
    }

    /**
     * Plugins implementing callback can add any HTML to the top of the body.
     *
     * Must be a string containing valid html head content.
     *
     * @param null|string $output
     */
    public function add_html(?string $output): void {
        if ($output) {
            $this->output .= $output;
        }
    }

    /**
     * Returns all HTML added by the plugins
     *
     * @return string
     */
    public function get_output(): string {
        return $this->output;
    }

    /**
     * Process legacy callbacks.
     */
    public function process_legacy_callbacks(): void {
        $pluginswithfunction = get_plugins_with_function(function: 'standard_after_main_region_html', migratedtohook: true);
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $extrafooter = $function();
                if (is_string($extrafooter)) {
                    $this->add_html($extrafooter);
                }
            }
        }
    }
}
