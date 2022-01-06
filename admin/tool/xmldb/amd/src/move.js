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

define(['jquery', 'core/sortable_list', 'core/ajax', 'core/notification'], function($, SortableList, Ajax, Notification) {
    return {
        init: function(tableid, moveaction) {
            // Initialise sortable for the given list.
            var sort = new SortableList('#' + tableid + ' tbody');
            sort.getElementName = function(element) {
                return $.Deferred().resolve(element.attr('data-name'));
            };
            var origIndex;
            $('#' + tableid + ' tbody tr').on(SortableList.EVENTS.DRAGSTART, function(_, info) {
                // Remember position of the element in the beginning of dragging.
                origIndex = info.sourceList.children().index(info.element);
                // Resize the "proxy" element to be the same width as the main element.
                setTimeout(function() {
                    $('.sortable-list-is-dragged').width(info.element.width());
                }, 501);
            }).on(SortableList.EVENTS.DROP, function(_, info) {
                // When a list element was moved send AJAX request to the server.
                var newIndex = info.targetList.children().index(info.element);
                var t = info.element.find('[data-action=' + moveaction + ']');
                if (info.positionChanged && t.length) {
                    var request = {
                        methodname: 'tool_xmldb_invoke_move_action',
                        args: {
                            action: moveaction,
                            dir: t.attr('data-dir'),
                            table: t.attr('data-table'),
                            field: t.attr('data-field'),
                            key: t.attr('data-key'),
                            index: t.attr('data-index'),
                            position: newIndex - origIndex
                        }
                    };
                    Ajax.call([request])[0].fail(Notification.exception);
                }
            });
        }
    };
});
