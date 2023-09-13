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
 * @module     core/addblockmodal
 * @deprecated since Moodle 4.2 - please use core_block/add_modal instead.
 * @copyright  2016 Damyon Wiese <damyon@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import CancelModal from './modal_cancel';
import Templates from 'core/templates';
import {getString} from 'core/str';
import Ajax from 'core/ajax';

const SELECTORS = {
    ADD_BLOCK: '[data-key="addblock"]'
};

// Ensure we only add our listeners once.
let listenerEventsRegistered = false;

/**
 * Register related event listeners.
 *
 * @method registerListenerEvents
 * @param {String} pageType The type of the page
 * @param {String} pageLayout The layout of the page
 * @param {String|null} addBlockUrl The add block URL
 * @param {String} subPage The subpage identifier
 */
const registerListenerEvents = (pageType, pageLayout, addBlockUrl, subPage) => {
    document.addEventListener('click', e => {

        const addBlock = e.target.closest(SELECTORS.ADD_BLOCK);
        if (addBlock) {
            e.preventDefault();

            let addBlockModal = null;
            let addBlockModalUrl = addBlockUrl ?? addBlock.dataset.url;

            buildAddBlockModal()
                .then(modal => {
                    addBlockModal = modal;
                    const modalBody = renderBlocks(addBlockModalUrl, pageType, pageLayout, subPage);
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
const buildAddBlockModal = () => {
    return CancelModal.create({
        title: getString('addblock')
    });
};

/**
 * Method that renders the list of available blocks.
 *
 * @method renderBlocks
 * @param {String} addBlockUrl The add block URL
 * @param {String} pageType The type of the page
 * @param {String} pageLayout The layout of the page
 * @param {String} subPage The subpage identifier
 * @return {Promise}
 */
const renderBlocks = async(addBlockUrl, pageType, pageLayout, subPage) => {
    // Fetch all addable blocks in the given page.
    const blocks = await getAddableBlocks(pageType, pageLayout, subPage);

    return Templates.render('core/add_block_body', {
        blocks: blocks,
        url: addBlockUrl
    });
};

/**
 * Method that fetches all addable blocks in a given page.
 *
 * @method getAddableBlocks
 * @param {String} pageType The type of the page
 * @param {String} pageLayout The layout of the page
 * @param {String} subPage The subpage identifier
 * @return {Promise}
 */
const getAddableBlocks = async(pageType, pageLayout, subPage) => {
    const request = {
        methodname: 'core_block_fetch_addable_blocks',
        args: {
            pagecontextid: M.cfg.contextid,
            pagetype: pageType,
            pagelayout: pageLayout,
            subpage: subPage,
        },
    };

    return Ajax.call([request])[0];
};

/**
 * Set up the actions.
 *
 * @method init
 * @param {String} pageType The type of the page
 * @param {String} pageLayout The layout of the page
 * @param {String|null} addBlockUrl The add block URL
 * @param {String} subPage The subpage identifier
 */
export const init = (pageType, pageLayout, addBlockUrl = null, subPage = '') => {
    if (!listenerEventsRegistered) {
        registerListenerEvents(pageType, pageLayout, addBlockUrl, subPage);
        listenerEventsRegistered = true;
    }
};
