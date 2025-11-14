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

import Selectors from '../selectors';
import {component} from '../common';
import {getString} from 'core/str';
import {
    sourceTypeChecked,
    getFileName,
    setPropertiesFromData,
    showElements,
    stopMediaLoading,
    hideElements,
} from '../helpers';
import {EmbedHandler} from './embedhandler';
import {MediaBase} from '../mediabase';
import Notification from 'core/notification';
import EmbedModal from './embedmodal';
import {
    getEmbeddedMediaDetails,
    insertMediaThumbnailTemplateContext,
    fetchPreview,
} from './embedhelpers';
import {notifyFilterContentUpdated} from 'core_filters/events';

export class EmbedPreview extends MediaBase {

    // Selector type for "EMBED".
    selectorType = Selectors.EMBED.type;

    // Fixed aspect ratio used for external media providers.
    linkMediaAspectRatio = 1.78;

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
            fetchedTitle: this.fetchedMediaLinkTitle ?? null,
            source: this.originalUrl,
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
    setMediaSourceAndPoster = async() => {
        const box = this.root.querySelector(Selectors.EMBED.elements.previewBox);
        const previewArea = document.querySelector(Selectors.EMBED.elements.mediaPreviewContainer);
        previewArea.setAttribute('data-original-url', this.originalUrl);

        // Previewing existing media could be a link one.
        // Or, new media added using url input and mediaType is neither video or audio.
        if (this.mediaType === 'link' || (this.newMediaLink && !['video', 'audio'].includes(this.mediaType))) {
            previewArea.setAttribute('data-media-type', 'link');
            previewArea.innerHTML = await fetchPreview(this.originalUrl, this.contextId);
            notifyFilterContentUpdated(previewArea);
        } else if (this.mediaType === 'video') {
            const video = document.createElement('video');
            video.src = this.originalUrl;

            // Media url can be played using html video.
            video.addEventListener('loadedmetadata', () => {
                const videoHeight = video.videoHeight;
                const videoWidth = video.videoWidth;
                const widthProportion = (videoWidth - videoHeight);
                const isLandscape = widthProportion > 0;

                // Store dimensions of the raw video.
                this.mediaDimensions = {
                    width: videoWidth,
                    height: videoHeight,
                };

                // Set the media preview based on the media dimensions.
                if (isLandscape) {
                    video.width = box.offsetWidth;
                } else {
                    video.height = box.offsetHeight;
                }

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

            video.controls = true;
            if (this.media.poster) {
                previewArea.setAttribute('data-media-poster', this.media.poster);
                if (!video.classList.contains('w-100')) {
                    video.classList.add('w-100');
                }
                video.poster = this.media.poster;
            }
            video.load();

            previewArea.setAttribute('data-media-type', 'video');
            previewArea.innerHTML = video.outerHTML;
            notifyFilterContentUpdated(previewArea);
        } else if (this.mediaType === 'audio') {
            const audio = document.createElement('audio');
            audio.src = this.originalUrl;
            audio.controls = true;
            audio.load();

            previewArea.setAttribute('data-media-type', 'audio');
            previewArea.innerHTML = audio.outerHTML;
            notifyFilterContentUpdated(previewArea);
        } else {
            // Show warning notification.
            const urlWarningLabelEle = this.root.querySelector(Selectors.EMBED.elements.urlWarning);
            urlWarningLabelEle.innerHTML = await getString('medianotavailabledesc', component, this.originalUrl);
            showElements(Selectors.EMBED.elements.urlWarning, this.root);

            // Stop the spinner.
            stopMediaLoading(this.root, Selectors.EMBED.type);

            // Reset the upload form.
            (new EmbedHandler(this)).resetUploadForm();
            return;
        }

        // Stop the loader and display back the body template when the media is loaded.
        stopMediaLoading(this.root, Selectors.EMBED.type);
        showElements(Selectors.EMBED.elements.mediaDetailsBody, this.root);

        // Set the media name/title.
        this.root.querySelector(Selectors.EMBED.elements.title).value = this.setMediaTitle();
    };

    /**
     * Set media name/title.
     *
     * @returns {string}
     */
    setMediaTitle = () => {
        // Getting and setting up media title/name.
        let fileName = null;
        if (['video', 'audio'].includes(this.mediaType)) {
            fileName = getFileName(this.originalUrl); // Get original filename.
        } else if (this.fetchedMediaLinkTitle) {
            fileName = this.fetchedMediaLinkTitle;
        } else {
            fileName = this.originalUrl;
        }

        if (this.isUpdating) {
            if (!this.newMediaLink) {
                fileName = this.mediaTitle; // Title from the selected media.
            }
        }

        return decodeURI(fileName);
    };

    /**
     * Deletes the media after confirming with the user and loads the insert media page.
     */
    deleteMedia = () => {
        Notification.deleteCancelPromise(
            getString('deletemedia', component),
            getString('deletemediawarning', component),
        ).then(() => {
            // Reset media upload form.
            (new EmbedHandler(this)).resetUploadForm();

            // Delete any selected media mediaData.
            delete this.mediaData;
            return;
        }).catch(error => {
            window.console.log(error);
        });
    };

    /**
     * Delete embedded media thumbnail.
     */
    deleteEmbeddedThumbnail = () => {
        Notification.deleteCancelPromise(
            getString('deleteembeddedthumbnail', component),
            getString('deleteembeddedthumbnailwarning', component),
        ).then(async() => {
            if (this.mediaType === 'video') {
                const video = this.root.querySelector('video');
                if (video) {
                    video.removeAttribute('poster');
                    const preview = this.root.querySelector(Selectors.EMBED.elements.mediaPreviewContainer);
                    preview.removeAttribute('data-media-poster');
                }
            }

            const deleteCustomThumbnail = this.root.querySelector(Selectors.EMBED.actions.deleteCustomThumbnail);
            deleteCustomThumbnail.remove();

            const uploadCustomThumbnail = this.root.querySelector(Selectors.EMBED.actions.uploadCustomThumbnail);
            uploadCustomThumbnail.textContent = await getString('uploadthumbnail', component);
            return;
        }).catch(error => {
            window.console.log(error);
        });
    };

    /**
     * Shows the insert thumbnail dialogue.
     */
    showUploadThumbnail = async() => {
        const uploadThumbnailModal = await EmbedModal.create({
            large: true,
            templateContext: {elementid: this.editor.getElement().id},
        });
        const root = uploadThumbnailModal.getRoot()[0];

        // Get selected media metadata.
        const mediaData = getEmbeddedMediaDetails(this);
        mediaData.isUpdating = this.isUpdating;

        const embedHandler = new EmbedHandler(this);
        embedHandler.loadInsertThumbnailTemplatePromise(
            insertMediaThumbnailTemplateContext(this), // Get template context for creating media thumbnail.
            {root, uploadThumbnailModal}, // Required root elements.
            await embedHandler.getMediaTemplateContext(mediaData) // Get current media data.
        );
    };

    /**
     * Only registers event listeners for new loaded elements in embed preview modal.
     */
    registerMediaDetailsEventListeners = async() => {
        // Handle the original size when selected.
        const originalSize = this.root.querySelector(Selectors.EMBED.elements.originalSizeToggle);
        if (originalSize) {
            originalSize.addEventListener('click', (e) => {
                e.preventDefault();
                this.sizeChecked('original');
            });
        }

        // Handle the custom size when selected.
        const customSize = this.root.querySelector(Selectors.EMBED.elements.customSizeToggle);
        if (customSize) {
            customSize.addEventListener('click', (e) => {
                e.preventDefault();
                this.sizeChecked('custom');
            });
        }

        const widthEle = this.root.querySelector(Selectors.EMBED.elements.width);
        const heightEle = this.root.querySelector(Selectors.EMBED.elements.height);

        // Handle the custom with size when inputted.
        if (widthEle) {
            widthEle.addEventListener('input', () => {
                if (this.mediaType === 'link') {
                    // Let's apply the 16:9 aspect ratio if it's a link media type.
                    heightEle.value = Math.round(widthEle.value / this.linkMediaAspectRatio);
                } else {
                    // Avoid empty value.
                    widthEle.value = widthEle.value === "" ? 0 : Number(widthEle.value);
                    this.autoAdjustSize();
                }
            });
        }

        // Handle the custom height size when inputted.
        if (heightEle) {
            heightEle.addEventListener('input', () => {
                if (this.mediaType === 'link') {
                    // Let's apply the 16:9 aspect ratio if it's a link media type.
                    widthEle.value = Math.round(heightEle.value * this.linkMediaAspectRatio);
                } else {
                    // Avoid empty value.
                    heightEle.value = heightEle.value === "" ? 0 : Number(heightEle.value);
                    this.autoAdjustSize(true);
                }
            });
        }

        // Handle media preview delete.
        const deleteMedia = this.root.querySelector(Selectors.EMBED.actions.deleteMedia);
        if (deleteMedia) {
            deleteMedia.addEventListener('click', (e) => {
                e.preventDefault();
                this.deleteMedia();
            });
        }

        // Show subtitles and captions settings.
        const showSubtitleCaption = this.root.querySelector(Selectors.EMBED.actions.showSubtitleCaption);
        if (showSubtitleCaption) {
            showSubtitleCaption.addEventListener('click', (e) => {
                e.preventDefault();
                hideElements([
                    Selectors.EMBED.actions.showSubtitleCaption,
                    Selectors.EMBED.actions.cancelMediaDetails,
                    Selectors.EMBED.elements.mediaDetailsBody,
                ], this.root);
                showElements([
                    Selectors.EMBED.actions.backToMediaDetails,
                    Selectors.EMBED.elements.mediaSubtitleCaptionBody,
                ], this.root);
            });
        }

        // Back to media preview.
        const backToMediaDetails = this.root.querySelector(Selectors.EMBED.actions.backToMediaDetails);
        if (backToMediaDetails) {
            backToMediaDetails.addEventListener('click', () => {
                hideElements([
                    Selectors.EMBED.actions.backToMediaDetails,
                    Selectors.EMBED.elements.mediaSubtitleCaptionBody,
                ], this.root);
                showElements([
                    Selectors.EMBED.actions.showSubtitleCaption,
                    Selectors.EMBED.actions.cancelMediaDetails,
                    Selectors.EMBED.elements.mediaDetailsBody,
                ], this.root);
            });
        }

        // Handles upload media thumbnail.
        const uploadCustomThumbnail = this.root.querySelector(Selectors.EMBED.actions.uploadCustomThumbnail);
        if (uploadCustomThumbnail) {
            uploadCustomThumbnail.addEventListener('click', () => {
                this.showUploadThumbnail();
            });
        }

        // Handles delete media thumbnail.
        const deleteCustomThumbnail = this.root.querySelector(Selectors.EMBED.actions.deleteCustomThumbnail);
        if (deleteCustomThumbnail) {
            deleteCustomThumbnail.addEventListener('click', () => {
                this.deleteEmbeddedThumbnail();
            });
        }

        // Handles language track selection.
        const langTracks = this.root.querySelectorAll(Selectors.EMBED.elements.trackLang);
        if (langTracks) {
            langTracks.forEach((dropdown) => {
                const defaultVal = dropdown.getAttribute('data-value');
                if (defaultVal) {
                    Array.from(dropdown.options).some(option => {
                        // Check if srclang in track is a language code like "en"
                        // or language name like "English" prior to MDL-85159.
                        if (option.dataset.languageCode === defaultVal || option.value === defaultVal) {
                            option.selected = true;
                            return true;
                        }
                        return false;
                    });
                }
            });
        }
    };
}
