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
 * Bulk selection auxiliar methods.
 *
 * @module     core_courseformat/local/content/actions/bulkselection
 * @class      core_courseformat/local/content/actions/bulkselection
 * @copyright  2023 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class BulkSelector {

    /**
     * The class constructor.
     * @param {CourseEditor} courseEditor the original actions component.
     */
    constructor(courseEditor) {
        this.courseEditor = courseEditor;
        this.selectors = {
            BULKCMCHECKBOX: `[data-bulkcheckbox][data-action='toggleSelectionCm']`,
            BULKSECTIONCHECKBOX: `[data-bulkcheckbox][data-action='toggleSelectionSection']`,
            CONTENT: `#region-main`,
        };
    }

    /**
     * Process a new selection.
     * @param {Number} id
     * @param {String} elementType cm or section
     * @param {Object} settings special selection settings
     * @param {Boolean} settings.all if the action is over all elements of the same type
     * @param {Boolean} settings.range if the action is over a range of elements
     */
    processNewSelection(id, elementType, settings) {
        const value = !this._isBulkSelected(id, elementType);
        if (settings.all && settings.range) {
            this.switchCurrentSelection();
            return;
        }
        if (!this._isSelectable(id, elementType)) {
            return;
        }
        if (settings.all) {
            if (elementType == 'cm') {
                this._updateBulkCmSiblings(id, value);
            } else {
                this._updateBulkSelectionAll(elementType, value);
            }
            return;
        }
        if (settings.range) {
            this._updateBulkSelectionRange(id, elementType, value);
            return;
        }
        this._updateBulkSelection([id], elementType, value);
    }

    /**
     * Switch between section and cm selection.
     */
    switchCurrentSelection() {
        const bulk = this.courseEditor.get('bulk');
        if (bulk.selectedType === '' || bulk.selection.length == 0) {
            return;
        }
        const newSelectedType = (bulk.selectedType === 'section') ? 'cm' : 'section';
        let newSelectedIds;
        if (bulk.selectedType === 'section') {
            newSelectedIds = this._getCmIdsFromSections(bulk.selection);
        } else {
            newSelectedIds = this._getSectionIdsFromCms(bulk.selection);
        }
        // Formats can display only a few activities of the section,
        // We need to select on the activities present in the page.
        const affectedIds = [];
        newSelectedIds.forEach(newId => {
            if (this._getSelector(newId, newSelectedType)) {
                affectedIds.push(newId);
            }
        });
        this.courseEditor.dispatch('bulkEnable', true);
        if (affectedIds.length != 0) {
            this._updateBulkSelection(affectedIds, newSelectedType, true);
        }
    }

    /**
     * Select all elements of the current type.
     * @param {Boolean} value the wanted selected value
     */
    selectAll(value) {
        const bulk = this.courseEditor.get('bulk');
        if (bulk.selectedType == '') {
            return;
        }
        if (!value) {
            this.courseEditor.dispatch('bulkEnable', true);
            return;
        }
        const elementType = bulk.selectedType;
        this._updateBulkSelectionAll(elementType, value);
    }

    /**
     * Checks if all selectable elements are selected.
     * @returns {Boolean} true if all are selected
     */
    checkAllSelected() {
        const bulk = this.courseEditor.get('bulk');
        if (bulk.selectedType == '') {
            return false;
        }
        return this._getContentCheckboxes(bulk.selectedType).every(bulkSelect => {
            if (bulkSelect.disabled) {
                return true;
            }
            // Some sections may not be selectale for bulk actions.
            if (bulk.selectedType == 'section') {
                const section = this.courseEditor.get('section', bulkSelect.dataset.id);
                if (!section.bulkeditable) {
                    return true;
                }
            }
            return bulk.selection.includes(bulkSelect.dataset.id);
        });
    }

    /**
     * Check if the id is part of the current bulk selection.
     * @private
     * @param {Number} id
     * @param {String} elementType
     * @returns {Boolean} if the element is present in the current selection.
     */
    _isBulkSelected(id, elementType) {
        const bulk = this.courseEditor.get('bulk');
        if (bulk.selectedType !== elementType) {
            return false;
        }
        return bulk.selection.includes(id);
    }

    /**
     * Update the current bulk selection removing or adding Ids.
     * @private
     * @param {Number[]} ids the user selected element id
     * @param {String} elementType cm or section
     * @param {Boolean} value the wanted selected value
     */
    _updateBulkSelection(ids, elementType, value) {
        let mutation = elementType;
        mutation += (value) ? 'Select' : 'Unselect';
        this.courseEditor.dispatch(mutation, ids);
    }

    /**
     * Get all content bulk selector checkboxes of one type (section/cm).
     * @private
     * @param {String} elementType section or cm
     * @returns {HTMLElement[]} an array with all checkboxes
     */
    _getContentCheckboxes(elementType) {
        const selector = (elementType == 'cm') ? this.selectors.BULKCMCHECKBOX : this.selectors.BULKSECTIONCHECKBOX;
        const checkboxes = document.querySelectorAll(`${this.selectors.CONTENT} ${selector}`);
        // Converting to array because NodeList has less iteration methods.
        return [...checkboxes];
    }

    /**
     * Validate if an element is selectable in the current page.
     * @private
     * @param {Number} id the user selected element id
     * @param {String} elementType cm or section
     * @return {Boolean}
     */
    _isSelectable(id, elementType) {
        const bulkSelect = this._getSelector(id, elementType);
        if (!bulkSelect || bulkSelect.disabled) {
            return false;
        }
        return true;
    }

    /**
     * Get as specific element checkbox.
     * @private
     * @param {Number} id
     * @param {String} elementType cm or section
     * @returns {HTMLElement|undefined}
     */
    _getSelector(id, elementType) {
        let selector = (elementType == 'cm') ? this.selectors.BULKCMCHECKBOX : this.selectors.BULKSECTIONCHECKBOX;
        selector += `[data-id='${id}']`;
        return document.querySelector(`${this.selectors.CONTENT} ${selector}`);
    }

    /**
     * Update the current bulk selection when a user uses shift to select a range.
     * @private
     * @param {Number} id the user selected element id
     * @param {String} elementType cm or section
     * @param {Boolean} value the wanted selected value
     */
    _updateBulkSelectionRange(id, elementType, value) {
        const bulk = this.courseEditor.get('bulk');
        let lastSelectedId = bulk.selection.at(-1);
        if (bulk.selectedType !== elementType || lastSelectedId == id) {
            this._updateBulkSelection([id], elementType, value);
            return;
        }
        const affectedIds = [];
        let found = 0;
        this._getContentCheckboxes(elementType).every(bulkSelect => {
            if (bulkSelect.disabled) {
                return true;
            }
            if (elementType == 'section') {
                const section = this.courseEditor.get('section', bulkSelect.dataset.id);
                if (value && !section?.bulkeditable) {
                    return true;
                }
            }
            if (bulkSelect.dataset.id == id || bulkSelect.dataset.id == lastSelectedId) {
                found++;
            }
            if (found == 0) {
                return true;
            }
            affectedIds.push(bulkSelect.dataset.id);
            return found != 2;
        });
        this._updateBulkSelection(affectedIds, elementType, value);
    }

    /**
     * Select or unselect all cm siblings.
     * @private
     * @param {Number} cmId the user selected element id
     * @param {Boolean} value the wanted selected value
     */
    _updateBulkCmSiblings(cmId, value) {
        const bulk = this.courseEditor.get('bulk');
        if (bulk.selectedType === 'section') {
            return;
        }
        const cm = this.courseEditor.get('cm', cmId);
        const section = this.courseEditor.get('section', cm.sectionid);
        // Formats can display only a few activities of the section,
        // We need to select on the activities selectable in the page.
        const affectedIds = [];
        section.cmlist.forEach(sectionCmId => {
            if (this._isSelectable(sectionCmId, 'cm')) {
                affectedIds.push(sectionCmId);
            }
        });
        this._updateBulkSelection(affectedIds, 'cm', value);
    }

    /**
     * Select or unselects al elements of the same type.
     * @private
     * @param {String} elementType section or cm
     * @param {Boolean} value if the elements must be selected or unselected.
     */
    _updateBulkSelectionAll(elementType, value) {
        const affectedIds = [];
        this._getContentCheckboxes(elementType).forEach(bulkSelect => {
            if (bulkSelect.disabled) {
                return;
            }
            if (elementType == 'section') {
                const section = this.courseEditor.get('section', bulkSelect.dataset.id);
                if (value && !section?.bulkeditable) {
                    return;
                }
            }
            affectedIds.push(bulkSelect.dataset.id);
        });
        this._updateBulkSelection(affectedIds, elementType, value);
    }

    /**
     * Get all cm ids from a specific section ids.
     * @private
     * @param {Number[]} sectionIds
     * @returns {Number[]} the cm ids
     */
    _getCmIdsFromSections(sectionIds) {
        const result = [];
        sectionIds.forEach(sectionId => {
            const section = this.courseEditor.get('section', sectionId);
            result.push(...section.cmlist);
        });
        return result;
    }

    /**
     * Get all section ids containing a specific cm ids.
     * @private
     * @param {Number[]} cmIds
     * @returns {Number[]} the section ids
     */
    _getSectionIdsFromCms(cmIds) {
        const result = new Set();
        cmIds.forEach(cmId => {
            const cm = this.courseEditor.get('cm', cmId);
            if (cm.sectionnumber == 0) {
                return;
            }
            result.add(cm.sectionid);
        });
        return [...result];
    }
}

/**
 * Process a bulk selection toggle action.
 * @method
 * @param {CourseEditor} courseEditor
 * @param {HTMLElement} target the action element
 * @param {Event} event
 * @param {String} elementType cm or section
 */
export const toggleBulkSelectionAction = function(courseEditor, target, event, elementType) {
    const id = target.dataset.id;
    if (!id) {
        return;
    }
    // When the action cames from a form element (checkbox) we should not preventDefault.
    // If we do it the changechecker module will execute the state change twice.
    if (target.dataset.preventDefault) {
        event.preventDefault();
    }
    // Using shift or alt key can produce text selection.
    document.getSelection().removeAllRanges();

    const bulkSelector = new BulkSelector(courseEditor);
    bulkSelector.processNewSelection(
        id,
        elementType,
        {
            range: event.shiftKey,
            all: event.altKey,
        }
    );
};

/**
 * Switch the current bulk selection.
 * @method
 * @param {CourseEditor} courseEditor
 */
export const switchBulkSelection = function(courseEditor) {
    const bulkSelector = new BulkSelector(courseEditor);
    bulkSelector.switchCurrentSelection();
};

/**
 * Select/unselect all element of the selected type.
 * @method
 * @param {CourseEditor} courseEditor
 * @param {Boolean} value if the elements must be selected or unselected.
 */
export const selectAllBulk = function(courseEditor, value) {
    const bulkSelector = new BulkSelector(courseEditor);
    bulkSelector.selectAll(value);
};

/**
 * Check if all possible elements are selected.
 * @method
 * @param {CourseEditor} courseEditor
 * @return {Boolean} if all elements of the current type are selected.
 */
export const checkAllBulkSelected = function(courseEditor) {
    const bulkSelector = new BulkSelector(courseEditor);
    return bulkSelector.checkAllSelected();
};
