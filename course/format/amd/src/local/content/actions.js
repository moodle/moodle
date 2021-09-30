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
 * Course state actions dispatcher.
 *
 * This module captures all data-dispatch links in the course content and dispatch the proper
 * state mutation, including any confirmation and modal required.
 *
 * @module     core_courseformat/local/content/actions
 * @class      core_courseformat/local/content/actions
 * @copyright  2021 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Templates from 'core/templates';
import {prefetchStrings} from 'core/prefetch';
import {get_string as getString} from 'core/str';
import {getList} from 'core/normalise';
import * as CourseEvents from 'core_course/events';
import Pending from 'core/pending';
import ContentTree from 'core_courseformat/local/courseeditor/contenttree';
// The jQuery module is only used for interacting with Boostrap 4. It can we removed when MDL-79179 is integrated.
import jQuery from 'jquery';

// Load global strings.
prefetchStrings('core', ['movecoursesection', 'movecoursemodule', 'confirm', 'delete']);

export default class extends BaseComponent {

    /**
     * Constructor hook.
     */
    create() {
        // Optional component name for debugging.
        this.name = 'content_actions';
        // Default query selectors.
        this.selectors = {
            ACTIONLINK: `[data-action]`,
            // Move modal selectors.
            SECTIONLINK: `[data-for='section']`,
            CMLINK: `[data-for='cm']`,
            SECTIONNODE: `[data-for='sectionnode']`,
            MODALTOGGLER: `[data-toggle='collapse']`,
            ADDSECTION: `[data-action='addSection']`,
            CONTENTTREE: `#destination-selector`,
            ACTIONMENU: `.action-menu`,
            ACTIONMENUTOGGLER: `[data-toggle="dropdown"]`,
        };
        // Component css classes.
        this.classes = {
            DISABLED: `disabled`,
        };
    }

    /**
     * Initial state ready method.
     *
     * @param {Object} state the state data.
     *
     */
    stateReady(state) {
        // Delegate dispatch clicks.
        this.addEventListener(
            this.element,
            'click',
            this._dispatchClick
        );
        // Check section limit.
        this._checkSectionlist({state});
        // Add an Event listener to recalculate limits it if a section HTML is altered.
        this.addEventListener(
            this.element,
            CourseEvents.sectionRefreshed,
            () => this._checkSectionlist({state})
        );
    }

    /**
     * Return the component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        return [
            // Check section limit.
            {watch: `course.sectionlist:updated`, handler: this._checkSectionlist},
        ];
    }

    _dispatchClick(event) {
        const target = event.target.closest(this.selectors.ACTIONLINK);
        if (!target) {
            return;
        }
        if (target.classList.contains(this.classes.DISABLED)) {
            event.preventDefault();
            return;
        }

        // Invoke proper method.
        const methodName = this._actionMethodName(target.dataset.action);

        if (this[methodName] !== undefined) {
            this[methodName](target, event);
        }
    }

    _actionMethodName(name) {
        const requestName = name.charAt(0).toUpperCase() + name.slice(1);
        return `_request${requestName}`;
    }

    /**
     * Check the section list and disable some options if needed.
     *
     * @param {Object} detail the update details.
     * @param {Object} detail.state the state object.
     */
    _checkSectionlist({state}) {
        // Disable "add section" actions if the course max sections has been exceeded.
        this._setAddSectionLocked(state.course.sectionlist.length > state.course.maxsections);
    }

    /**
     * Handle a move section request.
     *
     * @param {Element} target the dispatch action element
     * @param {Event} event the triggered event
     */
    async _requestMoveSection(target, event) {
        // Check we have an id.
        const sectionId = target.dataset.id;
        if (!sectionId) {
            return;
        }
        const sectionInfo = this.reactive.get('section', sectionId);

        event.preventDefault();

        // The section edit menu to refocus on end.
        const editTools = this._getClosestActionMenuToogler(target);

        // Collect section information from the state.
        const exporter = this.reactive.getExporter();
        const data = exporter.course(this.reactive.state);

        // Add the target section id and title.
        data.sectionid = sectionInfo.id;
        data.sectiontitle = sectionInfo.title;

        // Build the modal parameters from the event data.
        const modalParams = {
            title: getString('movecoursesection', 'core'),
            body: Templates.render('core_courseformat/local/content/movesection', data),
        };

        // Create the modal.
        const modal = await this._modalBodyRenderedPromise(modalParams);

        const modalBody = getList(modal.getBody())[0];

        // Disable current element and section zero.
        const currentElement = modalBody.querySelector(`${this.selectors.SECTIONLINK}[data-id='${sectionId}']`);
        this._disableLink(currentElement);
        const generalSection = modalBody.querySelector(`${this.selectors.SECTIONLINK}[data-number='0']`);
        this._disableLink(generalSection);

        // Setup keyboard navigation.
        new ContentTree(
            modalBody.querySelector(this.selectors.CONTENTTREE),
            {
                SECTION: this.selectors.SECTIONNODE,
                TOGGLER: this.selectors.MODALTOGGLER,
                COLLAPSE: this.selectors.MODALTOGGLER,
            },
            true
        );

        // Capture click.
        modalBody.addEventListener('click', (event) => {
            const target = event.target;
            if (!target.matches('a') || target.dataset.for != 'section' || target.dataset.id === undefined) {
                return;
            }
            if (target.getAttribute('aria-disabled')) {
                return;
            }
            event.preventDefault();
            this.reactive.dispatch('sectionMove', [sectionId], target.dataset.id);
            this._destroyModal(modal, editTools);
        });
    }

    /**
     * Handle a move cm request.
     *
     * @param {Element} target the dispatch action element
     * @param {Event} event the triggered event
     */
    async _requestMoveCm(target, event) {
        // Check we have an id.
        const cmId = target.dataset.id;
        if (!cmId) {
            return;
        }
        const cmInfo = this.reactive.get('cm', cmId);

        event.preventDefault();

        // The section edit menu to refocus on end.
        const editTools = this._getClosestActionMenuToogler(target);

        // Collect section information from the state.
        const exporter = this.reactive.getExporter();
        const data = exporter.course(this.reactive.state);

        // Add the target cm info.
        data.cmid = cmInfo.id;
        data.cmname = cmInfo.name;

        // Build the modal parameters from the event data.
        const modalParams = {
            title: getString('movecoursemodule', 'core'),
            body: Templates.render('core_courseformat/local/content/movecm', data),
        };

        // Create the modal.
        const modal = await this._modalBodyRenderedPromise(modalParams);

        const modalBody = getList(modal.getBody())[0];

        // Disable current element.
        let currentElement = modalBody.querySelector(`${this.selectors.CMLINK}[data-id='${cmId}']`);
        this._disableLink(currentElement);

        // Setup keyboard navigation.
        new ContentTree(
            modalBody.querySelector(this.selectors.CONTENTTREE),
            {
                SECTION: this.selectors.SECTIONNODE,
                TOGGLER: this.selectors.MODALTOGGLER,
                COLLAPSE: this.selectors.MODALTOGGLER,
                ENTER: this.selectors.SECTIONLINK,
            }
        );

        // Open the cm section node if possible (Bootstrap 4 uses jQuery to interact with collapsibles).
        // All jQuery int this code can be replaced when MDL-79179 is integrated.
        const sectionnode = currentElement.closest(this.selectors.SECTIONNODE);
        const toggler = jQuery(sectionnode).find(this.selectors.MODALTOGGLER);
        let collapsibleId = toggler.data('target') ?? toggler.attr('href');
        if (collapsibleId) {
            // We cannot be sure we have # in the id element name.
            collapsibleId = collapsibleId.replace('#', '');
            jQuery(`#${collapsibleId}`).collapse('toggle');
        }

        // Capture click.
        modalBody.addEventListener('click', (event) => {
            const target = event.target;
            if (!target.matches('a') || target.dataset.for === undefined || target.dataset.id === undefined) {
                return;
            }
            if (target.getAttribute('aria-disabled')) {
                return;
            }
            event.preventDefault();

            // Get draggable data from cm or section to dispatch.
            let targetSectionId;
            let targetCmId;
            if (target.dataset.for == 'cm') {
                const dropData = exporter.cmDraggableData(this.reactive.state, target.dataset.id);
                targetSectionId = dropData.sectionid;
                targetCmId = dropData.nextcmid;
            } else {
                const section = this.reactive.get('section', target.dataset.id);
                targetSectionId = target.dataset.id;
                targetCmId = section?.cmlist[0];
            }

            this.reactive.dispatch('cmMove', [cmId], targetSectionId, targetCmId);
            this._destroyModal(modal, editTools);
        });
    }

    /**
     * Handle a create section request.
     *
     * @param {Element} target the dispatch action element
     * @param {Event} event the triggered event
     */
    async _requestAddSection(target, event) {
        event.preventDefault();
        this.reactive.dispatch('addSection', target.dataset.id ?? 0);
    }

    /**
     * Handle a delete section request.
     *
     * @param {Element} target the dispatch action element
     * @param {Event} event the triggered event
     */
    async _requestDeleteSection(target, event) {
        // Check we have an id.
        const sectionId = target.dataset.id;

        if (!sectionId) {
            return;
        }
        const sectionInfo = this.reactive.get('section', sectionId);

        event.preventDefault();

        const cmList = sectionInfo.cmlist ?? [];
        if (cmList.length || sectionInfo.hassummary || sectionInfo.rawtitle) {
            // We need confirmation if the section has something.
            const modalParams = {
                title: getString('confirm', 'core'),
                body: getString('confirmdeletesection', 'moodle', sectionInfo.title),
                saveButtonText: getString('delete', 'core'),
                type: ModalFactory.types.SAVE_CANCEL,
            };

            const modal = await this._modalBodyRenderedPromise(modalParams);

            modal.getRoot().on(
                ModalEvents.save,
                e => {
                    // Stop the default save button behaviour which is to close the modal.
                    e.preventDefault();
                    modal.destroy();
                    this.reactive.dispatch('sectionDelete', [sectionId]);
                }
            );
            return;
        } else {
            // We don't need confirmation to delete empty sections.
            this.reactive.dispatch('sectionDelete', [sectionId]);
        }
    }

    /**
     * Disable all add sections actions.
     *
     * @param {boolean} locked the new locked value.
     */
    _setAddSectionLocked(locked) {
        const targets = this.getElements(this.selectors.ADDSECTION);
        targets.forEach(element => {
            element.classList.toggle(this.classes.DISABLED, locked);
            this.setElementLocked(element, locked);
        });
    }

    /**
     * Replace an element with a copy with a different tag name.
     *
     * @param {Element} element the original element
     */
    _disableLink(element) {
        if (element) {
            element.style.pointerEvents = 'none';
            element.style.userSelect = 'none';
            element.classList.add(this.classes.DISABLED);
            element.setAttribute('aria-disabled', true);
            element.addEventListener('click', event => event.preventDefault());
        }
    }

    /**
     * Render a modal and return a body ready promise.
     *
     * @param {object} modalParams the modal params
     * @return {Promise} the modal body ready promise
     */
    _modalBodyRenderedPromise(modalParams) {
        return new Promise((resolve, reject) => {
            ModalFactory.create(modalParams).then((modal) => {
                modal.setRemoveOnClose(true);
                // Handle body loading event.
                modal.getRoot().on(ModalEvents.bodyRendered, () => {
                    resolve(modal);
                });
                // Configure some extra modal params.
                if (modalParams.saveButtonText !== undefined) {
                    modal.setSaveButtonText(modalParams.saveButtonText);
                }
                modal.show();
                return;
            }).catch(() => {
                reject(`Cannot load modal content`);
            });
        });
    }

    /**
     * Hide and later destroy a modal.
     *
     * Behat will fail if we remove the modal while some boostrap collapse is executing.
     *
     * @param {Modal} modal
     * @param {HTMLElement} element the dom element to focus on.
     */
    _destroyModal(modal, element) {
        modal.hide();
        const pendingDestroy = new Pending(`courseformat/actions:destroyModal`);
        if (element) {
            element.focus();
        }
        setTimeout(() =>{
            modal.destroy();
            pendingDestroy.resolve();
        }, 500);
    }

    /**
     * Get the closest actions menu toggler to an action element.
     *
     * @param {HTMLElement} element the action link element
     * @returns {HTMLElement|undefined}
     */
    _getClosestActionMenuToogler(element) {
        const actionMenu = element.closest(this.selectors.ACTIONMENU);
        if (!actionMenu) {
            return undefined;
        }
        return actionMenu.querySelector(this.selectors.ACTIONMENUTOGGLER);
    }
}
