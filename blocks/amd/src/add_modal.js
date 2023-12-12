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
 * Show an add block modal instead of doing it on a separate page.
 *
 * @module     core_block/add_modal
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import {getString} from 'core/str';
import Ajax from 'core/ajax';
import ModalForm from "core_form/modalform";
import CancelModal from 'core/modal_cancel';

const SELECTORS = {
    ADD_BLOCK: '[data-key="addblock"]',
    SHOW_BLOCK_FORM: '[data-action="showaddblockform"][data-blockname][data-blockform]'
};

// Ensure we only add our listeners once.
let listenerEventsRegistered = false;

/**
 * Register related event listeners.
 *
 * @method registerListenerEvents
 * @param {String|null} addBlockUrl The add block URL
 * @param {String} pagehash
 */
const registerListenerEvents = (addBlockUrl, pagehash) => {
    let addBlockModal = null;
    document.addEventListener('click', e => {

        const showAddBlockForm = e.target.closest(SELECTORS.SHOW_BLOCK_FORM);
        if (showAddBlockForm) {
            e.preventDefault();

            const modalForm = new ModalForm({
                modalConfig: {
                    title: getString('addblock', 'core_block',
                        showAddBlockForm.getAttribute('data-blocktitle')),
                },
                args: {blockname: showAddBlockForm.getAttribute('data-blockname'), pagehash,
                    blockregion: showAddBlockForm.getAttribute('data-blockregion')},
                formClass: showAddBlockForm.getAttribute('data-blockform'),
                returnFocus: showAddBlockForm,
            });

            modalForm.addEventListener(modalForm.events.FORM_SUBMITTED, () => {
                addBlockModal.destroy();
                window.location.reload();
            });

            modalForm.show();
        }

        const addBlock = e.target.closest(SELECTORS.ADD_BLOCK);
        if (addBlock) {
            e.preventDefault();

            let addBlockModalUrl = addBlockUrl ?? addBlock.dataset.url;

            buildAddBlockModal()
                .then(modal => {
                    addBlockModal = modal;
                    const modalBody = renderBlocks(addBlockModalUrl, pagehash,
                        addBlock.getAttribute('data-blockregion'));
                    modal.setBody(modalBody);
                    modal.show();

                    return modalBody;
                })
                .catch(() => {
                    addBlockModal.destroy();
                });
        }
    });
};

/**
 * Method that creates the 'add block' modal.
 *
 * @method buildAddBlockModal
 * @returns {Promise} The modal promise (modal's body will be rendered later).
 */
const buildAddBlockModal = () => CancelModal.create({
    title: getString('addblock'),
});

/**
 * Method that renders the list of available blocks.
 *
 * @method renderBlocks
 * @param {String} addBlockUrl The add block URL
 * @param {String} pagehash
 * @param {String} region
 * @return {Promise}
 */
const renderBlocks = async(addBlockUrl, pagehash, region) => {
    // Fetch all addable blocks in the given page.
    const blocks = await getAddableBlocks(pagehash);

    return Templates.render('core/add_block_body', {
        blocks: blocks,
        url: addBlockUrl,
        blockregion: region,
        pagehash
    });
};

/**
 * Method that fetches all addable blocks in a given page.
 *
 * @method getAddableBlocks
 * @param {String} pagehash
 * @return {Promise}
 */
const getAddableBlocks = async(pagehash) => {
    const request = {
        methodname: 'core_block_fetch_addable_blocks',
        args: {
            pagecontextid: 0,
            pagetype: '',
            pagelayout: '',
            subpage: '',
            pagehash: pagehash,
        },
    };

    return Ajax.call([request])[0];
};

/**
 * Set up the actions.
 *
 * @method init
 * @param {String} addBlockUrl The add block URL
 * @param {String} pagehash
 */
export const init = (addBlockUrl = null, pagehash = '') => {
    if (!listenerEventsRegistered) {
        registerListenerEvents(addBlockUrl, pagehash);
        listenerEventsRegistered = true;
    }
};
