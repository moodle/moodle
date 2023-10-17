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
 * Module for auto-linking glossary entries.
 *
 * @module     filter_glossary/autolinker
 * @copyright  2023 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import ModalCancel from "core/modal_cancel";
import Templates from 'core/templates';
import {getString} from 'core/str';

/** @constant {Object} The object containing the relevant selectors. */
const Selectors = {
    glossaryEntryAutoLink: 'a.glossary.autolink.concept',
};

/**
 * Register the event listeners for the glossary entry auto-linker.
 *
 * @return {void}
 */
const registerEventListeners = () => {
    document.addEventListener('click', async(e) => {
        const glossaryEntryAutoLink = e.target.closest(Selectors.glossaryEntryAutoLink);
        if (glossaryEntryAutoLink) {
            e.preventDefault();
            const entryId = glossaryEntryAutoLink.dataset.entryid;
            await showGlossaryEntry(entryId);
            await glossaryEntryViewed(entryId);
        }
    });
};

/**
 * Show the linked glossary entry in a modal.
 *
 * @method showGlossaryEntry
 * @param {int} entryId The id of the linked glossary entry.
 * @returns {Promise} The modal promise.
 */
const showGlossaryEntry = async(entryId) => {
    const entryData = await fetchGlossaryEntry(entryId);
    // Obtain the HTML and JS used for rendering the auto-linked glossary entry.
    const {html, js} = await Templates.renderForPromise('filter_glossary/linked_glossary_entry', {
        definition: entryData.entry.definition,
        taglistdata: await generateTagListData(entryData.entry.tags),
        hasattachments: Boolean(entryData.entry.attachment),
        attachments: entryData.entry.attachments
    });
    // Create the modal.
    const modal = await ModalCancel.create({
        title: entryData.entry.concept,
        body: html,
        isVerticallyCentered: true,
        buttons: {
            cancel: await getString('ok')
        }
    });
    // Execute the JS code returned from the template once the modal is created.
    Templates.runTemplateJS(js);
    // Display the modal.
    modal.show();

    return modal;
};

/**
 * Fetch the linked glossary entry.
 *
 * @method fetchGlossaryEntry
 * @param {int} entryId The id of the linked glossary entry.
 * @returns {Promise} The glossary entry promise.
 */
const fetchGlossaryEntry = (entryId) => {
    const request = {
        methodname: 'mod_glossary_get_entry_by_id',
        args: {
            id: entryId,
        },
    };
    return Ajax.call([request])[0];
};

/**
 * Notify that the linked glossary entry was viewed.
 *
 * @method glossaryEntryViewed
 * @param {int} entryId The id of the linked glossary entry.
 * @returns {Promise} The promise object.
 */
const glossaryEntryViewed = (entryId) => {
    const request = {
        methodname: 'mod_glossary_view_entry',
        args: {
            id: entryId,
        },
    };
    return Ajax.call([request])[0];
};

/**
 * Generates an object that contains the data required to render a tag list.
 *
 * @method generateTagListData
 * @param {array} tags The array containing the tags related to the linked glossary entry.
 * @returns {Object} The data required to render a tag list.
 */
const generateTagListData = async(tags) => {
    // Define the number of initially displayed tags.
    const limit = 10;
    const hasOverflow = tags.length > limit;
    // If the total number of tags exceeds the defined limit, then we need to mark all the excess tags as over the limit.
    // By specifying this, these tags will be initially hidden.
    if (hasOverflow) {
        for (let i = limit; i < tags.length; i++) {
            tags[i].overlimit = true;
        }
    }

    return {
        tags: tags,
        tagscount: tags.length,
        overflow: hasOverflow,
        label: await getString('tags')
    };
};

/**
 * Initialize the module.
 */
export const init = () => {
    registerEventListeners();
};
