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

/**
 * Template renderer for Moodle. Load and render Moodle templates with Mustache.
 *
 * @module     core/templates
 * @package    core
 * @class      templates
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      2.9
 */
define(['jquery', './tether', 'core/event', 'core/custom_interaction_events'], function(jQuery, Tether, Event, customEvents) {

    window.jQuery = jQuery;
    window.Tether = Tether;
    M.util.js_pending('theme_iomadboost/loader:children');

    require(['theme_iomadboost/aria',
            'theme_iomadboost/pending',
            'theme_iomadboost/util',
            'theme_iomadboost/alert',
            'theme_iomadboost/button',
            'theme_iomadboost/carousel',
            'theme_iomadboost/collapse',
            'theme_iomadboost/dropdown',
            'theme_iomadboost/modal',
            'theme_iomadboost/scrollspy',
            'theme_iomadboost/tab',
            'theme_iomadboost/tooltip',
            'theme_iomadboost/popover'],
            function(Aria) {

        // We do twice because: https://github.com/twbs/bootstrap/issues/10547
        jQuery('body').popover({
            trigger: 'focus',
            selector: "[data-toggle=popover][data-trigger!=hover]",
            placement: 'auto'
        });

        // Popovers must close on Escape for accessibility reasons.
        customEvents.define(jQuery('body'), [
            customEvents.events.escape,
        ]);
        jQuery('body').on(customEvents.events.escape, '[data-toggle=popover]', function() {
            // Use "blur" instead of "popover('hide')" to prevent issue that the same tooltip can't be opened again.
            jQuery(this).trigger('blur');
        });

        jQuery("html").popover({
            container: "body",
            selector: "[data-toggle=popover][data-trigger=hover]",
            trigger: "hover",
            delay: {
                hide: 500
            }
        });

        jQuery("html").tooltip({
            selector: '[data-toggle="tooltip"]'
        });

        // Disables flipping the dropdowns up and getting hidden behind the navbar.
        jQuery.fn.dropdown.Constructor.Default.flip = false;

        jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            var hash = jQuery(e.target).attr('href');
            if (history.replaceState) {
                history.replaceState(null, null, hash);
            } else {
                location.hash = hash;
            }
        });

        var hash = window.location.hash;
        if (hash) {
           jQuery('.nav-link[href="' + hash + '"]').tab('show');
        }

        // We need to call popover automatically if nodes are added to the page later.
        Event.getLegacyEvents().done(function(events) {
            jQuery(document).on(events.FILTER_CONTENT_UPDATED, function() {
                jQuery('body').popover({
                    selector: '[data-toggle="popover"]',
                    trigger: 'focus'
                });

            });
        });

        Aria.init();
        M.util.js_complete('theme_iomadboost/loader:children');
    });


    return {};
});
