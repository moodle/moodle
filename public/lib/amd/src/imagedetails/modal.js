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
 * Image details modal.
 *
 * Presents the image-details controls (alternative text, decorative flag and display size) for an image
 * the user is about to embed, in a save/cancel modal of its own, and resolves with their choices, or
 * null when they cancel.
 *
 * Intended for any component that embeds an author-supplied image and needs to collect an accessible
 * description for it before the image is stored. A caller whose dialogue is its own, for example an
 * editor presenting these controls as one step of a larger flow, should render core/imagedetails/form
 * into its own container instead.
 *
 * @module      core/imagedetails/modal
 * @copyright   2026 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ModalSaveCancel from 'core/modal_save_cancel';
import ModalEvents from 'core/modal_events';
import {getString} from 'core/str';
import {ImageDetailsForm} from 'core/imagedetails/form';

/**
 * Load the natural dimensions of an image from an object URL.
 *
 * @param {string} objecturl The object URL of the image.
 * @returns {Promise<{width: number, height: number}>} The natural dimensions (0 when they cannot be read).
 */
const loadDimensions = (objecturl) => new Promise((resolve) => {
    const img = new Image();
    img.onload = () => resolve({width: img.naturalWidth || 0, height: img.naturalHeight || 0});
    img.onerror = () => resolve({width: 0, height: 0});
    img.src = objecturl;
});

/**
 * Show the image-details modal for an image and collect the user's choices.
 *
 * A width and height of 0 means the user kept the original size, leaving it to the caller to decide
 * what the image is displayed at.
 *
 * @param {File} file The image file the user is about to embed.
 * @returns {Promise<?{alt: string, presentation: boolean, width: number, height: number}>}
 *      The chosen image details, or null when the user cancels.
 */
export const getImageDetails = async(file) => {
    const objecturl = URL.createObjectURL(file);
    const dimensions = await loadDimensions(objecturl);

    const modal = await ModalSaveCancel.create({
        title: getString('imagedetails'),
        large: true,
    });
    modal.setSaveButtonText(getString('save'));

    // Render the form before showing, so the modal does not appear empty while the template loads.
    const form = new ImageDetailsForm(modal.getBody()[0], {
        naturalWidth: dimensions.width,
        naturalHeight: dimensions.height,
        showPreview: true,
        previewUrl: objecturl,
        filename: file.name,
    });
    await form.render();
    await modal.show();

    return new Promise((resolve) => {
        const root = modal.getRoot();
        let saved = false;

        root.on(ModalEvents.save, (e) => {
            if (!form.validate()) {
                // Keep the modal open so the error can be corrected.
                e.preventDefault();
                return;
            }
            saved = true;
            resolve(form.getDetails());
        });

        root.on(ModalEvents.hidden, () => {
            URL.revokeObjectURL(objecturl);
            modal.destroy();
            if (!saved) {
                // Cancelled or dismissed - report no choices so the caller can abandon its flow.
                resolve(null);
            }
        });
    });
};
