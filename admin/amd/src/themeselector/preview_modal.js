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
 * Modal for theme previews.
 *
 * @module     core_admin/themeselector/preview_modal
 * @copyright  2023 David Woloszyn <david.woloszyn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalEvents from 'core/modal_events';
import ModalCancel from 'core/modal_cancel';
import ModalSaveCancel from 'core/modal_save_cancel';
import Notification from 'core/notification';
import Templates from 'core/templates';
import {getString} from 'core/str';

const SELECTORS = {
    THEMES_CONTAINER: 'themelist',
    PREVIEW: '[data-action="preview"]',
};

/**
 * Entrypoint of the js.
 *
 * @method init
 */
export const init = () => {
    registerListenerEvents();
};

/**
 * Register theme related event listeners.
 *
 * @method registerListenerEvents
 */
const registerListenerEvents = () => {
    document.addEventListener('click', (e) => {
        const preview = e.target.closest(SELECTORS.PREVIEW);
        if (preview) {
            buildModal(preview).catch(Notification.exception);
        }
    });
};

/**
 * Build the modal with the provided data.
 *
 * @method buildModal
 * @param {object} element
 */
const buildModal = async(element) => {

    // This string can be long. We will fetch it with JS as opposed to passing it as an attribute.
    let description = await getString('choosereadme', 'theme_' + element.getAttribute('data-choose'));

    const themesContainer = document.getElementById(SELECTORS.THEMES_CONTAINER);
    const definedInConfig = parseInt(themesContainer.dataset.definedinconfig);
    // Prepare data for modal.
    const data = {
        name: element.getAttribute('data-name'),
        image: element.getAttribute('data-image'),
        description: description.replace(/<[^>]+>/g, ' '), // Strip out HTML tags.
        current: element.getAttribute('data-current'),
        actionurl: element.getAttribute('data-actionurl'),
        choose: element.getAttribute('data-choose'),
        sesskey: element.getAttribute('data-sesskey'),
        definedinconfig: definedInConfig,
    };

    // Determine which modal template we should use.
    let modalTemplate = ModalSaveCancel;
    if (data.current || data.definedinconfig) {
        modalTemplate = ModalCancel;
    }

    const modal = await modalTemplate.create({
        title: data.name,
        body: Templates.render('core_admin/themeselector/theme_preview_modal', data),
        large: true,
        buttons: {
            'save': getString('selecttheme', 'moodle'),
            'cancel': getString('closebuttontitle', 'moodle'),
        },
        show: true,
    });

    modal.getRoot().on(ModalEvents.save, () => {
        modal.getRoot().find('form').submit();
    });
};
