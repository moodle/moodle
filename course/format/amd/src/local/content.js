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
 * Course index main component.
 *
 * @module     core_courseformat/local/content
 * @class      core_courseformat/local/content
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {BaseComponent} from 'core/reactive';
import {debounce} from 'core/utils';
import {getCurrentCourseEditor} from 'core_courseformat/courseeditor';
import Config from 'core/config';
import inplaceeditable from 'core/inplace_editable';
import Section from 'core_courseformat/local/content/section';
import CmItem from 'core_courseformat/local/content/section/cmitem';
import Fragment from 'core/fragment';
import Templates from 'core/templates';
import DispatchActions from 'core_courseformat/local/content/actions';
import * as CourseEvents from 'core_course/events';
// The jQuery module is only used for interacting with Boostrap 4. It can we removed when MDL-71979 is integrated.
import jQuery from 'jquery';
import Pending from 'core/pending';

export default class Component extends BaseComponent {

    /**
     * Constructor hook.
     *
     * @param {Object} descriptor the component descriptor
     */
    create(descriptor) {
        // Optional component name for debugging.
        this.name = 'course_format';
        // Default query selectors.
        this.selectors = {
            SECTION: `[data-for='section']`,
            SECTION_ITEM: `[data-for='section_title']`,
            SECTION_CMLIST: `[data-for='cmlist']`,
            COURSE_SECTIONLIST: `[data-for='course_sectionlist']`,
            CM: `[data-for='cmitem']`,
            TOGGLER: `[data-action="togglecoursecontentsection"]`,
            COLLAPSE: `[data-toggle="collapse"]`,
            TOGGLEALL: `[data-toggle="toggleall"]`,
            // Formats can override the activity tag but a default one is needed to create new elements.
            ACTIVITYTAG: 'li',
            SECTIONTAG: 'li',
        };
        this.selectorGenerators = {
            cmNameFor: (id) => `[data-cm-name-for='${id}']`,
            sectionNameFor: (id) => `[data-section-name-for='${id}']`,
        };
        // Default classes to toggle on refresh.
        this.classes = {
            COLLAPSED: `collapsed`,
            // Course content classes.
            ACTIVITY: `activity`,
            STATEDREADY: `stateready`,
            SECTION: `section`,
        };
        // Array to save dettached elements during element resorting.
        this.dettachedCms = {};
        this.dettachedSections = {};
        // Index of sections and cms components.
        this.sections = {};
        this.cms = {};
        // The page section return.
        this.sectionReturn = descriptor.sectionReturn ?? null;
        this.debouncedReloads = new Map();
    }

    /**
     * Static method to create a component instance form the mustahce template.
     *
     * @param {string} target the DOM main element or its ID
     * @param {object} selectors optional css selector overrides
     * @param {number} sectionReturn the content section return
     * @return {Component}
     */
    static init(target, selectors, sectionReturn) {
        return new Component({
            element: document.getElementById(target),
            reactive: getCurrentCourseEditor(),
            selectors,
            sectionReturn,
        });
    }

    /**
     * Initial state ready method.
     *
     * @param {Object} state the state data
     */
    stateReady(state) {
        this._indexContents();
        // Activate section togglers.
        this.addEventListener(this.element, 'click', this._sectionTogglers);

        // Collapse/Expand all sections button.
        const toogleAll = this.getElement(this.selectors.TOGGLEALL);
        if (toogleAll) {

            // Ensure collapse menu button adds aria-controls attribute referring to each collapsible element.
            const collapseElements = this.getElements(this.selectors.COLLAPSE);
            const collapseElementIds = [...collapseElements].map(element => element.id);
            toogleAll.setAttribute('aria-controls', collapseElementIds.join(' '));

            this.addEventListener(toogleAll, 'click', this._allSectionToggler);
            this.addEventListener(toogleAll, 'keydown', e => {
                // Collapse/expand all sections when Space key is pressed on the toggle button.
                if (e.key === ' ') {
                    this._allSectionToggler(e);
                }
            });
            this._refreshAllSectionsToggler(state);
        }

        if (this.reactive.supportComponents) {
            // Actions are only available in edit mode.
            if (this.reactive.isEditing) {
                new DispatchActions(this);
            }

            // Mark content as state ready.
            this.element.classList.add(this.classes.STATEDREADY);
        }

        // Capture completion events.
        this.addEventListener(
            this.element,
            CourseEvents.manualCompletionToggled,
            this._completionHandler
        );

        // Capture page scroll to update page item.
        this.addEventListener(
            document,
            "scroll",
            this._scrollHandler
        );
        setTimeout(() => {
            this._scrollHandler();
        }, 500);
    }

    /**
     * Setup sections toggler.
     *
     * Toggler click is delegated to the main course content element because new sections can
     * appear at any moment and this way we prevent accidental double bindings.
     *
     * @param {Event} event the triggered event
     */
    _sectionTogglers(event) {
        const sectionlink = event.target.closest(this.selectors.TOGGLER);
        const closestCollapse = event.target.closest(this.selectors.COLLAPSE);
        // Assume that chevron is the only collapse toggler in a section heading;
        // I think this is the most efficient way to verify at the moment.
        const isChevron = closestCollapse?.closest(this.selectors.SECTION_ITEM);

        if (sectionlink || isChevron) {

            const section = event.target.closest(this.selectors.SECTION);
            const toggler = section.querySelector(this.selectors.COLLAPSE);
            const isCollapsed = toggler?.classList.contains(this.classes.COLLAPSED) ?? false;

            const sectionId = section.getAttribute('data-id');
            this.reactive.dispatch(
                'sectionContentCollapsed',
                [sectionId],
                !isCollapsed,
            );
        }
    }

    /**
     * Handle the collapse/expand all sections button.
     *
     * Toggler click is delegated to the main course content element because new sections can
     * appear at any moment and this way we prevent accidental double bindings.
     *
     * @param {Event} event the triggered event
     */
    _allSectionToggler(event) {
        event.preventDefault();

        const target = event.target.closest(this.selectors.TOGGLEALL);
        const isAllCollapsed = target.classList.contains(this.classes.COLLAPSED);

        const course = this.reactive.get('course');
        this.reactive.dispatch(
            'sectionContentCollapsed',
            course.sectionlist ?? [],
            !isAllCollapsed
        );
    }

    /**
     * Return the component watchers.
     *
     * @returns {Array} of watchers
     */
    getWatchers() {
        // Section return is a global page variable but most formats define it just before start printing
        // the course content. This is the reason why we define this page setting here.
        this.reactive.sectionReturn = this.sectionReturn;

        // Check if the course format is compatible with reactive components.
        if (!this.reactive.supportComponents) {
            return [];
        }
        return [
            // State changes that require to reload some course modules.
            {watch: `cm.visible:updated`, handler: this._reloadCm},
            {watch: `cm.stealth:updated`, handler: this._reloadCm},
            {watch: `cm.sectionid:updated`, handler: this._reloadCm},
            {watch: `cm.indent:updated`, handler: this._reloadCm},
            {watch: `cm.groupmode:updated`, handler: this._reloadCm},
            {watch: `cm.name:updated`, handler: this._refreshCmName},
            // Update section number and title.
            {watch: `section.number:updated`, handler: this._refreshSectionNumber},
            {watch: `section.title:updated`, handler: this._refreshSectionTitle},
            // Collapse and expand sections.
            {watch: `section.contentcollapsed:updated`, handler: this._refreshSectionCollapsed},
            // Sections and cm sorting.
            {watch: `transaction:start`, handler: this._startProcessing},
            {watch: `course.sectionlist:updated`, handler: this._refreshCourseSectionlist},
            {watch: `section.cmlist:updated`, handler: this._refreshSectionCmlist},
            // Section visibility.
            {watch: `section.visible:updated`, handler: this._reloadSection},
            // Reindex sections and cms.
            {watch: `state:updated`, handler: this._indexContents},
        ];
    }

    /**
     * Update a course module name on the whole page.
     *
     * @param {object} param
     * @param {Object} param.element details the update details.
     */
    _refreshCmName({element}) {
        // Update classes.
        // Replace the text content of the cm name.
        const allCmNamesFor = this.getElements(
            this.selectorGenerators.cmNameFor(element.id)
        );
        allCmNamesFor.forEach((cmNameFor) => {
            cmNameFor.textContent = element.name;
        });
    }

    /**
     * Update section collapsed state via bootstrap 4 if necessary.
     *
     * Formats that do not use bootstrap 4 must override this method in order to keep the section
     * toggling working.
     *
     * @param {object} args
     * @param {Object} args.state The state data
     * @param {Object} args.element The element to update
     */
    _refreshSectionCollapsed({state, element}) {
        const target = this.getElement(this.selectors.SECTION, element.id);
        if (!target) {
            throw new Error(`Unknown section with ID ${element.id}`);
        }
        // Check if it is already done.
        const toggler = target.querySelector(this.selectors.COLLAPSE);
        const isCollapsed = toggler?.classList.contains(this.classes.COLLAPSED) ?? false;

        if (element.contentcollapsed !== isCollapsed) {
            let collapsibleId = toggler.dataset.target ?? toggler.getAttribute("href");
            if (!collapsibleId) {
                return;
            }
            collapsibleId = collapsibleId.replace('#', '');
            const collapsible = document.getElementById(collapsibleId);
            if (!collapsible) {
                return;
            }

            // Course index is based on Bootstrap 4 collapsibles. To collapse them we need jQuery to
            // interact with collapsibles methods. Hopefully, this will change in Bootstrap 5 because
            // it does not require jQuery anymore (when MDL-71979 is integrated).
            jQuery(collapsible).collapse(element.contentcollapsed ? 'hide' : 'show');
        }

        this._refreshAllSectionsToggler(state);
    }

    /**
     * Refresh the collapse/expand all sections element.
     *
     * @param {Object} state The state data
     */
    _refreshAllSectionsToggler(state) {
        const target = this.getElement(this.selectors.TOGGLEALL);
        if (!target) {
            return;
        }
        // Check if we have all sections collapsed/expanded.
        let allcollapsed = true;
        let allexpanded = true;
        state.section.forEach(
            section => {
                allcollapsed = allcollapsed && section.contentcollapsed;
                allexpanded = allexpanded && !section.contentcollapsed;
            }
        );
        if (allcollapsed) {
            target.classList.add(this.classes.COLLAPSED);
            target.setAttribute('aria-expanded', false);
        }
        if (allexpanded) {
            target.classList.remove(this.classes.COLLAPSED);
            target.setAttribute('aria-expanded', true);
        }
    }

    /**
     * Setup the component to start a transaction.
     *
     * Some of the course actions replaces the current DOM element with a new one before updating the
     * course state. This means the component cannot preload any index properly until the transaction starts.
     *
     */
    _startProcessing() {
        // During a section or cm sorting, some elements could be dettached from the DOM and we
        // need to store somewhare in case they are needed later.
        this.dettachedCms = {};
        this.dettachedSections = {};
    }

    /**
     * Activity manual completion listener.
     *
     * @param {Event} event the custom ecent
     */
    _completionHandler({detail}) {
        if (detail === undefined) {
            return;
        }
        this.reactive.dispatch('cmCompletion', [detail.cmid], detail.completed);
    }

    /**
     * Check the current page scroll and update the active element if necessary.
     */
    _scrollHandler() {
        const pageOffset = window.scrollY;
        const items = this.reactive.getExporter().allItemsArray(this.reactive.state);
        // Check what is the active element now.
        let pageItem = null;
        items.every(item => {
            const index = (item.type === 'section') ? this.sections : this.cms;
            if (index[item.id] === undefined) {
                return true;
            }

            const element = index[item.id].element;
            pageItem = item;
            return pageOffset >= element.offsetTop;
        });
        if (pageItem) {
            this.reactive.dispatch('setPageItem', pageItem.type, pageItem.id);
        }
    }

    /**
     * Update a course section when the section number changes.
     *
     * The courseActions module used for most course section tools still depends on css classes and
     * section numbers (not id). To prevent inconsistencies when a section is moved, we need to refresh
     * the
     *
     * Course formats can override the section title rendering so the frontend depends heavily on backend
     * rendering. Luckily in edit mode we can trigger a title update using the inplace_editable module.
     *
     * @param {Object} param
     * @param {Object} param.element details the update details.
     */
    _refreshSectionNumber({element}) {
        // Find the element.
        const target = this.getElement(this.selectors.SECTION, element.id);
        if (!target) {
            // Job done. Nothing to refresh.
            return;
        }
        // Update section numbers in all data, css and YUI attributes.
        target.id = `section-${element.number}`;
        // YUI uses section number as section id in data-sectionid, in principle if a format use components
        // don't need this sectionid attribute anymore, but we keep the compatibility in case some plugin
        // use it for legacy purposes.
        target.dataset.sectionid = element.number;
        // The data-number is the attribute used by components to store the section number.
        target.dataset.number = element.number;

        // Update title and title inplace editable, if any.
        const inplace = inplaceeditable.getInplaceEditable(target.querySelector(this.selectors.SECTION_ITEM));
        if (inplace) {
            // The course content HTML can be modified at any moment, so the function need to do some checkings
            // to make sure the inplace editable still represents the same itemid.
            const currentvalue = inplace.getValue();
            const currentitemid = inplace.getItemId();
            // Unnamed sections must be recalculated.
            if (inplace.getValue() === '') {
                // The value to send can be an empty value if it is a default name.
                if (currentitemid == element.id && (currentvalue != element.rawtitle || element.rawtitle == '')) {
                    inplace.setValue(element.rawtitle);
                }
            }
        }
    }

    /**
     * Update a course section name on the whole page.
     *
     * @param {object} param
     * @param {Object} param.element details the update details.
     */
    _refreshSectionTitle({element}) {
        // Replace the text content of the section name in the whole page.
        const allSectionNamesFor = document.querySelectorAll(
            this.selectorGenerators.sectionNameFor(element.id)
        );
        allSectionNamesFor.forEach((sectionNameFor) => {
            sectionNameFor.textContent = element.title;
        });
    }

    /**
     * Refresh a section cm list.
     *
     * @param {Object} param
     * @param {Object} param.element details the update details.
     */
    _refreshSectionCmlist({element}) {
        const cmlist = element.cmlist ?? [];
        const section = this.getElement(this.selectors.SECTION, element.id);
        const listparent = section?.querySelector(this.selectors.SECTION_CMLIST);
        // A method to create a fake element to be replaced when the item is ready.
        const createCm = this._createCmItem.bind(this);
        if (listparent) {
            this._fixOrder(listparent, cmlist, this.selectors.CM, this.dettachedCms, createCm);
        }
    }

    /**
     * Refresh the section list.
     *
     * @param {Object} param
     * @param {Object} param.state the full state object.
     */
    _refreshCourseSectionlist({state}) {
        // If we have a section return means we only show a single section so no need to fix order.
        if (this.reactive.sectionReturn !== null) {
            return;
        }
        const sectionlist = this.reactive.getExporter().listedSectionIds(state);
        const listparent = this.getElement(this.selectors.COURSE_SECTIONLIST);
        // For now section cannot be created at a frontend level.
        const createSection = this._createSectionItem.bind(this);
        if (listparent) {
            this._fixOrder(listparent, sectionlist, this.selectors.SECTION, this.dettachedSections, createSection);
        }
    }

    /**
     * Regenerate content indexes.
     *
     * This method is used when a legacy action refresh some content element.
     */
    _indexContents() {
        // Find unindexed sections.
        this._scanIndex(
            this.selectors.SECTION,
            this.sections,
            (item) => {
                return new Section(item);
            }
        );

        // Find unindexed cms.
        this._scanIndex(
            this.selectors.CM,
            this.cms,
            (item) => {
                return new CmItem(item);
            }
        );
    }

    /**
     * Reindex a content (section or cm) of the course content.
     *
     * This method is used internally by _indexContents.
     *
     * @param {string} selector the DOM selector to scan
     * @param {*} index the index attribute to update
     * @param {*} creationhandler method to create a new indexed element
     */
    _scanIndex(selector, index, creationhandler) {
        const items = this.getElements(`${selector}:not([data-indexed])`);
        items.forEach((item) => {
            if (!item?.dataset?.id) {
                return;
            }
            // Delete previous item component.
            if (index[item.dataset.id] !== undefined) {
                index[item.dataset.id].unregister();
            }
            // Create the new component.
            index[item.dataset.id] = creationhandler({
                ...this,
                element: item,
            });
            // Mark as indexed.
            item.dataset.indexed = true;
        });
    }

    /**
     * Reload a course module contents.
     *
     * Most course module HTML is still strongly backend dependant.
     * Some changes require to get a new version of the module.
     *
     * @param {object} param0 the watcher details
     * @param {object} param0.element the state object
     */
    _reloadCm({element}) {
        if (!this.getElement(this.selectors.CM, element.id)) {
            return;
        }
        const debouncedReload = this._getDebouncedReloadCm(element.id);
        debouncedReload();
    }

    /**
     * Generate or get a reload CM debounced function.
     * @param {Number} cmId
     * @returns {Function} the debounced reload function
     */
    _getDebouncedReloadCm(cmId) {
        const pendingKey = `courseformat/content:reloadCm_${cmId}`;
        let debouncedReload = this.debouncedReloads.get(pendingKey);
        if (debouncedReload) {
            return debouncedReload;
        }
        const reload = () => {
            const pendingReload = new Pending(pendingKey);
            this.debouncedReloads.delete(pendingKey);
            const cmitem = this.getElement(this.selectors.CM, cmId);
            if (!cmitem) {
                return pendingReload.resolve();
            }
            const promise = Fragment.loadFragment(
                'core_courseformat',
                'cmitem',
                Config.courseContextId,
                {
                    id: cmId,
                    courseid: Config.courseId,
                    sr: this.reactive.sectionReturn ?? null,
                }
            );
            promise.then((html, js) => {
                // Other state change can reload the CM or the section before this one.
                if (!document.contains(cmitem)) {
                    pendingReload.resolve();
                    return false;
                }
                Templates.replaceNode(cmitem, html, js);
                this._indexContents();
                pendingReload.resolve();
                return true;
            }).catch(() => {
                pendingReload.resolve();
            });
            return pendingReload;
        };
        debouncedReload = debounce(
            reload,
            200,
            {
                cancel: true, pending: true
            }
        );
        this.debouncedReloads.set(pendingKey, debouncedReload);
        return debouncedReload;
    }

    /**
     * Cancel the active reload CM debounced function, if any.
     * @param {Number} cmId
     */
    _cancelDebouncedReloadCm(cmId) {
        const pendingKey = `courseformat/content:reloadCm_${cmId}`;
        const debouncedReload = this.debouncedReloads.get(pendingKey);
        if (!debouncedReload) {
            return;
        }
        debouncedReload.cancel();
        this.debouncedReloads.delete(pendingKey);
    }

    /**
     * Reload a course section contents.
     *
     * Section HTML is still strongly backend dependant.
     * Some changes require to get a new version of the section.
     *
     * @param {details} param0 the watcher details
     * @param {object} param0.element the state object
     */
    _reloadSection({element}) {
        const pendingReload = new Pending(`courseformat/content:reloadSection_${element.id}`);
        const sectionitem = this.getElement(this.selectors.SECTION, element.id);
        if (sectionitem) {
            // Cancel any pending reload because the section will reload cms too.
            for (const cmId of element.cmlist) {
                this._cancelDebouncedReloadCm(cmId);
            }
            const promise = Fragment.loadFragment(
                'core_courseformat',
                'section',
                Config.courseContextId,
                {
                    id: element.id,
                    courseid: Config.courseId,
                    sr: this.reactive.sectionReturn ?? null,
                }
            );
            promise.then((html, js) => {
                Templates.replaceNode(sectionitem, html, js);
                this._indexContents();
                pendingReload.resolve();
            }).catch(() => {
                pendingReload.resolve();
            });
        }
    }

    /**
     * Create a new course module item in a section.
     *
     * Thos method will append a fake item in the container and trigger an ajax request to
     * replace the fake element by the real content.
     *
     * @param {Element} container the container element (section)
     * @param {Number} cmid the course-module ID
     * @returns {Element} the created element
     */
    _createCmItem(container, cmid) {
        const newItem = document.createElement(this.selectors.ACTIVITYTAG);
        newItem.dataset.for = 'cmitem';
        newItem.dataset.id = cmid;
        // The legacy actions.js requires a specific ID and class to refresh the CM.
        newItem.id = `module-${cmid}`;
        newItem.classList.add(this.classes.ACTIVITY);
        container.append(newItem);
        this._reloadCm({
            element: this.reactive.get('cm', cmid),
        });
        return newItem;
    }

    /**
     * Create a new section item.
     *
     * This method will append a fake item in the container and trigger an ajax request to
     * replace the fake element by the real content.
     *
     * @param {Element} container the container element (section)
     * @param {Number} sectionid the course-module ID
     * @returns {Element} the created element
     */
    _createSectionItem(container, sectionid) {
        const section = this.reactive.get('section', sectionid);
        const newItem = document.createElement(this.selectors.SECTIONTAG);
        newItem.dataset.for = 'section';
        newItem.dataset.id = sectionid;
        newItem.dataset.number = section.number;
        // The legacy actions.js requires a specific ID and class to refresh the section.
        newItem.id = `section-${sectionid}`;
        newItem.classList.add(this.classes.SECTION);
        container.append(newItem);
        this._reloadSection({
            element: section,
        });
        return newItem;
    }

    /**
     * Fix/reorder the section or cms order.
     *
     * @param {Element} container the HTML element to reorder.
     * @param {Array} neworder an array with the ids order
     * @param {string} selector the element selector
     * @param {Object} dettachedelements a list of dettached elements
     * @param {function} createMethod method to create missing elements
     */
    async _fixOrder(container, neworder, selector, dettachedelements, createMethod) {
        if (container === undefined) {
            return;
        }

        // Empty lists should not be visible.
        if (!neworder.length) {
            container.classList.add('hidden');
            container.innerHTML = '';
            return;
        }

        // Grant the list is visible (in case it was empty).
        container.classList.remove('hidden');

        // Move the elements in order at the beginning of the list.
        neworder.forEach((itemid, index) => {
            let item = this.getElement(selector, itemid) ?? dettachedelements[itemid] ?? createMethod(container, itemid);
            if (item === undefined) {
                // Missing elements cannot be sorted.
                return;
            }
            // Get the current elemnt at that position.
            const currentitem = container.children[index];
            if (currentitem === undefined) {
                container.append(item);
                return;
            }
            if (currentitem !== item) {
                container.insertBefore(item, currentitem);
            }
        });

        // Dndupload add a fake element we need to keep.
        let dndFakeActivity;

        // Remove the remaining elements.
        while (container.children.length > neworder.length) {
            const lastchild = container.lastChild;
            if (lastchild?.classList?.contains('dndupload-preview')) {
                dndFakeActivity = lastchild;
            } else {
                dettachedelements[lastchild?.dataset?.id ?? 0] = lastchild;
            }
            container.removeChild(lastchild);
        }
        // Restore dndupload fake element.
        if (dndFakeActivity) {
            container.append(dndFakeActivity);
        }
    }
}
