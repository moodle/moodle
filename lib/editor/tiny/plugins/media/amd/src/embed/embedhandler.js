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
    createUrlParams,
} from '../helpers';
import * as ModalEvents from 'core/modal_events';
import {displayFilepicker} from 'editor_tiny/utils';
import {
    insertMediaTemplateContext,
    getHelpStrings,
    prepareMoodleLang,
    getMoodleLangObj,
    getLinkBasedUrl,
} from "./embedhelpers";
import Templates from 'core/templates';

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
     * @param {html} element Selected element.
     * @param {string} fpType Caption type.
     */
    trackFilePickerCallback(params, element, fpType) {
        if (params.url !== '') {
            const tabPane = element.closest('.tab-pane');
            if (tabPane) {
                element.closest(Selectors.EMBED.elements.source).querySelector(Selectors.EMBED.elements.url).value = params.url;

                if (fpType === 'subtitle') {
                    // If the file is subtitle file. We need to match the language and label for that file.
                    const subtitleLang = params.file.split('.vtt')[0].split('-').slice(-1)[0];
                    const langObj = getMoodleLangObj(subtitleLang, this.editor);
                    if (langObj) {
                        const track = element.closest(Selectors.EMBED.elements.track);
                        track.querySelector(Selectors.EMBED.elements.trackLabel).value = langObj.lang.trim();
                        track.querySelector(Selectors.EMBED.elements.trackLang).value = langObj.code;
                    }
                }
            } else {
                this.loadMediaPreview(params.url);
            }
        }
    }

    /**
     * Add new html track element.
     *
     * @param {html} element
     */
    addTrackComponent(element) {
        const trackElement = element.closest(Selectors.EMBED.elements.track);
        const clone = trackElement.cloneNode(true);

        trackElement.querySelector('.removecomponent-wrapper').classList.remove('hidden');
        trackElement.querySelector('.addcomponent-wrapper').classList.add('hidden');
        trackElement.parentNode.insertBefore(clone, trackElement.nextSibling);
    }

    /**
     * Remove added html track element.
     *
     * @param {html} element
     */
    removeTrackComponent(element) {
        const sourceElement = element.closest(Selectors.EMBED.elements.track);
        sourceElement.remove();
    }

    /**
     * Get picker type based on the selected element.
     *
     * @param {html} element Selected element
     * @returns {string}
     */
    getFilePickerTypeFromElement = (element) => {
        if (element.closest(Selectors.EMBED.elements.posterSource)) {
            return 'image';
        }
        if (element.closest(Selectors.EMBED.elements.trackSource)) {
            return 'subtitle';
        }

        return 'media';
    };

    /**
     * Get captions/subtitles type.
     *
     * @param {html} tabPane
     * @returns {string}
     */
    getTrackTypeFromTabPane = (tabPane) => {
        return tabPane.getAttribute('data-track-kind');
    };

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
            const fpType = this.getFilePickerTypeFromElement(element);
            const params = await displayFilepicker(this.editor, fpType);
            this.trackFilePickerCallback(params, element, fpType);
        }

        const addUrlEle = e.target.closest(Selectors.EMBED.actions.addUrl);
        if (addUrlEle) {
            this.urlChanged();
        }

        const addComponentTrackAction = element.closest(Selectors.EMBED.elements.track + ' .addcomponent');
        if (addComponentTrackAction) {
            e.preventDefault();
            this.addTrackComponent(element);
        }

        const removeComponentTrackAction = element.closest(Selectors.EMBED.elements.track + ' .removecomponent');
        if (removeComponentTrackAction) {
            e.preventDefault();
            this.removeTrackComponent(element);
        }

        // Only allow one track per tab to be selected as "default".
        const trackDefaultAction = element.closest(Selectors.EMBED.elements.trackDefault);
        if (trackDefaultAction && trackDefaultAction.checked) {
            const getKind = (el) => this.getTrackTypeFromTabPane(el.parentElement.closest('.tab-pane'));

            element.parentElement
                .closest('.tab-content')
                .querySelectorAll(Selectors.EMBED.elements.trackDefault)
                .forEach((select) => {
                    if (select !== element && getKind(element) === getKind(select)) {
                        select.checked = false;
                    }
                });
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

    /**
     * Get media html to be inserted or updated into tiny.
     *
     * @param {html} form Selected element
     * @returns {string} String of html
     */
    getMediaHTML = (form) => {
        let mediumType = this.root.querySelector(Selectors.EMBED.elements.previewClass).nodeName.toLowerCase();
        mediumType = (mediumType === 'iframe') ? 'link' : mediumType;
        const tabContent = form.querySelector('.tab-content');
        const callback = 'getMediaHTML' + mediumType[0].toUpperCase() + mediumType.substr(1);
        return this[callback](tabContent);
    };

    /**
     * Get media as link.
     *
     * @returns {string} String of html
     */
    getMediaHTMLLink = () => {
        const details = document.querySelector(Selectors.EMBED.elements.mediaDetailsBody);
        const url = details.querySelector(Selectors.EMBED.elements.preview).src ?? null;
        const urlParamString = createUrlParams({
            controls: details.querySelector(Selectors.EMBED.elements.mediaControl).checked ? 1 : 0,
            autoplay: details.querySelector(Selectors.EMBED.elements.mediaAutoplay).checked ? 1 : 0,
            mute: details.querySelector(Selectors.EMBED.elements.mediaMute).checked ? 1 : 0,
        });
        const name = details.querySelector(Selectors.EMBED.elements.title).value || false;
        const context = {
            url: getLinkBasedUrl(url) + urlParamString,
            name: name ? name.trim() : false,
        };

        return context.url ? Templates.renderForPromise('tiny_media/embed/embed_media_link', context) : '';
    };

    /**
     * Get media as video.
     *
     * @param {html} tab Selected element
     * @returns {string} String of html.
     */
    getMediaHTMLVideo = (tab) => {
        const details = document.querySelector(Selectors.EMBED.elements.mediaDetailsBody);
        const context = this.getContextForMediaHTML(tab, details);
        context.width = details.querySelector(Selectors.EMBED.elements.width).value || false;
        context.height = details.querySelector(Selectors.EMBED.elements.height).value || false;
        context.poster = details.querySelector(Selectors.EMBED.elements.videoTag).poster || false;
        return context.sources ? Templates.renderForPromise('tiny_media/embed/embed_media_video', context) : '';
    };

    /**
     * Get media as audio.
     *
     * @param {html} tab Selected element
     * @returns {string} String of html.
     */
    getMediaHTMLAudio = (tab) => {
        const details = document.querySelector(Selectors.EMBED.elements.mediaDetailsBody);
        const context = this.getContextForMediaHTML(tab, details);
        return context.sources.length ? Templates.renderForPromise('tiny_media/embed/embed_media_audio', context) : '';
    };

    /**
     * Get previewed media data.
     *
     * @param {html} tab Selected element
     * @param {html} details Selected element
     * @returns {object}
     */
    getContextForMediaHTML = (tab, details) => {
        const tracks = Array.from(tab.querySelectorAll(Selectors.EMBED.elements.track)).map(track => ({
            track: track.querySelector(Selectors.EMBED.elements.trackSource + ' ' + Selectors.EMBED.elements.url).value,
            kind: this.getTrackTypeFromTabPane(track.closest('.tab-pane')),
            label: track.querySelector(Selectors.EMBED.elements.trackLabel).value ||
                track.querySelector(Selectors.EMBED.elements.trackLang).value,
            srclang: track.querySelector(Selectors.EMBED.elements.trackLang).value,
            defaultTrack: track.querySelector(Selectors.EMBED.elements.trackDefault).checked ? "true" : null
        })).filter((track) => !!track.track);

        const sources = details.querySelector(Selectors.EMBED.elements.preview).src ?? null;
        const title = details.querySelector(Selectors.EMBED.elements.title).value;

        return {
            sources,
            description: tab.querySelector(Selectors.EMBED.elements.url).value || false,
            tracks,
            showControls: details.querySelector(Selectors.EMBED.elements.mediaControl).checked,
            autoplay: details.querySelector(Selectors.EMBED.elements.mediaAutoplay).checked,
            muted: details.querySelector(Selectors.EMBED.elements.mediaMute).checked,
            loop: details.querySelector(Selectors.EMBED.elements.mediaLoop).checked,
            title: title !== '' ? title.trim() : false,
        };
    };

    /**
     * Handle the insert/update media in tiny editor.
     *
     * @param {event} event
     * @param {object} modal Object of current modal
     */
    handleDialogueSubmission = async(event, modal) => {
        const {html} = await this.getMediaHTML(modal.getRoot()[0]);
        if (html) {
            if (this.isUpdating) {
                this.selectedMedia.outerHTML = html;
                this.isUpdating = false;
            } else {
                this.editor.insertContent(html);
            }
        }
    };

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

        $root.on(ModalEvents.shown, () => {
            this.root.querySelectorAll(Selectors.EMBED.elements.trackLang).forEach((dropdown) => {
                const defaultVal = dropdown.getAttribute('data-value');
                if (defaultVal) {
                    dropdown.value = defaultVal;
                }
            });
        });

        $root.on(ModalEvents.save, this.handleDialogueSubmission.bind(this));
    };
}
