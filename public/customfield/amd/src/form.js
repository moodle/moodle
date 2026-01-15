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
 * Custom Field interaction management for Moodle.
 *
 * @module     core_customfield/form
 * @copyright  2018 Toni Barbera
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import 'core/inplace_editable';
import {call as fetchMany} from 'core/ajax';
import {
    getString,
    getStrings,
} from 'core/str';
import {add as addToast} from 'core/toast';
import * as Repository from 'core_customfield/repository';
import ModalForm from 'core_form/modalform';
import Notification from 'core/notification';
import Pending from 'core/pending';
import SortableList from 'core/sortable_list';
import Templates from 'core/templates';
import jQuery from 'jquery';

/**
 * Display confirmation dialogue
 *
 * @param {Number} id
 * @param {String} type
 * @param {String} component
 * @param {String} area
 * @param {Number} itemid
 */
const confirmDelete = (id, type, component, area, itemid) => {
    const pendingPromise = new Pending('core_customfield/form:confirmDelete');

    getStrings([
        {'key': 'confirm'},
        {'key': 'confirmdelete' + type, component: 'core_customfield'},
        {'key': 'yes'},
        {'key': 'no'},
    ])
    .then(strings => {
        return Notification.confirm(strings[0], strings[1], strings[2], strings[3], function() {
            const pendingDeletePromise = new Pending('core_customfield/form:confirmDelete');
            fetchMany([
                {
                    methodname: (type === 'field') ? 'core_customfield_delete_field' : 'core_customfield_delete_category',
                    args: {id},
                },
            ])[0]
            .then(() => Repository.reloadTemplate(component, area, itemid))
            .then(response => Templates.render('core_customfield/list', response))
            .then((html, js) => Templates.replaceNode(jQuery('[data-region="list-page"]'), html, js))
            .then(pendingDeletePromise.resolve)
            .catch(Notification.exception);
        });
    })
    .then(pendingPromise.resolve)
    .catch(Notification.exception);
};

/**
 * Display a confirmation modal to convert an entity custom field category to a shared category.
 *
 * @param {Number} categoryId
 * @param {String} component
 * @param {String} area
 * @param {Number} itemid
 */
const confirmConvert = (categoryId, component, area, itemid) => {
    getStrings([
        {'key': 'convertcategorytitle', component: 'core_customfield'},
        {'key': 'convertcategoryconfirm', component: 'core_customfield'},
        {'key': 'proceed'},
        {'key': 'cancel'},
    ])
    .then(strings => {
        return Notification.confirm(strings[0], strings[1], strings[2], strings[3], function() {
            handleConversion(categoryId, component, area, itemid);
        });
    })
    .catch(Notification.exception);
};

/**
 * Handle the conversion of an entity custom field category to a shared category.
 *
 * @param {Number} categoryId
 * @param {String} component
 * @param {String} area
 * @param {Number} itemid
 */
const handleConversion = (categoryId, component, area, itemid) => {
    const pendingPromise = new Pending('core_customfield/form:handleConversion');

    Repository.convertCategory(categoryId, component, area, itemid)
    .then(() => Repository.toggleCategory(categoryId, component, area, itemid, true))
    .then(() => Repository.reloadTemplate(component, area, itemid))
    .then((response) => Templates.renderForPromise('core_customfield/list', response))
    .then(({html, js}) => {
        const listPage = document.querySelector('[data-region="list-page"]');
        return Templates.replaceNode(listPage, html, js);
    })
    .then(() => addToast(getString('categoryconverted', 'core_customfield'), {type: 'success'}))
    .then(() => pendingPromise.resolve())
    .catch(Notification.exception);
};

/**
 * Display an alert when conversion of a category to a shared category is blocked due to duplicate names.
 */
const alertDuplicateField = () => {
    getStrings([
        {'key': 'conversionerror', component: 'core_customfield'},
        {'key': 'sharedcustomfieldalreadyexists', component: 'core_customfield'},
        {'key': 'closebuttontitle'},
    ])
    .then(strings => {
        return Notification.alert(strings[0], strings[1], strings[2]);
    })
    .catch(Notification.exception);
};

/**
 * Creates a new custom fields category with default name and updates the list
 *
 * @param {String} component
 * @param {String} area
 * @param {Number} itemid
 */
const createNewCategory = (component, area, itemid) => {
    const pendingPromise = new Pending('core_customfield/form:createNewCategory');
    const promises = fetchMany([
        {methodname: 'core_customfield_create_category', args: {component, area, itemid}},
    ]);

    promises[0].then(() => Repository.reloadTemplate(component, area, itemid))
    .then(response => Templates.render('core_customfield/list', response))
    .then((html, js) => Templates.replaceNode(jQuery('[data-region="list-page"]'), html, js))
    .then(() => pendingPromise.resolve())
    .catch(Notification.exception);
};

/**
 * Create new custom field
 *
 * @param {HTMLElement} element
 * @param {String} component
 * @param {String} area
 * @param {Number} itemid
 */
const createNewField = (element, component, area, itemid) => {
    const pendingPromise = new Pending('core_customfield/form:createNewField');

    const returnFocus = element.closest(".action-menu").querySelector(".dropdown-toggle");
    const form = new ModalForm({
        formClass: "core_customfield\\field_config_form",
        args: {
            categoryid: element.getAttribute('data-categoryid'),
            type: element.getAttribute('data-type'),
        },
        modalConfig: {
            title: getString('addingnewcustomfield', 'core_customfield', element.getAttribute('data-typename')),
        },
        returnFocus,
    });

    form.addEventListener(form.events.FORM_SUBMITTED, () => {
        const pendingCreatedPromise = new Pending('core_customfield/form:createdNewField');

        Repository.reloadTemplate(component, area, itemid)
        .then(response => Templates.render('core_customfield/list', response))
        .then((html, js) => Templates.replaceNode(jQuery('[data-region="list-page"]'), html, js))
        .then(() => pendingCreatedPromise.resolve())
        .catch(() => window.location.reload());
    });

    form.show();

    pendingPromise.resolve();
};

/**
 * Edit custom field
 *
 * @param {HTMLElement} element
 * @param {String} component
 * @param {String} area
 * @param {Number} itemid
 */
const editField = (element, component, area, itemid) => {
    const pendingPromise = new Pending('core_customfield/form:editField');

    const form = new ModalForm({
        formClass: "core_customfield\\field_config_form",
        args: {
            id: element.getAttribute('data-id'),
        },
        modalConfig: {
            title: getString('editingfield', 'core_customfield', element.getAttribute('data-name')),
        },
        returnFocus: element,
    });

    form.addEventListener(form.events.FORM_SUBMITTED, () => {
        const pendingCreatedPromise = new Pending('core_customfield/form:createdNewField');

        Repository.reloadTemplate(component, area, itemid)
        .then(response => Templates.render('core_customfield/list', response))
        .then((html, js) => Templates.replaceNode(jQuery('[data-region="list-page"]'), html, js))
        .then(() => pendingCreatedPromise.resolve())
        .catch(() => window.location.reload());
    });

    form.show();

    pendingPromise.resolve();
};

/**
 * Fetch the category name from an inplace editable, given a child node of that field.
 *
 * @param {NodeElement} nodeElement
 * @returns {String}
 */
const getCategoryNameFor = nodeElement => nodeElement
    .closest('[data-category-id]')
    .attr('data-category-name');

const setupSortableLists = rootNode => {
    // Sort category.
    const sortCat = new SortableList(
        '#customfield_catlist .categorieslist',
        {
            moveHandlerSelector: '.movecategory [data-drag-type=move]',
        }
    );
    sortCat.getElementName = nodeElement => Promise.resolve(getCategoryNameFor(nodeElement));

    // Note: The sortable list currently uses jQuery events.
    jQuery('[data-category-id]').on(SortableList.EVENTS.DROP, (evt, info) => {
        if (info.positionChanged) {
            const pendingPromise = new Pending('core_customfield/form:categoryid:on:sortablelist-drop');
            fetchMany([{
                methodname: 'core_customfield_move_category',
                args: {
                    id: info.element.data('category-id'),
                    beforeid: info.targetNextElement.data('category-id')
                }

            }])[0]
            .then(pendingPromise.resolve)
            .catch(Notification.exception);
        }
        evt.stopPropagation(); // Important for nested lists to prevent multiple targets.
    });

    // Sort fields.
    var sort = new SortableList(
        '#customfield_catlist .fieldslist tbody',
        {
            moveHandlerSelector: '.movefield [data-drag-type=move]',
        }
    );

    sort.getDestinationName = (parentElement, afterElement) => {
        if (!afterElement.length) {
            return getString('totopofcategory', 'customfield', getCategoryNameFor(parentElement));
        } else if (afterElement.attr('data-field-name')) {
            return getString('afterfield', 'customfield', afterElement.attr('data-field-name'));
        } else {
            return Promise.resolve('');
        }
    };

    jQuery('[data-field-name]').on(SortableList.EVENTS.DROP, (evt, info) => {
        if (info.positionChanged) {
            const pendingPromise = new Pending('core_customfield/form:fieldname:on:sortablelist-drop');
            fetchMany([{
                methodname: 'core_customfield_move_field',
                args: {
                    id: info.element.data('field-id'),
                    beforeid: info.targetNextElement.data('field-id'),
                    categoryid: Number(info.targetList.closest('[data-category-id]').attr('data-category-id'))
                },
            }])[0]
            .then(pendingPromise.resolve)
            .catch(Notification.exception);
        }
        evt.stopPropagation(); // Important for nested lists to prevent multiple targets.
    });

    jQuery('[data-field-name]').on(SortableList.EVENTS.DRAG, evt => {
        var pendingPromise = new Pending('core_customfield/form:fieldname:on:sortablelist-drag');

        evt.stopPropagation(); // Important for nested lists to prevent multiple targets.

        // Refreshing fields tables.
        Templates.render('core_customfield/nofields', {})
        .then(html => {
            rootNode.querySelectorAll('.categorieslist > *')
            .forEach(category => {
                const fields = category.querySelectorAll('.field:not(.sortable-list-is-dragged)');
                const noFields = category.querySelector('.nofields');

                if (!fields.length && !noFields) {
                    category.querySelector('tbody').innerHTML = html;
                } else if (fields.length && noFields) {
                    noFields.remove();
                }
            });
            return;
        })
        .then(pendingPromise.resolve)
        .catch(Notification.exception);
    });

    jQuery('[data-category-id], [data-field-name]').on(SortableList.EVENTS.DRAGSTART, (evt, info) => {
        setTimeout(() => {
            jQuery('.sortable-list-is-dragged').width(info.element.width());
        }, 501);
    });
};

/**
 * Initialise the custom fields manager.
 */
export const init = () => {
    const rootNode = document.querySelector('#customfield_catlist');

    const component = rootNode.dataset.component;
    const area = rootNode.dataset.area;
    const itemid = rootNode.dataset.itemid;

    rootNode.addEventListener('click', e => {
        const roleHolder = e.target.closest('[data-role]');
        if (!roleHolder) {
            return;
        }

        if (roleHolder.dataset.role === 'deletefield') {
            e.preventDefault();

            confirmDelete(roleHolder.dataset.id, 'field', component, area, itemid);
            return;
        }

        if (roleHolder.dataset.role === 'deletecategory') {
            e.preventDefault();

            confirmDelete(roleHolder.dataset.id, 'category', component, area, itemid);
            return;
        }

        if (roleHolder.dataset.role === 'convertcategory') {
            e.preventDefault();

            confirmConvert(roleHolder.dataset.id, component, area, itemid);
            return;
        }

        if (roleHolder.dataset.role === 'hasduplicatecustomfield') {
            e.preventDefault();

            alertDuplicateField();
            return;
        }

        if (roleHolder.dataset.role === 'addnewcategory') {
            e.preventDefault();
            createNewCategory(component, area, itemid);

            return;
        }

        if (roleHolder.dataset.role === 'addfield') {
            e.preventDefault();
            createNewField(roleHolder, component, area, itemid);

            return;
        }

        if (roleHolder.dataset.role === 'editfield') {
            e.preventDefault();
            editField(roleHolder, component, area, itemid);

            return;
        }
    });

    setupSortableLists(rootNode, component, area, itemid);
};
