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

namespace theme_boost;

use core\hook\output\before_html_attributes;
use core\hook\output\before_requirejs_config;
use core\hook\output\before_standard_head_html_generation;
use core\output\html_writer;

/**
 * Hook listeners for theme_boost.
 *
 * @package    theme_boost
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {
    /**
     * Add imports for Bootstrap JS to the RequireJS map.
     *
     * @param before_requirejs_config $hook The hook object.
     */
    public static function before_requirejs_config_listener(before_requirejs_config $hook): void {
        $hook->add_requirejs_esm_map_entries(
            entries: [
                // To be deprecated removed from 7.0 onwards.
                'theme_boost/index' => 'bootstrap',
                'theme_boost/bootstrap' => 'bootstrap',
                'theme_boost/bootstrap/index' => 'bootstrap',
                'theme_boost/bootstrap/alert' => 'bootstrap:Alert',
                'theme_boost/bootstrap/base-component' => 'bootstrap:BaseComponent',
                'theme_boost/bootstrap/button' => 'bootstrap:Button',
                'theme_boost/bootstrap/carousel' => 'bootstrap:Carousel',
                'theme_boost/bootstrap/collapse' => 'bootstrap:Collapse',
                'theme_boost/bootstrap/dropdown' => 'bootstrap:Dropdown',
                'theme_boost/bootstrap/modal' => 'bootstrap:Modal',
                'theme_boost/bootstrap/offcanvas' => 'bootstrap:Offcanvas',
                'theme_boost/bootstrap/popover' => 'bootstrap:Popover',
                'theme_boost/bootstrap/scrollspy' => 'bootstrap:ScrollSpy',
                'theme_boost/bootstrap/tab' => 'bootstrap:Tab',
                'theme_boost/bootstrap/toast' => 'bootstrap:Toast',
                'theme_boost/bootstrap/tooltip' => 'bootstrap:Tooltip',

                'theme_boost/bootstrap/dom/data' => 'bootstrap/dom/data',
                'theme_boost/bootstrap/dom/event-handler' => 'bootstrap/dom/event-handler:default',
                'theme_boost/bootstrap/dom/manipulator' => 'bootstrap/dom/manipulator:default',
                'theme_boost/bootstrap/dom/selector-engine' => 'bootstrap/dom/selector-engine:default',
                'theme_boost/bootstrap/util/backdrop' => 'bootstrap/util/backdrop:default',
                'theme_boost/bootstrap/util/component-functions' => 'bootstrap/util/component-functions:default',
                'theme_boost/bootstrap/util/config' => 'bootstrap/util/config:default',
                'theme_boost/bootstrap/util/focustrap' => 'bootstrap/util/focustrap:default',
                'theme_boost/bootstrap/util/index' => 'bootstrap/util/index:default',
                'theme_boost/bootstrap/util/sanitizer' => 'bootstrap/util/sanitizer.js',
                'theme_boost/bootstrap/util/scrollbar' => 'bootstrap/util/scrollbar:default',
                'theme_boost/bootstrap/util/swipe' => 'bootstrap/util/swipe:default',
                'theme_boost/bootstrap/util/template-factory' => 'bootstrap/util/template-factory:default',
            ],
        );
    }

    /**
     * Set the Bootstrap colour mode on the html tag.
     *
     * The data-bs-theme attribute drives every Bootstrap colour mode override, so it has to be on the page from the
     * very first byte. The chosen mode is repeated in data-colourmode because "auto" can only be resolved in the
     * browser, and the switcher needs to know what the user actually picked.
     *
     * @param before_html_attributes $hook The hook object.
     */
    public static function before_html_attributes_listener(before_html_attributes $hook): void {
        if (!colour_mode::is_enabled() || !colour_mode::is_boost_theme()) {
            return;
        }

        $mode = colour_mode::get_current_mode();

        // Auto is resolved by the script added in before_standard_head_html_generation_listener(). Render light until
        // then so that the page is still usable with JavaScript disabled.
        $hook->add_attribute('data-bs-theme', $mode === colour_mode::AUTO ? colour_mode::LIGHT : $mode);
        $hook->add_attribute('data-colourmode', $mode);
    }

    /**
     * Add the script which resolves the "auto" colour mode against the device colour scheme.
     *
     * This has to run synchronously in the head, before the page is painted, otherwise people using the dark colour
     * scheme get a flash of the light theme on every page load.
     *
     * @param before_standard_head_html_generation $hook The hook object.
     */
    public static function before_standard_head_html_generation_listener(
        before_standard_head_html_generation $hook,
    ): void {
        if (!colour_mode::is_enabled() || !colour_mode::is_boost_theme()) {
            return;
        }

        // Behat sites keep the colour mode the server decided on: which mode "auto" resolves to depends on the
        // machine running the browser, which would make every colour assertion in the suite non-deterministic.
        if (defined('BEHAT_SITE_RUNNING')) {
            return;
        }

        $js = <<<'EOF'
            (function() {
                var query = window.matchMedia('(prefers-color-scheme: dark)');
                var resolve = function() {
                    var root = document.documentElement;
                    if (root.getAttribute('data-colourmode') !== 'auto') {
                        return;
                    }
                    root.setAttribute('data-bs-theme', query.matches ? 'dark' : 'light');
                };
                resolve();
                query.addEventListener('change', resolve);
            })();
            EOF;

        $hook->add_html(html_writer::script($js));
    }
}
