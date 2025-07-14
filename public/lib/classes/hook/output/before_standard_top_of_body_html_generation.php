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
 * Hook to allow subscribers to add HTML content to the top of the page body.
 *
 * @package    core
 * @copyright  2024 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @property-read \renderer_base $renderer The page renderer object
 */
#[\core\attribute\tags('output')]
#[\core\attribute\label('Allows plugins to add any elements to the page &lt;head&gt; html tag.')]
#[\core\attribute\hook\replaces_callbacks('before_standard_top_of_body_html')]
final class before_standard_top_of_body_html_generation {
    /**
     * Hook to allow subscribers to add HTML content to the top of the page body.
     *
     * @param \renderer_base $renderer
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
     *
     * Legacy callback 'before_standard_top_of_body_html' is deprecated since Moodle 4.4
     */
    public function process_legacy_callbacks(): void {
        // Give subsystems an opportunity to inject extra html content. The callback
        // must always return a string containing valid html.
        foreach (\core_component::get_core_subsystems() as $name => $path) {
            if ($path) {
                $this->add_html(
                    component_callback(
                        component: $name,
                        function: 'before_standard_top_of_body_html',
                        default: '',
                        migratedtohook: true,
                    ),
                );
            }
        }

        // Give plugins an opportunity to inject extra html content. The callback
        // must always return a string containing valid html.
        $pluginswithfunction = get_plugins_with_function(
            function: 'before_standard_top_of_body_html',
            migratedtohook: true,
        );
        foreach ($pluginswithfunction as $plugins) {
            foreach ($plugins as $function) {
                $this->add_html($function() ?? '');
            }
        }
    }
}
