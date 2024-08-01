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
} from '../helpers';
import Notification from 'core/notification';
import {EmbedPreview} from './embedpreview';
import {mediaDetailsTemplateContext} from './embedhelpers';
import {EmbedHandler} from './embedhandler';

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
        this.currentModal.uploadThumbnailModal.setTitle(getString('thumbnail', 'tiny_media'));
        sourceTypeChecked({
            source: this.thumbnail,
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
        const preview = this.thumbnailModalRoot.querySelector(Selectors.EMBED.elements.preview);
        preview.src = this.thumbnail;
    };

    /**
     * Deletes the media after confirming with the user and loads the insert media page.
     */
    deleteMedia = () => {
        Notification.deleteCancelPromise(
            getString('deletemediathumbnail', 'tiny_media'),
            getString('deletemediathumbnailwarning', 'tiny_media'),
        ).then(() => {
            this.currentModal.uploadThumbnailModal.destroy();
            return;
        }).catch(error => {
            window.console.log(error);
        });
    };

    /**
     * Loads and displays a media preview with thumbnail.
     */
    loadPreviewMediaThumbnail = async() => {
        const templateContext = await mediaDetailsTemplateContext(this);
        templateContext.selector = 'EMBED';
        if (this.mediaType === 'video' && this.isUpdating) {
            // Let's get selected video height & width and create props for them to be used in mediaDetails.
            const media = templateContext.media;
            if (media.height !== '' && media.width !== '') {
                this.mediaHeight = media.height;
                this.mediaWidth = media.width;
            }
        }

        (new EmbedHandler(this)).loadMediaDetails(new EmbedPreview(this), templateContext);
    };

    /**
     * Only registers event listeners for new loaded elements in mediaThumbnail.
     */
    registerMediaThumbnailEventListeners = () => {
        const deleteMedia = this.thumbnailModalRoot.querySelector(Selectors.EMBED.actions.deleteThumbnail);
        if (deleteMedia) {
            deleteMedia.addEventListener('click', () => {
                this.deleteMedia();
            });
        }

        const setPoster = this.thumbnailModalRoot.querySelector(Selectors.EMBED.actions.setPoster);
        if (setPoster) {
            setPoster.addEventListener('click', () => {
                this.loadPreviewMediaThumbnail();
            });
        }
    };
}
