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
 * Video JS loader.
 *
 * This takes care of applying the filter on content which was dynamically loaded.
 *
 * @module     media_videojs/loader
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import Ajax from 'core/ajax';
import Config from 'core/config';
import {eventTypes} from 'core_filters/events';
import LocalStorage from 'core/localstorage';
import Notification from 'core/notification';
import jQuery from 'jquery';

/** @var {bool} Whether this is the first load of videojs module */
let firstLoad;

/** @var {string} The language that is used in the player */
let language;

/** @var {object} List of languages and translations for the current page */
let langStringCache;

/**
 * Initialisei teh videojs Loader.
 *
 * Adds the listener for the event to then notify video.js.
 *
 * @method
 * @param {string} lang Language to be used in the player
 * @listens event:filterContentUpdated
 */
export const setUp = (lang) => {
    language = lang;
    firstLoad = true;

    // Notify Video.js about the nodes already present on the page.
    notifyVideoJS({
        detail: {
            nodes: document.body,
        }
    });

    // We need to call popover automatically if nodes are added to the page later.
    document.addEventListener(eventTypes.filterContentUpdated, notifyVideoJS);
};

/**
 * Notify video.js of new nodes.
 *
 * @param {Event} e The event.
 */
const notifyVideoJS = e => {
    const nodes = jQuery(e.detail.nodes);
    const selector = '.mediaplugin_videojs';
    const langStrings = getLanguageJson();

    // Find the descendants matching the expected parent of the audio and video
    // tags. Then also addBack the nodes matching the same selector. Finally,
    // we find the audio and video tags contained in those parents. Kind thanks
    // to jQuery for the simplicity.
    nodes.find(selector)
        .addBack(selector)
        .find('audio, video').each((index, element) => {
            const id = jQuery(element).attr('id');
            const config = jQuery(element).data('setup-lazy');
            const modulePromises = [import('media_videojs/video-lazy')];

            if (config.techOrder && config.techOrder.indexOf('youtube') !== -1) {
                // Add YouTube to the list of modules we require.
                modulePromises.push(import('media_videojs/Youtube-lazy'));
            }
            if (config.techOrder && config.techOrder.indexOf('OgvJS') !== -1) {
                config.ogvjs = {
                    worker: true,
                    wasm: true,
                    base: Config.wwwroot + '/media/player/videojs/ogvloader.php/' + Config.jsrev + '/'
                };
                // Add Ogv.JS to the list of modules we require.
                modulePromises.push(import('media_videojs/videojs-ogvjs-lazy'));
            }
            Promise.all([langStrings, ...modulePromises])
            .then(([langJson, videojs]) => {
                if (firstLoad) {
                    videojs.addLanguage(language, langJson);

                    firstLoad = false;
                }
                videojs(id, config);
                return;
            })
            .catch(Notification.exception);
        });
};

/**
 * Returns the json object of the language strings to be used in the player.
 *
 * @returns {Promise}
 */
const getLanguageJson = () => {
    if (langStringCache) {
        return Promise.resolve(langStringCache);
    }

    const cacheKey = `media_videojs/${language}`;

    const rawCacheContent = LocalStorage.get(cacheKey);
    if (rawCacheContent) {
        const cacheContent = JSON.parse(rawCacheContent);

        langStringCache = cacheContent;

        return Promise.resolve(langStringCache);
    }

    const request = {
        methodname: 'media_videojs_get_language',
        args: {
            lang: language,
        },
    };

    return Ajax.call([request])[0]
        .then(langStringData => {
            LocalStorage.set(cacheKey, langStringData);

            return langStringData;
        })
        .then(result => JSON.parse(result))
        .then(langStrings => {
            langStringCache = langStrings;

            return langStrings;
        });
};
