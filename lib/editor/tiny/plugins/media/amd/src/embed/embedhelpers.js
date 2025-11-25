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
 * Tiny media plugin embed helpers.
 *
 * This provides easy access to any classes without instantiating a new object.
 *
 * @module      tiny_media/embed/embedhelpers
 * @copyright   2024 Stevani Andolo <stevani@hotmail.com.au>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Selectors from '../selectors';
import {getStrings} from 'core/str';
import {component} from "../common";
import {getCurrentLanguage, getMoodleLang} from 'editor_tiny/options';
import Ajax from 'core/ajax';
import {getFileName} from '../helpers';

/**
 * Return template context for insert media.
 *
 * @param {object} props
 * @returns {object}
 */
export const insertMediaTemplateContext = (props) => {
    return {
        mediaType: props.mediaType,
        showDropzone: props.canShowDropZone,
        showFilePicker: props.canShowFilePicker,
        fileType: 'audio/video',
    };
};

/**
 * Return template context for insert media.
 *
 * @param {object} props
 * @returns {object}
 */
export const insertMediaThumbnailTemplateContext = (props) => {
    return {
        elementid: props.editor.id,
        showDropzone: props.canShowDropZone,
        showImageFilePicker: props.canShowImageFilePicker,
        bodyTemplate: Selectors.EMBED.template.body.insertMediaBody,
        footerTemplate: Selectors.EMBED.template.footer.insertMediaFooter,
        fileType: 'image',
        selector: Selectors.EMBED.type,
    };
};

/**
 * Return selected media type and element.
 *
 * @param {editor} editor
 * @returns {Array}
 */
export const getSelectedMediaElement = (editor) => {
    let mediaType = null;
    let selectedMedia = null;
    const mediaElm = editor.selection.getNode();

    if (!mediaElm) {
        mediaType = null;
        selectedMedia = null;
    } else if (mediaElm.nodeName.toLowerCase() === 'video' || mediaElm.nodeName.toLowerCase() === 'audio') {
        mediaType = mediaElm.nodeName.toLowerCase();
        selectedMedia = mediaElm;
    } else if (mediaElm.querySelector('video')) {
        mediaType = 'video';
        selectedMedia = mediaElm.querySelector('video');
    } else if (mediaElm.querySelector('audio')) {
        mediaType = 'audio';
        selectedMedia = mediaElm.querySelector('audio');
    } else if (mediaElm.nodeName.toLowerCase() === 'a') {
        selectedMedia = mediaElm;
    }

    return [mediaType, selectedMedia];
};

/**
 * Returns result of media filtering.
 *
 * @param {string} url
 * @param {string} contextId
 * @returns {string}
 */
export const fetchPreview = async(url, contextId) => {
    const request = {
        methodname: 'tiny_media_preview',
        args: {
            contextid: contextId, // Use the system one.
            content: url,
        }
    };
    const responseObj = await Ajax.call([request])[0];
    return responseObj.content;
};

/**
 * Returns media type.
 *
 * @param {string} url
 * @returns {string|null}
 */
export const checkMediaType = async(url) => {
    try {
        const response = await fetch(url, {method: 'HEAD'});
        const contentType = response.headers.get('Content-type');

        if (!contentType) {
            return null;
        }

        if (contentType.startsWith('video/')) {
            return 'video';
        } else if (contentType.startsWith('audio/')) {
            return 'audio';
        }

        return null;
    } catch (e) {
        return null;
    }
};

/**
 * Returns media title.
 *
 * @param {string} url
 * @param {object} props
 * @returns {string|null} String of media title.
 */
export const getMediaTitle = async(url, props) => {
    const parsedUrl = new URL(url);

    // Try get the title via the file name.
    const pathName = parsedUrl.pathname;
    const extension = pathName.includes('.') ? pathName.split('.').pop() : null;
    if (extension && props.acceptedMediaTypes.includes(`.${extension}`)) {
        return getFileName(url);
    }

    // Try and get the title from the value after the '#'.
    const hashTitle = parsedUrl.hash ? parsedUrl.hash.substring(1) : null;
    if (hashTitle) {
        return hashTitle;
    }

    return null;
};

/**
 * Return template context for media details.
 *
 * @param {object} props
 * @returns {object}
 */
export const mediaDetailsTemplateContext = async(props) => {
    const context = {
        bodyTemplate: Selectors.EMBED.template.body.mediaDetailsBody,
        footerTemplate: Selectors.EMBED.template.footer.mediaDetailsFooter,
        isLink: (props.mediaType === 'link'),
        isVideo: (props.mediaType === 'video'),
        showControl: (props.mediaType === 'video' || props.mediaType === 'audio'),
        isUpdating: props.isUpdating,
        isNewFileOrLinkUpload: (props.newMediaLink || props.newFileUpload),
        selector: Selectors.EMBED.type,
    };

    return {...context, ...props};
};

/**
 * Get help strings.
 *
 * @returns {object}
 */
export const getHelpStrings = async() => {
    const [
        subtitles,
        captions,
        descriptions,
        chapters,
        metadata,
        customsize,
        linkcustomsize,
    ] = await getStrings([
        'subtitles_help',
        'captions_help',
        'descriptions_help',
        'chapters_help',
        'metadata_help',
        'customsize_help',
        'linkcustomsize_help',
    ].map((key) => ({
        key,
        component,
    })));

    return {
        subtitles,
        captions,
        descriptions,
        chapters,
        metadata,
        customsize,
        linkcustomsize,
    };
};

/**
 * Get current moodle languages.
 *
 * @param {editor} editor
 * @returns {object}
 */
export const prepareMoodleLang = (editor) => {
    const moodleLangs = getMoodleLang(editor);
    const currentLanguage = getCurrentLanguage(editor);

    const installed = Object.entries(moodleLangs.installed).map(([code, lang]) => ({
        lang,
        code,
        "default": code === currentLanguage,
    }));

    const available = Object.entries(moodleLangs.available).map(([code, lang]) => ({
        lang,
        code,
        "default": code === currentLanguage,
    }));

    return {
        installed,
        available,
    };
};

/**
 * Return moodle lang.
 *
 * @param {string} subtitleLang
 * @param {editor} editor
 * @returns {object|null}
 */
export const getMoodleLangObj = (subtitleLang, editor) => {
    const {available} = getMoodleLang(editor);

    if (available[subtitleLang]) {
        return {
            lang: subtitleLang,
            code: available[subtitleLang],
        };
    }

    return null;
};

/**
 * Get media data from the inserted media.
 *
 * @param {object} props
 * @returns {object}
 */
export const getEmbeddedMediaDetails = (props) => {
    const tracks = {
        subtitles: [],
        captions: [],
        descriptions: [],
        chapters: [],
        metadata: []
    };

    const mediaMetadata = props.root.querySelectorAll(Selectors.EMBED.elements.mediaMetadataTabPane);
    mediaMetadata.forEach(metaData => {
        const trackElements = metaData.querySelectorAll(Selectors.EMBED.elements.track);
        trackElements.forEach(track => {
            tracks[metaData.dataset.trackKind].push({
                src: track.querySelector(Selectors.EMBED.elements.url).value,
                srclang: track.querySelector(Selectors.EMBED.elements.trackLang).value,
                label: track.querySelector(Selectors.EMBED.elements.trackLabel).value,
                defaultTrack: track.querySelector(Selectors.EMBED.elements.trackDefault).checked,
            });
        });
    });

    const querySelector = (element) => props.root.querySelector(element);
    const mediaDataProps = {};
    mediaDataProps.media = {
        type: props.mediaType,
        sources: props.media,
        poster: props.media.poster ?? null,
        title: querySelector(Selectors.EMBED.elements.title).value,
        width: querySelector(Selectors.EMBED.elements.width).value,
        height: querySelector(Selectors.EMBED.elements.height).value,
        autoplay: querySelector(Selectors.EMBED.elements.mediaAutoplay).checked,
        loop: querySelector(Selectors.EMBED.elements.mediaLoop).checked,
        muted: querySelector(Selectors.EMBED.elements.mediaMute).checked,
        controls: querySelector(Selectors.EMBED.elements.mediaControl).checked,
        tracks,
    };
    mediaDataProps.link = false;
    return mediaDataProps;
};

/**
 * Check for video/audio attributes.
 *
 * @param {HTMLElement} elem
 * @param {string} attr Attribute name
 * @returns {boolean}
 */
export const hasAudioVideoAttr = (elem, attr) => {
    // As explained in MDL-64175, some OS (like Ubuntu), are removing the value for these attributes.
    // So in order to check if attr="true", we need to check if the attribute exists and if the value is empty or true.
    return (elem.hasAttribute(attr) && (elem.getAttribute(attr) || elem.getAttribute(attr) === ''));
};
