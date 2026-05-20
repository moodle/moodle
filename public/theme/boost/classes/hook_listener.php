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

use core\hook\output\before_import_map_config;
use core\hook\output\before_requirejs_config;

/**
 * Hook listeners for theme_boost.
 *
 * @package    theme_boost
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {
    /**
     * Add Bootstrap as a bare specifier in the ES module import map.
     *
     * This allows ESM modules to import Bootstrap via `import { Modal } from 'bootstrap'`.
     *
     * @param before_import_map_config $hook The hook object.
     */
    public static function before_import_map_config_listener(before_import_map_config $hook): void {
        // Register the main Bootstrap bundle as a bare specifier, and the internal util and dom modules as subpath specifiers.
        $hook->add_import(
            specifier: 'bootstrap',
            path: 'public/theme/boost/js/bundles/bootstrap',
            urlsuffix: '/bootstrap.esm.min.js',
            allowedsuffixes: ['.js', '.js.map'],
        );
        $hook->add_import(
            specifier: 'bootstrap/',
            path: 'public/theme/boost/js/bundles/bootstrap',
            allowedsuffixes: ['.js', '.js.map'],
        );
    }

    /**
     * Add imports for Bootstrap JS to the RequireJS map.
     *
     * @param before_requirejs_config $hook The hook object.
     */
    public static function before_requirejs_config_listener(before_requirejs_config $hook): void {
        $hook->add_requirejs_esm_map_entries(
            entries: [
                'bootstrap' => 'bootstrap',
                'bootstrap/dom/data' => 'bootstrap/dom/data',
                'bootstrap/dom/event-handler' => 'bootstrap/dom/event-handler:default',
                'bootstrap/dom/manipulator' => 'bootstrap/dom/manipulator:default',
                'bootstrap/dom/selector-engine' => 'bootstrap/dom/selector-engine:default',
                'bootstrap/util/backdrop' => 'bootstrap/util/backdrop:default',
                'bootstrap/util/component-functions' => 'bootstrap/util/component-functions:default',
                'bootstrap/util/config' => 'bootstrap/util/config:default',
                'bootstrap/util/focustrap' => 'bootstrap/util/focustrap:default',
                'bootstrap/util/index' => 'bootstrap/util/index:default',
                'bootstrap/util/sanitizer' => 'bootstrap/util/sanitizer.js',
                'bootstrap/util/scrollbar' => 'bootstrap/util/scrollbar:default',
                'bootstrap/util/swipe' => 'bootstrap/util/swipe:default',
                'bootstrap/util/template-factory' => 'bootstrap/util/template-factory:default',

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
}
