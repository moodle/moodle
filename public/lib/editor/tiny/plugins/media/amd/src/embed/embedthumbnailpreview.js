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
 * Tiny media plugin embed thumbnail preview class.
 *
 * This handles:
 * - Embed thumbnail preview.
 * - Delete thumbnail preview.
 * - Update the embed preview details with new thumbnail.
 *
 * @module      tiny_media/embed/mediathumbnail
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Selectors from '../selectors';
import {getString} from 'core/str';
import {
    sourceTypeChecked,
    setPropertiesFromData,
    showElements,
    stopMediaLoading,
} from '../helpers';
import Notification from 'core/notification';
import {EmbedPreview} from './embedpreview';
import {mediaDetailsTemplateContext} from './embedhelpers';
import {EmbedHandler} from './embedhandler';
import {component} from '../common';

export class EmbedThumbnailPreview {

    constructor(data) {
        setPropertiesFromData(this, data); // Creates dynamic properties based on "data" param.
    }

    /**
     * Init the media thumbnail preview.
     *
     * @param {object} mediaData Object of selected media data
     */
    init = (mediaData) => {
        this.mediaData = mediaData;
        this.currentModal.uploadThumbnailModal.setTitle(getString('thumbnail', component));
        sourceTypeChecked({
            source: this.media.poster,
            root: this.thumbnailModalRoot,
            fileNameSelector: Selectors.EMBED.elements.fileNameLabel,
        });
        this.setThumbnailSource();
        this.registerMediaThumbnailEventListeners();
    };

    /**
     * Sets media thumbnail source.
     */
    setThumbnailSource = () => {
        const thumbnailPreview = this.thumbnailModalRoot.querySelector(Selectors.EMBED.elements.thumbnailPreview);
        thumbnailPreview.src = this.media.poster;

        thumbnailPreview.addEventListener('error', async() => {
            // Show warning notification.
            const urlWarningLabelEle = this.thumbnailModalRoot.querySelector(Selectors.EMBED.elements.urlWarning);
            urlWarningLabelEle.innerHTML = await getString('imageurlrequired', component);
            showElements(Selectors.EMBED.elements.urlWarning, this.thumbnailModalRoot);

            // Stop the spinner.
            stopMediaLoading(this.thumbnailModalRoot, Selectors.EMBED.type);

            // Reset the upload form.
            (new EmbedHandler(this)).resetUploadForm(false);
        });

        thumbnailPreview.addEventListener('load', () => {
            this.mediaData.media.poster = this.media.poster;
            this.media = this.mediaData.media;
            stopMediaLoading(this.thumbnailModalRoot, Selectors.EMBED.type);
        });
    };

    /**
     * Deletes the media after confirming with the user and loads the insert media page.
     */
    deleteMedia = () => {
        Notification.deleteCancelPromise(
            getString('deletemediathumbnail', component),
            getString('deletemediathumbnailwarning', component)
        ).then(() => {
            (new EmbedHandler(this)).resetUploadForm(false);
            return;
        }).catch(() => {
            // User cancelled the delete action.
            return;
        });
    };

    /**
     * Loads and displays a media preview with thumbnail.
     */
    loadPreviewMediaThumbnail = async() => {
        (new EmbedHandler(this)).loadMediaDetails(new EmbedPreview(this), await mediaDetailsTemplateContext(this))
        .then(() => {
            // Close the thumbnail upload modal once media details have been loaded.
            this.currentModal.uploadThumbnailModal.destroy();
            const currentModal = this.currentModal.insertMediaModal;
            this.currentModal = currentModal.insertMediaModal;
            delete this.mediaData;
            return;
        }).catch(error => {
            window.console.log(error);
        });
    };

    /**
     * Only registers event listeners for new loaded elements in mediaThumbnail.
     */
    registerMediaThumbnailEventListeners = () => {
        // Handles delete thumbnail.
        const deleteMedia = this.thumbnailModalRoot.querySelector(Selectors.EMBED.actions.deleteThumbnail);
        if (deleteMedia) {
            deleteMedia.addEventListener('click', (e) => {
                e.preventDefault();
                this.deleteMedia();
            });
        }

        // Handles setting the media poster.
        const setPoster = this.thumbnailModalRoot.querySelector(Selectors.EMBED.actions.setPoster);
        if (setPoster) {
            setPoster.addEventListener('click', () => {
                this.loadPreviewMediaThumbnail();
            });
        }
    };
}
