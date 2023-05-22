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
 * Allow the user to show and hide columns of the report at will.
 *
 * @module    gradereport_grader/collapse
 * @copyright 2023 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
import * as Repository from 'gradereport_grader/collapse/repository';
import GradebookSearchClass from 'gradereport_grader/search/search_class';
import {renderForPromise, replaceNodeContents, replaceNode} from 'core/templates';
import {debounce} from 'core/utils';
import $ from 'jquery';
import {get_strings as getStrings} from 'core/str';
import CustomEvents from "core/custom_interaction_events";
import storage from 'core/localstorage';
import {addIconToContainer} from 'core/loadingicon';
import Notification from 'core/notification';
import Pending from 'core/pending';

// Contain our selectors within this file until they could be of use elsewhere.
const selectors = {
    component: '.collapse-columns',
    formDropdown: '.columnsdropdownform',
    formItems: {
        cancel: 'cancel',
        save: 'save',
        checked: 'input[type="checkbox"]:checked'
    },
    hider: 'hide',
    expand: 'expand',
    colVal: '[data-col]',
    itemVal: '[data-itemid]',
    content: '[data-collapse="content"]',
    sort: '[data-collapse="sort"]',
    expandbutton: '[data-collapse="expandbutton"]',
    rangerowcell: '[data-collapse="rangerowcell"]',
    avgrowcell: '[data-collapse="avgrowcell"]',
    menu: '[data-collapse="menu"]',
    icons: '.data-collapse_gradeicons',
    count: '[data-collapse="count"]',
    placeholder: '.collapsecolumndropdown [data-region="placeholder"]',
    fullDropdown: '.collapsecolumndropdown',
};

const countIndicator = document.querySelector(selectors.count);

export default class ColumnSearch extends GradebookSearchClass {

    userID = -1;
    courseID = null;
    defaultSort = '';

    nodes = [];

    gradeStrings = null;
    userStrings = null;
    stringMap = [];

    static init(userID, courseID, defaultSort) {
        return new ColumnSearch(userID, courseID, defaultSort);
    }

    constructor(userID, courseID, defaultSort) {
        super();
        this.userID = userID;
        this.courseID = courseID;
        this.defaultSort = defaultSort;
        this.component = document.querySelector(selectors.component);

        const pendingPromise = new Pending();
        // Display a loader whilst collapsing appropriate columns (based on the locally stored state for the current user).
        addIconToContainer(document.querySelector('.gradeparent')).then((loader) => {
            setTimeout(() => {
                // Get the users' checked columns to change.
                this.getDataset().forEach((item) => {
                    this.nodesUpdate(item);
                });
                this.renderDefault();

                // Once the grade categories have been re-collapsed, remove the loader and display the Gradebook setup content.
                loader.remove();
                document.querySelector('.gradereport-grader-table').classList.remove('d-none');
            }, 10);
        }).then(() => pendingPromise.resolve()).catch(Notification.exception);
    }

    /**
     * The overall div that contains the searching widget.
     *
     * @returns {string}
     */
    setComponentSelector() {
        return '.collapse-columns';
    }

    /**
     * The dropdown div that contains the searching widget result space.
     *
     * @returns {string}
     */
    setDropdownSelector() {
        return '.searchresultitemscontainer';
    }

    /**
     * The triggering div that contains the searching widget.
     *
     * @returns {string}
     */
    setTriggerSelector() {
        return '.collapsecolumn';
    }

    /**
     * Return the dataset that we will be searching upon.
     *
     * @returns {Array}
     */
    getDataset() {
        if (!this.dataset) {
            const cols = this.fetchDataset();
            this.dataset = JSON.parse(cols) ? JSON.parse(cols).split(',') : [];
        }
        this.datasetSize = this.dataset.length;
        return this.dataset;
    }

    /**
     * Get the data we will be searching against in this component.
     *
     * @returns {string}
     */
    fetchDataset() {
        return storage.get(`gradereport_grader_collapseditems_${this.courseID}_${this.userID}`);
    }

    /**
     * Given a user performs an action, update the users' preferences.
     */
    setPreferences() {
        storage.set(`gradereport_grader_collapseditems_${this.courseID}_${this.userID}`,
            JSON.stringify(this.getDataset().join(','))
        );
    }

    /**
     * Register clickable event listeners.
     */
    registerClickHandlers() {
        // Register click events within the component.
        this.component.addEventListener('click', this.clickHandler.bind(this));

        document.addEventListener('click', this.docClickHandler.bind(this));
    }

    /**
     * The handler for when a user interacts with the component.
     *
     * @param {MouseEvent} e The triggering event that we are working with.
     */
    clickHandler(e) {
        super.clickHandler(e);
        // Prevent BS from closing the dropdown if they click elsewhere within the dropdown besides the form.
        if (e.target.closest(selectors.fullDropdown)) {
            e.stopPropagation();
        }
    }

    /**
     * Externally defined click function to improve memory handling.
     *
     * @param {MouseEvent} e
     * @returns {Promise<void>}
     */
    async docClickHandler(e) {
        if (e.target.dataset.hider === selectors.hider) {
            e.preventDefault();
            const desiredToHide = e.target.closest(selectors.colVal) ?
                e.target.closest(selectors.colVal)?.dataset.col :
                e.target.closest(selectors.itemVal)?.dataset.itemid;
            const idx = this.getDataset().indexOf(desiredToHide);
            if (idx === -1) {
                this.getDataset().push(desiredToHide);
            }
            await this.prefcountpippe();

            this.nodesUpdate(desiredToHide);
        }

        if (e.target.closest('button')?.dataset.hider === selectors.expand) {
            e.preventDefault();
            const desiredToHide = e.target.closest(selectors.colVal) ?
                e.target.closest(selectors.colVal)?.dataset.col :
                e.target.closest(selectors.itemVal)?.dataset.itemid;
            const idx = this.getDataset().indexOf(desiredToHide);
            this.getDataset().splice(idx, 1);

            await this.prefcountpippe();

            this.nodesUpdate(e.target.closest(selectors.colVal)?.dataset.col);
            this.nodesUpdate(e.target.closest(selectors.colVal)?.dataset.itemid);
        }
    }

    /**
     * The handler for when a user presses a key within the component.
     *
     * @param {KeyboardEvent} e The triggering event that we are working with.
     */
    async keyHandler(e) {
        super.keyHandler(e);

        // Switch the key presses to handle keyboard nav.
        switch (e.key) {
            case 'Tab':
                if (e.target.closest(this.selectors.input)) {
                    e.preventDefault();
                    this.clearSearchButton.focus({preventScroll: true});
                }
                break;
        }
    }

    /**
     * Handle any keyboard inputs.
     */
    registerInputEvents() {
        // Register & handle the text input.
        this.searchInput.addEventListener('input', debounce(async() => {
            this.setSearchTerms(this.searchInput.value);
            // We can also require a set amount of input before search.
            if (this.searchInput.value === '') {
                // Hide the "clear" search button in the search bar.
                this.clearSearchButton.classList.add('d-none');
            } else {
                // Display the "clear" search button in the search bar.
                this.clearSearchButton.classList.remove('d-none');
            }
            // User has given something for us to filter against.
            await this.filterrenderpipe();
        }, 300));
    }

    /**
     * Handle the form submission within the dropdown.
     */
    registerFormEvents() {
        const form = this.component.querySelector(selectors.formDropdown);
        const events = [
            'click',
            CustomEvents.events.activate,
            CustomEvents.events.keyboardActivate
        ];
        CustomEvents.define(document, events);

        // Register clicks & keyboard form handling.
        events.forEach((event) => {
            form.addEventListener(event, (e) => {
                // Stop Bootstrap from being clever.
                e.stopPropagation();
                const submitBtn = form.querySelector(`[data-action="${selectors.formItems.save}"`);
                if (e.target.closest('input')) {
                    const checkedCount = Array.from(form.querySelectorAll(selectors.formItems.checked)).length;
                    // Check if any are clicked or not then change disabled.
                    submitBtn.disabled = checkedCount <= 0;
                }
            }, false);

            // Stop Bootstrap from being clever.
            this.searchInput.addEventListener(event, e => e.stopPropagation());
            this.clearSearchButton.addEventListener(event, async(e) => {
                e.stopPropagation();
                this.searchInput.value = '';
                this.setSearchTerms(this.searchInput.value);
                await this.filterrenderpipe();
            });
        });

        form.addEventListener('submit', async(e) => {
            e.preventDefault();
            if (e.submitter.dataset.action === selectors.formItems.cancel) {
                $(this.component).dropdown('toggle');
                return;
            }
            // Get the users' checked columns to change.
            const checkedItems = [...form.elements].filter(item => item.checked);
            checkedItems.forEach((item) => {
                const idx = this.getDataset().indexOf(item.dataset.collapse);
                this.getDataset().splice(idx, 1);
                this.nodesUpdate(item.dataset.collapse);
            });
            await this.prefcountpippe();
        });
    }

    nodesUpdate(item) {
        const colNodesToHide = [...document.querySelectorAll(`[data-col="${item}"]`)];
        const itemIDNodesToHide = [...document.querySelectorAll(`[data-itemid="${item}"]`)];
        this.nodes = [...colNodesToHide, ...itemIDNodesToHide];
        this.updateDisplay();
    }

    /**
     * Update the user preferences, count display then render the results.
     *
     * @returns {Promise<void>}
     */
    async prefcountpippe() {
        this.setPreferences();
        this.countUpdate();
        await this.filterrenderpipe();
    }

    /**
     * Dictate to the search component how and what we want to match upon.
     *
     * @param {Array} filterableData
     * @returns {Array} An array of objects containing the system reference and the user readable value.
     */
    async filterDataset(filterableData) {
        const stringUserMap = await this.fetchRequiredUserStrings();
        const stringGradeMap = await this.fetchRequiredGradeStrings();
        // Custom user profile fields are not in our string map and need a bit of extra love.
        const customFieldMap = this.fetchCustomFieldValues();
        this.stringMap = new Map([...stringGradeMap, ...stringUserMap, ...customFieldMap]);

        const searching = filterableData.map(s => {
            const mapObj = this.stringMap.get(s);
            if (mapObj === undefined) {
                return {key: s, string: s};
            }
            return {
                key: s,
                string: mapObj.itemname ?? this.stringMap.get(s),
                category: mapObj.category ?? '',
            };
        });
        // Sometimes we just want to show everything.
        if (this.getPreppedSearchTerm() === '') {
            return searching;
        }
        // Other times we want to actually filter the content.
        return searching.filter((col) => {
            return col.string.toString().toLowerCase().includes(this.getPreppedSearchTerm());
        });
    }

    /**
     * Given we have a subset of the dataset, set the field that we matched upon to inform the end user.
     */
    filterMatchDataset() {
        this.setMatchedResults(
            this.getMatchedResults().map((column) => {
                return {
                    name: column.key,
                    displayName: column.string ?? column.key,
                    category: column.category ?? '',
                };
            })
        );
    }

    /**
     * Update any changeable nodes, filter and then render the result.
     *
     * @returns {Promise<void>}
     */
    async filterrenderpipe() {
        this.updateNodes();
        this.setMatchedResults(await this.filterDataset(this.getDataset()));
        this.filterMatchDataset();
        await this.renderDropdown();
    }

    /**
     * With an array of nodes, switch their classes and values.
     */
    updateDisplay() {
        this.nodes.forEach((element) => {
            const content = element.querySelector(selectors.content);
            const sort = element.querySelector(selectors.sort);
            const expandButton = element.querySelector(selectors.expandbutton);
            const rangeRowCell = element.querySelector(selectors.rangerowcell);
            const avgRowCell = element.querySelector(selectors.avgrowcell);
            const nodeSet = [
                element.querySelector(selectors.menu),
                element.querySelector(selectors.icons),
                content
            ];

            // This can be further improved to reduce redundant similar calls.
            if (element.classList.contains('cell')) {
                // The column is actively being sorted, lets reset that and reload the page.
                if (sort !== null) {
                    window.location = this.defaultSort;
                }
                if (content === null) {
                    // If it's not a content cell, it must be an overall average or a range cell.
                    const rowCell = avgRowCell ?? rangeRowCell;

                    rowCell?.classList.toggle('d-none');
                    rowCell?.setAttribute('aria-hidden',
                        rowCell?.classList.contains('d-none') ? 'true' : 'false');
                } else if (content.classList.contains('d-none')) {
                    // We should always have content but some cells do not contain menus or other actions.
                    element.classList.remove('collapsed');
                    // If there are many nodes, apply the following.
                    if (content.childNodes.length > 1) {
                        content.classList.add('d-flex');
                    }
                    nodeSet.forEach(node => {
                        node?.classList.remove('d-none');
                        node?.setAttribute('aria-hidden', 'false');
                    });
                    expandButton?.classList.add('d-none');
                    expandButton?.setAttribute('aria-hidden', 'true');
                } else {
                    element.classList.add('collapsed');
                    content.classList.remove('d-flex');
                    nodeSet.forEach(node => {
                        node?.classList.add('d-none');
                        node?.setAttribute('aria-hidden', 'true');
                    });
                    expandButton?.classList.remove('d-none');
                    expandButton?.setAttribute('aria-hidden', 'false');
                }
            }
        });
    }

    /**
     * Update the visual count of collapsed columns or hide the count all together.
     */
    countUpdate() {
        countIndicator.textContent = this.getDatasetSize();
        if (this.getDatasetSize() > 0) {
            this.component.parentElement.classList.add('d-flex');
            this.component.parentElement.classList.remove('d-none');
        } else {
            this.component.parentElement.classList.remove('d-flex');
            this.component.parentElement.classList.add('d-none');
        }
    }

    /**
     * Build the content then replace the node by default we want our form to exist.
     */
    async renderDefault() {
        this.setMatchedResults(await this.filterDataset(this.getDataset()));
        this.filterMatchDataset();

        // Update the collapsed button pill.
        this.countUpdate();
        const {html, js} = await renderForPromise('gradereport_grader/collapse/collapsebody', {
            'results': this.getMatchedResults(),
            'userid': this.userID,
        });
        replaceNode(selectors.placeholder, html, js);
        this.updateNodes();

        // Given we now have the body, we can set up more triggers.
        this.registerFormEvents();
        this.registerInputEvents();

        // Add a small BS listener so that we can set the focus correctly on open.
        this.$component.on('shown.bs.dropdown', () => {
            this.searchInput.focus({preventScroll: true});
        });
    }

    /**
     * Build the content then replace the node.
     */
    async renderDropdown() {
        const {html, js} = await renderForPromise('gradereport_grader/collapse/collapseresults', {
            'results': this.getMatchedResults(),
            'searchTerm': this.getSearchTerm(),
        });
        replaceNodeContents(this.getHTMLElements().searchDropdown, html, js);
    }

    /**
     * If we have any custom user profile fields, grab their system & readable names to add to our string map.
     *
     * @returns {array<string,*>} An array of associated string arrays ready for our map.
     */
    fetchCustomFieldValues() {
        const customFields = document.querySelectorAll('[data-collapse-name]');
        // Cast from NodeList to array to grab all the values.
        return [...customFields].map(field => [field.parentElement.dataset.col, field.dataset.collapseName]);
    }

    /**
     * Given the set of profile fields we can possibly search, fetch their strings,
     * so we can report to screen readers the field that matched.
     *
     * @returns {Promise<void>}
     */
    fetchRequiredUserStrings() {
        if (!this.userStrings) {
            const requiredStrings = [
                'username',
                'firstname',
                'lastname',
                'email',
                'city',
                'country',
                'department',
                'institution',
                'idnumber',
                'phone1',
                'phone2',
            ];
            this.userStrings = getStrings(requiredStrings.map((key) => ({key})))
                .then((stringArray) => new Map(
                    requiredStrings.map((key, index) => ([key, stringArray[index]]))
                ));
        }
        return this.userStrings;
    }

    /**
     * Given the set of gradable items we can possibly search, fetch their strings,
     * so we can report to screen readers the field that matched.
     *
     * @returns {Promise<void>}
     */
    fetchRequiredGradeStrings() {
        if (!this.gradeStrings) {
            this.gradeStrings = Repository.gradeItems(this.courseID)
                .then((result) => new Map(
                    result.gradeItems.map(key => ([key.id, key]))
                ));
        }
        return this.gradeStrings;
    }
}
