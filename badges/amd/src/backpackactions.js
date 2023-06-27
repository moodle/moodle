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
 * Action methods related to backpacks.
 *
 * @module     core_badges/backpackactions
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import selectors from 'core_badges/selectors';
import {get_string as getString} from 'core/str';
import Pending from 'core/pending';
import ModalFactory from 'core/modal_factory';
import ModalEvents from 'core/modal_events';
import Config from 'core/config';

/**
 * Set up the actions.
 *
 * @method init
 */
export const init = () => {
    const pendingPromise = new Pending();

    const root = $(selectors.elements.main);
    registerListenerEvents(root);

    pendingPromise.resolve();
};

/**
 * Register backpack related event listeners.
 *
 * @method registerListenerEvents
 * @param {Object} root The root element.
 */
const registerListenerEvents = (root) => {

    root.on('click', selectors.actions.deletebackpack, async(e) => {
        e.preventDefault();

        const link = $(e.currentTarget);
        const modal = await buildModal(link);

        displayModal(modal, link);
    });
};

const buildModal = async(link) => {

    const backpackurl = link.closest(selectors.elements.backpackurl).attr('data-backpackurl');

    return ModalFactory.create({
        title: await getString('delexternalbackpack', 'core_badges'),
        body: await getString('delexternalbackpackconfirm', 'core_badges', backpackurl),
        type: ModalFactory.types.SAVE_CANCEL,
    });

};

const displayModal = async(modal, link) => {
    modal.setSaveButtonText(await getString('delete', 'core'));

    modal.getRoot().on(ModalEvents.save, function() {
        window.location.href = link.attr('href') + '&sesskey=' + Config.sesskey + '&confirm=1';
    });

    modal.getRoot().on(ModalEvents.hidden, function() {
        modal.destroy();
    });

    modal.show();
};
