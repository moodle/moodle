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
 * Handles edit penalty form.
 *
 * @module     gradepenalty_duedate/edit_penalty_form
 * @copyright  2024 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import * as notification from 'core/notification';
import Fragment from 'core/fragment';
import Templates from 'core/templates';

/**
 * Rule js class.
 */
class PenaltyRule {
    constructor(
        overdueby = 0,
        penalty = 0,
    ) {
        this.overdueby = overdueby;
        this.penalty = penalty;
    }
}

/**
 * Selectors
 */
const SELECTORS = {
    FORM_CONTAINER: '#penalty_rule_form_container',
    ACTION_MENU: '.action-menu',
    ADD_BUTTON: '#addrulebutton',
    INSERT_BUTTON: '.insertbelow',
    DELETE_BUTTON: '.deleterulebuttons',
    DELETE_ALL_BUTTON_CONTAINER: '#deleteallrulesbuttoncontainer',
};

/**
 * Register click event for delete and insert buttons.
 */
const registerEventListeners = () => {
    // Find all action menus in penalty rule form.
    const container = document.querySelector(SELECTORS.FORM_CONTAINER);
    container.addEventListener('click', (e) => {
        if (e.target.closest(SELECTORS.DELETE_BUTTON)) {
            e.preventDefault();
            deleteRule(e.target);

            return;
        }

        if (e.target.closest(SELECTORS.INSERT_BUTTON)) {
            e.preventDefault();
            insertRule(e.target);

            return;
        }
    });

    document.querySelector(SELECTORS.ADD_BUTTON).addEventListener('click', (e) => {
        e.preventDefault();
        insertRuleAtIndex(container.querySelectorAll(SELECTORS.ACTION_MENU).length);

        return;
    });
};

/**
 * Delete a rule group represented by thenode.
 *
 * @param {NodeElement} target
 */
const deleteRule = (target) => {
    // Get all form data.
    const {contextid, penaltyRules, finalPenaltyRule} = buildFormParams();
    const ruleNumber = getRuleNumber(target);

    // Remove the penalty rule.
    const updatedPenaltyRules = penaltyRules.filter((rule, index) => index !== ruleNumber);

    loadPenaltyRuleForm(
        contextid,
        updatedPenaltyRules,
        finalPenaltyRule,
    );
};

/**
 * Insert a rule group below the clicked button.
 *
 * @param {NodeElement} target
 */
const insertRule = (target) => insertRuleAtIndex(getRuleNumber(target) + 1);

/**
 * Add a new rule group at the specified index.
 *
 * @param {Number} ruleNumber
 */
const insertRuleAtIndex = (ruleNumber) => {
    // Get all form data.
    const {contextid, penaltyRules, finalPenaltyRule} = buildFormParams();

    // Insert a new penalty rule.
    penaltyRules.splice(ruleNumber, 0, new PenaltyRule());

    loadPenaltyRuleForm(
        contextid,
        penaltyRules,
        finalPenaltyRule,
    );
};

/**
 * Get the rule number from the target.
 *
 * @param {Object} target
 * @return {Number} rule number
 */
const getRuleNumber = (target) => {
    const allRules = target
        .closest(SELECTORS.FORM_CONTAINER)
        .querySelectorAll(SELECTORS.ACTION_MENU);

    const foundIndex = Array.prototype.findIndex.call(
        allRules,
        (element) => element.contains(target),
    );

    if (foundIndex === -1) {
        throw new Error('Rule number not found on target', target);
    }

    return foundIndex;
};

/**
 * Build form parameters for loading fragment.
 *
 * @return {Object} form params
 */
const buildFormParams = () => {
    // Get the penalty rule form in its container.
    const container = document.querySelector(SELECTORS.FORM_CONTAINER);
    const form = container.querySelector('form');

    // Get all form data
    const formData = new FormData(form);

    // Get context id.
    const contextid = formData.get('contextid');

    // Get group count.
    const groupCount = formData.get('rulegroupcount');

    // Create list of penalty rules.
    const penaltyRules = [];

    // Current penalty rules.

    for (let i = 0; i < groupCount; i++) {
        penaltyRules.push(new PenaltyRule(
            formData.get(`overdueby[${i}][number]`) * formData.get(`overdueby[${i}][timeunit]`),
            formData.get(`penalty[${i}]`)
        ));
    }

    return {
        contextid,
        penaltyRules,
        finalPenaltyRule: formData.get('finalpenaltyrule'),
    };
};

/**
 * Load the penalty rule form.
 *
 * @param {Number} contextId
 * @param {Array} penaltyRules
 * @param {Number} finalPenaltyRule
 */
const loadPenaltyRuleForm = (
    contextId,
    penaltyRules,
    finalPenaltyRule,
) => {
    // Disable the form while loading to improve UX.
    const container = document.querySelector(SELECTORS.FORM_CONTAINER);
    const form = container.querySelector('form');
    form.querySelectorAll('input, select').forEach(input => {
        input.disabled = true;
    });

    // Disable the add rule button.
    const addButton = document.querySelector(SELECTORS.ADD_BUTTON);
    if (addButton) {
        addButton.disabled = true;
    }

    // Disable the delete all rules button.
    const deleteAllButton = document.querySelector(SELECTORS.DELETE_ALL_BUTTON_CONTAINER).querySelector('button');
    if (deleteAllButton) {
        deleteAllButton.disabled = true;
    }

    // Replace the form with the new form.
    Fragment.loadFragment(
        'gradepenalty_duedate',
        'penalty_rule_form',
        contextId,
        {
            penaltyrules: JSON.stringify(penaltyRules),
            finalpenaltyrule: finalPenaltyRule,
        },
    )
        .then((html, js) => {
            Templates.replaceNodeContents(document.querySelector(SELECTORS.FORM_CONTAINER), html, js);

            if (addButton) {
                addButton.disabled = false;
            }

            if (deleteAllButton) {
                deleteAllButton.disabled = false;
            }
            return;
        })
        .catch(notification.exception);


};

/**
 * Initialize the js.
 */
export const init = () => {
    registerEventListeners();
};
