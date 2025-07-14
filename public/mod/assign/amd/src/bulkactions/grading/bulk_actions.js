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
 * Class for defining the bulk actions area in the assignment grading page.
 *
 * @module     mod_assign/bulkactions/grading/bulk_actions
 * @copyright  2024 Shamim Rezaie <shamim@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import BulkActions from 'core/bulkactions/bulk_actions';
import GeneralAction from './general_action';
import DeleteAction from './delete';
import ExtendAction from './extend';
import MessageAction from './message';
import SetMarkingAllocationAction from './setmarkingallocation';
import SetMarkingWorkflowStateAction from './setmarkingworkflowstate';
import Templates from 'core/templates';
import {getString} from 'core/str';

const Selectors = {
    selectBulkItemCheckbox: 'input[type="checkbox"][name="selectedusers"]',
    selectBulkItemTrigger: 'input[type="checkbox"][name="selectedusers"], input[type="checkbox"][name="selectall"]',
};

export default class extends BulkActions {
    /** @type {number} The course module ID. */
    #cmid;

    /** @type {boolean} Whether to show the extend action. */
    #extend;

    /** @type {boolean} Whether to show the grant extension action. */
    #grantAttempt;

    /** @type {boolean} Whether to show the set marking allocation action. */
    #markingAllocation;

    /** @type {string} Whether to show the message action. */
    #message;

    /** @type {Array} The list of plugin operations. */
    #pluginOperations;

    /** @type {boolean} Whether to show the remove submission action. */
    #removeSubmission;

    /** @type {string} The session key. */
    #sesskey;

    /** @type {boolean} Whether to show the revert to draft action. */
    #submissionDrafts;

    /** @type {boolean} Whether to show the set workflow state action. */
    #workflowState;

    /** @type {boolean} Whether this assignment supports submissions. */
    #supportsSubmissions;

    /** @type {boolean} Whether this assignment has submissions. */
    #hasSubmissions;

    /**
     * Returns the instance of the class.
     *
     * @param {Object} options - The options object.
     * @param {number} options.cmid - The course module ID.
     * @param {string} options.message - Whether to show the message action.
     * @param {boolean} options.submissiondrafts - Whether to show the revert to draft action.
     * @param {boolean} options.removesubmission - Whether to show the remove submission action.
     * @param {boolean} options.extend - Whether to show the grant extension action.
     * @param {boolean} options.grantattempt - Whether to show the grant attempt action.
     * @param {boolean} options.workflowstate - Whether to show the set workflow state action.
     * @param {boolean} options.markingallocation - Whether to show the set marking allocation action.
     * @param {Array} options.pluginoperations - The list of plugin operations.
     * @param {string} options.sesskey - The session key.
     * @param {boolean} options.supportssubmissions - Whether this assignment supports submissions.
     * @param {boolean} options.hassubmissions - Whether this assignment has submissions.
     * @returns {this} An instance of the anonymous class extending BulkActions.
     */
    static init(options) {
        return new this(options);
    }

    /**
     * The class constructor
     *
     * @param {Object} options - The options object.
     * @param {number} options.cmid - The course module ID.
     * @param {string} options.message - Whether to show the message action.
     * @param {boolean} options.submissiondrafts - Whether to show the revert to draft action.
     * @param {boolean} options.removesubmission - Whether to show the remove submission action.
     * @param {boolean} options.extend - Whether to show the grant extension action.
     * @param {boolean} options.grantattempt - Whether to show the grant attempt action.
     * @param {boolean} options.workflowstate - Whether to show the set workflow state action.
     * @param {boolean} options.markingallocation - Whether to show the set marking allocation action.
     * @param {Array} options.pluginoperations - The list of plugin operations.
     * @param {string} options.sesskey - The session key.
     * @param {boolean} options.hassubmissions - Whether this assignment has any submissions.
     * @param {boolean} options.supportssubmissions - Whether this assignment allows submissions.
     */
    constructor({
        cmid, message, submissiondrafts, removesubmission, extend,
        grantattempt, workflowstate, markingallocation, pluginoperations, sesskey,
        hassubmissions, supportssubmissions
    }) {
        super();
        this.#cmid = cmid;
        this.#message = message;
        this.#submissionDrafts = submissiondrafts;
        this.#removeSubmission = removesubmission;
        this.#extend = extend;
        this.#grantAttempt = grantattempt;
        this.#workflowState = workflowstate;
        this.#markingAllocation = markingallocation;
        this.#sesskey = sesskey;
        this.#pluginOperations = pluginoperations;
        this.#hasSubmissions = hassubmissions;
        this.#supportsSubmissions = supportssubmissions;
    }

    getBulkActions() {
        const actions = [];
        if (this.#supportsSubmissions) {
            actions.push(
                new GeneralAction(
                    this.#cmid,
                    this.#sesskey,
                    'lock',
                    getString('batchoperationlock', 'mod_assign'),
                    Templates.renderPix('i/lock', 'core'),
                    getString('locksubmissions', 'mod_assign'),
                    getString('batchoperationconfirmlock', 'mod_assign'),
                    getString('batchoperationlock', 'mod_assign'),
                ),
                new GeneralAction(
                    this.#cmid,
                    this.#sesskey,
                    'unlock',
                    getString('batchoperationunlock', 'mod_assign'),
                    Templates.renderPix('i/unlock', 'core'),
                    getString('unlocksubmissions', 'mod_assign'),
                    getString('batchoperationconfirmunlock', 'mod_assign'),
                    getString('batchoperationunlock', 'mod_assign'),
                ),
            );
        }
        if (this.#supportsSubmissions && this.#hasSubmissions) {
            actions.push(
                new GeneralAction(
                    this.#cmid,
                    this.#sesskey,
                    'downloadselected',
                    getString('batchoperationdownloadselected', 'mod_assign'),
                    Templates.renderPix('t/download', 'core'),
                    getString('downloadselectedsubmissions', 'mod_assign'),
                    getString('batchoperationconfirmdownloadselected', 'mod_assign'),
                    getString('batchoperationdownloadselected', 'mod_assign'),
                ),
            );
        }
        if (this.#removeSubmission && this.#supportsSubmissions && this.#hasSubmissions) {
            actions.push(new DeleteAction(this.#cmid, this.#sesskey));
        }

        if (this.#extend) {
            actions.push(new ExtendAction(this.#cmid, this.#sesskey));
        }

        if (this.#grantAttempt) {
            actions.push(
                new GeneralAction(
                    this.#cmid,
                    this.#sesskey,
                    'addattempt',
                    getString('batchoperationaddattempt', 'mod_assign'),
                    Templates.renderPix('t/add', 'core'),
                    getString('addattempt', 'mod_assign'),
                    getString('batchoperationconfirmaddattempt', 'mod_assign'),
                    getString('batchoperationaddattempt', 'mod_assign'),
                )
            );
        }

        if (this.#workflowState) {
            actions.push(new SetMarkingWorkflowStateAction(this.#cmid, this.#sesskey));
        }

        if (this.#markingAllocation) {
            actions.push(new SetMarkingAllocationAction(this.#cmid, this.#sesskey));
        }

        if (this.#submissionDrafts) {
            actions.push(
                new GeneralAction(
                    this.#cmid,
                    this.#sesskey,
                    'reverttodraft',
                    getString('batchoperationreverttodraft', 'mod_assign'),
                    Templates.renderPix('e/undo', 'core'),
                    getString('reverttodraft', 'mod_assign'),
                    getString('batchoperationconfirmreverttodraft', 'mod_assign'),
                    getString('batchoperationreverttodraft', 'mod_assign'),
                )
            );
        }

        if (this.#message) {
            actions.push(new MessageAction());
        }

        for (const operation of this.#pluginOperations) {
            actions.push(
                new GeneralAction(
                    this.#cmid,
                    this.#sesskey,
                    operation.key,
                    operation.label,
                    operation.icon,
                    operation.confirmationtitle,
                    operation.confirmationquestion,
                )
            );
        }
        return actions;
    }

    getSelectedItems() {
        return document.querySelectorAll(`${Selectors.selectBulkItemCheckbox}:checked`);
    }

    registerItemSelectChangeEvent(eventHandler) {
        const itemSelectCheckboxes = document.querySelectorAll(Selectors.selectBulkItemTrigger);
        itemSelectCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', eventHandler.bind(this));
        });
    }

    deselectItem(selectedItem) {
        selectedItem.checked = false;
        selectedItem.closest('tr').classList.replace('selectedrow', 'unselectedrow');
    }
}
