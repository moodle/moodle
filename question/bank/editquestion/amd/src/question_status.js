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
 * Status column selector js.
 *
 * @module     qbank_editquestion/question_status
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Fragment from 'core/fragment';
import * as Str from 'core/str';
import ModalFactory from 'core/modal_factory';
import Notification from 'core/notification';
import ModalEvents from 'core/modal_events';
import Ajax from 'core/ajax';

/**
 * Get the fragment.
 *
 * @method getFragment
 * @param {{questioned: Number}} args
 * @param {Number} contextId
 * @return {String}
 */
const getFragment = (args, contextId) => {
    return Fragment.loadFragment('qbank_editquestion', 'question_status', contextId, args);
};

/**
 * Set the question status.
 *
 * @param {Number} questionId The question id.
 * @param {String} formData The question tag form data in a URI encoded param string
 * @return {Array} The modified question status
 */
const setQuestionStatus = (questionId, formData) => Ajax.call([{
    methodname: 'qbank_editquestion_set_status',
    args: {
        questionid: questionId,
        formdata: formData
    }
}])[0];

/**
 * Save the status.
 *
 * @method getFragment
 * @param {object} modal
 * @param {Number} questionId
 * @param {HTMLElement} target
 */
const save = (modal, questionId, target) => {
    const formData = modal.getBody().find('form').serialize();

    setQuestionStatus(questionId, formData)
        .then(result => {
            if (result.status) {
                target.innerText = result.statusname;
            }
            return;
        })
        .catch(Notification.exception);
};

/**
 * Event listeners for the module.
 *
 * @method clickEvent
 * @param {Number} questionId
 * @param {Number} contextId
 * @param {HTMLElement} target
 */
const statusEvent = (questionId, contextId, target) => {
    let args = {
        questionid: questionId
    };
    getStatusModal(args, contextId)
        .then((modal) => {
            modal.show();
            let root = modal.getRoot();
            root.on(ModalEvents.save, function(e) {
                e.preventDefault();
                e.stopPropagation();
                save(modal, questionId, target);
                modal.hide();
            });
            return modal;
        })
        .catch(Notification.exception);
};

/**
 * Get the status modal to display.
 *
 * @param {{questionid: Number}} args
 * @param {Number} contextId
 * @return {HTMLElement}
 */
const getStatusModal = (args, contextId) => ModalFactory.create({
    type: ModalFactory.types.SAVE_CANCEL,
    title: Str.get_string('questionstatusheader', 'qbank_editquestion'),
    body: getFragment(args, contextId),
    large: false,
});

/**
 * Entrypoint of the js.
 *
 * @method init
 * @param {String} questionSelector the question status identifier.
 * @param {Number} contextId The context id of the question.
 */
export const init = (questionSelector, contextId) => {
    let target = document.querySelector(questionSelector);
    let questionId = target.getAttribute('data-questionid');
    target.addEventListener('click', () => {
        // Call for the event listener to listed for clicks in any usage count row.
        statusEvent(questionId, contextId, target);
    });
};
