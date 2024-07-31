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
 * Tiny media plugin embed handler class.
 *
 * This handles anything that embed requires like:
 * - Calling the media preview in embedPreview.
 * - Loading the embed insert.
 * - Getting selected media data.
 * - Handles url and repository uploads.
 * - Reset embed insert when embed preview is deleted.
 * - Handles media embedding into tiny and etc.
 *
 * @module      tiny_media/embed/embedhandler
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Selectors from "../selectors";
import {EmbedInsert} from './embedinsert';
import {
    body,
    footer,
    setPropertiesFromData,
    hideElements,
    isValidUrl,
    convertStringUrlToObject,
    stopMediaLoading,
} from '../helpers';
import * as ModalEvents from 'core/modal_events';
import {displayFilepicker} from 'editor_tiny/utils';
import {
    insertMediaTemplateContext,
    getHelpStrings,
    prepareMoodleLang,
} from "./embedhelpers";

export class EmbedHandler {

    constructor(data) {
        setPropertiesFromData(this, data); // Creates dynamic properties based on "data" param.
    }

    /**
     * Load the media insert dialogue.
     *
     * @param {object} templateContext Object template context
     */
    loadTemplatePromise = (templateContext) => {
        templateContext.elementid = this.editor.id;
        templateContext.bodyTemplate = Selectors.EMBED.template.body.insertMediaBody;
        templateContext.footerTemplate = Selectors.EMBED.template.footer.insertMediaFooter;
        templateContext.selector = 'EMBED';

        Promise.all([body(templateContext, this.root), footer(templateContext, this.root)])
            .then(() => {
                (new EmbedInsert(this)).init();
                return;
            })
            .catch(error => {
                window.console.log(error);
            });
    };

    /**
     * Loads the media preview dialogue.
     *
     * @param {object} embedPreview Object of embedPreview
     * @param {object} templateContext Object of template context
     */
    loadMediaDetails = async(embedPreview, templateContext) => {
        Promise.all([body(templateContext, this.root), footer(templateContext, this.root)])
            .then(() => {
                // Hide the body template when preparing the media preview.
                hideElements(Selectors.EMBED.elements.bodyTemplate, this.root);

                if (this.mediaData) { // It came from mediaThumbnail and we should kill uploadThumbnailModal modal.
                    this.currentModal.uploadThumbnailModal.destroy();
                    const currentModal = this.currentModal.insertMediaModal;
                    this.currentModal = currentModal.insertMediaModal;
                }
                embedPreview.init();
                return;
            })
            .catch(error => {
                if (!this.mediaData) { // It came from mediaThumbnail and we did not init startMediaLoading from there.
                    stopMediaLoading(this.root, 'EMBED');
                }
                window.console.log(error);
            });
    };

    /**
     * Reset the media insert modal form.
     */
    resetUploadForm = () => {
        this.mediaType = null; // Set to null to be set again.
        this.loadTemplatePromise(insertMediaTemplateContext(this));
    };

    /**
     * Get selected media data.
     *
     * @returns {null|object}
     */
    getMediumProperties = () => {
        const boolAttr = (elem, attr) => {
            // As explained in MDL-64175, some OS (like Ubuntu), are removing the value for these attributes.
            // So in order to check if attr="true", we need to check if the attribute exists and if the value is empty or true.
            return (elem.hasAttribute(attr) && (elem.getAttribute(attr) || elem.getAttribute(attr) === ''));
        };

        const medium = this.selectedMedia;
        if (!medium) {
            return null;
        }

        const isLink = (this.mediaType === 'link');
        if (isLink) {
            const urlParams = convertStringUrlToObject(medium.href);
            const mediaData = {
                type: this.mediaType,
                title: medium.textContent.trim(),
            };

            for (const param in urlParams) {
                let prop = param;
                if (param === 'mute') {
                    prop = 'muted';
                }
                const isTrue = (urlParams[param] === 'true') || (urlParams[param] === '1');
                mediaData[prop] = isTrue;
            }
            return mediaData;
        } else {
            const tracks = {
                subtitles: [],
                captions: [],
                descriptions: [],
                chapters: [],
                metadata: []
            };
            const sources = [];

            medium.querySelectorAll('track').forEach((track) => {
                tracks[track.getAttribute('kind')].push({
                    src: track.getAttribute('src'),
                    srclang: track.getAttribute('srclang'),
                    label: track.getAttribute('label'),
                    defaultTrack: boolAttr(track, 'default')
                });
            });

            medium.querySelectorAll('source').forEach((source) => {
                sources.push(source.src);
            });
            const title = medium.getAttribute('title');

            return {
                type: this.mediaType,
                sources,
                poster: medium.getAttribute('poster'),
                title: title ? title.trim() : false,
                width: medium.getAttribute('width'),
                height: medium.getAttribute('height'),
                autoplay: boolAttr(medium, 'autoplay'),
                loop: boolAttr(medium, 'loop'),
                muted: boolAttr(medium, 'muted'),
                controls: boolAttr(medium, 'controls'),
                tracks,
            };
        }
    };

    /**
     * Get selected media data.
     *
     * @returns {object}
     */
    getCurrentEmbedData = () => {
        const properties = this.getMediumProperties();
        if (!properties) {
            return {};
        }

        const processedProperties = {};
        processedProperties.media = properties;
        processedProperties.link = false;

        return processedProperties;
    };

    /**
     * Get help strings for media subtitles and captions.
     *
     * @returns {null|object}
     */
    getHelpStrings = async() => {
        if (!this.helpStrings) {
            this.helpStrings = await getHelpStrings();
        }

        return this.helpStrings;
    };

    /**
     * Set template context for insert media dialogue.
     *
     * @param {object} data Object of media data
     * @returns {object}
     */
    getTemplateContext = async(data) => {
        const languages = prepareMoodleLang(this.editor);
        const helpIcons = Array.from(Object.entries(await this.getHelpStrings())).forEach(([key, text]) => {
            data[`${key.toLowerCase()}helpicon`] = {text};
        });

        return Object.assign({}, {
            elementid: this.editor.getElement().id,
            showFilePickerTrack: this.canShowFilePickerTrack,
            langsInstalled: languages.installed,
            langsAvailable: languages.available,
            media: true,
            isUpdating: this.isUpdating,
        }, data, helpIcons);
    };

    /**
     * Set and get media template context.
     *
     * @param {null|object} data Null or object of media data
     * @returns {Promise<object>} A promise that resolves template context.
     */
    getMediaTemplateContext = async(data = null) => {
        if (!data) {
            data = Object.assign({}, this.getCurrentEmbedData());
        }
        this.isUpdating = Object.keys(data).length !== 0;
        return await this.getTemplateContext(data);
    };

    /**
     * Handles changes in the media URL input field and loads a preview of the media if the URL has changed.
     */
    urlChanged() {
        hideElements(Selectors.EMBED.elements.urlWarning, this.root);
        const url = this.root.querySelector(Selectors.EMBED.elements.fromUrl).value;
        if (url && url !== this.currentUrl) {
            this.loadMediaPreview(url);
        }
    }

    /**
     * Load the media preview dialogue.
     *
     * @param {string} url String of media url
     */
    loadMediaPreview = (url) => {
        (new EmbedInsert(this)).loadMediaPreview(url);
    };

    /**
     * Callback for file picker that previews the media or add the captions and subtitles.
     *
     * @param {object} params Object of media url and etc
     */
    trackFilePickerCallback(params) {
        if (params.url !== '') {
            this.loadMediaPreview(params.url);
        }
    }

    /**
     * Handle click events.
     *
     * @param {html} e Selected element
     */
    clickHandler = async(e) => {
        const element = e.target;

        const mediaBrowser = element.closest(Selectors.EMBED.actions.mediaBrowser);
        if (mediaBrowser) {
            e.preventDefault();
            const params = await displayFilepicker(this.editor, 'media');
            this.trackFilePickerCallback(params);
        }

        const addUrlEle = e.target.closest(Selectors.EMBED.actions.addUrl);
        if (addUrlEle) {
            this.urlChanged();
        }
    };

    /**
     * Enables or disables the URL-related buttons in the footer based on the current URL and input value.
     *
     * @param {html} input Url input field
     */
    toggleUrlButton(input) {
        const url = input.value;
        const addUrl = this.root.querySelector(Selectors.EMBED.actions.addUrl);
        addUrl.disabled = !(url !== "" && isValidUrl(url));
    }

    registerEventListeners = async(modal) => {
        await modal.getBody();
        const $root = modal.getRoot();
        const root = $root[0];
        if (this.canShowFilePickerTrack) {
            root.addEventListener('click', this.clickHandler.bind(this));
        }

        root.addEventListener('input', (e) => {
            const urlEle = e.target.closest(Selectors.EMBED.elements.fromUrl);
            if (urlEle) {
                this.toggleUrlButton(urlEle);
            }
        });

        $root.on(ModalEvents.hidden, () => {
            this.currentModal.destroy();
        });
    };
}
