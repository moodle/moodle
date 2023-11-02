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
 * Tiny Media plugin Embed class for Moodle.
 *
 * @module      tiny_media/embed
 * @copyright   2022 Huong Nguyen <huongnv13@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Templates from 'core/templates';
import {
    get_string as getString,
    get_strings as getStrings,
} from 'core/str';
import * as ModalFactory from 'core/modal_factory';
import * as ModalEvents from 'core/modal_events';
import {displayFilepicker} from 'editor_tiny/utils';
import {getCurrentLanguage, getMoodleLang} from 'editor_tiny/options';
import {component} from "./common";
import Modal from './embedmodal';
import Selectors from './selectors';
import {getEmbedPermissions} from './options';
import {getFilePicker} from 'editor_tiny/options';

export const MediaEmbed = class {
    editor = null;
    canShowFilePicker = false;
    canShowFilePickerPoster = false;
    canShowFilePickerTrack = false;

    /**
     * @property {Object} The names of the alignment options.
     */
    helpStrings = null;

    /**
     * @property {boolean} Indicate that the user is updating the media or not.
     */
    isUpdating = false;

    /**
     * @property {Object} The currently selected media.
     */
    selectedMedia = null;

    constructor(editor) {
        const permissions = getEmbedPermissions(editor);

        // Indicates whether the file picker can be shown.
        this.canShowFilePicker = permissions.filepicker && (typeof getFilePicker(editor, 'media') !== 'undefined');
        this.canShowFilePickerPoster = permissions.filepicker && (typeof getFilePicker(editor, 'image') !== 'undefined');
        this.canShowFilePickerTrack = permissions.filepicker && (typeof getFilePicker(editor, 'subtitle') !== 'undefined');

        this.editor = editor;
    }

    async getHelpStrings() {
        if (!this.helpStrings) {
            const [addSource, tracks, subtitles, captions, descriptions, chapters, metadata] = await getStrings([
                'addsource_help',
                'tracks_help',
                'subtitles_help',
                'captions_help',
                'descriptions_help',
                'chapters_help',
                'metadata_help',
            ].map((key) => ({
                key,
                component,
            })));

            this.helpStrings = {addSource, tracks, subtitles, captions, descriptions, chapters, metadata};
        }

        return this.helpStrings;
    }

    async getTemplateContext(data) {
        const languages = this.prepareMoodleLang();

        const helpIcons = Array.from(Object.entries(await this.getHelpStrings())).forEach(([key, text]) => {
            data[`${key.toLowerCase()}helpicon`] = {text};
        });

        return Object.assign({}, {
            elementid: this.editor.getElement().id,
            showfilepicker: this.canShowFilePicker,
            showfilepickerposter: this.canShowFilePickerPoster,
            showfilepickertrack: this.canShowFilePickerTrack,
            langsinstalled: languages.installed,
            langsavailable: languages.available,
            link: true,
            video: false,
            audio: false,
            isupdating: this.isUpdating,
        }, data, helpIcons);
    }

    async displayDialogue() {
        this.selectedMedia = this.getSelectedMedia();
        const data = Object.assign({}, this.getCurrentEmbedData());
        this.isUpdating = Object.keys(data).length !== 0;

        const modal = await ModalFactory.create({
            type: Modal.TYPE,
            title: getString('createmedia', 'tiny_media'),
            templateContext: await this.getTemplateContext(data),
            removeOnClose: true,
            large: true,
        });

        this.currentModal = modal;
        await this.registerEventListeners(modal);
        modal.show();
    }

    getCurrentEmbedData() {
        const properties = this.getMediumProperties();
        if (!properties) {
            return {};
        }

        const processedProperties = {};
        processedProperties[properties.type.toLowerCase()] = properties;
        processedProperties.link = false;

        return processedProperties;
    }

    getSelectedMedia() {
        const mediaElm = this.editor.selection.getNode();

        if (!mediaElm) {
            return null;
        }

        if (mediaElm.nodeName.toLowerCase() === 'video' || mediaElm.nodeName.toLowerCase() === 'audio') {
            return mediaElm;
        }

        if (mediaElm.querySelector('video')) {
            return mediaElm.querySelector('video');
        }

        if (mediaElm.querySelector('audio')) {
            return mediaElm.querySelector('audio');
        }

        return null;
    }

    getMediumProperties() {
        const boolAttr = (elem, attr) => {
            // As explained in MDL-64175, some OS (like Ubuntu), are removing the value for these attributes.
            // So in order to check if attr="true", we need to check if the attribute exists and if the value is empty or true.
            return (elem.hasAttribute(attr) && (elem.getAttribute(attr) || elem.getAttribute(attr) === ''));
        };

        const tracks = {
            subtitles: [],
            captions: [],
            descriptions: [],
            chapters: [],
            metadata: []
        };
        const sources = [];

        const medium = this.selectedMedia;
        if (!medium) {
            return null;
        }
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

        return {
            type: medium.nodeName.toLowerCase() === 'video' ? Selectors.EMBED.mediaTypes.video : Selectors.EMBED.mediaTypes.audio,
            sources,
            poster: medium.getAttribute('poster'),
            title: medium.getAttribute('title'),
            width: medium.getAttribute('width'),
            height: medium.getAttribute('height'),
            autoplay: boolAttr(medium, 'autoplay'),
            loop: boolAttr(medium, 'loop'),
            muted: boolAttr(medium, 'muted'),
            controls: boolAttr(medium, 'controls'),
            tracks,
        };
    }

    prepareMoodleLang() {
        const moodleLangs = getMoodleLang(this.editor);
        const currentLanguage = getCurrentLanguage(this.editor);

        const installed = Object.entries(moodleLangs.installed).map(([lang, code]) => ({
            lang,
            code,
            "default": lang === currentLanguage,
        }));

        const available = Object.entries(moodleLangs.available).map(([lang, code]) => ({
            lang,
            code,
            "default": lang === currentLanguage,
        }));

        return {
            installed,
            available,
        };
    }

    getMoodleLangObj(subtitleLang) {
        const {available} = getMoodleLang(this.editor);

        if (available[subtitleLang]) {
            return {
                lang: subtitleLang,
                code: available[subtitleLang],
            };
        }

        return null;
    }

    filePickerCallback(params, element, fpType) {
        if (params.url !== '') {
            const tabPane = element.closest('.tab-pane');
            element.closest(Selectors.EMBED.elements.source).querySelector(Selectors.EMBED.elements.url).value = params.url;

            if (tabPane.id === this.editor.getElement().id + '_' + Selectors.EMBED.mediaTypes.link.toLowerCase()) {
                tabPane.querySelector(Selectors.EMBED.elements.name).value = params.file;
            }

            if (fpType === 'subtitle') {
                // If the file is subtitle file. We need to match the language and label for that file.
                const subtitleLang = params.file.split('.vtt')[0].split('-').slice(-1)[0];
                const langObj = this.getMoodleLangObj(subtitleLang);
                if (langObj) {
                    const track = element.closest(Selectors.EMBED.elements.track);
                    track.querySelector(Selectors.EMBED.elements.trackLabel).value = langObj.lang.trim();
                    track.querySelector(Selectors.EMBED.elements.trackLang).value = langObj.code;
                }
            }
        }
    }

    addMediaSourceComponent(element, callback) {
        const sourceElement = element.closest(Selectors.EMBED.elements.source + Selectors.EMBED.elements.mediaSource);
        const clone = sourceElement.cloneNode(true);

        sourceElement.querySelector('.removecomponent-wrapper').classList.remove('hidden');
        sourceElement.querySelector('.addcomponent-wrapper').classList.add('hidden');

        sourceElement.parentNode.insertBefore(clone, sourceElement.nextSibling);

        if (callback) {
            callback(clone);
        }
    }

    removeMediaSourceComponent(element) {
        const sourceElement = element.closest(Selectors.EMBED.elements.source + Selectors.EMBED.elements.mediaSource);
        sourceElement.remove();
    }

    addTrackComponent(element, callback) {
        const trackElement = element.closest(Selectors.EMBED.elements.track);
        const clone = trackElement.cloneNode(true);

        trackElement.querySelector('.removecomponent-wrapper').classList.remove('hidden');
        trackElement.querySelector('.addcomponent-wrapper').classList.add('hidden');

        trackElement.parentNode.insertBefore(clone, trackElement.nextSibling);

        if (callback) {
            callback(clone);
        }
    }

    removeTrackComponent(element) {
        const sourceElement = element.closest(Selectors.EMBED.elements.track);
        sourceElement.remove();
    }

    getMediumTypeFromTabPane(tabPane) {
        return tabPane.getAttribute('data-medium-type');
    }

    getTrackTypeFromTabPane(tabPane) {
        return tabPane.getAttribute('data-track-kind');
    }

    getMediaHTML(form) {
        const mediumType = this.getMediumTypeFromTabPane(form.querySelector('.root.tab-content > .tab-pane.active'));
        const tabContent = form.querySelector(Selectors.EMBED.elements[mediumType.toLowerCase() + 'Pane']);

        return this['getMediaHTML' + mediumType[0].toUpperCase() + mediumType.substr(1)](tabContent);
    }

    getMediaHTMLLink(tab) {
        const context = {
            url: tab.querySelector(Selectors.EMBED.elements.url).value,
            name: tab.querySelector(Selectors.EMBED.elements.name).value || false
        };

        return context.url ? Templates.renderForPromise('tiny_media/embed_media_link', context) : '';
    }

    getMediaHTMLVideo(tab) {
        const context = this.getContextForMediaHTML(tab);
        context.width = tab.querySelector(Selectors.EMBED.elements.width).value || false;
        context.height = tab.querySelector(Selectors.EMBED.elements.height).value || false;
        context.poster = tab.querySelector(
            `${Selectors.EMBED.elements.posterSource} ${Selectors.EMBED.elements.url}`
        ).value || false;

        return context.sources.length ? Templates.renderForPromise('tiny_media/embed_media_video', context) : '';
    }

    getMediaHTMLAudio(tab) {
        const context = this.getContextForMediaHTML(tab);

        return context.sources.length ? Templates.renderForPromise('tiny_media/embed_media_audio', context) : '';
    }

    getContextForMediaHTML(tab) {
        const tracks = Array.from(tab.querySelectorAll(Selectors.EMBED.elements.track)).map(track => ({
            track: track.querySelector(Selectors.EMBED.elements.trackSource + ' ' + Selectors.EMBED.elements.url).value,
            kind: this.getTrackTypeFromTabPane(track.closest('.tab-pane')),
            label: track.querySelector(Selectors.EMBED.elements.trackLabel).value ||
                track.querySelector(Selectors.EMBED.elements.trackLang).value,
            srclang: track.querySelector(Selectors.EMBED.elements.trackLang).value,
            defaultTrack: track.querySelector(Selectors.EMBED.elements.trackDefault).checked ? "true" : null
        })).filter((track) => !!track.track);

        const sources = Array.from(tab.querySelectorAll(Selectors.EMBED.elements.mediaSource + ' '
            + Selectors.EMBED.elements.url))
                .filter((source) => !!source.value)
                .map((source) => source.value);

        return {
            sources,
            description: tab.querySelector(Selectors.EMBED.elements.mediaSource + ' '
                + Selectors.EMBED.elements.url).value || false,
            tracks,
            showControls: tab.querySelector(Selectors.EMBED.elements.mediaControl).checked,
            autoplay: tab.querySelector(Selectors.EMBED.elements.mediaAutoplay).checked,
            muted: tab.querySelector(Selectors.EMBED.elements.mediaMute).checked,
            loop: tab.querySelector(Selectors.EMBED.elements.mediaLoop).checked,
            title: tab.querySelector(Selectors.EMBED.elements.title).value || false
        };
    }

    getFilepickerTypeFromElement(element) {
        if (element.closest(Selectors.EMBED.elements.posterSource)) {
            return 'image';
        }
        if (element.closest(Selectors.EMBED.elements.trackSource)) {
            return 'subtitle';
        }

        return 'media';
    }

    async clickHandler(e) {
        const element = e.target;

        const mediaBrowser = element.closest(Selectors.EMBED.actions.mediaBrowser);
        if (mediaBrowser) {
            e.preventDefault();
            const fpType = this.getFilepickerTypeFromElement(element);
            const params = await displayFilepicker(this.editor, fpType);
            this.filePickerCallback(params, element, fpType);
        }

        const addComponentSourceAction = element.closest(Selectors.EMBED.elements.mediaSource + ' .addcomponent');
        if (addComponentSourceAction) {
            e.preventDefault();
            this.addMediaSourceComponent(element);
        }

        const removeComponentSourceAction = element.closest(Selectors.EMBED.elements.mediaSource + ' .removecomponent');
        if (removeComponentSourceAction) {
            e.preventDefault();
            this.removeMediaSourceComponent(element);
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
                .closest('.root.tab-content')
                .querySelectorAll(Selectors.EMBED.elements.trackDefault)
                .forEach((select) => {
                    if (select !== element && getKind(element) === getKind(select)) {
                        select.checked = false;
                    }
                });
        }
    }

    async handleDialogueSubmission(event, modal) {
        const {html} = await this.getMediaHTML(modal.getRoot()[0]);
        if (html) {
            if (this.isUpdating) {
                this.selectedMedia.outerHTML = html;
                this.isUpdating = false;
            } else {
                this.editor.insertContent(html);
            }
        }
    }

    async registerEventListeners(modal) {
        await modal.getBody();
        const $root = modal.getRoot();
        const root = $root[0];
        if (this.canShowFilePicker || this.canShowFilePickerPoster || this.canShowFilePickerTrack) {
            root.addEventListener('click', this.clickHandler.bind(this));
        }

        $root.on(ModalEvents.save, this.handleDialogueSubmission.bind(this));
        $root.on(ModalEvents.hidden, () => {
            this.currentModal.destroy();
        });
        $root.on(ModalEvents.shown, () => {
            root.querySelectorAll(Selectors.EMBED.elements.trackLang).forEach((dropdown) => {
                const defaultVal = dropdown.getAttribute('data-value');
                if (defaultVal) {
                    dropdown.value = defaultVal;
                }
            });
        });
    }
};
