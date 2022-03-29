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
 * Report builder audiences
 *
 * @module      core_reportbuilder/audience
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

import 'core/inplace_editable';
import Templates from 'core/templates';
import Notification from 'core/notification';
import Pending from 'core/pending';
import {prefetchStrings} from 'core/prefetch';
import {get_string as getString} from 'core/str';
import DynamicForm from 'core_form/dynamicform';
import {add as addToast} from 'core/toast';
import {deleteAudience} from 'core_reportbuilder/local/repository/audiences';
import * as reportSelectors from 'core_reportbuilder/local/selectors';
import {loadFragment} from 'core/fragment';
import {markFormAsDirty} from 'core_form/changechecker';

let reportId = 0;
let contextId = 0;

/**
 * Add audience card
 *
 * @param {String} className
 * @param {String} title
 */
const addAudienceCard = (className, title) => {
    const pendingPromise = new Pending('core_reportbuilder/audience:add');

    const audiencesContainer = document.querySelector(reportSelectors.regions.audiencesContainer);
    const audienceCardLength = audiencesContainer.querySelectorAll(reportSelectors.regions.audienceCard).length;

    const params = {
        classname: className,
        reportid: reportId,
        showormessage: (audienceCardLength > 0),
        title: title,
    };

    // Load audience card fragment, render and then initialise the form within.
    loadFragment('core_reportbuilder', 'audience_form', contextId, params)
        .then((html, js) => {
            const audienceCard = Templates.appendNodeContents(audiencesContainer, html, js)[0];
            const audienceEmptyMessage = audiencesContainer.querySelector(reportSelectors.regions.audienceEmptyMessage);

            const audienceForm = initAudienceCardForm(audienceCard);
            // Mark as dirty new audience form created to prevent users leaving the page without saving it.
            markFormAsDirty(audienceForm.getFormNode());
            audienceEmptyMessage.classList.add('hidden');

            return getString('audienceadded', 'core_reportbuilder', title);
        })
        .then(addToast)
        .then(() => pendingPromise.resolve())
        .catch(Notification.exception);
};

/**
 * Edit audience card
 *
 * @param {Element} audienceCard
 */
const editAudienceCard = audienceCard => {
    const pendingPromise = new Pending('core_reportbuilder/audience:edit');

    // Load audience form with data for editing, then toggle visible controls in the card.
    const audienceForm = initAudienceCardForm(audienceCard);
    audienceForm.load({id: audienceCard.dataset.instanceid})
        .then(() => {
            const audienceFormContainer = audienceCard.querySelector(reportSelectors.regions.audienceFormContainer);
            const audienceDescription = audienceCard.querySelector(reportSelectors.regions.audienceDescription);
            const audienceEdit = audienceCard.querySelector(reportSelectors.actions.audienceEdit);

            audienceFormContainer.classList.remove('hidden');
            audienceDescription.classList.add('hidden');
            audienceEdit.disabled = true;

            return pendingPromise.resolve();
        })
        .catch(Notification.exception);
};

/**
 * Initialise dynamic form within given audience card
 *
 * @param {Element} audienceCard
 * @return {DynamicForm}
 */
const initAudienceCardForm = audienceCard => {
    const audienceFormContainer = audienceCard.querySelector(reportSelectors.regions.audienceFormContainer);
    const audienceForm = new DynamicForm(audienceFormContainer, '\\core_reportbuilder\\form\\audience');

    // After submitting the form, update the card instance and description properties.
    audienceForm.addEventListener(audienceForm.events.FORM_SUBMITTED, data => {
        const audienceHeading = audienceCard.querySelector(reportSelectors.regions.audienceHeading);
        const audienceDescription = audienceCard.querySelector(reportSelectors.regions.audienceDescription);

        audienceCard.dataset.instanceid = data.detail.instanceid;

        audienceHeading.innerHTML = data.detail.heading;
        audienceDescription.innerHTML = data.detail.description;

        closeAudienceCardForm(audienceCard);

        return getString('audiencesaved', 'core_reportbuilder')
            .then(addToast);
    });

    // If cancelling the form, close the card or remove it if it was never created.
    audienceForm.addEventListener(audienceForm.events.FORM_CANCELLED, () => {
        if (audienceCard.dataset.instanceid > 0) {
            closeAudienceCardForm(audienceCard);
        } else {
            removeAudienceCard(audienceCard);
        }
    });

    return audienceForm;
};

/**
 * Delete audience card
 *
 * @param {Element} audienceDelete
 */
const deleteAudienceCard = audienceDelete => {
    const audienceCard = audienceDelete.closest(reportSelectors.regions.audienceCard);
    const audienceTitle = audienceCard.dataset.title;

    Notification.saveCancelPromise(
        getString('deleteaudience', 'core_reportbuilder', audienceTitle),
        getString('deleteaudienceconfirm', 'core_reportbuilder', audienceTitle),
        getString('delete', 'core'),
        {triggerElement: audienceDelete}
    ).then(() => {
        const pendingPromise = new Pending('core_reportbuilder/audience:delete');

        return deleteAudience(reportId, audienceCard.dataset.instanceid)
            .then(() => addToast(getString('audiencedeleted', 'core_reportbuilder', audienceTitle)))
            .then(() => {
                removeAudienceCard(audienceCard);
                return pendingPromise.resolve();
            })
            .catch(Notification.exception);
    }).catch(() => {
        return;
    });
};

/**
 * Close audience card form
 *
 * @param {Element} audienceCard
 */
const closeAudienceCardForm = audienceCard => {
    // Remove the [data-region="audience-form-container"] (with all the event listeners attached to it), and create it again.
    const audienceFormContainer = audienceCard.querySelector(reportSelectors.regions.audienceFormContainer);
    const NewAudienceFormContainer = audienceFormContainer.cloneNode(false);
    audienceCard.querySelector(reportSelectors.regions.audienceForm).replaceChild(NewAudienceFormContainer, audienceFormContainer);
    // Show the description container and enable the action buttons.
    audienceCard.querySelector(reportSelectors.regions.audienceDescription).classList.remove('hidden');
    audienceCard.querySelector(reportSelectors.actions.audienceEdit).disabled = false;
    audienceCard.querySelector(reportSelectors.actions.audienceDelete).disabled = false;
};

/**
 * Remove audience card
 *
 * @param {Element} audienceCard
 */
const removeAudienceCard = audienceCard => {
    audienceCard.remove();

    const audiencesContainer = document.querySelector(reportSelectors.regions.audiencesContainer);
    const audienceCards = audiencesContainer.querySelectorAll(reportSelectors.regions.audienceCard);

    // Show message if there are no cards remaining, ensure first card's separator is not present.
    if (audienceCards.length === 0) {
        const audienceEmptyMessage = document.querySelector(reportSelectors.regions.audienceEmptyMessage);
        audienceEmptyMessage.classList.remove('hidden');
    } else {
        const audienceFirstCardSeparator = audienceCards[0].querySelector('.audience-separator');
        audienceFirstCardSeparator?.remove();
    }
};

let initialized = false;

/**
 * Initialise audiences tab.
 *
 * @param {Number} id
 * @param {Number} contextid
 */
export const init = (id, contextid) => {
    prefetchStrings('core_reportbuilder', [
        'audienceadded',
        'audiencedeleted',
        'audiencesaved',
        'deleteaudience',
        'deleteaudienceconfirm',
    ]);

    prefetchStrings('core', [
        'delete',
    ]);

    reportId = id;
    contextId = contextid;

    if (initialized) {
        // We already added the event listeners (can be called multiple times by mustache template).
        return;
    }

    document.addEventListener('click', event => {

        // Add instance.
        const audienceAdd = event.target.closest(reportSelectors.actions.audienceAdd);
        if (audienceAdd) {
            event.preventDefault();
            addAudienceCard(audienceAdd.dataset.uniqueIdentifier, audienceAdd.dataset.name);
        }

        // Edit instance.
        const audienceEdit = event.target.closest(reportSelectors.actions.audienceEdit);
        if (audienceEdit) {
            const audienceEditCard = audienceEdit.closest(reportSelectors.regions.audienceCard);

            event.preventDefault();
            editAudienceCard(audienceEditCard);
        }

        // Delete instance.
        const audienceDelete = event.target.closest(reportSelectors.actions.audienceDelete);
        if (audienceDelete) {
            event.preventDefault();
            deleteAudienceCard(audienceDelete);
        }
    });

    initialized = true;
};
