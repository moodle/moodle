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
 * Tiny Panopto LTI Video ui helper.
 *
 * @module     tiny_panoptoltibutton/ui
 * @copyright  2023 Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Modal from 'tiny_panoptoltibutton/modal';
import ModalFactory from 'core/modal_factory';
import
    {
        getCourseId,
        getWwwroot,
        getContentItemPath,
        getResourceBase,
        getTool,
        getUnprovisionedError
    } from './options';
import Config from 'core/config';

export const handleAction = (editor) => {
    displayDialogue(editor);
};

/**
 * Get the template context for the dialogue.
 *
 * @param {Editor} editor
 * @param {object} data
 * @returns {object} data
 */
const getTemplateContext = (editor, data) => {
    const baseUrl = `${Config.wwwroot}/lib/editor/tiny/plugins/panoptoltibutton/panoptowrapper.html#`;
    const unprovisionedError = encodeURIComponent(
        getUnprovisionedError(editor)
    );
    const courseId = getCourseId(editor);
    const tool = encodeURIComponent(JSON.stringify(getTool(editor)));
    const resourceBase = getResourceBase(editor);
    const wwwroot = encodeURIComponent(getWwwroot(editor));
    const contentItemPath = encodeURIComponent(getContentItemPath(editor));

    const fullUrl =
        baseUrl +
        `unprovisionerror=${unprovisionedError}` +
        `&courseid=${courseId}` +
        `&tool=${tool}` +
        `&resourcebase=${resourceBase}` +
        `&wwwroot=${wwwroot}` +
        `&contentitempath=${contentItemPath}`;

    return Object.assign({}, {
        iframe: fullUrl.toString(),
    }, data);
};

const displayDialogue = async (editor, data = {}) => {
    editor.focus(true);
    const modal = await ModalFactory.create({
        type: Modal.TYPE,
        templateContext: getTemplateContext(editor, data),
        large: true,
        scrollable: false,
    });

    const $root = await modal.getRoot();

    // Get the iframe element inside the modal.
    let iframe = $root.find("iframe");

    // Make the iframe responsive by setting its width and height to 100%.
    iframe.css({
        width: "100%",
        height: "100%",
        border: "none",
    });

    // Set the maximum width for the modal content container.
    let contentContainer = $root.find(".modal-dialog");
    contentContainer.css({
        "max-width": "55vw",
    });

    // Set height for the body container.
    let bodyContainer = $root.find(".modal-body");
    bodyContainer.css({
        height: "81vh",
    });

    modal.show();

    // Close modal from iframe.
    window.closeModal = () => {
        const closeButton = document.querySelector("#panopto-close");
        if (closeButton) {
            closeButton.click();
        }
    };
};
