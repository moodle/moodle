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
 * Tiny media plugin embed preview and details class.
 *
 * This handles the embed file/url preview before embedding them into tiny editor.
 *
 * @module      tiny_media/embed/embedpreview
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {alert} from 'core/notification';
import Selectors from '../selectors';
import {component} from '../common';
import {getString} from 'core/str';
import {
    sourceTypeChecked,
    getFileName,
    setPropertiesFromData,
    showElements,
    stopMediaLoading,
} from '../helpers';
import {EmbedHandler} from './embedhandler';
import {MediaBase} from '../mediabase';

export class EmbedPreview extends MediaBase {

    selectorType = 'EMBED';

    isEmbedPreviewDeleted = false;

    constructor(data) {
        super();
        setPropertiesFromData(this, data); // Creates dynamic properties based on "data" param.
    }

    /**
     * Init the media details preview.
     */
    init = async() => {
        this.currentModal.setTitle(getString('mediadetails', component));
        sourceTypeChecked({
            source: this.mediaSource,
            root: this.root,
            urlSelector: Selectors.EMBED.elements.fromUrl,
            fileNameSelector: Selectors.EMBED.elements.fileNameLabel,
        });
        this.setMediaSourceAndPoster();
        this.registerMediaDetailsEventListeners(this.currentModal);
    };

    /**
     * Sets media source and thumbnail for the video.
     */
    setMediaSourceAndPoster = () => {
        const box = this.root.querySelector(Selectors.EMBED.elements.previewBox);
        const preview = this.root.querySelector(Selectors.EMBED.elements.preview);
        preview.src = this.mediaSource;
        preview.innerHTML = this.mediaSource;

        // Getting and setting up media title/name.
        if (['video', 'audio'].includes(this.mediaType)) {
            let fileName = getFileName(this.root); // Get original filename.
            if (this.isUpdating) {
                if (!this.isEmbedPreviewDeleted) {
                    fileName = this.mediaTitle; // Title from the selected media.
                }
            }

            // Set the media name/title.
            this.root.querySelector(Selectors.EMBED.elements.title).value = fileName;
        }

        // Handle error when loading the media.
        preview.addEventListener('error', async() => {
            alert(
                await getString('medianotavailable', component),
                await getString('medianotavailabledesc', component, this.mediaSource)
            );

            // Stop the loader and display back the body template when failed to load the media.
            this.showBodyTemplate();

            (new EmbedHandler(this)).resetUploadForm();
            return;
        });

        if (this.mediaType === 'video') {
            let videoHeight = null;
            let videoWidth = null;
            const videoTag = document.querySelector(Selectors.EMBED.elements.videoTag);

            if (this.thumbnail) {
                videoTag.poster = this.thumbnail;
            }

            // Load the video html tag to load the media.
            videoTag.load();

            // Handle media metadata loading event.
            videoTag.addEventListener('loadedmetadata', () => {
                // Stop the loader and display back the body template when the media is loaded.
                this.showBodyTemplate();

                videoHeight = videoTag.videoHeight;
                videoWidth = videoTag.videoWidth;
                const widthProportion = (videoWidth - videoHeight);
                const isLandscape = widthProportion > 0;

                // Store dimensions of the raw video.
                this.mediaDimensions = {
                    width: videoWidth,
                    height: videoHeight,
                };

                // Set the media preview based on the media dimensions.
                if (isLandscape) {
                    videoTag.width = box.offsetWidth;
                } else {
                    videoTag.height = box.offsetHeight;
                }
            });

            // Handle media canplay event.
            videoTag.addEventListener('canplay', () => {
                const height = this.root.querySelector(Selectors.EMBED.elements.height);
                const width = this.root.querySelector(Selectors.EMBED.elements.width);

                if (height.value === '' && width.value === '') {
                    height.value = videoHeight;
                    width.value = videoWidth;
                }

                // Size checking and adjustment.
                if (videoHeight === parseInt(height.value) && videoWidth === parseInt(width.value)) {
                    this.currentWidth = this.mediaDimensions.width;
                    this.currentHeight = this.mediaDimensions.height;
                    this.sizeChecked('original');
                } else {
                    this.currentWidth = parseInt(width.value);
                    this.currentHeight = parseInt(height.value);
                    this.sizeChecked('custom');
                }
            });
        } else if (this.mediaType === 'audio') {
            const audioTag = this.root.querySelector(Selectors.EMBED.elements.audioTag);
            audioTag.load();

            // Handle media metadata loading event.
            audioTag.addEventListener('loadedmetadata', () => {
                // Stop the loader and display back the body template when the media is loaded.
                this.showBodyTemplate();
            });
        } else {
            // Stop the loader and display back the body template when the media is loaded.
            this.showBodyTemplate();

            // Set iframe width/height = box width/height.
            preview.width = box.offsetWidth;
            preview.height = box.offsetHeight;
        }
    };

    /**
     * Stop the loader and display back the body template.
     */
    showBodyTemplate = () => {
        stopMediaLoading(this.root, 'EMBED');
        showElements(Selectors.EMBED.elements.bodyTemplate, this.root);
    };

    /**
     * Only registers event listeners for new loaded elements in embed preview modal.
     */
    registerMediaDetailsEventListeners = async() => {
        // Handle media autoplay and mute.
        const autoPlay = this.root.querySelector(Selectors.EMBED.elements.mediaAutoplay);
        const mute = this.root.querySelector(Selectors.EMBED.elements.mediaMute);
        if (autoPlay && mute && this.mediaType === 'link') {
            autoPlay.addEventListener('change', () => {
                if (autoPlay.checked) {
                    mute.checked = true;
                }
            });

            mute.addEventListener('change', () => {
                if (autoPlay.checked && !mute.checked) {
                    autoPlay.checked = false;
                }
            });
        }

        // Handle the original size when selected.
        const sizeOriginalEle = this.root.querySelector(Selectors.EMBED.elements.sizeOriginal);
        if (sizeOriginalEle) {
            sizeOriginalEle.addEventListener('change', () => {
                this.sizeChecked('original');
            });
        }

        // Handle the custom size when selected.
        const sizeCustomEle = this.root.querySelector(Selectors.EMBED.elements.sizeCustom);
        if (sizeCustomEle) {
            sizeCustomEle.addEventListener('change', () => {
                this.sizeChecked('custom');
            });
        }

        // Handle the custom with size when inputted.
        const widthEle = this.root.querySelector(Selectors.EMBED.elements.width);
        if (widthEle) {
            widthEle.addEventListener('input', () => {
                // Avoid empty value.
                widthEle.value = widthEle.value === "" ? 0 : Number(widthEle.value);
                this.autoAdjustSize();
            });
        }

        // Handle the custom height size when inputted.
        const heightEle = this.root.querySelector(Selectors.EMBED.elements.height);
        if (heightEle) {
            heightEle.addEventListener('input', () => {
                // Avoid empty value.
                heightEle.value = heightEle.value === "" ? 0 : Number(heightEle.value);
                this.autoAdjustSize(true);
            });
        }
    };
}
