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
 * Wrapper for the YUI M.core.actionmenu class. Allows us to
 * use the YUI version in AMD code until it is replaced.
 *
 * @module     core/menu
 * @package    core
 * @copyright  2015 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/templates', 'core/notification', 'core/yui'], function($, templates, notification, Y) {

    // Private variables and functions.
    /**
     * Replace the menu node in the dom by this new HTML
     * rendered from a template and then call enhance on it.
     *
     * @param {string} newHTML
     */
    var enhanceMenu = function(newHTML) {
        var newMenu = $(newHTML);
        // Insert some more aria attrs into the menu entries.
        newMenu.find('li a').each(function(index, element) {
            $(element).addClass('menu-action');
            $(element).attr('role', 'menuitem');
        });
        $(this).replaceWith(newMenu);

        Y.use('moodle-core-actionmenu', function() {
            if (M.core.actionmenu.instance === null) {
                M.core.actionmenu.init();
            } else {
                var yuiNode = Y.one(newMenu.get(0));
                M.core.actionmenu.newDOMNode(yuiNode.ancestor());
            }
        });
    };

    return /** @alias module:core/menu */ {
        // Public variables and functions.
        /**
         * Wrap M.core.actionmenu.
         * @param {string} triggerMessage Text for the button to open the menu.
         * @param {string} selector CSS selector for a list of links to turn into a menu (can match mulitple menus).
         */
        menu: function(triggerMessage, selector) {
            // First we need to modify the list(s) to have markup compatible with actionmenu.

            $(selector).each(function (index, element) {
                var links = [];
                $(element).find('li').each(function (index, link) {
                    links.push($(link).html().trim());
                });
                var data = {
                    triggerMessage: triggerMessage,
                    links: links
                };

                var enhanceCallback = $.proxy(enhanceMenu, element);

                templates.render('core/menu', data)
                    .done(enhanceCallback)
                    .fail(notification.exception);
            });

        }
    };
});
