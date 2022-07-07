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
 * This module depends on the real jquery - and returns the non-global version of it.
 *
 * @module     core_customfield/form
 * @package    core_customfield
 * @copyright  2018 Toni Barbera
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/str', 'core/notification', 'core/ajax', 'core/templates', 'core/sortable_list', 'core/inplace_editable'],
    function(
        $, Str, Notification, Ajax, Templates, SortableList) {

    /**
     * Display confirmation dialogue
     *
     * @param {Number} id
     * @param {String} type
     * @param {String} component
     * @param {String} area
     * @param {Number} itemid
     */
    var confirmDelete = function(id, type, component, area, itemid) {
        Str.get_strings([
            {'key': 'confirm'},
            {'key': 'confirmdelete' + type, component: 'core_customfield'},
            {'key': 'yes'},
            {'key': 'no'},
        ]).done(function(s) {
            Notification.confirm(s[0], s[1], s[2], s[3], function() {
                var func = (type === 'field') ? 'core_customfield_delete_field' : 'core_customfield_delete_category';
                Ajax.call([
                    {methodname: func, args: {id: id}},
                    {methodname: 'core_customfield_reload_template', args: {component: component, area: area, itemid: itemid}}
                ])[1].then(function(response) {
                    return Templates.render('core_customfield/list', response);
                }).then(function(html, js) {
                    Templates.replaceNode($('[data-region="list-page"]'), html, js);
                    return null;
                }).fail(Notification.exception);
            });
        }).fail(Notification.exception);
    };

    /**
     * Creates a new custom fields category with default name and updates the list
     *
     * @param {String} component
     * @param {String} area
     * @param {Number} itemid
     */
    var createNewCategory = function(component, area, itemid) {
        var promises = Ajax.call([
                {methodname: 'core_customfield_create_category', args: {component: component, area: area, itemid: itemid}},
                {methodname: 'core_customfield_reload_template', args: {component: component, area: area, itemid: itemid}}
            ]),
            categoryid;

        promises[0].then(function(response) {
            categoryid = response;
            return null;
        }).fail(Notification.exception);

        promises[1].then(function(response) {
            return Templates.render('core_customfield/list', response);
        }).then(function(html, js) {
            Templates.replaceNode($('[data-region="list-page"]'), html, js);
            window.location.href = '#category-' + categoryid;
            return null;
        }).fail(Notification.exception);
    };

    return {
        /**
         * Initialise the custom fields manager
         */
        init: function() {
            var mainlist = $('#customfield_catlist'),
                component = mainlist.attr('data-component'),
                area = mainlist.attr('data-area'),
                itemid = mainlist.attr('data-itemid');
            $("[data-role=deletefield]").on('click', function(e) {
                confirmDelete($(this).attr('data-id'), 'field', component, area, itemid);
                e.preventDefault();
            });
            $("[data-role=deletecategory]").on('click', function(e) {
                confirmDelete($(this).attr('data-id'), 'category', component, area, itemid);
                e.preventDefault();
            });
            $('[data-role=addnewcategory]').on('click', function() {
                createNewCategory(component, area, itemid);
            });

            var categoryName = function(element) {
                return element
                    .closest('[data-category-id]')
                    .find('[data-inplaceeditable][data-itemtype=category][data-component=core_customfield]')
                    .attr('data-value');
            };

            // Sort category.
            var sortCat = new SortableList(
                $('#customfield_catlist .categorieslist'),
                {moveHandlerSelector: '.movecategory [data-drag-type=move]'}
            );

            sortCat.getElementName = function(el) {
                return $.Deferred().resolve(categoryName(el));
            };

            $('[data-category-id]').on('sortablelist-drop', function(evt, info) {
                if (info.positionChanged) {
                    var promises = Ajax.call([
                        {
                            methodname: 'core_customfield_move_category',
                            args: {
                                id: info.element.data('category-id'),
                                beforeid: info.targetNextElement.data('category-id')
                            }

                        },
                    ]);
                    promises[0].fail(Notification.exception);
                }
                evt.stopPropagation(); // Important for nested lists to prevent multiple targets.
            });

            // Sort fields.
            var sort = new SortableList(
                $('#customfield_catlist .fieldslist tbody'),
                {moveHandlerSelector: '.movefield [data-drag-type=move]'}
            );

            sort.getDestinationName = function(parentElement, afterElement) {
                if (!afterElement.length) {
                    return Str.get_string('totopofcategory', 'customfield', categoryName(parentElement));
                } else if (afterElement.attr('data-field-name')) {
                    return Str.get_string('afterfield', 'customfield', afterElement.attr('data-field-name'));
                } else {
                    return $.Deferred().resolve('');
                }
            };

            $('[data-field-name]').on('sortablelist-drop', function(evt, info) {
                evt.stopPropagation(); // Important for nested lists to prevent multiple targets.
                if (info.positionChanged) {
                    var promises = Ajax.call([
                        {
                            methodname: 'core_customfield_move_field',
                            args: {
                                id: info.element.data('field-id'),
                                beforeid: info.targetNextElement.data('field-id'),
                                categoryid: Number(info.targetList.closest('[data-category-id]').attr('data-category-id'))
                            },
                        },
                    ]);
                    promises[0].fail(Notification.exception);
                }
            }).on('sortablelist-drag', function(evt) {
                evt.stopPropagation(); // Important for nested lists to prevent multiple targets.
                // Refreshing fields tables.
                Str.get_string('therearenofields', 'core_customfield').then(function(s) {
                    $('#customfield_catlist .categorieslist').children().each(function() {
                        var fields = $(this).find($('.field')),
                            nofields = $(this).find($('.nofields'));
                        if (!fields.length && !nofields.length) {
                            $(this).find('tbody').append(
                                '<tr class="nofields"><td colspan="5">' + s + '</td></tr>'
                            );
                        }
                        if (fields.length && nofields.length) {
                            nofields.remove();
                        }
                    });
                    return null;
                }).fail(Notification.exception);
            });

            $('[data-category-id], [data-field-name]').on('sortablelist-dragstart',
                function(evt, info) {
                    setTimeout(function() {
                        $('.sortable-list-is-dragged').width(info.element.width());
                    }, 501);
                }
            );

        }
    };
});
