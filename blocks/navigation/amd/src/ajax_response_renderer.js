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
 * Parse the response from the navblock ajax page and render the correct DOM
 * structure for the tree from it.
 *
 * @module     block_navigation/ajax_response_renderer
 * @package    core
 * @copyright  2015 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    // Mappings for the different types of nodes coming from the navigation.
    // Copied from lib/navigationlib.php navigation_node constants.
    var NODETYPE = {
        // @type int Activity (course module) = 40.
        ACTIVITY: 40,
        // @type int Resource (course module = 50.
        RESOURCE: 50,
    };

    /**
     * Build DOM.
     *
     * @method buildDOM
     * @param {Object} rootElement the root element of DOM.
     * @param {object} nodes jquery object representing the nodes to be build.
     */
    function buildDOM(rootElement, nodes) {
        var ul = $('<ul></ul>');
        ul.attr('role', 'group');
        ul.attr('aria-hidden', true);

        $.each(nodes, function(index, node) {
            if (typeof node !== 'object') {
                return;
            }

            var li = $('<li></li>');
            var p = $('<p></p>');
            var id = node.id || node.key + '_tree_item';
            var icon = null;
            var isBranch = (node.expandable || node.haschildren) ? true : false;

            p.addClass('tree_item');
            p.attr('id', id);
            p.attr('role', 'treeitem');
            // Negative tab index to allow it to receive focus.
            p.attr('tabindex', '-1');

            if (node.requiresajaxloading) {
                p.attr('data-requires-ajax', true);
                p.attr('data-node-id', node.id);
                p.attr('data-node-key', node.key);
                p.attr('data-node-type', node.type);
            }

            if (isBranch) {
                li.addClass('collapsed contains_branch');
                p.attr('aria-expanded', false);
                p.addClass('branch');
            }

            if (node.icon && (!isBranch || node.type === NODETYPE.ACTIVITY || node.type === NODETYPE.RESOURCE)) {
                li.addClass('item_with_icon');
                p.addClass('hasicon');

                icon = $('<img/>');
                icon.attr('alt', node.icon.alt);
                icon.attr('title', node.icon.title);
                icon.attr('src', M.util.image_url(node.icon.pix, node.icon.component));
                $.each(node.icon.classes, function(index, className) {
                    icon.addClass(className);
                });
            }

            if (node.link) {
                var link = $('<a title="' + node.title + '" href="' + node.link + '"></a>');

                if (icon) {
                    link.append(icon);
                    link.append('<span class="item-content-wrap">' + node.name + '</span>');
                } else {
                    link.append(node.name);
                }

                if (node.hidden) {
                    link.addClass('dimmed');
                }

                p.append(link);
            } else {
                var span = $('<span></span>');

                if (icon) {
                    span.append(icon);
                    span.append('<span class="item-content-wrap">' + node.name + '</span>');
                } else {
                    span.append(node.name);
                }

                if (node.hidden) {
                    span.addClass('dimmed');
                }

                p.append(span);
            }

            li.append(p);
            ul.append(li);

            if (node.children && node.children.length) {
                buildDOM(p, node.children);
            } else if (isBranch && !node.requiresajaxloading) {
                li.removeClass('contains_branch');
                p.addClass('emptybranch');
            }
        });

        rootElement.parent().append(ul);
        var id = rootElement.attr('id') + '_group';
        ul.attr('id', id);
        rootElement.attr('aria-owns', id);
        rootElement.attr('role', 'treeitem');
    }

    return {
        render: function(element, nodes) {
            // The first element of the response is the existing node so we start with processing the children.
            if (nodes.children && nodes.children.length) {
                buildDOM(element, nodes.children);

                var item = element.children("[role='treeitem']").first();
                var group = element.find('#' + item.attr('aria-owns'));

                item.attr('aria-expanded', true);
                group.attr('aria-hidden', false);
            } else {
                if (element.parent().hasClass('contains_branch')) {
                    element.parent().removeClass('contains_branch');
                    element.addClass('emptybranch');
                }
            }
        }
    };
});
