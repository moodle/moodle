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
 * Functions related to downloading course content.
 *
 * @module     core_course/downloadcontent
 * @package    core_course
 * @copyright  2020 Michael Hawkins <michaelh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Config from 'core/config';
import CustomEvents from 'core/custom_interaction_events';
import * as ModalFactory from 'core/modal_factory';
import jQuery from 'jquery';
import Pending from 'core/pending';

/**
 * Set up listener to trigger the download course content modal.
 *
 * @return {void}
 */
export const init = () => {
    const pendingPromise = new Pending();

    document.addEventListener('click', (e) => {
        const downloadModalTrigger = e.target.closest('[data-downloadcourse]');

        if (downloadModalTrigger) {
            e.preventDefault();
            displayDownloadConfirmation(downloadModalTrigger);
        }
    });

    pendingPromise.resolve();
};

/**
 * Display the download course content modal.
 *
 * @method displayDownloadConfirmation
 * @param {Object} downloadModalTrigger The DOM element that triggered the download modal.
 * @return {void}
 */
const displayDownloadConfirmation = (downloadModalTrigger) => {
    ModalFactory.create({
        title: downloadModalTrigger.dataset.downloadTitle,
        type: ModalFactory.types.SAVE_CANCEL,
        body: `<p>${downloadModalTrigger.dataset.downloadBody}</p>`,
        buttons: {
            save: downloadModalTrigger.dataset.downloadButtonText
        },
        templateContext: {
            classes: 'downloadcoursecontentmodal'
        }
    })
    .then(modal => {
        // Display the modal.
        modal.show();

        const saveButton = document.querySelector('.modal .downloadcoursecontentmodal [data-action="save"]');
        const cancelButton = document.querySelector('.modal .downloadcoursecontentmodal [data-action="cancel"]');
        const modalContainer = document.querySelector('.modal[data-region="modal-container"]');

        // Create listener to trigger the download when the "Download" button is pressed.
        jQuery(saveButton).on(CustomEvents.events.activate, (e) => downloadContent(e, downloadModalTrigger, modal));

        // Create listener to destroy the modal when closing modal by cancelling.
        jQuery(cancelButton).on(CustomEvents.events.activate, () => {
            modal.destroy();
        });

        // Create listener to destroy the modal when closing modal by clicking outside of it.
        if (modalContainer.querySelector('.downloadcoursecontentmodal')) {
            jQuery(modalContainer).on(CustomEvents.events.activate, () => {
                modal.destroy();
            });
        }
    });
};

/**
 * Trigger downloading of course content.
 *
 * @method downloadContent
 * @param {Event} e The event triggering the download.
 * @param {Object} downloadModalTrigger The DOM element that triggered the download modal.
 * @param {Object} modal The modal object.
 * @return {void}
 */
const downloadContent = (e, downloadModalTrigger, modal) => {
    e.preventDefault();

    // Create a form to submit the file download request, so we can avoid sending sesskey over GET.
    const downloadForm = document.createElement('form');
    downloadForm.action = downloadModalTrigger.dataset.downloadLink;
    downloadForm.method = 'POST';
    // Open download in a new tab, so current course view is not disrupted.
    downloadForm.target = '_blank';
    const downloadSesskey = document.createElement('input');
    downloadSesskey.name = 'sesskey';
    downloadSesskey.value = Config.sesskey;
    downloadForm.appendChild(downloadSesskey);
    downloadForm.style.display = 'none';

    document.body.appendChild(downloadForm);
    downloadForm.submit();
    document.body.removeChild(downloadForm);

    // Destroy the modal to prevent duplicates if reopened later.
    modal.destroy();
};
