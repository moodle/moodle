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

// Load global strings.
prefetchStrings('core', ['movecoursesection', 'movecoursemodule']);

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
            SECTIONLINK: `[data-for='section']`,
            CMLINK: `[data-for='cm']`,
            SECTIONNODE: `[data-for='sectionnode']`,
            TOGGLER: `[data-toggle='collapse']`,
        };
        // Component css classes.
        this.classes = {
            DISABLED: `disabled`,
        };
    }

    /**
     * Initial state ready method.
     *
     */
    stateReady() {
        // Delegate dispatch clicks.
        this.addEventListener(
            this.element,
            'click',
            this._dispatchClick
        );
    }

    _dispatchClick(event) {
        const target = event.target.closest(this.selectors.ACTIONLINK);
        if (!target) {
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
            modal.destroy();
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

        // Open the cm section node if possible.
        currentElement.closest(this.selectors.SECTIONNODE)?.querySelector(this.selectors.TOGGLER)?.click();

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
            modal.destroy();
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
                // Handle body loading event.
                modal.getRoot().on(ModalEvents.bodyRendered, () => {
                    resolve(modal);
                });
                modal.show();
                return;
            }).catch(() => {
                reject(`Cannot load modal content`);
            });
        });
    }
}
