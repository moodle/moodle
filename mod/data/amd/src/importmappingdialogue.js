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
 * Javascript module for deleting a database as a preset.
 *
 * @module      mod_data/importmappingdialogue
 * @copyright   2022 Amaia Anabitarte <amaia@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Notification from 'core/notification';
import Ajax from 'core/ajax';
import Url from 'core/url';
import Templates from 'core/templates';
import ModalFactory from 'core/modal_factory';
import {prefetchStrings} from 'core/prefetch';
import {get_string as getString} from 'core/str';

// Load global strings.
prefetchStrings('mod_data', ['mapping:dialogtitle:usepreset']);

const selectors = {
    selectPreset: '[data-action="selectpreset"]',
};

/**
 * Initialize module
 */
export const init = () => {
    registerEventListeners();
};

/**
 * Register events.
 */
const registerEventListeners = () => {
    document.addEventListener('click', (event) => {
        const preset = event.target.closest(selectors.selectPreset);
        if (preset) {
            event.preventDefault();
            showMappingDialogue(preset);
        }
    });
};

/**
 * Show the confirmation modal for uploading a preset.
 *
 * @param {HTMLElement} usepreset the preset to import.
 */
const showMappingDialogue = (usepreset) => {
    const presetName = usepreset.dataset.presetname;
    const cmId = usepreset.dataset.cmid;

    getMappingInformation(cmId, presetName).then((result) => {
        if (result.data && result.data.needsmapping) {
            buildModal({
                title: getString('mapping:dialogtitle:usepreset', 'mod_data', result.data.presetname),
                body: Templates.render('mod_data/fields_mapping_body', result.data),
                footer: Templates.render('mod_data/fields_mapping_footer', getMappingButtons(cmId, presetName)),
                large: true,
            });
        } else {
            window.location.href = Url.relativeUrl(
                'mod/data/field.php',
                {
                    id: cmId,
                    mode: 'usepreset',
                    fullname: presetName,
                },
                false
            );
        }
        return true;
    }).catch(Notification.exception);
};

/**
 * Given an object we want to build a modal ready to show
 *
 * @method buildModal
 * @param {Object} params the modal params
 * @param {Promise} params.title
 * @param {Promise} params.body
 * @param {Promise} params.footer
 * @return {Object} The modal ready to display immediately and render body in later.
 */
const buildModal = (params) => {
    return ModalFactory.create({
        ...params,
        type: ModalFactory.types.DEFAULT,
    }).then(modal => {
        modal.show();
        modal.showFooter();
        modal.registerCloseOnCancel();
        return modal;
    }).catch(Notification.exception);
};

/**
 * Add buttons to render on mapping modal.
 *
 * @param {int} cmId The id of the current database activity.
 * @param {string} presetName The preset name to delete.
 * @return {array} Same data with buttons.
 */
const getMappingButtons = (cmId, presetName) => {
    const data = {};

    data.mapfieldsbutton = Url.relativeUrl(
        'mod/data/field.php',
        {
            id: cmId,
            fullname: presetName,
            mode: 'usepreset',
            action: 'select',
        },
        false
    );

    data.applybutton = Url.relativeUrl(
        'mod/data/field.php',
        {
            id: cmId,
            fullname: presetName,
            mode: 'usepreset',
            action: 'notmapping'
        },
        false
    );

    return data;
};

/**
 * Check whether we should show the mapping dialogue or not.
 *
 * @param {int} cmId The id of the current database activity.
 * @param {string} presetName The preset name to delete.
 * @return {promise} Resolved with the result and warnings of deleting a preset.
 */
const getMappingInformation = (cmId, presetName) => {
    const request = {
        methodname: 'mod_data_get_mapping_information',
        args: {
            cmid: cmId,
            importedpreset: presetName,
        }
    };
    return Ajax.call([request])[0];
};
