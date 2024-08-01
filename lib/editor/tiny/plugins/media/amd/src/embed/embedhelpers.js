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
import {
    convertStringUrlToObject,
    createUrlParams,
} from '../helpers';
import {getStrings} from 'core/str';
import {component} from "../common";
import {
    getCurrentLanguage,
    getMoodleLang
} from 'editor_tiny/options';

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
    } else if (mediaElm.nodeName.toLowerCase() === 'a') {
        mediaType = 'link';
        selectedMedia = mediaElm;
    } else if (mediaElm.querySelector('video')) {
        mediaType = 'video';
        selectedMedia = mediaElm.querySelector('video');
    } else if (mediaElm.querySelector('audio')) {
        mediaType = 'audio';
        selectedMedia = mediaElm.querySelector('audio');
    }

    return [mediaType, selectedMedia];
};

/**
 * Format url when inserting media link to be previewed.
 *
 * @param {string} url
 * @returns {string}
 */
export const formatMediaUrl = (url) => {
    // Convert the string url into url param object.
    const params = convertStringUrlToObject(url);

    // Format the url for youtube links.
    if (url.includes(Selectors.EMBED.mediaSites.youtube)) {
        let fetchedUrl = null;
        let fetchedUrlValue = null;
        for (const k in params) {
            if (url.includes(k)) {
                fetchedUrl = k;
                fetchedUrlValue = params[k];
                delete params[k];
                break;
            }
        }
        url = fetchedUrl.replace('watch?v', 'embed/');
        url = url + fetchedUrlValue + '?' + createUrlParams(params);
    }
    return url;
};

/**
 * Check if the url is from a known media site.
 *
 * @param {string} url
 * @returns {boolean}
 */
export const isUrlFromKnownMediaSites = (url) => {
    let state = false;
    const sites = Selectors.EMBED.mediaSites;
    for (const site in sites) {
        if (url.includes(sites[site])) {
            state = true;
            break;
        }
    }
    return state;
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
        isVideo: (props.mediaType === 'video'),
        isAudio: (props.mediaType === 'audio'),
        isLink: (props.mediaType === 'link'),
        isUpdating: props.isUpdating,
    };

    if (props.mediaData) {
        return {
            ...context,
            ...props.mediaData,
        };
    } else {
        return {
            ...context,
            ...await props.mediaTemplateContext,
        };
    }
};

/**
 * Get help strings.
 *
 * @returns {object}
 */
export const getHelpStrings = async() => {
    const [
        customsize,
    ] = await getStrings([
        'customsize_help',
    ].map((key) => ({
        key,
        component,
    })));

    return {
        customsize,
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
        poster: querySelector(Selectors.EMBED.elements.videoTag).getAttribute('poster'),
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
 * Get based url from the link.
 *
 * @param {string} url
 * @returns {string}
 */
export const getLinkBasedUrl = (url) => {
    if (url.includes(Selectors.EMBED.mediaSites.youtube)) {
        if (url.includes('embed/')) {
            const urlParams = url.split('embed/');
            let id = null;
            if (urlParams[1].includes('?')) {
                id = urlParams[1].split('?')[0];
            } else {
                id = urlParams[1];
            }
            url = `${urlParams[0]}watch?v=${id}&`;
        }
    }
    return url;
};
