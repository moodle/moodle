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
 * Class that defines the bulk move action in the gradebook setup page.
 *
 * @module     core_grades/bulkactions/edit/tree/move
 * @copyright  2023 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import BulkAction from 'core/bulkactions/bulk_action';
import {get_string as getString} from 'core/str';
import ModalSaveCancel from 'core/modal_save_cancel';
import Templates from 'core/templates';
import Ajax from 'core/ajax';
import ModalEvents from 'core/modal_events';
import MoveOptionsTree from 'core_grades/bulkactions/edit/tree/move_options_tree';

/** @constant {Object} The object containing the relevant selectors. */
const Selectors = {
    editTreeForm: '#gradetreeform',
    bulkMoveInput: 'input[name="bulkmove"]',
    bulkMoveAfterInput: 'input[name="moveafter"]'
};

export default class GradebookEditTreeBulkMove extends BulkAction {

    /** @property {int|null} courseId The course ID. */
    courseId = null;

    /** @property {MoveOptionsTree|null} moveOptionsTree The move options tree object. */
    moveOptionsTree = null;

    /** @property {string|null} gradeTree The grade tree structure. */
    gradeTree = null;

    /**
     * The class constructor.
     *
     * @param {int} courseId The course ID.
     * @returns {void}
     */
    constructor(courseId) {
        super();
        this.courseId = courseId;
    }

    /**
     * Defines the selector of the element that triggers the bulk move action.
     *
     * @returns {string} The bulk move action trigger selector.
     */
    getBulkActionTriggerSelector() {
        return '[data-type="bulkactions"] [data-action="move"]';
    }

    /**
     * Defines the behavior once the bulk move action is triggered.
     *
     * @method executeBulkAction
     * @returns {void}
     */
    async triggerBulkAction() {
        const modal = await this.showModal();
        this.registerCustomListenerEvents(modal);
    }

    /**
     * Renders the bulk move action trigger element.
     *
     * @method renderBulkActionTrigger
     * @param {boolean} showInDropdown Whether the action is displayed under a 'More' dropdown or as a separate button.
     * @param {number} index The index of the action.
     * @returns {Promise} The bulk move action trigger promise
     */
    async renderBulkActionTrigger(showInDropdown, index) {
        return Templates.render('core_grades/bulkactions/edit/tree/bulk_move_trigger', {
            showindropdown: showInDropdown,
            isfirst: index === 0,
        });
    }

    /**
     * Register custom event listeners.
     *
     * @method registerCustomClickListenerEvents
     * @param {Object} modal The modal object.
     * @returns {void}
     */
    async registerCustomListenerEvents(modal) {
        await modal.getBody();
        // Initialize the move options tree once the modal is shown.
        modal.getRoot().on(ModalEvents.shown, () => {
            this.moveOptionsTree = new MoveOptionsTree(() => {
                // Enable the 'Move' action button once something is selected.
                modal.setButtonDisabled('save', false);
            });
        });
        // Destroy the modal once it is hidden.
        modal.getRoot().on(ModalEvents.hidden, () => {
            modal.destroy();
        });
        // Define the move action event.
        modal.getRoot().on(ModalEvents.save, () => {
            // Make sure that a move option is selected.
            if (this.moveOptionsTree && this.moveOptionsTree.selectedMoveOption) {
                // Set the relevant form values.
                document.querySelector(Selectors.bulkMoveInput).value = 1;
                document.querySelector(Selectors.bulkMoveAfterInput).value = this.moveOptionsTree.selectedMoveOption.dataset.id;
                // Submit the form.
                document.querySelector(Selectors.editTreeForm).submit();
            }
        });
    }

    /**
     * Fetch the grade tree structure for the current course.
     *
     * @method fetchGradeTree
     * @returns {Promise} The grade tree promise
     */
    fetchGradeTree() {
        const request = {
            methodname: 'core_grades_get_grade_tree',
            args: {
                courseid: this.courseId,
            },
        };
        return Ajax.call([request])[0];
    }

    /**
     * Renders the bulk move modal body.
     *
     * @method renderModalBody
     * @returns {Promise} The modal body promise
     */
    async renderModalBody() {
        // We need to fetch the grade tree structure only once.
        if (this.gradeTree === null) {
            this.gradeTree = await this.fetchGradeTree();
        }

        return Templates.render('core_grades/bulkactions/edit/tree/bulk_move_grade_tree',
            JSON.parse(this.gradeTree));
    }

    /**
     * Show the bulk move modal.
     *
     * @method showModal
     * @returns {Promise} The modal promise
     */
     async showModal() {
        const modal = await ModalSaveCancel.create({
            title: await getString('movesitems', 'grades'),
            body: await this.renderModalBody(),
            buttons: {
                save: await getString('move')
            },
            large: true,
        });
        // Disable the 'Move' action button until something is selected.
        modal.setButtonDisabled('save', true);
        modal.show();

        return modal;
    }
}
